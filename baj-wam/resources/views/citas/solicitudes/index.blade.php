@extends('layouts.admin')

@section('title', 'Solicitudes de cita')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Solicitudes de cita</h1>
        <p class="page-subtitle">
            Revisa las solicitudes enviadas desde el sitio público.
        </p>
    </div>

    @can('citas.ver')
        <a href="{{ route('citas.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-2"></i>
            Regresar a citas
        </a>
    @endcan
</div>

<div class="row g-3 mb-4">

    <div class="col-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body">
                <div class="mini-stat-value">
                    {{ $resumen['pendientes'] }}
                </div>
                <div class="mini-stat-label">
                    Pendientes
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body">
                <div class="mini-stat-value">
                    {{ $resumen['aprobadas'] }}
                </div>
                <div class="mini-stat-label">
                    Aprobadas
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body">
                <div class="mini-stat-value">
                    {{ $resumen['rechazadas'] }}
                </div>
                <div class="mini-stat-label">
                    Rechazadas
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card bw-card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('solicitudes-cita.index') }}"
            class="row g-2"
        >

            <div class="col-md-8">

                <input
                    type="search"
                    name="buscar"
                    class="form-control"
                    placeholder="Buscar por nombre o teléfono"
                    value="{{ request('buscar') }}"
                >

            </div>

            <div class="col-md-3">

                <select name="estado" class="form-select">

                    <option value="">
                        Todos los estados
                    </option>

                    <option
                        value="PENDIENTE"
                        @selected(request('estado') === 'PENDIENTE')
                    >
                        Pendientes
                    </option>

                    <option
                        value="APROBADA"
                        @selected(request('estado') === 'APROBADA')
                    >
                        Aprobadas
                    </option>

                    <option
                        value="RECHAZADA"
                        @selected(request('estado') === 'RECHAZADA')
                    >
                        Rechazadas
                    </option>

                </select>

            </div>

            <div class="col-md-1">

                <button class="btn btn-outline-brand w-100">
                    <i class="bi bi-search"></i>
                </button>

            </div>

        </form>

    </div>


    {{-- ESCRITORIO / TABLET --}}
    <div class="table-responsive d-none d-md-block">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>Servicio</th>
                    <th>Fecha solicitada</th>
                    <th>Solicitud</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($solicitudes as $solicitud)

                    @php
                        $claseEstado = match ($solicitud->estado) {
                            'APROBADA' => 'confirmada',
                            'RECHAZADA' => 'cancelada',
                            default => 'pendiente',
                        };
                    @endphp

                    <tr>

                        <td>
                            <strong>
                                {{ $solicitud->nombre }}
                            </strong>

                            <div class="text-muted small">
                                {{ $solicitud->telefono }}
                            </div>
                        </td>

                        <td>
                            <span class="service-chip">
                                <i class="bi bi-flower1"></i>
                                {{ $solicitud->servicio?->nombre ?? 'Sin servicio' }}
                            </span>
                        </td>

                        <td>
                            <strong>
                                {{ $solicitud->fecha->format('d/m/Y') }}
                            </strong>

                            <div class="text-muted small">
                                {{ substr($solicitud->hora, 0, 5) }}
                            </div>
                        </td>

                        <td>
                            {{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <span class="badge-status status-{{ $claseEstado }}">
                                {{ $solicitud->estado }}
                            </span>
                        </td>

                        <td class="text-end">

                            @if ($solicitud->estado === 'PENDIENTE')

                                @can('solicitudes.procesar')
                                    <a
                                        href="{{ route(
                                            'solicitudes-cita.procesar',
                                            $solicitud->id_solicitud
                                        ) }}"
                                        class="btn btn-sm btn-brand"
                                    >
                                        Procesar
                                    </a>
                                @endcan

                            @elseif ($solicitud->id_cita)

                                @can('citas.ver')
                                    <a
                                        href="{{ route(
                                            'citas.show',
                                            $solicitud->id_cita
                                        ) }}"
                                        class="btn btn-sm btn-outline-brand"
                                        title="Ver cita"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        Ver cita
                                    </a>
                                @endcan

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                No hay solicitudes registradas.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- MÓVIL --}}
    <div class="d-md-none p-3">

        @forelse ($solicitudes as $solicitud)

            @php
                $claseEstado = match ($solicitud->estado) {
                    'APROBADA' => 'confirmada',
                    'RECHAZADA' => 'cancelada',
                    default => 'pendiente',
                };
            @endphp

            <div class="card bw-card mb-3">

                <div class="card-body">

                    {{-- SOLICITANTE Y ESTADO --}}
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                        <div class="min-w-0">

                            <div class="fw-bold">
                                {{ $solicitud->nombre }}
                            </div>

                            <div class="text-muted small">
                                <i class="bi bi-telephone me-1"></i>
                                {{ $solicitud->telefono }}
                            </div>

                        </div>

                        <span
                            class="badge-status status-{{ $claseEstado }} flex-shrink-0 text-nowrap"
                        >
                            {{ $solicitud->estado }}
                        </span>

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="row g-3 small">

                        {{-- SERVICIO --}}
                        <div class="col-12">

                            <div class="text-muted mb-1">
                                Servicio
                            </div>

                            <span class="service-chip">
                                <i class="bi bi-flower1"></i>
                                {{ $solicitud->servicio?->nombre ?? 'Sin servicio' }}
                            </span>

                        </div>


                        {{-- FECHA SOLICITADA --}}
                        <div class="col-6">

                            <div class="text-muted">
                                Fecha solicitada
                            </div>

                            <div class="fw-semibold text-nowrap">
                                {{ $solicitud->fecha->format('d/m/Y') }}
                            </div>

                            <div class="text-muted">
                                {{ substr($solicitud->hora, 0, 5) }}
                            </div>

                        </div>


                        {{-- FECHA DE SOLICITUD --}}
                        <div class="col-6">

                            <div class="text-muted">
                                Solicitud
                            </div>

                            <div class="fw-semibold">
                                {{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') }}
                            </div>

                        </div>

                    </div>


                    {{-- ACCIONES --}}
                    @if (
                        $solicitud->estado === 'PENDIENTE'
                        || $solicitud->id_cita
                    )

                        @canany(['solicitudes.procesar', 'citas.ver'])

                            <hr>

                            <div class="d-flex">

                                @if ($solicitud->estado === 'PENDIENTE')

                                    @can('solicitudes.procesar')
                                        <a
                                            href="{{ route(
                                                'solicitudes-cita.procesar',
                                                $solicitud->id_solicitud
                                            ) }}"
                                            class="btn btn-brand w-100"
                                        >
                                            Procesar
                                        </a>
                                    @endcan

                                @elseif ($solicitud->id_cita)

                                    @can('citas.ver')
                                        <a
                                            href="{{ route(
                                                'citas.show',
                                                $solicitud->id_cita
                                            ) }}"
                                            class="btn btn-outline-brand w-100"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            Ver cita
                                        </a>
                                    @endcan

                                @endif

                            </div>

                        @endcanany

                    @endif

                </div>

            </div>

        @empty

            <div class="empty-state">
                No hay solicitudes registradas.
            </div>

        @endforelse

    </div>


    @if ($solicitudes->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $solicitudes->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@endsection