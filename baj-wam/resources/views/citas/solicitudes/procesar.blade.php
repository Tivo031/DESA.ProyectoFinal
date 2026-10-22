@extends('layouts.admin')

@section('title', 'Procesar solicitud')

@section('content')
    @php
        $bloqueadoPorPacienteInactivo =
            ($pacientePorDpi && !$pacientePorDpi->activo) ||
            ($pacientePorExpediente && !$pacientePorExpediente->activo);
        $solicitudConIdentificadoresContradictorios = $identificadoresContradictorios;
        $solicitudBloqueada = $bloqueadoPorPacienteInactivo;
        $tipoPorDefecto =
            $pacienteSeleccionado?->activo ||
            $solicitudConIdentificadoresContradictorios ||
            $coincidencias->isNotEmpty()
                ? 'existente'
                : 'nuevo';
    @endphp

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Solicitudes de cita</div>
            <h1 class="page-title">Procesar solicitud</h1>
            <p class="page-subtitle">Confirma la identidad del paciente antes de crear la cita.</p>
        </div>

        <a href="{{ route('solicitudes-cita.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-2"></i>Regresar
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card bw-card">
                <div class="card-header">
                    <h2 class="card-title-sm mb-0">Solicitud recibida</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="detail-label">Solicitante</span>
                        <div class="fw-semibold">{{ $solicitud->nombre }}</div>
                        <div class="text-muted">{{ $solicitud->telefono }}</div>
                    </div>

                    <div class="mb-3">
                        <span class="detail-label">DPI</span>
                        <div>{{ $solicitud->dpi ?: 'No proporcionado' }}</div>
                    </div>

                    <div class="mb-3">
                        <span class="detail-label">Número de expediente</span>
                        <div>{{ $solicitud->numero_expediente ?: 'No proporcionado' }}</div>
                    </div>

                    <div class="mb-3">
                        <span class="detail-label">Servicio</span>
                        <div class="mt-1"><span class="service-chip"><i
                                    class="bi bi-flower1"></i>{{ $solicitud->servicio?->nombre }}</span></div>
                    </div>

                    <div class="mb-3">
                        <span class="detail-label">Fecha solicitada</span>
                        <div class="fw-semibold">{{ $solicitud->fecha->format('d/m/Y') }}</div>
                        <div class="text-muted">{{ substr($solicitud->hora, 0, 5) }}</div>
                    </div>

                    <div>
                        <span class="detail-label">Comentario</span>
                        <p class="mb-0">{{ $solicitud->comentario ?: 'Sin comentario.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card bw-card">
                <div class="card-header">
                    <h2 class="card-title-sm mb-0">Crear cita</h2>
                </div>
                <div class="card-body">
                    @if ($solicitudBloqueada)
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            El identificador proporcionado corresponde a un expediente inactivo. Debe reactivarse mediante
                            el procedimiento autorizado antes de asignarle una cita. No se creará un expediente nuevo para
                            evitar esta restricción.
                        </div>
                        <form method="POST" action="{{ route('solicitudes-cita.rechazar', $solicitud->id_solicitud) }}"
                            class="mt-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger"
                                data-confirm-delete="La solicitud será rechazada. ¿Deseas continuar?">
                                <i class="bi bi-x-circle me-2"></i>Rechazar solicitud
                            </button>
                        </form>
                    @else
                        @if ($solicitudConIdentificadoresContradictorios)
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                El DPI y el número de expediente identifican pacientes distintos. Verifica la identidad,
                                selecciona uno de esos expedientes y documenta la resolución antes de aprobar.
                            </div>
                        @elseif ($pacienteIdentificado)
                            <div class="alert alert-success">
                                <i class="bi bi-person-check-fill me-2"></i>

                                Se encontró una coincidencia exacta por

                                @if ($pacientePorDpi)
                                    DPI: <strong>{{ $pacienteIdentificado->dpi }}</strong>
                                @else
                                    número de expediente:
                                    <strong>{{ $pacienteIdentificado->numero_expediente }}</strong>.
                                @endif

                                Verifica la identidad antes de continuar.
                            </div>
                        @elseif ($coincidencias->isNotEmpty())
                            <div class="alert alert-light border">
                                <i class="bi bi-info-circle me-2"></i>

                                Se encontraron
                                <strong>{{ $coincidencias->count() }}</strong>
                                posibles coincidencias por nombre o teléfono.

                                Teléfono proporcionado:
                                <strong>{{ $solicitud->telefono }}</strong>.

                                Revisa los expedientes antes de seleccionar un paciente.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('solicitudes-cita.rechazar', $solicitud->id_solicitud) }}"
                            class="mb-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger"
                                data-confirm-delete="La solicitud será rechazada. ¿Deseas continuar?">
                                <i class="bi bi-x-circle me-2"></i>Rechazar solicitud
                            </button>
                        </form>

                        <form method="POST" action="{{ route('solicitudes-cita.aprobar', $solicitud->id_solicitud) }}">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">Paciente</label>
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_paciente"
                                            id="pacienteExistente" value="existente" @checked(old('tipo_paciente', $tipoPorDefecto) === 'existente')>
                                        <label class="form-check-label" for="pacienteExistente">Paciente existente</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_paciente"
                                            id="pacienteNuevo" value="nuevo" @checked(old('tipo_paciente', $tipoPorDefecto) === 'nuevo')>
                                        <label class="form-check-label" for="pacienteNuevo">Nuevo paciente</label>
                                    </div>
                                </div>
                            </div>

                            <div id="bloquePacienteExistente" class="mb-4">
                                <label class="form-label" for="buscarPaciente">Buscar paciente</label>
                                <input class="form-control mb-2" id="buscarPaciente" type="search" autocomplete="off"
                                    data-url="{{ route('pacientes.buscar') }}"
                                    placeholder="Expediente, DPI exacto, nombre, apellido o teléfono">
                                <div class="form-text mb-2">La búsqueda es interna, limitada y no muestra información
                                    clínica.</div>

                                <select class="form-select @error('id_paciente') is-invalid @enderror" id="id_paciente"
                                    name="id_paciente">
                                    <option value="">Selecciona un paciente</option>
                                    @if ($pacienteSeleccionado)
                                        <option value="{{ $pacienteSeleccionado->id_paciente }}"
                                            data-activo="{{ $pacienteSeleccionado->activo ? '1' : '0' }}"
                                            @selected(old('id_paciente', $pacienteIdentificado?->id_paciente) == $pacienteSeleccionado->id_paciente)>
                                            {{ $pacienteSeleccionado->numero_expediente }}
                                            · {{ $pacienteSeleccionado->nombre_completo }}
                                            · {{ $pacienteSeleccionado->dpi ?: 'Sin DPI' }}
                                            · Tel: {{ $pacienteSeleccionado->telefono }}
                                            {{ $pacienteSeleccionado->activo ? '' : '· INACTIVO' }}
                                        </option>
                                    @endif
                                    @foreach ($coincidencias as $paciente)
                                        @if (!$pacienteSeleccionado || $paciente->id_paciente !== $pacienteSeleccionado->id_paciente)
                                            <option value="{{ $paciente->id_paciente }}"
                                                data-activo="{{ $paciente->activo ? '1' : '0' }}">
                                                {{ $paciente->numero_expediente }}
                                                · {{ $paciente->nombre_completo }}
                                                · {{ $paciente->dpi ?: 'Sin DPI' }}
                                                · Tel: {{ $paciente->telefono }}
                                                {{ $paciente->activo ? '' : '· INACTIVO' }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('id_paciente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($pacienteIdentificado)
                                <div class="form-check mb-4">
                                    <input class="form-check-input @error('confirmar_identidad') is-invalid @enderror"
                                        type="checkbox" value="1" id="confirmar_identidad"
                                        name="confirmar_identidad" @checked(old('confirmar_identidad'))>
                                    <label class="form-check-label" for="confirmar_identidad">Confirmo que verifiqué la
                                        identidad y que el expediente seleccionado corresponde a la persona
                                        solicitante.</label>
                                    @error('confirmar_identidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            @if ($solicitudConIdentificadoresContradictorios)
                                <div class="form-check mb-4">
                                    <input class="form-check-input @error('identificacion') is-invalid @enderror"
                                        type="checkbox" value="1" id="resolver_identificadores"
                                        name="resolver_identificadores" @checked(old('resolver_identificadores'))>
                                    <label class="form-check-label" for="resolver_identificadores">Confirmo que revisé la
                                        discrepancia de identidad y seleccioné el expediente correcto mediante el
                                        procedimiento autorizado.</label>
                                    @error('identificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div id="bloquePacienteNuevo" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nombres">Nombres</label>
                                        <input class="form-control @error('nombres') is-invalid @enderror" id="nombres"
                                            name="nombres" maxlength="80"
                                            value="{{ old('nombres', $nombresSugeridos) }}">
                                        @error('nombres')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="apellidos">Apellidos</label>
                                        <input class="form-control @error('apellidos') is-invalid @enderror"
                                            id="apellidos" name="apellidos" maxlength="80"
                                            value="{{ old('apellidos', $apellidosSugeridos) }}">
                                        @error('apellidos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="dpi">DPI <span
                                                class="text-muted small">(opcional)</span></label>
                                        <input class="form-control @error('dpi') is-invalid @enderror" id="dpi"
                                            name="dpi" inputmode="numeric" maxlength="13"
                                            value="{{ old('dpi', $solicitud->dpi) }}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        @error('dpi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="telefono">Teléfono</label>
                                        <input class="form-control @error('telefono') is-invalid @enderror" type="text"
                                            id="telefono" name="telefono"
                                            value="{{ old('telefono', $solicitud->telefono) }}" inputmode="numeric"
                                            minlength="9" maxlength="9" pattern="[0-9]{4}-[0-9]{4}"
                                            data-formato-telefono placeholder="Ej. 5555-5555">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="id_especialista">Especialista</label>
                                    <select class="form-select @error('id_especialista') is-invalid @enderror"
                                        id="id_especialista" name="id_especialista" data-buscador placeholder="Selecciona un especialista" required>
                                        <option value="" disabled>Selecciona un especialista</option>
                                        @foreach ($especialistas as $especialista)
                                            <option value="{{ $especialista->id_especialista }}"
                                                @selected(old('id_especialista') == $especialista->id_especialista)>
                                                {{ $especialista->usuario?->nombres }}
                                                {{ $especialista->usuario?->apellidos }} · {{ $especialista->profesion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_especialista')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                        id="fecha" name="fecha"
                                        value="{{ old('fecha', $solicitud->fecha->format('Y-m-d')) }}"
                                        min="{{ now()->toDateString() }}" required>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="hora">Hora</label>
                                    <select class="form-select @error('hora') is-invalid @enderror" id="hora"
                                        name="hora" required>
                                        @foreach (['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'] as $hora)
                                            <option value="{{ $hora }}" @selected(old('hora', substr($solicitud->hora, 0, 5)) === $hora)>
                                                {{ $hora }}</option>
                                        @endforeach
                                    </select>
                                    @error('hora')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-light border mt-4">
                                <i class="bi bi-clock me-2 text-brand"></i>Duración del servicio:
                                <strong>{{ $solicitud->servicio?->duracion_minutos }} minutos</strong>
                            </div>

                            @error('solicitud')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-brand"><i
                                        class="bi bi-calendar-check me-2"></i>Aprobar y crear cita</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/citas/solicitud-form.js') }}"></script>
    @endpush
@endsection
