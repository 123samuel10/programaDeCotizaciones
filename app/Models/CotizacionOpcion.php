<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CotizacionOpcion extends Model
{
  use HasFactory;

    protected $table = 'cotizacion_opciones';

    protected $fillable = [
        'cotizacion_id',
        'opcion_id',
        'cantidad',
        'subtotal_venta',
        'subtotal_costo',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function opcion()
    {
        return $this->belongsTo(Opcion::class);
    }
}
