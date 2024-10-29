<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public $table = "compras_item";
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_sol',
        'itemnum',
        'subcuenta',
        'descrip',
        'cant',
        'cchica',
        'rfactura',
        'prioridad',
        'aprob',
        'comprador',
        'estado',
        'id_subcuenta',
        'importacion_leasing',
        'id_item',
        'id_articulo',
        'patente',
        'gastorecuperable',
        'prorrateo'
    ];
}
