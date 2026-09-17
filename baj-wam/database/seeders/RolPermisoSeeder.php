<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $asignaciones = [
                'ADMINISTRADOR' => [
                    '*',
                ],

                'RECEPCION' => [
                    'dashboard.ver',

                    'pacientes.ver',
                    'pacientes.crear',
                    'pacientes.editar',

                    'citas.ver',
                    'citas.crear',
                    'citas.editar',
                    'citas.cancelar',

                    'solicitudes.ver',
                    'solicitudes.procesar',
                ],

                'ESPECIALISTA' => [
                    'dashboard.ver',

                    'pacientes.ver',

                    'citas.ver',

                    'consultas.ver',
                    'consultas.crear',
                    'consultas.editar',

                    'productos.ver',
                ],

                'INVENTARIO' => [
                    'dashboard.ver',

                    'productos.ver',
                    'productos.crear',
                    'productos.editar',
                    'productos.catalogo',

                    'inventario.ver',
                    'inventario.movimiento',
                ],
                
            ];

            foreach ($asignaciones as $nombreRol => $codigosPermisos) {

                $rol = DB::table('roles')
                    ->where('nombre', $nombreRol)
                    ->first();

                if (!$rol) {
                    throw new RuntimeException(
                        "No existe el rol {$nombreRol}."
                    );
                }

                /*
                 * El administrador recibe todos los permisos.
                 */
                if ($codigosPermisos === ['*']) {
                    $permisos = DB::table('permisos')
                        ->where('activo', true)
                        ->pluck('id_permiso');
                } else {
                    $permisos = DB::table('permisos')
                        ->whereIn('codigo', $codigosPermisos)
                        ->where('activo', true)
                        ->pluck('id_permiso');
                }

                foreach ($permisos as $idPermiso) {
                    DB::table('rol_permiso')->insertOrIgnore([
                        'id_rol' => $rol->id_rol,
                        'id_permiso' => $idPermiso,
                    ]);
                }
            }
        });
    }
}