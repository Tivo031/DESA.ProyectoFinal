@extends('layouts.admin')

@section('title', 'Detalle del paciente')

@section('content')

    <div class="page-header">

        <div>

            <div class="page-eyebrow">
                Atención clínica
            </div>

            <h1 class="page-title">
                {{ $paciente->nombres }} {{ $paciente->apellidos }}
            </h1>

            <p class="page-subtitle">
                Información general e historial de citas del paciente.
            </p>

        </div>


        <div class="d-flex flex-wrap gap-2">

            @can('pacientes.ver')
                <a href="{{ route('pacientes.index') }}" class="btn btn-light border">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </a>
            @endcan


            @can('pacientes.editar')
                <a href="{{ route('pacientes.edit', $paciente->id_paciente) }}" class="btn btn-brand">
                    <i class="bi bi-pencil me-2"></i>
                    Editar paciente
                </a>


                @if ($paciente->activo)
                    <form method="POST" action="{{ route('pacientes.destroy', $paciente->id_paciente) }}">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-danger"
                            data-confirm-delete="El paciente quedará inactivo. ¿Deseas continuar?">
                            <i class="bi bi-person-x me-2"></i>
                            Desactivar
                        </button>

                    </form>
                @endif
            @endcan

        </div>

    </div>


    <div class="row g-4 mb-4">

        <div class="col-12 col-lg-8">

            <div class="card bw-card h-100">

                <div class="card-header">

                    <h2 class="card-title-sm mb-0">
                        Información personal
                    </h2>

                </div>


                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-6">

                            <span class="detail-label">
                                Nombre completo
                            </span>

                            <div class="fw-semibold">
                                {{ $paciente->nombres }}
                                {{ $paciente->apellidos }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <span class="detail-label">
                                DPI
                            </span>

                            <div>
                                {{ $paciente->dpi ?: 'No registrado' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <span class="detail-label">
                                Teléfono
                            </span>

                            <div>
                                <i class="bi bi-telephone me-1 text-muted"></i>
                                {{ $paciente->telefono }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <span class="detail-label">
                                Correo electrónico
                            </span>

                            <div>
                                {{ $paciente->correo ?: 'No registrado' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <span class="detail-label">
                                Fecha de nacimiento
                            </span>

                            <div>

                                @if ($paciente->fecha_nacimiento)
                                    {{ \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }}
                                @else
                                    No registrada
                                @endif

                            </div>

                        </div>


                        <div class="col-md-6">

                            <span class="detail-label">
                                Sexo
                            </span>

                            <div>
                                {{ $paciente->sexo ? str_replace('_', ' ', $paciente->sexo) : 'No indicado' }}
                            </div>

                        </div>


                        <div class="col-12">

                            <span class="detail-label">
                                Dirección
                            </span>

                            <div>
                                {{ $paciente->direccion ?: 'No registrada' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-lg-4">

            <div class="card bw-card h-100">

                <div class="card-header">

                    <h2 class="card-title-sm mb-0">
                        Estado del registro
                    </h2>

                </div>


                <div class="card-body">

                    <div class="mb-4">

                        <span class="detail-label">
                            Estado
                        </span>

                        <div class="mt-1">

                            @if ($paciente->activo)
                                <span class="badge-status status-confirmada">
                                    ACTIVO
                                </span>
                            @else
                                <span class="badge-status status-cancelada">
                                    INACTIVO
                                </span>
                            @endif

                        </div>

                    </div>


                    <div class="mb-4">

                        <span class="detail-label">
                            Registrado por
                        </span>

                        <div>

                            @if ($paciente->usuarioRegistro)
                                {{ $paciente->usuarioRegistro->nombres }}
                                {{ $paciente->usuarioRegistro->apellidos }}
                            @else
                                No disponible
                            @endif

                        </div>

                    </div>


                    <div>

                        <span class="detail-label">
                            Fecha de registro
                        </span>

                        <div>

                            @if ($paciente->fecha_registro)
                                {{ \Illuminate\Support\Carbon::parse($paciente->fecha_registro)->format('d/m/Y H:i') }}
                            @else
                                No disponible
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- HISTORIAL DE CITAS --}}
    <div class="card bw-card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <div>

                <h2 class="card-title-sm mb-1">
                    Historial de citas
                </h2>

                <div class="text-muted small">

                    {{ $paciente->citas->count() }}

                    {{ $paciente->citas->count() === 1 ? 'cita registrada' : 'citas registradas' }}

                </div>

            </div>

        </div>


        {{-- ESCRITORIO / TABLET --}}
        <div class="table-responsive d-none d-md-block">

            <table class="table align-middle mb-0">

                <thead>

                    <tr>
                        <th>Fecha y horario</th>
                        <th>Servicio</th>
                        <th>Especialista</th>
                        <th>Estado</th>
                        <th class="text-end">Acción</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse ($paciente->citas as $cita)

                        @php
                            $codigoEstado = $cita->estado?->codigo ?? 'PENDIENTE';

                            $claseEstado = match ($codigoEstado) {
                                'CONFIRMADA' => 'confirmada',
                                'COMPLETADA' => 'completada',
                                'CANCELADA' => 'cancelada',
                                'NO_ASISTIO' => 'cancelada',
                                default => 'pendiente',
                            };
                        @endphp


                        <tr>

                            <td class="text-nowrap">

                                <strong>
                                    {{ \Illuminate\Support\Carbon::parse($cita->inicio)->format('d/m/Y') }}
                                </strong>

                                <div class="text-muted small">

                                    {{ \Illuminate\Support\Carbon::parse($cita->inicio)->format('H:i') }}

                                    -

                                    {{ \Illuminate\Support\Carbon::parse($cita->fin)->format('H:i') }}

                                </div>

                            </td>


                            <td>

                                <span class="service-chip">

                                    <i class="bi bi-flower1"></i>

                                    {{ $cita->servicio?->nombre ?? 'Sin servicio' }}

                                </span>

                            </td>


                            <td>

                                @if ($cita->especialista?->usuario)
                                    {{ $cita->especialista->usuario->nombres }}
                                    {{ $cita->especialista->usuario->apellidos }}
                                @else
                                    Sin especialista
                                @endif

                            </td>


                            <td class="text-nowrap">

                                <span
                                    class="badge-status
                                           status-{{ $claseEstado }}">
                                    {{ str_replace('_', ' ', $codigoEstado) }}
                                </span>

                            </td>


                            <td class="text-end">

                                <div
                                    class="d-flex
                                           justify-content-end
                                           flex-wrap
                                           gap-2">

                                    @can('citas.ver')
                                        <a href="{{ route('citas.show', $cita->id_cita) }}?origen=paciente&paciente={{ $paciente->id_paciente }}"
                                            class="btn btn-sm btn-outline-brand" title="Ver cita">
                                            <i class="bi bi-eye me-1"></i>
                                            Ver cita
                                        </a>
                                    @endcan


                                    @can('consultas.ver')
                                        @if ($cita->consulta)
                                            <a href="{{ route('consultas.show', $cita->consulta->id_consulta) }}?origen=paciente&paciente={{ $paciente->id_paciente }}"
                                                class="btn btn-sm btn-brand" title="Ver consulta">
                                                <i class="bi bi-journal-medical me-1"></i>
                                                Ver consulta
                                            </a>
                                        @endif
                                    @endcan

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="empty-state">
                                    <i class="bi bi-calendar2"></i>
                                    Este paciente todavía no tiene citas registradas.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- MÓVIL --}}
        <div class="d-md-none p-3">

            @forelse ($paciente->citas as $cita)

                @php
                    $codigoEstado = $cita->estado?->codigo ?? 'PENDIENTE';

                    $claseEstado = match ($codigoEstado) {
                        'CONFIRMADA' => 'confirmada',
                        'COMPLETADA' => 'completada',
                        'CANCELADA' => 'cancelada',
                        'NO_ASISTIO' => 'cancelada',
                        default => 'pendiente',
                    };
                @endphp


                <div class="card bw-card mb-3">

                    <div class="card-body">

                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start
                                   gap-3
                                   mb-3">

                            <div>

                                <strong class="d-block">

                                    {{ \Illuminate\Support\Carbon::parse($cita->inicio)->format('d/m/Y') }}

                                </strong>

                                <span class="text-muted small text-nowrap">

                                    {{ \Illuminate\Support\Carbon::parse($cita->inicio)->format('H:i') }}

                                    -

                                    {{ \Illuminate\Support\Carbon::parse($cita->fin)->format('H:i') }}

                                </span>

                            </div>


                            <span
                                class="badge-status
                                       status-{{ $claseEstado }}
                                       text-nowrap">
                                {{ str_replace('_', ' ', $codigoEstado) }}
                            </span>

                        </div>


                        <div class="mb-3">

                            <span class="detail-label">
                                Servicio
                            </span>

                            <div class="mt-1">

                                <span class="service-chip">

                                    <i class="bi bi-flower1"></i>

                                    {{ $cita->servicio?->nombre ?? 'Sin servicio' }}

                                </span>

                            </div>

                        </div>


                        <div class="mb-3">

                            <span class="detail-label">
                                Especialista
                            </span>

                            <div class="mt-1">

                                @if ($cita->especialista?->usuario)
                                    {{ $cita->especialista->usuario->nombres }}
                                    {{ $cita->especialista->usuario->apellidos }}
                                @else
                                    Sin especialista
                                @endif

                            </div>

                        </div>


                        @canany(['citas.ver', 'consultas.ver'])
                            <div
                                class="d-flex
                                       flex-wrap
                                       gap-2
                                       pt-3
                                       border-top">

                                @can('citas.ver')
                                    <a href="{{ route('citas.show', $cita->id_cita) }}?origen=paciente&paciente={{ $paciente->id_paciente }}"
                                        class="btn btn-sm btn-outline-brand">
                                        <i class="bi bi-eye me-1"></i>
                                        Ver cita
                                    </a>
                                @endcan

                                @can('consultas.ver')
                                    @if ($cita->consulta)
                                        <a href="{{ route('consultas.show', $cita->consulta->id_consulta) }}?origen=paciente&paciente={{ $paciente->id_paciente }}"
                                            class="btn btn-sm btn-brand">
                                            <i class="bi bi-journal-medical me-1"></i>
                                            Ver consulta
                                        </a>
                                    @endif
                                @endcan

                            </div>
                        @endcanany

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <i class="bi bi-calendar2"></i>

                    Este paciente todavía no tiene citas registradas.

                </div>

            @endforelse

        </div>

    </div>

@endsection
