@extends('layouts.admin')

@section('title', 'Consultas')

@section('content')

    @php
        $fecha = function ($valorFecha, $formato) {
            if (!$valorFecha) {
                return 'Sin fecha';
            }

            try {
                return \Illuminate\Support\Carbon::parse($valorFecha)->format($formato);
            } catch (\Throwable $e) {
                return $valorFecha;
            }
        };
    @endphp


    {{-- ENCABEZADO --}}
    <div class="page-header">

        <div>
            <div class="page-eyebrow">
                Atención clínica
            </div>

            <h1 class="page-title">
                Consultas
            </h1>

            <p class="page-subtitle">
                Consulta el historial clínico, tratamientos y productos recomendados.
            </p>
        </div>


        @can('consultas.crear')
            <a href="{{ route('consultas.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-2"></i>
                Nueva consulta
            </a>
        @endcan

    </div>


    {{-- RESUMEN --}}
    <div class="row g-3 mb-4">

        {{-- CONSULTAS DEL MES --}}
        <div class="col-6 col-xl-3">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-purple text-brand">
                        <i class="bi bi-clipboard2-pulse-fill"></i>
                    </span>

                    <div>

                        <div class="mini-stat-value">
                            {{ $resumenConsultas['mes'] ?? 0 }}
                        </div>

                        <div class="mini-stat-label">
                            Consultas este mes
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ATENCIONES DE HOY --}}
        <div class="col-6 col-xl-3">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-green text-green-bw">
                        <i class="bi bi-calendar2-heart-fill"></i>
                    </span>

                    <div>

                        <div class="mini-stat-value">
                            {{ $resumenConsultas['hoy'] ?? 0 }}
                        </div>

                        <div class="mini-stat-label">
                            Atenciones de hoy
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PACIENTES --}}
        <div class="col-6 col-xl-3">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-plum text-plum-bw">
                        <i class="bi bi-people-fill"></i>
                    </span>

                    <div>

                        <div class="mini-stat-value">
                            {{ $resumenConsultas['pacientes'] ?? 0 }}
                        </div>

                        <div class="mini-stat-label">
                            Pacientes atendidos
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PRODUCTOS --}}
        <div class="col-6 col-xl-3">

            <div class="card bw-card mini-stat-card h-100">

                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-lime text-lime-bw">
                        <i class="bi bi-flower2"></i>
                    </span>

                    <div>

                        <div class="mini-stat-value">
                            {{ $resumenConsultas['con_productos'] ?? 0 }}
                        </div>

                        <div class="mini-stat-label">
                            Con productos recomendados
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- CONTENIDO --}}
    <div class="card bw-card">


        {{-- FILTROS --}}
        <div class="card-header">

            <form class="row g-2 align-items-end" method="GET" action="{{ url('/consultas') }}">

                {{-- BUSCAR --}}
                <div class="col-12 col-lg-4">

                    <label class="form-label visually-hidden" for="buscar">
                        Buscar paciente
                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input class="form-control" id="buscar" name="buscar" type="search"
                            value="{{ request('buscar') }}" placeholder="Buscar por paciente o motivo">

                    </div>

                </div>


                {{-- DESDE --}}
                <div class="col-6 col-md-3 col-lg-2">

                    <label class="form-label visually-hidden" for="desde">
                        Desde
                    </label>

                    <input class="form-control" id="desde" name="desde" type="date" value="{{ request('desde') }}"
                        title="Fecha inicial">

                </div>


                {{-- HASTA --}}
                <div class="col-6 col-md-3 col-lg-2">

                    <label class="form-label visually-hidden" for="hasta">
                        Hasta
                    </label>

                    <input class="form-control" id="hasta" name="hasta" type="date" value="{{ request('hasta') }}"
                        title="Fecha final">

                </div>


                {{-- ESPECIALISTA --}}
                <div class="col-6 col-md-3 col-lg-2">

                    <label class="form-label visually-hidden" for="especialista">
                        Especialista
                    </label>

                    <select class="form-select" id="especialista" name="especialista">

                        <option value="">
                            Todos los especialistas
                        </option>

                        @foreach ($especialistas as $especialista)
                            <option value="{{ $especialista->id_especialista }}" @selected((string) request('especialista') === (string) $especialista->id_especialista)>
                                {{ $especialista->usuario?->nombres }}
                                {{ $especialista->usuario?->apellidos }}
                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- BOTONES --}}
                <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">

                    <button class="btn btn-outline-brand flex-grow-1" type="submit">
                        Filtrar
                    </button>

                    <a class="btn btn-light border" href="{{ url('/consultas') }}" title="Limpiar filtros"
                        aria-label="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>

                </div>

            </form>

        </div>


        {{-- ESCRITORIO / TABLET --}}
        <div class="table-responsive d-none d-md-block">

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
                            $paciente = $consulta->cita?->paciente;

                            $especialista = $consulta->cita?->especialista;

                            $usuarioEspecialista = $especialista?->usuario;

                            $nombrePaciente = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? ''));

                            $nombreEspecialista = trim(
                                ($usuarioEspecialista?->nombres ?? '') . ' ' . ($usuarioEspecialista?->apellidos ?? ''),
                            );

                            $iniciales = strtoupper(
                                mb_substr($paciente?->nombres ?? '', 0, 1) .
                                    mb_substr($paciente?->apellidos ?? '', 0, 1),
                            );

                            $cantidadProductos = $consulta->productos->count();
                        @endphp


                        <tr>

                            {{-- FECHA --}}
                            <td>

                                <strong class="d-block">
                                    {{ $fecha($consulta->fecha_consulta, 'd/m/Y') }}
                                </strong>

                                <span class="text-secondary small">
                                    {{ $fecha($consulta->fecha_consulta, 'H:i') }}
                                </span>

                            </td>


                            {{-- PACIENTE --}}
                            <td>

                                <div class="record-person compact-person">

                                    <span class="list-avatar patient-avatar">
                                        {{ $iniciales ?: 'P' }}
                                    </span>

                                    <div class="min-w-0">

                                        <a class="record-name"
                                            href="{{ url('/consultas/' . $consulta->id_consulta) }}">
                                            {{ $nombrePaciente ?: 'Sin paciente' }}
                                        </a>

                                        <span class="record-subtitle">
                                            Consulta #{{ $consulta->id_consulta }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- SERVICIO / ESPECIALISTA --}}
                            <td>

                                <div class="fw-semibold small">
                                    {{ $consulta->cita?->servicio?->nombre ?? 'Sin servicio' }}
                                </div>

                                <div class="text-secondary small">
                                    {{ $nombreEspecialista ?: 'Sin especialista' }}
                                </div>

                            </td>


                            {{-- MOTIVO --}}
                            <td>

                                <div class="consultation-reason">
                                    {{ \Illuminate\Support\Str::limit($consulta->motivo_consulta ?? 'Sin motivo', 74) }}
                                </div>

                                <span class="text-secondary small">
                                    {{ \Illuminate\Support\Str::limit($consulta->tratamiento_realizado ?? '', 64) }}
                                </span>

                            </td>


                            {{-- PRODUCTOS --}}
                            <td>

                                @if ($cantidadProductos > 0)
                                    <span class="product-count-badge">

                                        <i class="bi bi-flower2"></i>

                                        {{ $cantidadProductos }}

                                        recomendado{{ $cantidadProductos === 1 ? '' : 's' }}

                                    </span>
                                @else
                                    <span class="text-secondary small">
                                        Sin productos
                                    </span>
                                @endif

                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-end text-nowrap">

                                <div class="table-actions justify-content-end">

                                    <a class="btn btn-sm btn-light border"
                                        href="{{ url('/consultas/' . $consulta->id_consulta) }}"
                                        title="Ver consulta" aria-label="Ver consulta">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="empty-state">

                                    <i class="bi bi-clipboard2-x"></i>

                                    No hay consultas que coincidan
                                    con los filtros.

                                </div>

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- MÓVIL --}}
        <div class="d-md-none p-3">

            @forelse ($consultas as $consulta)

                @php
                    $paciente = $consulta->cita?->paciente;

                    $especialista = $consulta->cita?->especialista;

                    $usuarioEspecialista = $especialista?->usuario;

                    $nombrePaciente = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? ''));

                    $nombreEspecialista = trim(
                        ($usuarioEspecialista?->nombres ?? '') . ' ' . ($usuarioEspecialista?->apellidos ?? ''),
                    );

                    $iniciales = strtoupper(
                        mb_substr($paciente?->nombres ?? '', 0, 1) . mb_substr($paciente?->apellidos ?? '', 0, 1),
                    );

                    $cantidadProductos = $consulta->productos->count();
                @endphp


                <div class="card bw-card mb-3">

                    <div class="card-body">


                        {{-- PACIENTE --}}
                        <div class="d-flex align-items-start gap-3 mb-3">

                            <span class="list-avatar patient-avatar flex-shrink-0">
                                {{ $iniciales ?: 'P' }}
                            </span>


                            <div class="flex-grow-1 min-w-0">

                                <a href="{{ url('/consultas/' . $consulta->id_consulta) }}"
                                    class="record-name text-break">
                                    {{ $nombrePaciente ?: 'Sin paciente' }}
                                </a>

                                <div class="text-secondary small">

                                    Consulta
                                    #{{ $consulta->id_consulta }}

                                </div>

                            </div>


                            <div class="text-end flex-shrink-0">

                                <div class="fw-bold small">
                                    {{ $fecha($consulta->fecha_consulta, 'd/m/Y') }}
                                </div>

                                <div class="text-secondary small">
                                    {{ $fecha($consulta->fecha_consulta, 'H:i') }}
                                </div>

                            </div>

                        </div>


                        {{-- INFORMACIÓN --}}
                        <div class="row g-3 small">


                            {{-- SERVICIO --}}
                            <div class="col-12">

                                <div class="text-secondary">
                                    Servicio
                                </div>

                                <div class="fw-semibold">
                                    {{ $consulta->cita?->servicio?->nombre ?? 'Sin servicio' }}
                                </div>

                            </div>


                            {{-- ESPECIALISTA --}}
                            <div class="col-12">

                                <div class="text-secondary">
                                    Especialista
                                </div>

                                <div class="fw-semibold">
                                    {{ $nombreEspecialista ?: 'Sin especialista' }}
                                </div>

                                @if ($especialista?->profesion)
                                    <div class="text-secondary">
                                        {{ $especialista->profesion }}
                                    </div>
                                @endif

                            </div>


                            {{-- MOTIVO --}}
                            <div class="col-12">

                                <div class="text-secondary">
                                    Motivo de consulta
                                </div>

                                <div class="fw-semibold">
                                    {{ \Illuminate\Support\Str::limit($consulta->motivo_consulta ?? 'Sin motivo', 110) }}
                                </div>

                            </div>


                            {{-- TRATAMIENTO --}}
                            @if ($consulta->tratamiento_realizado)
                                <div class="col-12">

                                    <div class="text-secondary">
                                        Tratamiento realizado
                                    </div>

                                    <div>
                                        {{ \Illuminate\Support\Str::limit($consulta->tratamiento_realizado, 110) }}
                                    </div>

                                </div>
                            @endif


                            {{-- PRODUCTOS --}}
                            <div class="col-12">

                                <div class="text-secondary mb-1">
                                    Productos recomendados
                                </div>

                                @if ($cantidadProductos > 0)
                                    <span class="product-count-badge">

                                        <i class="bi bi-flower2"></i>

                                        {{ $cantidadProductos }}

                                        recomendado{{ $cantidadProductos === 1 ? '' : 's' }}

                                    </span>
                                @else
                                    <span class="text-secondary">
                                        Sin productos
                                    </span>
                                @endif

                            </div>

                        </div>


                        <hr>


                        {{-- ACCIONES --}}
                        <div class="d-flex">

                            <a href="{{ url('/consultas/' . $consulta->id_consulta) }}"
                                class="btn btn-light border w-100">
                                <i class="bi bi-eye me-1"></i>
                                Ver consulta
                            </a>

                        </div>

                    </div>

                </div>


            @empty

                <div class="empty-state">

                    <i class="bi bi-clipboard2-x"></i>

                    <div class="fw-bold mb-1">
                        No se encontraron consultas
                    </div>

                    <div class="small">
                        No hay consultas que coincidan con los filtros.
                    </div>

                </div>
            @endforelse

        </div>


        {{-- PAGINACIÓN --}}
        @if ($consultas->hasPages())
            <div class="card-footer bg-white border-0 px-3 py-3">
                {{ $consultas->links() }}
            </div>
        @endif

    </div>

@endsection
