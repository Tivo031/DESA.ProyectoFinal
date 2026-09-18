@extends('layouts.admin')

@section('title', 'Permisos')

@section('content')

    <div class="page-header">

        <div>
            <div class="page-eyebrow">
                Administración
            </div>

            <h1 class="page-title">
                Permisos
            </h1>

            <p class="page-subtitle">
                Administra los permisos disponibles en el sistema.
            </p>
        </div>

        @can('permisos.crear')
            <a href="{{ url('/permisos/create') }}" class="btn btn-brand">

                <i class="bi bi-plus-circle-fill me-2"></i>
                Nuevo permiso
            </a>
        @endcan

    </div>


    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-4">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-purple text-brand">
                        <i class="bi bi-key-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenPermisos['total'] }}
                        </div>

                        <div class="mini-stat-label">
                            Permisos registrados
                        </div>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-6 col-sm-4">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-green text-green-bw">
                        <i class="bi bi-check-circle-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenPermisos['activos'] }}
                        </div>

                        <div class="mini-stat-label">
                            Permisos activos
                        </div>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-6 col-sm-4">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-danger text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenPermisos['inactivos'] }}
                        </div>

                        <div class="mini-stat-label">
                            Permisos inactivos
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="card bw-card">

        <div class="card-header">

            <form id="form-filtros-permisos" method="GET" action="{{ url('/permisos') }}" class="row g-2 align-items-end"
                autocomplete="off">

                <div class="col-12 col-lg-5">

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input class="form-control" id="buscar" type="search" name="buscar"
                            value="{{ request('buscar') }}" placeholder="Buscar por código, nombre o descripción"
                            autocomplete="new-password">

                    </div>

                </div>


                <div class="col-6 col-md-4 col-lg-3">

                    <select class="form-select" id="modulo" name="modulo">

                        <option value="">
                            Todos los módulos
                        </option>

                        @foreach ($modulos as $modulo)
                            <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>

                                {{ $modulo }}

                            </option>
                        @endforeach

                    </select>

                </div>


                <div class="col-6 col-md-4 col-lg-2">

                    <select class="form-select" id="estado" name="estado">

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


                <div class="col-12 col-md-4 col-lg-2">

                    <a class="btn btn-light border w-100" href="{{ url('/permisos') }}">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Limpiar

                    </a>

                </div>

            </form>

        </div>


        {{-- ESCRITORIO --}}

        <div class="table-responsive d-none d-lg-block">

            <table class="table align-middle mb-0">

                <thead>
                    <tr>
                        <th>Permiso</th>
                        <th>Código</th>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th class="text-nowrap" style="min-width: 110px;">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($permisos as $permiso)

                        <tr>

                            <td class="fw-bold">
                                {{ $permiso->nombre }}
                            </td>

                            <td>
                                <code>
                                    {{ $permiso->codigo }}
                                </code>
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ $permiso->modulo }}
                                </span>
                            </td>

                            <td>
                                {{ $permiso->descripcion ?: 'Sin descripción' }}
                            </td>

                            <td class="text-nowrap" style="min-width: 110px;">
                                <span class="badge-status {{ $permiso->activo ? 'status-activo' : 'status-inactivo' }}">
                                    {{ $permiso->activo ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </td>

                            <td class="text-end text-nowrap">

                                <div class="table-actions justify-content-end">

                                    @can('permisos.editar')
                                        <a class="btn btn-sm btn-light border"
                                            href="{{ url('/permisos/' . $permiso->id_permiso . '/edit') }}"
                                            title="Editar permiso" aria-label="Editar permiso">

                                            <i class="bi bi-pencil"></i>

                                        </a>
                                    @endcan


                                    @can('permisos.desactivar')
                                        @if ($permiso->activo)
                                            <form method="POST" action="{{ url('/permisos/' . $permiso->id_permiso) }}"
                                                class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-light border text-danger" type="submit"
                                                    title="Desactivar permiso" aria-label="Desactivar permiso"
                                                    data-confirm-delete="¿Deseas desactivar el permiso {{ $permiso->nombre }}?">
                                                    <i class="bi bi-x-circle"></i>
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

                                    <i class="bi bi-key"></i>

                                    <div class="fw-bold mb-1">
                                        No se encontraron permisos
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

            @forelse ($permisos as $permiso)

                <div class="card bw-card mb-3">

                    <div class="card-body">

                        <div class="d-flex justify-content-between gap-3 mb-3">

                            <div class="min-w-0">

                                <div class="fw-bold text-break">
                                    {{ $permiso->nombre }}
                                </div>

                                <div class="text-secondary small text-break">
                                    {{ $permiso->codigo }}
                                </div>

                            </div>

                            <span class="badge-status {{ $permiso->activo ? 'status-activo' : 'status-inactivo' }}">
                                {{ $permiso->activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>

                        </div>


                        <div class="row g-3 small">

                            <div class="col-12">

                                <div class="text-secondary">
                                    Módulo
                                </div>

                                <span class="role-badge">
                                    {{ $permiso->modulo }}
                                </span>

                            </div>


                            <div class="col-12">

                                <div class="text-secondary">
                                    Descripción
                                </div>

                                <div class="fw-semibold">
                                    {{ $permiso->descripcion ?: 'Sin descripción' }}
                                </div>

                            </div>

                        </div>


                        @canany(['permisos.editar', 'permisos.desactivar'])
                            <hr>

                            <div class="d-flex gap-2">

                                @can('permisos.editar')
                                    <a href="{{ url('/permisos/' . $permiso->id_permiso . '/edit') }}"
                                        class="btn btn-outline-brand flex-fill">

                                        <i class="bi bi-pencil me-1"></i>
                                        Editar

                                    </a>
                                @endcan


                                @can('permisos.desactivar')
                                    @if ($permiso->activo)
                                        <form method="POST" action="{{ url('/permisos/' . $permiso->id_permiso) }}"
                                            class="flex-fill">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger w-100"
                                                data-confirm-delete="¿Deseas desactivar el permiso {{ $permiso->nombre }}?">

                                                <i class="bi bi-x-circle me-1"></i>
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
                        No se encontraron permisos
                    </div>

                </div>

            @endforelse

        </div>


        @if ($permisos->hasPages())
            <div class="card-footer bg-white border-0 px-3 py-3">
                {{ $permisos->links() }}
            </div>
        @endif
            
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/permisos/filtros.js') }}"></script>
@endpush
