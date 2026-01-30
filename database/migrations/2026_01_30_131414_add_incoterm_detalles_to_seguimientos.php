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
            $table->json('incoterm_detalles')->nullable()->after('incoterm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropColumn('incoterm_detalles');
        });
    }
};
