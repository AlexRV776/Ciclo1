<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Grupos</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Reporte de Grupos</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grupos as $g)
                <tr>
                    <td>{{ $g->id }}</td>
                    <td>{{ $g->nombre }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
