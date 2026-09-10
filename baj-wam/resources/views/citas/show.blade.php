@extends('layouts.admin')

@section('title', 'Detalle de la cita')

@section('content')
@php
    $cita = $cita ?? (object) [
        'id_cita' => 1,
        'inicio' => '2026-09-10 08:00:00',
        'fin' => '2026-09-10 09:00:00',
        'observaciones' => 'Primera sesión de tratamiento. La paciente solicita seguimiento por dolor lumbar.',
        'motivo_cancelacion' => null,
        'fecha_creacion' => '2026-09-08 14:20:00',
        'paciente' => ['id_paciente' => 1, 'nombres' => 'María Fernanda', 'apellidos' => 'López Castillo', 'telefono' => '5555-2100', 'correo' => 'maria@example.com'],
        'servicio' => ['nombre' => 'Acupuntura', 'duracion_minutos' => 60],
        'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz', 'profesion' => 'Acupunturista'],
        'estado' => ['codigo' => 'CONFIRMADA', 'nombre' => 'Confirmada'],
        'usuario_registro' => ['nombre_completo' => 'Sofía López'],
        'consulta' => null,
    ];

    $id = data_get($cita, 'id_cita');
    $inicio = data_get($cita, 'inicio');
    $fin = data_get($cita, 'fin');
    $codigoEstado = strtoupper(data_get($cita, 'estado.codigo', 'PENDIENTE'));
    $nombreEstado = data_get($cita, 'estado.nombre', str_replace('_', ' ', $codigoEstado));
    $claseEstado = strtolower(str_replace('_', '-', $codigoEstado));
    $nombres = data_get($cita, 'paciente.nombres', '');
    $apellidos = data_get($cita, 'paciente.apellidos', '');
    $nombrePaciente = trim($nombres.' '.$apellidos);
    $iniciales = strtoupper(mb_substr($nombres, 0, 1).mb_substr($apellidos, 0, 1));
    $fecha = function ($valor, $formato) {
        if (!$valor) return 'Sin registro';
        try { return \Illuminate\Support\Carbon::parse($valor)->format($formato); }
        catch (\Throwable $e) { return $valor; }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Agenda clínica</div>
        <h1 class="page-title">Detalle de la cita</h1>
        <p class="page-subtitle">Consulta la información completa antes de atender o reprogramar.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/citas') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
        <a href="{{ url('/citas/'.$id.'/edit') }}" class="btn btn-brand"><i class="bi bi-pencil me-2"></i>Editar</a>
    </div>
</div>

<div class="card bw-card appointment-hero mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <div class="appointment-hero-date">
                        <strong>{{ $fecha($inicio, 'd') }}</strong>
                        <span>{{ strtoupper($fecha($inicio, 'M')) }}</span>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge-status status-{{ $claseEstado }}">{{ mb_strtoupper($nombreEstado) }}</span>
                            <span class="role-badge">CITA #{{ $id }}</span>
                        </div>
                        <h2 class="record-hero-title mb-1">{{ $fecha($inicio, 'l, d \d\e F') }}</h2>
                        <div class="appointment-hero-time"><i class="bi bi-clock-fill"></i>{{ $fecha($inicio, 'H:i') }} - {{ $fecha($fin, 'H:i') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="appointment-patient-card">
                    <span class="detail-avatar patient-detail-avatar">{{ $iniciales ?: 'P' }}</span>
                    <div class="min-w-0">
                        <span class="detail-label">Paciente</span>
                        <a href="{{ url('/pacientes/'.data_get($cita, 'paciente.id_paciente')) }}" class="record-name fs-5">{{ $nombrePaciente }}</a>
                        <span class="record-subtitle">{{ data_get($cita, 'paciente.telefono') }} · {{ data_get($cita, 'paciente.correo', 'Sin correo') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-7">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="card-title-sm mb-0">Datos de la atención</h2>
                <i class="bi bi-calendar2-check text-brand"></i>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item"><span class="detail-label">Servicio</span><strong class="detail-value">{{ data_get($cita, 'servicio.nombre') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Duración</span><strong class="detail-value">{{ data_get($cita, 'servicio.duracion_minutos') }} minutos</strong></div>
                    <div class="detail-item"><span class="detail-label">Especialista</span><strong class="detail-value">{{ data_get($cita, 'especialista.nombre_completo') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Profesión</span><strong class="detail-value">{{ data_get($cita, 'especialista.profesion', 'No indicada') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Registrada por</span><strong class="detail-value">{{ data_get($cita, 'usuario_registro.nombre_completo', 'Usuario del sistema') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Fecha de registro</span><strong class="detail-value">{{ $fecha(data_get($cita, 'fecha_creacion'), 'd/m/Y H:i') }}</strong></div>
                </div>
                <div class="detail-item mt-3">
                    <span class="detail-label">Observaciones</span>
                    <p class="detail-value mb-0">{{ data_get($cita, 'observaciones', 'No se registraron observaciones.') }}</p>
                </div>
                @if ($codigoEstado === 'CANCELADA')
                    <div class="detail-item mt-3 border-danger-subtle bg-danger-subtle">
                        <span class="detail-label text-danger">Motivo de cancelación</span>
                        <p class="detail-value mb-0">{{ data_get($cita, 'motivo_cancelacion', 'No indicado.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="card-title-sm mb-0">Acciones de la cita</h2>
                <i class="bi bi-lightning-charge-fill text-warning"></i>
            </div>
            <div class="card-body d-grid gap-3">
                @if (data_get($cita, 'consulta'))
                    <a href="{{ url('/consultas/'.data_get($cita, 'consulta.id_consulta')) }}" class="action-tile is-success">
                        <span><i class="bi bi-clipboard2-pulse-fill"></i></span>
                        <div><strong>Ver consulta registrada</strong><small>La atención clínica ya fue documentada.</small></div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @elseif (!in_array($codigoEstado, ['CANCELADA', 'NO_ASISTIO']))
                    <a href="{{ url('/consultas/create?cita='.$id) }}" class="action-tile">
                        <span><i class="bi bi-clipboard2-plus-fill"></i></span>
                        <div><strong>Registrar consulta</strong><small>Documenta el tratamiento y recomendaciones.</small></div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif

                <a href="{{ url('/pacientes/'.data_get($cita, 'paciente.id_paciente')) }}" class="action-tile">
                    <span><i class="bi bi-person-vcard-fill"></i></span>
                    <div><strong>Ver expediente</strong><small>Consulta el historial del paciente.</small></div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                @if (!in_array($codigoEstado, ['CANCELADA', 'COMPLETADA']))
                    <form method="POST" action="{{ url('/citas/'.$id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="action-tile is-danger w-100 text-start" type="submit" data-confirm-delete="La cita cambiará a estado cancelada. ¿Deseas continuar?">
                            <span><i class="bi bi-calendar2-x-fill"></i></span>
                            <div><strong>Cancelar cita</strong><small>El registro y su historial se conservarán.</small></div>
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
