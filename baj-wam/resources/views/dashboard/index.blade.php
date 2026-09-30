@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Resumen de la actividad principal de la clínica.</p>
        </div>

        @can('citas.crear')
            <a href="{{ url('/citas/create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-2"></i>Nueva cita
            </a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bw-card stat-card stat-purple h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="bi bi-calendar2-check"></i></div>
                    <div class="stat-value">{{ $metricas['citas_hoy'] }}</div>
                    <div class="stat-label">Citas programadas hoy</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bw-card stat-card stat-plum h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="bi bi-inbox"></i></div>
                    <div class="stat-value">{{ $metricas['solicitudes_pendientes'] }}</div>
                    <div class="stat-label">Solicitudes pendientes</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bw-card stat-card stat-green h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-value">{{ $metricas['pacientes_activos'] }}</div>
                    <div class="stat-label">Pacientes activos</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bw-card stat-card stat-lime h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="stat-value">{{ $metricas['productos_bajos'] }}</div>
                    <div class="stat-label">Productos con existencia baja</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-12 col-xl-8">

            <div class="card bw-card h-100">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="h6 fw-bold mb-1">Agenda de hoy</h2>
                        <p class="small text-secondary mb-0">
                            Próximas atenciones programadas.
                        </p>
                    </div>

                    @can('citas.ver')
                        <a href="{{ url('/citas') }}" class="btn btn-sm btn-brand">
                            Ver agenda
                        </a>
                    @endcan
                </div>

                {{-- ESCRITORIO / TABLET --}}
                <div class="table-responsive d-none d-md-block">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Servicio</th>
                                <th>Especialista</th>
                                <th class="text-nowrap" style="width: 130px;">Estado</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($citasHoy as $cita)
                                <tr>

                                    <td class="fw-bold">
                                        {{ $cita['hora'] }}
                                    </td>

                                    <td>
                                        {{ $cita['paciente'] }}
                                    </td>

                                    <td>
                                        {{ $cita['servicio'] }}
                                    </td>

                                    <td>
                                        {{ $cita['especialista'] }}
                                    </td>

                                    <td>
                                        <span
                                            class="badge-status status-{{ strtolower(str_replace(' ', '-', $cita['estado'])) }}">
                                            {{ $cita['estado'] }}
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-calendar2-x"></i>
                                            No hay citas para hoy.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- MÓVIL --}}
                <div class="d-md-none p-3">

                    @forelse ($citasHoy as $cita)
                        <div class="card bw-card mb-3">

                            <div class="card-body">

                                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                                    <div>
                                        <div class="fw-bold">
                                            {{ $cita['paciente'] }}
                                        </div>

                                        <div class="text-secondary small">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $cita['hora'] }}
                                        </div>
                                    </div>

                                    <span
                                        class="badge-status status-{{ strtolower(str_replace(' ', '-', $cita['estado'])) }} flex-shrink-0 text-nowrap">
                                        {{ $cita['estado'] }}
                                    </span>

                                </div>

                                <div class="row g-3 small">

                                    <div class="col-12">

                                        <div class="text-secondary mb-1">
                                            Servicio
                                        </div>

                                        <div class="fw-semibold">
                                            {{ $cita['servicio'] }}
                                        </div>

                                    </div>

                                    <div class="col-12">

                                        <div class="text-secondary mb-1">
                                            Especialista
                                        </div>

                                        <div class="fw-semibold">
                                            {{ $cita['especialista'] }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">
                            <i class="bi bi-calendar2-x"></i>
                            No hay citas para hoy.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="card bw-card h-100">

                <div class="card-header">
                    <h2 class="h6 fw-bold mb-1">
                        Existencias bajas
                    </h2>

                    <p class="small text-secondary mb-0">
                        Productos que requieren atención.
                    </p>
                </div>

                <div class="card-body">

                    <ul class="activity-list">

                        @forelse ($productosBajos as $producto)
                            <li class="activity-item">

                                <span class="activity-dot" style="background: var(--bw-plum);"></span>

                                <div class="flex-grow-1">

                                    <div class="d-flex justify-content-between gap-2">

                                        <span class="fw-semibold">
                                            {{ $producto['nombre'] }}
                                        </span>

                                        <span class="badge-status status-bajo">
                                            {{ $producto['existencia'] }} unidades
                                        </span>

                                    </div>

                                    <small class="text-secondary">
                                        Mínimo configurado:
                                        {{ $producto['minimo'] }}
                                    </small>

                                </div>

                            </li>

                        @empty

                            <li class="empty-state">
                                <i class="bi bi-check2-circle"></i>
                                No hay alertas de inventario.
                            </li>
                        @endforelse

                    </ul>

                </div>

            </div>

        </div>

    </div>

@endsection
