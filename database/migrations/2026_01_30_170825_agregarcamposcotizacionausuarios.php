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
          Schema::table('users', function (Blueprint $table) {
            $table->string('pais')->nullable()->after('empresa');
            $table->string('ciudad')->nullable()->after('pais');
            $table->string('direccion')->nullable()->after('ciudad');
            $table->string('telefono')->nullable()->after('direccion');
            $table->string('nit')->nullable()->after('telefono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pais','ciudad','direccion','telefono','nit']);
        });
    }
};
