<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class VerificarPermisosPacientes extends Command
{
    protected $signature = 'pacientes:verificar {usuario=admin}';
    protected $description = 'Comprueba rutas y permisos de Pacientes sin cambiar datos';

    public function handle(): int
    {
        $usuario = Usuario::with('rol.permisos')
            ->where('usuario', $this->argument('usuario'))->first();

        $this->line('Proyecto: '.base_path());
        $this->line('URL configurada: '.config('app.url'));
        $this->line('Entorno: '.app()->environment());

        if (!$usuario) {
            $this->error('No existe ese nombre de usuario en esta base de datos.');
            return self::FAILURE;
        }

        $this->line('Usuario: '.$usuario->usuario.' | Rol: '.($usuario->rol?->nombre ?? 'Sin rol'));
        $this->line('Cuenta activa: '.($usuario->activo ? 'SI' : 'NO'));
        $this->line('Rol activo: '.($usuario->rol?->activo ? 'SI' : 'NO'));
        $this->line('Cambio de clave pendiente: '.($usuario->debe_cambiar_password ? 'SI' : 'NO'));

        $filas = [];
        foreach (['pacientes.ver', 'pacientes.editar', 'pacientes.desactivar'] as $permiso) {
            $filas[] = [$permiso, Gate::forUser($usuario)->allows($permiso) ? 'SI' : 'NO'];
        }
        $this->table(['Permiso', 'Habilitado'], $filas);

        foreach (['pacientes.index', 'pacientes.edit', 'pacientes.estado'] as $nombre) {
            $ruta = Route::getRoutes()->getByName($nombre);
            $this->line($nombre.': '.($ruta ? implode('|', $ruta->methods()).' '.$ruta->uri() : 'NO EXISTE'));
        }

        $vista = file_get_contents(resource_path('views/pacientes/index.blade.php'));
        $this->line('Vista nueva instalada: '.(str_contains($vista, "pacientes.partials.acciones") ? 'SI' : 'NO'));
        $this->line('JavaScript instalado: '.(is_file(public_path('assets/js/pacientes-estado.js')) ? 'SI' : 'NO'));

        return self::SUCCESS;
    }
}
