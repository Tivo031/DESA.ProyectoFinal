@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content')
    @php
        $valor = fn($item, $campo, $default = null) => data_get($item, $campo, $default);
        $formatearFecha = function ($fecha) {
            if (!$fecha) {
                return 'Sin registro';
            }

            try {
                return \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y');
            } catch (\Throwable $e) {
                return $fecha;
            }
        };
    @endphp

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Administración</div>
            <h1 class="page-title">Usuarios</h1>
            <p class="page-subtitle">Administra los accesos, roles y estado de las cuentas internas.</p>
        </div>
        @can('usuarios.crear')
            <a href="{{ url('/usuarios/create') }}" class="btn btn-brand">
                <i class="bi bi-person-plus-fill me-2"></i>Nuevo usuario
            </a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="mini-stat-icon bg-soft-purple text-brand"><i class="bi bi-people-fill"></i></span>
                    <div>
                        <div class="mini-stat-value">{{ data_get($resumenUsuarios, 'total', 0) }}</div>
                        <div class="mini-stat-label">Usuarios registrados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="mini-stat-icon bg-soft-green text-green-bw"><i class="bi bi-person-check-fill"></i></span>
                    <div>
                        <div class="mini-stat-value">{{ data_get($resumenUsuarios, 'activos', 0) }}</div>
                        <div class="mini-stat-label">Cuentas activas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="mini-stat-icon bg-soft-danger text-danger"><i class="bi bi-person-dash-fill"></i></span>
                    <div>
                        <div class="mini-stat-value">{{ data_get($resumenUsuarios, 'inactivos', 0) }}</div>
                        <div class="mini-stat-label">Cuentas inactivas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bw-card">
        <div class="card-header">
            <form id="form-filtros-usuarios" class="row g-2 align-items-end" method="GET"
                action="{{ route('usuarios.index') }}" autocomplete="off">
                <div class="col-12 col-lg-5">
                    <label class="form-label visually-hidden" for="buscar">Buscar usuario</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="buscar" type="search" name="buscar"
                            value="{{ request('buscar') }}" placeholder="Buscar por nombre, usuario, correo, teléfono o rol"
                            autocomplete="new-password" autocapitalize="off" spellcheck="false">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <label class="form-label visually-hidden" for="rol">Rol</label>
                    <select class="form-select" id="rol" name="rol">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $valor($rol, 'id_rol') }}" @selected((string) request('rol') === (string) $valor($rol, 'id_rol'))>
                                {{ $valor($rol, 'nombre') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label visually-hidden" for="estado">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="1" @selected(request('estado') === '1')>Activos</option>
                        <option value="0" @selected(request('estado') === '0')>Inactivos</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                    <a class="btn btn-light border w-100" href="{{ route('usuarios.index') }}" title="Limpiar filtros"
                        aria-label="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Contacto</th>
                        <th>Rol</th>
                        <th class="text-nowrap" style="min-width: 110px;">Estado</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        @php
                            $id = $valor($usuario, 'id_usuario');
                            $nombres = $valor($usuario, 'nombres', '');
                            $apellidos = $valor($usuario, 'apellidos', '');
                            $nombreCompleto = trim($nombres . ' ' . $apellidos);
                            $iniciales = strtoupper(mb_substr($nombres, 0, 1) . mb_substr($apellidos, 0, 1));
                            $activo = (bool) $valor($usuario, 'activo', true);
                            $rolNombre = $valor($usuario, 'rol.nombre', $valor($usuario, 'nombre_rol', 'SIN ROL'));
                        @endphp
                        <tr>
                            <td>
                                <div class="record-person">
                                    <span class="list-avatar">{{ $iniciales ?: 'U' }}</span>
                                    <div class="min-w-0">
                                        <a class="record-name"
                                            href="{{ url('/usuarios/' . $id) }}">{{ $nombreCompleto ?: 'Sin nombre' }}</a>
                                        <span
                                            class="record-subtitle">{{ '@' . $valor($usuario, 'usuario', 'sin_usuario') }}
                                            ·
                                            ID
                                            {{ $id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $valor($usuario, 'correo', 'Sin correo') }}</div>
                                <div class="text-secondary small">{{ $valor($usuario, 'telefono', 'Sin teléfono') }}</div>
                            </td>
                            <td>
                                <span class="role-badge">{{ $rolNombre }}</span>
                            </td>
                            <td class="text-nowrap" style="min-width: 110px;">
                                <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                                    {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </td>
                            <td>{{ $formatearFecha($valor($usuario, 'fecha_creacion')) }}</td>
                            <td class="text-end text-nowrap">
                                <div class="table-actions justify-content-end">
                                    @can('usuarios.ver')
                                        <a class="btn btn-sm btn-light border" href="{{ url('/usuarios/' . $id) }}"
                                            title="Ver usuario" aria-label="Ver usuario">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endcan
                                    @can('usuarios.editar')
                                        <a class="btn btn-sm btn-light border" href="{{ url('/usuarios/' . $id . '/edit') }}"
                                            title="Editar usuario" aria-label="Editar usuario">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('usuarios.desactivar')
                                        @if ($activo)
                                            <form method="POST" action="{{ url('/usuarios/' . $id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-light border text-danger" type="submit"
                                                    title="Desactivar usuario" aria-label="Desactivar usuario"
                                                    data-confirm-delete="¿Deseas desactivar a {{ $nombreCompleto }}? La cuenta dejará de tener acceso al sistema.">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-person-x"></i>
                                    <div class="fw-bold mb-1">No se encontraron usuarios</div>
                                    <div class="small">Prueba con otros filtros o registra una nueva cuenta.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-lg-none p-3">
            @forelse ($usuarios as $usuario)
                @php
                    $id = $valor($usuario, 'id_usuario');
                    $nombres = $valor($usuario, 'nombres', '');
                    $apellidos = $valor($usuario, 'apellidos', '');

                    $nombreCompleto = trim($nombres . ' ' . $apellidos);

                    $iniciales = strtoupper(mb_substr($nombres, 0, 1) . mb_substr($apellidos, 0, 1));

                    $activo = (bool) $valor($usuario, 'activo', true);

                    $rolNombre = $valor($usuario, 'rol.nombre', $valor($usuario, 'nombre_rol', 'SIN ROL'));
                @endphp

                <div class="card bw-card mb-3">
                    <div class="card-body">

                        <div class="d-flex align-items-start gap-3 mb-3">

                            <span class="list-avatar flex-shrink-0">
                                {{ $iniciales ?: 'U' }}
                            </span>

                            <div class="flex-grow-1 min-w-0">

                                <div class="fw-bold text-break">
                                    {{ $nombreCompleto ?: 'Sin nombre' }}
                                </div>

                                <div class="text-secondary small">
                                    {{ '@' . $valor($usuario, 'usuario', 'sin_usuario') }}
                                    · ID {{ $id }}
                                </div>

                            </div>

                            <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                                {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>

                        </div>

                        <div class="row g-3 small">

                            <div class="col-12">
                                <div class="text-secondary">Correo</div>
                                <div class="fw-semibold text-break">
                                    {{ $valor($usuario, 'correo', 'Sin correo') }}
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="text-secondary">Teléfono</div>
                                <div class="fw-semibold">
                                    {{ $valor($usuario, 'telefono', 'Sin teléfono') }}
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="text-secondary">Rol</div>
                                <div>
                                    <span class="role-badge">
                                        {{ $rolNombre }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-secondary">Fecha de creación</div>
                                <div class="fw-semibold">
                                    {{ $formatearFecha($valor($usuario, 'fecha_creacion')) }}
                                </div>
                            </div>

                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            @can('usuarios.ver')
                                <a href="{{ url('/usuarios/' . $id) }}" class="btn btn-light border flex-fill">
                                    <i class="bi bi-eye me-1"></i>
                                    Ver
                                </a>
                            @endcan
                            @can('usuarios.editar')
                                <a href="{{ url('/usuarios/' . $id . '/edit') }}" class="btn btn-outline-brand flex-fill">
                                    <i class="bi bi-pencil me-1"></i>
                                    Editar
                                </a>
                            @endcan

                            @can('usuarios.desactivar')
                                @if ($activo)
                                    <form method="POST" action="{{ url('/usuarios/' . $id) }}" class="flex-fill">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-outline-danger w-100"
                                            data-confirm-delete="¿Deseas desactivar a {{ $nombreCompleto }}? La cuenta dejará de tener acceso al sistema.">

                                            <i class="bi bi-person-x me-1"></i>
                                            Desactivar

                                        </button>

                                    </form>
                                @endif
                            @endcan

                        </div>
                    </div>

                @empty

                    <div class="empty-state">
                        <i class="bi bi-person-x"></i>

                        <div class="fw-bold mb-1">
                            No se encontraron usuarios
                        </div>

                        <div class="small">
                            Prueba con otros filtros o registra una nueva cuenta.
                        </div>
                    </div>
            @endforelse
        </div>

        @if (is_object($usuarios) && method_exists($usuarios, 'links'))
            <div class="card-footer bg-white border-0 px-3 py-3">
                {{ $usuarios->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/usuarios/filtros.js') }}"></script>
@endpush
