<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolCompra extends Model
{
    use HasFactory;

    public $table = "compras_sol_compra";
    protected $primaryKey = 'id_sol';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'fecha_emision',
        'ccosto',
        'anegocio',
        'id_anegocio'
    ];
}
