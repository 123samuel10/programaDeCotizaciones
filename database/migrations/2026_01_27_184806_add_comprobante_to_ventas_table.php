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
      Schema::table('ventas', function (Blueprint $table) {

            // Referencia que da el banco
            $table->string('referencia_pago')->nullable();

            // Ruta del archivo (capture / PDF)
            $table->string('comprobante_path')->nullable();

            // Fecha en que el cliente subió el comprobante
            $table->timestamp('comprobante_subido_en')->nullable();

            // Estado del comprobante
            // pendiente_revision | aceptado | rechazado
            $table->string('comprobante_estado')->nullable();

            // Nota del admin si rechaza
            $table->text('comprobante_nota_admin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'referencia_pago',
                'comprobante_path',
                'comprobante_subido_en',
                'comprobante_estado',
                'comprobante_nota_admin',
            ]);
        });
    }
};
