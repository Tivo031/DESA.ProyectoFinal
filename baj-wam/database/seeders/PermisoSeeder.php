<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permisos')->upsert(
            [
                // DASHBOARD
                [
                    'codigo' => 'dashboard.ver',
                    'nombre' => 'Ver dashboard',
                    'modulo' => 'DASHBOARD',
                    'descripcion' => 'Permite acceder al dashboard',
                    'activo' => true,
                ],

                // USUARIOS
                [
                    'codigo' => 'usuarios.ver',
                    'nombre' => 'Ver usuarios',
                    'modulo' => 'USUARIOS',
                    'descripcion' => 'Permite consultar usuarios',
                    'activo' => true,
                ],
                [
                    'codigo' => 'usuarios.crear',
                    'nombre' => 'Crear usuarios',
                    'modulo' => 'USUARIOS',
                    'descripcion' => 'Permite registrar nuevos usuarios',
                    'activo' => true,
                ],
                [
                    'codigo' => 'usuarios.editar',
                    'nombre' => 'Editar usuarios',
                    'modulo' => 'USUARIOS',
                    'descripcion' => 'Permite modificar usuarios',
                    'activo' => true,
                ],
                [
                    'codigo' => 'usuarios.desactivar',
                    'nombre' => 'Desactivar usuarios',
                    'modulo' => 'USUARIOS',
                    'descripcion' => 'Permite activar o desactivar usuarios',
                    'activo' => true,
                ],
                [
                    'codigo' => 'usuarios.restablecer_password',
                    'nombre' => 'Restablecer contraseña',
                    'modulo' => 'USUARIOS',
                    'descripcion' => 'Permite restablecer la contraseña de un usuario',
                    'activo' => true,
                ],

                    // ROLES
                [
                    'codigo' => 'roles.ver',
                    'nombre' => 'Ver roles',
                    'modulo' => 'ROLES',
                    'descripcion' => 'Permite consultar roles',
                    'activo' => true,
                ],
                [
                    'codigo' => 'roles.crear',
                    'nombre' => 'Crear roles',
                    'modulo' => 'ROLES',
                    'descripcion' => 'Permite registrar nuevos roles',
                    'activo' => true,
                ],
                [
                    'codigo' => 'roles.editar',
                    'nombre' => 'Editar roles',
                    'modulo' => 'ROLES',
                    'descripcion' => 'Permite modificar roles',
                    'activo' => true,
                ],
                [
                    'codigo' => 'roles.desactivar',
                    'nombre' => 'Desactivar roles',
                    'modulo' => 'ROLES',
                    'descripcion' => 'Permite activar o desactivar roles',
                    'activo' => true,
                ],
                [
                    'codigo' => 'roles.permisos',
                    'nombre' => 'Administrar permisos',
                    'modulo' => 'ROLES',
                    'descripcion' => 'Permite asignar y quitar permisos a los roles',
                    'activo' => true,
                ],

                // PACIENTES
                
                [
                    'codigo' => 'pacientes.ver',
                    'nombre' => 'Ver pacientes',
                    'modulo' => 'PACIENTES',
                    'descripcion' => 'Permite consultar pacientes',
                    'activo' => true,
                ],
                [
                    'codigo' => 'pacientes.crear',
                    'nombre' => 'Crear pacientes',
                    'modulo' => 'PACIENTES',
                    'descripcion' => 'Permite registrar pacientes',
                    'activo' => true,
                ],
                [
                    'codigo' => 'pacientes.editar',
                    'nombre' => 'Editar pacientes',
                    'modulo' => 'PACIENTES',
                    'descripcion' => 'Permite modificar pacientes',
                    'activo' => true,
                ],

                // CITAS
                [
                    'codigo' => 'citas.ver',
                    'nombre' => 'Ver citas',
                    'modulo' => 'CITAS',
                    'descripcion' => 'Permite consultar citas',
                    'activo' => true,
                ],
                [
                    'codigo' => 'citas.crear',
                    'nombre' => 'Crear citas',
                    'modulo' => 'CITAS',
                    'descripcion' => 'Permite registrar citas',
                    'activo' => true,
                ],
                [
                    'codigo' => 'citas.editar',
                    'nombre' => 'Editar citas',
                    'modulo' => 'CITAS',
                    'descripcion' => 'Permite modificar citas',
                    'activo' => true,
                ],
                [
                    'codigo' => 'citas.cancelar',
                    'nombre' => 'Cancelar citas',
                    'modulo' => 'CITAS',
                    'descripcion' => 'Permite cancelar citas',
                    'activo' => true,
                ],

                // SOLICITUDES
                [
                    'codigo' => 'solicitudes.ver',
                    'nombre' => 'Ver solicitudes',
                    'modulo' => 'SOLICITUDES',
                    'descripcion' => 'Permite consultar solicitudes de cita',
                    'activo' => true,
                ],
                [
                    'codigo' => 'solicitudes.procesar',
                    'nombre' => 'Procesar solicitudes',
                    'modulo' => 'SOLICITUDES',
                    'descripcion' => 'Permite aprobar o rechazar solicitudes',
                    'activo' => true,
                ],

                // CONSULTAS
                [
                    'codigo' => 'consultas.ver',
                    'nombre' => 'Ver consultas',
                    'modulo' => 'CONSULTAS',
                    'descripcion' => 'Permite consultar consultas',
                    'activo' => true,
                ],
                [
                    'codigo' => 'consultas.crear',
                    'nombre' => 'Crear consultas',
                    'modulo' => 'CONSULTAS',
                    'descripcion' => 'Permite registrar consultas',
                    'activo' => true,
                ],
                [
                    'codigo' => 'consultas.editar',
                    'nombre' => 'Editar consultas',
                    'modulo' => 'CONSULTAS',
                    'descripcion' => 'Permite modificar consultas',
                    'activo' => true,
                ],

                // PRODUCTOS
                [
                    'codigo' => 'productos.ver',
                    'nombre' => 'Ver productos',
                    'modulo' => 'PRODUCTOS',
                    'descripcion' => 'Permite consultar productos',
                    'activo' => true,
                ],
                [
                    'codigo' => 'productos.crear',
                    'nombre' => 'Crear productos',
                    'modulo' => 'PRODUCTOS',
                    'descripcion' => 'Permite registrar productos',
                    'activo' => true,
                ],
                [
                    'codigo' => 'productos.editar',
                    'nombre' => 'Editar productos',
                    'modulo' => 'PRODUCTOS',
                    'descripcion' => 'Permite modificar productos',
                    'activo' => true,
                ],
                [
                    'codigo' => 'productos.catalogo',
                    'nombre' => 'Administrar catálogo',
                    'modulo' => 'PRODUCTOS',
                    'descripcion' => 'Permite administrar el catálogo público',
                    'activo' => true,
                ],

                // INVENTARIO
                [
                    'codigo' => 'inventario.ver',
                    'nombre' => 'Ver inventario',
                    'modulo' => 'INVENTARIO',
                    'descripcion' => 'Permite consultar inventario',
                    'activo' => true,
                ],
                [
                    'codigo' => 'inventario.movimiento',
                    'nombre' => 'Registrar movimientos',
                    'modulo' => 'INVENTARIO',
                    'descripcion' => 'Permite registrar movimientos de inventario',
                    'activo' => true,
                ],
            ],
            ['codigo'],
            [
                'nombre',
                'modulo',
                'descripcion',
                'activo',
            ]
        );
    }
}