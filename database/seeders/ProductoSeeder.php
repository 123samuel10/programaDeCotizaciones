<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Nevera LG 300L',
                'marca' => 'LG',
                'modelo' => 'GR-B422FL',
                'tipo' => 'Nevera',
                'capacidad' => '300 litros',
                'peso' => '75 kg',
                'dimensiones' => '180 x 70 x 65 cm',
                'color' => 'Blanco',
                'precio' => 1200000,
                'stock' => 5,
            ],
            [
                'nombre' => 'Congelador Samsung 500L',
                'marca' => 'Samsung',
                'modelo' => 'CF-500D',
                'tipo' => 'Congelador',
                'capacidad' => '500 litros',
                'peso' => '95 kg',
                'dimensiones' => '190 x 80 x 70 cm',
                'color' => 'Acero inoxidable',
                'precio' => 2500000,
                'stock' => 3,
            ],
            // Puedes agregar más productos...
        ];

        foreach($productos as $prod){
            Producto::create($prod);
        }
    }
}
