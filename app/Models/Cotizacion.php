<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

   protected $fillable = [
  'user_id',
  'total_venta',
  'total_costo',
  'estado',
  'respondida_en',
  'nota_cliente',
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

public function venta()
{
    return $this->hasOne(\App\Models\Venta::class, 'cotizacion_id');
}


}
