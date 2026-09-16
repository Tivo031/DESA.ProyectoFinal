<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioAdministradorSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        $rol = Rol::where('nombre', 'ADMINISTRADOR')
            ->firstOrFail();

        Usuario::firstOrCreate(
            [
                'correo' => env(
                    'SEED_ADMIN_EMAIL',
                    'admin@bajwam.test'
                ),
            ],
            [
                'id_rol' => $rol->id_rol,
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'telefono' => '5555-0000',
                'usuario' => env(
                    'SEED_ADMIN_USER',
                    'admin'
                ),
                'password' => Hash::make(
                    env(
                        'SEED_ADMIN_PASSWORD',
                        'Admin@12345'
                    )
                ),
                'activo' => true,
            ]
        );
    }
}