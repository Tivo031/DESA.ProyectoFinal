@extends('layouts.admin')

@section('title', 'Pacientes')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Pacientes</h1>
        <p class="page-subtitle">
            Consulta y administra la información de los pacientes.
        </p>
    </div>

    @can('pacientes.crear')
        <a href="{{ route('pacientes.create') }}" class="btn btn-brand">
            <i class="bi bi-person-plus-fill me-2"></i>
            Nuevo paciente
        </a>
    @endcan
</div>

<div class="row g-3 mb-4">

    <div class="col-12 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand">
                    <i class="bi bi-people-fill"></i>
                </span>

                <div>
                    <div class="mini-stat-value">
                        {{ $resumenPacientes['total'] }}
                    </div>

                    <div class="mini-stat-label">
                        Total de pacientes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw">
                    <i class="bi bi-person-check-fill"></i>
                </span>

                <div>
                    <div class="mini-stat-value">
                        {{ $resumenPacientes['activos'] }}
                    </div>

                    <div class="mini-stat-label">
                        Activos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-danger text-danger">
                    <i class="bi bi-person-x-fill"></i>
                </span>

                <div>
                    <div class="mini-stat-value">
                        {{ $resumenPacientes['inactivos'] }}
                    </div>

                    <div class="mini-stat-label">
                        Inactivos
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card bw-card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('pacientes.index') }}"
            class="row g-2 align-items-end"
        >

            <div class="col-12 col-lg-7">

                <label class="form-label">
                    Buscar paciente
                </label>

                <input
                    type="search"
                    name="buscar"
                    class="form-control"
                    placeholder="Nombre, DPI, teléfono o correo"
                    value="{{ request('buscar') }}"
                >

            </div>

            <div class="col-12 col-md-6 col-lg-3">

                <label class="form-label">
                    Estado
                </label>

                <select name="estado" class="form-select">

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="activo"
                        @selected(request('estado') === 'activo')
                    >
                        Activos
                    </option>

                    <option
                        value="inactivo"
                        @selected(request('estado') === 'inactivo')
                    >
                        Inactivos
                    </option>

                </select>

            </div>

            <div class="col-12 col-md-6 col-lg-2">

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-outline-brand flex-grow-1"
                    >
                        Filtrar
                    </button>

                    <a
                        href="{{ route('pacientes.index') }}"
                        class="btn btn-light border"
                        title="Limpiar filtros"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- ESCRITORIO / TABLET --}}
    <div class="table-responsive d-none d-md-block">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>DPI</th>
                    <th>Contacto</th>
                    <th>Sexo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($pacientes as $paciente)

                    @php
                        $iniciales = strtoupper(
                            mb_substr($paciente->nombres, 0, 1) .
                            mb_substr($paciente->apellidos, 0, 1)
                        );
                    @endphp

                    <tr>

                        <td>
                            <div class="record-person compact-person">

                                <span class="list-avatar patient-avatar">
                                    {{ $iniciales ?: 'P' }}
                                </span>

                                <div class="min-w-0">

                                    @can('pacientes.ver')
                                        <a
                                            href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                            class="record-name"
                                        >
                                            {{ $paciente->nombres }}
                                            {{ $paciente->apellidos }}
                                        </a>
                                    @else
                                        <span class="record-name">
                                            {{ $paciente->nombres }}
                                            {{ $paciente->apellidos }}
                                        </span>
                                    @endcan

                                    <span class="record-subtitle">
                                        {{ $paciente->correo ?: 'Sin correo' }}
                                    </span>

                                </div>

                            </div>
                        </td>

                        <td>
                            {{ $paciente->dpi ?: 'Sin DPI' }}
                        </td>

                        <td>
                            <i class="bi bi-telephone me-1 text-muted"></i>
                            {{ $paciente->telefono }}
                        </td>

                        <td>
                            {{ $paciente->sexo ?: 'No especificado' }}
                        </td>

                        <td>

                            @if ($paciente->activo)

                                <span class="badge-status status-confirmada">
                                    ACTIVO
                                </span>

                            @else

                                <span class="badge-status status-cancelada">
                                    INACTIVO
                                </span>

                            @endif

                        </td>

                        <td class="text-end text-nowrap">

                            <div class="table-actions justify-content-end">

                                @can('pacientes.ver')
                                    <a
                                        href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                        class="btn btn-sm btn-light border"
                                        title="Ver paciente"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endcan

                                @can('pacientes.editar')
                                    <a
                                        href="{{ route('pacientes.edit', $paciente->id_paciente) }}"
                                        class="btn btn-sm btn-light border"
                                        title="Editar paciente"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if ($paciente->activo)

                                        <form
                                            method="POST"
                                            action="{{ route('pacientes.destroy', $paciente->id_paciente) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-light border text-danger"
                                                title="Desactivar paciente"
                                                data-confirm-delete="El paciente quedará inactivo. ¿Deseas continuar?"
                                            >
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
                                <i class="bi bi-people"></i>
                                No hay pacientes que coincidan con los filtros.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- MÓVIL --}}
    <div class="d-md-none p-3">

        @forelse ($pacientes as $paciente)

            @php
                $iniciales = strtoupper(
                    mb_substr($paciente->nombres, 0, 1) .
                    mb_substr($paciente->apellidos, 0, 1)
                );
            @endphp

            <div class="card bw-card mb-3">

                <div class="card-body">

                    {{-- PACIENTE Y ESTADO --}}
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                        <div class="record-person compact-person min-w-0">

                            <span class="list-avatar patient-avatar flex-shrink-0">
                                {{ $iniciales ?: 'P' }}
                            </span>

                            <div class="min-w-0">

                                @can('pacientes.ver')
                                    <a
                                        href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                        class="record-name"
                                    >
                                        {{ $paciente->nombres }}
                                        {{ $paciente->apellidos }}
                                    </a>
                                @else
                                    <span class="record-name">
                                        {{ $paciente->nombres }}
                                        {{ $paciente->apellidos }}
                                    </span>
                                @endcan

                                <span class="record-subtitle">
                                    {{ $paciente->correo ?: 'Sin correo' }}
                                </span>

                            </div>

                        </div>

                        @if ($paciente->activo)

                            <span class="badge-status status-confirmada flex-shrink-0 text-nowrap">
                                ACTIVO
                            </span>

                        @else

                            <span class="badge-status status-cancelada flex-shrink-0 text-nowrap">
                                INACTIVO
                            </span>

                        @endif

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="row g-3 small">

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                DPI
                            </div>

                            <div class="fw-semibold">
                                {{ $paciente->dpi ?: 'Sin DPI' }}
                            </div>

                        </div>

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Sexo
                            </div>

                            <div class="fw-semibold">
                                {{ $paciente->sexo ?: 'No especificado' }}
                            </div>

                        </div>

                        <div class="col-12">

                            <div class="text-muted mb-1">
                                Contacto
                            </div>

                            <div class="fw-semibold">
                                <i class="bi bi-telephone me-1 text-muted"></i>
                                {{ $paciente->telefono }}
                            </div>

                        </div>

                    </div>


                    {{-- ACCIONES --}}
                    @canany(['pacientes.ver', 'pacientes.editar'])

                        <hr>

                        <div class="d-flex gap-2">

                            @can('pacientes.ver')
                                <a
                                    href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                    class="btn btn-light border flex-grow-1"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Ver
                                </a>
                            @endcan

                            @can('pacientes.editar')
                                <a
                                    href="{{ route('pacientes.edit', $paciente->id_paciente) }}"
                                    class="btn btn-light border flex-grow-1"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Editar
                                </a>

                                @if ($paciente->activo)

                                    <form
                                        method="POST"
                                        action="{{ route('pacientes.destroy', $paciente->id_paciente) }}"
                                        class="flex-grow-1"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-light border text-danger w-100"
                                            title="Desactivar paciente"
                                            data-confirm-delete="El paciente quedará inactivo. ¿Deseas continuar?"
                                        >
                                            <i class="bi bi-person-x me-1"></i>
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
                <i class="bi bi-people"></i>
                No hay pacientes que coincidan con los filtros.
            </div>

        @endforelse

    </div>


    @if ($pacientes->hasPages())
        <div class="card-footer bg-white border-0 pt-0">
            {{ $pacientes->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@endsection