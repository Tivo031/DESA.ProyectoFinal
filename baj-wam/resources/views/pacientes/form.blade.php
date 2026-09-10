@php
    $paciente = $paciente ?? null;
    $esEdicion = filled(data_get($paciente, 'id_paciente'));
    $idPaciente = data_get($paciente, 'id_paciente');
    $activoActual = old('activo', data_get($paciente, 'activo', true));

    $fechaNacimiento = old('fecha_nacimiento', data_get($paciente, 'fecha_nacimiento'));
    if ($fechaNacimiento) {
        try {
            $fechaNacimiento = \Illuminate\Support\Carbon::parse($fechaNacimiento)->format('Y-m-d');
        } catch (\Throwable $e) {
            // Se conserva el valor recibido si no puede convertirse.
        }
    }
@endphp

<form method="POST" action="{{ $esEdicion ? url('/pacientes/'.$idPaciente) : url('/pacientes') }}">
    @csrf
    @if ($esEdicion)
        @method('PUT')
    @endif

    <div class="card bw-card mb-4">
        <div class="card-header form-section-header">
            <span class="form-section-icon bg-soft-purple text-brand"><i class="bi bi-person-vcard"></i></span>
            <div>
                <h2 class="form-section-title">Datos de identificación</h2>
                <p class="form-section-copy">Información general para identificar correctamente al paciente.</p>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="dpi">DPI</label>
                    <input
                        class="form-control @error('dpi') is-invalid @enderror"
                        id="dpi"
                        name="dpi"
                        type="text"
                        inputmode="numeric"
                        maxlength="13"
                        pattern="[0-9]{13}"
                        value="{{ old('dpi', data_get($paciente, 'dpi')) }}"
                        placeholder="13 dígitos"
                    >
                    @error('dpi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Puede dejarse vacío si el paciente no lo proporciona.</div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label" for="nombres">Nombres <span class="text-danger">*</span></label>
                    <input
                        class="form-control @error('nombres') is-invalid @enderror"
                        id="nombres"
                        name="nombres"
                        type="text"
                        maxlength="80"
                        value="{{ old('nombres', data_get($paciente, 'nombres')) }}"
                        autocomplete="given-name"
                        required
                    >
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label" for="apellidos">Apellidos <span class="text-danger">*</span></label>
                    <input
                        class="form-control @error('apellidos') is-invalid @enderror"
                        id="apellidos"
                        name="apellidos"
                        type="text"
                        maxlength="80"
                        value="{{ old('apellidos', data_get($paciente, 'apellidos')) }}"
                        autocomplete="family-name"
                        required
                    >
                    @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
                    <input
                        class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                        id="fecha_nacimiento"
                        name="fecha_nacimiento"
                        type="date"
                        max="{{ now()->format('Y-m-d') }}"
                        value="{{ $fechaNacimiento }}"
                    >
                    @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="sexo">Sexo</label>
                    <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                        <option value="">Seleccionar</option>
                        <option value="FEMENINO" @selected(old('sexo', data_get($paciente, 'sexo')) === 'FEMENINO')>Femenino</option>
                        <option value="MASCULINO" @selected(old('sexo', data_get($paciente, 'sexo')) === 'MASCULINO')>Masculino</option>
                        <option value="OTRO" @selected(old('sexo', data_get($paciente, 'sexo')) === 'OTRO')>Otro</option>
                        <option value="NO_INDICA" @selected(old('sexo', data_get($paciente, 'sexo')) === 'NO_INDICA')>Prefiere no indicar</option>
                    </select>
                    @error('sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card bw-card mb-4">
        <div class="card-header form-section-header">
            <span class="form-section-icon bg-soft-green text-green-bw"><i class="bi bi-telephone-fill"></i></span>
            <div>
                <h2 class="form-section-title">Información de contacto</h2>
                <p class="form-section-copy">Datos necesarios para comunicarse y confirmar sus citas.</p>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="telefono">Teléfono <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                        <input
                            class="form-control @error('telefono') is-invalid @enderror"
                            id="telefono"
                            name="telefono"
                            type="tel"
                            maxlength="20"
                            value="{{ old('telefono', data_get($paciente, 'telefono')) }}"
                            autocomplete="tel"
                            placeholder="Ej. 5555-5555"
                            required
                        >
                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="correo">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                        <input
                            class="form-control @error('correo') is-invalid @enderror"
                            id="correo"
                            name="correo"
                            type="email"
                            maxlength="120"
                            value="{{ old('correo', data_get($paciente, 'correo')) }}"
                            autocomplete="email"
                            placeholder="paciente@correo.com"
                        >
                        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label" for="direccion">Dirección</label>
                    <textarea
                        class="form-control @error('direccion') is-invalid @enderror"
                        id="direccion"
                        name="direccion"
                        rows="3"
                        maxlength="250"
                        autocomplete="street-address"
                        placeholder="Dirección de residencia"
                    >{{ old('direccion', data_get($paciente, 'direccion')) }}</textarea>
                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="status-switch-panel">
                        <div>
                            <div class="fw-bold">Paciente activo</div>
                            <div class="text-secondary small">El historial se conserva aunque el paciente sea desactivado.</div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input type="hidden" name="activo" value="0">
                            <input
                                class="form-check-input"
                                id="activo"
                                name="activo"
                                type="checkbox"
                                role="switch"
                                value="1"
                                @checked((bool) $activoActual)
                            >
                            <label class="visually-hidden" for="activo">Paciente activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-action-bar">
        <div class="text-secondary small">
            <i class="bi bi-shield-check me-1"></i>El usuario que registra al paciente se asigna automáticamente desde la sesión.
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light border" href="{{ url('/pacientes') }}">Cancelar</a>
            <button class="btn btn-brand" type="submit">
                <i class="bi bi-floppy-fill me-2"></i>{{ $esEdicion ? 'Guardar cambios' : 'Registrar paciente' }}
            </button>
        </div>
    </div>
</form>
