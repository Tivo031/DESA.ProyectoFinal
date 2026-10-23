
@extends('layouts.admin')

@section('title', 'Especialistas')

@section('content')
    @php
        $puedeVer = auth()->user()->can('especialistas.ver');
        $puedeEditar = auth()->user()->can('especialistas.editar');
        $puedeCambiarEstado = auth()->user()->can('especialistas.desactivar');
        $mostrarAcciones = $puedeVer || $puedeEditar || $puedeCambiarEstado;
    @endphp

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Atención clínica</div>
            <h1 class="page-title">Especialistas</h1>
            <p class="page-subtitle">
                Profesionales registrados con una cuenta de usuario asociada.
            </p>
        </div>

        @can('especialistas.crear')
            <a href="{{ route('especialistas.create') }}" class="btn btn-brand">
                <i class="bi bi-person-plus-fill me-2"></i>Nuevo especialista
            </a>
        @endcan
    </div>

    {{-- FILTROS --}}
    <div class="card bw-card mb-4">
        <div class="card-header">
            <form class="row g-2 align-items-end"
                method="GET"
                action="{{ route('especialistas.index') }}">

                <div class="col-12 col-lg-7">
                    <label class="form-label visually-hidden" for="buscar">
                        Buscar especialista
                    </label>

                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            class="form-control"
                            type="search"
                            id="buscar"
                            name="buscar"
                            value="{{ request('buscar') }}"
                            placeholder="Buscar por nombre, usuario o profesión">
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <label class="form-label visually-hidden" for="estado">
                        Estado
                    </label>

                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" @selected(request('estado') === 'activo')>
                            Activos
                        </option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>
                            Inactivos
                        </option>
                    </select>
                </div>

                <div class="col-6 col-lg-2 d-flex gap-2">
                    <button type="submit"
                        class="btn btn-outline-brand flex-fill"
                        title="Buscar especialistas">
                        <i class="bi bi-search"></i>
                    </button>

                    <a href="{{ route('especialistas.index') }}"
                        class="btn btn-light border flex-fill"
                        title="Limpiar filtros"
                        aria-label="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card bw-card">

        {{-- ESCRITORIO / TABLET --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Especialista</th>
                        <th>Contacto</th>
                        <th>Profesión</th>
                        <th class="text-nowrap">Estado</th>

                        @if ($mostrarAcciones)
                            <th class="text-end">Acciones</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse ($especialistas as $especialista)
                        @php
                            $usuario = $especialista->usuario;

                            $nombres = $usuario?->nombres ?? '';
                            $apellidos = $usuario?->apellidos ?? '';
                            $nombreCompleto = trim($nombres . ' ' . $apellidos);

                            $iniciales = mb_strtoupper(
                                mb_substr($nombres, 0, 1) .
                                mb_substr($apellidos, 0, 1)
                            );

                            $activo = (bool) $especialista->activo
                                && (bool) $usuario?->activo;

                            $esCuentaPropia = (int) auth()->id()
                                === (int) $especialista->id_usuario;
                        @endphp

                        <tr>
                            {{-- ESPECIALISTA --}}
                            <td>
                                <div class="record-person">
                                    <span class="list-avatar">
                                        {{ $iniciales ?: 'E' }}
                                    </span>

                                    <div class="min-w-0">
                                        @can('especialistas.ver')
                                            <a class="record-name"
                                                href="{{ route('especialistas.show', $especialista) }}">
                                                {{ $nombreCompleto ?: 'Sin nombre' }}
                                            </a>
                                        @else
                                            <span class="record-name">
                                                {{ $nombreCompleto ?: 'Sin nombre' }}
                                            </span>
                                        @endcan

                                        <span class="record-subtitle">
                                            {{ '@' . ($usuario?->usuario ?? 'sin_usuario') }}
                                            · ID {{ $especialista->id_especialista }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- CONTACTO --}}
                            <td>
                                <div class="fw-semibold small">
                                    {{ $usuario?->correo ?: 'Sin correo' }}
                                </div>

                                <div class="text-secondary small">
                                    {{ $usuario?->telefono ?: 'Sin teléfono' }}
                                </div>
                            </td>

                            {{-- PROFESIÓN --}}
                            <td>
                                {{ $especialista->profesion }}
                            </td>

                            {{-- ESTADO --}}
                            <td class="text-nowrap">
                                <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                                    {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            @if ($mostrarAcciones)
                                <td class="text-end text-nowrap">
                                    <div class="table-actions justify-content-end">

                                        @can('especialistas.ver')
                                            <a class="btn btn-sm btn-light border"
                                                href="{{ route('especialistas.show', $especialista) }}"
                                                title="Ver especialista"
                                                aria-label="Ver especialista">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan

                                        @can('especialistas.editar')
                                            <a class="btn btn-sm btn-light border"
                                                href="{{ route('especialistas.edit', $especialista->id_usuario) }}"
                                                title="Editar especialista"
                                                aria-label="Editar especialista">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan

                                        @can('especialistas.desactivar')
                                            @if (!$esCuentaPropia)
                                                <form method="POST"
                                                    action="{{ route('especialistas.estado', $especialista) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden"
                                                        name="activo"
                                                        value="{{ $especialista->activo ? 0 : 1 }}">

                                                    <button
                                                        class="btn btn-sm btn-light border {{ $especialista->activo ? 'text-danger' : 'text-success' }}"
                                                        type="submit"
                                                        title="{{ $especialista->activo ? 'Desactivar especialista' : 'Activar especialista' }}"
                                                        aria-label="{{ $especialista->activo ? 'Desactivar especialista' : 'Activar especialista' }}"
                                                        data-confirm-delete="¿Deseas {{ $especialista->activo ? 'desactivar' : 'activar' }} a {{ $nombreCompleto }}?">
                                                        <i class="bi {{ $especialista->activo ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $mostrarAcciones ? 5 : 4 }}">
                                <div class="empty-state">
                                    <i class="bi bi-person-x"></i>
                                    <div class="fw-bold mb-1">
                                        No se encontraron especialistas
                                    </div>
                                    <div class="small">
                                        Prueba con otros filtros o registra un nuevo especialista.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MÓVIL --}}
        <div class="d-md-none p-3">
            @forelse ($especialistas as $especialista)
                @php
                    $usuario = $especialista->usuario;

                    $nombres = $usuario?->nombres ?? '';
                    $apellidos = $usuario?->apellidos ?? '';
                    $nombreCompleto = trim($nombres . ' ' . $apellidos);

                    $iniciales = mb_strtoupper(
                        mb_substr($nombres, 0, 1) .
                        mb_substr($apellidos, 0, 1)
                    );

                    $activo = (bool) $especialista->activo
                        && (bool) $usuario?->activo;

                    $esCuentaPropia = (int) auth()->id()
                        === (int) $especialista->id_usuario;
                @endphp

                <div class="card bw-card mb-3">
                    <div class="card-body">

                        <div class="d-flex align-items-start gap-3 mb-3">
                            <span class="list-avatar flex-shrink-0">
                                {{ $iniciales ?: 'E' }}
                            </span>

                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-break">
                                    {{ $nombreCompleto ?: 'Sin nombre' }}
                                </div>

                                <div class="text-secondary small">
                                    {{ '@' . ($usuario?->usuario ?? 'sin_usuario') }}
                                    · ID {{ $especialista->id_especialista }}
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
                                    {{ $usuario?->correo ?: 'Sin correo' }}
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="text-secondary">Teléfono</div>
                                <div class="fw-semibold">
                                    {{ $usuario?->telefono ?: 'Sin teléfono' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-secondary">Profesión</div>
                                <div class="fw-semibold">
                                    {{ $especialista->profesion }}
                                </div>
                            </div>
                        </div>

                        @if ($mostrarAcciones)
                            <hr>

                            <div class="d-flex gap-2 flex-wrap">

                                @can('especialistas.ver')
                                    <a href="{{ route('especialistas.show', $especialista) }}"
                                        class="btn btn-light border flex-fill">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                @endcan

                                @can('especialistas.editar')
                                    <a href="{{ route('especialistas.edit', $especialista->id_usuario) }}"
                                        class="btn btn-outline-brand flex-fill">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </a>
                                @endcan

                                @can('especialistas.desactivar')
                                    @if (!$esCuentaPropia)
                                        <form method="POST"
                                            action="{{ route('especialistas.estado', $especialista) }}"
                                            class="flex-fill">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden"
                                                name="activo"
                                                value="{{ $especialista->activo ? 0 : 1 }}">

                                            <button type="submit"
                                                class="btn {{ $especialista->activo ? 'btn-outline-danger' : 'btn-outline-success' }} w-100"
                                                data-confirm-delete="¿Deseas {{ $especialista->activo ? 'desactivar' : 'activar' }} a {{ $nombreCompleto }}?">
                                                <i class="bi {{ $especialista->activo ? 'bi-person-x' : 'bi-person-check' }} me-1"></i>
                                                {{ $especialista->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-person-x"></i>
                    <div class="fw-bold mb-1">
                        No se encontraron especialistas
                    </div>
                    <div class="small">
                        Prueba con otros filtros o registra un nuevo especialista.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- PAGINACIÓN --}}
        @if ($especialistas->hasPages())
            <div class="card-footer bg-white border-0 px-3 py-3">
                {{ $especialistas->links() }}
            </div>
        @endif

    </div>
@endsection
