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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('marca');
            $table->string('modelo');
            $table->string('tipo'); // Nevera, Congelador, etc.
            $table->string('capacidad'); // Ej: 300 litros
            $table->string('peso'); // Ej: 75 kg
            $table->string('dimensiones'); // Ej: 180 x 70 x 65 cm
            $table->string('color');
            $table->decimal('precio', 12, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
