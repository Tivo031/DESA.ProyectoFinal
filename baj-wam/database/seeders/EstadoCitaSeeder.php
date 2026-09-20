<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCitaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            [
                'codigo' => 'PENDIENTE',
                'nombre' => 'Pendiente',
                'descripcion' => 'La cita se encuentra pendiente de confirmación.',
            ],
            [
                'codigo' => 'CONFIRMADA',
                'nombre' => 'Confirmada',
                'descripcion' => 'La cita ha sido confirmada.',
            ],
            [
                'codigo' => 'COMPLETADA',
                'nombre' => 'Completada',
                'descripcion' => 'La atención fue realizada.',
            ],
            [
                'codigo' => 'CANCELADA',
                'nombre' => 'Cancelada',
                'descripcion' => 'La cita fue cancelada.',
            ],
            [
                'codigo' => 'NO_ASISTIO',
                'nombre' => 'No asistió',
                'descripcion' => 'El paciente no asistió a la cita.',
            ],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_cita')->updateOrInsert(
                [
                    'codigo' => $estado['codigo'],
                ],
                $estado
            );
        }
    }
}