<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pagos')) return;

        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_suscripcion');
            $table->string('paypal_order_id', 100)->nullable();
            $table->string('paypal_transaction_id', 100)->nullable();
            $table->decimal('mon_monto', 10, 2)->default(0);
            $table->tinyInteger('est_pago')->default(0)->comment('0=pendiente, 1=completado, 2=cancelado, 3=fallido');
            $table->date('fec_pago')->nullable();
            $table->string('tipo_pago', 50)->default('paypal');
            $table->timestamps();

            $table->foreign('id_suscripcion')->references('id_suscripcion')->on('suscripciones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
