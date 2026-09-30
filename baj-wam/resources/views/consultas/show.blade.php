@extends('layouts.admin')

@section('title', 'Detalle de la consulta')

@push('styles')
    <link href="{{ asset('assets/css/consulta-print.css') }}" rel="stylesheet">
@endpush

@section('content')

    @php
        $id = $consulta->id_consulta;

        $nombres = $consulta->cita?->paciente?->nombres ?? '';
        $apellidos = $consulta->cita?->paciente?->apellidos ?? '';

        $nombrePaciente = trim($nombres . ' ' . $apellidos);

        $iniciales = strtoupper(mb_substr($nombres, 0, 1) . mb_substr($apellidos, 0, 1));

        $nombreUsuarioRegistro = trim(
            ($consulta->usuarioRegistro?->nombres ?? '') . ' ' . ($consulta->usuarioRegistro?->apellidos ?? ''),
        );

        $fecha = function ($valor, $formato) {
            if (!$valor) {
                return 'Sin registro';
            }

            try {
                return \Illuminate\Support\Carbon::parse($valor)->format($formato);
            } catch (\Throwable $e) {
                return $valor;
            }
        };

        // RUTA DE REGRESO
        $rutaRegreso = route('consultas.index');

        if (request('origen') === 'paciente' && request()->filled('paciente')) {
            $rutaRegreso = route('pacientes.show', request('paciente'));
        }
    @endphp


    {{-- ENCABEZADO --}}
    <div class="page-header">

        <div>
            <div class="page-eyebrow">
                Historial clínico
            </div>

            <h1 class="page-title">
                Detalle de la consulta
            </h1>

            <p class="page-subtitle">
                Registro de atención, tratamiento y recomendaciones.
            </p>
        </div>


        {{-- ACCIONES --}}
        <div class="d-flex flex-wrap gap-2 no-print">

            @can('consultas.ver')
                <a href="{{ $rutaRegreso }}" class="btn btn-light border">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </a>
            @endcan

            <button class="btn btn-light border" type="button" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>
                Imprimir
            </button>

        </div>

    </div>


    {{-- INFORMACIÓN PRINCIPAL --}}
    <div class="card bw-card consultation-hero mb-4">

        <div class="card-body p-4 p-lg-5">

            <div class="row g-4 align-items-center">

                {{-- PACIENTE --}}
                <div class="col-12 col-lg-7">

                    <div class="d-flex align-items-center gap-3 gap-md-4">

                        <span class="detail-avatar patient-detail-avatar flex-shrink-0">
                            {{ $iniciales ?: 'P' }}
                        </span>

                        <div class="min-w-0">

                            <div class="d-flex flex-wrap gap-2 mb-2">

                                <span class="role-badge">
                                    CONSULTA #{{ $id }}
                                </span>

                                <span class="service-chip">
                                    <i class="bi bi-flower1"></i>

                                    {{ $consulta->cita?->servicio?->nombre ?? 'Sin servicio' }}
                                </span>

                            </div>

                            <h2 class="record-hero-title mb-1">
                                {{ $nombrePaciente ?: 'Paciente sin nombre' }}
                            </h2>

                            <div class="text-secondary">

                                {{ $consulta->cita?->paciente?->telefono ?? 'Sin teléfono' }}

                                ·

                                {{ $consulta->cita?->paciente?->correo ?? 'Sin correo' }}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- DATOS DE ATENCIÓN --}}
                <div class="col-12 col-lg-5">

                    <div class="consultation-meta-card">

                        <div>
                            <span>
                                Fecha de atención
                            </span>

                            <strong>
                                {{ $fecha($consulta->fecha_consulta, 'd/m/Y H:i') }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Especialista
                            </span>

                            <strong>
                                {{ trim(
                                    ($consulta->cita?->especialista?->usuario?->nombres ?? '') .
                                        ' ' .
                                        ($consulta->cita?->especialista?->usuario?->apellidos ?? ''),
                                ) ?:
                                    'Sin especialista' }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Profesión
                            </span>

                            <strong>
                                {{ $consulta->cita?->especialista?->profesion ?? 'Sin registro' }}
                            </strong>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- CONTENIDO --}}
    <div class="row g-4 mb-4 consultation-content">

        <div class="col-12 col-xl-8 consultation-main-column">


            {{-- REGISTRO CLÍNICO --}}
            <div class="card bw-card mb-4 clinical-record-card">

                <div
                    class="card-header
                           d-flex
                           align-items-center
                           justify-content-between">

                    <h2 class="card-title-sm mb-0">
                        Registro clínico
                    </h2>

                    <i class="bi bi-journal-medical text-brand"></i>

                </div>


                <div class="card-body p-4 d-grid gap-3 clinical-record-body">

                    {{-- MOTIVO --}}
                    <section class="clinical-note-block">

                        <span
                            class="clinical-note-icon
                                   bg-soft-purple
                                   text-brand">
                            <i class="bi bi-chat-square-heart-fill"></i>
                        </span>

                        <div>
                            <h3>
                                Motivo de consulta
                            </h3>

                            <p>
                                {{ $consulta->motivo_consulta }}
                            </p>
                        </div>

                    </section>


                    {{-- OBSERVACIONES --}}
                    <section class="clinical-note-block">

                        <span
                            class="clinical-note-icon
                                   bg-soft-green
                                   text-green-bw">
                            <i class="bi bi-search-heart-fill"></i>
                        </span>

                        <div>
                            <h3>
                                Observaciones
                            </h3>

                            <p>
                                {{ $consulta->observaciones ?: 'No se registraron observaciones.' }}
                            </p>
                        </div>

                    </section>


                    {{-- DIAGNÓSTICO --}}
                    <section class="clinical-note-block">

                        <span
                            class="clinical-note-icon
                                   bg-soft-plum
                                   text-plum-bw">
                            <i class="bi bi-activity"></i>
                        </span>

                        <div>
                            <h3>
                                Diagnóstico u orientación
                            </h3>

                            <p>
                                {{ $consulta->diagnostico ?: 'No se registró diagnóstico.' }}
                            </p>
                        </div>

                    </section>


                    {{-- TRATAMIENTO --}}
                    <section class="clinical-note-block is-highlighted">

                        <span
                            class="clinical-note-icon
                                   bg-soft-lime
                                   text-lime-bw">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </span>

                        <div>
                            <h3>
                                Tratamiento realizado
                            </h3>

                            <p>
                                {{ $consulta->tratamiento_realizado }}
                            </p>
                        </div>

                    </section>


                    {{-- RECOMENDACIONES --}}
                    <section class="clinical-note-block">

                        <span
                            class="clinical-note-icon
                                   bg-soft-warning
                                   text-warning-emphasis">
                            <i class="bi bi-list-check"></i>
                        </span>

                        <div>
                            <h3>
                                Recomendaciones generales
                            </h3>

                            <p>
                                {{ $consulta->recomendaciones ?: 'No se registraron recomendaciones.' }}
                            </p>
                        </div>

                    </section>

                </div>

            </div>


            {{-- PRODUCTOS RECOMENDADOS --}}
            <div class="card bw-card products-card">

                <div
                    class="card-header
                           d-flex
                           align-items-center
                           justify-content-between">

                    <div>
                        <h2 class="card-title-sm mb-1">
                            Productos recomendados
                        </h2>

                        <p class="text-secondary small mb-0">
                            Indicaciones registradas durante esta consulta.
                        </p>
                    </div>

                    <i class="bi bi-flower2 text-green-bw"></i>

                </div>


                {{-- ESCRITORIO / TABLET --}}
                <div
                    class="table-responsive
                           d-none
                           d-md-block
                           products-desktop">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Presentación</th>
                                <th>Cantidad</th>
                                <th>Indicaciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($consulta->productos as $producto)
                                <tr>

                                    <td>
                                        <strong class="d-block">
                                            {{ $producto->nombre }}
                                        </strong>

                                        <span class="text-secondary small">
                                            {{ $producto->codigo }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $producto->presentacion ?: 'No indicada' }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $producto->pivot?->cantidad_recomendada ?? 'No indicada' }}
                                    </td>

                                    <td>
                                        {{ $producto->pivot?->indicaciones ?: 'Sin indicaciones' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4">

                                        <div class="empty-state">
                                            <i class="bi bi-flower2"></i>

                                            No se recomendaron productos.
                                        </div>

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- MÓVIL --}}
                <div class="d-md-none p-3 products-mobile">

                    @forelse ($consulta->productos as $producto)
                        <div class="card bw-card mb-3">

                            <div class="card-body">

                                <div
                                    class="d-flex
                                           align-items-start
                                           justify-content-between
                                           gap-3
                                           mb-3">

                                    <div class="min-w-0">

                                        <div class="fw-bold">
                                            {{ $producto->nombre }}
                                        </div>

                                        <div class="text-secondary small">
                                            {{ $producto->codigo }}
                                        </div>

                                    </div>


                                    <span
                                        class="badge-status
                                               status-confirmada
                                               flex-shrink-0
                                               text-nowrap">
                                        Cantidad:
                                        {{ $producto->pivot?->cantidad_recomendada ?? 'No indicada' }}
                                    </span>

                                </div>


                                <div class="row g-3 small">

                                    <div class="col-12">

                                        <div class="text-secondary mb-1">
                                            Presentación
                                        </div>

                                        <div class="fw-semibold">
                                            {{ $producto->presentacion ?: 'No indicada' }}
                                        </div>

                                    </div>


                                    <div class="col-12">

                                        <div class="text-secondary mb-1">
                                            Indicaciones
                                        </div>

                                        <div>
                                            {{ $producto->pivot?->indicaciones ?: 'Sin indicaciones' }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">
                            <i class="bi bi-flower2"></i>

                            No se recomendaron productos.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        {{-- COLUMNA LATERAL --}}
        <div class="col-12 col-xl-4 consultation-side-column">


            {{-- DATOS RELACIONADOS --}}
            <div class="card bw-card mb-4 related-data-card">

                <div
                    class="card-header
                           d-flex
                           align-items-center
                           justify-content-between">

                    <h2 class="card-title-sm mb-0">
                        Datos relacionados
                    </h2>

                    <i class="bi bi-link-45deg text-brand"></i>

                </div>


                <div class="card-body d-grid gap-3 related-data-body">

                    {{-- CITA --}}
                    <div class="detail-item compact">

                        <span class="detail-label">
                            Cita relacionada
                        </span>

                        @can('citas.ver')
                            <a class="detail-value fw-semibold" href="{{ route('citas.show', $consulta->cita->id_cita) }}">
                                Cita #{{ $consulta->cita->id_cita }}
                            </a>
                        @else
                            <strong class="detail-value">
                                Cita #{{ $consulta->cita->id_cita }}
                            </strong>
                        @endcan

                    </div>


                    {{-- HORARIO --}}
                    <div class="detail-item compact">

                        <span class="detail-label">
                            Horario programado
                        </span>

                        <strong class="detail-value">

                            {{ $fecha($consulta->cita?->inicio, 'd/m/Y H:i') }}

                            -

                            {{ $fecha($consulta->cita?->fin, 'H:i') }}

                        </strong>

                    </div>


                    {{-- REGISTRADA POR --}}
                    <div class="detail-item compact">

                        <span class="detail-label">
                            Registrada por
                        </span>

                        <strong class="detail-value">
                            {{ $nombreUsuarioRegistro ?: 'Usuario del sistema' }}
                        </strong>

                    </div>


                    {{-- ACTUALIZACIÓN --}}
                    <div class="detail-item compact">

                        <span class="detail-label">
                            Última actualización
                        </span>

                        <strong class="detail-value">
                            {{ $fecha($consulta->fecha_actualizacion, 'd/m/Y H:i') }}
                        </strong>

                    </div>


                    {{-- EXPEDIENTE --}}
                    @can('pacientes.ver')
                        <a href="{{ route('pacientes.show', $consulta->cita->paciente->id_paciente) }}"
                            class="btn btn-outline-brand no-print">
                            <i class="bi bi-person-vcard me-2"></i>
                            Ver expediente del paciente
                        </a>
                    @endcan

                </div>

            </div>


            {{-- CONFIDENCIALIDAD --}}
            <div class="clinical-privacy-card
                       mb-4
                       privacy-print">

                <span>
                    <i class="bi bi-shield-lock-fill"></i>
                </span>

                <div>

                    <strong>
                        Información confidencial
                    </strong>

                    <p>
                        Este registro debe ser consultado únicamente por
                        personal autorizado.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection 

