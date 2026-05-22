<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('suscripciones')) return;

        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id('id_suscripcion');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->tinyInteger('plan')->default(0)->comment('0=basico, 1=mensual');
            $table->tinyInteger('est_suscripcion')->default(1)->comment('0=inactivo, 1=activo, 2=vencido, 3=gracia');
            $table->date('fec_inicio')->nullable();
            $table->date('fec_fin')->nullable();
            $table->date('fec_ultimo_pago')->nullable();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
