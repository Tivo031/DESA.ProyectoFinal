@php
    $producto = $producto ?? null;
    $esEdicion = filled(data_get($producto, 'id_producto'));
    $idProducto = data_get($producto, 'id_producto');
    $activoActual = old('activo', data_get($producto, 'activo', true));
    $visibleActual = old('visible_catalogo', data_get($producto, 'catalogo.visible', false));

    $categorias = $categorias ?? collect([
        ['id_categoria' => 1, 'nombre' => 'Extractos'],
        ['id_categoria' => 2, 'nombre' => 'Infusiones'],
        ['id_categoria' => 3, 'nombre' => 'Aceites'],
        ['id_categoria' => 4, 'nombre' => 'Suplementos'],
    ]);
@endphp

<form method="POST" action="{{ $esEdicion ? url('/productos/'.$idProducto) : url('/productos') }}" enctype="multipart/form-data">
    @csrf
    @if ($esEdicion)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-purple text-brand"><i class="bi bi-capsule-pill"></i></span>
                    <div>
                        <h2 class="form-section-title">Información del producto</h2>
                        <p class="form-section-copy">Datos principales con los que se identificará dentro del sistema.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="codigo">Código <span class="text-danger">*</span></label>
                            <input
                                class="form-control text-uppercase @error('codigo') is-invalid @enderror"
                                id="codigo"
                                name="codigo"
                                type="text"
                                maxlength="40"
                                value="{{ old('codigo', data_get($producto, 'codigo')) }}"
                                placeholder="Ej. EXT-001"
                                required
                            >
                            @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-8">
                            <label class="form-label" for="nombre">Nombre <span class="text-danger">*</span></label>
                            <input
                                class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre"
                                name="nombre"
                                type="text"
                                maxlength="120"
                                value="{{ old('nombre', data_get($producto, 'nombre')) }}"
                                placeholder="Nombre comercial del producto"
                                required
                            >
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="id_categoria">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_categoria') is-invalid @enderror" id="id_categoria" name="id_categoria" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ data_get($categoria, 'id_categoria') }}" @selected((string) old('id_categoria', data_get($producto, 'id_categoria')) === (string) data_get($categoria, 'id_categoria'))>
                                        {{ data_get($categoria, 'nombre') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="presentacion">Presentación</label>
                            <input
                                class="form-control @error('presentacion') is-invalid @enderror"
                                id="presentacion"
                                name="presentacion"
                                type="text"
                                maxlength="100"
                                value="{{ old('presentacion', data_get($producto, 'presentacion')) }}"
                                placeholder="Ej. Frasco de 30 ml"
                            >
                            @error('presentacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <textarea
                                class="form-control @error('descripcion') is-invalid @enderror"
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                                placeholder="Descripción general del producto"
                            >{{ old('descripcion', data_get($producto, 'descripcion')) }}</textarea>
                            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-green text-green-bw"><i class="bi bi-box-seam"></i></span>
                    <div>
                        <h2 class="form-section-title">Control y referencia</h2>
                        <p class="form-section-copy">Unidad, precio de referencia y nivel mínimo para alertas.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="unidad_medida">Unidad de medida <span class="text-danger">*</span></label>
                            <select class="form-select @error('unidad_medida') is-invalid @enderror" id="unidad_medida" name="unidad_medida" required>
                                <option value="">Seleccionar</option>
                                @foreach (['UNIDAD' => 'Unidad', 'FRASCO' => 'Frasco', 'CAJA' => 'Caja', 'BOLSA' => 'Bolsa', 'MILILITRO' => 'Mililitro', 'GRAMO' => 'Gramo'] as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('unidad_medida', data_get($producto, 'unidad_medida')) === $valor)>{{ $texto }}</option>
                                @endforeach
                            </select>
                            @error('unidad_medida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label" for="precio_referencia">Precio de referencia</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">Q</span>
                                <input
                                    class="form-control @error('precio_referencia') is-invalid @enderror"
                                    id="precio_referencia"
                                    name="precio_referencia"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('precio_referencia', data_get($producto, 'precio_referencia')) }}"
                                    placeholder="0.00"
                                >
                                @error('precio_referencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label" for="existencia_minima">Existencia mínima <span class="text-danger">*</span></label>
                            <input
                                class="form-control @error('existencia_minima') is-invalid @enderror"
                                id="existencia_minima"
                                name="existencia_minima"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('existencia_minima', data_get($producto, 'existencia_minima', 0)) }}"
                                required
                            >
                            @error('existencia_minima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Al llegar a esta cantidad, el sistema mostrará una alerta.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-plum text-plum-bw"><i class="bi bi-image"></i></span>
                    <div>
                        <h2 class="form-section-title">Imagen</h2>
                        <p class="form-section-copy">Fotografía para identificarlo y mostrarlo en el catálogo.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="product-image-uploader mb-3">
                        @if (data_get($producto, 'imagen_url'))
                            <img src="{{ str_starts_with(data_get($producto, 'imagen_url'), 'http') ? data_get($producto, 'imagen_url') : asset(data_get($producto, 'imagen_url')) }}" alt="Imagen actual">
                        @else
                            <i class="bi bi-flower2"></i>
                            <span>Sin imagen cargada</span>
                        @endif
                    </div>
                    <label class="form-label" for="imagen">Seleccionar imagen</label>
                    <input class="form-control @error('imagen') is-invalid @enderror" id="imagen" name="imagen" type="file" accept="image/png,image/jpeg,image/webp">
                    @error('imagen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Formatos sugeridos: PNG, JPG o WEBP. Máximo recomendado: 2 MB.</div>
                </div>
            </div>

            <div class="card bw-card mb-4">
                <div class="card-header form-section-header">
                    <span class="form-section-icon bg-soft-lime text-lime-bw"><i class="bi bi-shop"></i></span>
                    <div>
                        <h2 class="form-section-title">Estado y catálogo</h2>
                        <p class="form-section-copy">Controla su uso interno y visibilidad pública.</p>
                    </div>
                </div>
                <div class="card-body p-4 d-grid gap-3">
                    <div class="status-switch-panel">
                        <div>
                            <div class="fw-bold">Producto activo</div>
                            <div class="text-secondary small">Puede utilizarse en inventario y consultas.</div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" id="activo" name="activo" type="checkbox" role="switch" value="1" @checked((bool) $activoActual)>
                            <label class="visually-hidden" for="activo">Producto activo</label>
                        </div>
                    </div>

                    <div class="status-switch-panel">
                        <div>
                            <div class="fw-bold">Publicar en catálogo</div>
                            <div class="text-secondary small">Lo muestra en la sección pública del sitio.</div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input type="hidden" name="visible_catalogo" value="0">
                            <input class="form-check-input" id="visible_catalogo" name="visible_catalogo" type="checkbox" role="switch" value="1" @checked((bool) $visibleActual)>
                            <label class="visually-hidden" for="visible_catalogo">Publicar en catálogo</label>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="orden_visualizacion">Orden en catálogo</label>
                        <input
                            class="form-control @error('orden_visualizacion') is-invalid @enderror"
                            id="orden_visualizacion"
                            name="orden_visualizacion"
                            type="number"
                            min="0"
                            value="{{ old('orden_visualizacion', data_get($producto, 'catalogo.orden_visualizacion', 0)) }}"
                        >
                        @error('orden_visualizacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="info-callout">
                <span class="info-callout-icon"><i class="bi bi-info-circle-fill"></i></span>
                <div class="small">
                    <strong class="d-block mb-1">La existencia no se modifica aquí</strong>
                    Las entradas, salidas y ajustes deben registrarse desde el módulo de inventario para conservar el historial.
                </div>
            </div>
        </div>
    </div>

    <div class="form-action-bar mt-4">
        <div class="text-secondary small">
            <i class="bi bi-shield-check me-1"></i>Los campos con asterisco son obligatorios.
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light border" href="{{ $esEdicion ? url('/productos/'.$idProducto) : url('/productos') }}">Cancelar</a>
            <button class="btn btn-brand" type="submit">
                <i class="bi bi-floppy-fill me-2"></i>{{ $esEdicion ? 'Guardar cambios' : 'Registrar producto' }}
            </button>
        </div>
    </div>
</form>
