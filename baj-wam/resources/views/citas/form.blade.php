@php
    $cita = $cita ?? null;
    $esEdicion = filled(data_get($cita, 'id_cita'));
    $idCita = data_get($cita, 'id_cita');

    $pacientes = $pacientes ?? collect([
        ['id_paciente' => 1, 'nombres' => 'María Fernanda', 'apellidos' => 'López Castillo', 'telefono' => '5555-2100'],
        ['id_paciente' => 2, 'nombres' => 'Carlos Estuardo', 'apellidos' => 'Méndez López', 'telefono' => '5555-3344'],
        ['id_paciente' => 3, 'nombres' => 'Andrea Lucía', 'apellidos' => 'Morales Díaz', 'telefono' => '5555-7721'],
    ]);

    $servicios = $servicios ?? collect([
        ['id_servicio' => 1, 'nombre' => 'Acupuntura', 'duracion_minutos' => 60],
        ['id_servicio' => 2, 'nombre' => 'Acupuntura láser', 'duracion_minutos' => 45],
        ['id_servicio' => 3, 'nombre' => 'Quiropráctico', 'duracion_minutos' => 60],
        ['id_servicio' => 4, 'nombre' => 'Terapia nutricional', 'duracion_minutos' => 60],
    ]);

    $especialistas = $especialistas ?? collect([
        ['id_especialista' => 1, 'nombre_completo' => 'Dra. Ana Ruiz', 'profesion' => 'Acupunturista'],
        ['id_especialista' => 2, 'nombre_completo' => 'Dr. Luis García', 'profesion' => 'Quiropráctico'],
        ['id_especialista' => 3, 'nombre_completo' => 'Lic. Sofía Pérez', 'profesion' => 'Nutricionista'],
    ]);

    $estados = $estados ?? collect([
        ['id_estado_cita' => 1, 'codigo' => 'PENDIENTE', 'nombre' => 'Pendiente'],
        ['id_estado_cita' => 2, 'codigo' => 'CONFIRMADA', 'nombre' => 'Confirmada'],
        ['id_estado_cita' => 3, 'codigo' => 'COMPLETADA', 'nombre' => 'Completada'],
        ['id_estado_cita' => 4, 'codigo' => 'CANCELADA', 'nombre' => 'Cancelada'],
        ['id_estado_cita' => 5, 'codigo' => 'NO_ASISTIO', 'nombre' => 'No asistió'],
    ]);

    $inicio = data_get($cita, 'inicio');
    $fin = data_get($cita, 'fin');
    $fechaCita = old('fecha_cita');
    $horaInicio = old('hora_inicio');
    $horaFin = old('hora_fin');

    if (!$fechaCita && $inicio) {
        try { $fechaCita = \Illuminate\Support\Carbon::parse($inicio)->format('Y-m-d'); } catch (\Throwable $e) {}
    }
    if (!$horaInicio && $inicio) {
        try { $horaInicio = \Illuminate\Support\Carbon::parse($inicio)->format('H:i'); } catch (\Throwable $e) {}
    }
    if (!$horaFin && $fin) {
        try { $horaFin = \Illuminate\Support\Carbon::parse($fin)->format('H:i'); } catch (\Throwable $e) {}
    }

    $idEstadoActual = old('id_estado_cita', data_get($cita, 'id_estado_cita', 1));
@endphp

<form method="POST" action="{{ $esEdicion ? url('/citas/'.$idCita) : url('/citas') }}">
    @csrf
    @if ($esEdicion)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-purple text-brand"><i class="bi bi-person-check-fill"></i></span>
                    <div>
                        <h2 class="form-section-title">Paciente y servicio</h2>
                        <p class="form-section-copy">Selecciona quién será atendido y el tipo de servicio solicitado.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="id_paciente">Paciente <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_paciente') is-invalid @enderror" id="id_paciente" name="id_paciente" required>
                                <option value="">Buscar o seleccionar paciente</option>
                                @foreach ($pacientes as $paciente)
                                    <option value="{{ data_get($paciente, 'id_paciente') }}" @selected((string) old('id_paciente', data_get($cita, 'id_paciente')) === (string) data_get($paciente, 'id_paciente'))>
                                        {{ trim(data_get($paciente, 'nombres').' '.data_get($paciente, 'apellidos')) }} · {{ data_get($paciente, 'telefono') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_paciente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Si el paciente aún no existe, regístralo primero desde el módulo Pacientes.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="id_servicio">Servicio <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_servicio') is-invalid @enderror" id="id_servicio" name="id_servicio" required>
                                <option value="">Seleccionar servicio</option>
                                @foreach ($servicios as $servicio)
                                    <option value="{{ data_get($servicio, 'id_servicio') }}" data-duration="{{ data_get($servicio, 'duracion_minutos') }}" @selected((string) old('id_servicio', data_get($cita, 'id_servicio')) === (string) data_get($servicio, 'id_servicio'))>
                                        {{ data_get($servicio, 'nombre') }} · {{ data_get($servicio, 'duracion_minutos') }} min
                                    </option>
                                @endforeach
                            </select>
                            @error('id_servicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="id_especialista">Especialista <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_especialista') is-invalid @enderror" id="id_especialista" name="id_especialista" required>
                                <option value="">Seleccionar especialista</option>
                                @foreach ($especialistas as $especialista)
                                    <option value="{{ data_get($especialista, 'id_especialista') }}" @selected((string) old('id_especialista', data_get($cita, 'id_especialista')) === (string) data_get($especialista, 'id_especialista'))>
                                        {{ data_get($especialista, 'nombre_completo') }} · {{ data_get($especialista, 'profesion') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_especialista')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-green text-green-bw"><i class="bi bi-clock-fill"></i></span>
                    <div>
                        <h2 class="form-section-title">Fecha y horario</h2>
                        <p class="form-section-copy">El sistema validará que el especialista no tenga otra cita durante este intervalo.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="fecha_cita">Fecha <span class="text-danger">*</span></label>
                            <input class="form-control @error('fecha_cita') is-invalid @enderror" id="fecha_cita" name="fecha_cita" type="date" value="{{ $fechaCita }}" required>
                            @error('fecha_cita')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label" for="hora_inicio">Hora de inicio <span class="text-danger">*</span></label>
                            <input class="form-control @error('hora_inicio') is-invalid @enderror" id="hora_inicio" name="hora_inicio" type="time" value="{{ $horaInicio }}" required>
                            @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label" for="hora_fin">Hora de finalización <span class="text-danger">*</span></label>
                            <input class="form-control @error('hora_fin') is-invalid @enderror" id="hora_fin" name="hora_fin" type="time" value="{{ $horaFin }}" required>
                            @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="availability-callout mt-3">
                        <span class="availability-icon"><i class="bi bi-calendar2-check-fill"></i></span>
                        <div>
                            <strong>Validación de disponibilidad</strong>
                            <p class="mb-0">Al guardar, el backend debe comprobar que no exista otra cita activa del mismo especialista que se cruce con este horario.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-plum text-plum-bw"><i class="bi bi-chat-left-text-fill"></i></span>
                    <div>
                        <h2 class="form-section-title">Observaciones</h2>
                        <p class="form-section-copy">Información administrativa necesaria antes de la atención.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <label class="form-label" for="observaciones">Observaciones de la cita</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="4" maxlength="500" placeholder="Indicaciones, motivo general o información relevante">{{ old('observaciones', data_get($cita, 'observaciones')) }}</textarea>
                    @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-lime text-lime-bw"><i class="bi bi-flag-fill"></i></span>
                    <div>
                        <h2 class="form-section-title">Estado de la cita</h2>
                        <p class="form-section-copy">Define en qué etapa se encuentra.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <label class="form-label" for="id_estado_cita">Estado <span class="text-danger">*</span></label>
                    <select class="form-select @error('id_estado_cita') is-invalid @enderror" id="id_estado_cita" name="id_estado_cita" required>
                        @foreach ($estados as $estado)
                            <option value="{{ data_get($estado, 'id_estado_cita') }}" data-code="{{ data_get($estado, 'codigo') }}" @selected((string) $idEstadoActual === (string) data_get($estado, 'id_estado_cita'))>
                                {{ data_get($estado, 'nombre') }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_estado_cita')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div class="mt-3 {{ old('id_estado_cita', data_get($cita, 'id_estado_cita')) == 4 ? '' : 'd-none' }}" id="cancel-reason-wrapper">
                        <label class="form-label" for="motivo_cancelacion">Motivo de cancelación</label>
                        <textarea class="form-control @error('motivo_cancelacion') is-invalid @enderror" id="motivo_cancelacion" name="motivo_cancelacion" rows="3" maxlength="250" placeholder="Explica brevemente el motivo">{{ old('motivo_cancelacion', data_get($cita, 'motivo_cancelacion')) }}</textarea>
                        @error('motivo_cancelacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-purple text-brand"><i class="bi bi-list-check"></i></span>
                    <div>
                        <h2 class="form-section-title">Antes de guardar</h2>
                        <p class="form-section-copy">Revisa estos datos para evitar errores.</p>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="check-list mb-0">
                        <li><i class="bi bi-check-circle-fill"></i>Paciente correcto.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Servicio y especialista compatibles.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Horario dentro de la jornada.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Datos de contacto actualizados.</li>
                    </ul>
                </div>
            </div>

            <div class="info-callout">
                <span class="info-callout-icon"><i class="bi bi-person-lock"></i></span>
                <div class="small">
                    <strong class="d-block mb-1">Registro automático</strong>
                    El usuario responsable se tomará de la sesión iniciada, por lo que no debe seleccionarse manualmente.
                </div>
            </div>
        </div>
    </div>

    <div class="form-action-bar mt-4">
        <div class="text-secondary small"><i class="bi bi-shield-check me-1"></i>Los campos con asterisco son obligatorios.</div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light border" href="{{ $esEdicion ? url('/citas/'.$idCita) : url('/citas') }}">Cancelar</a>
            <button class="btn btn-brand" type="submit"><i class="bi bi-floppy-fill me-2"></i>{{ $esEdicion ? 'Guardar cambios' : 'Registrar cita' }}</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const estado = document.getElementById('id_estado_cita');
    const cancelacion = document.getElementById('cancel-reason-wrapper');
    if (!estado || !cancelacion) return;

    const actualizarCancelacion = function () {
        const opcion = estado.options[estado.selectedIndex];
        cancelacion.classList.toggle('d-none', opcion?.dataset.code !== 'CANCELADA');
    };

    estado.addEventListener('change', actualizarCancelacion);
    actualizarCancelacion();
});
</script>
@endpush
