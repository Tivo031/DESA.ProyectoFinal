@extends('layouts.admin')

@section('title', 'Detalle de la cita')

@section('content')

@php
    $id = $cita->id_cita;
    $inicio = $cita->inicio;
    $fin = $cita->fin;

    $codigoEstado = strtoupper(
        $cita->estado?->codigo ?? 'PENDIENTE'
    );

    $nombreEstado = $cita->estado?->nombre ?? 'Pendiente';

    $claseEstado = strtolower(
        str_replace('_', '-', $codigoEstado)
    );

    $nombrePaciente = trim(
        ($cita->paciente?->nombres ?? '') . ' ' .
        ($cita->paciente?->apellidos ?? '')
    );

    $iniciales = strtoupper(
        mb_substr($cita->paciente?->nombres ?? '', 0, 1) .
        mb_substr($cita->paciente?->apellidos ?? '', 0, 1)
    );

    $nombreEspecialista = trim(
        ($cita->especialista?->usuario?->nombres ?? '') . ' ' .
        ($cita->especialista?->usuario?->apellidos ?? '')
    );

    $nombreUsuarioRegistro = trim(
        ($cita->usuarioRegistro?->nombres ?? '') . ' ' .
        ($cita->usuarioRegistro?->apellidos ?? '')
    );
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Agenda clínica</div>

        <h1 class="page-title">
            Detalle de la cita
        </h1>

        <p class="page-subtitle">
            Consulta la información completa de la cita registrada.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">

        <a
            href="{{ route('citas.index') }}"
            class="btn btn-light border"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Regresar
        </a>

        <a
            href="{{ route('citas.edit', $cita->id_cita) }}"
            class="btn btn-brand"
        >
            <i class="bi bi-pencil me-2"></i>
            Editar
        </a>

        @if (!in_array($codigoEstado, ['CANCELADA', 'COMPLETADA']))
            <button
                class="btn btn-outline-danger"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#modalCancelarCita"
            >
                <i class="bi bi-calendar-x me-2"></i>
                Cancelar cita
            </button>
        @endif

    </div>
</div>

<div class="card bw-card appointment-hero mb-4">

    <div class="card-body p-4 p-lg-5">

        <div class="row g-4 align-items-center">

            <div class="col-12 col-lg-7">

                <div class="d-flex align-items-center gap-3 gap-md-4">

                    <div class="appointment-hero-date">

                        <strong>
                            {{ $inicio->format('d') }}
                        </strong>

                        <span>
                            {{ strtoupper($inicio->format('M')) }}
                        </span>

                    </div>

                    <div>

                        <div class="d-flex flex-wrap gap-2 mb-2">

                            <span class="badge-status status-{{ $claseEstado }}">
                                {{ mb_strtoupper($nombreEstado) }}
                            </span>

                            <span class="role-badge">
                                CITA #{{ $id }}
                            </span>

                        </div>

                        <h2 class="record-hero-title mb-1">
                            {{ $inicio->format('d/m/Y') }}
                        </h2>

                        <div class="appointment-hero-time">

                            <i class="bi bi-clock-fill"></i>

                            {{ $inicio->format('H:i') }}
                            -
                            {{ $fin->format('H:i') }}

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-12 col-lg-5">

                <div class="appointment-patient-card">

                    <span class="detail-avatar patient-detail-avatar">
                        {{ $iniciales ?: 'P' }}
                    </span>

                    <div class="min-w-0">

                        <span class="detail-label">
                            Paciente
                        </span>

                        <div class="record-name fs-5">
                            {{ $nombrePaciente ?: 'Sin paciente' }}
                        </div>

                        <span class="record-subtitle">

                            {{ $cita->paciente?->telefono ?? 'Sin teléfono' }}

                            @if ($cita->paciente?->correo)
                                · {{ $cita->paciente->correo }}
                            @endif

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="row g-4 mb-4">

    <div class="col-12 col-lg-7">

        <div class="card bw-card h-100">

            <div class="card-header d-flex align-items-center justify-content-between">

                <h2 class="card-title-sm mb-0">
                    Datos de la atención
                </h2>

                <i class="bi bi-calendar2-check text-brand"></i>

            </div>


            <div class="card-body">

                <div class="detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            Servicio
                        </span>

                        <strong class="detail-value">
                            {{ $cita->servicio?->nombre ?? 'Sin servicio' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Duración
                        </span>

                        <strong class="detail-value">

                            @if ($cita->servicio?->duracion_minutos)
                                {{ $cita->servicio->duracion_minutos }} minutos
                            @else
                                No indicada
                            @endif

                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Especialista
                        </span>

                        <strong class="detail-value">
                            {{ $nombreEspecialista ?: 'Sin especialista' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Profesión
                        </span>

                        <strong class="detail-value">
                            {{ $cita->especialista?->profesion ?? 'No indicada' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Registrada por
                        </span>

                        <strong class="detail-value">
                            {{ $nombreUsuarioRegistro ?: 'Usuario del sistema' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Fecha de registro
                        </span>

                        <strong class="detail-value">

                            @if ($cita->fecha_creacion)
                                {{ $cita->fecha_creacion->format('d/m/Y H:i') }}
                            @else
                                Sin registro
                            @endif

                        </strong>

                    </div>

                </div>


                <div class="detail-item mt-3">

                    <span class="detail-label">
                        Observaciones
                    </span>

                    <p class="detail-value mb-0">
                        {{ $cita->observaciones ?: 'No se registraron observaciones.' }}
                    </p>

                </div>


                @if ($codigoEstado === 'CANCELADA')

                    <div class="detail-item mt-3 border-danger-subtle bg-danger-subtle">

                        <span class="detail-label text-danger">
                            Motivo de cancelación
                        </span>

                        <p class="detail-value mb-0">
                            {{ $cita->motivo_cancelacion ?: 'No indicado.' }}
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>


    <div class="col-12 col-lg-5">

        <div class="card bw-card h-100">

            <div class="card-header d-flex align-items-center justify-content-between">

                <h2 class="card-title-sm mb-0">
                    Información de la cita
                </h2>

                <i class="bi bi-info-circle-fill text-brand"></i>

            </div>


            <div class="card-body">

                <div class="detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            Estado
                        </span>

                        <strong class="detail-value">
                            {{ $nombreEstado }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Fecha
                        </span>

                        <strong class="detail-value">
                            {{ $inicio->format('d/m/Y') }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Hora de inicio
                        </span>

                        <strong class="detail-value">
                            {{ $inicio->format('H:i') }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Hora de finalización
                        </span>

                        <strong class="detail-value">
                            {{ $fin->format('H:i') }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@if (!in_array($codigoEstado, ['CANCELADA', 'COMPLETADA']))
    <div
        class="modal fade"
        id="modalCancelarCita"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form
                    method="POST"
                    action="{{ route('citas.destroy', $cita->id_cita) }}"
                >
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Cancelar cita
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>

                    <div class="modal-body">

                        <p>
                            ¿Deseas cancelar la cita de
                            <strong>{{ $nombrePaciente }}</strong>?
                        </p>

                        <label
                            class="form-label"
                            for="motivo_cancelacion"
                        >
                            Motivo de cancelación
                        </label>

                        <textarea
                            class="form-control"
                            id="motivo_cancelacion"
                            name="motivo_cancelacion"
                            rows="3"
                            maxlength="250"
                            placeholder="Escribe el motivo si es necesario"
                        ></textarea>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal"
                        >
                            Regresar
                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Cancelar cita
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
@endif

@endsection