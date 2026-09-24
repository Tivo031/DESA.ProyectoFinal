@extends('layouts.admin')

@section('title', 'Productos')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Productos naturales</div>
        <h1 class="page-title">Productos</h1>
        <p class="page-subtitle">
            Administra los productos naturales disponibles en la clínica.
        </p>
    </div>

    <a href="{{ route('productos.create') }}" class="btn btn-brand">
        <i class="bi bi-plus-circle-fill me-2"></i>
        Nuevo producto
    </a>
</div>

<div class="row g-3 mb-4">

    <div class="col-12 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand">
                    <i class="bi bi-box-seam-fill"></i>
                </span>

                <div>
                    <div class="mini-stat-value">
                        {{ $resumenProductos['total'] }}
                    </div>

                    <div class="mini-stat-label">
                        Total de productos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw">
                    <i class="bi bi-check-circle-fill"></i>
                </span>

                <div>
                    <div class="mini-stat-value">
                        {{ $resumenProductos['activos'] }}
                    </div>

                    <div class="mini-stat-label">
                        Activos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-danger text-danger">
                    <i class="bi bi-x-circle-fill"></i>
                </span>

                <div>
                    <div class="mini-stat-value">
                        {{ $resumenProductos['inactivos'] }}
                    </div>

                    <div class="mini-stat-label">
                        Inactivos
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card bw-card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('productos.index') }}"
            class="row g-2 align-items-end"
        >

            <div class="col-12 col-lg-4">

                <label class="form-label">
                    Buscar producto
                </label>

                <input
                    type="search"
                    name="buscar"
                    class="form-control"
                    placeholder="Código, nombre o presentación"
                    value="{{ request('buscar') }}"
                >

            </div>

            <div class="col-6 col-lg-3">

                <label class="form-label">
                    Categoría
                </label>

                <select
                    name="categoria"
                    class="form-select"
                >
                    <option value="">
                        Todas las categorías
                    </option>

                    @foreach ($categorias as $categoria)

                        <option
                            value="{{ $categoria->id_categoria }}"
                            @selected(
                                (string) request('categoria')
                                ===
                                (string) $categoria->id_categoria
                            )
                        >
                            {{ $categoria->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-6 col-lg-3">

                <label class="form-label">
                    Estado
                </label>

                <select
                    name="estado"
                    class="form-select"
                >
                    <option value="">
                        Todos
                    </option>

                    <option
                        value="activo"
                        @selected(request('estado') === 'activo')
                    >
                        Activos
                    </option>

                    <option
                        value="inactivo"
                        @selected(request('estado') === 'inactivo')
                    >
                        Inactivos
                    </option>

                </select>

            </div>

            <div class="col-12 col-lg-2">

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-outline-brand flex-grow-1"
                    >
                        Filtrar
                    </button>

                    <a
                        href="{{ route('productos.index') }}"
                        class="btn btn-light border"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>

                </div>

            </div>

        </form>

    </div>

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th>Presentación</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($productos as $producto)

                    <tr>

                        <td>
                            <div class="record-person compact-person">

                                <span class="list-avatar patient-avatar">
                                    <i class="bi bi-flower1"></i>
                                </span>

                                <div class="min-w-0">

                                    <a
                                        href="{{ route('productos.show', $producto->id_producto) }}"
                                        class="record-name"
                                    >
                                        {{ $producto->nombre }}
                                    </a>

                                    <span class="record-subtitle">
                                        {{ $producto->unidad_medida }}
                                    </span>

                                </div>

                            </div>
                        </td>

                        <td>
                            <strong>
                                {{ $producto->codigo }}
                            </strong>
                        </td>

                        <td>
                            <span class="service-chip">
                                <i class="bi bi-tag"></i>
                                {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                            </span>
                        </td>

                        <td>
                            {{ $producto->presentacion ?: 'Sin presentación' }}
                        </td>

                        <td>
                            @if ($producto->precio_referencia !== null)
                                Q{{ number_format($producto->precio_referencia, 2) }}
                            @else
                                Sin precio
                            @endif
                        </td>

                        <td>

                            @if ($producto->activo)

                                <span class="badge-status status-confirmada">
                                    ACTIVO
                                </span>

                            @else

                                <span class="badge-status status-cancelada">
                                    INACTIVO
                                </span>

                            @endif

                        </td>

                        <td class="text-end text-nowrap">

                            <div class="table-actions justify-content-end">

                                <a
                                    href="{{ route('productos.show', $producto->id_producto) }}"
                                    class="btn btn-sm btn-light border"
                                    title="Ver producto"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a
                                    href="{{ route('productos.edit', $producto->id_producto) }}"
                                    class="btn btn-sm btn-light border"
                                    title="Editar producto"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                @if ($producto->activo)

                                    <form
                                        method="POST"
                                        action="{{ route('productos.destroy', $producto->id_producto) }}"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-light border text-danger"
                                            title="Desactivar producto"
                                            data-confirm-delete="El producto quedará inactivo. ¿Deseas continuar?"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </button>

                                    </form>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-box-seam"></i>
                                No hay productos que coincidan con los filtros.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if ($productos->hasPages())
        <div class="card-footer bg-white border-0 pt-0">
            {{ $productos->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@endsection