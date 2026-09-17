@extends('layouts.admin')

@section('title', 'Editar usuario')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Usuarios</div>
        <h1 class="page-title">Editar usuario</h1>
        <p class="page-subtitle">
            Actualiza la información y los permisos de la cuenta.
        </p>
    </div>

    <a href="{{ url('/usuarios') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('usuarios.form', ['usuario' => $usuario])

@endsection