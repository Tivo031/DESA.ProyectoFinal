@extends('layouts.admin')

@section('title', 'Nuevo producto')

@section('content')
<div class="page-header">
    <div>
        <div class="page-eyebrow">Productos naturales</div>
        <h1 class="page-title">Nuevo producto</h1>
        <p class="page-subtitle">Registra la información que utilizarán el catálogo y el inventario.</p>
    </div>
    <a href="{{ url('/productos') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('productos.form', ['producto' => null])
@endsection
