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
          Schema::table('seguimientos', function (Blueprint $table) {
            $table->string('awb')->nullable()->after('tipo_envio');                 // Air Waybill
            $table->string('aerolinea')->nullable()->after('awb');
            $table->string('aeropuerto_salida')->nullable()->after('aerolinea');
            $table->string('aeropuerto_llegada')->nullable()->after('aeropuerto_salida');
            $table->string('vuelo')->nullable()->after('aeropuerto_llegada');
            $table->string('tracking_url')->nullable()->after('vuelo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropColumn([
                'awb','aerolinea','aeropuerto_salida','aeropuerto_llegada','vuelo','tracking_url'
            ]);
        });
    }
};
