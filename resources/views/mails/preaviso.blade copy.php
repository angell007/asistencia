<table class="table"  cellspacing="0" cellpadding="0" style="width:800px;">
    <tr style="background-color:#FFD44C;">
        <td style="width:240px;padding:10px;-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;">
            <h4 style="color:#ffffff;margin:0;margin-bottom:2px;">{{$empresa->razon_social}}</h4>
            <h5 style="color:#ffffff;margin:0;">{{ number_format($empresa->numero_documento,0,",",".")}}-{{$empresa->digito_verificacion}}</h5>
        </td>
        <td style="width:320px">        
        </td>
        <td style="width:240px;text-align:right;padding:10px;-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;">
            <h4 style="color:#ffffff;margin:0;">Preaviso de<br>Terminación de Contrato</h4>
        </td>
    </tr>
    <tr style="background:url(https://app.geneticapp.co/img/fondo-email.svg) repeat;">
        <td colspan="3" style="padding:30px 150px;color: #000000;font-size: 18px;font-weight:600;">
            <p>Hola, <strong>{{ $empleado->nombres }} {{$empleado->apellidos}}</strong></p>
            <p style="text-align:justify;">Adjunto te hacemos llegar el preaviso de terminación de contrato que hemos mantenido desde {{ $inicio }}, te pido nos lo hagas llegar debidamente firmado.</p>
            <p style="text-align:justify;">Hasta el momento estamos felices de haber contado contigo en nuestra empresa, sin embargo hoy nos decimos hasta pronto, quizá nos volvamos a encontrar en el camino y queremos desearte desde nuestro corazón muchos éxitos en todo lo que emprendas.</p>
            <p><strong>¡Bendiciones!</strong></p>
            <p style="margin-top:50px;">Cordialmente,</p>
            <p>El Equipo de Recursos Humanos</p>
        </td>
    </tr>
    <tr  style="height:55px;background-color:#E2E2E2;">
        <td colspan="3" style="text-align:center;-webkit-border-bottom-right-radius: 20px;-webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomright: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-right-radius: 20px;border-bottom-left-radius: 20px;">
            <p class="text-center" style="margin: 2px;font-size: 10px;">Email Generado desde </p>
            <img src="https://app.geneticapp.co/img/logo-login.svg" style="width:165px;" /> 
        </td>
    </tr>
</table>