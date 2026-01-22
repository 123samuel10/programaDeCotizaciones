<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

protected $fillable = [
    'marca','modelo','nombre_producto','descripcion','foto',
    'repisas_iluminadas', // ✅
    'refrigerante','longitud','profundidad','altura',
    'precio_base_venta','precio_base_costo'
];

    public function opciones()
    {
        return $this->hasMany(Opcion::class);
    }
}
