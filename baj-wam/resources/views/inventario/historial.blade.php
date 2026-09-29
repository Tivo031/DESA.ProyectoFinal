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

        <a
            href="{{ route('inventario.index') }}"
            class="btn btn-light border"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Inventario
        </a>

        <a
            href="{{ route('inventario.create') }}"
            class="btn btn-brand"
        >
            <i class="bi bi-plus-circle-fill me-2"></i>
            Nuevo movimiento
        </a>

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


    <div class="table-responsive">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
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

                            <a
                                href="{{ route(
                                    'productos.show',
                                    $movimiento->producto->id_producto
                                ) }}"
                                class="record-name"
                            >
                                {{ $movimiento->producto->nombre }}
                            </a>

                            <span class="record-subtitle">
                                {{ $movimiento->producto->codigo }}
                            </span>

                        </td>


                        <td>

                            @if ($movimiento->tipo === 'ENTRADA')

                                <span class="badge-status status-confirmada">
                                    ENTRADA
                                </span>

                            @elseif ($movimiento->tipo === 'SALIDA')

                                <span class="badge-status status-cancelada">
                                    SALIDA
                                </span>

                            @elseif ($movimiento->tipo === 'AJUSTE_POSITIVO')

                                <span class="badge-status status-confirmada">
                                    AJUSTE +
                                </span>

                            @else

                                <span class="badge-status status-pendiente">
                                    AJUSTE -
                                </span>

                            @endif

                        </td>


                        <td>

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


    @if ($movimientos->hasPages())

        <div class="card-footer bg-white border-0 pt-0">
            {{ $movimientos->links('pagination::bootstrap-5') }}
        </div>

    @endif

</div>

@endsection