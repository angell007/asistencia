<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            justify-items: center;
            align-items: center;
            padding: 0;
            border: 1px solid #000;
            margin: auto;
            width: 100%;
            height: 1100px;
        }

        .page-content {
            margin: 0;
            padding: 0;
            position: relative;
        }

        .data {
            margin: 0;
            padding: 50px;
            text-align: justify;
        }

        .backg {
            margin: 0;
            padding: 0;
            position: absolute;
            left: 0px;
            top: 0px;
            z-index: -1;
            width: 100%;
            height: 100%;
        }

        .centrado {
            margin-top: 200px;
            text-align: center;
            padding: 8px;
        }

        .centrado-dirigido {
            text-align: center;
            padding: 8px;
        }

        p {
            margin: 20px 50px;
            text-align: justify;
        }

        center {
            margin: 16px 0;
        }

        .w-100 {
            margin: 80px 0;
            width: 100% !important
        }

        footer {
            /* background-color: black; */
            position: absolute;
            bottom: 0;
            /* width: 100%; */
            /* height: 40px; */
        }
    </style>

</head>

<body>

    <div class="page-content">

        <!-- <img class="backg" src="{{$empresa->imagen}}" alt=""> -->

        <p class="centrado" style="text-transform: uppercase; font-weight:bold"> {!!($formato->encabezado)!!}</p>

        <div class="w-100"></div>

        {!!($formato->cuerpo)!!}

        <div class="w-100"></div>
        <p style="margin:50px; font-weight:bold; font-style: italic;">Atentamente:</p>

        <table style="margin-top:20px; margin-left:50px">
            <tr>
                <td style="width:300px;padding-left:10px">
                    <img src="{{$formato->firma}}" width="200" height="50px" style="margin-left: 40px;"><br>
                    <table>
                        <tr>
                            <td style="width:300px;font-weight:bold; border-top:1px solid black; text-align:center;">
                                {!!$formato->responsable!!}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <footer style="height:55px;background-color:#D6EEFF;">
                <div colspan="3" style="text-align:center;-webkit-border-bottom-right-radius: 20px;-webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomright: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;">
                    <p class="text-center" style="margin: 2px;font-size: 10px;"> Generado desde </p>
                    <img src="https://app.geneticapp.co/img/logo-login.svg" style="width:150px;" />
                </div>
            </footer>

        </table>

    </div>

</body>

</html>