@extends('layouts.admin')

@section('title', 'Nueva cita')

@section('content')
<div class="page-header">
    <div>
        <div class="page-eyebrow">Agenda clínica</div>
        <h1 class="page-title">Nueva cita</h1>
        <p class="page-subtitle">Selecciona al paciente, especialista y un horario disponible.</p>
    </div>
    <a href="{{ url('/citas') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
</div>

@include('citas.form', ['cita' => null])
@endsection
