<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Especialista;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        $query = Consulta::query()
            ->with([
                'cita.paciente',
                'cita.especialista.usuario',
                'cita.servicio',
                'usuarioRegistro',
                'productos',
            ]);

        // BÚSQUEDA
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {
                $q->where('motivo_consulta', 'like', "%{$buscar}%")
                    ->orWhereHas('cita.paciente', function ($paciente) use ($buscar) {
                        $paciente->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%");
                    });
            });
        }

        // FECHA DESDE
        if ($request->filled('desde')) {
            $query->whereDate(
                'fecha_consulta',
                '>=',
                $request->desde
            );
        }

        // FECHA HASTA
        if ($request->filled('hasta')) {
            $query->whereDate(
                'fecha_consulta',
                '<=',
                $request->hasta
            );
        }

        // ESPECIALISTA
        if ($request->filled('especialista')) {
            $query->whereHas('cita', function ($cita) use ($request) {
                $cita->where(
                    'id_especialista',
                    $request->especialista
                );
            });
        }

        // CONSULTAS
        $consultas = $query
            ->orderByDesc('fecha_consulta')
            ->paginate(10)
            ->withQueryString();

        // ESPECIALISTAS
        $especialistas = Especialista::query()
            ->with('usuario')
            ->where('activo', true)
            ->orderBy('id_especialista')
            ->get();

        // RESUMEN
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $resumenConsultas = [
            'mes' => Consulta::whereBetween(
                'fecha_consulta',
                [$inicioMes, $finMes]
            )->count(),

            'hoy' => Consulta::whereDate(
                'fecha_consulta',
                Carbon::today()
            )->count(),

            'pacientes' => Consulta::whereBetween(
                'fecha_consulta',
                [$inicioMes, $finMes]
            )
                ->join(
                    'citas',
                    'consultas.id_cita',
                    '=',
                    'citas.id_cita'
                )
                ->distinct()
                ->count('citas.id_paciente'),

            'con_productos' => Consulta::whereBetween(
                'fecha_consulta',
                [$inicioMes, $finMes]
            )
                ->whereHas('productos')
                ->count(),
        ];

        return view('consultas.index', compact(
            'consultas',
            'especialistas',
            'resumenConsultas'
        ));
    }

    public function create()
    {
        $estadoConfirmada = EstadoCita::where(
            'codigo',
            'CONFIRMADA'
        )->firstOrFail();

        $citasDisponibles = Cita::query()
            ->with([
                'paciente',
                'servicio',
                'especialista.usuario',
            ])
            ->where(
                'id_estado_cita',
                $estadoConfirmada->id_estado_cita
            )
            ->whereDoesntHave('consulta')
            ->orderBy('inicio')
            ->get();

        $productos = Producto::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('consultas.create', compact(
            'citasDisponibles',
            'productos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cita' => [
                'required',
                'integer',
                'exists:citas,id_cita',
            ],

            'fecha_consulta' => [
                'required',
                'date',
            ],

            'motivo_consulta' => [
                'required',
                'string',
            ],

            'observaciones' => [
                'nullable',
                'string',
            ],

            'diagnostico' => [
                'nullable',
                'string',
            ],

            'tratamiento_realizado' => [
                'required',
                'string',
            ],

            'recomendaciones' => [
                'nullable',
                'string',
            ],

            'productos' => [
                'nullable',
                'array',
            ],

            'productos.*.id_producto' => [
                'nullable',
                'integer',
                'exists:productos,id_producto',
            ],

            'productos.*.cantidad_recomendada' => [
                'nullable',
                'required_with:productos.*.id_producto',
                'numeric',
                'min:0.01',
            ],

            'productos.*.indicaciones' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        try {

            DB::transaction(function () use ($request) {

                $estadoConfirmada = EstadoCita::where(
                    'codigo',
                    'CONFIRMADA'
                )->firstOrFail();

                $estadoCompletada = EstadoCita::where(
                    'codigo',
                    'COMPLETADA'
                )->firstOrFail();


                // CITA

                $cita = Cita::query()
                    ->where(
                        'id_cita',
                        $request->id_cita
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $cita->id_estado_cita !==
                    $estadoConfirmada->id_estado_cita
                ) {

                    throw ValidationException::withMessages([
                        'id_cita' =>
                            'La cita seleccionada ya no está disponible para atención.',
                    ]);
                }

                if (
                    Consulta::where(
                        'id_cita',
                        $cita->id_cita
                    )->exists()
                ) {

                    throw ValidationException::withMessages([
                        'id_cita' =>
                            'La cita seleccionada ya tiene una consulta registrada.',
                    ]);
                }


                // CONSULTA

                $consulta = Consulta::create([
                    'id_cita' =>
                        $cita->id_cita,

                    'id_usuario_registro' =>
                        $request->user()->id_usuario,

                    'fecha_consulta' =>
                        $request->fecha_consulta,

                    'motivo_consulta' =>
                        trim($request->motivo_consulta),

                    'observaciones' =>
                        $request->filled('observaciones')
                        ? trim($request->observaciones)
                        : null,

                    'diagnostico' =>
                        $request->filled('diagnostico')
                        ? trim($request->diagnostico)
                        : null,

                    'tratamiento_realizado' =>
                        trim($request->tratamiento_realizado),

                    'recomendaciones' =>
                        $request->filled('recomendaciones')
                        ? trim($request->recomendaciones)
                        : null,
                ]);


                // PRODUCTOS

                $productos = collect(
                    $request->input('productos', [])
                )->filter(function ($producto) {

                    return !empty(
                        $producto['id_producto']
                    );
                });

                $productosIds = $productos->pluck(
                    'id_producto'
                );

                if (
                    $productosIds
                        ->duplicates()
                        ->isNotEmpty()
                ) {

                    throw ValidationException::withMessages([
                        'productos' =>
                            'No puedes agregar el mismo producto más de una vez.',
                    ]);
                }

                foreach ($productos as $producto) {

                    $consulta->productos()->attach(
                        $producto['id_producto'],
                        [
                            'cantidad_recomendada' =>
                                $producto['cantidad_recomendada'],

                            'indicaciones' =>
                                !empty($producto['indicaciones'])
                                ? trim(
                                    $producto['indicaciones']
                                )
                                : null,
                        ]
                    );
                }


                // COMPLETAR CITA

                $cita->id_estado_cita =
                    $estadoCompletada->id_estado_cita;

                $cita->save();
            });


            return redirect()
                ->route('consultas.index')
                ->with(
                    'success',
                    'Consulta registrada correctamente.'
                );

        } catch (ValidationException $e) {

            throw $e;

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible registrar la consulta.'
                );
        }
    }

    public function show(int $id)
    {
        try {

            $consulta = Consulta::query()
                ->with([
                    'cita.paciente',
                    'cita.especialista.usuario',
                    'cita.servicio',
                    'usuarioRegistro',
                    'productos',
                ])
                ->findOrFail($id);

            return view(
                'consultas.show',
                compact('consulta')
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return redirect()
                ->route('consultas.index')
                ->with(
                    'error',
                    'La consulta solicitada no existe.'
                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('consultas.index')
                ->with(
                    'error',
                    'No fue posible cargar la consulta.'
                );
        }
    }
}