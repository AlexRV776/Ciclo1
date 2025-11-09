<?Php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencia';

    protected $fillable = [
        'fecha',
        'modalidad',
        'estado',
        'docente_registro',
        'horario_materia_id'
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_registro', 'registro');
    }

    public function horarioMateria()
    {
        return $this->belongsTo(HorarioMateria::class);
    }
}