@extends('layouts.admin')

@section('title', 'Detalle del especialista')

@section('content')
    <div class="page-header">
        <div>
            <div class="page-eyebrow">Atención clínica</div>
            <h1 class="page-title">{{ $especialista->usuario?->nombres }} {{ $especialista->usuario?->apellidos }}</h1>
            <p class="page-subtitle">Información administrativa y resumen de citas.</p>
        </div><a href="{{ route('especialistas.index') }}" class="btn btn-light border">Regresar</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card bw-card h-100">
                <div class="card-header">
                    <h2 class="card-title-sm mb-0">Información profesional</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Usuario</dt>
                        <dd class="col-sm-7">{{ $especialista->usuario?->usuario }}</dd>
                        <dt class="col-sm-5">Correo</dt>
                        <dd class="col-sm-7">{{ $especialista->usuario?->correo }}</dd>
                        <dt class="col-sm-5">Teléfono</dt>
                        <dd class="col-sm-7">{{ $especialista->usuario?->telefono ?: 'No registrado' }}</dd>
                        <dt class="col-sm-5">Profesión</dt>
                        <dd class="col-sm-7">{{ $especialista->profesion }}</dd>
                        <dt class="col-sm-5">Estado</dt>
                        <dd class="col-sm-7">
                            {{ $especialista->activo && $especialista->usuario?->activo ? 'Activo' : 'Inactivo' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bw-card h-100">
                <div class="card-header">
                    <h2 class="card-title-sm mb-0">Citas recientes</h2>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($especialista->citas as $cita)
                            <div class="list-group-item">
                                <div class="fw-semibold">{{ $cita->inicio?->format('d/m/Y H:i') }}</div><small
                                    class="text-muted">{{ $cita->servicio?->nombre }} ·
                                    {{ $cita->estado?->codigo }}</small>
                        </div>@empty<div class="list-group-item text-muted">No hay citas registradas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
