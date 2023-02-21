<!DOCTYPE html>
<html>

<body>

    <table class="table" cellspacing="0" cellpadding="0" style="width:800px;">
        <tr style="background-color:#FFD44C;">
            <td style="width:240px;padding:10px;-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;">
                <h4 style="color:#ffffff;margin:0;margin-bottom:2px;">{{ $correo->empresa }}</h4>
                <h5 style="color:#ffffff;margin:0;">{{ $correo->nit }}</h5>
            </td>
            <td style="width:320px">
            </td>
            <td style="width:240px;text-align:right;padding:10px;-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;">
                <h4 style="color:#ffffff;margin:0;">Registro de<br>Asistencia</h4>
            </td>
        </tr>
        <tr style="background:url(https://app.geneticapp.co/img/fondo-email.svg) repeat;">
            <td colspan="3" style="text-align:center;padding:15px;">
                <img src="{{ $correo->imagen }}" style="width:200px;height:200px;border-radius:50%;border:1px solid #ccc;" />
                <h4 style="margin:0;font-size:22px;">{{ $correo->nombre }}</h4>
                <h6 style="margin:0;font-size:16px;font-weight:100;">{{ $correo->cargo }}</h6>
                <h6 style="margin:0;font-size:16px;">{{ $correo->tipo }}</h6>
                <h6 style="margin:0;font-size:16px;color:#005C9E;">{{ $correo->hora }}</h6>
                <h6 style="margin:0;font-size:16px;color:#FF0000;">Temperatura {{ $correo->temperatura }}º</h6>
                <h6 style="margin:0;font-size:14px;color:#FF0000;">{{ $correo->tarde }}</h6>
            </td>
        </tr>
        <tr style="height:55px;background-color:#E2E2E2;">
            <td colspan="3" style="text-align:center;-webkit-border-bottom-right-radius: 20px;-webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomright: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;">
                <p class="text-center" style="margin: 2px;font-size: 10px;">Email Generado desde </p>
                <img src="https://app.geneticapp.co/img/logo-login.svg" style="width:165px;" />
            </td>
        </tr>
    </table>

</body>

</html>