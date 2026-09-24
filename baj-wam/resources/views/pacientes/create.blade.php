@extends('layouts.admin')

@section('title', 'Nuevo paciente')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Atención clínica</div>
        <h1 class="page-title">Nuevo paciente</h1>
        <p class="page-subtitle">
            Registra la información general y de contacto del paciente.
        </p>
    </div>

    <a href="{{ route('pacientes.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<div class="card bw-card">
    <div class="card-header">
        <h2 class="card-title-sm mb-0">
            Información del paciente
        </h2>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('pacientes.store') }}">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label for="nombres" class="form-label">
                        Nombres
                    </label>

                    <input
                        type="text"
                        id="nombres"
                        name="nombres"
                        class="form-control @error('nombres') is-invalid @enderror"
                        value="{{ old('nombres') }}"
                        maxlength="80"
                        required
                    >

                    @error('nombres')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="apellidos" class="form-label">
                        Apellidos
                    </label>

                    <input
                        type="text"
                        id="apellidos"
                        name="apellidos"
                        class="form-control @error('apellidos') is-invalid @enderror"
                        value="{{ old('apellidos') }}"
                        maxlength="80"
                        required
                    >

                    @error('apellidos')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="dpi" class="form-label">
                        DPI
                    </label>

                    <input
                        type="text"
                        id="dpi"
                        name="dpi"
                        class="form-control @error('dpi') is-invalid @enderror"
                        value="{{ old('dpi') }}"
                        inputmode="numeric"
                        maxlength="13"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        placeholder="13 dígitos"
                    >

                    @error('dpi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="telefono" class="form-label">
                        Teléfono
                    </label>

                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        class="form-control @error('telefono') is-invalid @enderror"
                        value="{{ old('telefono') }}"
                        inputmode="numeric"
                        maxlength="20"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        required
                    >

                    @error('telefono')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="fecha_nacimiento" class="form-label">
                        Fecha de nacimiento
                    </label>

                    <input
                        type="date"
                        id="fecha_nacimiento"
                        name="fecha_nacimiento"
                        class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                        value="{{ old('fecha_nacimiento') }}"
                        max="{{ now()->toDateString() }}"
                    >

                    @error('fecha_nacimiento')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="sexo" class="form-label">
                        Sexo
                    </label>

                    <select
                        id="sexo"
                        name="sexo"
                        class="form-select @error('sexo') is-invalid @enderror"
                    >
                        <option value="">Seleccionar</option>
                        <option value="FEMENINO" @selected(old('sexo') === 'FEMENINO')>
                            Femenino
                        </option>
                        <option value="MASCULINO" @selected(old('sexo') === 'MASCULINO')>
                            Masculino
                        </option>
                        <option value="OTRO" @selected(old('sexo') === 'OTRO')>
                            Otro
                        </option>
                        <option value="NO_INDICA" @selected(old('sexo') === 'NO_INDICA')>
                            Prefiere no indicar
                        </option>
                    </select>

                    @error('sexo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="correo" class="form-label">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        class="form-control @error('correo') is-invalid @enderror"
                        value="{{ old('correo') }}"
                        maxlength="120"
                    >

                    @error('correo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="direccion" class="form-label">
                        Dirección
                    </label>

                    <textarea
                        id="direccion"
                        name="direccion"
                        class="form-control @error('direccion') is-invalid @enderror"
                        rows="3"
                        maxlength="250"
                    >{{ old('direccion') }}</textarea>

                    @error('direccion')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="{{ route('pacientes.index') }}"
                    class="btn btn-light border"
                >
                    Cancelar
                </a>

                <button type="submit" class="btn btn-brand">
                    <i class="bi bi-person-check-fill me-2"></i>
                    Guardar paciente
                </button>

            </div>

        </form>

    </div>
</div>

@endsection