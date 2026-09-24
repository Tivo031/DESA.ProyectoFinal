@extends('layouts.admin')

@section('title', 'Nuevo movimiento')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Control de inventario</div>
        <h1 class="page-title">Nuevo movimiento</h1>
        <p class="page-subtitle">
            Registra una entrada, salida o ajuste de inventario.
        </p>
    </div>

    <a
        href="{{ route('inventario.index') }}"
        class="btn btn-light border"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

<div class="card bw-card">

    <div class="card-header">
        <h2 class="card-title-sm mb-0">
            Información del movimiento
        </h2>
    </div>

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('inventario.store') }}"
        >
            @csrf

            <div class="row g-3">

                <div class="col-12">
                    <label for="id_producto" class="form-label">
                        Producto
                    </label>

                    <select
                        id="id_producto"
                        name="id_producto"
                        class="form-select @error('id_producto') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            Selecciona un producto
                        </option>

                        @foreach ($productos as $producto)
                            <option
                                value="{{ $producto->id_producto }}"
                                @selected(
                                    old('id_producto')
                                    == $producto->id_producto
                                )
                            >
                                {{ $producto->codigo }}
                                -
                                {{ $producto->nombre }}
                                |
                                Existencia:
                                {{ number_format($producto->existencia_actual, 2) }}
                                {{ $producto->unidad_medida }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_producto')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="tipo" class="form-label">
                        Tipo de movimiento
                    </label>

                    <select
                        id="tipo"
                        name="tipo"
                        class="form-select @error('tipo') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            Selecciona un tipo
                        </option>

                        <option value="ENTRADA" @selected(old('tipo') === 'ENTRADA')>
                            Entrada
                        </option>

                        <option value="SALIDA" @selected(old('tipo') === 'SALIDA')>
                            Salida
                        </option>

                        <option value="AJUSTE_POSITIVO" @selected(old('tipo') === 'AJUSTE_POSITIVO')>
                            Ajuste positivo
                        </option>

                        <option value="AJUSTE_NEGATIVO" @selected(old('tipo') === 'AJUSTE_NEGATIVO')>
                            Ajuste negativo
                        </option>
                    </select>

                    @error('tipo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="cantidad" class="form-label">
                        Cantidad
                    </label>

                    <input
                        type="number"
                        id="cantidad"
                        name="cantidad"
                        class="form-control @error('cantidad') is-invalid @enderror"
                        value="{{ old('cantidad') }}"
                        min="0.01"
                        step="0.01"
                        placeholder="0.00"
                        required
                    >

                    @error('cantidad')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="referencia" class="form-label">
                        Referencia
                    </label>

                    <input
                        type="text"
                        id="referencia"
                        name="referencia"
                        class="form-control @error('referencia') is-invalid @enderror"
                        value="{{ old('referencia') }}"
                        maxlength="100"
                        placeholder="Ej. COMPRA-006"
                    >

                    @error('referencia')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="observaciones" class="form-label">
                        Observaciones
                    </label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        class="form-control @error('observaciones') is-invalid @enderror"
                        rows="3"
                        maxlength="500"
                    >{{ old('observaciones') }}</textarea>

                    @error('observaciones')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="{{ route('inventario.index') }}"
                    class="btn btn-light border"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-brand"
                >
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Registrar movimiento
                </button>

            </div>

        </form>

    </div>

</div>

@endsection