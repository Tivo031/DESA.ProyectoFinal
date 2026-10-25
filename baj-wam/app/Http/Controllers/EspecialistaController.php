<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class EspecialistaController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ]);

        $consulta = Especialista::query()
            ->with('usuario.rol')
            ->whereHas('usuario.rol', fn ($rol) => $rol->where('nombre', 'ESPECIALISTA'));

        if (!empty($filtros['buscar'])) {
            $buscar = addcslashes(trim($filtros['buscar']), '\\%_');
            $consulta->where(function ($query) use ($buscar) {
                $query->where('profesion', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($usuario) use ($buscar) {
                        $usuario->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%")
                            ->orWhere('usuario', 'like', "%{$buscar}%");
                    });
            });
        }

        if (!empty($filtros['estado'])) {
            $estado = $filtros['estado'] === 'activo';
            if ($estado) {
                $consulta->where('activo', true)
                    ->whereHas('usuario', fn ($usuario) => $usuario->where('activo', true));
            } else {
                $consulta->where(function ($query) {
                    $query->where('activo', false)
                        ->orWhereHas('usuario', fn ($usuario) => $usuario->where('activo', false));
                });
            }
        }

        $especialistas = $consulta->orderBy('id_especialista')->paginate(10)->withQueryString();

        return view('especialistas.index', compact('especialistas'));
    }

    public function show(Especialista $especialista)
    {
        $especialista->load([
            'usuario.rol',
            'citas' => fn ($citas) => $citas->with(['servicio', 'estado'])->latest('inicio')->limit(10),
        ]);

        abort_unless($especialista->usuario?->rol?->nombre === 'ESPECIALISTA', 404);

        return view('especialistas.show', compact('especialista'));
    }

    public function cambiarEstado(Request $request, Especialista $especialista)
    {
        $datos = $request->validate(['activo' => ['required', 'boolean']]);

        try {
            DB::transaction(function () use ($especialista, $datos) {
                $especialista = Especialista::query()->with('usuario.rol')->lockForUpdate()
                    ->findOrFail($especialista->id_especialista);

                if ($especialista->usuario?->rol?->nombre !== 'ESPECIALISTA') {
                    throw ValidationException::withMessages([
                        'activo' => 'Este registro ya no corresponde a un usuario con rol ESPECIALISTA.',
                    ]);
                }

                $activar = (bool) $datos['activo'];

                if ($activar && !$especialista->usuario->activo) {
                    throw ValidationException::withMessages([
                        'activo' => 'Primero debes activar la cuenta del usuario para habilitar al especialista.',
                    ]);
                }

                if (!$activar && $this->tieneCitasFuturasActivas($especialista)) {
                    throw ValidationException::withMessages([
                        'activo' => 'No se puede desactivar al especialista porque tiene citas futuras activas.',
                    ]);
                }

                $especialista->update(['activo' => $activar]);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'No fue posible cambiar el estado del especialista.');
        }

        return back()->with('success', 'Estado del especialista actualizado correctamente.');
    }

    private function tieneCitasFuturasActivas(Especialista $especialista): bool
    {
        return Cita::query()
            ->where('id_especialista', $especialista->id_especialista)
            ->where('inicio', '>=', now())
            ->whereHas('estado', fn ($estado) => $estado->where('codigo', '!=', 'CANCELADA'))
            ->lockForUpdate()
            ->exists();
    }
}
