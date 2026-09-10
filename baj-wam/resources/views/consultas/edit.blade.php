@extends('layouts.admin')

@section('title', 'Editar consulta')

@section('content')
@php
    $consulta = $consulta ?? (object) [
        'id_consulta' => 1,
        'id_cita' => 1,
        'fecha_consulta' => '2026-09-10 09:05:00',
        'motivo_consulta' => 'Dolor lumbar y tensión muscular',
        'observaciones' => 'Refiere dolor de intensidad moderada después de permanecer sentada por periodos prolongados.',
        'diagnostico' => 'Tensión muscular en zona lumbar y limitación leve de movilidad.',
        'tratamiento_realizado' => 'Sesión de acupuntura en puntos lumbares y técnica de relajación.',
        'recomendaciones' => 'Evitar levantar peso durante 24 horas y realizar estiramientos suaves.',
        'productos' => [
            ['id_producto' => 3, 'cantidad_recomendada' => 1, 'indicaciones' => 'Aplicar en la zona dos veces al día.'],
        ],
    ];
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Historial clínico</div>
        <h1 class="page-title">Editar consulta</h1>
        <p class="page-subtitle">Actualiza la información registrada durante la atención.</p>
    </div>
    <a href="{{ url('/consultas/'.data_get($consulta, 'id_consulta')) }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
</div>

@include('consultas.form', ['consulta' => $consulta])
@endsection
