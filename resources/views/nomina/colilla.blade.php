<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <style>
        @font-face {
            font-family: 'Montserrat';
            /* src: url({{ storage_path('fonts/Montserrat-Regular.ttf')}}) format("truetype"); */
        }

        table thead,
        tbody {
            font-family: 'Montserrat';
        }

        .table-top,
        .table-resumen-pago {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
            margin: 0;
        }

        .table-top td,
        th {
            border: 1px solid #ddd;
            text-align: left;

        }

        .table-top th {
            padding: 12px;
            text-align: center;

        }

        .table-top td {
            padding: 2px;
            text-align: center;

        }

        .table-resumen-pago,
        td,
        th {
            border: 1px solid #ddd;
            text-align: left;
            padding: 5px;
        }

        .table-resumen-pago thead th:last-child {
            text-align: right;
        }

        .table-resumen-pago tbody td:last-child {
            text-align: right;
        }

        .table-retenciones-deducciones {
            border-collapse: collapse;
            width: 99%;
            font-size: 12px;
            margin-left: 5px;
        }

        .table-retenciones-deducciones thead th:last-child {
            text-align: right;
        }

        .table-retenciones-deducciones tbody td:last-child {
            text-align: right;
        }

        .table-retenciones-ingresos {
            border-collapse: collapse;
            width: 99%;
            font-size: 12px;
        }

        .table-retenciones-ingresos thead th:last-child {
            text-align: right;
        }

        .table-retenciones-ingresos tbody td:last-child {
            text-align: right;
        }

        .resume-title {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .resume-title-left {
            display: flex;
            align-items: center;
            justify-content: left;
        }

        .offset {
            width: 7%;
        }

        .tabla {
            display: table;
            width: 100%;
        }

        .fila {
            display: table-row;
        }

        .columna {
            display: table-cell;
            width: 50%;
        }

        h2,
        h3,
        small {
            font-weight: 500;
            text-align: center;
            font-family: 'Montserrat';
        }

        footer {
            margin-top: 30px;
        }

        footer p {
            font-size: 12px;
            text-align: center;
            font-family: 'Montserrat';
        }
    </style>

    <title>Colilla de Pago</title>
</head>

<body class="bg-white">
    <div class="container">

        <h2>Colilla de Pago</h2>


        <div class="resume-title">
            <table class="table-top">
                <thead>
                    <tr class="text-center">
                        <th>Empresa</th>
                        <th>Funcionario</th>
                        <th>Periodo de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td>
                            <p>{{$funcionario['empresa']}}</p>
                            <p>{{$funcionario['tipo_doc']}} : {{$funcionario['num_doc']}}</p>
                        </td>
                        <td>
                            <p> {{$funcionario['nombres']}} {{$funcionario['apellidos']}}</p>
                            <p> C.C {{$funcionario['identidad']}}</p>
                            <p> <small class="font-weight-bold">{{$funcionario['cargo']}}</small></p>
                        </td>
                        <td>
                            <p> {{$funcionario['inicio']}} al {{$funcionario['fin']}}</p>
                            <p>Días trabajados: {{$funcionario['periodo']}}</p>
                            <p>Salario base: @money($funcionario['salario_base']) </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <h3 class="text-center font-weight-bold">Resumen de pago</h3>


        <div class="resume-title">
            <table class="table-resumen-pago">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Salario</td>
                        <td class="text-right">@money($funcionario['salario_periodo'])</td>
                    </tr>
                    <tr>
                        <td>Auxilio de transporte</td>
                        <td class="text-right">@money($funcionario['auxilio_transporte'])</td>
                    </tr>
                    <tr>
                        <td>Adiccionales</td>
                        <td class="text-right">@money($funcionario['adicionales'])</td>
                    </tr>
                    <tr>
                        <td>Retenciones y deducciones</td>
                        <td class="text-right"> - @money($funcionario['deducciones'] + $funcionario['retenciones'])</td>
                    </tr>
                    <tr>
                        <td>Novedades</td>
                        <td class="text-right">@money($funcionario['novedades']['valor_total'])</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold bg-light">Total neto a pagar</td>
                        <td class="text-right font-weight-bold bg-light">@money($funcionario['salario_neto'])</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tabla" style="margin:auto;text-align:center;">
            <div class="fila">

                <div class="columna">
                    <h4 class="font-weight-bold text-center">Novedades</h4>
                    <div class="resume-title">

                        <table class="table-retenciones-deducciones">

                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-right">Valor</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach( $funcionario['novedades']['novedades_totales'] as $key => $novedad )
                                <tr>
                                    <td>{{$key}}</td>
                                    <td>@money($novedad)</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>


                    </div>
                </div>

            </div>
        </div>

        <div class="tabla" style="margin:auto;text-align:center;">
            <div class="fila">
                <div class="columna">
                    <h4 class="font-weight-bold text-center">Ingresos</h4>
                    <div class="resume-title">

                        <table class="table-retenciones-deducciones">

                            <thead>
                                <tr>
                                    <th class="text-right">Concepto</th>
                                    <th class="text-right">Valor</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>Horas extras</td>
                                    <td>{{$funcionario['horas_extras']}}</td>
                                </tr>

                                <tr>
                                    <td>Adiccionales</td>
                                    <td>@money($funcionario['adicionales'])</td>
                                </tr>

                            </tbody>
                        </table>


                    </div>
                </div>

                <div class="columna">
                    <h4 class="font-weight-bold text-center">Retenciones y deducciones</h4>

                    <div class="resume-title">

                        <table class="table-retenciones-ingresos">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>Deducciones</td>
                                    <td>@money($funcionario['deducciones'])</td>
                                </tr>

                                <tr>
                                    <td>Salud</td>
                                    <td>@money($funcionario['comodin']['total_retenciones']['Salud'])</td>
                                </tr>

                                <tr>
                                    <td>Pensión</td>
                                    <td>@money($funcionario['comodin']['total_retenciones']['Pensión'])</td>
                                </tr>

                                <tr>
                                    <td>Fondo de solidaridad :</td>
                                    <td> @money($funcionario['comodin']['total_retenciones']['Fondo de solidaridad'])</td>
                                </tr>

                                <tr>
                                    <td> Fondo de subsistencia</td>
                                    <td>@money($funcionario['comodin']['total_retenciones']['Fondo de subsistencia'])
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <footer>
            <p class="text-center font-weight-bold text-muted">GeneticApp © 2020. Software de gestión de empleados y
                nómina en la nube<br> https://geneticapp.co</p>
        </footer>
</body>

</html>