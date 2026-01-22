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
       Schema::table('opciones', function (Blueprint $table) {
            $table->string('categoria')->nullable()->after('nombre'); // Accesorios, Refrigeración, CO2-Control, Logística...
            $table->boolean('es_predeterminada')->default(false)->after('categoria');
            $table->integer('orden')->default(0)->after('es_predeterminada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opciones', function (Blueprint $table) {
            $table->dropColumn(['categoria','es_predeterminada','orden']);
        });
    }
};
