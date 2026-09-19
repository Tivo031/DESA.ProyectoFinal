<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Especialista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $consulta = Cita::query()
                ->with([
                    'paciente',
                    'servicio',
                    'estado',
                    'especialista.usuario',
                ]);

            $buscar = trim(
                (string) $request->input('buscar', '')
            );

            if ($buscar !== '') {
                $consulta->whereHas(
                    'paciente',
                    function ($query) use ($buscar) {
                        $query->where(
                            'nombres',
                            'LIKE',
                            '%' . $buscar . '%'
                        )
                        ->orWhere(
                            'apellidos',
                            'LIKE',
                            '%' . $buscar . '%'
                        )
                        ->orWhere(
                            'telefono',
                            'LIKE',
                            '%' . $buscar . '%'
                        );
                    }
                );
            }

            if ($request->filled('fecha')) {
                $consulta->whereDate(
                    'inicio',
                    $request->fecha
                );
            }

            if ($request->filled('especialista')) {
                $consulta->where(
                    'id_especialista',
                    $request->especialista
                );
            }

            if ($request->filled('estado')) {
                $consulta->where(
                    'id_estado_cita',
                    $request->estado
                );
            }

            $citas = $consulta
                ->orderByDesc('inicio')
                ->paginate(10)
                ->withQueryString();

            $especialistas = Especialista::query()
                ->with('usuario')
                ->where('activo', true)
                ->whereHas('usuario', function ($query) {
                    $query->where('activo', true);
                })
                ->orderBy('id_especialista')
                ->get();

            $estados = EstadoCita::query()
                ->orderBy('id_estado_cita')
                ->get();

            $resumenCitas = [
                'hoy' => Cita::whereDate(
                    'inicio',
                    now()->toDateString()
                )->count(),

                'confirmadas' => Cita::whereHas(
                    'estado',
                    function ($query) {
                        $query->where(
                            'codigo',
                            'CONFIRMADA'
                        );
                    }
                )->count(),

                'pendientes' => Cita::whereHas(
                    'estado',
                    function ($query) {
                        $query->where(
                            'codigo',
                            'PENDIENTE'
                        );
                    }
                )->count(),

                'canceladas' => Cita::whereHas(
                    'estado',
                    function ($query) {
                        $query->where(
                            'codigo',
                            'CANCELADA'
                        );
                    }
                )->count(),
            ];

            return view(
                'citas.index',
                compact(
                    'citas',
                    'especialistas',
                    'estados',
                    'resumenCitas'
                )
            );

        } catch (Throwable $e) {

            Log::error(
                'Error al cargar el listado de citas.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return back()->with(
                'error',
                'No fue posible cargar el listado de citas.'
            );
        }
    }
}