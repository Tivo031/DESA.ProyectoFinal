<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_productos', function (Blueprint $table) {
            $table->unsignedInteger('id_consulta');
            $table->unsignedInteger('id_producto');

            $table->decimal('cantidad_recomendada', 10, 2)
                ->nullable();

            $table->string('indicaciones', 500)
                ->nullable();

            $table->primary([
                'id_consulta',
                'id_producto',
            ]);

            $table->foreign('id_consulta')
                ->references('id_consulta')
                ->on('consultas');

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('productos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_productos');
    }
};