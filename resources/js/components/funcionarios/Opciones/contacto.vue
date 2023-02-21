
<template>
  <div>
    <div class="row mt-3" v-if="funcionarioPropio.contactos_emergencia">
      <div class="col-md-12">
        <div class="row"></div>
        <div class="card">
          <div class="card-title pl-4 pt-4 row">
            <h5 class="titulo col-md-6">Contacto de Emergencia</h5>
            <div class="col-md-6 text-right">
              <button
                class="btn btn-link pt-0 edit"
                @click="mostarModal('datosContacto')"
              >
                <i class="simple-icon-pencil"></i> Editar
              </button>
            </div>
          </div>

          <div class="card-body pt-0">
            <div class="row d-flex justify-content-center">
              <div class="col-md-4 offset-1">
                <p class="font-weight-bold text-primary text-right text-mute">
                  Nombre Completo
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Parentesco
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Teléfono
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Celular
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Direccion de residencia
                </p>
              </div>

              <div class="col-md-6 offset-1 item-data">
                <p class="font-weight-bold">
                  {{
                    funcionarioPropio.contactos_emergencia.nombre_completo ||
                    "No registrado"
                  }}
                </p>
                <p class="font-weight-bold">
                  {{
                    funcionarioPropio.contactos_emergencia.parentesco ||
                    "No registrado"
                  }}
                </p>
                <p class="font-weight-bold">
                  {{
                    funcionarioPropio.contactos_emergencia.telefono ||
                    "No registrado"
                  }}
                </p>
                <p class="font-weight-bold">
                  {{
                    funcionarioPropio.contactos_emergencia.celular ||
                    "No registrado"
                  }}
                </p>
                <p class="font-weight-bold">
                  {{
                    funcionarioPropio.contactos_emergencia.direccion ||
                    "No registrado"
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <modal id="datosContacto" ref="datosContacto" :esLg="true">
        <template slot="titulo">Edición de datos básicos</template>
        <template slot="contenido">
          <datos-contacto
            ref="datosContacto"
            :funcionarioEditar="funcionarioPropio"
          ></datos-contacto>
        </template>
      </modal>
    </div>

    <div class="row" v-else>
      <div class="col-md-12">
        <div class="card">
          <div class="card-title pl-4 pt-4 row">
            <h1 class="titulo col-md-6">
              Sin datos de contacto para Emergencias
            </h1>
            <div class="col-md-6 text-right">
              <button
                class="btn btn-link pt-0 edit"
                @click="mostarModalRegistro('datosContacto')"
              >
                <i class="simple-icon-pencil"></i> Ingresar
              </button>
            </div>
          </div>
          <div class="card-body"></div>
        </div>
      </div>
    </div>

    <modal id="datosContacto" ref="datosContacto" :esLg="true">
      <template slot="titulo">
        Creación Datos de Contacto de Emergencia</template
      >
      <template slot="contenido">
        <datos-contacto-create
          ref="datosContacto"
          :funcionarioEditar="funcionarioPropio"
          @mensaje="mostrarMensaje"
          @update="updateInfo"
        ></datos-contacto-create>
      </template>
    </modal>

    <!-- <modal id="datosContactoEdit" ref="datosContactoEdit" :esLg="true">
      <template slot="titulo"> Edicón Datos de Contacto de Emergencia</template>
      <template slot="contenido">
        <datos-contacto
          ref="datosContactoEdit"
          :funcionarioEditar="funcionarioPropio"
          @mensaje="mostrarMensaje"
          @update="updateInfo"
        ></datos-contacto>
      </template>
    </modal> -->

    <notifications group="notificaciones" position="bottom right" />
  </div>
</template>

<script>
import Modal from "../../utiles/Modal";
import DatosContacto from "./../detalles/formularios/DatosContacto";
import DatosContactoCreate from "./../detalles/formularios/DatosContactoCreate";

export default {
  props: ["funcionario"],
  components: {
    Modal,
    DatosContactoCreate,
    DatosContacto,
  },

  data() {
    return {
      funcionarioPropio: this.funcionario,
    };
  },

  methods: {
    mostarModalRegistro(modal) {
      this.$refs[modal].mostrarModal();
    },
    mostarModal(modal) {
      this.$refs[modal].mostrarModal();
    },

    mostrarMensaje(mensaje) {
      console.log(mensaje);

      setTimeout(() => {
        this.$notify({
          group: "notificaciones",
          title: "Notificación:",
          text: mensaje,
        });
      }, 800);
    },

    updateInfo() {
      axios
        .get(
          `/api/${localStorage.getItem("tenant")}/funcionarios/${
            this.funcionario.identidad
          }/mostrar`
        )
        .then((datos) => {
          this.funcionarioPropio = datos.data;
        });
    },
  },
};
</script>
       