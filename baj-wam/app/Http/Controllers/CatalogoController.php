<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Producto::with('categoria')
            ->join(
                'catalogo_publico as cp',
                'cp.id_producto',
                '=',
                'productos.id_producto'
            )
            ->select('productos.*')
            ->addSelect(
                'cp.orden_visualizacion',
                'cp.fecha_publicacion'
            )
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
            ->where('productos.activo', true)
            ->where('cp.visible', true);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $consulta->where(function ($query) use ($buscar) {
                $query->where(
                    'productos.nombre',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'productos.descripcion',
                    'like',
                    '%' . $buscar . '%'
                );
            });
        }

        if ($request->filled('categoria')) {
            $consulta->where(
                'productos.id_categoria',
                $request->categoria
            );
        }

        $productos = $consulta
            ->orderBy('cp.orden_visualizacion')
            ->orderBy('productos.nombre')
            ->paginate(12)
            ->withQueryString();

        $categorias = CategoriaProducto::where('activo', true)
            ->whereHas('productos', function ($query) {
                $query->where('activo', true)
                    ->whereHas('catalogo', function ($catalogo) {
                        $catalogo->where('visible', true);
                    });
            })
            ->orderBy('nombre')
            ->get();

        return view(
            'catalogo.index',
            compact('productos', 'categorias')
        );
    }
}