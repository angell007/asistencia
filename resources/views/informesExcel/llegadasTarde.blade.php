<div class="container">
    <table class="minimalistBlack">
        <thead class="thead-dark">
            <tr>
                <th>Fecha</th>
                <th>Entrada de turno</th>
                <th>Entrada de real</th>
                <th>Tiempo de retraso</th>
                <th>Nombre del funcionario</th>
                <th>Dependencia</th>
                <th>Centro de costos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($llegadasTarde as $llegada)
            <tr>
                <td>{{ $llegada->fecha }}</td>

                <td>{{ $llegada->entrada_turno }}</td>
                <td>{{ $llegada->entrada_real }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($llegada->entrada_turno)->diffInMinutes(\Carbon\Carbon::parse($llegada->entrada_real)) }}
                </td>

                <td>{{ $llegada->funcionario->nombres }} {{ $llegada->funcionario->apellidos }}</td>
                <td>{{ $llegada->funcionario->dependencia->nombre }}</td>
                <td>{{ $llegada->funcionario->dependencia->centroCosto->nombre }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>