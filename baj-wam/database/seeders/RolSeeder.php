<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'ADMINISTRADOR',
            'RECEPCION',
            'ESPECIALISTA',
            'INVENTARIO',
        ];

        foreach ($roles as $nombre) {
            DB::table('roles')->updateOrInsert(
                [
                    'nombre' => $nombre,
                ],
                [
                    'nombre' => $nombre,
                ]
            );
        }
    }
}