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

    <a href="{{ route('pacientes.create') }}" class="btn btn-brand">
        <i class="bi bi-person-plus-fill me-2"></i>
        Nuevo paciente
    </a>
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

    <div class="table-responsive">

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

                                    <a
                                        href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                        class="record-name"
                                    >
                                        {{ $paciente->nombres }}
                                        {{ $paciente->apellidos }}
                                    </a>

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

                                <a
                                    href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                    class="btn btn-sm btn-light border"
                                    title="Ver paciente"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

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

    @if ($pacientes->hasPages())
        <div class="card-footer bg-white border-0 pt-0">
            {{ $pacientes->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@endsection