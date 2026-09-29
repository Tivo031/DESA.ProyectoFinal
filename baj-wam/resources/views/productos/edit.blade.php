@extends('layouts.admin')

@section('title', 'Editar producto')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Productos naturales</div>
        <h1 class="page-title">Editar producto</h1>
        <p class="page-subtitle">
            Actualiza la información del producto.
        </p>
    </div>

    <a
        href="{{ route('productos.show', $producto->id_producto) }}"
        class="btn btn-light border"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<div class="card bw-card">

    <div class="card-header">
        <h2 class="card-title-sm mb-0">
            Información del producto
        </h2>
    </div>

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('productos.update', $producto->id_producto) }}"
        >
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre</label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $producto->nombre) }}"
                        maxlength="120"
                        required
                    >

                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código</label>

                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        class="form-control @error('codigo') is-invalid @enderror"
                        value="{{ old('codigo', $producto->codigo) }}"
                        maxlength="40"
                        required
                    >

                    @error('codigo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="id_categoria" class="form-label">Categoría</label>

                    <select
                        id="id_categoria"
                        name="id_categoria"
                        class="form-select @error('id_categoria') is-invalid @enderror"
                        required
                    >
                        @foreach ($categorias as $categoria)
                            <option
                                value="{{ $categoria->id_categoria }}"
                                @selected(
                                    old('id_categoria', $producto->id_categoria)
                                    == $categoria->id_categoria
                                )
                            >
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="presentacion" class="form-label">Presentación</label>

                    <input
                        type="text"
                        id="presentacion"
                        name="presentacion"
                        class="form-control"
                        value="{{ old('presentacion', $producto->presentacion) }}"
                        maxlength="100"
                    >
                </div>

                <div class="col-md-4">
                    <label for="unidad_medida" class="form-label">
                        Unidad de medida
                    </label>

                    <input
                        type="text"
                        id="unidad_medida"
                        name="unidad_medida"
                        class="form-control"
                        value="{{ old('unidad_medida', $producto->unidad_medida) }}"
                        maxlength="30"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label for="precio_referencia" class="form-label">
                        Precio de referencia
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">Q</span>

                        <input
                            type="number"
                            id="precio_referencia"
                            name="precio_referencia"
                            class="form-control"
                            value="{{ old('precio_referencia', $producto->precio_referencia) }}"
                            min="0"
                            step="0.01"
                        >
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="existencia_minima" class="form-label">
                        Existencia mínima
                    </label>

                    <input
                        type="number"
                        id="existencia_minima"
                        name="existencia_minima"
                        class="form-control"
                        value="{{ old('existencia_minima', $producto->existencia_minima) }}"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>

                <div class="col-12">
                    <label for="descripcion" class="form-label">
                        Descripción
                    </label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="form-control"
                        rows="3"
                    >{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                <div class="col-12">
                    <label for="imagen_url" class="form-label">
                        URL de imagen
                    </label>

                    <input
                        type="url"
                        id="imagen_url"
                        name="imagen_url"
                        class="form-control"
                        value="{{ old('imagen_url', $producto->imagen_url) }}"
                        maxlength="255"
                    >
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="{{ route('productos.show', $producto->id_producto) }}"
                    class="btn btn-light border"
                >
                    Cancelar
                </a>

                <button type="submit" class="btn btn-brand">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>
</div>

@endsection