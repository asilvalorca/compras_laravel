<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{

    public $table = "compras_articulos";
    protected $primaryKey = 'id_articulo';
    public $timestamps = false;

    protected $fillable = [
        'codigo_cuenta',
        'nombrecuenta',
        'categoria',
        'articulo',
        'item_gasto',
        'condicion',
        'fecha_creacion',
        'estado',
        'tipo_articulo',
        'cuenta_contable',
        'id_estado_nuevo',
        'articulo_completo',
        'cuentaflex',
        'codigo_producto',
        'iva',
        'inventario',
        'codigo2',
        'bodegueable',
        'img',
        'ficha_tecnica',
        'certificacion',
        'sku',
        'prorrateo',
        'servicio'
    ];

}
