@extends('layouts.admin')

@section('title', 'Productos naturales')

@section('content')
@php
    $productos = $productos ?? collect([
        [
            'id_producto' => 1,
            'codigo' => 'EXT-001',
            'nombre' => 'Extracto de valeriana',
            'descripcion' => 'Extracto natural utilizado como apoyo para la relajación y el descanso.',
            'presentacion' => 'Frasco de 30 ml',
            'unidad_medida' => 'UNIDAD',
            'precio_referencia' => 85.00,
            'existencia_minima' => 5,
            'existencia_actual' => 3,
            'imagen_url' => null,
            'activo' => true,
            'categoria' => ['id_categoria' => 1, 'nombre' => 'Extractos'],
            'catalogo' => ['visible' => true],
        ],
        [
            'id_producto' => 2,
            'codigo' => 'INF-004',
            'nombre' => 'Té digestivo natural',
            'descripcion' => 'Mezcla de plantas para acompañar el bienestar digestivo.',
            'presentacion' => 'Caja de 20 sobres',
            'unidad_medida' => 'CAJA',
            'precio_referencia' => 48.50,
            'existencia_minima' => 4,
            'existencia_actual' => 14,
            'imagen_url' => null,
            'activo' => true,
            'categoria' => ['id_categoria' => 2, 'nombre' => 'Infusiones'],
            'catalogo' => ['visible' => true],
        ],
        [
            'id_producto' => 3,
            'codigo' => 'ACE-002',
            'nombre' => 'Aceite de árnica',
            'descripcion' => 'Aceite de uso externo para masaje y cuidado muscular.',
            'presentacion' => 'Frasco de 60 ml',
            'unidad_medida' => 'UNIDAD',
            'precio_referencia' => 65.00,
            'existencia_minima' => 3,
            'existencia_actual' => 0,
            'imagen_url' => null,
            'activo' => true,
            'categoria' => ['id_categoria' => 3, 'nombre' => 'Aceites'],
            'catalogo' => ['visible' => false],
        ],
        [
            'id_producto' => 4,
            'codigo' => 'SUP-006',
            'nombre' => 'Complejo herbal',
            'descripcion' => 'Producto natural de apoyo nutricional.',
            'presentacion' => 'Frasco de 60 cápsulas',
            'unidad_medida' => 'FRASCO',
            'precio_referencia' => 110.00,
            'existencia_minima' => 5,
            'existencia_actual' => 9,
            'imagen_url' => null,
            'activo' => false,
            'categoria' => ['id_categoria' => 4, 'nombre' => 'Suplementos'],
            'catalogo' => ['visible' => false],
        ],
    ]);

    $categorias = $categorias ?? collect([
        ['id_categoria' => 1, 'nombre' => 'Extractos'],
        ['id_categoria' => 2, 'nombre' => 'Infusiones'],
        ['id_categoria' => 3, 'nombre' => 'Aceites'],
        ['id_categoria' => 4, 'nombre' => 'Suplementos'],
    ]);

    $resumenProductos = $resumenProductos ?? [
        'total' => 4,
        'publicados' => 2,
        'existencia_baja' => 2,
        'inactivos' => 1,
    ];

    $valor = fn ($item, $campo, $default = null) => data_get($item, $campo, $default);
    $formatearDinero = fn ($monto) => $monto === null || $monto === '' ? 'Sin precio' : 'Q'.number_format((float) $monto, 2);
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Productos e inventario</div>
        <h1 class="page-title">Productos naturales</h1>
        <p class="page-subtitle">Administra la información, disponibilidad y publicación de los productos.</p>
    </div>

    <a href="{{ url('/productos/create') }}" class="btn btn-brand">
        <i class="bi bi-plus-lg me-2"></i>Nuevo producto
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-purple text-brand"><i class="bi bi-capsule-pill"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenProductos, 'total', 0) }}</div>
                    <div class="mini-stat-label">Productos registrados</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-green text-green-bw"><i class="bi bi-shop-window"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenProductos, 'publicados', 0) }}</div>
                    <div class="mini-stat-label">En catálogo público</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-warning text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenProductos, 'existencia_baja', 0) }}</div>
                    <div class="mini-stat-label">Con alerta de existencia</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card bw-card mini-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="mini-stat-icon bg-soft-danger text-danger"><i class="bi bi-archive-fill"></i></span>
                <div>
                    <div class="mini-stat-value">{{ data_get($resumenProductos, 'inactivos', 0) }}</div>
                    <div class="mini-stat-label">Productos inactivos</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card bw-card">
    <div class="card-header">
        <form class="row g-2 align-items-end" method="GET" action="{{ url('/productos') }}">
            <div class="col-12 col-lg-4">
                <label class="form-label visually-hidden" for="buscar">Buscar producto</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input
                        class="form-control"
                        id="buscar"
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por código o nombre"
                    >
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="categoria">Categoría</label>
                <select class="form-select" id="categoria" name="categoria">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $valor($categoria, 'id_categoria') }}" @selected((string) request('categoria') === (string) $valor($categoria, 'id_categoria'))>
                            {{ $valor($categoria, 'nombre') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="existencia">Existencia</label>
                <select class="form-select" id="existencia" name="existencia">
                    <option value="">Toda existencia</option>
                    <option value="disponible" @selected(request('existencia') === 'disponible')>Disponible</option>
                    <option value="baja" @selected(request('existencia') === 'baja')>Existencia baja</option>
                    <option value="agotado" @selected(request('existencia') === 'agotado')>Agotado</option>
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label visually-hidden" for="catalogo">Catálogo</label>
                <select class="form-select" id="catalogo" name="catalogo">
                    <option value="">Todo el catálogo</option>
                    <option value="1" @selected(request('catalogo') === '1')>Publicado</option>
                    <option value="0" @selected(request('catalogo') === '0')>Oculto</option>
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">
                <button class="btn btn-outline-brand flex-grow-1" type="submit">Filtrar</button>
                <a class="btn btn-light border" href="{{ url('/productos') }}" title="Limpiar filtros" aria-label="Limpiar filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría / presentación</th>
                    <th>Precio</th>
                    <th>Existencia</th>
                    <th>Catálogo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    @php
                        $id = $valor($producto, 'id_producto');
                        $nombre = $valor($producto, 'nombre', 'Producto sin nombre');
                        $codigo = $valor($producto, 'codigo', 'SIN-CÓDIGO');
                        $existencia = (float) $valor($producto, 'existencia_actual', 0);
                        $minimo = (float) $valor($producto, 'existencia_minima', 0);
                        $activo = (bool) $valor($producto, 'activo', true);
                        $publicado = (bool) $valor($producto, 'catalogo.visible', $valor($producto, 'publicado', false));
                        $imagen = $valor($producto, 'imagen_url');
                        $estadoExistencia = $existencia <= 0 ? 'agotado' : ($existencia <= $minimo ? 'bajo' : 'disponible');
                        $textoExistencia = $existencia <= 0 ? 'Agotado' : ($existencia <= $minimo ? 'Existencia baja' : 'Disponible');
                    @endphp
                    <tr>
                        <td>
                            <div class="record-person product-record">
                                <span class="product-thumb">
                                    @if ($imagen)
                                        <img src="{{ str_starts_with($imagen, 'http') ? $imagen : asset($imagen) }}" alt="{{ $nombre }}">
                                    @else
                                        <i class="bi bi-flower2"></i>
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <a class="record-name" href="{{ url('/productos/'.$id) }}">{{ $nombre }}</a>
                                    <span class="record-subtitle">{{ $codigo }} · ID {{ $id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold small">{{ $valor($producto, 'categoria.nombre', $valor($producto, 'categoria', 'Sin categoría')) }}</div>
                            <div class="text-secondary small">{{ $valor($producto, 'presentacion', 'Sin presentación') }}</div>
                        </td>
                        <td class="fw-semibold">{{ $formatearDinero($valor($producto, 'precio_referencia')) }}</td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge-status status-{{ $estadoExistencia }}">{{ $textoExistencia }}</span>
                                <span class="text-secondary small">{{ rtrim(rtrim(number_format($existencia, 2), '0'), '.') }} {{ strtolower($valor($producto, 'unidad_medida', 'unidad')) }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="catalog-badge {{ $publicado ? 'is-visible' : 'is-hidden' }}">
                                <i class="bi {{ $publicado ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                                {{ $publicado ? 'Publicado' : 'Oculto' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">
                                {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <div class="table-actions justify-content-end">
                                <a class="btn btn-sm btn-light border" href="{{ url('/productos/'.$id) }}" title="Ver producto" aria-label="Ver producto">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-light border" href="{{ url('/productos/'.$id.'/edit') }}" title="Editar producto" aria-label="Editar producto">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($activo)
                                    <form method="POST" action="{{ url('/productos/'.$id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="btn btn-sm btn-light border text-danger"
                                            type="submit"
                                            title="Desactivar producto"
                                            aria-label="Desactivar producto"
                                            data-confirm-delete="El producto dejará de mostrarse como activo, pero se conservarán sus movimientos. ¿Deseas continuar?"
                                        >
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state"><i class="bi bi-box-seam"></i>No hay productos que coincidan con los filtros.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (is_object($productos) && method_exists($productos, 'links'))
        <div class="card-footer bg-white border-0 pt-0">{{ $productos->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
