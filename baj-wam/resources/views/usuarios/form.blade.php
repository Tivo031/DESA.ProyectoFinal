@php
    $usuario = $usuario ?? null;
    $esEdicion = filled(data_get($usuario, 'id_usuario'));

    $roles = $roles ?? collect([
        ['id_rol' => 1, 'nombre' => 'ADMINISTRADOR'],
        ['id_rol' => 2, 'nombre' => 'RECEPCION'],
        ['id_rol' => 3, 'nombre' => 'ESPECIALISTA'],
        ['id_rol' => 4, 'nombre' => 'INVENTARIO'],
    ]);

    $idUsuario = data_get($usuario, 'id_usuario');
    $activoActual = old('activo', data_get($usuario, 'activo', true));
@endphp

<form method="POST" action="{{ $esEdicion ? url('/usuarios/'.$idUsuario) : url('/usuarios') }}">
    @csrf
    @if ($esEdicion)
        @method('PUT')
    @endif

    <div class="card bw-card mb-4">
        <div class="card-header form-section-header">
            <span class="form-section-icon bg-soft-purple text-brand"><i class="bi bi-person-vcard"></i></span>
            <div>
                <h2 class="form-section-title">Datos personales</h2>
                <p class="form-section-copy">Información básica de la persona que utilizará el sistema.</p>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nombres">Nombres <span class="text-danger">*</span></label>
                    <input
                        class="form-control @error('nombres') is-invalid @enderror"
                        id="nombres"
                        name="nombres"
                        type="text"
                        maxlength="80"
                        value="{{ old('nombres', data_get($usuario, 'nombres')) }}"
                        autocomplete="given-name"
                        required
                    >
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="apellidos">Apellidos <span class="text-danger">*</span></label>
                    <input
                        class="form-control @error('apellidos') is-invalid @enderror"
                        id="apellidos"
                        name="apellidos"
                        type="text"
                        maxlength="80"
                        value="{{ old('apellidos', data_get($usuario, 'apellidos')) }}"
                        autocomplete="family-name"
                        required
                    >
                    @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="correo">Correo electrónico <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                        <input
                            class="form-control @error('correo') is-invalid @enderror"
                            id="correo"
                            name="correo"
                            type="email"
                            maxlength="120"
                            value="{{ old('correo', data_get($usuario, 'correo')) }}"
                            autocomplete="email"
                            required
                        >
                        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                        <input
                            class="form-control @error('telefono') is-invalid @enderror"
                            id="telefono"
                            name="telefono"
                            type="tel"
                            maxlength="20"
                            value="{{ old('telefono', data_get($usuario, 'telefono')) }}"
                            autocomplete="tel"
                            placeholder="Ej. 5555-5555"
                        >
                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bw-card mb-4">
        <div class="card-header form-section-header">
            <span class="form-section-icon bg-soft-green text-green-bw"><i class="bi bi-shield-lock"></i></span>
            <div>
                <h2 class="form-section-title">Acceso y permisos</h2>
                <p class="form-section-copy">Credenciales, rol y estado de la cuenta.</p>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="usuario">Nombre de usuario <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">@</span>
                        <input
                            class="form-control @error('usuario') is-invalid @enderror"
                            id="usuario"
                            name="usuario"
                            type="text"
                            maxlength="50"
                            value="{{ old('usuario', data_get($usuario, 'usuario')) }}"
                            autocomplete="username"
                            required
                        >
                        @error('usuario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text">Usa letras, números, puntos o guiones bajos.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="id_rol">Rol <span class="text-danger">*</span></label>
                    <select class="form-select @error('id_rol') is-invalid @enderror" id="id_rol" name="id_rol" required>
                        <option value="">Seleccionar rol</option>
                        @foreach ($roles as $rol)
                            <option
                                value="{{ data_get($rol, 'id_rol') }}"
                                @selected((string) old('id_rol', data_get($usuario, 'id_rol')) === (string) data_get($rol, 'id_rol'))
                            >
                                {{ data_get($rol, 'nombre') }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="password">
                        Contraseña @if (!$esEdicion)<span class="text-danger">*</span>@endif
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                        <input
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            type="password"
                            minlength="8"
                            autocomplete="new-password"
                            @if (!$esEdicion) required @endif
                        >
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text">
                        {{ $esEdicion ? 'Déjala vacía para conservar la contraseña actual.' : 'Utiliza como mínimo 8 caracteres.' }}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="password_confirmation">Confirmar contraseña @if (!$esEdicion)<span class="text-danger">*</span>@endif</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-key-fill"></i></span>
                        <input
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            minlength="8"
                            autocomplete="new-password"
                            @if (!$esEdicion) required @endif
                        >
                    </div>
                </div>

                <div class="col-12">
                    <div class="status-switch-panel">
                        <div>
                            <div class="fw-bold">Cuenta activa</div>
                            <div class="text-secondary small">Al desactivarla, el usuario ya no podrá iniciar sesión.</div>
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
                            <label class="visually-hidden" for="activo">Cuenta activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-action-bar">
        <div class="text-secondary small">
            <i class="bi bi-info-circle me-1"></i>Los campos marcados con <span class="text-danger">*</span> son obligatorios.
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light border" href="{{ url('/usuarios') }}">Cancelar</a>
            <button class="btn btn-brand" type="submit">
                <i class="bi bi-floppy-fill me-2"></i>{{ $esEdicion ? 'Guardar cambios' : 'Crear usuario' }}
            </button>
        </div>
    </div>
</form>
