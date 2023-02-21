<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>GeneticApp - Software de Recursos Humanos y Nómina</title>
</head>

<body id="app-container" class="rounded menu-default ltr">


    <h1>Hola</h1>

    <script type="text/javascript">
        function redirectGeneticApp(a) {
            setTimeout(function() {
                window.location = a
                document.getElementById("cargando").style.display = "none";
                document.getElementById("exito").style.display = "block";
                setTimeout(function() {
                    window.location = a
                }, 300);
            }, 1000);
        }
    </script>

    <?php

    $datos["usuario"] = "alberto_balcarcel@hotmail.com";
    $datos["password"] = "123456789";

    $data["usuario"] = $datos["usuario"];
    $data["password"] = $datos["password"];

    $ejemplo = base64_encode(json_encode($data));

    echo "<script>redirectGeneticApp('http://corvus.test/login?up=" . $ejemplo . "')</script>";
    ?>

</body>

</html>