@extends('layouts.admin')

@section('title', 'Citas')

@section('content')

    @php
        $valor = fn($item, $campo, $default = null) => data_get($item, $campo, $default);

        $fecha = function ($valorFecha, $formato) {
            if (!$valorFecha) {
                return '';
            }

            try {
                return \Illuminate\Support\Carbon::parse($valorFecha)->format($formato);
            } catch (\Throwable $e) {
                return $valorFecha;
            }
        };
    @endphp

    <div class="page-header">
        <div>
            <div class="page-eyebrow">
                Atención clínica
            </div>

            <h1 class="page-title">
                Citas
            </h1>

            <p class="page-subtitle">
                Organiza la agenda, valida horarios y da seguimiento a cada atención.
            </p>
        </div>

        <div class="d-flex gap-2">

            @can('solicitudes.ver')
                <a href="{{ route('solicitudes-cita.index') }}" class="btn btn-light border position-relative">
                    <i class="bi bi-inbox me-2"></i>
                    Solicitudes

                    @if ($solicitudesPendientes > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $solicitudesPendientes > 99 ? '99+' : $solicitudesPendientes }}
                        </span>
                    @endif
                </a>
            @endcan

            @can('citas.crear')
                <a href="{{ route('citas.create') }}" class="btn btn-brand">
                    <i class="bi bi-calendar-plus-fill me-2"></i>
                    Nueva cita
                </a>
            @endcan

        </div>
    </div>


    {{-- RESUMEN --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-purple text-brand">
                        <i class="bi bi-calendar2-day-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ data_get($resumenCitas, 'hoy', 0) }}
                        </div>

                        <div class="mini-stat-label">
                            Citas para hoy
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-green text-green-bw">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ data_get($resumenCitas, 'confirmadas', 0) }}
                        </div>

                        <div class="mini-stat-label">
                            Confirmadas
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-warning text-warning-emphasis">
                        <i class="bi bi-hourglass-split"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ data_get($resumenCitas, 'pendientes', 0) }}
                        </div>

                        <div class="mini-stat-label">
                            Pendientes
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-danger text-danger">
                        <i class="bi bi-calendar2-x-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ data_get($resumenCitas, 'canceladas', 0) }}
                        </div>

                        <div class="mini-stat-label">
                            Canceladas
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    <div class="card bw-card">

        {{-- FILTROS --}}
        <div class="card-header">

            <form class="row g-2 align-items-end" method="GET" action="{{ route('citas.index') }}">

                {{-- BUSCAR PACIENTE --}}
                <div class="col-12 col-lg-4">

                    <label class="form-label visually-hidden" for="buscar">
                        Buscar paciente
                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input class="form-control" id="buscar" name="buscar" type="search"
                            value="{{ request('buscar') }}" placeholder="Buscar por paciente o teléfono">

                    </div>
                </div>


                {{-- FECHA --}}
                <div class="col-6 col-md-3 col-lg-2">

                    <label class="form-label visually-hidden" for="fecha">
                        Fecha
                    </label>

                    <input class="form-control" id="fecha" name="fecha" type="date" value="{{ request('fecha') }}">

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
                            @php
                                $nombreEspecialistaFiltro = trim(
                                    $valor($especialista, 'usuario.nombres', '') .
                                        ' ' .
                                        $valor($especialista, 'usuario.apellidos', ''),
                                );
                            @endphp

                            <option value="{{ $valor($especialista, 'id_especialista') }}" @selected((string) request('especialista') === (string) $valor($especialista, 'id_especialista'))>
                                {{ $nombreEspecialistaFiltro ?: 'Sin especialista' }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- ESTADO --}}
                <div class="col-6 col-md-3 col-lg-2">

                    <label class="form-label visually-hidden" for="estado">
                        Estado
                    </label>

                    <select class="form-select" id="estado" name="estado">

                        <option value="">
                            Todos los estados
                        </option>

                        @foreach ($estados as $estado)
                            <option value="{{ $valor($estado, 'id_estado_cita') }}" @selected((string) request('estado') === (string) $valor($estado, 'id_estado_cita'))>
                                {{ $valor($estado, 'nombre') }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- BOTONES --}}
                <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">

                    <button class="btn btn-outline-brand flex-grow-1" type="submit">
                        Filtrar
                    </button>

                    <a class="btn btn-light border" href="{{ route('citas.index') }}" title="Limpiar filtros"
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
                        <th>Fecha y horario</th>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Especialista</th>
                        <th class="cita-estado-col">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($citas as $cita)

                        @php
                            $id = $valor($cita, 'id_cita');

                            $inicio = $valor($cita, 'inicio');
                            $fin = $valor($cita, 'fin');

                            $codigoEstado = strtoupper($valor($cita, 'estado.codigo', 'PENDIENTE'));

                            $nombreEstado = $valor($cita, 'estado.nombre', str_replace('_', ' ', $codigoEstado));

                            $claseEstado = strtolower(str_replace('_', '-', $codigoEstado));

                            $nombresPaciente = $valor($cita, 'paciente.nombres', '');

                            $apellidosPaciente = $valor($cita, 'paciente.apellidos', '');

                            $nombrePaciente = trim($nombresPaciente . ' ' . $apellidosPaciente);

                            if ($nombrePaciente === '') {
                                $nombrePaciente = 'Sin paciente';
                            }

                            $iniciales = strtoupper(
                                mb_substr($nombresPaciente, 0, 1) . mb_substr($apellidosPaciente, 0, 1),
                            );

                            $nombreEspecialista = trim(
                                $valor($cita, 'especialista.usuario.nombres', '') .
                                    ' ' .
                                    $valor($cita, 'especialista.usuario.apellidos', ''),
                            );

                            if ($nombreEspecialista === '') {
                                $nombreEspecialista = 'Sin especialista';
                            }

                            $profesionEspecialista = $valor($cita, 'especialista.profesion', '');
                        @endphp

                        <tr>

                            {{-- FECHA Y HORARIO --}}
                            <td>

                                <div class="appointment-date-cell">

                                    <span class="appointment-day">
                                        {{ $fecha($inicio, 'd') }}
                                    </span>

                                    <span class="appointment-month">
                                        {{ strtoupper($fecha($inicio, 'M')) }}
                                    </span>

                                    <div>

                                        <strong>
                                            {{ $fecha($inicio, 'd/m/Y') }}
                                        </strong>

                                        <span>
                                            {{ $fecha($inicio, 'H:i') }}
                                            -
                                            {{ $fecha($fin, 'H:i') }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- PACIENTE --}}
                            <td>

                                <div class="record-person compact-person">

                                    <span class="list-avatar patient-avatar">
                                        {{ $iniciales ?: 'P' }}
                                    </span>

                                    <div class="min-w-0">

                                        <a class="record-name" href="{{ route('citas.show', $id) }}">
                                            {{ $nombrePaciente }}
                                        </a>

                                        <span class="record-subtitle">
                                            {{ $valor($cita, 'paciente.telefono', 'Sin teléfono') }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- SERVICIO --}}
                            <td>

                                <span class="service-chip">
                                    <i class="bi bi-flower1"></i>
                                    {{ $valor($cita, 'servicio.nombre', 'Sin servicio') }}
                                </span>

                            </td>


                            {{-- ESPECIALISTA --}}
                            <td>

                                <div class="fw-semibold">
                                    {{ $nombreEspecialista }}
                                </div>

                                @if ($profesionEspecialista)
                                    <small class="text-muted">
                                        {{ $profesionEspecialista }}
                                    </small>
                                @endif

                            </td>


                            {{-- ESTADO --}}
                            <td class="cita-estado-col">

                                <span class="badge-status status-{{ $claseEstado }}">
                                    {{ mb_strtoupper($nombreEstado) }}
                                </span>

                            </td>


                            {{-- ACCIONES --}}
                            <td class="text-end text-nowrap">

                                <div class="table-actions justify-content-end">

                                    {{-- VER --}}
                                    <a class="btn btn-sm btn-light border" href="{{ route('citas.show', $id) }}"
                                        title="Ver cita" aria-label="Ver cita">
                                        <i class="bi bi-eye"></i>
                                    </a>


                                    {{-- EDITAR --}}
                                    @can('citas.editar')
                                        <a class="btn btn-sm btn-light border" href="{{ route('citas.edit', $id) }}"
                                            title="Editar cita" aria-label="Editar cita">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan


                                    {{-- CANCELAR --}}
                                    @can('citas.cancelar')
                                        @if (!in_array($codigoEstado, ['CANCELADA', 'COMPLETADA']))
                                            <form method="POST" action="{{ url('/citas/' . $id) }}" class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-light border text-danger" type="submit"
                                                    title="Cancelar cita" aria-label="Cancelar cita"
                                                    data-confirm-delete="La cita cambiará a estado cancelada. ¿Deseas continuar?">
                                                    <i class="bi bi-calendar-x"></i>
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

                                    <i class="bi bi-calendar2-x"></i>

                                    <div class="fw-bold mb-1">
                                        No se encontraron citas
                                    </div>

                                    <div class="small">
                                        No hay citas que coincidan con los filtros.
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

            @forelse ($citas as $cita)

                @php
                    $id = $valor($cita, 'id_cita');

                    $inicio = $valor($cita, 'inicio');
                    $fin = $valor($cita, 'fin');

                    $codigoEstado = strtoupper($valor($cita, 'estado.codigo', 'PENDIENTE'));

                    $nombreEstado = $valor($cita, 'estado.nombre', str_replace('_', ' ', $codigoEstado));

                    $claseEstado = strtolower(str_replace('_', '-', $codigoEstado));

                    $nombresPaciente = $valor($cita, 'paciente.nombres', '');

                    $apellidosPaciente = $valor($cita, 'paciente.apellidos', '');

                    $nombrePaciente = trim($nombresPaciente . ' ' . $apellidosPaciente);

                    if ($nombrePaciente === '') {
                        $nombrePaciente = 'Sin paciente';
                    }

                    $iniciales = strtoupper(mb_substr($nombresPaciente, 0, 1) . mb_substr($apellidosPaciente, 0, 1));

                    $nombreEspecialista = trim(
                        $valor($cita, 'especialista.usuario.nombres', '') .
                            ' ' .
                            $valor($cita, 'especialista.usuario.apellidos', ''),
                    );

                    if ($nombreEspecialista === '') {
                        $nombreEspecialista = 'Sin especialista';
                    }

                    $profesionEspecialista = $valor($cita, 'especialista.profesion', '');
                @endphp


                <div class="card bw-card mb-3">

                    <div class="card-body">

                        {{-- ENCABEZADO --}}
                        {{-- ENCABEZADO --}}
                        <div class="mb-3">

                            <div class="d-flex align-items-start gap-3">

                                <div class="appointment-date-cell flex-shrink-0">

                                    <span class="appointment-day">
                                        {{ $fecha($inicio, 'd') }}
                                    </span>

                                    <span class="appointment-month">
                                        {{ strtoupper($fecha($inicio, 'M')) }}
                                    </span>

                                </div>


                                <div class="flex-grow-1 min-w-0">

                                    <div class="fw-bold text-nowrap">
                                        {{ $fecha($inicio, 'd/m/Y') }}
                                    </div>

                                    <div class="text-secondary small text-nowrap">
                                        {{ $fecha($inicio, 'H:i') }}
                                        -
                                        {{ $fecha($fin, 'H:i') }}
                                    </div>

                                </div>

                            </div>


                            <div class="mt-3">

                                <span class="badge-status status-{{ $claseEstado }} d-inline-flex text-nowrap">
                                    {{ mb_strtoupper($nombreEstado) }}
                                </span>

                            </div>

                        </div>

                        {{-- PACIENTE --}}
                        <div class="d-flex align-items-center gap-3 mb-3">

                            <span class="list-avatar patient-avatar flex-shrink-0">
                                {{ $iniciales ?: 'P' }}
                            </span>

                            <div class="min-w-0">

                                <a href="{{ route('citas.show', $id) }}" class="record-name text-break">
                                    {{ $nombrePaciente }}
                                </a>

                                <div class="text-secondary small">
                                    {{ $valor($cita, 'paciente.telefono', 'Sin teléfono') }}
                                </div>

                            </div>

                        </div>


                        {{-- INFORMACIÓN --}}
                        <div class="row g-3 small">

                            <div class="col-12">

                                <div class="text-secondary">
                                    Servicio
                                </div>

                                <div class="mt-1">

                                    <span class="service-chip">
                                        <i class="bi bi-flower1"></i>

                                        {{ $valor($cita, 'servicio.nombre', 'Sin servicio') }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-12">

                                <div class="text-secondary">
                                    Especialista
                                </div>

                                <div class="fw-semibold">
                                    {{ $nombreEspecialista }}
                                </div>

                                @if ($profesionEspecialista)
                                    <div class="text-secondary">
                                        {{ $profesionEspecialista }}
                                    </div>
                                @endif

                            </div>

                        </div>


                        <hr>


                        {{-- ACCIONES --}}
                        <div class="d-flex gap-2">

                            <a href="{{ route('citas.show', $id) }}" class="btn btn-light border flex-fill">
                                <i class="bi bi-eye me-1"></i>
                                Ver
                            </a>


                            @can('citas.editar')
                                <a href="{{ route('citas.edit', $id) }}" class="btn btn-outline-brand flex-fill">
                                    <i class="bi bi-pencil me-1"></i>
                                    Editar
                                </a>
                            @endcan


                            @can('citas.cancelar')
                                @if (!in_array($codigoEstado, ['CANCELADA', 'COMPLETADA']))
                                    <form method="POST" action="{{ url('/citas/' . $id) }}" class="flex-fill">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-outline-danger w-100"
                                            data-confirm-delete="La cita cambiará a estado cancelada. ¿Deseas continuar?">
                                            <i class="bi bi-calendar-x me-1"></i>
                                            Cancelar
                                        </button>

                                    </form>
                                @endif
                            @endcan

                        </div>

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <i class="bi bi-calendar2-x"></i>

                    <div class="fw-bold mb-1">
                        No se encontraron citas
                    </div>

                    <div class="small">
                        No hay citas que coincidan con los filtros.
                    </div>

                </div>

            @endforelse

        </div>


        {{-- PAGINACIÓN --}}
        @if ($citas->hasPages())
            <div class="card-footer bg-white border-0 px-3 py-3">

                {{ $citas->links('pagination::bootstrap-5') }}

            </div>
        @endif

    </div>

@endsection
