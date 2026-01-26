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
       Schema::table('cotizaciones', function (Blueprint $table) {
            $table->string('estado')->default('pendiente')->after('total_costo');
            // pendiente | aceptada | rechazada
            $table->timestamp('respondida_en')->nullable()->after('estado');
            $table->text('nota_cliente')->nullable()->after('respondida_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn(['estado','respondida_en','nota_cliente']);
        });
    }
};
