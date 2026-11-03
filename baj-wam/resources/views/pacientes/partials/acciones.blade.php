@php
    $modo = $modo ?? 'tabla';
    $mostrarVer = $mostrarVer ?? true;
    $claseBoton = $modo === 'tabla' ? 'btn btn-sm' : 'btn';
    $accionEstado = $paciente->activo ? 'Desactivar' : 'Activar';
@endphp

<div class="d-flex flex-wrap align-items-center gap-2 {{ $modo === 'tabla' ? 'justify-content-end' : '' }}"
     data-pacientes-version="estado-unificado-v1">
    @if ($mostrarVer)
        @can('pacientes.ver')
            <a href="{{ route('pacientes.show', $paciente->id_paciente) }}"
               class="{{ $claseBoton }} btn-light border"
               title="Ver paciente" aria-label="Ver paciente">
                <i class="bi bi-eye" aria-hidden="true"></i>
            </a>
        @endcan
    @endif

    @can('pacientes.editar')
        <a href="{{ route('pacientes.edit', $paciente->id_paciente) }}"
           class="{{ $claseBoton }} {{ $modo === 'detalle' ? 'btn-brand' : 'btn-outline-brand' }}"
           title="Editar paciente" aria-label="Editar paciente">
            <i class="bi bi-pencil" aria-hidden="true"></i>
        </a>
    @endcan

    @can('pacientes.desactivar')
        <form method="POST"
              action="{{ route('pacientes.estado', $paciente) }}"
              class="m-0"
              data-paciente-estado
              data-paciente-nombre="{{ $paciente->nombre_completo }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="activo" value="{{ $paciente->activo ? 0 : 1 }}">
            <button type="submit"
                    class="{{ $claseBoton }} {{ $paciente->activo ? 'btn-outline-danger' : 'btn-outline-success' }}"
                    title="{{ $accionEstado }} paciente" aria-label="{{ $accionEstado }} paciente">
                <i class="bi {{ $paciente->activo ? 'bi-person-x' : 'bi-person-check' }}" aria-hidden="true"></i>
            </button>
        </form>
    @endcan
</div>

@once
    @push('scripts')
        <script src="{{ asset('assets/js/pacientes-estado.js') }}?v=20261103-1" defer></script>
    @endpush
@endonce
