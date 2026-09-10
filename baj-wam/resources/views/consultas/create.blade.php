@extends('layouts.admin')

@section('title', 'Nueva consulta')

@section('content')
<div class="page-header">
    <div>
        <div class="page-eyebrow">Historial clínico</div>
        <h1 class="page-title">Nueva consulta</h1>
        <p class="page-subtitle">Documenta la atención, tratamiento y recomendaciones del paciente.</p>
    </div>
    <a href="{{ url('/consultas') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
</div>

@include('consultas.form', ['consulta' => null])
@endsection
