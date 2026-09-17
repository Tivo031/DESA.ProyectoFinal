<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'ADMINISTRADOR',
                'descripcion' => 'Acceso completo al sistema',
                'activo' => true,
            ],
            [
                'nombre' => 'RECEPCION',
                'descripcion' => 'Gestión de pacientes y citas',
                'activo' => true,
            ],
            [
                'nombre' => 'ESPECIALISTA',
                'descripcion' => 'Registro de consultas y tratamientos',
                'activo' => true,
            ],
            [
                'nombre' => 'INVENTARIO',
                'descripcion' => 'Gestión de productos e inventario',
                'activo' => true,
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                [
                    'nombre' => $rol['nombre'],
                ],
                [
                    'descripcion' => $rol['descripcion'],
                    'activo' => $rol['activo'],
                ]
            );
        }
    }
}