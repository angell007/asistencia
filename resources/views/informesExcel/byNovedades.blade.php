<div class="container">
    <table class="minimalistBlack">
        <thead class="thead-dark">
            <tr>
                <th>Nombre del funcionario</th>
                <th>Dependencia</th>
                <th>Centro de costos</th>
                <th>Novedades acumuladas</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($funcionarios as $funcionario)

            <tr>
                <td>{{ $funcionario->nombres }} {{ $funcionario->apellidos }}</td>
                <td>{{ $funcionario->contractultimate->cargo->dependencia->nombre }}</td>
                <td>{{ $funcionario->contractultimate->cargo->dependencia->centroCosto->nombre }}</td>
                <td>{{ $funcionario->novedades  }}</td>
            </tr>

            @endforeach

        </tbody>
    </table>
</div>