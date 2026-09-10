@extends('layouts.admin')

@section('title', 'Detalle del usuario')

@section('content')
@php
    $usuario = $usuario ?? (object) [
        'id_usuario' => 2,
        'id_rol' => 3,
        'nombres' => 'Ana Lucía',
        'apellidos' => 'Ruiz Morales',
        'usuario' => 'aruiz',
        'correo' => 'ana@bajwam.com',
        'telefono' => '5555-0188',
        'activo' => true,
        'fecha_creacion' => '2026-08-22 11:10:00',
        'fecha_actualizacion' => '2026-09-08 16:35:00',
        'rol' => (object) ['nombre' => 'ESPECIALISTA', 'descripcion' => 'Registra consultas y tratamientos.'],
    ];

    $nombreCompleto = trim(data_get($usuario, 'nombres', '').' '.data_get($usuario, 'apellidos', ''));
    $iniciales = strtoupper(mb_substr(data_get($usuario, 'nombres', 'U'), 0, 1).mb_substr(data_get($usuario, 'apellidos', ''), 0, 1));
    $activo = (bool) data_get($usuario, 'activo', true);

    $formatearFecha = function ($fecha) {
        if (!$fecha) {
            return 'Sin registro';
        }

        try {
            return \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y H:i');
        } catch (\Throwable $e) {
            return $fecha;
        }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Usuarios</div>
        <h1 class="page-title">Detalle del usuario</h1>
        <p class="page-subtitle">Consulta los datos y permisos de la cuenta seleccionada.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/usuarios') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-2"></i>Regresar
        </a>
        <a href="{{ url('/usuarios/'.data_get($usuario, 'id_usuario').'/edit') }}" class="btn btn-brand">
            <i class="bi bi-pencil-square me-2"></i>Editar
        </a>
    </div>
</div>

<div class="card bw-card record-hero mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
            <span class="detail-avatar">{{ $iniciales ?: 'U' }}</span>
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h2 class="record-hero-title mb-0">{{ $nombreCompleto ?: 'Sin nombre' }}</h2>
                    <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                        {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                </div>
                <div class="text-secondary mb-3">{{ '@'.data_get($usuario, 'usuario') }} · Usuario #{{ data_get($usuario, 'id_usuario') }}</div>
                <span class="role-badge fs-6">{{ data_get($usuario, 'rol.nombre', 'SIN ROL') }}</span>
            </div>
            <div class="record-meta text-md-end">
                <span>Última actualización</span>
                <strong>{{ $formatearFecha(data_get($usuario, 'fecha_actualizacion')) }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-brand"></i>
                <h2 class="card-title-sm mb-0">Información de contacto</h2>
            </div>
            <div class="card-body p-4">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nombres</span>
                        <strong class="detail-value">{{ data_get($usuario, 'nombres', 'No registrado') }}</strong>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Apellidos</span>
                        <strong class="detail-value">{{ data_get($usuario, 'apellidos', 'No registrado') }}</strong>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Correo electrónico</span>
                        <a class="detail-value text-decoration-none" href="mailto:{{ data_get($usuario, 'correo') }}">{{ data_get($usuario, 'correo', 'No registrado') }}</a>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Teléfono</span>
                        <strong class="detail-value">{{ data_get($usuario, 'telefono', 'No registrado') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-green-bw"></i>
                <h2 class="card-title-sm mb-0">Seguridad y acceso</h2>
            </div>
            <div class="card-body p-4">
                <div class="info-callout mb-3">
                    <span class="info-callout-icon"><i class="bi bi-lock-fill"></i></span>
                    <div>
                        <div class="fw-bold">Contraseña protegida</div>
                        <div class="small text-secondary">Por seguridad, la contraseña nunca se muestra en esta pantalla.</div>
                    </div>
                </div>

                <div class="detail-item compact mb-3">
                    <span class="detail-label">Rol asignado</span>
                    <strong class="detail-value">{{ data_get($usuario, 'rol.nombre', 'Sin rol') }}</strong>
                </div>
                <div class="detail-item compact mb-3">
                    <span class="detail-label">Descripción del rol</span>
                    <span class="detail-value">{{ data_get($usuario, 'rol.descripcion', 'Sin descripción') }}</span>
                </div>
                <div class="detail-item compact">
                    <span class="detail-label">Fecha de creación</span>
                    <strong class="detail-value">{{ $formatearFecha(data_get($usuario, 'fecha_creacion')) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
