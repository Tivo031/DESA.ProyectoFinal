<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            [
                'nombre' => 'Acupuntura',
                'descripcion' => 'Sesión de acupuntura tradicional.',
                'duracion_minutos' => 60,
                'precio' => null,
                'activo' => 1,
            ],
            [
                'nombre' => 'Acupuntura láser',
                'descripcion' => 'Tratamiento mediante acupuntura láser.',
                'duracion_minutos' => 45,
                'precio' => null,
                'activo' => 1,
            ],
            [
                'nombre' => 'Quiropráctico',
                'descripcion' => 'Atención quiropráctica.',
                'duracion_minutos' => 60,
                'precio' => null,
                'activo' => 1,
            ],
            [
                'nombre' => 'Kinesiología',
                'descripcion' => 'Sesión de kinesiología.',
                'duracion_minutos' => 60,
                'precio' => null,
                'activo' => 1,
            ],
            [
                'nombre' => 'Botánica',
                'descripcion' => 'Orientación relacionada con medicina botánica.',
                'duracion_minutos' => 60,
                'precio' => null,
                'activo' => 1,
            ],
            [
                'nombre' => 'Terapia nutricional',
                'descripcion' => 'Consulta y seguimiento nutricional.',
                'duracion_minutos' => 60,
                'precio' => null,
                'activo' => 1,
            ],
        ];

        foreach ($servicios as $servicio) {
            DB::table('servicios')->updateOrInsert(
                [
                    'nombre' => $servicio['nombre'],
                ],
                $servicio
            );
        }
    }
}