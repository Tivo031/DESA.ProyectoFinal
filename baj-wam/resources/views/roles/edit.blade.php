@extends('layouts.admin')

@section('title', 'Editar rol')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Roles</div>

        <h1 class="page-title">
            Editar rol
        </h1>

        <p class="page-subtitle">
            Actualiza la información y los permisos del rol.
        </p>
    </div>

    <a href="{{ url('/roles') }}"
        class="btn btn-light border">

        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

@include('roles.form', [
    'rol' => $rol,
    'permisos' => $permisos
])

@endsection