@extends('layouts.admin')

@section('title', 'Roles')

@section('content')

    <div class="page-header">
        <div>
            <div class="page-eyebrow">
                Administración
            </div>

            <h1 class="page-title">
                Roles
            </h1>

            <p class="page-subtitle">
                Administra los roles y permisos del sistema.
            </p>
        </div>

        @can('roles.crear')
            <a href="{{ url('/roles/create') }}" class="btn btn-brand">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Nuevo rol
            </a>
        @endcan
    </div>


    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-4">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-purple text-brand">
                        <i class="bi bi-shield-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenRoles['total'] }}
                        </div>

                        <div class="mini-stat-label">
                            Roles registrados
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-6 col-sm-4">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-green text-green-bw">
                        <i class="bi bi-shield-check"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenRoles['activos'] }}
                        </div>

                        <div class="mini-stat-label">
                            Roles activos
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-6 col-sm-4">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-danger text-danger">
                        <i class="bi bi-shield-x"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenRoles['inactivos'] }}
                        </div>

                        <div class="mini-stat-label">
                            Roles inactivos
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    <div class="card bw-card">

        <div class="card-header">

            <form id="form-filtros-roles" method="GET" action="{{ route('roles.index') }}" class="row g-2">

                <div class="col-12 col-md-8">

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input id="buscar" type="search" name="buscar" class="form-control"
                            value="{{ request('buscar') }}" placeholder="Buscar por nombre o descripción">

                    </div>

                </div>


                <div class="col-8 col-md-3">


                    <select id="estado" name="estado" class="form-select">
                        <option value="">
                            Todos
                        </option>

                        <option value="1" @selected(request('estado') === '1')>
                            Activos
                        </option>

                        <option value="0" @selected(request('estado') === '0')>
                            Inactivos
                        </option>

                    </select>

                </div>


                <div class="col-4 col-md-1">

                    <a href="{{ route('roles.index') }}" class="btn btn-light border w-100" title="Limpiar filtros">

                        <i class="bi bi-arrow-counterclockwise"></i>

                    </a>

                </div>

            </form>

        </div>


        {{-- ESCRITORIO --}}

        <div class="table-responsive d-none d-lg-block">

            <table class="table align-middle mb-0">

                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Descripción</th>
                        <th>Usuarios</th>
                        <th>Permisos</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($roles as $rol)

                        <tr>

                            <td class="fw-bold">
                                {{ $rol->nombre }}
                            </td>

                            <td>
                                {{ $rol->descripcion ?: 'Sin descripción' }}
                            </td>

                            <td>
                                {{ $rol->usuarios_count }}
                            </td>

                            <td>
                                {{ $rol->permisos_count }}
                            </td>

                            <td>
                                <span class="badge-status {{ $rol->activo ? 'status-activo' : 'status-inactivo' }}">
                                    {{ $rol->activo ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </td>

                            <td class="text-end text-nowrap">

                                <div class="table-actions justify-content-end">

                                    @can('roles.editar')
                                        <a class="btn btn-sm btn-light border"
                                            href="{{ url('/roles/' . $rol->id_rol . '/edit') }}" title="Editar rol"
                                            aria-label="Editar rol">

                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan


                                    @can('roles.desactivar')
                                        @if ($rol->activo)
                                            <form method="POST" action="{{ url('/roles/' . $rol->id_rol) }}" class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-light border text-danger" type="submit"
                                                    title="Desactivar rol" aria-label="Desactivar rol"
                                                    data-confirm-delete="¿Deseas desactivar el rol {{ $rol->nombre }}?">

                                                    <i class="bi bi-shield-x"></i>

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

                                    <i class="bi bi-shield-x"></i>

                                    <div class="fw-bold mb-1">
                                        No se encontraron roles
                                    </div>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- MÓVIL --}}

        <div class="d-lg-none p-3">

            @forelse ($roles as $rol)

                <div class="card bw-card mb-3">

                    <div class="card-body">

                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                            <div class="d-flex align-items-center gap-3">

                                <span class="list-avatar flex-shrink-0">
                                    {{ strtoupper(mb_substr($rol->nombre, 0, 2)) }}
                                </span>

                                <div class="min-w-0">

                                    <div class="fw-bold text-break">
                                        {{ $rol->nombre }}
                                    </div>

                                    <div class="text-secondary small">
                                        ID {{ $rol->id_rol }}
                                    </div>

                                </div>

                            </div>

                            <span class="badge-status {{ $rol->activo ? 'status-activo' : 'status-inactivo' }}">
                                {{ $rol->activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>

                        </div>


                        <div class="mb-3">

                            <div class="text-secondary small">
                                Descripción
                            </div>

                            <div class="fw-semibold">
                                {{ $rol->descripcion ?: 'Sin descripción' }}
                            </div>

                        </div>


                        <div class="row g-3 small">

                            <div class="col-6">

                                <div class="text-secondary">
                                    Usuarios
                                </div>

                                <div class="fw-semibold">
                                    {{ $rol->usuarios_count }}
                                </div>

                            </div>


                            <div class="col-6">

                                <div class="text-secondary">
                                    Permisos
                                </div>

                                <div class="fw-semibold">
                                    {{ $rol->permisos_count }}
                                </div>

                            </div>

                        </div>


                        @canany(['roles.editar', 'roles.desactivar'])
                            <hr>

                            <div class="d-flex gap-2">

                                @can('roles.editar')
                                    <a href="{{ url('/roles/' . $rol->id_rol . '/edit') }}"
                                        class="btn btn-outline-brand flex-fill">

                                        <i class="bi bi-pencil me-1"></i>
                                        Editar

                                    </a>
                                @endcan


                                @can('roles.desactivar')
                                    @if ($rol->activo)
                                        <form method="POST" action="{{ url('/roles/' . $rol->id_rol) }}" class="flex-fill">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger w-100"
                                                data-confirm-delete="¿Deseas desactivar el rol {{ $rol->nombre }}?">

                                                <i class="bi bi-shield-x me-1"></i>
                                                Desactivar

                                            </button>

                                        </form>
                                    @endif
                                @endcan

                            </div>
                        @endcanany

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <i class="bi bi-shield-x"></i>

                    <div class="fw-bold mb-1">
                        No se encontraron roles
                    </div>

                </div>

            @endforelse

        </div>


        @if ($roles->hasPages())
            <div class="card-footer bg-white border-0 px-3 py-3">
                {{ $roles->links() }}
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/roles/filtros.js') }}"></script>
@endpush
