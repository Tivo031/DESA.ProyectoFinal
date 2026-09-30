<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->increments('id_consulta');

            $table->unsignedInteger('id_cita')->unique();
            $table->unsignedInteger('id_usuario_registro');

            $table->dateTime('fecha_consulta')
                ->useCurrent();

            $table->text('motivo_consulta');

            $table->text('observaciones')
                ->nullable();

            $table->text('diagnostico')
                ->nullable();

            $table->text('tratamiento_realizado');

            $table->text('recomendaciones')
                ->nullable();

            $table->dateTime('fecha_actualizacion')
                ->useCurrent()
                ->useCurrentOnUpdate();

            $table->index(
                'fecha_consulta',
                'idx_consultas_fecha'
            );

            $table->foreign('id_cita')
                ->references('id_cita')
                ->on('citas');

            $table->foreign('id_usuario_registro')
                ->references('id_usuario')
                ->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};