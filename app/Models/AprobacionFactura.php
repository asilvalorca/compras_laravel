<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprobacionFactura extends Model
{
    use HasFactory;

    public $table = "compras_aprobacion_factura";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'numero_oc',
        'tipo_documento',
        'numero_factura',
        'rut',
        'fecha_emision',
        'fecha_vencimiento',
        'monto',
        'aprobacion_sii',
        'fecha_aprobacion_sii',
        'aprobacion_encargado',
        'fecha_aprobacion_encargado',
        'comentario',
        'id_aprobador',
        'fecha_creacion'
    ];


}
