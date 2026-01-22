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
      Schema::create('cotizacion_opciones', function (Blueprint $table) {
    $table->id();
   $table->foreignId('cotizacion_id')
      ->constrained('cotizaciones')
      ->cascadeOnDelete();
   $table->foreignId('opcion_id')
      ->constrained('opciones')
      ->cascadeOnDelete();

    $table->integer('cantidad')->default(1);
    $table->decimal('subtotal_venta', 12, 2);
    $table->decimal('subtotal_costo', 12, 2);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_opciones');
    }
};
