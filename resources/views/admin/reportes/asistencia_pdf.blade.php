<h2 style="text-align:center; margin-bottom: 20px;">Reporte de Asistencia</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr style="background: #f0f0f0;">
            <th>Fecha</th>
            <th>Docente</th>
            <th>Materia</th>
            <th>Estado</th>
            <th>Modalidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($asistencias as $a)
            <tr>
                <td>{{ $a->fecha }}</td>
                <td>{{ $a->docente->usuario->nombre ?? 'No registrado' }}</td>
                <td>{{ $a->horarioMateria->grupoMateria->materia->nombre ?? 'Sin materia' }}</td>
                <td>{{ ucfirst($a->estado) }}</td>
                <td>{{ ucfirst($a->modalidad) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

