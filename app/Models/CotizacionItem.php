<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionItem extends Model
{
    protected $table = 'cotizacionitems';

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'cantidad',
        'precio_base_venta',
        'precio_base_costo',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function opciones()
    {
        return $this->hasMany(CotizacionItemOpcion::class, 'cotizacionitem_id');
    }
}
