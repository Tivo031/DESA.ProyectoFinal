@extends('layouts.admin')

@section('title', 'Editar permiso')

@section('content')

<div class="page-header">

    <div>
        <div class="page-eyebrow">
            Permisos
        </div>

        <h1 class="page-title">
            Editar permiso
        </h1>

        <p class="page-subtitle">
            Actualiza la información del permiso.
        </p>
    </div>

    <a href="{{ url('/permisos') }}"
        class="btn btn-light border">

        <i class="bi bi-arrow-left me-2"></i>
        Regresar

    </a>

</div>

@include('permisos.form', [
    'permiso' => $permiso
])

@endsection