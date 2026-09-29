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

    @can('productos.crear')
        <a href="{{ route('productos.create') }}" class="btn btn-brand">
            <i class="bi bi-plus-circle-fill me-2"></i>
            Nuevo producto
        </a>
    @endcan
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

        <form method="GET" action="{{ route('productos.index') }}" class="row g-2 align-items-end">

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

                <select name="categoria" class="form-select">

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

                <select name="estado" class="form-select">

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


    {{-- ESCRITORIO / TABLET --}}
    <div class="table-responsive d-none d-md-block">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th>Presentación</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Catálogo</th>
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

                                    @can('productos.ver')
                                        <a
                                            href="{{ route('productos.show', $producto->id_producto) }}"
                                            class="record-name"
                                        >
                                            {{ $producto->nombre }}
                                        </a>
                                    @else
                                        <span class="record-name">
                                            {{ $producto->nombre }}
                                        </span>
                                    @endcan

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

                        <td>

                            @if ($producto->catalogo?->visible)

                                <span class="badge-status status-confirmada">
                                    PUBLICADO
                                </span>

                            @else

                                <span class="badge-status status-cancelada">
                                    NO PUBLICADO
                                </span>

                            @endif

                        </td>

                        <td class="text-end text-nowrap">

                            <div class="table-actions justify-content-end">

                                @can('productos.ver')
                                    <a
                                        href="{{ route('productos.show', $producto->id_producto) }}"
                                        class="btn btn-sm btn-light border"
                                        title="Ver producto"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endcan

                                @can('productos.editar')
                                    <a
                                        href="{{ route('productos.edit', $producto->id_producto) }}"
                                        class="btn btn-sm btn-light border"
                                        title="Editar producto"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan

                                @if ($producto->activo)

                                    @can('productos.editar')
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
                                    @endcan

                                    @can('productos.catalogo')

                                        @if ($producto->catalogo?->visible)

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'productos.catalogo.retirar',
                                                    $producto->id_producto
                                                ) }}"
                                                class="d-inline"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-light border"
                                                    title="Retirar del catálogo"
                                                >
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            </form>

                                        @else

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'productos.catalogo.publicar',
                                                    $producto->id_producto
                                                ) }}"
                                                class="d-inline"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-light border text-success"
                                                    title="Publicar en catálogo"
                                                >
                                                    <i class="bi bi-globe2"></i>
                                                </button>
                                            </form>

                                        @endif

                                    @endcan

                                @else

                                    @can('productos.editar')
                                        <form
                                            method="POST"
                                            action="{{ route('productos.activar', $producto->id_producto) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-light border text-success"
                                                title="Activar producto"
                                            >
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    @endcan

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8">
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


    {{-- MÓVIL --}}
    <div class="d-md-none p-3">

        @forelse ($productos as $producto)

            <div class="card bw-card mb-3">

                <div class="card-body">

                    {{-- PRODUCTO Y ESTADO --}}
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                        <div class="record-person compact-person min-w-0">

                            <span class="list-avatar patient-avatar flex-shrink-0">
                                <i class="bi bi-flower1"></i>
                            </span>

                            <div class="min-w-0">

                                @can('productos.ver')
                                    <a
                                        href="{{ route('productos.show', $producto->id_producto) }}"
                                        class="record-name"
                                    >
                                        {{ $producto->nombre }}
                                    </a>
                                @else
                                    <span class="record-name">
                                        {{ $producto->nombre }}
                                    </span>
                                @endcan

                                <span class="record-subtitle">
                                    {{ $producto->unidad_medida }}
                                </span>

                            </div>

                        </div>

                        @if ($producto->activo)

                            <span class="badge-status status-confirmada flex-shrink-0 text-nowrap">
                                ACTIVO
                            </span>

                        @else

                            <span class="badge-status status-cancelada flex-shrink-0 text-nowrap">
                                INACTIVO
                            </span>

                        @endif

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="row g-3 small">

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Código
                            </div>

                            <div class="fw-semibold">
                                {{ $producto->codigo }}
                            </div>

                        </div>

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Precio
                            </div>

                            <div class="fw-semibold">

                                @if ($producto->precio_referencia !== null)

                                    Q{{ number_format($producto->precio_referencia, 2) }}

                                @else

                                    Sin precio

                                @endif

                            </div>

                        </div>

                        <div class="col-12">

                            <div class="text-muted mb-1">
                                Categoría
                            </div>

                            <span class="service-chip">
                                <i class="bi bi-tag"></i>
                                {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                            </span>

                        </div>

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Presentación
                            </div>

                            <div class="fw-semibold">
                                {{ $producto->presentacion ?: 'Sin presentación' }}
                            </div>

                        </div>

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Catálogo
                            </div>

                            @if ($producto->catalogo?->visible)

                                <span class="badge-status status-confirmada text-nowrap">
                                    PUBLICADO
                                </span>

                            @else

                                <span class="badge-status status-cancelada text-nowrap">
                                    NO PUBLICADO
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- ACCIONES --}}
                    @canany(['productos.ver', 'productos.editar', 'productos.catalogo'])

                        <hr>

                        <div class="d-flex flex-wrap gap-2">

                            @can('productos.ver')
                                <a
                                    href="{{ route('productos.show', $producto->id_producto) }}"
                                    class="btn btn-light border flex-grow-1"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Ver
                                </a>
                            @endcan

                            @can('productos.editar')
                                <a
                                    href="{{ route('productos.edit', $producto->id_producto) }}"
                                    class="btn btn-light border flex-grow-1"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Editar
                                </a>
                            @endcan

                            @if ($producto->activo)

                                @can('productos.editar')
                                    <form
                                        method="POST"
                                        action="{{ route('productos.destroy', $producto->id_producto) }}"
                                        class="flex-grow-1"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-light border text-danger w-100"
                                            data-confirm-delete="El producto quedará inactivo. ¿Deseas continuar?"
                                        >
                                            <i class="bi bi-x-circle me-1"></i>
                                            Desactivar
                                        </button>

                                    </form>
                                @endcan


                                @can('productos.catalogo')

                                    @if ($producto->catalogo?->visible)

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'productos.catalogo.retirar',
                                                $producto->id_producto
                                            ) }}"
                                            class="flex-grow-1"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-light border w-100"
                                            >
                                                <i class="bi bi-eye-slash me-1"></i>
                                                Retirar
                                            </button>

                                        </form>

                                    @else

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'productos.catalogo.publicar',
                                                $producto->id_producto
                                            ) }}"
                                            class="flex-grow-1"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-light border text-success w-100"
                                            >
                                                <i class="bi bi-globe2 me-1"></i>
                                                Publicar
                                            </button>

                                        </form>

                                    @endif

                                @endcan

                            @else

                                @can('productos.editar')
                                    <form
                                        method="POST"
                                        action="{{ route('productos.activar', $producto->id_producto) }}"
                                        class="flex-grow-1"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-light border text-success w-100"
                                        >
                                            <i class="bi bi-check-circle me-1"></i>
                                            Activar
                                        </button>

                                    </form>
                                @endcan

                            @endif

                        </div>

                    @endcanany

                </div>

            </div>

        @empty

            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                No hay productos que coincidan con los filtros.
            </div>

        @endforelse

    </div>


    @if ($productos->hasPages())
        <div class="card-footer bg-white border-0 pt-0">
            {{ $productos->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@endsection