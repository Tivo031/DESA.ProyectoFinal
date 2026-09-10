@extends('layouts.admin')

@section('title', 'Nuevo paciente')

@section('content')
<div class="page-header">
    <div>
        <div class="page-eyebrow">Pacientes</div>
        <h1 class="page-title">Nuevo paciente</h1>
        <p class="page-subtitle">Registra la información general y de contacto del paciente.</p>
    </div>
    <a href="{{ url('/pacientes') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('pacientes.form', ['paciente' => null])
@endsection
