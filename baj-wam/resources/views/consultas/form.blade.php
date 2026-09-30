@php
    $productosSeleccionados = collect(old('productos', []))
        ->map(function ($item) {
            return [
                'id_producto' => data_get($item, 'id_producto'),
                'cantidad_recomendada' => data_get($item, 'cantidad_recomendada'),
                'indicaciones' => data_get($item, 'indicaciones'),
            ];
        })
        ->values()
        ->all();

    if (empty($productosSeleccionados)) {
        $productosSeleccionados = [
            [
                'id_producto' => '',
                'cantidad_recomendada' => 1,
                'indicaciones' => '',
            ],
        ];
    }

    $fechaConsulta = old('fecha_consulta', now()->format('Y-m-d\TH:i'));

    $formatearCita = function ($cita) {
        $nombrePaciente = trim(($cita->paciente?->nombres ?? '') . ' ' . ($cita->paciente?->apellidos ?? ''));

        $fecha = $cita->inicio;

        try {
            $fecha = \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y H:i');
        } catch (\Throwable $e) {
        }

        return $nombrePaciente . ' · ' . $fecha . ' · ' . ($cita->servicio?->nombre ?? 'Sin servicio');
    };
@endphp

<form id="formConsulta" method="POST" action="{{ route('consultas.store') }}" novalidate>
    @csrf

    <div class="row g-4">

        <div class="col-12 col-xl-8">

            {{-- CITA --}}
            <div class="card bw-card mb-4">

                <div class="card-header form-section-header">

                    <span class="form-section-icon bg-soft-purple text-brand">
                        <i class="bi bi-calendar2-heart-fill"></i>
                    </span>

                    <div>
                        <h2 class="form-section-title">
                            Cita y fecha de atención
                        </h2>

                        <p class="form-section-copy">
                            Cada consulta debe quedar relacionada
                            con una cita y un paciente.
                        </p>
                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="row g-3">

                        <div class="col-12 col-lg-8">

                            <label class="form-label" for="id_cita">
                                Cita
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select
                                    @error('id_cita')
                                        is-invalid
                                    @enderror"
                                id="id_cita" name="id_cita">
                                <option value="">
                                    Seleccionar cita
                                </option>

                                @foreach ($citasDisponibles as $citaDisponible)
                                    <option value="{{ $citaDisponible->id_cita }}" @selected((string) old('id_cita') === (string) $citaDisponible->id_cita)>
                                        {{ $formatearCita($citaDisponible) }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="error-id_cita" class="invalid-feedback">
                                @error('id_cita')
                                    {{ $message }}
                                @enderror
                            </div>

                            <div class="form-text">
                                Solo aparecen citas confirmadas
                                que todavía no tienen una consulta
                                registrada.
                            </div>

                        </div>

                        <div class="col-12 col-lg-4">

                            <label class="form-label" for="fecha_consulta">
                                Fecha de consulta
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                class="form-control
                                    @error('fecha_consulta')
                                        is-invalid
                                    @enderror"
                                id="fecha_consulta" name="fecha_consulta" type="datetime-local"
                                value="{{ $fechaConsulta }}">

                            <div id="error-fecha_consulta" class="invalid-feedback">
                                @error('fecha_consulta')
                                    {{ $message }}
                                @enderror
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- REGISTRO CLÍNICO --}}
            <div class="card bw-card mb-4">

                <div class="card-header form-section-header">

                    <span class="form-section-icon
                            bg-soft-green text-green-bw">
                        <i class="bi bi-clipboard2-pulse-fill"></i>
                    </span>

                    <div>
                        <h2 class="form-section-title">
                            Registro clínico
                        </h2>

                        <p class="form-section-copy">
                            Documenta el motivo, hallazgos y
                            tratamiento realizado.
                        </p>
                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="row g-3">

                        {{-- MOTIVO --}}
                        <div class="col-12">

                            <label class="form-label" for="motivo_consulta">
                                Motivo de consulta
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                class="form-control
                                    @error('motivo_consulta')
                                        is-invalid
                                    @enderror"
                                id="motivo_consulta" name="motivo_consulta" rows="3"
                                placeholder="Describe el motivo principal de la atención">{{ old('motivo_consulta') }}</textarea>

                            <div id="error-motivo_consulta" class="invalid-feedback">
                                @error('motivo_consulta')
                                    {{ $message }}
                                @enderror
                            </div>

                        </div>


                        {{-- OBSERVACIONES --}}
                        <div class="col-12 col-lg-6">

                            <label class="form-label" for="observaciones">
                                Observaciones
                            </label>

                            <textarea
                                class="form-control
                                    @error('observaciones')
                                        is-invalid
                                    @enderror"
                                id="observaciones" name="observaciones" rows="5" placeholder="Hallazgos relevantes durante la atención">{{ old('observaciones') }}</textarea>

                            <div id="error-observaciones" class="invalid-feedback">
                                @error('observaciones')
                                    {{ $message }}
                                @enderror
                            </div>

                        </div>


                        {{-- DIAGNÓSTICO --}}
                        <div class="col-12 col-lg-6">

                            <label class="form-label" for="diagnostico">
                                Diagnóstico u orientación
                            </label>

                            <textarea
                                class="form-control
                                    @error('diagnostico')
                                        is-invalid
                                    @enderror"
                                id="diagnostico" name="diagnostico" rows="5"
                                placeholder="Evaluación u orientación registrada por el especialista">{{ old('diagnostico') }}</textarea>

                            <div id="error-diagnostico" class="invalid-feedback">
                                @error('diagnostico')
                                    {{ $message }}
                                @enderror
                            </div>

                        </div>


                        {{-- TRATAMIENTO --}}
                        <div class="col-12">

                            <label class="form-label" for="tratamiento_realizado">
                                Tratamiento realizado
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                class="form-control
                                    @error('tratamiento_realizado')
                                        is-invalid
                                    @enderror"
                                id="tratamiento_realizado" name="tratamiento_realizado" rows="4"
                                placeholder="Describe el procedimiento o tratamiento aplicado">{{ old('tratamiento_realizado') }}</textarea>

                            <div id="error-tratamiento_realizado" class="invalid-feedback">
                                @error('tratamiento_realizado')
                                    {{ $message }}
                                @enderror
                            </div>

                        </div>


                        {{-- RECOMENDACIONES --}}
                        <div class="col-12">

                            <label class="form-label" for="recomendaciones">
                                Recomendaciones generales
                            </label>

                            <textarea
                                class="form-control
                                    @error('recomendaciones')
                                        is-invalid
                                    @enderror"
                                id="recomendaciones" name="recomendaciones" rows="4"
                                placeholder="Cuidados, seguimiento o indicaciones generales">{{ old('recomendaciones') }}</textarea>

                            <div id="error-recomendaciones" class="invalid-feedback">
                                @error('recomendaciones')
                                    {{ $message }}
                                @enderror
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PRODUCTOS --}}
            <div class="card bw-card mb-4">

                <div
                    class="card-header d-flex flex-column
                        flex-sm-row gap-3 align-items-sm-center
                        justify-content-between">

                    <div class="form-section-header">

                        <span class="form-section-icon
                                bg-soft-lime text-lime-bw">
                            <i class="bi bi-flower2"></i>
                        </span>

                        <div>
                            <h2 class="form-section-title">
                                Productos recomendados
                            </h2>

                            <p class="form-section-copy">
                                Agrega únicamente los productos
                                sugeridos durante esta consulta.
                            </p>
                        </div>

                    </div>

                    <button class="btn btn-sm btn-outline-brand" type="button" id="add-product-row">
                        <i class="bi bi-plus-lg me-1"></i>
                        Agregar producto
                    </button>

                </div>

                <div class="card-body p-4">

                    @error('productos')
                        <div class="alert alert-danger py-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="recommended-products-list" class="d-grid gap-3">

                        @foreach ($productosSeleccionados as $indice => $seleccionado)
                            <div class="recommended-product-row" data-product-row>

                                <div class="row g-3 align-items-end">

                                    {{-- PRODUCTO --}}
                                    <div class="col-12 col-lg-5">

                                        <label class="form-label">
                                            Producto
                                        </label>

                                        <select
                                            class="form-select
                                                @error('productos.' . $indice . '.id_producto')
                                                    is-invalid
                                                @enderror"
                                            name="productos[{{ $indice }}][id_producto]">
                                            <option value="">
                                                Seleccionar producto
                                            </option>

                                            @foreach ($productos as $producto)
                                                <option value="{{ $producto->id_producto }}"
                                                    @selected((string) data_get($seleccionado, 'id_producto') === (string) $producto->id_producto)>
                                                    {{ $producto->codigo }}
                                                    ·
                                                    {{ $producto->nombre }}

                                                    @if ($producto->presentacion)
                                                        ({{ $producto->presentacion }})
                                                    @endif
                                                </option>
                                            @endforeach

                                        </select>

                                        <div class="invalid-feedback">
                                            @error('productos.' . $indice . '.id_producto')
                                                {{ $message }}
                                            @enderror
                                        </div>

                                    </div>


                                    {{-- CANTIDAD --}}
                                    <div class="col-5 col-lg-2">

                                        <label class="form-label">
                                            Cantidad
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            class="form-control
                                                @error('productos.' . $indice . '.cantidad_recomendada')
                                                    is-invalid
                                                @enderror"
                                            name="productos[{{ $indice }}][cantidad_recomendada]"
                                            type="number" min="0.01" step="0.01"
                                            value="{{ data_get($seleccionado, 'cantidad_recomendada', 1) ?: 1 }}">

                                        <div class="invalid-feedback">
                                            @error('productos.' . $indice . '.cantidad_recomendada')
                                                {{ $message }}
                                            @enderror
                                        </div>

                                    </div>


                                    {{-- INDICACIONES --}}
                                    <div class="col-7 col-lg-4">

                                        <label class="form-label">
                                            Indicaciones
                                        </label>

                                        <input
                                            class="form-control
                                                @error('productos.' . $indice . '.indicaciones')
                                                    is-invalid
                                                @enderror"
                                            name="productos[{{ $indice }}][indicaciones]" type="text"
                                            maxlength="500"
                                            value="{{ data_get($seleccionado, 'indicaciones') }}"
                                            placeholder="Ej. Tomar una vez al día">

                                        <div class="invalid-feedback">
                                            @error('productos.' . $indice . '.indicaciones')
                                                {{ $message }}
                                            @enderror
                                        </div>

                                    </div>


                                    {{-- QUITAR --}}
                                    <div class="col-12 col-lg-1 d-grid">

                                        <button
                                            class="btn btn-light border
                                                text-danger
                                                remove-product-row"
                                            type="button" title="Quitar producto" aria-label="Quitar producto">
                                            <i class="bi bi-trash3"></i>
                                        </button>

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>


                    {{-- PLANTILLA PARA PRODUCTOS NUEVOS --}}
                    <template id="product-row-template">

                        <div class="recommended-product-row" data-product-row>

                            <div class="row g-3 align-items-end">

                                {{-- PRODUCTO --}}
                                <div class="col-12 col-lg-5">

                                    <label class="form-label">
                                        Producto
                                    </label>

                                    <select class="form-select" name="productos[__INDEX__][id_producto]">
                                        <option value="">
                                            Seleccionar producto
                                        </option>

                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id_producto }}">
                                                {{ $producto->codigo }}
                                                ·
                                                {{ $producto->nombre }}

                                                @if ($producto->presentacion)
                                                    ({{ $producto->presentacion }})
                                                @endif
                                            </option>
                                        @endforeach

                                    </select>

                                    <div class="invalid-feedback"></div>

                                </div>


                                {{-- CANTIDAD --}}
                                <div class="col-5 col-lg-2">

                                    <label class="form-label">
                                        Cantidad
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input class="form-control" name="productos[__INDEX__][cantidad_recomendada]"
                                        type="number" min="0.01" step="0.01" value="1">

                                    <div class="invalid-feedback"></div>

                                </div>


                                {{-- INDICACIONES --}}
                                <div class="col-7 col-lg-4">

                                    <label class="form-label">
                                        Indicaciones
                                    </label>

                                    <input class="form-control" name="productos[__INDEX__][indicaciones]"
                                        type="text" maxlength="500" placeholder="Ej. Tomar una vez al día">

                                    <div class="invalid-feedback"></div>

                                </div>


                                {{-- QUITAR --}}
                                <div class="col-12 col-lg-1 d-grid">

                                    <button
                                        class="btn btn-light border
                                            text-danger
                                            remove-product-row"
                                        type="button" title="Quitar producto" aria-label="Quitar producto">
                                        <i class="bi bi-trash3"></i>
                                    </button>

                                </div>

                            </div>

                        </div>

                    </template>

                </div>

            </div>

        </div>


        {{-- COLUMNA DERECHA --}}
        <div class="col-12 col-xl-4">

            <div class="card bw-card mb-4">

                <div class="card-header form-section-header">

                    <span class="form-section-icon
                            bg-soft-plum text-plum-bw">
                        <i class="bi bi-journal-medical"></i>
                    </span>

                    <div>
                        <h2 class="form-section-title">
                            Buenas prácticas
                        </h2>

                        <p class="form-section-copy">
                            Mantén el registro claro y útil
                            para futuras atenciones.
                        </p>
                    </div>

                </div>

                <div class="card-body">

                    <ul class="check-list mb-0">

                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Escribe información breve y precisa.
                        </li>

                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Diferencia observaciones y tratamiento.
                        </li>

                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Registra las indicaciones de cada producto.
                        </li>

                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Confirma que la cita y paciente sean correctos.
                        </li>

                    </ul>

                </div>

            </div>


            <div class="clinical-privacy-card mb-4">

                <span>
                    <i class="bi bi-shield-lock-fill"></i>
                </span>

                <div>
                    <strong>
                        Información confidencial
                    </strong>

                    <p>
                        El acceso a las consultas debe limitarse
                        a usuarios autorizados según su rol.
                    </p>
                </div>

            </div>


            <div class="info-callout">

                <span class="info-callout-icon">
                    <i class="bi bi-person-badge-fill"></i>
                </span>

                <div class="small">

                    <strong class="d-block mb-1">
                        Especialista responsable
                    </strong>

                    Se obtiene por medio de la cita seleccionada;
                    el usuario que registra se toma de la sesión
                    iniciada.

                </div>

            </div>

        </div>

    </div>


    {{-- ACCIONES --}}
    <div class="form-action-bar mt-4">

        <div class="text-secondary small">
            <i class="bi bi-shield-check me-1"></i>
            Los campos con asterisco son obligatorios.
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a class="btn btn-light border" href="{{ route('consultas.index') }}">
                Cancelar
            </a>

            <button class="btn btn-brand" type="submit">
                <i class="bi bi-floppy-fill me-2"></i>
                Registrar consulta
            </button>

        </div>

    </div>

</form>


@push('scripts')
    <script src="{{ asset('assets/js/consultas/consultas-form.js') }}"></script>
@endpush
