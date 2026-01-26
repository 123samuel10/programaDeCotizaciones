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
     Schema::create('ventaitemopciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ventaitem_id')
                ->constrained('ventaitems')
                ->cascadeOnDelete();

            $table->foreignId('opcion_id')
                ->constrained('opciones')
                ->restrictOnDelete();

            // snapshot de nombre para mostrar rápido
            $table->string('nombre_opcion')->nullable();

            $table->unsignedInteger('cantidad')->default(1);

            $table->decimal('precio_unit_venta', 14, 2)->default(0);
            $table->decimal('precio_unit_costo', 14, 2)->default(0);

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
        Schema::dropIfExists('ventaitemopciones');
    }
};
