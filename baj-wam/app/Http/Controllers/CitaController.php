<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\Especialista;
use App\Models\EstadoCita;
use App\Models\SolicitudCita;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

            $solicitudesPendientes = SolicitudCita::where('estado', 'PENDIENTE')->count();

            return view(
                'citas.index',
                compact(
                    'citas',
                    'especialistas',
                    'estados',
                    'resumenCitas',
                    'solicitudesPendientes',
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

    public function create()
    {
        try {
            $pacientes = Paciente::where('activo', true)
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();

            $servicios = Servicio::where('activo', true)
                ->orderBy('nombre')
                ->get();

            $especialistas = Especialista::with('usuario')
                ->where('activo', true)
                ->whereHas('usuario', function ($query) {
                    $query->where('activo', true);
                })
                ->get();

            $estados = EstadoCita::whereIn(
                'codigo',
                ['PENDIENTE', 'CONFIRMADA']
            )
                ->orderBy('id_estado_cita')
                ->get();

            return view(
                'citas.create',
                compact(
                    'pacientes',
                    'servicios',
                    'especialistas',
                    'estados'
                )
            );

        } catch (Throwable $e) {

            Log::error(
                'Error al cargar el formulario de citas.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return redirect()
                ->route('citas.index')
                ->with(
                    'error',
                    'No fue posible cargar el formulario de citas.'
                );
        }
    }

    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'id_paciente' => [
                    'required',
                    'integer',
                    'exists:pacientes,id_paciente',
                ],
                'id_servicio' => [
                    'required',
                    'integer',
                    'exists:servicios,id_servicio',
                ],
                'id_especialista' => [
                    'required',
                    'integer',
                    'exists:especialistas,id_especialista',
                ],
                'id_estado_cita' => [
                    'required',
                    'integer',
                    'exists:estados_cita,id_estado_cita',
                ],
                'fecha_cita' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                ],
                'hora_inicio' => [
                    'required',
                    'date_format:H:i',
                ],
                'hora_fin' => [
                    'required',
                    'date_format:H:i',
                ],
                'observaciones' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
            ],
            [
                'id_paciente.required' =>
                    'Debes seleccionar un paciente.',

                'id_servicio.required' =>
                    'Debes seleccionar un servicio.',

                'id_especialista.required' =>
                    'Debes seleccionar un especialista.',

                'id_estado_cita.required' =>
                    'Debes seleccionar un estado.',

                'fecha_cita.required' =>
                    'Debes seleccionar la fecha de la cita.',

                'fecha_cita.after_or_equal' =>
                    'La fecha de la cita no puede ser anterior a hoy.',

                'hora_inicio.required' =>
                    'Debes indicar la hora de inicio.',

                'hora_fin.required' =>
                    'Debes indicar la hora de finalización.',
            ]
        );

        try {

            $inicio = Carbon::createFromFormat(
                'Y-m-d H:i',
                $datos['fecha_cita'] . ' ' . $datos['hora_inicio']
            );

            $fin = Carbon::createFromFormat(
                'Y-m-d H:i',
                $datos['fecha_cita'] . ' ' . $datos['hora_fin']
            );

            if ($fin->lessThanOrEqualTo($inicio)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora_fin' =>
                            'La hora de finalización debe ser posterior a la hora de inicio.',
                    ]);
            }

            $pacienteActivo = Paciente::where(
                'id_paciente',
                $datos['id_paciente']
            )
                ->where('activo', true)
                ->exists();

            if (!$pacienteActivo) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_paciente' =>
                            'El paciente seleccionado no está disponible.',
                    ]);
            }

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
                        'id_servicio' =>
                            'El servicio seleccionado no está disponible.',
                    ]);
            }

            $especialistaActivo = Especialista::where(
                'id_especialista',
                $datos['id_especialista']
            )
                ->where('activo', true)
                ->whereHas('usuario', function ($query) {
                    $query->where('activo', true);
                })
                ->exists();

            if (!$especialistaActivo) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_especialista' =>
                            'El especialista seleccionado no está disponible.',
                    ]);
            }

            $estado = EstadoCita::findOrFail(
                $datos['id_estado_cita']
            );

            if (
                !in_array(
                    $estado->codigo,
                    ['PENDIENTE', 'CONFIRMADA']
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_estado_cita' =>
                            'El estado seleccionado no es válido para una nueva cita.',
                    ]);
            }

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
                        'hora_inicio' =>
                            'El especialista ya tiene una cita en ese horario.',
                    ]);
            }

            Cita::create([
                'id_paciente' =>
                    $datos['id_paciente'],

                'id_especialista' =>
                    $datos['id_especialista'],

                'id_servicio' =>
                    $datos['id_servicio'],

                'id_estado_cita' =>
                    $datos['id_estado_cita'],

                'id_usuario_registro' =>
                    $request->user()->id_usuario,

                'inicio' => $inicio,

                'fin' => $fin,

                'observaciones' =>
                    $datos['observaciones'] ?? null,

                'motivo_cancelacion' => null,
            ]);

            return redirect()
                ->route('citas.index')
                ->with(
                    'success',
                    'La cita fue registrada correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al registrar una cita.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible registrar la cita.'
                );
        }
    }

    public function show($id)
    {
        try {
            $cita = Cita::with([
                'paciente',
                'servicio',
                'estado',
                'especialista.usuario',
                'usuarioRegistro',
            ])->find($id);

            if (!$cita) {
                return redirect()
                    ->route('citas.index')
                    ->with('error', 'La cita no existe.');
            }

            return view('citas.show', compact('cita'));

        } catch (Throwable $e) {

            Log::error('Error al mostrar la cita.', [
                'id_cita' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('citas.index')
                ->with('error', 'No fue posible cargar la cita.');
        }
    }

    public function edit($id)
    {
        try {
            $cita = Cita::with('estado')->findOrFail($id);

            if ($cita->estado?->codigo === 'CANCELADA') {
                return redirect()
                    ->route('citas.show', $id)
                    ->with('error', 'Una cita cancelada no puede editarse.');
            }

            $pacientes = Paciente::where('activo', true)
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();

            $servicios = Servicio::where('activo', true)
                ->orderBy('nombre')
                ->get();

            $especialistas = Especialista::with('usuario')
                ->where('activo', true)
                ->whereHas('usuario', function ($query) {
                    $query->where('activo', true);
                })
                ->get();

            $estados = EstadoCita::whereIn('codigo', [
                'PENDIENTE',
                'CONFIRMADA',
                'COMPLETADA',
                'NO_ASISTIO',
            ])->get();

            return view('citas.edit', compact(
                'cita',
                'pacientes',
                'servicios',
                'especialistas',
                'estados'
            ));

        } catch (Throwable $e) {
            Log::error('Error al cargar la edición de la cita.', [
                'id_cita' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('citas.index')
                ->with('error', 'No fue posible cargar la cita.');
        }
    }

    public function update(Request $request, $id)
    {
        $datos = $request->validate([
            'id_paciente' => [
                'required',
                'integer',
                'exists:pacientes,id_paciente',
            ],
            'id_servicio' => [
                'required',
                'integer',
                'exists:servicios,id_servicio',
            ],
            'id_especialista' => [
                'required',
                'integer',
                'exists:especialistas,id_especialista',
            ],
            'id_estado_cita' => [
                'required',
                'integer',
                'exists:estados_cita,id_estado_cita',
            ],
            'fecha_cita' => [
                'required',
                'date',
            ],
            'hora_inicio' => [
                'required',
                'date_format:H:i',
            ],
            'hora_fin' => [
                'required',
                'date_format:H:i',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        try {
            $cita = Cita::with('estado')->findOrFail($id);

            if ($cita->estado?->codigo === 'CANCELADA') {
                return redirect()
                    ->route('citas.show', $id)
                    ->with('error', 'Una cita cancelada no puede editarse.');
            }

            $inicio = Carbon::createFromFormat(
                'Y-m-d H:i',
                $datos['fecha_cita'] . ' ' . $datos['hora_inicio']
            );

            $fin = Carbon::createFromFormat(
                'Y-m-d H:i',
                $datos['fecha_cita'] . ' ' . $datos['hora_fin']
            );

            if ($fin->lessThanOrEqualTo($inicio)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora_fin' => 'La hora de finalización debe ser posterior a la hora de inicio.',
                    ]);
            }

            $estado = EstadoCita::findOrFail(
                $datos['id_estado_cita']
            );

            if ($estado->codigo === 'CANCELADA') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_estado_cita' => 'Utiliza la opción Cancelar cita.',
                    ]);
            }

            $horarioOcupado = Cita::where(
                'id_especialista',
                $datos['id_especialista']
            )
                ->where('id_cita', '!=', $cita->id_cita)
                ->where('inicio', '<', $fin)
                ->where('fin', '>', $inicio)
                ->whereHas('estado', function ($query) {
                    $query->where('codigo', '!=', 'CANCELADA');
                })
                ->exists();

            if ($horarioOcupado) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora_inicio' => 'El especialista ya tiene una cita en ese horario.',
                    ]);
            }

            $cita->update([
                'id_paciente' => $datos['id_paciente'],
                'id_especialista' => $datos['id_especialista'],
                'id_servicio' => $datos['id_servicio'],
                'id_estado_cita' => $datos['id_estado_cita'],
                'inicio' => $inicio,
                'fin' => $fin,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            return redirect()
                ->route('citas.show', $cita->id_cita)
                ->with('success', 'La cita fue actualizada correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al actualizar la cita.', [
                'id_cita' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible actualizar la cita.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $cita = Cita::with('estado')->findOrFail($id);

            if ($cita->estado?->codigo === 'CANCELADA') {
                return back()
                    ->with('error', 'La cita ya se encuentra cancelada.');
            }

            if ($cita->estado?->codigo === 'COMPLETADA') {
                return back()
                    ->with('error', 'Una cita completada no puede cancelarse.');
            }

            $estadoCancelado = EstadoCita::where(
                'codigo',
                'CANCELADA'
            )->firstOrFail();

            $request->validate([
                'motivo_cancelacion' => [
                    'nullable',
                    'string',
                    'max:250',
                ],
            ]);

            $cita->update([
                'id_estado_cita' => $estadoCancelado->id_estado_cita,
                'motivo_cancelacion' => $request->motivo_cancelacion,
            ]);

            return redirect()
                ->route('citas.show', $cita->id_cita)
                ->with('success', 'La cita fue cancelada correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al cancelar la cita.', [
                'id_cita' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'No fue posible cancelar la cita.');
        }
    }
}