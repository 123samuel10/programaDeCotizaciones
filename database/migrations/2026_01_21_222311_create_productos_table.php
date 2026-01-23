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

$table->string('marca');
$table->string('modelo');
$table->string('nombre_producto');

$table->text('descripcion')->nullable();
$table->string('foto')->nullable();

$table->unsignedTinyInteger('repisas_iluminadas')->nullable();

$table->string('refrigerante')->nullable();
$table->integer('longitud')->nullable();
$table->integer('profundidad')->nullable();
$table->integer('altura')->nullable();

$table->decimal('precio_base_venta', 12, 2);
$table->decimal('precio_base_costo', 12, 2);



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
