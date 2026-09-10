@extends('layouts.admin')

@section('title', 'Editar cita')

@section('content')
@php
    $cita = $cita ?? (object) [
        'id_cita' => 1,
        'id_paciente' => 1,
        'id_servicio' => 1,
        'id_especialista' => 1,
        'id_estado_cita' => 2,
        'inicio' => '2026-09-10 08:00:00',
        'fin' => '2026-09-10 09:00:00',
        'observaciones' => 'Primera sesión de tratamiento.',
        'motivo_cancelacion' => null,
    ];
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Agenda clínica</div>
        <h1 class="page-title">Editar cita</h1>
        <p class="page-subtitle">Actualiza el horario, servicio o estado de la atención.</p>
    </div>
    <a href="{{ url('/citas/'.data_get($cita, 'id_cita')) }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
</div>

@include('citas.form', ['cita' => $cita])
@endsection
