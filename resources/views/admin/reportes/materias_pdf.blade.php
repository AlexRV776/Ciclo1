<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Semestre</th>
        </tr>
    </thead>
    <tbody>
        @foreach($materias as $m)
        <tr>
            <td>{{ $m->sigla }}</td>
            <td>{{ $m->nombre }}</td>
            <td>{{ $m->semestre }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
