@extends('layouts.admin')

@section('title', 'Expediente del paciente')

@section('content')
@php
    $paciente = $paciente ?? (object) [
        'id_paciente' => 1,
        'dpi' => '2456789010101',
        'nombres' => 'María Fernanda',
        'apellidos' => 'López Castillo',
        'fecha_nacimiento' => '1987-05-18',
        'sexo' => 'FEMENINO',
        'telefono' => '5555-2100',
        'correo' => 'maria@example.com',
        'direccion' => 'Jocotenango, Sacatepéquez',
        'activo' => true,
        'fecha_registro' => '2026-07-12 10:20:00',
        'usuarioRegistro' => (object) ['nombres' => 'Cristian', 'apellidos' => 'Pérez'],
    ];

    $resumenPaciente = $resumenPaciente ?? [
        'proxima_cita' => '15/09/2026 · 10:00',
        'ultima_cita' => '08/09/2026',
        'total_consultas' => 6,
    ];

    $historial = $historial ?? collect([
        ['fecha' => '08/09/2026', 'servicio' => 'Acupuntura', 'especialista' => 'Ana Ruiz', 'estado' => 'COMPLETADA'],
        ['fecha' => '26/08/2026', 'servicio' => 'Terapia nutricional', 'especialista' => 'Ana Ruiz', 'estado' => 'COMPLETADA'],
        ['fecha' => '10/08/2026', 'servicio' => 'Acupuntura láser', 'especialista' => 'Ana Ruiz', 'estado' => 'COMPLETADA'],
    ]);

    $nombreCompleto = trim(data_get($paciente, 'nombres', '').' '.data_get($paciente, 'apellidos', ''));
    $iniciales = strtoupper(mb_substr(data_get($paciente, 'nombres', 'P'), 0, 1).mb_substr(data_get($paciente, 'apellidos', ''), 0, 1));
    $activo = (bool) data_get($paciente, 'activo', true);

    $formatearFecha = function ($fecha, $formato = 'd/m/Y') {
        if (!$fecha) {
            return 'No registrada';
        }

        try {
            return \Illuminate\Support\Carbon::parse($fecha)->format($formato);
        } catch (\Throwable $e) {
            return $fecha;
        }
    };

    $edad = 'No registrada';
    if (data_get($paciente, 'fecha_nacimiento')) {
        try {
            $edad = \Illuminate\Support\Carbon::parse(data_get($paciente, 'fecha_nacimiento'))->age.' años';
        } catch (\Throwable $e) {
            $edad = 'No disponible';
        }
    }
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Pacientes</div>
        <h1 class="page-title">Expediente del paciente</h1>
        <p class="page-subtitle">Información general y resumen del historial de atención.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/pacientes') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-2"></i>Regresar
        </a>
        <a href="{{ url('/citas/create?paciente='.data_get($paciente, 'id_paciente')) }}" class="btn btn-outline-brand">
            <i class="bi bi-calendar-plus me-2"></i>Nueva cita
        </a>
        <a href="{{ url('/pacientes/'.data_get($paciente, 'id_paciente').'/edit') }}" class="btn btn-brand">
            <i class="bi bi-pencil-square me-2"></i>Editar
        </a>
    </div>
</div>

<div class="card bw-card record-hero mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
            <span class="detail-avatar patient-detail-avatar">{{ $iniciales ?: 'P' }}</span>
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h2 class="record-hero-title mb-0">{{ $nombreCompleto ?: 'Sin nombre' }}</h2>
                    <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                        {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                </div>
                <div class="text-secondary mb-3">Paciente #{{ str_pad((string) data_get($paciente, 'id_paciente'), 4, '0', STR_PAD_LEFT) }}</div>
                <div class="d-flex flex-wrap gap-3 text-secondary small">
                    <span><i class="bi bi-telephone me-1 text-brand"></i>{{ data_get($paciente, 'telefono', 'Sin teléfono') }}</span>
                    <span><i class="bi bi-envelope me-1 text-brand"></i>{{ data_get($paciente, 'correo', 'Sin correo') }}</span>
                </div>
            </div>
            <div class="record-meta text-md-end">
                <span>Registrado el</span>
                <strong>{{ $formatearFecha(data_get($paciente, 'fecha_registro')) }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand"><i class="bi bi-calendar-event"></i></span>
                <div>
                    <div class="mini-stat-value compact-value">{{ data_get($resumenPaciente, 'proxima_cita', 'Sin cita') }}</div>
                    <div class="mini-stat-label">Próxima cita</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw"><i class="bi bi-calendar-check"></i></span>
                <div>
                    <div class="mini-stat-value compact-value">{{ data_get($resumenPaciente, 'ultima_cita', 'Sin registro') }}</div>
                    <div class="mini-stat-label">Última cita</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-lime text-green-bw"><i class="bi bi-clipboard2-pulse"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenPaciente, 'total_consultas', 0) }}</div>
                    <div class="mini-stat-label">Consultas registradas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-5">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-brand"></i>
                <h2 class="card-title-sm mb-0">Información general</h2>
            </div>
            <div class="card-body p-4">
                <div class="detail-grid single-column">
                    <div class="detail-item">
                        <span class="detail-label">DPI</span>
                        <strong class="detail-value">{{ data_get($paciente, 'dpi', 'No registrado') ?: 'No registrado' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fecha de nacimiento</span>
                        <strong class="detail-value">{{ $formatearFecha(data_get($paciente, 'fecha_nacimiento')) }} · {{ $edad }}</strong>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Sexo</span>
                        <strong class="detail-value">{{ ucfirst(strtolower(str_replace('_', ' ', data_get($paciente, 'sexo', 'No registrado')))) }}</strong>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Dirección</span>
                        <strong class="detail-value">{{ data_get($paciente, 'direccion', 'No registrada') }}</strong>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Registrado por</span>
                        <strong class="detail-value">{{ trim(data_get($paciente, 'usuarioRegistro.nombres', '').' '.data_get($paciente, 'usuarioRegistro.apellidos', '')) ?: 'No disponible' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="card bw-card h-100">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-green-bw"></i>
                    <h2 class="card-title-sm mb-0">Historial reciente</h2>
                </div>
                <a href="{{ url('/consultas?paciente='.data_get($paciente, 'id_paciente')) }}" class="btn btn-sm btn-soft">
                    Ver historial completo
                </a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Servicio</th>
                            <th>Especialista</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historial as $registro)
                            <tr>
                                <td>{{ data_get($registro, 'fecha') }}</td>
                                <td class="fw-semibold">{{ data_get($registro, 'servicio') }}</td>
                                <td>{{ data_get($registro, 'especialista') }}</td>
                                <td>
                                    <span class="badge-status status-{{ strtolower(str_replace(' ', '-', data_get($registro, 'estado', 'pendiente'))) }}">
                                        {{ data_get($registro, 'estado', 'PENDIENTE') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-clipboard2-x"></i>
                                        No hay atenciones registradas.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
