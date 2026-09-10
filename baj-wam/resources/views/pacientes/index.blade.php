@extends('layouts.admin')

@section('title', 'Pacientes')

@section('content')
@php
    $pacientes = $pacientes ?? collect([
        [
            'id_paciente' => 1,
            'dpi' => '2456789010101',
            'nombres' => 'María Fernanda',
            'apellidos' => 'López Castillo',
            'sexo' => 'FEMENINO',
            'telefono' => '5555-2100',
            'correo' => 'maria@example.com',
            'activo' => true,
            'fecha_registro' => '2026-07-12',
            'ultima_cita' => '2026-09-08',
        ],
        [
            'id_paciente' => 2,
            'dpi' => '2567890120101',
            'nombres' => 'Carlos Eduardo',
            'apellidos' => 'Méndez Ruiz',
            'sexo' => 'MASCULINO',
            'telefono' => '5555-2198',
            'correo' => 'carlos@example.com',
            'activo' => true,
            'fecha_registro' => '2026-07-20',
            'ultima_cita' => '2026-09-02',
        ],
        [
            'id_paciente' => 3,
            'dpi' => null,
            'nombres' => 'Andrea',
            'apellidos' => 'Morales Pérez',
            'sexo' => 'FEMENINO',
            'telefono' => '5555-1644',
            'correo' => 'andrea@example.com',
            'activo' => true,
            'fecha_registro' => '2026-08-04',
            'ultima_cita' => '2026-08-27',
        ],
        [
            'id_paciente' => 4,
            'dpi' => '2678901230101',
            'nombres' => 'José Antonio',
            'apellidos' => 'García López',
            'sexo' => 'MASCULINO',
            'telefono' => '5555-1412',
            'correo' => null,
            'activo' => false,
            'fecha_registro' => '2026-06-18',
            'ultima_cita' => '2026-07-30',
        ],
    ]);

    $resumenPacientes = $resumenPacientes ?? [
        'total' => 4,
        'activos' => 3,
        'nuevos_mes' => 1,
    ];

    $valor = fn ($item, $campo, $default = null) => data_get($item, $campo, $default);
    $formatearFecha = function ($fecha) {
        if (!$fecha) {
            return 'Sin registro';
        }

        try {
            return \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y');
        } catch (\Throwable $e) {
            return $fecha;
        }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Pacientes</h1>
        <p class="page-subtitle">Registro, búsqueda y seguimiento general de los pacientes de BAJ WAM.</p>
    </div>

    <a href="{{ url('/pacientes/create') }}" class="btn btn-brand">
        <i class="bi bi-person-plus-fill me-2"></i>Nuevo paciente
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand"><i class="bi bi-people-fill"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenPacientes, 'total', 0) }}</div>
                    <div class="mini-stat-label">Pacientes registrados</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw"><i class="bi bi-person-check-fill"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenPacientes, 'activos', 0) }}</div>
                    <div class="mini-stat-label">Pacientes activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-lime text-green-bw"><i class="bi bi-calendar-plus-fill"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenPacientes, 'nuevos_mes', 0) }}</div>
                    <div class="mini-stat-label">Nuevos este mes</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card bw-card">
    <div class="card-header">
        <form class="row g-2 align-items-end" method="GET" action="{{ url('/pacientes') }}">
            <div class="col-12 col-lg-6">
                <label class="form-label visually-hidden" for="buscar">Buscar paciente</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input
                        class="form-control"
                        id="buscar"
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por nombre, DPI o teléfono"
                    >
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label visually-hidden" for="sexo">Sexo</label>
                <select class="form-select" id="sexo" name="sexo">
                    <option value="">Todos</option>
                    <option value="FEMENINO" @selected(request('sexo') === 'FEMENINO')>Femenino</option>
                    <option value="MASCULINO" @selected(request('sexo') === 'MASCULINO')>Masculino</option>
                    <option value="OTRO" @selected(request('sexo') === 'OTRO')>Otro</option>
                </select>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label visually-hidden" for="estado">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('estado') === '1')>Activos</option>
                    <option value="0" @selected(request('estado') === '0')>Inactivos</option>
                </select>
            </div>

            <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                <button class="btn btn-outline-brand flex-grow-1" type="submit">Filtrar</button>
                <a class="btn btn-light border" href="{{ url('/pacientes') }}" title="Limpiar filtros" aria-label="Limpiar filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Contacto</th>
                    <th>DPI</th>
                    <th>Última cita</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pacientes as $paciente)
                    @php
                        $id = $valor($paciente, 'id_paciente');
                        $nombres = $valor($paciente, 'nombres', '');
                        $apellidos = $valor($paciente, 'apellidos', '');
                        $nombreCompleto = trim($nombres.' '.$apellidos);
                        $iniciales = strtoupper(mb_substr($nombres, 0, 1).mb_substr($apellidos, 0, 1));
                        $activo = (bool) $valor($paciente, 'activo', true);
                    @endphp
                    <tr>
                        <td>
                            <div class="record-person">
                                <span class="list-avatar patient-avatar">{{ $iniciales ?: 'P' }}</span>
                                <div class="min-w-0">
                                    <a class="record-name" href="{{ url('/pacientes/'.$id) }}">{{ $nombreCompleto ?: 'Sin nombre' }}</a>
                                    <span class="record-subtitle">Paciente #{{ str_pad((string) $id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold small">{{ $valor($paciente, 'telefono', 'Sin teléfono') }}</div>
                            <div class="text-secondary small">{{ $valor($paciente, 'correo', 'Sin correo') }}</div>
                        </td>
                        <td>{{ $valor($paciente, 'dpi', 'No registrado') ?: 'No registrado' }}</td>
                        <td>{{ $formatearFecha($valor($paciente, 'ultima_cita')) }}</td>
                        <td>
                            <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                                {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <div class="table-actions justify-content-end">
                                <a class="btn btn-sm btn-light border" href="{{ url('/pacientes/'.$id) }}" title="Ver paciente" aria-label="Ver paciente">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-light border" href="{{ url('/pacientes/'.$id.'/edit') }}" title="Editar paciente" aria-label="Editar paciente">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($activo)
                                    <form method="POST" action="{{ url('/pacientes/'.$id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="btn btn-sm btn-light border text-danger"
                                            type="submit"
                                            title="Desactivar paciente"
                                            aria-label="Desactivar paciente"
                                            data-confirm-delete="¿Deseas desactivar a {{ $nombreCompleto }}? Su historial no será eliminado."
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
                                <div class="fw-bold mb-1">No se encontraron pacientes</div>
                                <div class="small">Prueba con otros filtros o registra un nuevo paciente.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (is_object($pacientes) && method_exists($pacientes, 'links'))
        <div class="card-footer bg-white border-0 px-3 py-3">
            {{ $pacientes->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
