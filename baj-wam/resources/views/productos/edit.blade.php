@extends('layouts.admin')

@section('title', 'Editar producto')

@section('content')
@php
    $producto = $producto ?? (object) [
        'id_producto' => 1,
        'id_categoria' => 1,
        'codigo' => 'EXT-001',
        'nombre' => 'Extracto de valeriana',
        'descripcion' => 'Extracto natural utilizado como apoyo para la relajación y el descanso.',
        'presentacion' => 'Frasco de 30 ml',
        'unidad_medida' => 'UNIDAD',
        'precio_referencia' => 85.00,
        'existencia_minima' => 5,
        'imagen_url' => null,
        'activo' => true,
        'catalogo' => ['visible' => true, 'orden_visualizacion' => 1],
    ];
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Productos naturales</div>
        <h1 class="page-title">Editar producto</h1>
        <p class="page-subtitle">Actualiza sus datos sin alterar el historial de inventario.</p>
    </div>
    <a href="{{ url('/productos/'.data_get($producto, 'id_producto')) }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('productos.form', ['producto' => $producto])
@endsection
