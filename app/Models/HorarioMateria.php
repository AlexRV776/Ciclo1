<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\ReservaAula;

class HorarioMateria extends Model
{
    protected $table = 'horario_materia';

    protected $fillable = [
        'grupo_materia_id',
        'horario_id',
        'nro'
    ];

    public function grupoMateria()
    {
        return $this->belongsTo(GrupoMateria::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'nro', 'nro');
    }

}