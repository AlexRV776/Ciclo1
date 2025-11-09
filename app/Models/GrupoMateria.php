<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    protected $table = 'grupo_materia';

    protected $fillable = [
        'grupo_id',
        'materia_sigla',
        'docente_registro'
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_sigla', 'sigla');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_registro', 'registro');
    }
}