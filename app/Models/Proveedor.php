<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
   use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre','contacto','email','whatsapp','pais','ciudad','notas'
    ];

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'proveedor_id');
    }
}
