<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaItem extends Model
{
    protected $table = 'ventaitems';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'nombre_producto',
        'marca',
        'modelo',
        'cantidad',
        'precio_unit_venta',
        'precio_unit_costo',
        'subtotal_venta',
        'subtotal_costo',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function opciones()
    {
        return $this->hasMany(VentaItemOpcion::class, 'ventaitem_id');
    }
}
