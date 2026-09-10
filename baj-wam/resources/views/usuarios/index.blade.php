@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content')
@php
    $usuarios = $usuarios ?? collect([
        [
            'id_usuario' => 1,
            'nombres' => 'Cristian Alfredo',
            'apellidos' => 'Pérez Paz',
            'usuario' => 'cperez',
            'correo' => 'cristian@bajwam.com',
            'telefono' => '5555-0101',
            'activo' => true,
            'fecha_creacion' => '2026-08-20 09:30:00',
            'rol' => ['nombre' => 'ADMINISTRADOR'],
        ],
        [
            'id_usuario' => 2,
            'nombres' => 'Ana Lucía',
            'apellidos' => 'Ruiz Morales',
            'usuario' => 'aruiz',
            'correo' => 'ana@bajwam.com',
            'telefono' => '5555-0188',
            'activo' => true,
            'fecha_creacion' => '2026-08-22 11:10:00',
            'rol' => ['nombre' => 'ESPECIALISTA'],
        ],
        [
            'id_usuario' => 3,
            'nombres' => 'Laura',
            'apellidos' => 'Gómez Castillo',
            'usuario' => 'lgomez',
            'correo' => 'laura@bajwam.com',
            'telefono' => '5555-0144',
            'activo' => true,
            'fecha_creacion' => '2026-08-24 08:45:00',
            'rol' => ['nombre' => 'INVENTARIO'],
        ],
        [
            'id_usuario' => 4,
            'nombres' => 'Sofía',
            'apellidos' => 'López García',
            'usuario' => 'slopez',
            'correo' => 'sofia@bajwam.com',
            'telefono' => '5555-0172',
            'activo' => false,
            'fecha_creacion' => '2026-08-25 14:20:00',
            'rol' => ['nombre' => 'RECEPCION'],
        ],
    ]);

    $roles = $roles ?? collect([
        ['id_rol' => 1, 'nombre' => 'ADMINISTRADOR'],
        ['id_rol' => 2, 'nombre' => 'RECEPCION'],
        ['id_rol' => 3, 'nombre' => 'ESPECIALISTA'],
        ['id_rol' => 4, 'nombre' => 'INVENTARIO'],
    ]);

    $resumenUsuarios = $resumenUsuarios ?? [
        'total' => 4,
        'activos' => 3,
        'inactivos' => 1,
    ];

    $valor = fn ($item, $campo, $default = null) => data_get($item, $campo, $default);
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

    <a href="{{ url('/usuarios/create') }}" class="btn btn-brand">
        <i class="bi bi-person-plus-fill me-2"></i>Nuevo usuario
    </a>
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
        <form class="row g-2 align-items-end" method="GET" action="{{ url('/usuarios') }}">
            <div class="col-12 col-lg-5">
                <label class="form-label visually-hidden" for="buscar">Buscar usuario</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input
                        class="form-control"
                        id="buscar"
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por nombre, usuario o correo"
                    >
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
                <button class="btn btn-outline-brand flex-grow-1" type="submit">Filtrar</button>
                <a class="btn btn-light border" href="{{ url('/usuarios') }}" title="Limpiar filtros" aria-label="Limpiar filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Contacto</th>
                    <th>Rol</th>
                    <th>Estado</th>
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
                        $nombreCompleto = trim($nombres.' '.$apellidos);
                        $iniciales = strtoupper(mb_substr($nombres, 0, 1).mb_substr($apellidos, 0, 1));
                        $activo = (bool) $valor($usuario, 'activo', true);
                        $rolNombre = $valor($usuario, 'rol.nombre', $valor($usuario, 'nombre_rol', 'SIN ROL'));
                    @endphp
                    <tr>
                        <td>
                            <div class="record-person">
                                <span class="list-avatar">{{ $iniciales ?: 'U' }}</span>
                                <div class="min-w-0">
                                    <a class="record-name" href="{{ url('/usuarios/'.$id) }}">{{ $nombreCompleto ?: 'Sin nombre' }}</a>
                                    <span class="record-subtitle">{{ '@'.$valor($usuario, 'usuario', 'sin_usuario') }} · ID {{ $id }}</span>
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
                        <td>
                            <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                                {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </td>
                        <td>{{ $formatearFecha($valor($usuario, 'fecha_creacion')) }}</td>
                        <td class="text-end text-nowrap">
                            <div class="table-actions justify-content-end">
                                <a class="btn btn-sm btn-light border" href="{{ url('/usuarios/'.$id) }}" title="Ver usuario" aria-label="Ver usuario">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-light border" href="{{ url('/usuarios/'.$id.'/edit') }}" title="Editar usuario" aria-label="Editar usuario">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($activo)
                                    <form method="POST" action="{{ url('/usuarios/'.$id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="btn btn-sm btn-light border text-danger"
                                            type="submit"
                                            title="Desactivar usuario"
                                            aria-label="Desactivar usuario"
                                            data-confirm-delete="¿Deseas desactivar a {{ $nombreCompleto }}? La cuenta dejará de tener acceso al sistema."
                                        >
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </form>
                                @endif
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

    @if (is_object($usuarios) && method_exists($usuarios, 'links'))
        <div class="card-footer bg-white border-0 px-3 py-3">
            {{ $usuarios->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
