<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialista;
use App\Models\EstadoCita;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\SolicitudCita;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class SolicitudCitaController extends Controller
{
    public function index()
    {
        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('public.inicio', compact('servicios'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'numero_expediente' => $this->normalizarNumeroExpediente($request->input('numero_expediente')),
        ]);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:160'],
            'telefono' => ['required', 'string', 'min:8', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'dpi' => ['nullable', 'digits:13'],
            'numero_expediente' => ['nullable', 'regex:/^PAC-\d{6,}$/'],
            'id_servicio' => ['required', 'integer', 'exists:servicios,id_servicio'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ], [
            'dpi.digits' => 'El DPI debe contener exactamente 13 números.',
            'numero_expediente.regex' => 'El número de expediente debe tener el formato PAC-000001.',
            'telefono.required' => 'Debes ingresar un número de teléfono.',
            'El teléfono debe tener el formato 5555-5555.',
        ]);

        try {
            $servicioActivo = Servicio::where('id_servicio', $datos['id_servicio'])
                ->where('activo', true)
                ->exists();

            if (!$servicioActivo) {
                return back()->withInput()->withErrors([
                    'id_servicio' => 'El servicio seleccionado no está disponible.',
                ]);
            }

            $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $datos['fecha'] . ' ' . $datos['hora']);

            if ($fechaHora->isPast()) {
                return back()->withInput()->withErrors([
                    'hora' => 'Debes seleccionar una fecha y hora futura.',
                ]);
            }

            SolicitudCita::create([
                'id_servicio' => $datos['id_servicio'],
                'id_cita' => null,
                'nombre' => trim($datos['nombre']),
                'telefono' => trim($datos['telefono']),
                'dpi' => $datos['dpi'] ?? null,
                'numero_expediente' => $datos['numero_expediente'] ?? null,
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'comentario' => $datos['comentario'] ?? null,
                'estado' => 'PENDIENTE',
            ]);

            return redirect()
                ->to(route('inicio') . '#cita')
                ->with('success', 'Tu solicitud fue enviada correctamente. La clínica confirmará la disponibilidad.');
        } catch (Throwable $e) {
            Log::error('No fue posible registrar una solicitud de cita.', [
                'codigo' => 'SOLICITUD_REGISTRO_ERROR',
            ]);

            return back()->withInput()->with('error', 'No fue posible enviar la solicitud.');
        }
    }

    public function solicitudes(Request $request)
    {
        $consulta = SolicitudCita::with(['servicio', 'cita']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $consulta->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', '%' . $buscar . '%')
                    ->orWhere('telefono', 'like', '%' . $buscar . '%')
                    ->orWhere('dpi', 'like', '%' . $buscar . '%')
                    ->orWhere('numero_expediente', 'like', '%' . $buscar . '%');
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

        return view('citas.solicitudes.index', compact('solicitudes', 'resumen'));
    }

    public function procesar($id)
    {
        try {
            $solicitud = SolicitudCita::with('servicio')->findOrFail($id);

            if ($solicitud->estado !== 'PENDIENTE') {
                return redirect()->route('solicitudes-cita.index')
                    ->with('error', 'Esta solicitud ya fue procesada.');
            }

            $especialistas = Especialista::with('usuario')
                ->where('activo', true)
                ->whereHas('usuario', fn($query) => $query->where('activo', true))
                ->get();

            $pacientePorDpi = $solicitud->dpi
                ? Paciente::where('dpi', $solicitud->dpi)->first()
                : null;
            $pacientePorExpediente = $this->pacientePorNumeroExpediente($solicitud->numero_expediente);
            $identificadoresContradictorios = $pacientePorDpi
                && $pacientePorExpediente
                && $pacientePorDpi->id_paciente !== $pacientePorExpediente->id_paciente;
            $pacienteIdentificado = $identificadoresContradictorios
                ? null
                : ($pacientePorDpi ?: $pacientePorExpediente);

            $pacienteSeleccionado = old('id_paciente')
                ? Paciente::find(old('id_paciente'))
                : $pacienteIdentificado;

            $coincidencias = collect();

            if (!$solicitud->dpi && !$solicitud->numero_expediente) {
                $primerNombre = trim(explode(' ', $solicitud->nombre)[0]);

                $coincidencias = Paciente::query()
                    ->where(function ($query) use ($solicitud, $primerNombre) {
                        $query->where('telefono', $solicitud->telefono)
                            ->orWhere('nombres', 'like', '%' . $primerNombre . '%');
                    })
                    ->orderByDesc('activo')
                    ->orderBy('nombres')
                    ->limit(5)
                    ->get();
            } elseif ($identificadoresContradictorios) {
                $coincidencias = collect([$pacientePorDpi, $pacientePorExpediente])
                    ->unique('id_paciente')
                    ->values();
            }

            [$nombresSugeridos, $apellidosSugeridos] = $this->separarNombre($solicitud->nombre);

            return view('citas.solicitudes.procesar', compact(
                'solicitud',
                'especialistas',
                'pacientePorDpi',
                'pacientePorExpediente',
                'pacienteIdentificado',
                'identificadoresContradictorios',
                'pacienteSeleccionado',
                'coincidencias',
                'nombresSugeridos',
                'apellidosSugeridos'
            ));
        } catch (Throwable $e) {
            Log::error('No fue posible cargar una solicitud para procesarla.', [
                'codigo' => 'SOLICITUD_PROCESAR_ERROR',
                'id_solicitud' => (int) $id,
            ]);

            return redirect()->route('solicitudes-cita.index')
                ->with('error', 'No fue posible cargar la solicitud.');
        }
    }

    public function aprobar(Request $request, $id)
    {
        $datos = $request->validate([
            'tipo_paciente' => ['required', 'in:existente,nuevo'],
            'id_paciente' => ['nullable', 'required_if:tipo_paciente,existente', 'integer', 'exists:pacientes,id_paciente'],
            'nombres' => ['nullable', 'required_if:tipo_paciente,nuevo', 'string', 'max:80'],
            'apellidos' => ['nullable', 'required_if:tipo_paciente,nuevo', 'string', 'max:80'],
            'telefono' => ['required', 'string', 'min:8', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'dpi' => ['nullable', 'digits:13'],
            'confirmar_identidad' => ['sometimes', 'accepted'],
            'resolver_identificadores' => ['sometimes', 'accepted'],
            'id_especialista' => ['required', 'integer', 'exists:especialistas,id_especialista'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
        ], [
            'dpi.digits' => 'El DPI debe contener exactamente 13 números.',
            'telefono.required_if' => 'Debes ingresar un número de teléfono para el nuevo paciente.',
            'El teléfono debe tener el formato 5555-5555.',
            'apellidos.required_if' => 'Debes ingresar los apellidos del paciente.',
            'nombres.required_if' => 'Debes ingresar los nombres del paciente.',
        ]);

        try {
            $idCita = DB::transaction(function () use ($datos, $id, $request) {
                $solicitud = SolicitudCita::query()
                    ->with('servicio')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($solicitud->estado !== 'PENDIENTE') {
                    throw ValidationException::withMessages([
                        'solicitud' => 'La solicitud ya fue procesada.',
                    ]);
                }

                $servicio = Servicio::query()
                    ->whereKey($solicitud->id_servicio)
                    ->where('activo', true)
                    ->lockForUpdate()
                    ->first();

                if (!$servicio) {
                    throw ValidationException::withMessages([
                        'solicitud' => 'El servicio solicitado ya no está disponible.',
                    ]);
                }

                $pacientePorDpi = $solicitud->dpi
                    ? Paciente::where('dpi', $solicitud->dpi)->lockForUpdate()->first()
                    : null;
                $pacientePorExpediente = $this->pacientePorNumeroExpediente(
                    $solicitud->numero_expediente,
                    true
                );

                $identificadoresContradictorios = $pacientePorDpi && $pacientePorExpediente
                    && $pacientePorDpi->id_paciente !== $pacientePorExpediente->id_paciente;

                if (
                    ($pacientePorDpi && !$pacientePorDpi->activo)
                    || ($pacientePorExpediente && !$pacientePorExpediente->activo)
                ) {
                    throw ValidationException::withMessages([
                        'id_paciente' => 'El expediente identificado en la solicitud está inactivo y debe reactivarse antes de crear una cita.',
                    ]);
                }

                if ($identificadoresContradictorios) {
                    $idsIdentificados = [$pacientePorDpi->id_paciente, $pacientePorExpediente->id_paciente];

                    if (!$request->boolean('resolver_identificadores')) {
                        throw ValidationException::withMessages([
                            'identificacion' => 'Confirma que resolviste la discrepancia entre DPI y expediente antes de aprobar la solicitud.',
                        ]);
                    }

                    if (
                        $datos['tipo_paciente'] !== 'existente'
                        || !in_array((int) $datos['id_paciente'], $idsIdentificados, true)
                    ) {
                        throw ValidationException::withMessages([
                            'id_paciente' => 'Selecciona uno de los dos expedientes identificados después de la revisión autorizada.',
                        ]);
                    }

                    $pacienteIdentificado = Paciente::query()
                        ->lockForUpdate()
                        ->findOrFail($datos['id_paciente']);
                } else {
                    $pacienteIdentificado = $pacientePorDpi ?: $pacientePorExpediente;
                }

                if (!$identificadoresContradictorios && $pacienteIdentificado && !$request->boolean('confirmar_identidad')) {
                    throw ValidationException::withMessages([
                        'confirmar_identidad' => 'Confirma que verificaste la identidad antes de continuar.',
                    ]);
                }

                if (
                    $pacienteIdentificado && (
                        $datos['tipo_paciente'] !== 'existente'
                        || (int) $datos['id_paciente'] !== $pacienteIdentificado->id_paciente
                    )
                ) {
                    throw ValidationException::withMessages([
                        'id_paciente' => 'Los identificadores de la solicitud corresponden a un paciente existente. Selecciónalo para continuar.',
                    ]);
                }

                $especialista = Especialista::query()
                    ->whereKey($datos['id_especialista'])
                    ->where('activo', true)
                    ->whereHas('usuario', fn($query) => $query->where('activo', true))
                    ->lockForUpdate()
                    ->first();

                if (!$especialista) {
                    throw ValidationException::withMessages([
                        'id_especialista' => 'El especialista seleccionado no está disponible.',
                    ]);
                }

                $inicio = Carbon::createFromFormat('Y-m-d H:i', $datos['fecha'] . ' ' . $datos['hora']);

                if ($inicio->isPast()) {
                    throw ValidationException::withMessages([
                        'hora' => 'La fecha y hora deben ser futuras.',
                    ]);
                }

                $fin = $inicio->copy()->addMinutes($servicio->duracion_minutos);

                $horarioOcupado = Cita::where('id_especialista', $especialista->id_especialista)
                    ->where('inicio', '<', $fin)
                    ->where('fin', '>', $inicio)
                    ->whereHas('estado', fn($query) => $query->where('codigo', '!=', 'CANCELADA'))
                    ->lockForUpdate()
                    ->exists();

                if ($horarioOcupado) {
                    throw ValidationException::withMessages([
                        'hora' => 'El especialista ya tiene una cita en ese horario.',
                    ]);
                }

                if ($datos['tipo_paciente'] === 'existente') {
                    $paciente = Paciente::query()->lockForUpdate()->findOrFail($datos['id_paciente']);

                    if (!$paciente->activo) {
                        throw ValidationException::withMessages([
                            'id_paciente' => 'El expediente seleccionado está inactivo y debe reactivarse antes de asignarle una cita.',
                        ]);
                    }
                } else {
                    $dpi = $datos['dpi'] ?? null;

                    if ($dpi && Paciente::where('dpi', $dpi)->lockForUpdate()->exists()) {
                        throw ValidationException::withMessages([
                            'dpi' => 'Ya existe un paciente con este DPI. Selecciónalo como paciente existente.',
                        ]);
                    }

                    $paciente = Paciente::create([
                        'id_usuario_registro' => $request->user()->id_usuario,
                        'dpi' => $dpi,
                        'nombres' => trim($datos['nombres']),
                        'apellidos' => trim($datos['apellidos']),
                        'telefono' => trim($datos['telefono']),
                        'activo' => true,
                    ]);
                }

                $estado = EstadoCita::where('codigo', 'CONFIRMADA')->firstOrFail();

                $cita = Cita::create([
                    'id_paciente' => $paciente->id_paciente,
                    'id_especialista' => $especialista->id_especialista,
                    'id_servicio' => $servicio->id_servicio,
                    'id_estado_cita' => $estado->id_estado_cita,
                    'id_usuario_registro' => $request->user()->id_usuario,
                    'inicio' => $inicio,
                    'fin' => $fin,
                    'observaciones' => $solicitud->comentario,
                    'motivo_cancelacion' => null,
                ]);

                $solicitud->update([
                    'id_cita' => $cita->id_cita,
                    'estado' => 'APROBADA',
                    'fecha_respuesta' => now(),
                ]);

                return $cita->id_cita;
            });

            return redirect()->route('citas.show', $idCita)
                ->with('success', 'Solicitud aprobada y cita creada correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            if ($this->esErrorDpiDuplicado($e)) {
                throw ValidationException::withMessages([
                    'dpi' => 'Ya existe un paciente con este DPI. Selecciónalo como paciente existente.',
                ]);
            }

            Log::error('No fue posible aprobar una solicitud de cita.', [
                'codigo' => 'SOLICITUD_APROBACION_BD_ERROR',
                'id_solicitud' => (int) $id,
            ]);

            return back()->withInput()->with('error', 'No fue posible aprobar la solicitud.');
        } catch (Throwable $e) {
            Log::error('No fue posible aprobar una solicitud de cita.', [
                'codigo' => 'SOLICITUD_APROBACION_ERROR',
                'id_solicitud' => (int) $id,
            ]);

            return back()->withInput()->with('error', 'No fue posible aprobar la solicitud.');
        }
    }

    public function rechazar($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $solicitud = SolicitudCita::query()->lockForUpdate()->findOrFail($id);

                if ($solicitud->estado !== 'PENDIENTE') {
                    throw ValidationException::withMessages([
                        'solicitud' => 'La solicitud ya fue procesada.',
                    ]);
                }

                $solicitud->update([
                    'estado' => 'RECHAZADA',
                    'fecha_respuesta' => now(),
                ]);
            });

            return redirect()->route('solicitudes-cita.index')
                ->with('success', 'Solicitud rechazada correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('No fue posible rechazar una solicitud de cita.', [
                'codigo' => 'SOLICITUD_RECHAZO_ERROR',
                'id_solicitud' => (int) $id,
            ]);

            return back()->with('error', 'No fue posible rechazar la solicitud.');
        }
    }

    private function separarNombre(string $nombre): array
    {
        $partes = preg_split('/\s+/', trim($nombre));

        if (count($partes) === 1) {
            return [$partes[0], ''];
        }

        $cantidadApellidos = count($partes) > 3 ? 2 : 1;

        return [
            implode(' ', array_slice($partes, 0, -$cantidadApellidos)),
            implode(' ', array_slice($partes, -$cantidadApellidos)),
        ];
    }

    private function normalizarNumeroExpediente(?string $numeroExpediente): ?string
    {
        $normalizado = strtoupper(trim((string) $numeroExpediente));

        return $normalizado === '' ? null : $normalizado;
    }

    private function pacientePorNumeroExpediente(?string $numeroExpediente, bool $bloquear = false): ?Paciente
    {
        $numeroExpediente = $this->normalizarNumeroExpediente($numeroExpediente);

        if (!$numeroExpediente || !preg_match('/^PAC-(\d{6,})$/', $numeroExpediente, $coincidencias)) {
            return null;
        }

        $idPaciente = ltrim($coincidencias[1], '0');

        if ($idPaciente === '' || !ctype_digit($idPaciente)) {
            return null;
        }

        $consulta = Paciente::query()->where('id_paciente', $idPaciente);

        if ($bloquear) {
            $consulta->lockForUpdate();
        }

        return $consulta->first();
    }

    private function esErrorDpiDuplicado(QueryException $e): bool
    {
        return (string) $e->getCode() === '23000'
            && str_contains(strtolower($e->getMessage()), 'dpi');
    }
}
