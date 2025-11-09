<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaAula extends Model
{
    protected $table = 'reservas_aulas';

    protected $fillable = [
        'usuario_registro',
        'aula_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'registro');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id', 'nro');
    }
}