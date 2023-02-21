<template>
  <div>
    <div class="row mt-4" v-if="funcionario.experiencias_laborales.length">
      <!-- <div class="col-md-12">
      <div class="card">
        <div class="card-title pl-4 pt-4">
          <h6 class="titulo">Experiencia Laboral</h6>
        </div>
        <div class="card-body">
          <div
            class="row"
            v-for="experiencia in funcionario.experiencias_laborales"
            :key="experiencia.id"
          >
            <div class="col-md-4">
              <p class="font-weight-bold text-right text-mute">
                Nombre de la empresa
              </p>
              <p class="font-weight-bold text-right text-mute">Cargo</p>
              <p class="font-weight-bold text-right text-mute">
                Labores realizadas
              </p>
              <p class="font-weight-bold text-right text-mute">
                Teléfono de la empresa
              </p>
            </div>
            <div class="col-md-7 offset-1 item-data">
              <p class="font-weight-bold">
                {{ experiencia.nombre_empresa }}
              </p>
              <p class="font-weight-bold">{{ experiencia.cargo }}</p>
              <p class="font-weight-bold">{{ experiencia.labores }}</p>
              <p class="font-weight-bold">
                {{ experiencia.telefono_empresa }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div> -->

      <div class="col-md-12">
        <div class="row"></div>
        <div class="card">
          <div class="card-title pl-4 pt-4 row">
            <h5 class="titulo col-md-6">Experiencia Laboral</h5>
            <div class="col-md-6 text-right">
              <button
                class="btn btn-link pt-0 edit"
                @click="mostarModalRegistro('datosExperiencia')"
              >
                <i class="simple-icon-pencil"></i> Ingresar lll
              </button>
            </div>
          </div>

          <div class="card-body pt-0">
            <div
              class="row d-flex justify-content-center"
              v-for="experiencia in funcionario.experiencias_laborales"
              :key="experiencia.id"
            >
              <div class="col-md-4 offset-1">
                <p class="font-weight-bold text-primary text-right text-mute">
                  Nombre de la empresa
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Cargo
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Labores realizadas
                </p>
                <p class="font-weight-bold text-primary text-right text-mute">
                  Teléfono de la empresa
                </p>
              </div>

              <div class="col-md-6 offset-1 item-data">
                <p class="font-weight-bold">
                  {{ experiencia.nombre_empresa || "No registrado" }}
                </p>
                <p class="font-weight-bold">
                  {{ experiencia.cargo || "No registrado" }}
                </p>
                <p class="font-weight-bold">
                  {{ experiencia.labores || "No registrado" }}
                </p>
                <p class="font-weight-bold">
                  {{ experiencia.telefono_empresa || "No registrado" }}
                </p>
                <br />
                <br />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" v-else>
      <div class="col-md-12">
        <div class="card">
          <div class="card-title pl-4 pt-4 row">
            <h1 class="titulo col-md-6">Sin experiencia registrada</h1>
            <div class="col-md-6 text-right">
              <button
                class="btn btn-link pt-0 edit"
                @click="mostarModalRegistro('datosExperiencia')"
              >
                <i class="simple-icon-pencil"></i> Ingresar
              </button>
            </div>
          </div>
          <div class="card-body"></div>
        </div>
      </div>
    </div>

    <modal id="datosExperiencia" ref="datosExperiencia" :esLg="true">
      <template slot="titulo"> Registro de experiencia</template>
      <template slot="contenido">
        <datos-experiencia-create
          ref="datosExperiencia"
          :funcionarioEditar="funcionarioPropio"
          @mensaje="mostrarMensaje"
          @update="updateInfo"
        ></datos-experiencia-create>
      </template>
    </modal>
    <notifications group="notificaciones" position="bottom right" />
  </div>
</template>
         
       <script>
import Modal from "../../utiles/Modal";
import DatosExperiencia from "./../detalles/formularios/DatosExperiencia";
import DatosExperienciaCreate from "./../detalles/formularios/DatosExperienciaCreate";

export default {
  props: ["funcionario"],
  components: {
    Modal,
    DatosExperienciaCreate,
    DatosExperiencia,
  },

  data() {
    return {
      funcionarioPropio: this.funcionario,
    };
  },

  methods: {
    mostarModalRegistro(modal) {
      console.log(modal);
      this.$refs[modal].mostrarModal();
    },
    mostarModal(modal) {
      console.log(modal);

      this.$refs[modal].mostrarModal();
    },

    mostrarMensaje(mensaje) {
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
          console.log(datos.data);
          this.funcionarioPropio = datos.data;
        });
    },
  },
};
</script>
       