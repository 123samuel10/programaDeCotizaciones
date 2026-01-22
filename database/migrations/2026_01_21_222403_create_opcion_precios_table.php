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
   Schema::create('opcion_precios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('opcion_id')->constrained('opciones')->cascadeOnDelete();
    $table->decimal('precio_venta', 12, 2);
    $table->decimal('precio_costo', 12, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcion_precios');
    }
};
