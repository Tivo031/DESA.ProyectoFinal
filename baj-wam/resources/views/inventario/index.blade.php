@extends('layouts.admin')

@section('title', 'Inventario')

@section('content')

    <div class="page-header">
        <div>
            <div class="page-eyebrow">Productos naturales</div>
            <h1 class="page-title">Inventario</h1>
            <p class="page-subtitle">
                Consulta existencias y registra movimientos de productos.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('inventario.historial') }}" class="btn btn-light border">
                <i class="bi bi-clock-history me-2"></i>
                Historial
            </a>

            <a href="{{ route('inventario.create') }}" class="btn btn-brand">
                <i class="bi bi-box-arrow-in-down me-2"></i>
                Nuevo movimiento
            </a>

        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-purple text-brand">
                        <i class="bi bi-box-seam-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenInventario['productos'] }}
                        </div>

                        <div class="mini-stat-label">
                            Productos activos
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-warning text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenInventario['bajos'] }}
                        </div>

                        <div class="mini-stat-label">
                            Existencia baja
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-danger text-danger">
                        <i class="bi bi-box2-fill"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenInventario['sin_existencia'] }}
                        </div>

                        <div class="mini-stat-label">
                            Sin existencia
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card bw-card mini-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <span class="mini-stat-icon bg-soft-green text-green-bw">
                        <i class="bi bi-arrow-left-right"></i>
                    </span>

                    <div>
                        <div class="mini-stat-value">
                            {{ $resumenInventario['movimientos_hoy'] }}
                        </div>

                        <div class="mini-stat-label">
                            Movimientos hoy
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="card bw-card">

        <div class="card-header">

            <form method="GET" action="{{ route('inventario.index') }}" class="row g-2 align-items-end">

                <div class="col-12 col-md-7">

                    <label class="form-label">
                        Buscar producto
                    </label>

                    <input type="search" name="buscar" class="form-control" placeholder="Código o nombre"
                        value="{{ request('buscar') }}">

                </div>

                <div class="col-8 col-md-3">

                    <label class="form-label">
                        Estado
                    </label>

                    <select name="estado" class="form-select">
                        <option value="">
                            Todos
                        </option>

                        <option value="activo" @selected(request('estado') === 'activo')>
                            Activos
                        </option>

                        <option value="inactivo" @selected(request('estado') === 'inactivo')>
                            Inactivos
                        </option>
                    </select>

                </div>

                <div class="col-4 col-md-2">

                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-outline-brand flex-grow-1">
                            Filtrar
                        </button>

                        <a href="{{ route('inventario.index') }}" class="btn btn-light border">
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
                        <th>Categoría</th>
                        <th>Existencia actual</th>
                        <th>Mínima</th>
                        <th>Estado de inventario</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($productos as $producto)

                                    @php
                                        $existencia = (float) $producto->existencia_actual;
                                        $minima = (float) $producto->existencia_minima;
                                    @endphp

                                    <tr>

                                        <td>
                                            <div class="record-person compact-person">

                                                <span class="list-avatar patient-avatar">
                                                    <i class="bi bi-box-seam"></i>
                                                </span>

                                                <div>
                                                    <a href="{{ route(
                            'productos.show',
                            $producto->id_producto
                        ) }}" class="record-name">
                                                        {{ $producto->nombre }}
                                                    </a>

                                                    <span class="record-subtitle">
                                                        {{ $producto->codigo }}
                                                    </span>
                                                </div>

                                            </div>
                                        </td>

                                        <td>
                                            <span class="service-chip">
                                                <i class="bi bi-tag"></i>
                                                {{ $producto->categoria?->nombre }}
                                            </span>
                                        </td>

                                        <td>
                                            <strong>
                                                {{ number_format($existencia, 2) }}
                                            </strong>

                                            <span class="text-muted">
                                                {{ $producto->unidad_medida }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ number_format($minima, 2) }}
                                            {{ $producto->unidad_medida }}
                                        </td>

                                        <td>

                                            @if ($existencia <= 0)

                                                <span class="badge-status status-cancelada">
                                                    SIN EXISTENCIA
                                                </span>

                                            @elseif ($existencia <= $minima)

                                                <span class="badge-status status-pendiente">
                                                    EXISTENCIA BAJA
                                                </span>

                                            @else

                                                <span class="badge-status status-confirmada">
                                                    DISPONIBLE
                                                </span>

                                            @endif

                                        </td>

                                        <td class="text-end">

                                            <a href="{{ route(
                            'inventario.historial',
                            ['producto' => $producto->id_producto]
                        ) }}" class="btn btn-sm btn-light border" title="Ver movimientos">
                                                <i class="bi bi-clock-history"></i>
                                            </a>

                                        </td>

                                    </tr>

                    @empty

                        <tr>
                            <td colspan="5">

                                <div class="empty-state">
                                    <i class="bi bi-box-seam"></i>
                                    No hay productos para mostrar.
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