<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    // 👇 Indica el nombre exacto de la tabla
    protected $table = 'usuarios';

    // 👇 Tu clave primaria es 'registro', no 'id'
    protected $primaryKey = 'registro';

    // 👇 Laravel no debe autoincrementar porque tu PK no es id
    public $incrementing = false;

    // 👇 Si es un entero
    protected $keyType = 'int';

    protected $fillable = [
        'registro',
        'nombre',
        'correo',
        'contrasena',
        'rol_id',
        'estado',
    ];

    protected $hidden = ['contrasena'];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
    public function tienePermiso($nombrePermiso)
    {
        return $this->rol && $this->rol->permisos->contains('nombre', $nombrePermiso);
    }
        // ✅ RELACIÓN CLAVE: permite acceder al perfil docente
    public function docente()
    {
        return $this->hasOne(Docente::class, 'registro', 'registro');
    }
}
