<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo')</title>
    <style>
        table.minimalistBlack {
            border: 1px solid #ccc;
            width: 100%;
            text-align: left;
            border-collapse: collapse;
        }

        table.minimalistBlack td,
        table.minimalistBlack th {
            border: 1px solid #ccc;
            padding: 5px 0px;
        }

        table.minimalistBlack tbody td {
            font-size: 1em;
        }

        table.minimalistBlack tbody tr {
            text-align: center;
        }

        table.minimalistBlack thead {
            background: #CFCFCF;
            background: -moz-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
            background: -webkit-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
            background: linear-gradient(to bottom, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
            border-bottom: 1px solid #000000;
        }

        table.minimalistBlack thead th {
            font-size: 1em;
            color: #ccc;
            text-align: center;
        }
    </style>
</head>

<body>

    <h1 style="">Asignación de Horarios </h1>
    <h4 style="">Centro de costo : {{$infoCentroCosto->nombre}} </h4>

    <div class="container">
        <table class="minimalistBlack">
            <tbody>

                <tr>
                    <th class="text-center">Funcionario</th>
                    @foreach(Carbon\CarbonPeriod::between($inicio,$fin) as $dia)
                    <th class="text-center" colspan="2">
                        {{$dia->format('Y-m-d')}}
                    </th>
                    @endforeach
                </tr>

                @if (count($funcionarios) > 0 )
                @foreach ($dependencias as $dependencia)

                <tr>
                    <th class="text-center" colspan="15">{{$dependencia->nombre}}</th>
                </tr>

                @foreach ($dependencia->funcionarios as $funcionario)
                <tr>
                    <th class="text-center">{{$funcionario->nombres}} {{$funcionario->apellidos}}</th>
                    @foreach ( $funcionario->horariosTurnoRotativo as $turno)
                    <th class="text-center">
                        @if ($turno->turnoRotativo != null)
                        {{$turno->turnoRotativo->hora_inicio_uno}}
                        @else
                        {{'Desc'}}
                        @endif
                    </th>
                    <th class="text-center">
                        @if ($turno->turnoRotativo != null)
                        {{$turno->turnoRotativo->hora_fin_uno}}
                        @else
                        {{'Desc'}}
                        @endif
                    </th>
                    @endforeach
                </tr>
                @endforeach
                @endforeach
                @else
                <tr>
                    <th class="text-center" colspan="15">No existen turnos asignados</th>
                </tr>
                @endif

            </tbody>
        </table>
    </div>

</body>

</html>