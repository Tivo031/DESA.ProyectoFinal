@php
    $rol = $rol ?? null;
    $esEdicion = filled(data_get($rol, 'id_rol'));

    $idRol = data_get($rol, 'id_rol');

    $activoActual = old('activo', data_get($rol, 'activo', true));
@endphp

<form id="formRol" method="POST" action="{{ $esEdicion ? url('/roles/' . $idRol) : url('/roles') }}"
    data-modo="{{ $esEdicion ? 'editar' : 'crear' }}" novalidate>

    @csrf

    @if ($esEdicion)
        @method('PUT')
    @endif

    <div class="card bw-card mb-4">

        <div class="card-header form-section-header">

            <span class="form-section-icon bg-soft-purple text-brand">
                <i class="bi bi-shield-lock"></i>
            </span>

            <div>
                <h2 class="form-section-title">
                    Datos del rol
                </h2>

                <p class="form-section-copy">
                    Define el nombre, descripción y estado del rol.
                </p>
            </div>

        </div>

        <div class="card-body p-4">

            <div class="row g-3">

                <div class="col-12 col-md-6">

                    <label class="form-label" for="nombre">
                        Nombre
                        <span class="text-danger">*</span>
                    </label>

                    <input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
                        type="text" maxlength="50" value="{{ old('nombre', data_get($rol, 'nombre')) }}">

                    <div class="invalid-feedback" id="error-nombre">
                        @error('nombre')
                            {{ $message }}
                        @enderror
                    </div>

                </div>


                <div class="col-12 col-md-6">

                    <label class="form-label" for="descripcion">
                        Descripción
                        <span class="text-danger">*</span>
                    </label>

                    <input class="form-control @error('descripcion') is-invalid @enderror" id="descripcion"
                        name="descripcion" type="text" maxlength="150"
                        value="{{ old('descripcion', data_get($rol, 'descripcion')) }}">

                    <div class="invalid-feedback" id="error-descripcion">
                        @error('descripcion')
                            {{ $message }}
                        @enderror
                    </div>

                </div>


                <div class="col-12">

                    <div class="status-switch-panel">

                        <div>
                            <div class="fw-bold">
                                {{ $activoActual ? 'Rol activo' : 'Rol inactivo' }}
                            </div>

                            <div class="text-secondary small">
                                {{ $activoActual
                                    ? 'El rol puede ser utilizado y asignado a usuarios.'
                                    : 'El rol no podrá ser utilizado para nuevas asignaciones.' }}
                            </div>
                        </div>

                        <div class="form-check form-switch m-0">

                            <input type="hidden" name="activo" value="0">

                            <input class="form-check-input" id="activo" name="activo" type="checkbox" role="switch"
                                value="1" @checked((bool) $activoActual)>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    @can('roles.permisos')

        @php
            $permisosSeleccionados = old(
                'permisos',
                $esEdicion
                    ? data_get($rol, 'permisos', collect())->pluck('id_permiso')->map(fn($id) => (string) $id)->all()
                    : [],
            );
        @endphp

        <div class="card bw-card mb-4">

            <div class="card-header form-section-header">

                <span class="form-section-icon bg-soft-green text-green-bw">
                    <i class="bi bi-shield-check"></i>
                </span>

                <div class="flex-grow-1">
                    <h2 class="form-section-title">
                        Permisos del rol
                    </h2>

                    <p class="form-section-copy">
                        Selecciona las acciones que podrá realizar este rol.
                    </p>
                </div>

                <button type="button" class="btn btn-sm btn-light border" id="seleccionarTodosPermisos">

                    <i class="bi bi-check2-square me-1"></i>
                    Seleccionar todos
                </button>

            </div>

            <div class="card-body p-4">

                @error('permisos')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @enderror

                @error('permisos.*')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @enderror

                <div class="row g-4">

                    @foreach ($permisos as $modulo => $permisosModulo)
                        <div class="col-12 col-lg-6">

                            <div class="border rounded-3 p-3 h-100">

                                <div class="fw-bold mb-3">
                                    {{ $modulo }}
                                </div>

                                <div class="d-flex flex-column gap-2">

                                    @foreach ($permisosModulo as $permiso)
                                        <div class="form-check">

                                            <input class="form-check-input permiso-checkbox" type="checkbox"
                                                name="permisos[]" value="{{ $permiso->id_permiso }}"
                                                id="permiso_{{ $permiso->id_permiso }}" @checked(in_array((string) $permiso->id_permiso, $permisosSeleccionados, true))>

                                            <label class="form-check-label" for="permiso_{{ $permiso->id_permiso }}">

                                                <span class="fw-semibold">
                                                    {{ $permiso->nombre }}
                                                </span>

                                                @if ($permiso->descripcion)
                                                    <div class="text-secondary small">
                                                        {{ $permiso->descripcion }}
                                                    </div>
                                                @endif

                                            </label>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    @endcan


    <div class="form-action-bar">

        <div class="text-secondary small">
            <i class="bi bi-info-circle me-1"></i>

            Los campos marcados con
            <span class="text-danger">*</span>
            son obligatorios.
        </div>

        <div class="d-flex gap-2">

            <a href="{{ url('/roles') }}" class="btn btn-light border">
                Cancelar
            </a>

            <button type="submit" class="btn btn-brand">

                <i class="bi bi-floppy-fill me-2"></i>

                {{ $esEdicion ? 'Guardar cambios' : 'Crear rol' }}
            </button>

        </div>

    </div>

</form>

@push('scripts')
    <script src="{{ asset('assets/js/roles/roles-form.js') }}"></script>
@endpush
