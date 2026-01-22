<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotizacion extends Model
{
     use HasFactory;

    protected $fillable = [
        'producto_id',
        'cliente_id',
        'total'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function opciones()
    {
        return $this->hasMany(CotizacionOpcion::class);
    }
}
