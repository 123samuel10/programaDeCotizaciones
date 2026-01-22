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
        'total_venta',
        'total_costo',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function items()
    {
        return $this->hasMany(CotizacionOpcion::class, 'cotizacion_id');
    }
}
