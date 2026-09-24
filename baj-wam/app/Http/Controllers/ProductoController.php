<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $consulta = Producto::with('categoria');

            if ($request->filled('buscar')) {
                $buscar = trim($request->buscar);

                $consulta->where(function ($query) use ($buscar) {
                    $query->where('codigo', 'like', '%' . $buscar . '%')
                        ->orWhere('nombre', 'like', '%' . $buscar . '%')
                        ->orWhere('presentacion', 'like', '%' . $buscar . '%');
                });
            }

            if ($request->filled('categoria')) {
                $consulta->where(
                    'id_categoria',
                    $request->categoria
                );
            }

            if ($request->filled('estado')) {
                $consulta->where(
                    'activo',
                    $request->estado === 'activo' ? 1 : 0
                );
            }

            $productos = $consulta
                ->orderBy('nombre')
                ->paginate(10)
                ->withQueryString();

            $categorias = CategoriaProducto::where('activo', true)
                ->orderBy('nombre')
                ->get();

            $resumenProductos = [
                'total' => Producto::count(),
                'activos' => Producto::where('activo', true)->count(),
                'inactivos' => Producto::where('activo', false)->count(),
            ];

            return view(
                'productos.index',
                compact(
                    'productos',
                    'categorias',
                    'resumenProductos'
                )
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar productos.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No fue posible cargar los productos.'
            );
        }
    }

    public function create()
    {
        $categorias = CategoriaProducto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_categoria' => [
                'required',
                'exists:categorias_producto,id_categoria',
            ],
            'codigo' => [
                'required',
                'string',
                'max:40',
                'unique:productos,codigo',
            ],
            'nombre' => [
                'required',
                'string',
                'max:120',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'presentacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'unidad_medida' => [
                'required',
                'string',
                'max:30',
            ],
            'precio_referencia' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'existencia_minima' => [
                'required',
                'numeric',
                'min:0',
            ],
            'imagen_url' => [
                'nullable',
                'url',
                'max:255',
            ],
        ], [
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'codigo.required' => 'Debes ingresar el código del producto.',
            'codigo.unique' => 'Ya existe un producto con este código.',
            'nombre.required' => 'Debes ingresar el nombre del producto.',
            'unidad_medida.required' => 'Debes ingresar la unidad de medida.',
            'precio_referencia.numeric' => 'El precio debe ser un valor numérico.',
            'precio_referencia.min' => 'El precio no puede ser negativo.',
            'existencia_minima.required' => 'Debes ingresar la existencia mínima.',
            'existencia_minima.min' => 'La existencia mínima no puede ser negativa.',
            'imagen_url.url' => 'La imagen debe ser una URL válida.',
        ]);

        try {
            $categoria = CategoriaProducto::where(
                'id_categoria',
                $datos['id_categoria']
            )
                ->where('activo', true)
                ->first();

            if (!$categoria) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_categoria' => 'La categoría seleccionada no está disponible.',
                    ]);
            }

            $datos['codigo'] = strtoupper(trim($datos['codigo']));
            $datos['unidad_medida'] = strtoupper(trim($datos['unidad_medida']));
            $datos['activo'] = true;

            $producto = Producto::create($datos);

            return redirect()
                ->route('productos.show', $producto->id_producto)
                ->with('success', 'Producto registrado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al registrar producto.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible registrar el producto.');
        }
    }

    public function show($id)
    {
        try {
            $producto = Producto::with('categoria')
                ->findOrFail($id);

            return view(
                'productos.show',
                compact('producto')
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar producto.', [
                'id_producto' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('productos.index')
                ->with('error', 'No fue posible cargar el producto.');
        }
    }

    public function edit($id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $categorias = CategoriaProducto::where('activo', true)
                ->orderBy('nombre')
                ->get();

            return view(
                'productos.edit',
                compact('producto', 'categorias')
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar producto para edición.', [
                'id_producto' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('productos.index')
                ->with('error', 'No fue posible cargar el producto.');
        }
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $datos = $request->validate([
            'id_categoria' => [
                'required',
                'exists:categorias_producto,id_categoria',
            ],
            'codigo' => [
                'required',
                'string',
                'max:40',
                Rule::unique('productos', 'codigo')
                    ->ignore($producto->id_producto, 'id_producto'),
            ],
            'nombre' => [
                'required',
                'string',
                'max:120',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'presentacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'unidad_medida' => [
                'required',
                'string',
                'max:30',
            ],
            'precio_referencia' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'existencia_minima' => [
                'required',
                'numeric',
                'min:0',
            ],
            'imagen_url' => [
                'nullable',
                'url',
                'max:255',
            ],
        ], [
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'codigo.required' => 'Debes ingresar el código del producto.',
            'codigo.unique' => 'Ya existe otro producto con este código.',
            'nombre.required' => 'Debes ingresar el nombre del producto.',
            'unidad_medida.required' => 'Debes ingresar la unidad de medida.',
            'precio_referencia.numeric' => 'El precio debe ser un valor numérico.',
            'precio_referencia.min' => 'El precio no puede ser negativo.',
            'existencia_minima.required' => 'Debes ingresar la existencia mínima.',
            'existencia_minima.min' => 'La existencia mínima no puede ser negativa.',
            'imagen_url.url' => 'La imagen debe ser una URL válida.',
        ]);

        try {
            $categoria = CategoriaProducto::where(
                'id_categoria',
                $datos['id_categoria']
            )
                ->where('activo', true)
                ->first();

            if (!$categoria) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_categoria' => 'La categoría seleccionada no está disponible.',
                    ]);
            }

            $datos['codigo'] = strtoupper(trim($datos['codigo']));
            $datos['unidad_medida'] = strtoupper(trim($datos['unidad_medida']));

            $producto->update($datos);

            return redirect()
                ->route('productos.show', $producto->id_producto)
                ->with('success', 'Producto actualizado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al actualizar producto.', [
                'id_producto' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible actualizar el producto.');
        }
    }

    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);

            if (!$producto->activo) {
                return redirect()
                    ->route('productos.index')
                    ->with('error', 'El producto ya se encuentra inactivo.');
            }

            $producto->update([
                'activo' => false,
            ]);

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto desactivado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al desactivar producto.', [
                'id_producto' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'No fue posible desactivar el producto.');
        }
    }
}