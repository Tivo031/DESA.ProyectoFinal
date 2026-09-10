@extends('layouts.admin')

@section('title', 'Editar paciente')

@section('content')
@php
    $paciente = $paciente ?? (object) [
        'id_paciente' => 1,
        'dpi' => '2456789010101',
        'nombres' => 'María Fernanda',
        'apellidos' => 'López Castillo',
        'fecha_nacimiento' => '1987-05-18',
        'sexo' => 'FEMENINO',
        'telefono' => '5555-2100',
        'correo' => 'maria@example.com',
        'direccion' => 'Jocotenango, Sacatepéquez',
        'activo' => true,
    ];
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Pacientes</div>
        <h1 class="page-title">Editar paciente</h1>
        <p class="page-subtitle">Actualiza los datos sin alterar su historial de atención.</p>
    </div>
    <a href="{{ url('/pacientes/'.data_get($paciente, 'id_paciente')) }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Regresar
    </a>
</div>

@include('pacientes.form', ['paciente' => $paciente])
@endsection
