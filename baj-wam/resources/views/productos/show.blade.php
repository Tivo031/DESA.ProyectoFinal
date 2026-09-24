@extends('layouts.admin')

@section('title', 'Detalle del producto')

@section('content')

    <div class="page-header">

        <div>
            <div class="page-eyebrow">Productos naturales</div>

            <h1 class="page-title">
                {{ $producto->nombre }}
            </h1>

            <p class="page-subtitle">
                Información general del producto.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('productos.index') }}" class="btn btn-light border">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>

            <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-brand">
                <i class="bi bi-pencil me-2"></i>
                Editar producto
            </a>

            @if ($producto->activo)

                <form method="POST" action="{{ route('productos.destroy', $producto->id_producto) }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-outline-danger"
                        data-confirm-delete="El producto quedará inactivo. ¿Deseas continuar?">
                        <i class="bi bi-x-circle me-2"></i>
                        Desactivar
                    </button>

                </form>

            @endif

        </div>

    </div>


    <div class="row g-4">

        <div class="col-12 col-lg-8">

            <div class="card bw-card h-100">

                <div class="card-header">
                    <h2 class="card-title-sm mb-0">
                        Información del producto
                    </h2>
                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <span class="detail-label">
                                Nombre
                            </span>

                            <div class="fw-semibold">
                                {{ $producto->nombre }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="detail-label">
                                Código
                            </span>

                            <div>
                                {{ $producto->codigo }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="detail-label">
                                Categoría
                            </span>

                            <div class="mt-1">
                                <span class="service-chip">
                                    <i class="bi bi-tag"></i>
                                    {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="detail-label">
                                Presentación
                            </span>

                            <div>
                                {{ $producto->presentacion ?: 'No registrada' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="detail-label">
                                Unidad de medida
                            </span>

                            <div>
                                {{ $producto->unidad_medida }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="detail-label">
                                Precio de referencia
                            </span>

                            <div class="fw-semibold">
                                @if ($producto->precio_referencia !== null)
                                    Q{{ number_format($producto->precio_referencia, 2) }}
                                @else
                                    No registrado
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <span class="detail-label">
                                Existencia mínima
                            </span>

                            <div>
                                {{ number_format($producto->existencia_minima, 2) }}
                                {{ $producto->unidad_medida }}
                            </div>
                        </div>

                        <div class="col-12">
                            <span class="detail-label">
                                Descripción
                            </span>

                            <p class="mb-0">
                                {{ $producto->descripcion ?: 'Sin descripción.' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-lg-4">

            <div class="card bw-card h-100">

                <div class="card-header">
                    <h2 class="card-title-sm mb-0">
                        Estado del producto
                    </h2>
                </div>

                <div class="card-body">

                    <div class="mb-4">

                        <span class="detail-label">
                            Estado
                        </span>

                        <div class="mt-1">

                            @if ($producto->activo)

                                <span class="badge-status status-confirmada">
                                    ACTIVO
                                </span>

                            @else

                                <span class="badge-status status-cancelada">
                                    INACTIVO
                                </span>

                            @endif

                        </div>

                    </div>

                    <div class="mb-4">

                        <span class="detail-label">
                            Fecha de registro
                        </span>

                        <div>
                            {{ $producto->fecha_registro?->format('d/m/Y H:i') }}
                        </div>

                    </div>

                    <div>

                        <span class="detail-label">
                            Última actualización
                        </span>

                        <div>
                            {{ $producto->fecha_actualizacion?->format('d/m/Y H:i') }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection