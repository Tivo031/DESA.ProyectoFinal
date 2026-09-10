@extends('layouts.admin')

@section('title', 'Citas')

@section('content')
@php
    $citas = $citas ?? collect([
        [
            'id_cita' => 1,
            'inicio' => '2026-09-10 08:00:00',
            'fin' => '2026-09-10 09:00:00',
            'paciente' => ['nombres' => 'María Fernanda', 'apellidos' => 'López Castillo', 'telefono' => '5555-2100'],
            'servicio' => ['nombre' => 'Acupuntura'],
            'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz'],
            'estado' => ['codigo' => 'CONFIRMADA', 'nombre' => 'Confirmada'],
            'observaciones' => 'Primera sesión de tratamiento.',
        ],
        [
            'id_cita' => 2,
            'inicio' => '2026-09-10 09:30:00',
            'fin' => '2026-09-10 10:30:00',
            'paciente' => ['nombres' => 'Carlos Estuardo', 'apellidos' => 'Méndez López', 'telefono' => '5555-3344'],
            'servicio' => ['nombre' => 'Quiropráctico'],
            'especialista' => ['nombre_completo' => 'Dr. Luis García'],
            'estado' => ['codigo' => 'PENDIENTE', 'nombre' => 'Pendiente'],
            'observaciones' => null,
        ],
        [
            'id_cita' => 3,
            'inicio' => '2026-09-10 11:00:00',
            'fin' => '2026-09-10 12:00:00',
            'paciente' => ['nombres' => 'Andrea Lucía', 'apellidos' => 'Morales Díaz', 'telefono' => '5555-7721'],
            'servicio' => ['nombre' => 'Terapia nutricional'],
            'especialista' => ['nombre_completo' => 'Lic. Sofía Pérez'],
            'estado' => ['codigo' => 'COMPLETADA', 'nombre' => 'Completada'],
            'observaciones' => 'Seguimiento mensual.',
        ],
        [
            'id_cita' => 4,
            'inicio' => '2026-09-11 08:00:00',
            'fin' => '2026-09-11 08:45:00',
            'paciente' => ['nombres' => 'José Antonio', 'apellidos' => 'Ramírez Soto', 'telefono' => '5555-1108'],
            'servicio' => ['nombre' => 'Acupuntura láser'],
            'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz'],
            'estado' => ['codigo' => 'CANCELADA', 'nombre' => 'Cancelada'],
            'observaciones' => null,
        ],
    ]);

    $especialistas = $especialistas ?? collect([
        ['id_especialista' => 1, 'nombre_completo' => 'Dra. Ana Ruiz'],
        ['id_especialista' => 2, 'nombre_completo' => 'Dr. Luis García'],
        ['id_especialista' => 3, 'nombre_completo' => 'Lic. Sofía Pérez'],
    ]);

    $estados = $estados ?? collect([
        ['id_estado_cita' => 1, 'codigo' => 'PENDIENTE', 'nombre' => 'Pendiente'],
        ['id_estado_cita' => 2, 'codigo' => 'CONFIRMADA', 'nombre' => 'Confirmada'],
        ['id_estado_cita' => 3, 'codigo' => 'COMPLETADA', 'nombre' => 'Completada'],
        ['id_estado_cita' => 4, 'codigo' => 'CANCELADA', 'nombre' => 'Cancelada'],
        ['id_estado_cita' => 5, 'codigo' => 'NO_ASISTIO', 'nombre' => 'No asistió'],
    ]);

    $resumenCitas = $resumenCitas ?? [
        'hoy' => 3,
        'confirmadas' => 1,
        'pendientes' => 1,
        'canceladas' => 1,
    ];

    $valor = fn ($item, $campo, $default = null) => data_get($item, $campo, $default);
    $fecha = function ($valorFecha, $formato) {
        if (!$valorFecha) return '';
        try { return \Illuminate\Support\Carbon::parse($valorFecha)->format($formato); }
        catch (\Throwable $e) { return $valorFecha; }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Citas</h1>
        <p class="page-subtitle">Organiza la agenda, valida horarios y da seguimiento a cada atención.</p>
    </div>

    <a href="{{ url('/citas/create') }}" class="btn btn-brand">
        <i class="bi bi-calendar-plus-fill me-2"></i>Nueva cita
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand"><i class="bi bi-calendar2-day-fill"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenCitas, 'hoy', 0) }}</div><div class="mini-stat-label">Citas para hoy</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw"><i class="bi bi-calendar2-check-fill"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenCitas, 'confirmadas', 0) }}</div><div class="mini-stat-label">Confirmadas</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-warning text-warning-emphasis"><i class="bi bi-hourglass-split"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenCitas, 'pendientes', 0) }}</div><div class="mini-stat-label">Pendientes</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-danger text-danger"><i class="bi bi-calendar2-x-fill"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenCitas, 'canceladas', 0) }}</div><div class="mini-stat-label">Canceladas</div></div>
            </div>
        </div>
    </div>
</div>

<div class="card bw-card">
    <div class="card-header">
        <form class="row g-2 align-items-end" method="GET" action="{{ url('/citas') }}">
            <div class="col-12 col-lg-4">
                <label class="form-label visually-hidden" for="buscar">Buscar paciente</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control" id="buscar" name="buscar" type="search" value="{{ request('buscar') }}" placeholder="Buscar por paciente o teléfono">
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="fecha">Fecha</label>
                <input class="form-control" id="fecha" name="fecha" type="date" value="{{ request('fecha') }}">
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="especialista">Especialista</label>
                <select class="form-select" id="especialista" name="especialista">
                    <option value="">Todos los especialistas</option>
                    @foreach ($especialistas as $especialista)
                        <option value="{{ $valor($especialista, 'id_especialista') }}" @selected((string) request('especialista') === (string) $valor($especialista, 'id_especialista'))>
                            {{ $valor($especialista, 'nombre_completo') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="estado">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $valor($estado, 'id_estado_cita') }}" @selected((string) request('estado') === (string) $valor($estado, 'id_estado_cita'))>
                            {{ $valor($estado, 'nombre') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">
                <button class="btn btn-outline-brand flex-grow-1" type="submit">Filtrar</button>
                <a class="btn btn-light border" href="{{ url('/citas') }}" title="Limpiar filtros" aria-label="Limpiar filtros"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Fecha y horario</th>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th>Especialista</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($citas as $cita)
                    @php
                        $id = $valor($cita, 'id_cita');
                        $inicio = $valor($cita, 'inicio');
                        $fin = $valor($cita, 'fin');
                        $codigoEstado = strtoupper($valor($cita, 'estado.codigo', $valor($cita, 'estado', 'PENDIENTE')));
                        $nombreEstado = $valor($cita, 'estado.nombre', str_replace('_', ' ', $codigoEstado));
                        $claseEstado = strtolower(str_replace('_', '-', $codigoEstado));
                        $nombres = $valor($cita, 'paciente.nombres', '');
                        $apellidos = $valor($cita, 'paciente.apellidos', '');
                        $nombrePaciente = trim($nombres.' '.$apellidos) ?: $valor($cita, 'paciente', 'Sin paciente');
                        $iniciales = strtoupper(mb_substr($nombres, 0, 1).mb_substr($apellidos, 0, 1));
                    @endphp
                    <tr>
                        <td>
                            <div class="appointment-date-cell">
                                <span class="appointment-day">{{ $fecha($inicio, 'd') }}</span>
                                <span class="appointment-month">{{ strtoupper($fecha($inicio, 'M')) }}</span>
                                <div>
                                    <strong>{{ $fecha($inicio, 'd/m/Y') }}</strong>
                                    <span>{{ $fecha($inicio, 'H:i') }} - {{ $fecha($fin, 'H:i') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="record-person compact-person">
                                <span class="list-avatar patient-avatar">{{ $iniciales ?: 'P' }}</span>
                                <div class="min-w-0">
                                    <a class="record-name" href="{{ url('/citas/'.$id) }}">{{ $nombrePaciente }}</a>
                                    <span class="record-subtitle">{{ $valor($cita, 'paciente.telefono', 'Sin teléfono') }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="service-chip"><i class="bi bi-flower1"></i>{{ $valor($cita, 'servicio.nombre', $valor($cita, 'servicio', 'Sin servicio')) }}</span></td>
                        <td>{{ $valor($cita, 'especialista.nombre_completo', $valor($cita, 'especialista', 'Sin especialista')) }}</td>
                        <td><span class="badge-status status-{{ $claseEstado }}">{{ mb_strtoupper($nombreEstado) }}</span></td>
                        <td class="text-end text-nowrap">
                            <div class="table-actions justify-content-end">
                                <a class="btn btn-sm btn-light border" href="{{ url('/citas/'.$id) }}" title="Ver cita" aria-label="Ver cita"><i class="bi bi-eye"></i></a>
                                <a class="btn btn-sm btn-light border" href="{{ url('/citas/'.$id.'/edit') }}" title="Editar cita" aria-label="Editar cita"><i class="bi bi-pencil"></i></a>
                                @if (!in_array($codigoEstado, ['CANCELADA', 'COMPLETADA']))
                                    <form method="POST" action="{{ url('/citas/'.$id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light border text-danger" type="submit" title="Cancelar cita" aria-label="Cancelar cita" data-confirm-delete="La cita cambiará a estado cancelada. ¿Deseas continuar?">
                                            <i class="bi bi-calendar-x"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-calendar2-x"></i>No hay citas que coincidan con los filtros.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (is_object($citas) && method_exists($citas, 'links'))
        <div class="card-footer bg-white border-0 pt-0">{{ $citas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
