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
       Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete()
                ->unique(); // 1 seguimiento por venta

            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('proveedores')
                ->nullOnDelete();

            $table->string('pais_destino')->nullable();    // Colombia / México
            $table->string('tipo_envio')->default('maritimo'); // maritimo|aereo
            $table->string('incoterm')->nullable();        // EXW/FOB/CIF...

            // Pipeline principal del seguimiento
            // ordencompra|produccion|listoparaembarque|embarcado|entransito|arriboapuerto|aduana|liberado|entregado|cerrado
            $table->string('estado')->default('ordencompra');

            $table->date('etd')->nullable(); // fecha estimada salida
            $table->date('eta')->nullable(); // fecha estimada llegada

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
