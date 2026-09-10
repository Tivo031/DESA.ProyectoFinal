@extends('layouts.admin')

@section('title', 'Inventario')

@section('content')
@php
    $movimientos = $movimientos ?? [
        ['fecha' => '09/09/2026 08:15', 'producto' => 'Extracto de valeriana', 'tipo' => 'ENTRADA', 'cantidad' => 10, 'usuario' => 'Laura Gómez'],
        ['fecha' => '08/09/2026 15:42', 'producto' => 'Té digestivo', 'tipo' => 'SALIDA', 'cantidad' => 2, 'usuario' => 'Laura Gómez'],
        ['fecha' => '08/09/2026 11:05', 'producto' => 'Aceite de árnica', 'tipo' => 'AJUSTE NEGATIVO', 'cantidad' => 1, 'usuario' => 'Administrador'],
    ];
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">Inventario</h1>
        <p class="page-subtitle">Entradas, salidas, ajustes y existencias de productos.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ url('/inventario/movimientos/create?tipo=ENTRADA') }}" class="btn btn-outline-brand"><i class="bi bi-box-arrow-in-down me-2"></i>Entrada</a>
        <a href="{{ url('/inventario/movimientos/create?tipo=SALIDA') }}" class="btn btn-brand"><i class="bi bi-box-arrow-up me-2"></i>Salida</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4"><div class="card bw-card stat-card stat-green"><div class="card-body"><div class="stat-icon"><i class="bi bi-box-seam"></i></div><div class="stat-value">48</div><div class="stat-label">Productos activos</div></div></div></div>
    <div class="col-12 col-md-4"><div class="card bw-card stat-card stat-lime"><div class="card-body"><div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div><div class="stat-value">3</div><div class="stat-label">Existencias bajas</div></div></div></div>
    <div class="col-12 col-md-4"><div class="card bw-card stat-card stat-purple"><div class="card-body"><div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div><div class="stat-value">17</div><div class="stat-label">Movimientos este mes</div></div></div></div>
</div>

<div class="card bw-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><h2 class="h6 fw-bold mb-1">Últimos movimientos</h2><p class="small text-secondary mb-0">Historial reciente del inventario.</p></div>
        <a class="btn btn-sm btn-soft" href="#">Ver todos</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Responsable</th></tr></thead>
            <tbody>
                @forelse ($movimientos as $movimiento)
                    <tr>
                        <td>{{ $movimiento['fecha'] }}</td>
                        <td class="fw-semibold">{{ $movimiento['producto'] }}</td>
                        <td><span class="badge {{ $movimiento['tipo'] === 'ENTRADA' ? 'text-bg-success' : ($movimiento['tipo'] === 'SALIDA' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ $movimiento['tipo'] }}</span></td>
                        <td>{{ $movimiento['cantidad'] }}</td>
                        <td>{{ $movimiento['usuario'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state"><i class="bi bi-arrow-left-right"></i>No hay movimientos registrados.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
