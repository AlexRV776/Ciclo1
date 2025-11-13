<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docente';
    protected $primaryKey = 'registro';
    public $incrementing = false; // porque no es autoincremental
    protected $fillable = [
        'registro',
        'fecha_contrato',
        'especialidad',
        'sueldo',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'registro');
    }
    public function docente()
    {
        return $this->hasOne(Docente::class, 'registro', 'registro');
    }
}