<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaItemOpcion extends Model
{
     protected $table = 'ventaitemopciones';

    protected $fillable = [
        'ventaitem_id',
        'opcion_id',
        'nombre_opcion',
        'cantidad',
        'precio_unit_venta',
        'precio_unit_costo',
        'subtotal_venta',
        'subtotal_costo',
    ];

    public function item()
    {
        return $this->belongsTo(VentaItem::class, 'ventaitem_id');
    }

    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'opcion_id');
    }
}
