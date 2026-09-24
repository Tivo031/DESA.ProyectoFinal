@extends('layouts.public')

@section('title', 'Catálogo de productos')

@section('content')

<section class="py-5 bg-light flex-grow-1">
    <div class="container">

        <div class="text-center mb-5">
            <span class="text-uppercase small fw-semibold text-success">
                Productos naturales
            </span>

            <h1 class="fw-bold mt-2">
                Nuestro catálogo
            </h1>

            <p class="text-muted mx-auto" style="max-width: 650px;">
                Conoce algunos de los productos naturales disponibles
                en BAJ WAM Acupuntura.
            </p>
        </div>


        <form
            method="GET"
            action="{{ route('catalogo.publico') }}"
            class="row g-2 justify-content-center mb-5"
        >

            <div class="col-12 col-md-5">

                <input
                    type="search"
                    name="buscar"
                    class="form-control"
                    placeholder="Buscar producto..."
                    value="{{ request('buscar') }}"
                >

            </div>

            <div class="col-8 col-md-3">

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
                                request('categoria')
                                == $categoria->id_categoria
                            )
                        >
                            {{ $categoria->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-4 col-md-auto">

                <button
                    type="submit"
                    class="btn btn-success w-100"
                >
                    <i class="bi bi-search me-1"></i>
                    Buscar
                </button>

            </div>

        </form>


        <div class="row g-4">

            @forelse ($productos as $producto)

                @php
                    $existencia = (float) $producto->existencia_actual;
                    $minima = (float) $producto->existencia_minima;
                @endphp

                <div class="col-12 col-md-6 col-lg-4">

                    <div class="card h-100 border-0 shadow-sm">

                        @if ($producto->imagen_url)

                            <img
                                src="{{ $producto->imagen_url }}"
                                class="card-img-top"
                                alt="{{ $producto->nombre }}"
                                style="height: 220px; object-fit: cover;"
                            >

                        @else

                            <div
                                class="d-flex align-items-center justify-content-center bg-light"
                                style="height: 220px;"
                            >
                                <i
                                    class="bi bi-flower1 text-success"
                                    style="font-size: 4rem;"
                                ></i>
                            </div>

                        @endif


                        <div class="card-body d-flex flex-column">

                            <div class="mb-2">

                                <span class="badge bg-light text-dark border">
                                    {{ $producto->categoria?->nombre }}
                                </span>

                            </div>

                            <h5 class="card-title fw-bold">
                                {{ $producto->nombre }}
                            </h5>

                            @if ($producto->presentacion)

                                <div class="text-muted small mb-2">
                                    {{ $producto->presentacion }}
                                </div>

                            @endif

                            <p class="card-text text-muted flex-grow-1">
                                {{ $producto->descripcion }}
                            </p>


                            <div class="d-flex align-items-center justify-content-between mt-3">

                                <div>

                                    @if ($producto->precio_referencia !== null)

                                        <span class="fw-bold fs-5">
                                            Q{{ number_format(
                                                $producto->precio_referencia,
                                                2
                                            ) }}
                                        </span>

                                    @endif

                                </div>


                                <div>

                                    @if ($existencia <= 0)

                                        <span class="badge bg-secondary">
                                            Agotado
                                        </span>

                                    @elseif ($existencia <= $minima)

                                        <span class="badge bg-warning text-dark">
                                            Pocas unidades
                                        </span>

                                    @else

                                        <span class="badge bg-success">
                                            Disponible
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="text-center py-5">

                        <i
                            class="bi bi-search text-muted"
                            style="font-size: 3rem;"
                        ></i>

                        <h4 class="mt-3">
                            No encontramos productos
                        </h4>

                        <p class="text-muted">
                            Intenta cambiar los filtros de búsqueda.
                        </p>

                    </div>

                </div>

            @endforelse

        </div>


        @if ($productos->hasPages())

            <div class="mt-5">
                {{ $productos->links('pagination::bootstrap-5') }}
            </div>

        @endif

    </div>
</section>

@endsection