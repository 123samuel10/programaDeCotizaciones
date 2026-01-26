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
 Schema::create('ventaitems', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            // snapshot del producto (para que la venta no cambie si editas el producto luego)
            $table->string('nombre_producto')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();

            $table->unsignedInteger('cantidad')->default(1);

            // snapshot precios (venta/costo)
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
        Schema::dropIfExists('ventaitems');
    }
};
