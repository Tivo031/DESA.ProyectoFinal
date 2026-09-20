@extends('layouts.admin')

@section('title', 'Procesar solicitud')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Procesar solicitud</h1>
        <p class="page-subtitle">
            Revisa la solicitud y conviértela en una cita.
        </p>
    </div>

    <a
        href="{{ route('solicitudes-cita.index') }}"
        class="btn btn-light border"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<div class="row g-4">

    <div class="col-12 col-lg-4">

        <div class="card bw-card">

            <div class="card-header">
                <h2 class="card-title-sm mb-0">
                    Solicitud recibida
                </h2>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <span class="detail-label">Solicitante</span>
                    <div class="fw-semibold">
                        {{ $solicitud->nombre }}
                    </div>
                    <div class="text-muted">
                        {{ $solicitud->telefono }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="detail-label">Servicio</span>

                    <div class="mt-1">
                        <span class="service-chip">
                            <i class="bi bi-flower1"></i>
                            {{ $solicitud->servicio?->nombre }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="detail-label">Fecha solicitada</span>

                    <div class="fw-semibold">
                        {{ $solicitud->fecha->format('d/m/Y') }}
                    </div>

                    <div class="text-muted">
                        {{ substr($solicitud->hora, 0, 5) }}
                    </div>
                </div>

                <div>
                    <span class="detail-label">Comentario</span>

                    <p class="mb-0">
                        {{ $solicitud->comentario ?: 'Sin comentario.' }}
                    </p>
                </div>

            </div>

        </div>

    </div>


    <div class="col-12 col-lg-8">

        <div class="card bw-card">

            <div class="card-header">
                <h2 class="card-title-sm mb-0">
                    Crear cita
                </h2>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route(
                        'solicitudes-cita.aprobar',
                        $solicitud->id_solicitud
                    ) }}"
                >
                    @csrf

                    <div class="mb-4">

                        <label class="form-label">
                            Paciente
                        </label>

                        <div class="d-flex gap-4">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="tipo_paciente"
                                    id="pacienteExistente"
                                    value="existente"
                                    @checked(
                                        old(
                                            'tipo_paciente',
                                            $pacienteSugerido
                                                ? 'existente'
                                                : 'nuevo'
                                        ) === 'existente'
                                    )
                                >

                                <label
                                    class="form-check-label"
                                    for="pacienteExistente"
                                >
                                    Paciente existente
                                </label>

                            </div>

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="tipo_paciente"
                                    id="pacienteNuevo"
                                    value="nuevo"
                                    @checked(
                                        old(
                                            'tipo_paciente',
                                            $pacienteSugerido
                                                ? 'existente'
                                                : 'nuevo'
                                        ) === 'nuevo'
                                    )
                                >

                                <label
                                    class="form-check-label"
                                    for="pacienteNuevo"
                                >
                                    Nuevo paciente
                                </label>

                            </div>

                        </div>

                    </div>


                    <div id="bloquePacienteExistente" class="mb-4">

                        <label
                            class="form-label"
                            for="id_paciente"
                        >
                            Seleccionar paciente
                        </label>

                        <select
                            class="form-select @error('id_paciente') is-invalid @enderror"
                            id="id_paciente"
                            name="id_paciente"
                        >

                            <option value="">
                                Selecciona un paciente
                            </option>

                            @foreach ($pacientes as $paciente)

                                <option
                                    value="{{ $paciente->id_paciente }}"
                                    @selected(
                                        old(
                                            'id_paciente',
                                            $pacienteSugerido?->id_paciente
                                        )
                                        ==
                                        $paciente->id_paciente
                                    )
                                >
                                    {{ $paciente->nombres }}
                                    {{ $paciente->apellidos }}
                                    ·
                                    {{ $paciente->telefono }}
                                </option>

                            @endforeach

                        </select>

                        @error('id_paciente')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div id="bloquePacienteNuevo" class="mb-4">

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    class="form-label"
                                    for="nombres"
                                >
                                    Nombres
                                </label>

                                <input
                                    class="form-control @error('nombres') is-invalid @enderror"
                                    id="nombres"
                                    name="nombres"
                                    value="{{ old('nombres', $nombresSugeridos) }}"
                                >

                                @error('nombres')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="col-md-6">

                                <label
                                    class="form-label"
                                    for="apellidos"
                                >
                                    Apellidos
                                </label>

                                <input
                                    class="form-control @error('apellidos') is-invalid @enderror"
                                    id="apellidos"
                                    name="apellidos"
                                    value="{{ old('apellidos', $apellidosSugeridos) }}"
                                >

                                @error('apellidos')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="col-md-6">

                                <label
                                    class="form-label"
                                    for="telefono"
                                >
                                    Teléfono
                                </label>

                                <input
                                    class="form-control"
                                    id="telefono"
                                    name="telefono"
                                    value="{{ old(
                                        'telefono',
                                        $solicitud->telefono
                                    ) }}"
                                >

                            </div>

                        </div>

                    </div>


                    <div class="row g-3">

                        <div class="col-md-6">

                            <label
                                class="form-label"
                                for="id_especialista"
                            >
                                Especialista
                            </label>

                            <select
                                class="form-select @error('id_especialista') is-invalid @enderror"
                                id="id_especialista"
                                name="id_especialista"
                                required
                            >

                                <option value="">
                                    Selecciona un especialista
                                </option>

                                @foreach ($especialistas as $especialista)

                                    <option
                                        value="{{ $especialista->id_especialista }}"
                                        @selected(
                                            old('id_especialista')
                                            ==
                                            $especialista->id_especialista
                                        )
                                    >
                                        {{ $especialista->usuario?->nombres }}
                                        {{ $especialista->usuario?->apellidos }}
                                        ·
                                        {{ $especialista->profesion }}
                                    </option>

                                @endforeach

                            </select>

                            @error('id_especialista')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-3">

                            <label
                                class="form-label"
                                for="fecha"
                            >
                                Fecha
                            </label>

                            <input
                                type="date"
                                class="form-control @error('fecha') is-invalid @enderror"
                                id="fecha"
                                name="fecha"
                                value="{{ old(
                                    'fecha',
                                    $solicitud->fecha->format('Y-m-d')
                                ) }}"
                                min="{{ now()->toDateString() }}"
                                required
                            >

                            @error('fecha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-3">

                            <label
                                class="form-label"
                                for="hora"
                            >
                                Hora
                            </label>

                            <select
                                class="form-select @error('hora') is-invalid @enderror"
                                id="hora"
                                name="hora"
                                required
                            >

                                @foreach ([
                                    '08:00',
                                    '08:30',
                                    '09:00',
                                    '09:30',
                                    '10:00',
                                    '10:30',
                                    '11:00',
                                    '11:30',
                                    '12:00',
                                    '12:30',
                                    '13:00',
                                    '13:30',
                                    '14:00',
                                    '14:30',
                                    '15:00',
                                    '15:30',
                                    '16:00',
                                    '16:30',
                                    '17:00',
                                    '17:30'
                                ] as $hora)

                                    <option
                                        value="{{ $hora }}"
                                        @selected(
                                            old(
                                                'hora',
                                                substr(
                                                    $solicitud->hora,
                                                    0,
                                                    5
                                                )
                                            ) === $hora
                                        )
                                    >
                                        {{ $hora }}
                                    </option>

                                @endforeach

                            </select>

                            @error('hora')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <div class="alert alert-light border mt-4">

                        <i class="bi bi-clock me-2 text-brand"></i>

                        Duración del servicio:

                        <strong>
                            {{ $solicitud->servicio?->duracion_minutos }}
                            minutos
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mt-4">

                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalRechazar"
                        >
                            <i class="bi bi-x-circle me-2"></i>
                            Rechazar
                        </button>

                        <button
                            type="submit"
                            class="btn btn-brand"
                        >
                            <i class="bi bi-calendar-check me-2"></i>
                            Aprobar y crear cita
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<div
    class="modal fade"
    id="modalRechazar"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Rechazar solicitud
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">
                ¿Deseas rechazar la solicitud de
                <strong>{{ $solicitud->nombre }}</strong>?
            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>

                <form
                    method="POST"
                    action="{{ route(
                        'solicitudes-cita.rechazar',
                        $solicitud->id_solicitud
                    ) }}"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Sí, rechazar
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

@push('scripts')
    <script src="{{ asset(
        'assets/js/citas/solicitud-form.js'
    ) }}"></script>
@endpush

@endsection