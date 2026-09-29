<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Producto;
use App\Models\SolicitudCita;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // CITAS DE HOY
        $citas = Cita::with([
            'paciente',
            'servicio',
            'especialista.usuario',
            'estado',
        ])
            ->whereDate('inicio', today())
            ->orderBy('inicio')
            ->get();

        // AGENDA DE HOY
        $citasHoy = $citas->map(function ($cita) {
            $nombrePaciente = trim(
                ($cita->paciente?->nombres ?? '') . ' ' .
                ($cita->paciente?->apellidos ?? '')
            );

            $nombreEspecialista = trim(
                ($cita->especialista?->usuario?->nombres ?? '') . ' ' .
                ($cita->especialista?->usuario?->apellidos ?? '')
            );

            return [
                'hora' => $cita->inicio->format('H:i'),
                'paciente' => $nombrePaciente ?: 'Sin paciente',
                'servicio' => $cita->servicio?->nombre ?? 'Sin servicio',
                'especialista' => $nombreEspecialista ?: 'Sin especialista',
                'estado' => $cita->estado?->nombre ?? 'Sin estado',
            ];
        });

        // SOLICITUDES PENDIENTES
        $solicitudesPendientes = SolicitudCita::where(
            'estado',
            'PENDIENTE'
        )->count();

        // PACIENTES ACTIVOS
        $pacientesActivos = Paciente::where(
            'activo',
            true
        )->count();

        // EXISTENCIA ACTUAL POR PRODUCTO
        $existencias = DB::table('movimientos_inventario')
            ->select(
                'id_producto',
                DB::raw("
                    SUM(
                        CASE
                            WHEN tipo IN ('ENTRADA', 'AJUSTE_POSITIVO')
                                THEN cantidad
                            WHEN tipo IN ('SALIDA', 'AJUSTE_NEGATIVO')
                                THEN -cantidad
                            ELSE 0
                        END
                    ) AS existencia
                ")
            )
            ->groupBy('id_producto');

        // PRODUCTOS CON EXISTENCIA BAJA
        $productosBajosConsulta = Producto::query()
            ->leftJoinSub(
                $existencias,
                'existencias',
                function ($join) {
                    $join->on(
                        'productos.id_producto',
                        '=',
                        'existencias.id_producto'
                    );
                }
            )
            ->where('productos.activo', true)
            ->whereRaw(
                'COALESCE(existencias.existencia, 0) <= productos.existencia_minima'
            )
            ->select(
                'productos.id_producto',
                'productos.nombre',
                'productos.existencia_minima',
                DB::raw(
                    'COALESCE(existencias.existencia, 0) AS existencia'
                )
            )
            ->orderBy('existencia')
            ->get();

        // LISTADO PARA EL DASHBOARD
        $productosBajos = $productosBajosConsulta
            ->map(function ($producto) {
                return [
                    'nombre' => $producto->nombre,
                    'existencia' => (float) $producto->existencia,
                    'minimo' => (float) $producto->existencia_minima,
                ];
            });

        // MÉTRICAS
        $metricas = [
            'citas_hoy' => $citas->count(),
            'solicitudes_pendientes' => $solicitudesPendientes,
            'pacientes_activos' => $pacientesActivos,
            'productos_bajos' => $productosBajos->count(),
        ];

        return view(
            'dashboard.index',
            compact(
                'metricas',
                'citasHoy',
                'productosBajos'
            )
        );
    }
}