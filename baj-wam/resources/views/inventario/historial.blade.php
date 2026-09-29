@extends('layouts.admin')

@section('title', 'Historial de inventario')

@section('content')

<div class="page-header">

    <div>
        <div class="page-eyebrow">Control de inventario</div>

        <h1 class="page-title">
            Historial de movimientos
        </h1>

        <p class="page-subtitle">
            Consulta las entradas, salidas y ajustes registrados.
        </p>
    </div>

    <div class="d-flex gap-2">

        @can('inventario.ver')
            <a
                href="{{ route('inventario.index') }}"
                class="btn btn-light border"
            >
                <i class="bi bi-arrow-left me-2"></i>
                Inventario
            </a>
        @endcan

        @can('inventario.movimiento')
            <a
                href="{{ route('inventario.create') }}"
                class="btn btn-brand"
            >
                <i class="bi bi-plus-circle-fill me-2"></i>
                Nuevo movimiento
            </a>
        @endcan

    </div>

</div>


<div class="card bw-card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('inventario.historial') }}"
            class="row g-2 align-items-end"
        >

            <div class="col-12 col-lg-3">

                <label class="form-label">
                    Buscar
                </label>

                <input
                    type="search"
                    name="buscar"
                    class="form-control"
                    placeholder="Producto, código o referencia"
                    value="{{ request('buscar') }}"
                >

            </div>


            <div class="col-6 col-lg-2">

                <label class="form-label">
                    Producto
                </label>

                <select
                    name="producto"
                    class="form-select"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach ($productos as $producto)

                        <option
                            value="{{ $producto->id_producto }}"
                            @selected(
                                request('producto')
                                == $producto->id_producto
                            )
                        >
                            {{ $producto->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-6 col-lg-2">

                <label class="form-label">
                    Tipo
                </label>

                <select
                    name="tipo"
                    class="form-select"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="ENTRADA"
                        @selected(request('tipo') === 'ENTRADA')
                    >
                        Entrada
                    </option>

                    <option
                        value="SALIDA"
                        @selected(request('tipo') === 'SALIDA')
                    >
                        Salida
                    </option>

                    <option
                        value="AJUSTE_POSITIVO"
                        @selected(request('tipo') === 'AJUSTE_POSITIVO')
                    >
                        Ajuste positivo
                    </option>

                    <option
                        value="AJUSTE_NEGATIVO"
                        @selected(request('tipo') === 'AJUSTE_NEGATIVO')
                    >
                        Ajuste negativo
                    </option>

                </select>

            </div>


            <div class="col-6 col-lg-2">

                <label class="form-label">
                    Desde
                </label>

                <input
                    type="date"
                    name="fecha_desde"
                    class="form-control"
                    value="{{ request('fecha_desde') }}"
                >

            </div>


            <div class="col-6 col-lg-2">

                <label class="form-label">
                    Hasta
                </label>

                <input
                    type="date"
                    name="fecha_hasta"
                    class="form-control"
                    value="{{ request('fecha_hasta') }}"
                >

            </div>


            <div class="col-12 col-lg-1">

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-outline-brand flex-grow-1"
                    >
                        <i class="bi bi-funnel"></i>
                    </button>

                    <a
                        href="{{ route('inventario.historial') }}"
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
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th class="text-nowrap" style="min-width: 125px;">
                        Tipo
                    </th>
                    <th>Cantidad</th>
                    <th>Referencia</th>
                    <th>Responsable</th>
                    <th>Observaciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($movimientos as $movimiento)

                    @php
                        $esPositivo = in_array(
                            $movimiento->tipo,
                            [
                                'ENTRADA',
                                'AJUSTE_POSITIVO'
                            ]
                        );

                        $nombreUsuario = trim(
                            ($movimiento->usuario?->nombres ?? '')
                            . ' '
                            . ($movimiento->usuario?->apellidos ?? '')
                        );
                    @endphp

                    <tr>

                        <td class="text-nowrap">

                            <strong>
                                {{ $movimiento->fecha_movimiento?->format('d/m/Y') }}
                            </strong>

                            <div class="text-muted small">
                                {{ $movimiento->fecha_movimiento?->format('H:i') }}
                            </div>

                        </td>


                        <td>

                            @can('productos.ver')
                                <a
                                    href="{{ route(
                                        'productos.show',
                                        $movimiento->producto->id_producto
                                    ) }}"
                                    class="record-name"
                                >
                                    {{ $movimiento->producto->nombre }}
                                </a>
                            @else
                                <span class="record-name">
                                    {{ $movimiento->producto->nombre }}
                                </span>
                            @endcan

                            <span class="record-subtitle">
                                {{ $movimiento->producto->codigo }}
                            </span>

                        </td>


                        <td
                            class="text-nowrap"
                            style="min-width: 125px;"
                        >

                            @if ($movimiento->tipo === 'ENTRADA')

                                <span class="badge-status status-confirmada text-nowrap">
                                    ENTRADA
                                </span>

                            @elseif ($movimiento->tipo === 'SALIDA')

                                <span class="badge-status status-cancelada text-nowrap">
                                    SALIDA
                                </span>

                            @elseif ($movimiento->tipo === 'AJUSTE_POSITIVO')

                                <span class="badge-status status-confirmada text-nowrap">
                                    AJUSTE +
                                </span>

                            @else

                                <span class="badge-status status-pendiente text-nowrap">
                                    AJUSTE -
                                </span>

                            @endif

                        </td>


                        <td class="text-nowrap">

                            <strong
                                class="{{ $esPositivo
                                    ? 'text-success'
                                    : 'text-danger' }}"
                            >
                                {{ $esPositivo ? '+' : '-' }}
                                {{ number_format(
                                    $movimiento->cantidad,
                                    2
                                ) }}
                            </strong>

                            <span class="text-muted">
                                {{ $movimiento->producto->unidad_medida }}
                            </span>

                        </td>


                        <td>
                            {{ $movimiento->referencia ?: 'Sin referencia' }}
                        </td>


                        <td>
                            {{ $nombreUsuario ?: 'Usuario no disponible' }}
                        </td>


                        <td>
                            {{ $movimiento->observaciones ?: 'Sin observaciones' }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7">

                            <div class="empty-state">
                                <i class="bi bi-clock-history"></i>
                                No hay movimientos que coincidan con los filtros.
                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- MÓVIL --}}
    <div class="d-md-none p-3">

        @forelse ($movimientos as $movimiento)

            @php
                $esPositivo = in_array(
                    $movimiento->tipo,
                    [
                        'ENTRADA',
                        'AJUSTE_POSITIVO'
                    ]
                );

                $nombreUsuario = trim(
                    ($movimiento->usuario?->nombres ?? '')
                    . ' '
                    . ($movimiento->usuario?->apellidos ?? '')
                );
            @endphp

            <div class="card bw-card mb-3">

                <div class="card-body">

                    {{-- PRODUCTO Y TIPO --}}
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                        <div class="min-w-0">

                            @can('productos.ver')
                                <a
                                    href="{{ route(
                                        'productos.show',
                                        $movimiento->producto->id_producto
                                    ) }}"
                                    class="record-name"
                                >
                                    {{ $movimiento->producto->nombre }}
                                </a>
                            @else
                                <span class="record-name">
                                    {{ $movimiento->producto->nombre }}
                                </span>
                            @endcan

                            <span class="record-subtitle">
                                {{ $movimiento->producto->codigo }}
                            </span>

                        </div>


                        @if ($movimiento->tipo === 'ENTRADA')

                            <span class="badge-status status-confirmada flex-shrink-0 text-nowrap">
                                ENTRADA
                            </span>

                        @elseif ($movimiento->tipo === 'SALIDA')

                            <span class="badge-status status-cancelada flex-shrink-0 text-nowrap">
                                SALIDA
                            </span>

                        @elseif ($movimiento->tipo === 'AJUSTE_POSITIVO')

                            <span class="badge-status status-confirmada flex-shrink-0 text-nowrap">
                                AJUSTE +
                            </span>

                        @else

                            <span class="badge-status status-pendiente flex-shrink-0 text-nowrap">
                                AJUSTE -
                            </span>

                        @endif

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="row g-3 small">

                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Fecha
                            </div>

                            <div class="fw-semibold text-nowrap">
                                {{ $movimiento->fecha_movimiento?->format('d/m/Y') }}
                            </div>

                            <div class="text-muted">
                                {{ $movimiento->fecha_movimiento?->format('H:i') }}
                            </div>

                        </div>


                        <div class="col-6">

                            <div class="text-muted mb-1">
                                Cantidad
                            </div>

                            <div class="text-nowrap">

                                <strong
                                    class="{{ $esPositivo
                                        ? 'text-success'
                                        : 'text-danger' }}"
                                >
                                    {{ $esPositivo ? '+' : '-' }}
                                    {{ number_format(
                                        $movimiento->cantidad,
                                        2
                                    ) }}
                                </strong>

                                <span class="text-muted">
                                    {{ $movimiento->producto->unidad_medida }}
                                </span>

                            </div>

                        </div>


                        <div class="col-12">

                            <div class="text-muted mb-1">
                                Referencia
                            </div>

                            <div class="fw-semibold">
                                {{ $movimiento->referencia ?: 'Sin referencia' }}
                            </div>

                        </div>


                        <div class="col-12">

                            <div class="text-muted mb-1">
                                Responsable
                            </div>

                            <div class="fw-semibold">
                                {{ $nombreUsuario ?: 'Usuario no disponible' }}
                            </div>

                        </div>


                        <div class="col-12">

                            <div class="text-muted mb-1">
                                Observaciones
                            </div>

                            <div>
                                {{ $movimiento->observaciones ?: 'Sin observaciones' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-state">
                <i class="bi bi-clock-history"></i>
                No hay movimientos que coincidan con los filtros.
            </div>

        @endforelse

    </div>


    @if ($movimientos->hasPages())

        <div class="card-footer bg-white border-0 pt-0">
            {{ $movimientos->links('pagination::bootstrap-5') }}
        </div>

    @endif

</div>

@endsection