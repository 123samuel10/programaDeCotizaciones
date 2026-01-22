<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpcionPrecio extends Model
{
    use HasFactory;

    protected $table = 'opcion_precios';

    protected $fillable = ['opcion_id', 'precio_venta', 'precio_costo'];

    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'opcion_id');
    }
}
