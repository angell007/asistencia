<div class="container form-registro mt-5">
    <div class="row">
        <div class="col-12">

        </div>
    </div>
    <form action="<?php echo get_site_url(); ?>/wp-content/plugins/triquinet-personalized/includes/register-vendor-form.php" method="post" enctype="multipart/form-data" id="form-register-new-user">

        <div class="row">
            <h2 class="col-sm-12 my-5 text-uppercase text-center">Registro de usuario</h2>
        </div>
        <div class="row ">
            <div class="col-sm-12 form-group fotografia-user-box mb-5">
                <div id="box-image-user">
                    <label for="fotografia">Foto de usuario / logo marca</label>
                    <img src="">
                    <input type="file" class="add-image file-input" name="fotografia-user" id="fotografia-user">
                    <?php wp_nonce_field('fotografia-user', 'fotografia-user'); ?>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-6 col-sm-12 form-group mt-5">
                <label for="nombre-usuario">Nombre de usuario / marca</label>

                <input type="text" class="form-control form-custom-dan " id="nombre-usuario" name="nombre-usuario" aria-describedby="textHelpNombreUsuario" placeholder="username" pattern="[A-Za-z0-9]{4,16}">
                <small id="textHelpNombreUsuario" class="form-text text-muted">Ingrese su nombre de usuario sin espacios, por ejemplo: username</small>

            </div>

            <div class="col-md-6 col-sm-12 form-group mt-5">
                <label for="correo-electronico">Correo Electrónico</label>
                <input type="email" class="form-control form-custom-dan" id="correo-electronico" name="correo-electronico" aria-describedby="textHelpCorreoElectronico" placeholder="example@example.com">
                <small id="textHelpCorreoElectronico" class="form-text text-muted">Ingrese su correo electrónico, por ejemplo: example@example.com</small>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-6 col-sm-12 form-group">
                <label for="contrasenia">Contraseña</label>
                <input type="password" class="form-control form-custom-dan" id="contrasenia" name="contrasenia" aria-describedby="textHelpContrasenia" value="">
                <small id="textHelpContrasenia" class="form-text text-muted">Ingrese su contraseña.</small>
            </div>

            <div class="col-md-6 col-sm-12 form-group">
                <label for="rep-contrasenia">Repetir Contraseña</label>
                <input type="password" class="form-control form-custom-dan" id="rep-contrasenia" name="rep-contrasenia" aria-describedby="textHelpRepContrasenia" value="">
                <small id="textHelpRepContrasenia" class="form-text text-muted">Repita su contraseña.</small>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-6 col-sm-12 form-group">
                <label for="nombres">Nombres</label>
                <input type="text" class="form-control form-custom-dan" id="nombres" name="nombres" aria-describedby="textHelpNombres" placeholder="Ingrese sus nombres..." value="">
                <small id="textHelpNombres" class="form-text text-muted">Ingrese sus nombres, por ejemplo: Ovidio Jose</small>
            </div>

            <div class="col-md-6 col-sm-12 form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" class="form-control form-custom-dan" id="apellidos" name="apellidos" aria-describedby="textHelpApellidos" placeholder="Ingrese sus apellidos..." value="">
                <small id="textHelpApellidos" class="form-text text-muted">Ingrese sus apellidos, por ejemplo: Arteaga Bracho</small>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-6 col-sm-12 form-group">
                <label for="numero-telefono">Número de teléfono</label>
                <input type="tel" class="form-control form-custom-dan" id="numero-telefono" name="numero-telefono" aria-describedby="textHelpNumeroTelefono" placeholder="123456789" value="">
                <small id="textHelpNumeroTelefono" class="form-text text-muted">Ingrese su número de teléfono, por ejemplo: 123456789</small>
            </div>

            <div class="col-md-6 col-sm-12 form-group">
                <label for="numero-celular">Número Celular</label>
                <input type="tel" class="form-control form-custom-dan" id="numero-celular" name="numero-celular" aria-describedby="textHelpNumeroCelular" placeholder="123456789" value="">
                <small id="textHelpNumeroCelular" class="form-text text-muted">Ingrese su número celular, por ejemplo: 123456789</small>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-6 col-sm-12 form-group">
                <label for="cedula-nit"> Cédula de ciudadanía, NIT, Cédula de Extranjería, y Pasaporte</label>
                <select name="personal-document-type" id="personal-document-type" class="custom-select">
                    <option value=""></option>
                    <option value="cc">CC</option>
                    <option value="nit">NIT</option>
                    <option value="ce">CE</option>
                    <option value="pp">PP</option>
                </select>
                <input type="number" class="form-control form-custom-dan" id="cedula-nit" name="cedula-nit" aria-describedby="textHelpCedulaNit" placeholder="123456789" value="">
                <small id="textHelpCedulaNit" class="form-text text-muted">Ingrese su Cédula de ciudadanía, NIT, Cédula de Extranjería, y Pasaporte.</small>
            </div>

            <div class="col-md-6 col-sm-12 form-group">
                <label for="direccion">Dirección</label>
                <input type="text" class="form-control form-custom-dan" id="direccion" name="direccion" aria-describedby="textHelpDireccion" placeholder="Escribe tu direccíon" value="">
                <small id="textHelpDireccion" class="form-text text-muted">Ingrese su dirección, por ejemplo: Calle Las Flores, Sector La Salina, Casa 24</small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12 form-group">
                <label for="user-departamento">Departamento</label>
                <select id="user-departamento" name="user-departamento" class="custom-select" aria-describedby="textHelpUserDepartamento">
                    <option value="">Seleccionar</option>
                    <?php
                    $departamentos = get_terms('departamentos', array(
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'hide_empty' => 0,
                    ));

                    foreach ($departamentos as $value) {
                        echo "<option value='" . $value->term_id . "' >" . $value->name . "</option>";
                    }
                    ?>
                </select>
                <small id="textHelpUserDepartamento" class="form-text text-muted">Seleccione, por ejemplo: Bogota.</small>
            </div>

            <div class="col-md-6 col-sm-12 form-group">
                <label for="user-ciudad">Ciudad </label>
                <select id="user-ciudad" name="user-ciudad" class="custom-select" aria-describedby="textHelpUserCiudad" disabled>
                    <option value="">Seleccionar</option>
                </select>
                <small id="textHelpUserCiudad" class="form-text text-muted">Seleccione, por ejemplo: Bogota.</small>
            </div>
        </div>

        <!--
      <hr>
      <div class="row">
        <h3 class="col-12">Documentos Comerciales</h3>
        <div class="col-md-6 col-sm-12 form-group">
          <label for="fotocopia-cedula">Fotocopia Cédula (PDF)</label>
          <input type="file" class="form-control form-custom-dan" id="fotocopia-cedula" name="fotocopia-cedula" aria-describedby="textHelpDireccion" placeholder="Fotocopia Cédula (PDF)" accept="application/pdf" >
          <?php wp_nonce_field('fotocopia-cedula', 'fotocopia-cedula'); ?>  
          <small id="textHelpDireccion" class="form-text text-muted">Fotocopia Cédula</small>
        </div>

        <div class="col-md-6 col-sm-12 form-group">
          <label for="document-rut">Documento RUT (PDF)</label>
          <input type="file" class="form-control form-custom-dan" id="document-rut" name="document-rut" aria-describedby="textHelpDireccion" placeholder="Documento RUT" accept="application/pdf" >
          <?php wp_nonce_field('document-rut', 'document-rut'); ?>  
          <small id="textHelpDireccion" class="form-text text-muted">Documento RUT</small>
        </div>

        <div class="col-md-6 col-sm-12 form-group">
          <label for="document-camara-comercio">Documento Cámara de Comercio (PDF)<br/><span class="text-solo-aplica">Cargar, solo si aplica.</span></label>
          <input type="file" class="form-control form-custom-dan" id="document-camara-comercio" name="document-camara-comercio" aria-describedby="textHelpDireccion" placeholder="Documento Cámara de Comercio" accept="application/pdf" >
          <?php wp_nonce_field('document-camara-comercio', 'document-camara-comercio'); ?>  
          <small id="textHelpDireccion" class="form-text text-muted">Documento Cámara de Comercio</small>
        </div>
      </div>
      <br />
      <div class="background-datos-bancarios"></div>
      <div class="row my-5 box caja-datos-bancarios">
        <h3 class="col-12">Datos Bancarios</h3>
        <div class="col-md-6 col-sm-12 form-group">
          <label for="Banco">Nombre del Banco</label>
          <select id="banco" name="banco" class="custom-select" aria-describedby="textHelpBanco" >
              <option value="" >Seleccionar</option>
              <?php

                $queryBancos = new WP_Query(array('post_type' => 'bancos', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'title'));
                if ($queryBancos->have_posts()) {
                    while ($queryBancos->have_posts()) {
                        $queryBancos->the_post();
                        $bancoCurrent = get_the_title();
                        echo "<option value='" . get_the_title() . "' >" . get_the_title() . "</option>";
                    }
                }
                ?>
            </select>
          <small id="textHelpBanco" class="form-text text-muted">Escribe el nombre del banco</small>
        </div>

        <div class="col-md-6 col-sm-12 form-group">
          <label for="tipo-cuenta">Tipo de cuenta</label>
          <select class="custom-select" id="tipo-cuenta" name="tipo-cuenta">
            <option value="">Seleccionar</option>
            <option value="ahorro">Ahorro</option>
            <option value="corriente">Corriente</option>
          </select>
          <small id="textHelpTipoCuenta" class="form-text text-muted">Indica el tipo de cuenta</small>
        </div>

        <div class="col-md-6 col-sm-12 form-group">
          <label for="numero-cuenta">Número de cuenta</label>
          <input type="text" class="form-control form-custom-dan" id="numero-cuenta" name="numero-cuenta" aria-describedby="textHelpNumeroCuenta" placeholder="NumeroCuenta" value="">
          <small id="textHelpNumeroCuenta" class="form-text text-muted">Escibe tu número de cuenta</small>
        </div>
      </div>
      -->

        <div class="background-terminos-condiciones"></div>
        <div class="row">
            <div class="col-12 mt-5">
                <p><a href="<?php echo get_site_url(); ?>/terminos-y-condiciones-de-triquinet">Leer Términos y Condiciones de Uso</a></p>
                <label><input type="checkbox" name="terminos-condiciones" id="terminos-condiciones">He leído y acepto los términos y condiciones de uso.</label>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-5">
                <span class="msg-error error" style="color:red; font-size:13.5px"></span>
                <div id="recaptcha" class="g-recaptcha" data-sitekey="6LdPrHIUAAAAABJszFdWo3GGPdOrproYkKDfoIed"></div>
            </div>
        </div>

        <div class="row button-submit">
            <div class="col-md-6 col-sm-12">
                <button class="btn btn-lg btn-register-new-user new-button" name="submit" id="submit">Registrarme</button>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6 col-sm-12">
                <p class="error-message" id="missing-image">* La imagen de usuario/marca es obligatoria.</p>
                <p class="error-message" id="missing-user">* El campo usuario es obligatorio.</p>
                <p class="error-message" id="missing-email">* El campo email es obligatorio.</p>

                <p class="error-message" id="email-exists">* El correo ya existe.</p>
                <p class="error-message" id="missing-user">* El campo usuario es obligatorio.</p>
                <p class="error-message" id="missing-password">* El campo password es obligatorio.</p>
                <p class="error-message" id="missing-repeat-password">* El campo repetir es obligatorio.</p>
                <p class="error-message" id="missing-nombres">* El campo nobmre es obligatorio.</p>
                <p class="error-message" id="missing-apellidos">* El campo apellidos es obligatorio.</p>
                <p class="error-message" id="missing-telefono">* El campo teléfono es obligatorio.</p>
                <p class="error-message" id="missing-celular">* El campo celular es obligatorio.</p>
                <p class="error-message" id="missing-tipo-documento">* El campo tipo de documento es obligatorio.</p>
                <p class="error-message" id="missing-documento">* El campo número de documento es obligatorio.</p>
                <p class="error-message" id="missing-direccion">* El campo dirección es obligatorio.</p>
                <p class="error-message" id="missing-departamento">* El campo departamento es obligatorio.</p>
                <p class="error-message" id="missing-ciudad">* El campo ciudad es obligatorio.</p>
                <p class="error-message" id="missing-terminos-condiciones">* Debes marcar el campo Términos y Condiciones de Uso.</p>
            </div>
        </div>
    </form>
</div>
<script src='https://www.google.com/recaptcha/api.js?hl=es'></script>