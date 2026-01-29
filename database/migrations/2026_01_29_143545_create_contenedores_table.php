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
     Schema::create('contenedores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seguimiento_id')
                ->constrained('seguimientos')
                ->cascadeOnDelete();

            $table->string('numero_contenedor')->nullable(); // ABCD1234567
            $table->string('bl')->nullable();                // Bill of Lading
            $table->string('naviera')->nullable();

            $table->string('puerto_salida')->nullable();
            $table->string('puerto_llegada')->nullable();

            $table->date('etd')->nullable();
            $table->date('eta')->nullable();

            // reservado|asignado|embarcado|entransito|arribado|aduana|liberado|entregado
            $table->string('estado')->default('reservado');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenedores');
    }
};
