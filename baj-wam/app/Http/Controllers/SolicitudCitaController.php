<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Especialista;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\SolicitudCita;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SolicitudCitaController extends Controller
{
    public function index()
    {
        $servicios = Servicio::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('public.inicio', compact('servicios'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:160',
            ],
            'telefono' => [
                'nullable',
                'required_if:tipo_paciente,nuevo',
                'string',
                'min:8',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
            'id_servicio' => [
                'required',
                'integer',
                'exists:servicios,id_servicio',
            ],
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'hora' => [
                'required',
                'date_format:H:i',
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        try {
            $servicioActivo = Servicio::where(
                'id_servicio',
                $datos['id_servicio']
            )
                ->where('activo', true)
                ->exists();

            if (!$servicioActivo) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_servicio' => 'El servicio seleccionado no está disponible.',
                    ]);
            }

            $fechaHora = Carbon::createFromFormat(
                'Y-m-d H:i',
                $datos['fecha'] . ' ' . $datos['hora']
            );

            if ($fechaHora->isPast()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora' => 'Debes seleccionar una fecha y hora futura.',
                    ]);
            }

            SolicitudCita::create([
                'id_servicio' => $datos['id_servicio'],
                'id_cita' => null,
                'nombre' => trim($datos['nombre']),
                'telefono' => trim($datos['telefono']),
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'comentario' => $datos['comentario'] ?? null,
                'estado' => 'PENDIENTE',
            ]);

            return redirect()
                ->to(route('inicio') . '#cita')
                ->with(
                    'success',
                    'Tu solicitud fue enviada correctamente. La clínica confirmará la disponibilidad.'
                );

        } catch (Throwable $e) {
            Log::error('Error al registrar solicitud de cita.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible enviar la solicitud.'
                );
        }
    }

    public function solicitudes(Request $request)
    {
        $consulta = SolicitudCita::with([
            'servicio',
            'cita',
        ]);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $consulta->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', '%' . $buscar . '%')
                    ->orWhere('telefono', 'like', '%' . $buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        $solicitudes = $consulta
            ->orderByRaw("FIELD(estado, 'PENDIENTE', 'APROBADA', 'RECHAZADA')")
            ->orderByDesc('fecha_solicitud')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'pendientes' => SolicitudCita::where('estado', 'PENDIENTE')->count(),
            'aprobadas' => SolicitudCita::where('estado', 'APROBADA')->count(),
            'rechazadas' => SolicitudCita::where('estado', 'RECHAZADA')->count(),
        ];

        return view(
            'citas.solicitudes.index',
            compact('solicitudes', 'resumen')
        );
    }

    public function procesar($id)
    {
        try {
            $solicitud = SolicitudCita::with('servicio')
                ->findOrFail($id);

            if ($solicitud->estado !== 'PENDIENTE') {
                return redirect()
                    ->route('solicitudes-cita.index')
                    ->with('error', 'Esta solicitud ya fue procesada.');
            }

            $pacientes = Paciente::where('activo', true)
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();

            $especialistas = Especialista::with('usuario')
                ->where('activo', true)
                ->whereHas('usuario', function ($query) {
                    $query->where('activo', true);
                })
                ->get();

            $pacienteSugerido = Paciente::where(
                'telefono',
                $solicitud->telefono
            )
                ->where('activo', true)
                ->first();

            $partesNombre = preg_split(
                '/\s+/',
                trim($solicitud->nombre)
            );

            $nombresSugeridos = '';
            $apellidosSugeridos = '';

            if (count($partesNombre) === 1) {
                $nombresSugeridos = $partesNombre[0];
            } elseif (count($partesNombre) <= 3) {
                $apellidosSugeridos = array_pop($partesNombre);
                $nombresSugeridos = implode(' ', $partesNombre);
            } else {
                $apellidosSugeridos = implode(
                    ' ',
                    array_slice($partesNombre, -2)
                );

                $nombresSugeridos = implode(
                    ' ',
                    array_slice($partesNombre, 0, -2)
                );
            }

            return view(
                'citas.solicitudes.procesar',
                compact(
                    'solicitud',
                    'pacientes',
                    'especialistas',
                    'pacienteSugerido',
                    'nombresSugeridos',
                    'apellidosSugeridos'
                )
            );

        } catch (Throwable $e) {
            Log::error('Error al procesar solicitud.', [
                'id_solicitud' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('solicitudes-cita.index')
                ->with('error', 'No fue posible cargar la solicitud.');
        }
    }

    public function aprobar(Request $request, $id)
    {
        $datos = $request->validate([
            'tipo_paciente' => [
                'required',
                'in:existente,nuevo',
            ],
            'id_paciente' => [
                'nullable',
                'required_if:tipo_paciente,existente',
                'exists:pacientes,id_paciente',
            ],
            'nombres' => [
                'nullable',
                'required_if:tipo_paciente,nuevo',
                'string',
                'max:80',
            ],
            'apellidos' => [
                'nullable',
                'required_if:tipo_paciente,nuevo',
                'string',
                'max:80',
            ],
            'telefono' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
            'id_especialista' => [
                'required',
                'exists:especialistas,id_especialista',
            ],
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'hora' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        try {
            $solicitud = SolicitudCita::with('servicio')
                ->findOrFail($id);

            if ($solicitud->estado !== 'PENDIENTE') {
                return redirect()
                    ->route('solicitudes-cita.index')
                    ->with('error', 'La solicitud ya fue procesada.');
            }

            $inicio = Carbon::createFromFormat(
                'Y-m-d H:i',
                $datos['fecha'] . ' ' . $datos['hora']
            );

            if ($inicio->isPast()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora' => 'La fecha y hora deben ser futuras.',
                    ]);
            }

            $duracion = $solicitud
                ->servicio
                ->duracion_minutos;

            $fin = $inicio
                ->copy()
                ->addMinutes($duracion);

            $horarioOcupado = Cita::where(
                'id_especialista',
                $datos['id_especialista']
            )
                ->where('inicio', '<', $fin)
                ->where('fin', '>', $inicio)
                ->whereHas('estado', function ($query) {
                    $query->where(
                        'codigo',
                        '!=',
                        'CANCELADA'
                    );
                })
                ->exists();

            if ($horarioOcupado) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora' => 'El especialista ya tiene una cita en ese horario.',
                    ]);
            }

            DB::transaction(function () use ($datos, $solicitud, $inicio, $fin) {
                if ($datos['tipo_paciente'] === 'existente') {

                    $paciente = Paciente::findOrFail(
                        $datos['id_paciente']
                    );

                } else {

                    $paciente = Paciente::create([
                        'id_usuario_registro' =>
                            auth()->user()->id_usuario,

                        'nombres' =>
                            trim($datos['nombres']),

                        'apellidos' =>
                            trim($datos['apellidos']),

                        'telefono' =>
                            trim($datos['telefono']),

                        'activo' => true,
                    ]);
                }

                $estado = EstadoCita::where(
                    'codigo',
                    'CONFIRMADA'
                )->firstOrFail();

                $cita = Cita::create([
                    'id_paciente' =>
                        $paciente->id_paciente,

                    'id_especialista' =>
                        $datos['id_especialista'],

                    'id_servicio' =>
                        $solicitud->id_servicio,

                    'id_estado_cita' =>
                        $estado->id_estado_cita,

                    'id_usuario_registro' =>
                        auth()->user()->id_usuario,

                    'inicio' => $inicio,

                    'fin' => $fin,

                    'observaciones' =>
                        $solicitud->comentario,

                    'motivo_cancelacion' => null,
                ]);

                $solicitud->update([
                    'id_cita' => $cita->id_cita,
                    'estado' => 'APROBADA',
                    'fecha_respuesta' => now(),
                ]);
            });

            return redirect()
                ->route(
                    'citas.show',
                    $solicitud->fresh()->id_cita
                )
                ->with(
                    'success',
                    'Solicitud aprobada y cita creada correctamente.'
                );

        } catch (Throwable $e) {
            Log::error('Error al aprobar solicitud.', [
                'id_solicitud' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible aprobar la solicitud.'
                );
        }
    }

    public function rechazar($id)
    {
        try {
            $solicitud = SolicitudCita::findOrFail($id);

            if ($solicitud->estado !== 'PENDIENTE') {
                return redirect()
                    ->route('solicitudes-cita.index')
                    ->with('error', 'La solicitud ya fue procesada.');
            }

            $solicitud->update([
                'estado' => 'RECHAZADA',
                'fecha_respuesta' => now(),
            ]);

            return redirect()
                ->route('solicitudes-cita.index')
                ->with(
                    'success',
                    'Solicitud rechazada correctamente.'
                );

        } catch (Throwable $e) {
            Log::error('Error al rechazar solicitud.', [
                'id_solicitud' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No fue posible rechazar la solicitud.'
            );
        }
    }
}