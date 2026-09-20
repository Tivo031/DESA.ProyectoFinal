@extends('layouts.admin')

@section('title', 'Editar cita')

@section('content')

<div class="page-header">
    <div>
        <div class="page-eyebrow">Agenda clínica</div>
        <h1 class="page-title">Editar cita</h1>
        <p class="page-subtitle">
            Actualiza el horario, servicio o estado de la atención.
        </p>
    </div>

    <a
        href="{{ route('citas.show', $cita->id_cita) }}"
        class="btn btn-light border"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Regresar
    </a>
</div>

@include('citas.form', ['cita' => $cita])

@endsection