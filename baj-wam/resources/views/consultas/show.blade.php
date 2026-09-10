@extends('layouts.admin')

@section('title', 'Detalle de la consulta')

@section('content')
@php
    $consulta = $consulta ?? (object) [
        'id_consulta' => 1,
        'fecha_consulta' => '2026-09-10 09:05:00',
        'motivo_consulta' => 'Dolor lumbar y tensión muscular',
        'observaciones' => 'La paciente refiere dolor de intensidad moderada después de permanecer sentada durante periodos prolongados. No reporta lesión reciente.',
        'diagnostico' => 'Tensión muscular en zona lumbar y limitación leve de movilidad.',
        'tratamiento_realizado' => 'Se realizó sesión de acupuntura en puntos lumbares y técnica de relajación muscular. La paciente toleró adecuadamente el procedimiento.',
        'recomendaciones' => 'Evitar levantar peso durante 24 horas, mantener una hidratación adecuada y realizar estiramientos suaves. Programar seguimiento en siete días.',
        'fecha_actualizacion' => '2026-09-10 09:35:00',
        'cita' => [
            'id_cita' => 1,
            'inicio' => '2026-09-10 08:00:00',
            'fin' => '2026-09-10 09:00:00',
            'paciente' => ['id_paciente' => 1, 'nombres' => 'María Fernanda', 'apellidos' => 'López Castillo', 'telefono' => '5555-2100', 'correo' => 'maria@example.com'],
            'especialista' => ['nombre_completo' => 'Dra. Ana Ruiz', 'profesion' => 'Acupunturista'],
            'servicio' => ['nombre' => 'Acupuntura'],
        ],
        'usuario_registro' => ['nombre_completo' => 'Dra. Ana Ruiz'],
        'productos' => [
            ['codigo' => 'ACE-002', 'nombre' => 'Aceite de árnica', 'presentacion' => 'Frasco de 60 ml', 'pivot' => ['cantidad_recomendada' => 1, 'indicaciones' => 'Aplicar en la zona dos veces al día.']],
            ['codigo' => 'INF-004', 'nombre' => 'Té digestivo natural', 'presentacion' => 'Caja de 20 sobres', 'pivot' => ['cantidad_recomendada' => 1, 'indicaciones' => 'Tomar una taza por la noche durante cinco días.']],
        ],
    ];

    $id = data_get($consulta, 'id_consulta');
    $nombres = data_get($consulta, 'cita.paciente.nombres', '');
    $apellidos = data_get($consulta, 'cita.paciente.apellidos', '');
    $nombrePaciente = trim($nombres.' '.$apellidos);
    $iniciales = strtoupper(mb_substr($nombres, 0, 1).mb_substr($apellidos, 0, 1));
    $fecha = function ($valor, $formato) {
        if (!$valor) return 'Sin registro';
        try { return \Illuminate\Support\Carbon::parse($valor)->format($formato); }
        catch (\Throwable $e) { return $valor; }
    };
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">Historial clínico</div>
        <h1 class="page-title">Detalle de la consulta</h1>
        <p class="page-subtitle">Registro de atención, tratamiento y recomendaciones.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/consultas') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
        <button class="btn btn-light border" type="button" onclick="window.print()"><i class="bi bi-printer me-2"></i>Imprimir</button>
        <a href="{{ url('/consultas/'.$id.'/edit') }}" class="btn btn-brand"><i class="bi bi-pencil me-2"></i>Editar</a>
    </div>
</div>

<div class="card bw-card consultation-hero mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <span class="detail-avatar patient-detail-avatar">{{ $iniciales ?: 'P' }}</span>
                    <div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="role-badge">CONSULTA #{{ $id }}</span>
                            <span class="service-chip"><i class="bi bi-flower1"></i>{{ data_get($consulta, 'cita.servicio.nombre') }}</span>
                        </div>
                        <h2 class="record-hero-title mb-1">{{ $nombrePaciente }}</h2>
                        <div class="text-secondary">{{ data_get($consulta, 'cita.paciente.telefono') }} · {{ data_get($consulta, 'cita.paciente.correo', 'Sin correo') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="consultation-meta-card">
                    <div><span>Fecha de atención</span><strong>{{ $fecha(data_get($consulta, 'fecha_consulta'), 'd/m/Y H:i') }}</strong></div>
                    <div><span>Especialista</span><strong>{{ data_get($consulta, 'cita.especialista.nombre_completo') }}</strong></div>
                    <div><span>Profesión</span><strong>{{ data_get($consulta, 'cita.especialista.profesion') }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card bw-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="card-title-sm mb-0">Registro clínico</h2>
                <i class="bi bi-journal-medical text-brand"></i>
            </div>
            <div class="card-body p-4 d-grid gap-3">
                <section class="clinical-note-block">
                    <span class="clinical-note-icon bg-soft-purple text-brand"><i class="bi bi-chat-square-heart-fill"></i></span>
                    <div><h3>Motivo de consulta</h3><p>{{ data_get($consulta, 'motivo_consulta') }}</p></div>
                </section>
                <section class="clinical-note-block">
                    <span class="clinical-note-icon bg-soft-green text-green-bw"><i class="bi bi-search-heart-fill"></i></span>
                    <div><h3>Observaciones</h3><p>{{ data_get($consulta, 'observaciones', 'No se registraron observaciones.') }}</p></div>
                </section>
                <section class="clinical-note-block">
                    <span class="clinical-note-icon bg-soft-plum text-plum-bw"><i class="bi bi-activity"></i></span>
                    <div><h3>Diagnóstico u orientación</h3><p>{{ data_get($consulta, 'diagnostico', 'No se registró diagnóstico.') }}</p></div>
                </section>
                <section class="clinical-note-block is-highlighted">
                    <span class="clinical-note-icon bg-soft-lime text-lime-bw"><i class="bi bi-heart-pulse-fill"></i></span>
                    <div><h3>Tratamiento realizado</h3><p>{{ data_get($consulta, 'tratamiento_realizado') }}</p></div>
                </section>
                <section class="clinical-note-block">
                    <span class="clinical-note-icon bg-soft-warning text-warning-emphasis"><i class="bi bi-list-check"></i></span>
                    <div><h3>Recomendaciones generales</h3><p>{{ data_get($consulta, 'recomendaciones', 'No se registraron recomendaciones.') }}</p></div>
                </section>
            </div>
        </div>

        <div class="card bw-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div><h2 class="card-title-sm mb-1">Productos recomendados</h2><p class="text-secondary small mb-0">Indicaciones registradas durante esta consulta.</p></div>
                <i class="bi bi-flower2 text-green-bw"></i>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Producto</th><th>Presentación</th><th>Cantidad</th><th>Indicaciones</th></tr></thead>
                    <tbody>
                        @forelse (data_get($consulta, 'productos', []) as $producto)
                            <tr>
                                <td><strong class="d-block">{{ data_get($producto, 'nombre') }}</strong><span class="text-secondary small">{{ data_get($producto, 'codigo') }}</span></td>
                                <td>{{ data_get($producto, 'presentacion', 'No indicada') }}</td>
                                <td>{{ data_get($producto, 'pivot.cantidad_recomendada', data_get($producto, 'cantidad_recomendada', 'No indicada')) }}</td>
                                <td>{{ data_get($producto, 'pivot.indicaciones', data_get($producto, 'indicaciones')) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty-state"><i class="bi bi-flower2"></i>No se recomendaron productos.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card bw-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="card-title-sm mb-0">Datos relacionados</h2>
                <i class="bi bi-link-45deg text-brand"></i>
            </div>
            <div class="card-body d-grid gap-3">
                <div class="detail-item compact"><span class="detail-label">Cita relacionada</span><a class="detail-value fw-semibold" href="{{ url('/citas/'.data_get($consulta, 'cita.id_cita')) }}">Cita #{{ data_get($consulta, 'cita.id_cita') }}</a></div>
                <div class="detail-item compact"><span class="detail-label">Horario programado</span><strong class="detail-value">{{ $fecha(data_get($consulta, 'cita.inicio'), 'd/m/Y H:i') }} - {{ $fecha(data_get($consulta, 'cita.fin'), 'H:i') }}</strong></div>
                <div class="detail-item compact"><span class="detail-label">Registrada por</span><strong class="detail-value">{{ data_get($consulta, 'usuario_registro.nombre_completo', 'Usuario del sistema') }}</strong></div>
                <div class="detail-item compact"><span class="detail-label">Última actualización</span><strong class="detail-value">{{ $fecha(data_get($consulta, 'fecha_actualizacion'), 'd/m/Y H:i') }}</strong></div>
                <a href="{{ url('/pacientes/'.data_get($consulta, 'cita.paciente.id_paciente')) }}" class="btn btn-outline-brand"><i class="bi bi-person-vcard me-2"></i>Ver expediente del paciente</a>
            </div>
        </div>

        <div class="clinical-privacy-card mb-4">
            <span><i class="bi bi-shield-lock-fill"></i></span>
            <div><strong>Información confidencial</strong><p>Este registro debe ser consultado únicamente por personal autorizado.</p></div>
        </div>

        <form method="POST" action="{{ url('/consultas/'.$id) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger w-100" type="submit" data-confirm-delete="Esta acción eliminará la consulta y sus productos recomendados. ¿Deseas continuar?">
                <i class="bi bi-trash3 me-2"></i>Eliminar consulta
            </button>
        </form>
    </div>
</div>
@endsection
