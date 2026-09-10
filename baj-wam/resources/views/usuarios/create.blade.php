@extends('layouts.admin')

@section('title', 'Nuevo usuario')

@section('content')
<div class="page-header">
    <div>
        <div class="page-eyebrow">Usuarios</div>
        <h1 class="page-title">Nuevo usuario</h1>
        <p class="page-subtitle">Crea una cuenta interna y asigna los permisos correspondientes.</p>
    </div>
    <a href="{{ url('/usuarios') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('usuarios.form', ['usuario' => null])
@endsection
