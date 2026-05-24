<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio', 'hora_fin', 'dias']);
            $table->json('horario')->nullable()->after('periodo')
                  ->comment('[{"dia":"L","hora_inicio":"07:00","hora_fin":"09:00"}, ...]');
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('horario');
            $table->time('hora_inicio')->nullable()->after('periodo');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->string('dias', 20)->nullable()->after('hora_fin');
        });
    }
};
