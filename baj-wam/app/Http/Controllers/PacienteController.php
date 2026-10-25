<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Especialista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ]);

        try {
            $consulta = Paciente::query();
            $consultaResumen = Paciente::query();

            if ($especialista = $this->especialistaAutenticado($request)) {
                $restringirPorEspecialista = function ($query) use ($especialista) {
                    $query->where('id_especialista', $especialista->id_especialista);
                };

                $consulta->whereHas('citas', $restringirPorEspecialista);
                $consultaResumen->whereHas('citas', $restringirPorEspecialista);
            }

            if (!empty($filtros['buscar'])) {
                $termino = trim($filtros['buscar']);
                $idExpediente = $this->idDesdeNumeroExpediente($termino);

                if ($idExpediente !== null) {
                    $consulta->where('id_paciente', $idExpediente);
                } else {
                    $buscar = $this->escaparLike($termino);

                    $consulta->where(function ($query) use ($buscar) {
                        $query->where('nombres', 'like', '%' . $buscar . '%')
                            ->orWhere('apellidos', 'like', '%' . $buscar . '%')
                            ->orWhere('dpi', 'like', '%' . $buscar . '%')
                            ->orWhere('telefono', 'like', '%' . $buscar . '%')
                            ->orWhere('correo', 'like', '%' . $buscar . '%');
                    });
                }
            }

            if (!empty($filtros['estado'])) {
                $consulta->where(
                    'activo',
                    $filtros['estado'] === 'activo' ? 1 : 0
                );
            }

            $pacientes = $consulta
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->paginate(10)
                ->withQueryString();

            $resumenPacientes = [
                'total' => (clone $consultaResumen)->count(),
                'activos' => (clone $consultaResumen)->where('activo', true)->count(),
                'inactivos' => (clone $consultaResumen)->where('activo', false)->count(),
            ];

            return view(
                'pacientes.index',
                compact('pacientes', 'resumenPacientes')
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar pacientes.', [
                'codigo' => 'PACIENTES_LISTADO_ERROR',
            ]);

            return back()->with(
                'error',
                'No fue posible cargar los pacientes.'
            );
        }
    }

    public function buscar(Request $request)
    {
        $datos = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $termino = trim($datos['q'] ?? '');

        if ($termino === '') {
            return response()->json([]);
        }

        $buscar = $this->escaparLike($termino);
        $idExpediente = $this->idDesdeNumeroExpediente($termino);

        $consulta = Paciente::query();

        if ($especialista = $this->especialistaAutenticado($request)) {
            $consulta->whereHas('citas', function ($query) use ($especialista) {
                $query->where('id_especialista', $especialista->id_especialista);
            });
        }

        $pacientes = $consulta
            ->where(function ($query) use ($termino, $buscar, $idExpediente) {
                if ($idExpediente !== null) {
                    $query->where('id_paciente', $idExpediente);
                } elseif (preg_match('/^\d{13}$/', $termino)) {
                    $query->where('dpi', $termino)
                        ->orWhere('telefono', 'like', '%' . $buscar . '%');
                } else {
                    $query->where('nombres', 'like', '%' . $buscar . '%')
                        ->orWhere('apellidos', 'like', '%' . $buscar . '%')
                        ->orWhere('telefono', 'like', '%' . $buscar . '%');
                }
            })
            ->orderByDesc('activo')
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->limit(10)
            ->get(['id_paciente', 'dpi', 'nombres', 'apellidos', 'telefono', 'activo'])
            ->map(fn(Paciente $paciente) => [
                'id_paciente' => $paciente->id_paciente,
                'nombre_completo' => $paciente->nombre_completo,
                'dpi' => $paciente->dpi,
                'numero_expediente' => $paciente->numero_expediente,
                'telefono' => $paciente->telefono,
                'activo' => $paciente->activo,
            ]);

        return response()->json($pacientes);
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'dpi' => [
                'nullable',
                'digits:13',
                'unique:pacientes,dpi',
            ],
            'nombres' => [
                'required',
                'string',
                'max:80',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:80',
            ],
            'fecha_nacimiento' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'sexo' => [
                'nullable',
                'in:FEMENINO,MASCULINO,OTRO,NO_INDICA',
            ],
            'telefono' => [
                'nullable',
                'regex:/^\d{4}-\d{4}$/',
            ],
            'correo' => [
                'nullable',
                'email',
                'max:120',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:250',
            ],
        ], [
            'dpi.digits' => 'El DPI debe contener exactamente 13 números.',
            'dpi.unique' => 'Ya existe un paciente con este DPI.',
            'nombres.required' => 'Debes ingresar los nombres.',
            'apellidos.required' => 'Debes ingresar los apellidos.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.required' => 'Debes ingresar un número de teléfono.',
            'telefono.min' => 'El teléfono debe tener al menos 8 números.',
            'telefono.regex' => 'El teléfono debe tener el formato 5555-5555.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
        ]);

        try {
            $datos['id_usuario_registro'] = $request->user()->id_usuario;
            $datos['activo'] = true;

            $paciente = Paciente::create($datos);

            return redirect()
                ->route('pacientes.show', $paciente->id_paciente)
                ->with('success', 'Paciente registrado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al registrar paciente.', [
                'codigo' => 'PACIENTE_REGISTRO_ERROR',
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible registrar el paciente.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $consulta = Paciente::with([
                'usuarioRegistro',
                'citas' => function ($query) {
                    $query->with([
                        'servicio',
                        'estado',
                        'especialista.usuario',
                        'consulta',
                    ])->orderByDesc('inicio');
                },
            ]);

            if ($especialista = $this->especialistaAutenticado($request)) {
                $consulta->whereHas('citas', function ($query) use ($especialista) {
                    $query->where('id_especialista', $especialista->id_especialista);
                })->with([
                            'citas' => function ($query) use ($especialista) {
                                $query->where('id_especialista', $especialista->id_especialista)
                                    ->with(['servicio', 'estado', 'especialista.usuario', 'consulta'])
                                    ->orderByDesc('inicio');
                            },
                        ]);
            }

            $paciente = $consulta->findOrFail($id);

            return view('pacientes.show', compact('paciente'));

        } catch (Throwable $e) {
            Log::error('Error al cargar paciente.', [
                'id_paciente' => $id,
                'codigo' => 'PACIENTE_DETALLE_ERROR',
            ]);

            return redirect()
                ->route('pacientes.index')
                ->with('error', 'No fue posible cargar el paciente.');
        }
    }

    public function edit($id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            return view('pacientes.edit', compact('paciente'));

        } catch (Throwable $e) {
            Log::error('Error al cargar paciente para edición.', [
                'id_paciente' => $id,
                'codigo' => 'PACIENTE_EDICION_CARGA_ERROR',
            ]);

            return redirect()
                ->route('pacientes.index')
                ->with('error', 'No fue posible cargar el paciente.');
        }
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);

        $datos = $request->validate([
            'dpi' => [
                'nullable',
                'digits:13',
                Rule::unique('pacientes', 'dpi')
                    ->ignore($paciente->id_paciente, 'id_paciente'),
            ],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'sexo' => ['nullable', 'in:FEMENINO,MASCULINO,OTRO,NO_INDICA'],
            'telefono' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9]+$/'],
            'correo' => ['nullable', 'email', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:250'],
        ], [
            'dpi.digits' => 'El DPI debe contener exactamente 13 números.',
            'dpi.unique' => 'Ya existe otro paciente con este DPI.',
            'nombres.required' => 'Debes ingresar los nombres.',
            'apellidos.required' => 'Debes ingresar los apellidos.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.required' => 'Debes ingresar un número de teléfono.',
            'telefono.min' => 'El teléfono debe tener al menos 8 números.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
        ]);

        try {
            $paciente->update($datos);

            return redirect()
                ->route('pacientes.show', $paciente->id_paciente)
                ->with('success', 'Paciente actualizado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al actualizar paciente.', [
                'id_paciente' => $id,
                'codigo' => 'PACIENTE_ACTUALIZACION_ERROR',
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible actualizar el paciente.');
        }
    }

    public function destroy($id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            if (!$paciente->activo) {
                return redirect()
                    ->route('pacientes.index')
                    ->with('error', 'El paciente ya se encuentra inactivo.');
            }

            $paciente->update([
                'activo' => false,
            ]);

            return redirect()
                ->route('pacientes.index')
                ->with('success', 'Paciente desactivado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al desactivar paciente.', [
                'id_paciente' => $id,
                'codigo' => 'PACIENTE_DESACTIVACION_ERROR',
            ]);

            return back()
                ->with('error', 'No fue posible desactivar el paciente.');
        }
    }

    private function especialistaAutenticado(Request $request): ?Especialista
    {
        $usuario = $request->user();

        if (!$usuario || $usuario->rol?->nombre !== 'ESPECIALISTA') {
            return null;
        }

        $especialista = Especialista::where('id_usuario', $usuario->id_usuario)
            ->where('activo', true)
            ->first();

        abort_unless($especialista !== null, 403);

        return $especialista;
    }

    private function escaparLike(string $valor): string
    {
        return addcslashes($valor, '\\%_');
    }

    private function idDesdeNumeroExpediente(string $numeroExpediente): ?string
    {
        $numeroExpediente = strtoupper(trim($numeroExpediente));

        if (!preg_match('/^PAC-(\d{6,})$/', $numeroExpediente, $coincidencias)) {
            return null;
        }

        $idPaciente = ltrim($coincidencias[1], '0');

        return $idPaciente !== '' && ctype_digit($idPaciente) ? $idPaciente : null;
    }
}
