<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Esta migración existe por compatibilidad con entornos que corrieron
 * la migración original sin la columna clave.
 * Si la columna ya existe (create_sesiones_table actualizado), no hace nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sesiones', 'clave')) {
            Schema::table('sesiones', function (Blueprint $table) {
                $table->string('clave', 20)->nullable()->default(null)->after('id_grupo');
            });
        } else {
            // Asegurar que la columna existente acepta NULL
            Schema::table('sesiones', function (Blueprint $table) {
                $table->string('clave', 20)->nullable()->default(null)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('sesiones', function (Blueprint $table) {
            $table->dropColumn('clave');
        });
    }
};
