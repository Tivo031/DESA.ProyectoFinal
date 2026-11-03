<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PacientesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')
            ->whereIn('nombre', ['ADMINISTRADOR', 'RECEPCION'])
            ->pluck('id_rol', 'nombre');

        if ($roles->count() !== 2) {
            throw new RuntimeException('Deben existir los roles ADMINISTRADOR y RECEPCION. No se modificaron permisos.');
        }

        $permisos = [
            'pacientes.ver' => ['Ver pacientes', 'Permite consultar pacientes'],
            'pacientes.crear' => ['Crear pacientes', 'Permite registrar pacientes'],
            'pacientes.editar' => ['Editar pacientes', 'Permite modificar pacientes'],
            'pacientes.desactivar' => ['Activar y desactivar pacientes', 'Permite cambiar el estado sin eliminar el historial'],
        ];

        DB::transaction(function () use ($roles, $permisos) {
            foreach ($permisos as $codigo => [$nombre, $descripcion]) {
                DB::table('permisos')->updateOrInsert(
                    ['codigo' => $codigo],
                    ['nombre' => $nombre, 'modulo' => 'PACIENTES', 'descripcion' => $descripcion, 'activo' => true]
                );

                $idPermiso = DB::table('permisos')->where('codigo', $codigo)->value('id_permiso');

                foreach ($roles as $idRol) {
                    DB::table('rol_permiso')->insertOrIgnore([
                        'id_rol' => $idRol,
                        'id_permiso' => $idPermiso,
                    ]);
                }
            }
        });

        $this->command?->info('Permisos de Pacientes configurados para ADMINISTRADOR y RECEPCION.');
        $this->command?->info('No se eliminaron asignaciones ni se modificaron otros modulos.');
    }
}
