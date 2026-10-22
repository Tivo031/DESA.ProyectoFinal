<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_cita', function (Blueprint $table) {
            $table->char('dpi', 13)->nullable()->after('telefono');
            $table->string('numero_expediente', 20)->nullable()->after('dpi');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cita', function (Blueprint $table) {
            $table->dropColumn(['dpi', 'numero_expediente']);
        });
    }
};
