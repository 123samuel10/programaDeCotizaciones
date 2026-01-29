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
    Schema::create('seguimientoeventos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seguimiento_id')
                ->constrained('seguimientos')
                ->cascadeOnDelete();

            $table->foreignId('creado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('tipo')->default('general'); // general|produccion|embarque|aduana|entrega...
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_evento')->nullable();

            // Archivos opcionales (PDF, imagen, etc.)
            $table->string('archivo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientoeventos');
    }
};
