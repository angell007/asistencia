<template>
  <div>
    <nav class="navbar fixed-top">
      <div class="d-flex align-items-center navbar-left">
        <a href="#" class="menu-button d-none d-md-block" @click.prevent="ocultarSidebar">
          <svg class="main" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 17">
            <rect x="0.48" y="0.5" width="7" height="1" />
            <rect x="0.48" y="7.5" width="7" height="1" />
            <rect x="0.48" y="15.5" width="7" height="1" />
          </svg>
          <svg class="sub" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 17">
            <rect x="1.56" y="0.5" width="16" height="1" />
            <rect x="1.56" y="7.5" width="16" height="1" />
            <rect x="1.56" y="15.5" width="16" height="1" />
          </svg>
        </a>

        <a
          href="#"
          class="menu-button-mobile d-xs-block d-sm-block d-md-none"
          @click.prevent="ocultarSidebar"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 17">
            <rect x="0.5" y="0.5" width="25" height="1" />
            <rect x="0.5" y="7.5" width="25" height="1" />
            <rect x="0.5" y="15.5" width="25" height="1" />
          </svg>
        </a>
        <img :src="`${myempresa}`" class="logo-cliente" />
      </div>

      <a class="navbar-logo" href="#">
        <span class="logo d-block"></span>
        <span class="logo-mobile d-none d-xs-block"></span>
      </a>

      <div class="navbar-right">
        <div class="header-icons d-inline-block align-middle">
          <!-- <div class="position-relative d-inline-block">
          <button
            @click="mostarModal('NuevoTicket')"
            class="header-icon btn btn-empty"
            type="button"
            aria-expanded="false"
          >
            <i class="iconsmind-Support"></i>
          </button>


          </div>-->

          <div class="position-relative d-inline-block">
            <button
              class="header-icon btn btn-empty"
              type="button"
              id="notificationButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="simple-icon-bell"></i>
              <span class="count">0</span>
            </button>
            <div
              class="dropdown-menu dropdown-menu-right mt-3 scroll position-absolute"
              id="notificationDropdown"
            >
              <div class="d-flex flex-row mb-3 pb-3 border-bottom"></div>
            </div>
          </div>

          <div class="position-relative d-inline-block">
            <a
              class="header-icon btn btn-empty"
              type="button"
              id="notificationButton"
              href="https://www.google.com/"
              target="_blank"
              title="Herramientas Geneticapp"
            >
              <i class="simple-icon-settings"></i>
            </a>
          </div>

          <div class="position-relative d-inline-block">
            <a
              class="header-icon btn btn-empty"
              type="button"
              id="notificationButton"
              href="https://www.google.com/"
              target="_blank"
              title="Soporte Geneticapp"
            >
              <i class="simple-icon-earphones-alt"></i>
            </a>
          </div>
        </div>

        <div class="user d-inline-block">
          <button
            class="btn btn-empty p-0"
            type="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <span>
              {{ Funcionario.Nombre }}
              <img
                :src="`${Funcionario.Imagen}`"
                class="img-funcionario"
                alt
              />
            </span>
          </button>

          <div class="dropdown-menu dropdown-menu-right mt-3">
            <a class="dropdown-item" href="#" @click.prevent="cerrarSesion">Cerrar Sesión</a>
            <a
              class="dropdown-item"
              href="https://www.geneticapp.co/terminos-y-condiciones-y-politica-de-proteccion-de-datos-personales/"
              target="_blank"
            >Términos y Condiciones</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import Modal from "./utiles/Modal";
export default {
  components: {
    Modal
  },
  data() {
    return {
      Funcionario: {
        Nombre: "",
        Imagen: ""
      },
      Empresa: {
        Nombre: "",
        Imagen: ""
      },
      Imagen: ""
    };
  },
  created() {
    this.getInformacion();
    this.ocultarSidebar();
    eventEmitter.$on("updateImagen", () => {
      this.getInformacion();
    });
  },

  methods: {
    getInformacion() {
      var usuario = JSON.parse(localStorage.getItem("usuario"));
      var empresa = JSON.parse(localStorage.getItem("empresa"));

      this.Funcionario.Nombre =
        usuario.nombres.split(" ")[0] + " " + usuario.apellidos.split(" ")[0];
      this.Funcionario.Imagen = usuario.image
        ? usuario.image
        : "/img/robot.jpg";

      this.Empresa.Nombre = empresa.razon_social;
      if (empresa.imagen != "") {
        this.myempresa = empresa.imagen;
      }
    },

    ocultarSidebar() {
      eventEmitter.$emit("ocultarSidebar", false);
    },
    mostarModal(referencia) {
      setTimeout(() => {
        this.$refs[referencia].mostrarModal();
      }, 500);
    },
    cerrarSesion() {
      const token = localStorage.getItem("token");
      axios
        .post(
          "/api/auth/logout",
          {},
          { headers: { Authorization: "Bearer " + token } }
        )
        .then(respuesta => {
          localStorage.removeItem("token");
          localStorage.removeItem("tenant");
          localStorage.removeItem("empresa");
          localStorage.removeItem("usuario");
          this.$emit("logout");
        })
        .catch(error => {
          if (error.response.status == 400) {
            this.$swal.fire(
              "Error!",
              "No se encuentra autorizado para realizar esta acción",
              "error"
            );
          }
        });
    }
  }
};
</script>
<style>
</style>

