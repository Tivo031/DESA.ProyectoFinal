@php
    $permiso = $permiso ?? null;

    $esEdicion = filled(
        data_get($permiso, 'id_permiso')
    );

    $idPermiso = data_get(
        $permiso,
        'id_permiso'
    );

    $activoActual = old(
        'activo',
        data_get($permiso, 'activo', true)
    );
@endphp


<form id="formPermiso"
    method="POST"
    action="{{ $esEdicion
        ? url('/permisos/' . $idPermiso)
        : url('/permisos') }}"
    data-modo="{{ $esEdicion ? 'editar' : 'crear' }}"
    novalidate>

    @csrf

    @if ($esEdicion)
        @method('PUT')
    @endif


    <div class="card bw-card mb-4">

        <div class="card-header form-section-header">

            <span class="form-section-icon bg-soft-purple text-brand">
                <i class="bi bi-key-fill"></i>
            </span>

            <div>

                <h2 class="form-section-title">
                    Datos del permiso
                </h2>

                <p class="form-section-copy">
                    Configura la identificación y descripción del permiso.
                </p>

            </div>

        </div>


        <div class="card-body p-4">

            <div class="row g-3">


                <div class="col-12 col-md-6">

                    <label class="form-label"
                        for="codigo">

                        Código
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        class="form-control @error('codigo') is-invalid @enderror"
                        id="codigo"
                        name="codigo"
                        type="text"
                        maxlength="100"
                        placeholder="Ej. usuarios.ver"
                        value="{{ old(
                            'codigo',
                            data_get($permiso, 'codigo')
                        ) }}"
                        @disabled($esEdicion)>

                    <div class="invalid-feedback"
                        id="error-codigo">

                        @error('codigo')
                            {{ $message }}
                        @enderror

                    </div>

                    @if ($esEdicion)

                        <div class="form-text">
                            El código no puede modificarse porque se utiliza para controlar el acceso al sistema.
                        </div>

                    @endif

                </div>


                <div class="col-12 col-md-6">

                    <label class="form-label"
                        for="nombre">

                        Nombre
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        class="form-control @error('nombre') is-invalid @enderror"
                        id="nombre"
                        name="nombre"
                        type="text"
                        maxlength="100"
                        placeholder="Ej. Ver usuarios"
                        value="{{ old(
                            'nombre',
                            data_get($permiso, 'nombre')
                        ) }}">

                    <div class="invalid-feedback"
                        id="error-nombre">

                        @error('nombre')
                            {{ $message }}
                        @enderror

                    </div>

                </div>


                <div class="col-12 col-md-6">

                    <label class="form-label"
                        for="modulo">

                        Módulo
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        class="form-control @error('modulo') is-invalid @enderror"
                        id="modulo"
                        name="modulo"
                        type="text"
                        maxlength="50"
                        placeholder="Ej. USUARIOS"
                        value="{{ old(
                            'modulo',
                            data_get($permiso, 'modulo')
                        ) }}">

                    <div class="invalid-feedback"
                        id="error-modulo">

                        @error('modulo')
                            {{ $message }}
                        @enderror

                    </div>

                </div>


                <div class="col-12 col-md-6">

                    <label class="form-label"
                        for="descripcion">

                        Descripción
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        class="form-control @error('descripcion') is-invalid @enderror"
                        id="descripcion"
                        name="descripcion"
                        type="text"
                        maxlength="200"
                        placeholder="Ej. Permite consultar usuarios"
                        value="{{ old(
                            'descripcion',
                            data_get($permiso, 'descripcion')
                        ) }}">

                    <div class="invalid-feedback"
                        id="error-descripcion">

                        @error('descripcion')
                            {{ $message }}
                        @enderror

                    </div>

                </div>


                @if (
                    !$esEdicion ||
                    auth()->user()->can('permisos.desactivar')
                )

                    <div class="col-12">

                        <div class="status-switch-panel">

                            <div>

                                <div class="fw-bold">

                                    {{ $activoActual
                                        ? 'Permiso activo'
                                        : 'Permiso inactivo' }}

                                </div>

                                <div class="text-secondary small">

                                    {{ $activoActual
                                        ? 'El permiso puede ser asignado y utilizado por los roles.'
                                        : 'El permiso no estará disponible para nuevas asignaciones.' }}

                                </div>

                            </div>


                            <div class="form-check form-switch m-0">

                                <input
                                    type="hidden"
                                    name="activo"
                                    value="0">

                                <input
                                    class="form-check-input"
                                    id="activo"
                                    name="activo"
                                    type="checkbox"
                                    role="switch"
                                    value="1"
                                    @checked((bool) $activoActual)>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>


    <div class="form-action-bar">

        <div class="text-secondary small">

            <i class="bi bi-info-circle me-1"></i>

            Los campos marcados con
            <span class="text-danger">*</span>
            son obligatorios.

        </div>


        <div class="d-flex flex-wrap gap-2">

            <a class="btn btn-light border"
                href="{{ url('/permisos') }}">

                Cancelar

            </a>

            <button
                class="btn btn-brand"
                type="submit">

                <i class="bi bi-floppy-fill me-2"></i>

                {{ $esEdicion
                    ? 'Guardar cambios'
                    : 'Crear permiso' }}

            </button>

        </div>

    </div>

</form>


@push('scripts')
    <script src="{{ asset('assets/js/permisos/permisos-form.js') }}"></script>
@endpush