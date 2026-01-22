<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

   protected $fillable = [
    'user_id',
    'producto_id',
    'cantidad_producto',
    'total_venta',
    'total_costo',
];




    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }


public function items()
{
    return $this->hasMany(\App\Models\CotizacionItem::class);
}

public function usuario()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}

}
