@extends('layouts.admin')

@section('title', 'Nuevo rol')

@section('content')
<div class="page-header">
    <div>
        <div class="page-eyebrow">Roles</div>
        <h1 class="page-title">Nuevo rol</h1>
        <p class="page-subtitle">
            Crea un nuevo rol y configura sus permisos.
        </p>
    </div>

    <a href="{{ url('/roles') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('roles.form', [
    'rol' => null,
    'permisos' => $permisos
])

@endsection