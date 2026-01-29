<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
   use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cotizacion_id',
        'user_id',
        'total_venta',
        'total_costo',
        'estado_venta',
        'metodo_pago',
        'pagada_en',
        'nota_cliente',
        'notas_internas',

          // ✅ FALTABAN (comprobante)
        'referencia_pago',
        'comprobante_path',
        'comprobante_subido_en',
        'comprobante_estado',
        'comprobante_nota_admin',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(VentaItem::class, 'venta_id');
    }


public function seguimiento()
{
    return $this->hasOne(Seguimiento::class, 'venta_id');
}

}
