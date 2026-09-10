@extends('layouts.admin')

@section('title', 'Consultas')

@section('content')
@php
    $consultas = $consultas ?? collect([
        [
            'id_consulta' => 1,
            'fecha_consulta' => '2026-09-10 09:05:00',
            'motivo_consulta' => 'Dolor lumbar y tensión muscular',
            'tratamiento_realizado' => 'Sesión de acupuntura en puntos lumbares.',
            'cita' => [
                'paciente' => ['nombres' => 'María Fernanda', 'apellidos' => 'López Castillo'],
                'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz'],
                'servicio' => ['nombre' => 'Acupuntura'],
            ],
            'productos' => [['id_producto' => 1]],
        ],
        [
            'id_consulta' => 2,
            'fecha_consulta' => '2026-09-09 10:35:00',
            'motivo_consulta' => 'Seguimiento quiropráctico',
            'tratamiento_realizado' => 'Ajuste y movilización de la zona cervical.',
            'cita' => [
                'paciente' => ['nombres' => 'Carlos Estuardo', 'apellidos' => 'Méndez López'],
                'especialista' => ['nombre_completo' => 'Dr. Luis García'],
                'servicio' => ['nombre' => 'Quiropráctico'],
            ],
            'productos' => [],
        ],
        [
            'id_consulta' => 3,
            'fecha_consulta' => '2026-09-08 12:10:00',
            'motivo_consulta' => 'Evaluación nutricional mensual',
            'tratamiento_realizado' => 'Evaluación de progreso y ajuste del plan nutricional.',
            'cita' => [
                'paciente' => ['nombres' => 'Andrea Lucía', 'apellidos' => 'Morales Díaz'],
                'especialista' => ['nombre_completo' => 'Lic. Sofía Pérez'],
                'servicio' => ['nombre' => 'Terapia nutricional'],
            ],
            'productos' => [['id_producto' => 2], ['id_producto' => 4]],
        ],
        [
            'id_consulta' => 4,
            'fecha_consulta' => '2026-09-05 08:50:00',
            'motivo_consulta' => 'Molestia muscular en hombro derecho',
            'tratamiento_realizado' => 'Acupuntura láser y masaje localizado.',
            'cita' => [
                'paciente' => ['nombres' => 'José Antonio', 'apellidos' => 'Ramírez Soto'],
                'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz'],
                'servicio' => ['nombre' => 'Acupuntura láser'],
            ],
            'productos' => [['id_producto' => 3]],
        ],
    ]);

    $especialistas = $especialistas ?? collect([
        ['id_especialista' => 1, 'nombre_completo' => 'Dra. Ana Ruiz'],
        ['id_especialista' => 2, 'nombre_completo' => 'Dr. Luis García'],
        ['id_especialista' => 3, 'nombre_completo' => 'Lic. Sofía Pérez'],
    ]);

    $resumenConsultas = $resumenConsultas ?? [
        'mes' => 24,
        'hoy' => 3,
        'pacientes' => 18,
        'con_productos' => 14,
    ];

    $valor = fn ($item, $campo, $default = null) => data_get($item, $campo, $default);
    $fecha = function ($valorFecha, $formato) {
        if (!$valorFecha) return 'Sin fecha';
        try { return \Illuminate\Support\Carbon::parse($valorFecha)->format($formato); }
        catch (\Throwable $e) { return $valorFecha; }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Consultas</h1>
        <p class="page-subtitle">Consulta el historial clínico, tratamientos y productos recomendados.</p>
    </div>

    <a href="{{ url('/consultas/create') }}" class="btn btn-brand">
        <i class="bi bi-clipboard2-plus-fill me-2"></i>Nueva consulta
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand"><i class="bi bi-clipboard2-pulse-fill"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenConsultas, 'mes', 0) }}</div><div class="mini-stat-label">Consultas este mes</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw"><i class="bi bi-calendar2-heart-fill"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenConsultas, 'hoy', 0) }}</div><div class="mini-stat-label">Atenciones de hoy</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-plum text-plum-bw"><i class="bi bi-people-fill"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenConsultas, 'pacientes', 0) }}</div><div class="mini-stat-label">Pacientes atendidos</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-lime text-lime-bw"><i class="bi bi-flower2"></i></span>
                <div><div class="mini-stat-value">{{ data_get($resumenConsultas, 'con_productos', 0) }}</div><div class="mini-stat-label">Con productos recomendados</div></div>
            </div>
        </div>
    </div>
</div>

<div class="card bw-card">
    <div class="card-header">
        <form class="row g-2 align-items-end" method="GET" action="{{ url('/consultas') }}">
            <div class="col-12 col-lg-4">
                <label class="form-label visually-hidden" for="buscar">Buscar paciente</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control" id="buscar" name="buscar" type="search" value="{{ request('buscar') }}" placeholder="Buscar por paciente o motivo">
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="desde">Desde</label>
                <input class="form-control" id="desde" name="desde" type="date" value="{{ request('desde') }}" title="Fecha inicial">
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="hasta">Hasta</label>
                <input class="form-control" id="hasta" name="hasta" type="date" value="{{ request('hasta') }}" title="Fecha final">
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

            <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">
                <button class="btn btn-outline-brand flex-grow-1" type="submit">Filtrar</button>
                <a class="btn btn-light border" href="{{ url('/consultas') }}" title="Limpiar filtros" aria-label="Limpiar filtros"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Servicio / especialista</th>
                    <th>Motivo de consulta</th>
                    <th>Productos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($consultas as $consulta)
                    @php
                        $id = $valor($consulta, 'id_consulta');
                        $nombres = $valor($consulta, 'cita.paciente.nombres', '');
                        $apellidos = $valor($consulta, 'cita.paciente.apellidos', '');
                        $nombrePaciente = trim($nombres.' '.$apellidos) ?: $valor($consulta, 'paciente', 'Sin paciente');
                        $iniciales = strtoupper(mb_substr($nombres, 0, 1).mb_substr($apellidos, 0, 1));
                        $productosConsulta = $valor($consulta, 'productos', []);
                        $cantidadProductos = is_countable($productosConsulta) ? count($productosConsulta) : 0;
                    @endphp
                    <tr>
                        <td>
                            <strong class="d-block">{{ $fecha($valor($consulta, 'fecha_consulta'), 'd/m/Y') }}</strong>
                            <span class="text-secondary small">{{ $fecha($valor($consulta, 'fecha_consulta'), 'H:i') }}</span>
                        </td>
                        <td>
                            <div class="record-person compact-person">
                                <span class="list-avatar patient-avatar">{{ $iniciales ?: 'P' }}</span>
                                <div class="min-w-0">
                                    <a class="record-name" href="{{ url('/consultas/'.$id) }}">{{ $nombrePaciente }}</a>
                                    <span class="record-subtitle">Consulta #{{ $id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold small">{{ $valor($consulta, 'cita.servicio.nombre', 'Sin servicio') }}</div>
                            <div class="text-secondary small">{{ $valor($consulta, 'cita.especialista.nombre_completo', 'Sin especialista') }}</div>
                        </td>
                        <td>
                            <div class="consultation-reason">{{ \Illuminate\Support\Str::limit($valor($consulta, 'motivo_consulta', 'Sin motivo'), 74) }}</div>
                            <span class="text-secondary small">{{ \Illuminate\Support\Str::limit($valor($consulta, 'tratamiento_realizado', ''), 64) }}</span>
                        </td>
                        <td>
                            @if ($cantidadProductos > 0)
                                <span class="product-count-badge"><i class="bi bi-flower2"></i>{{ $cantidadProductos }} recomendado{{ $cantidadProductos === 1 ? '' : 's' }}</span>
                            @else
                                <span class="text-secondary small">Sin productos</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <div class="table-actions justify-content-end">
                                <a class="btn btn-sm btn-light border" href="{{ url('/consultas/'.$id) }}" title="Ver consulta" aria-label="Ver consulta"><i class="bi bi-eye"></i></a>
                                <a class="btn btn-sm btn-light border" href="{{ url('/consultas/'.$id.'/edit') }}" title="Editar consulta" aria-label="Editar consulta"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ url('/consultas/'.$id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light border text-danger" type="submit" title="Eliminar consulta" aria-label="Eliminar consulta" data-confirm-delete="Esta acción eliminará la consulta y sus productos recomendados. ¿Deseas continuar?">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-clipboard2-x"></i>No hay consultas que coincidan con los filtros.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (is_object($consultas) && method_exists($consultas, 'links'))
        <div class="card-footer bg-white border-0 pt-0">{{ $consultas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
