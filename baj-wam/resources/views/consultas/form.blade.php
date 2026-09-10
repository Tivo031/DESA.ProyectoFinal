@php
    $consulta = $consulta ?? null;
    $esEdicion = filled(data_get($consulta, 'id_consulta'));
    $idConsulta = data_get($consulta, 'id_consulta');

    $citasDisponibles = $citasDisponibles ?? collect([
        [
            'id_cita' => 1,
            'inicio' => '2026-09-10 08:00:00',
            'paciente' => ['nombres' => 'María Fernanda', 'apellidos' => 'López Castillo'],
            'servicio' => ['nombre' => 'Acupuntura'],
            'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz'],
        ],
        [
            'id_cita' => 2,
            'inicio' => '2026-09-10 09:30:00',
            'paciente' => ['nombres' => 'Carlos Estuardo', 'apellidos' => 'Méndez López'],
            'servicio' => ['nombre' => 'Quiropráctico'],
            'especialista' => ['nombre_completo' => 'Dr. Luis García'],
        ],
        [
            'id_cita' => 3,
            'inicio' => '2026-09-10 11:00:00',
            'paciente' => ['nombres' => 'Andrea Lucía', 'apellidos' => 'Morales Díaz'],
            'servicio' => ['nombre' => 'Terapia nutricional'],
            'especialista' => ['nombre_completo' => 'Lic. Sofía Pérez'],
        ],
    ]);

    $productos = $productos ?? collect([
        ['id_producto' => 1, 'codigo' => 'EXT-001', 'nombre' => 'Extracto de valeriana', 'presentacion' => '30 ml'],
        ['id_producto' => 2, 'codigo' => 'INF-004', 'nombre' => 'Té digestivo natural', 'presentacion' => '20 sobres'],
        ['id_producto' => 3, 'codigo' => 'ACE-002', 'nombre' => 'Aceite de árnica', 'presentacion' => '60 ml'],
        ['id_producto' => 4, 'codigo' => 'SUP-006', 'nombre' => 'Complejo herbal', 'presentacion' => '60 cápsulas'],
    ]);

    $productosSeleccionados = collect(old('productos', data_get($consulta, 'productos', [])))
        ->map(function ($item) {
            return [
                'id_producto' => data_get($item, 'id_producto'),
                'cantidad_recomendada' => data_get($item, 'cantidad_recomendada', data_get($item, 'pivot.cantidad_recomendada')),
                'indicaciones' => data_get($item, 'indicaciones', data_get($item, 'pivot.indicaciones')),
            ];
        })
        ->values()
        ->all();

    if (empty($productosSeleccionados)) {
        $productosSeleccionados = [['id_producto' => '', 'cantidad_recomendada' => '', 'indicaciones' => '']];
    }

    $fechaConsulta = old('fecha_consulta', data_get($consulta, 'fecha_consulta'));
    if ($fechaConsulta) {
        try { $fechaConsulta = \Illuminate\Support\Carbon::parse($fechaConsulta)->format('Y-m-d\TH:i'); } catch (\Throwable $e) {}
    } else {
        $fechaConsulta = now()->format('Y-m-d\TH:i');
    }

    $formatearCita = function ($cita) {
        $nombre = trim(data_get($cita, 'paciente.nombres').' '.data_get($cita, 'paciente.apellidos'));
        $fecha = data_get($cita, 'inicio');
        try { $fecha = \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y H:i'); } catch (\Throwable $e) {}
        return $nombre.' · '.$fecha.' · '.data_get($cita, 'servicio.nombre');
    };
@endphp

<form method="POST" action="{{ $esEdicion ? url('/consultas/'.$idConsulta) : url('/consultas') }}">
    @csrf
    @if ($esEdicion)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-purple text-brand"><i class="bi bi-calendar2-heart-fill"></i></span>
                    <div>
                        <h2 class="form-section-title">Cita y fecha de atención</h2>
                        <p class="form-section-copy">Cada consulta debe quedar relacionada con una cita y un paciente.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <label class="form-label" for="id_cita">Cita <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_cita') is-invalid @enderror" id="id_cita" name="id_cita" required {{ $esEdicion ? 'disabled' : '' }}>
                                <option value="">Seleccionar cita</option>
                                @foreach ($citasDisponibles as $citaDisponible)
                                    <option value="{{ data_get($citaDisponible, 'id_cita') }}" @selected((string) old('id_cita', data_get($consulta, 'id_cita')) === (string) data_get($citaDisponible, 'id_cita'))>
                                        {{ $formatearCita($citaDisponible) }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($esEdicion)
                                <input type="hidden" name="id_cita" value="{{ data_get($consulta, 'id_cita') }}">
                            @endif
                            @error('id_cita')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Solo deben aparecer citas que todavía no tengan una consulta registrada.</div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label class="form-label" for="fecha_consulta">Fecha de consulta <span class="text-danger">*</span></label>
                            <input class="form-control @error('fecha_consulta') is-invalid @enderror" id="fecha_consulta" name="fecha_consulta" type="datetime-local" value="{{ $fechaConsulta }}" required>
                            @error('fecha_consulta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-green text-green-bw"><i class="bi bi-clipboard2-pulse-fill"></i></span>
                    <div>
                        <h2 class="form-section-title">Registro clínico</h2>
                        <p class="form-section-copy">Documenta el motivo, hallazgos y tratamiento realizado.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="motivo_consulta">Motivo de consulta <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('motivo_consulta') is-invalid @enderror" id="motivo_consulta" name="motivo_consulta" rows="3" required placeholder="Describe el motivo principal de la atención">{{ old('motivo_consulta', data_get($consulta, 'motivo_consulta')) }}</textarea>
                            @error('motivo_consulta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="observaciones">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="5" placeholder="Hallazgos relevantes durante la atención">{{ old('observaciones', data_get($consulta, 'observaciones')) }}</textarea>
                            @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="diagnostico">Diagnóstico u orientación</label>
                            <textarea class="form-control @error('diagnostico') is-invalid @enderror" id="diagnostico" name="diagnostico" rows="5" placeholder="Evaluación u orientación registrada por el especialista">{{ old('diagnostico', data_get($consulta, 'diagnostico')) }}</textarea>
                            @error('diagnostico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="tratamiento_realizado">Tratamiento realizado <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('tratamiento_realizado') is-invalid @enderror" id="tratamiento_realizado" name="tratamiento_realizado" rows="4" required placeholder="Describe el procedimiento o tratamiento aplicado">{{ old('tratamiento_realizado', data_get($consulta, 'tratamiento_realizado')) }}</textarea>
                            @error('tratamiento_realizado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="recomendaciones">Recomendaciones generales</label>
                            <textarea class="form-control @error('recomendaciones') is-invalid @enderror" id="recomendaciones" name="recomendaciones" rows="4" placeholder="Cuidados, seguimiento o indicaciones generales">{{ old('recomendaciones', data_get($consulta, 'recomendaciones')) }}</textarea>
                            @error('recomendaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header d-flex flex-column flex-sm-row gap-3 align-items-sm-center justify-content-between">
                    <div class="form-section-header">
                        <span class="form-section-icon bg-soft-lime text-lime-bw"><i class="bi bi-flower2"></i></span>
                        <div>
                            <h2 class="form-section-title">Productos recomendados</h2>
                            <p class="form-section-copy">Agrega únicamente los productos sugeridos durante esta consulta.</p>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-brand" type="button" id="add-product-row"><i class="bi bi-plus-lg me-1"></i>Agregar producto</button>
                </div>
                <div class="card-body p-4">
                    <div id="recommended-products-list" class="d-grid gap-3">
                        @foreach ($productosSeleccionados as $indice => $seleccionado)
                            <div class="recommended-product-row" data-product-row>
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-lg-5">
                                        <label class="form-label">Producto</label>
                                        <select class="form-select" name="productos[{{ $indice }}][id_producto]">
                                            <option value="">Seleccionar producto</option>
                                            @foreach ($productos as $producto)
                                                <option value="{{ data_get($producto, 'id_producto') }}" @selected((string) data_get($seleccionado, 'id_producto') === (string) data_get($producto, 'id_producto'))>
                                                    {{ data_get($producto, 'codigo') }} · {{ data_get($producto, 'nombre') }} ({{ data_get($producto, 'presentacion') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-5 col-lg-2">
                                        <label class="form-label">Cantidad</label>
                                        <input class="form-control" name="productos[{{ $indice }}][cantidad_recomendada]" type="number" min="0.01" step="0.01" value="{{ data_get($seleccionado, 'cantidad_recomendada') }}" placeholder="1">
                                    </div>
                                    <div class="col-7 col-lg-4">
                                        <label class="form-label">Indicaciones</label>
                                        <input class="form-control" name="productos[{{ $indice }}][indicaciones]" type="text" maxlength="500" value="{{ data_get($seleccionado, 'indicaciones') }}" placeholder="Ej. Tomar una vez al día">
                                    </div>
                                    <div class="col-12 col-lg-1 d-grid">
                                        <button class="btn btn-light border text-danger remove-product-row" type="button" title="Quitar producto" aria-label="Quitar producto"><i class="bi bi-trash3"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <template id="product-row-template">
                        <div class="recommended-product-row" data-product-row>
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-lg-5">
                                    <label class="form-label">Producto</label>
                                    <select class="form-select" name="productos[__INDEX__][id_producto]">
                                        <option value="">Seleccionar producto</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ data_get($producto, 'id_producto') }}">{{ data_get($producto, 'codigo') }} · {{ data_get($producto, 'nombre') }} ({{ data_get($producto, 'presentacion') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5 col-lg-2">
                                    <label class="form-label">Cantidad</label>
                                    <input class="form-control" name="productos[__INDEX__][cantidad_recomendada]" type="number" min="0.01" step="0.01" placeholder="1">
                                </div>
                                <div class="col-7 col-lg-4">
                                    <label class="form-label">Indicaciones</label>
                                    <input class="form-control" name="productos[__INDEX__][indicaciones]" type="text" maxlength="500" placeholder="Ej. Tomar una vez al día">
                                </div>
                                <div class="col-12 col-lg-1 d-grid">
                                    <button class="btn btn-light border text-danger remove-product-row" type="button" title="Quitar producto" aria-label="Quitar producto"><i class="bi bi-trash3"></i></button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-plum text-plum-bw"><i class="bi bi-journal-medical"></i></span>
                    <div>
                        <h2 class="form-section-title">Buenas prácticas</h2>
                        <p class="form-section-copy">Mantén el registro claro y útil para futuras atenciones.</p>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="check-list mb-0">
                        <li><i class="bi bi-check-circle-fill"></i>Escribe información breve y precisa.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Diferencia observaciones y tratamiento.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Registra las indicaciones de cada producto.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Confirma que la cita y paciente sean correctos.</li>
                    </ul>
                </div>
            </div>

            <div class="clinical-privacy-card mb-4">
                <span><i class="bi bi-shield-lock-fill"></i></span>
                <div>
                    <strong>Información confidencial</strong>
                    <p>El acceso a las consultas debe limitarse a usuarios autorizados según su rol.</p>
                </div>
            </div>

            <div class="info-callout">
                <span class="info-callout-icon"><i class="bi bi-person-badge-fill"></i></span>
                <div class="small">
                    <strong class="d-block mb-1">Especialista responsable</strong>
                    Se obtiene por medio de la cita seleccionada; el usuario que registra se toma de la sesión iniciada.
                </div>
            </div>
        </div>
    </div>

    <div class="form-action-bar mt-4">
        <div class="text-secondary small"><i class="bi bi-shield-check me-1"></i>Los campos con asterisco son obligatorios.</div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light border" href="{{ $esEdicion ? url('/consultas/'.$idConsulta) : url('/consultas') }}">Cancelar</a>
            <button class="btn btn-brand" type="submit"><i class="bi bi-floppy-fill me-2"></i>{{ $esEdicion ? 'Guardar cambios' : 'Registrar consulta' }}</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('recommended-products-list');
    const template = document.getElementById('product-row-template');
    const addButton = document.getElementById('add-product-row');
    if (!list || !template || !addButton) return;

    const bindRemoveButtons = function () {
        list.querySelectorAll('.remove-product-row').forEach(function (button) {
            button.onclick = function () {
                const rows = list.querySelectorAll('[data-product-row]');
                if (rows.length === 1) {
                    rows[0].querySelectorAll('input, select').forEach(function (field) { field.value = ''; });
                    return;
                }
                button.closest('[data-product-row]').remove();
            };
        });
    };

    addButton.addEventListener('click', function () {
        const index = list.querySelectorAll('[data-product-row]').length;
        const html = template.innerHTML.replaceAll('__INDEX__', index);
        list.insertAdjacentHTML('beforeend', html);
        bindRemoveButtons();
    });

    bindRemoveButtons();
});
</script>
@endpush
