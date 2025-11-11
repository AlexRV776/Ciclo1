<h2 style="text-align:center; margin-bottom: 20px;">Reporte de Personal</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr style="background: #f0f0f0;">
            <th>Registro</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Fecha de Contrato</th>
            <th>Sueldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($personal as $p)
            <tr>
                <td>{{ $p->registro }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->correo }}</td>
                <td>{{ $p->rol->nombre ?? 'Sin rol' }}</td>
                <td>{{ $p->docente->fecha_contrato ?? 'No registrado' }}</td>
                <td>{{ $p->docente->sueldo ?? 'No registrado' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>