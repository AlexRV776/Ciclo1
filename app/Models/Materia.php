<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia'; // Nombre exacto de tu tabla

    protected $primaryKey = 'sigla'; // Clave primaria personalizada

    public $incrementing = false; // No es autoincremental

    protected $keyType = 'string'; // Tipo de dato del primary key

    protected $fillable = [
        'sigla',
        'nombre',
        'semestre',
    ];
}