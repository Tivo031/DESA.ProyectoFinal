@extends('layouts.admin')

@section('title', 'Detalle del producto')

@section('content')
@php
    $producto = $producto ?? (object) [
        'id_producto' => 1,
        'codigo' => 'EXT-001',
        'nombre' => 'Extracto de valeriana',
        'descripcion' => 'Extracto natural utilizado como apoyo para la relajación y el descanso. Su presentación facilita la dosificación indicada por el especialista.',
        'presentacion' => 'Frasco de 30 ml',
        'unidad_medida' => 'UNIDAD',
        'precio_referencia' => 85.00,
        'existencia_minima' => 5,
        'existencia_actual' => 3,
        'imagen_url' => null,
        'activo' => true,
        'fecha_registro' => '2026-08-20 10:00:00',
        'categoria' => ['nombre' => 'Extractos'],
        'catalogo' => ['visible' => true, 'orden_visualizacion' => 1, 'fecha_publicacion' => '2026-08-21 09:00:00'],
    ];

    $movimientos = $movimientos ?? collect([
        ['id_movimiento' => 31, 'tipo' => 'ENTRADA', 'cantidad' => 10, 'referencia' => 'Compra agosto', 'usuario' => 'Laura Gómez', 'fecha_movimiento' => '2026-09-08 11:30:00'],
        ['id_movimiento' => 35, 'tipo' => 'SALIDA', 'cantidad' => 2, 'referencia' => 'Uso clínico', 'usuario' => 'Ana Ruiz', 'fecha_movimiento' => '2026-09-09 10:15:00'],
        ['id_movimiento' => 39, 'tipo' => 'SALIDA', 'cantidad' => 5, 'referencia' => 'Entrega a paciente', 'usuario' => 'Laura Gómez', 'fecha_movimiento' => '2026-09-10 08:20:00'],
    ]);

    $id = data_get($producto, 'id_producto');
    $existencia = (float) data_get($producto, 'existencia_actual', 0);
    $minimo = (float) data_get($producto, 'existencia_minima', 0);
    $estadoExistencia = $existencia <= 0 ? 'agotado' : ($existencia <= $minimo ? 'bajo' : 'disponible');
    $textoExistencia = $existencia <= 0 ? 'Agotado' : ($existencia <= $minimo ? 'Existencia baja' : 'Disponible');
    $activo = (bool) data_get($producto, 'activo', true);
    $publicado = (bool) data_get($producto, 'catalogo.visible', false);
    $imagen = data_get($producto, 'imagen_url');
    $formatearFecha = function ($fecha, $formato = 'd/m/Y') {
        if (!$fecha) return 'Sin registro';
        try { return \Illuminate\Support\Carbon::parse($fecha)->format($formato); }
        catch (\Throwable $e) { return $fecha; }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Productos naturales</div>
        <h1 class="page-title">Detalle del producto</h1>
        <p class="page-subtitle">Información general, publicación y existencias.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/productos') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
        <a href="{{ url('/productos/'.$id.'/edit') }}" class="btn btn-brand"><i class="bi bi-pencil me-2"></i>Editar</a>
    </div>
</div>

<div class="card bw-card record-hero mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="product-detail-image">
                    @if ($imagen)
                        <img src="{{ str_starts_with($imagen, 'http') ? $imagen : asset($imagen) }}" alt="{{ data_get($producto, 'nombre') }}">
                    @else
                        <i class="bi bi-flower2"></i>
                    @endif
                </div>
                <div>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="role-badge">{{ data_get($producto, 'categoria.nombre', 'Sin categoría') }}</span>
                        <span class="badge-status {{ $activo ? 'status-activo' : 'status-inactivo' }}">{{ $activo ? 'ACTIVO' : 'INACTIVO' }}</span>
                        <span class="catalog-badge {{ $publicado ? 'is-visible' : 'is-hidden' }}">
                            <i class="bi {{ $publicado ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>{{ $publicado ? 'Publicado' : 'Oculto' }}
                        </span>
                    </div>
                    <h2 class="record-hero-title mb-1">{{ data_get($producto, 'nombre') }}</h2>
                    <div class="text-secondary">{{ data_get($producto, 'codigo') }} · {{ data_get($producto, 'presentacion', 'Sin presentación') }}</div>
                </div>
            </div>

            <div class="stock-summary-card">
                <span class="detail-label">Existencia actual</span>
                <div class="stock-summary-value">{{ rtrim(rtrim(number_format($existencia, 2), '0'), '.') }}</div>
                <span class="badge-status status-{{ $estadoExistencia }}">{{ $textoExistencia }}</span>
                <small>Mínimo: {{ rtrim(rtrim(number_format($minimo, 2), '0'), '.') }} {{ strtolower(data_get($producto, 'unidad_medida', 'unidad')) }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-7">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="card-title-sm mb-0">Información del producto</h2>
                <i class="bi bi-capsule-pill text-brand"></i>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item"><span class="detail-label">Código</span><strong class="detail-value">{{ data_get($producto, 'codigo') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Categoría</span><strong class="detail-value">{{ data_get($producto, 'categoria.nombre', 'Sin categoría') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Presentación</span><strong class="detail-value">{{ data_get($producto, 'presentacion', 'No indicada') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Unidad de medida</span><strong class="detail-value">{{ data_get($producto, 'unidad_medida') }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Precio de referencia</span><strong class="detail-value">{{ data_get($producto, 'precio_referencia') !== null ? 'Q'.number_format((float) data_get($producto, 'precio_referencia'), 2) : 'No indicado' }}</strong></div>
                    <div class="detail-item"><span class="detail-label">Fecha de registro</span><strong class="detail-value">{{ $formatearFecha(data_get($producto, 'fecha_registro')) }}</strong></div>
                </div>
                <div class="detail-item mt-3">
                    <span class="detail-label">Descripción</span>
                    <p class="detail-value mb-0">{{ data_get($producto, 'descripcion', 'Sin descripción registrada.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card bw-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="card-title-sm mb-0">Catálogo público</h2>
                <i class="bi bi-shop-window text-green-bw"></i>
            </div>
            <div class="card-body d-grid gap-3">
                <div class="catalog-preview-card {{ $publicado ? 'is-published' : '' }}">
                    <span class="catalog-preview-icon"><i class="bi bi-globe2"></i></span>
                    <div>
                        <strong>{{ $publicado ? 'Producto visible' : 'Producto oculto' }}</strong>
                        <p class="mb-0">{{ $publicado ? 'Los visitantes pueden consultarlo en el catálogo.' : 'No aparece en el sitio público.' }}</p>
                    </div>
                </div>
                <div class="detail-grid single-column">
                    <div class="detail-item compact"><span class="detail-label">Orden de visualización</span><strong class="detail-value">{{ data_get($producto, 'catalogo.orden_visualizacion', 0) }}</strong></div>
                    <div class="detail-item compact"><span class="detail-label">Fecha de publicación</span><strong class="detail-value">{{ $formatearFecha(data_get($producto, 'catalogo.fecha_publicacion')) }}</strong></div>
                </div>
                <a href="{{ url('/inventario?producto='.$id) }}" class="btn btn-outline-brand"><i class="bi bi-box-seam me-2"></i>Registrar movimiento</a>
            </div>
        </div>
    </div>
</div>

<div class="card bw-card">
    <div class="card-header d-flex flex-column flex-sm-row gap-2 align-items-sm-center justify-content-between">
        <div>
            <h2 class="card-title-sm mb-1">Movimientos recientes</h2>
            <p class="text-secondary small mb-0">Últimas entradas, salidas y ajustes del producto.</p>
        </div>
        <a href="{{ url('/inventario?producto='.$id) }}" class="btn btn-sm btn-light border">Ver historial completo</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Fecha</th><th>Movimiento</th><th>Cantidad</th><th>Referencia</th><th>Responsable</th></tr></thead>
            <tbody>
                @forelse ($movimientos as $movimiento)
                    @php $esEntrada = in_array(data_get($movimiento, 'tipo'), ['ENTRADA', 'AJUSTE_POSITIVO']); @endphp
                    <tr>
                        <td>{{ $formatearFecha(data_get($movimiento, 'fecha_movimiento'), 'd/m/Y H:i') }}</td>
                        <td><span class="movement-badge {{ $esEntrada ? 'movement-in' : 'movement-out' }}"><i class="bi {{ $esEntrada ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>{{ str_replace('_', ' ', data_get($movimiento, 'tipo')) }}</span></td>
                        <td class="fw-bold {{ $esEntrada ? 'text-success' : 'text-danger' }}">{{ $esEntrada ? '+' : '-' }}{{ data_get($movimiento, 'cantidad') }}</td>
                        <td>{{ data_get($movimiento, 'referencia', 'Sin referencia') }}</td>
                        <td>{{ data_get($movimiento, 'usuario', 'Sin usuario') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state"><i class="bi bi-clock-history"></i>No hay movimientos registrados.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
