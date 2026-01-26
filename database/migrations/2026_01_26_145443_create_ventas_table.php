<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            // Relación con cotización (1 venta por cotización)
            $table->foreignId('cotizacion_id')
                ->constrained('cotizaciones')
                ->cascadeOnDelete()
                ->unique();

            // Redundante útil para reportes (cliente)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Totales (guardamos venta y costo por si luego quieres reportes internos)
            $table->decimal('total_venta', 14, 2)->default(0);
            $table->decimal('total_costo', 14, 2)->default(0);

            // Estado de la venta (CRM real)
            // pendiente_pago | pagada | cancelada
            $table->string('estado_venta')->default('pendiente_pago');

            $table->string('metodo_pago')->nullable();   // efectivo | transferencia | etc
            $table->timestamp('pagada_en')->nullable();

            $table->text('nota_cliente')->nullable();    // la nota que dejó el cliente en la cotización
            $table->text('notas_internas')->nullable();  // notas del admin

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
