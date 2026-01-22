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
     Schema::create('cotizacionitemopciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacionitem_id')->constrained('cotizacionitems')->cascadeOnDelete();
            $table->foreignId('opcion_id')->constrained('opciones')->restrictOnDelete();

            $table->unsignedInteger('cantidad')->default(1);

            // snapshot de precio de la opción en el momento de cotizar
            $table->decimal('precio_venta', 14, 2)->default(0);
            $table->decimal('precio_costo', 14, 2)->default(0);

            $table->decimal('subtotal_venta', 14, 2)->default(0);
            $table->decimal('subtotal_costo', 14, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_item_opciones');
    }
};
