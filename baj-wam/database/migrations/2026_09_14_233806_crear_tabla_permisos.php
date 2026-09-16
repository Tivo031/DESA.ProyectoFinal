<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->increments('id_permiso');

            $table->string('codigo', 100)->unique();
            $table->string('nombre', 100);
            $table->string('modulo', 50);
            $table->string('descripcion', 200)->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};