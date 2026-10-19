@extends('layouts.admin')

@section('title', 'Detalle del usuario')

@section('content')

    @php
        $nombreCompleto = trim($usuario->nombres . ' ' . $usuario->apellidos);

        $iniciales = strtoupper(mb_substr($usuario->nombres ?: 'U', 0, 1) . mb_substr($usuario->apellidos ?: '', 0, 1));

        $activo = (bool) $usuario->activo;

        $fechaCreacion = $usuario->fecha_creacion ? $usuario->fecha_creacion->format('d/m/Y H:i') : 'Sin registro';

        $fechaActualizacion = $usuario->fecha_actualizacion
            ? $usuario->fecha_actualizacion->format('d/m/Y H:i')
            : 'Sin registro';
    @endphp

    <div class="page-header">
        <div>
            <div class="page-eyebrow">
                Usuarios
            </div> 

            <h1 class="page-title">
                Detalle del usuario
            </h1>

            <p class="page-subtitle">
                Consulta los datos y permisos de la cuenta seleccionada.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('usuarios.index') }}" class="btn btn-light border">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>

            @can('usuarios.editar')
                <a href="{{ url('/usuarios/' . $usuario->id_usuario . '/edit') }}" class="btn btn-brand">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar
                </a>
            @endcan

        </div>
    </div>

    <div class="card bw-card record-hero mb-4">

        <div class="card-body p-4 p-lg-5">

            <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">

                <span class="detail-avatar">
                    {{ $iniciales ?: 'U' }}
                </span>

                <div class="flex-grow-1">

                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                        <h2 class="record-hero-title mb-0">
                            {{ $nombreCompleto ?: 'Sin nombre' }}
                        </h2>

                        <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                            {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                        </span>

                    </div>

                    <div class="text-secondary mb-3">
                        {{ '@' . $usuario->usuario }}
                        ·
                        Usuario #{{ $usuario->id_usuario }}
                    </div>

                    <span class="role-badge fs-6">
                        {{ $usuario->rol?->nombre ?? 'SIN ROL' }}
                    </span>

                </div>

                <div class="record-meta text-md-end">

                    <span>
                        Última actualización
                    </span>

                    <strong>
                        {{ $fechaActualizacion }}
                    </strong>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-12 col-xl-7">

            <div class="card bw-card h-100">

                <div class="card-header d-flex align-items-center gap-2">

                    <i class="bi bi-person-lines-fill text-brand"></i>

                    <h2 class="card-title-sm mb-0">
                        Información de contacto
                    </h2>

                </div>

                <div class="card-body p-4">

                    <div class="detail-grid">

                        <div class="detail-item">

                            <span class="detail-label">
                                Nombres
                            </span>

                            <strong class="detail-value">
                                {{ $usuario->nombres ?: 'No registrado' }}
                            </strong>

                        </div>

                        <div class="detail-item">

                            <span class="detail-label">
                                Apellidos
                            </span>

                            <strong class="detail-value">
                                {{ $usuario->apellidos ?: 'No registrado' }}
                            </strong>

                        </div>

                        <div class="detail-item">

                            <span class="detail-label">
                                Correo electrónico
                            </span>

                            @if ($usuario->correo)
                                <a class="detail-value text-decoration-none" href="mailto:{{ $usuario->correo }}">
                                    {{ $usuario->correo }}
                                </a>
                            @else
                                <span class="detail-value">
                                    No registrado
                                </span>
                            @endif

                        </div>

                        <div class="detail-item">

                            <span class="detail-label">
                                Teléfono
                            </span>

                            <strong class="detail-value">
                                {{ $usuario->telefono ?: 'No registrado' }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-xl-5">

            <div class="card bw-card h-100">

                <div class="card-header d-flex align-items-center gap-2">

                    <i class="bi bi-shield-check text-green-bw"></i>

                    <h2 class="card-title-sm mb-0">
                        Seguridad y acceso
                    </h2>

                </div>

                <div class="card-body p-4">

                    <div class="info-callout mb-3">

                        <span class="info-callout-icon">
                            <i class="bi bi-lock-fill"></i>
                        </span>

                        <div>

                            <div class="fw-bold">
                                Contraseña protegida
                            </div>

                            <div class="small text-secondary">
                                Por seguridad, la contraseña nunca se muestra en esta pantalla.
                            </div>

                        </div>

                    </div>
                    @can('usuarios.restablecer_password')

                        @if (auth()->id() !== $usuario->id_usuario)
                            <div class="border rounded-3 p-3 mb-3">

                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">

                                    <div>
                                        <div class="fw-bold">
                                            Restablecer contraseña
                                        </div>

                                        <div class="small text-secondary">
                                            Genera una contraseña temporal para este usuario.
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-outline-warning" id="btnRestablecerPassword"
                                        data-url="{{ route('usuarios.restablecer-password', $usuario) }}"
                                        data-nombre="{{ $nombreCompleto }}">
                                        <i class="bi bi-key-fill me-2"></i>
                                        Restablecer
                                    </button>

                                </div>

                            </div>

                            <div id="resultadoPassword"></div>
                        @endif

                    @endcan

                    <div class="detail-item compact mb-3">

                        <span class="detail-label">
                            Rol asignado
                        </span>

                        <strong class="detail-value">
                            {{ $usuario->rol?->nombre ?? 'Sin rol' }}
                        </strong>

                    </div>

                    <div class="detail-item compact mb-3">

                        <span class="detail-label">
                            Descripción del rol
                        </span>

                        <span class="detail-value">
                            {{ $usuario->rol?->descripcion ?? 'Sin descripción' }}
                        </span>

                    </div>

                    <div class="detail-item compact">

                        <span class="detail-label">
                            Fecha de creación
                        </span>

                        <strong class="detail-value">
                            {{ $fechaCreacion }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/usuarios/usuario-show.js') }}"></script>
@endpush
