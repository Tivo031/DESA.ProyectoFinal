<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        try {
            $consulta = Producto::with('categoria')
                ->select('productos.*')
                ->selectSub(
                    DB::table('movimientos_inventario as m')
                        ->selectRaw("
                            COALESCE(
                                SUM(
                                    CASE
                                        WHEN m.tipo IN ('ENTRADA', 'AJUSTE_POSITIVO')
                                            THEN m.cantidad
                                        WHEN m.tipo IN ('SALIDA', 'AJUSTE_NEGATIVO')
                                            THEN -m.cantidad
                                        ELSE 0
                                    END
                                ),
                                0
                            )
                        ")
                        ->whereColumn(
                            'm.id_producto',
                            'productos.id_producto'
                        ),
                    'existencia_actual'
                );

            if ($request->filled('buscar')) {
                $buscar = trim($request->buscar);

                $consulta->where(function ($query) use ($buscar) {
                    $query->where('nombre', 'like', '%' . $buscar . '%')
                        ->orWhere('codigo', 'like', '%' . $buscar . '%');
                });
            }

            if ($request->filled('estado')) {
                if ($request->estado === 'activo') {
                    $consulta->where('activo', true);
                }

                if ($request->estado === 'inactivo') {
                    $consulta->where('activo', false);
                }
            }

            $productos = $consulta
                ->orderBy('nombre')
                ->paginate(10)
                ->withQueryString();

            $todosProductos = Producto::select('productos.*')
                ->selectSub(
                    DB::table('movimientos_inventario as m')
                        ->selectRaw("
                            COALESCE(
                                SUM(
                                    CASE
                                        WHEN m.tipo IN ('ENTRADA', 'AJUSTE_POSITIVO')
                                            THEN m.cantidad
                                        WHEN m.tipo IN ('SALIDA', 'AJUSTE_NEGATIVO')
                                            THEN -m.cantidad
                                        ELSE 0
                                    END
                                ),
                                0
                            )
                        ")
                        ->whereColumn(
                            'm.id_producto',
                            'productos.id_producto'
                        ),
                    'existencia_actual'
                )
                ->where('activo', true)
                ->get();

            $resumenInventario = [
                'productos' => $todosProductos->count(),

                'bajos' => $todosProductos->filter(function ($producto) {
                    return $producto->existencia_actual > 0
                        && $producto->existencia_actual
                        <= $producto->existencia_minima;
                })->count(),

                'sin_existencia' => $todosProductos->filter(function ($producto) {
                    return $producto->existencia_actual <= 0;
                })->count(),

                'movimientos_hoy' => MovimientoInventario::whereDate(
                    'fecha_movimiento',
                    today()
                )->count(),
            ];

            return view(
                'inventario.index',
                compact('productos', 'resumenInventario')
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar inventario.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No fue posible cargar el inventario.'
            );
        }
    }

    public function create()
    {
        $productos = Producto::where('activo', true)
            ->select('productos.*')
            ->selectSub(
                DB::table('movimientos_inventario as m')
                    ->selectRaw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN m.tipo IN ('ENTRADA', 'AJUSTE_POSITIVO')
                                    THEN m.cantidad
                                WHEN m.tipo IN ('SALIDA', 'AJUSTE_NEGATIVO')
                                    THEN -m.cantidad
                                ELSE 0
                            END
                        ),
                        0
                    )
                ")
                    ->whereColumn(
                        'm.id_producto',
                        'productos.id_producto'
                    ),
                'existencia_actual'
            )
            ->orderBy('nombre')
            ->get();

        return view('inventario.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_producto' => [
                'required',
                'exists:productos,id_producto',
            ],
            'tipo' => [
                'required',
                'in:ENTRADA,SALIDA,AJUSTE_POSITIVO,AJUSTE_NEGATIVO',
            ],
            'cantidad' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'referencia' => [
                'nullable',
                'string',
                'max:100',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'id_producto.required' => 'Debes seleccionar un producto.',
            'tipo.required' => 'Debes seleccionar el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento no es válido.',
            'cantidad.required' => 'Debes ingresar una cantidad.',
            'cantidad.numeric' => 'La cantidad debe ser numérica.',
            'cantidad.gt' => 'La cantidad debe ser mayor que cero.',
            'referencia.max' => 'La referencia no puede superar los 100 caracteres.',
            'observaciones.max' => 'Las observaciones no pueden superar los 500 caracteres.',
        ]);

        try {
            DB::transaction(function () use ($datos) {

                $producto = Producto::where(
                    'id_producto',
                    $datos['id_producto']
                )
                    ->where('activo', true)
                    ->lockForUpdate()
                    ->first();

                if (!$producto) {
                    throw ValidationException::withMessages([
                        'id_producto' => 'El producto seleccionado no está disponible.',
                    ]);
                }

                $existenciaActual = $this->obtenerExistenciaActual(
                    $producto->id_producto
                );

                $esSalida = in_array($datos['tipo'], [
                    'SALIDA',
                    'AJUSTE_NEGATIVO',
                ]);

                if (
                    $esSalida
                    && (float) $datos['cantidad'] > $existenciaActual
                ) {
                    throw ValidationException::withMessages([
                        'cantidad' =>
                            'La cantidad supera la existencia disponible. '
                            . 'Actualmente hay '
                            . number_format($existenciaActual, 2)
                            . ' '
                            . $producto->unidad_medida
                            . '.',
                    ]);
                }

                MovimientoInventario::create([
                    'id_producto' => $producto->id_producto,
                    'id_usuario' => auth()->user()->id_usuario,
                    'tipo' => $datos['tipo'],
                    'cantidad' => $datos['cantidad'],
                    'referencia' => $datos['referencia'] ?? null,
                    'observaciones' => $datos['observaciones'] ?? null,
                ]);
            });

            return redirect()
                ->route('inventario.index')
                ->with(
                    'success',
                    'Movimiento de inventario registrado correctamente.'
                );

        } catch (ValidationException $e) {
            throw $e;

        } catch (Throwable $e) {
            Log::error('Error al registrar movimiento de inventario.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible registrar el movimiento.'
                );
        }
    }

    private function obtenerExistenciaActual($idProducto)
    {
        $existencia = DB::table('movimientos_inventario')
            ->where('id_producto', $idProducto)
            ->selectRaw("
            COALESCE(
                SUM(
                    CASE
                        WHEN tipo IN ('ENTRADA', 'AJUSTE_POSITIVO')
                            THEN cantidad
                        WHEN tipo IN ('SALIDA', 'AJUSTE_NEGATIVO')
                            THEN -cantidad
                        ELSE 0
                    END
                ),
                0
            ) AS existencia_actual
        ")
            ->value('existencia_actual');

        return (float) $existencia;
    }

    public function historial(Request $request)
    {
        try {
            $consulta = MovimientoInventario::with([
                'producto.categoria',
                'usuario',
            ]);

            if ($request->filled('buscar')) {
                $buscar = trim($request->buscar);

                $consulta->where(function ($query) use ($buscar) {
                    $query->where(
                        'referencia',
                        'like',
                        '%' . $buscar . '%'
                    )
                        ->orWhereHas('producto', function ($producto) use ($buscar) {
                            $producto->where(
                                'nombre',
                                'like',
                                '%' . $buscar . '%'
                            )
                                ->orWhere(
                                    'codigo',
                                    'like',
                                    '%' . $buscar . '%'
                                );
                        });
                });
            }

            if ($request->filled('producto')) {
                $consulta->where(
                    'id_producto',
                    $request->producto
                );
            }

            if ($request->filled('tipo')) {
                $consulta->where(
                    'tipo',
                    $request->tipo
                );
            }

            if ($request->filled('fecha_desde')) {
                $consulta->whereDate(
                    'fecha_movimiento',
                    '>=',
                    $request->fecha_desde
                );
            }

            if ($request->filled('fecha_hasta')) {
                $consulta->whereDate(
                    'fecha_movimiento',
                    '<=',
                    $request->fecha_hasta
                );
            }

            $movimientos = $consulta
                ->orderByDesc('fecha_movimiento')
                ->orderByDesc('id_movimiento')
                ->paginate(15)
                ->withQueryString();

            $productos = Producto::orderBy('nombre')->get();

            return view(
                'inventario.historial',
                compact('movimientos', 'productos')
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar historial de inventario.', [
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('inventario.index')
                ->with(
                    'error',
                    'No fue posible cargar el historial.'
                );
        }
    }
}