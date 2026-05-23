<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->time('hora_inicio')->nullable()->after('periodo');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->string('dias', 20)->nullable()->after('hora_fin')
                  ->comment('Ej: LMV, MA, LMJV');
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio', 'hora_fin', 'dias']);
        });
    }
};
