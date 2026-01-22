<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opcion extends Model
{
    use HasFactory;

    // ✅ IMPORTANTE: forzar nombre de tabla en español
    protected $table = 'opciones';

    protected $fillable = [
        'producto_id',
        'nombre',
        'descripcion',
        'categoria',
        'es_predeterminada',
        'orden',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function precios()
    {
        return $this->hasMany(OpcionPrecio::class, 'opcion_id');
    }
}
