<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionItemOpcion extends Model
{
    protected $table = 'cotizacionitemopciones';

    protected $fillable = [
        'cotizacionitem_id',
        'opcion_id',
        'cantidad',
        'precio_venta',
        'precio_costo',
        'subtotal_venta',
        'subtotal_costo',
    ];

    public function item()
    {
        return $this->belongsTo(CotizacionItem::class, 'cotizacionitem_id');
    }

    public function opcion()
    {
        return $this->belongsTo(Opcion::class);
    }
}
