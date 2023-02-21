<table class="table" cellspacing="0" cellpadding="0" style="width:800px;">
    <tr style="background-color:#64c0f8;">
        <td style="width:240px;padding:10px;-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;">
            <h4 style="color:#ffffff;margin:0;margin-bottom:2px;">Recuperación de contraseña</h4>
            <h5 style="color:#ffffff;margin:0;"></h5>
        </td>
        <td style="width:320px">
        </td>
        <td style="width:240px;text-align:right;padding:10px;-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;">
            <h4 style="color:#ffffff;margin:0;"><br>¿Has olvidado tu contraseña? No te preocupes, vamos a reestablecer una nueva.</h4>
        </td>
    </tr>
    <tr style="background:url();">
        <td colspan="3" style="padding:30px 150px;color: #000000;font-size: 18px;font-weight:600;">
            <p>Hola, {{$funcionario->nombres}} {{$funcionario->apellidos}} <strong></strong></p>
            <p style="text-align:justify;">Si no has solicitado un cambio de contraseña, no dudes en comunicarte con nuestro equipo de Ayuda Geneticapp inmediatamente escribiendo al siguiente correo: Geneticapp@genetic.com</p>
            <p style="text-align:justify;">Tu nueva contraseña es: {{$funcionario->identidad}}</p>
            <p><strong></strong>Por favor recuerda cambiarla al ingresar</p>
            <p><strong>¡Bendiciones!</strong></p>
            <p style="margin-top:50px;">Cordialmente,</p>
            <p>El Equipo de Recursos Humanos</p>
        </td>
    </tr>
    <tr style="height:55px;background-color:#E2E2E2;">
        <td colspan="3" style="text-align:center;-webkit-border-bottom-right-radius: 20px;-webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomright: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;">
            <p class="text-center" style="margin: 2px;font-size: 10px;">Email Generado desde </p>
            <img src="https://app.geneticapp.co/img/logo-login.svg" style="width:165px;" />
        </td>
    </tr>
</table>