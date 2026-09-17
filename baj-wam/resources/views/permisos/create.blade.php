@extends('layouts.admin')

@section('title', 'Nuevo permiso')

@section('content')

<div class="page-header">

    <div>
        <div class="page-eyebrow">
            Permisos
        </div>

        <h1 class="page-title">
            Nuevo permiso
        </h1>

        <p class="page-subtitle">
            Registra un nuevo permiso para el sistema.
        </p>
    </div>

    <a href="{{ url('/permisos') }}"
        class="btn btn-light border">

        <i class="bi bi-arrow-left me-2"></i>
        Regresar

    </a>

</div>

@include('permisos.form', [
    'permiso' => null
])

@endsection