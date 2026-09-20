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

    <a href="{{ route('citas.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>
        Regresar a citas
    </a>
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

    <div class="table-responsive">

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

                                <a
                                    href="{{ route(
                                        'solicitudes-cita.procesar',
                                        $solicitud->id_solicitud
                                    ) }}"
                                    class="btn btn-sm btn-brand"
                                >
                                    Procesar
                                </a>

                            @elseif ($solicitud->id_cita)

                                <a
                                    href="{{ route('citas.show', $solicitud->id_cita) }}"
                                    class="btn btn-sm btn-outline-brand"
                                    title="Ver cita"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Ver cita
                                </a>

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

    @if ($solicitudes->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $solicitudes->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@endsection