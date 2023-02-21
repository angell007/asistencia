<template>
  <div class="container">
    <div v-if="mostrarInfo">
      <div class="row">
        <div class="col-12 mb-5">
          <header class="card">
            <div class="card-body">
              <div class="row pt-4">
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-4 col-xs-12 text-center">
                      <img
                        :src="rutaImagen"
                        class="img-responsive"
                        id="img-funcionario"
                      />
                    </div>

                    <div class="col-md-8 col-xs-12 text-xs-center">
                      <h4 class="font-weight-bold">
                        {{ funcionario.nombres }} {{ funcionario.apellidos }}
                      </h4>
                      <p>
                        <span id="employee-position">{{
                          funcionario.contractultimate.cargo.nombre
                        }}</span>
                      </p>
                      <p class="font-weight-bold">
                        {{
                          funcionario.contractultimate.cargo.dependencia.nombre
                        }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-md-2 text-center">
                  <p class="pb-0 mb-0 font-weight-bold text-mute">
                    Salario base
                  </p>
                  <img
                    src="/img/coin-icon.svg"
                    alt="coin"
                    class="svg-icon mx-auto"
                  />
                  <span class="salary-vacations-item">{{
                    funcionario.contractultimate.salario | moneda
                  }}</span>
                </div>

                <div class="col-md-2 text-center">
                  <p class="pb-0 mb-0 font-weight-bold text-mute">
                    Vacaciones acumuladas
                  </p>
                  <img
                    src="/img/vacations-icon.svg"
                    alt="vacations"
                    class="svg-icon mx-auto"
                  />
                  <span class="salary-vacations-item">{{
                    funcionario.contractultimate.vacaciones_acumuladas
                  }}</span>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-12 text-right">
                  <button
                    class="btn btn-secondary default btn-xs dropdown-toggle"
                    type="button"
                    data-toggle="dropdown"
                    aria-expanded="false"
                  >
                    Más opciones
                  </button>
                  <div
                    class="dropdown-menu"
                    x-placement="bottom-start"
                    style="
                      position: absolute;
                      transform: translate3d(0px, 25px, 0px);
                      top: 0px;
                      left: 0px;
                      will-change: transform;
                    "
                  >
                    <a
                      @click="verPerfil()"
                      href="#"
                      class="dropdown-item extra-option"
                    >
                      Información Personal
                    </a>

                    <a
                      class="dropdown-item extra-option"
                      href="#"
                      @click="verContactoEmergencia()"
                      >Contacto de emergencia</a
                    >
                    <a
                      class="dropdown-item extra-option"
                      href="#"
                      @click="verExperiencia()"
                      >Experiencia laboral</a
                    >
                    <a
                      class="dropdown-item extra-option"
                      href="#"
                      @click="verDocumentos()"
                      >Documentos</a
                    >
                  </div>
                  <button
                    href="#"
                    class="ml-5 mr-3 btn btn-danger btn-xs"
                    @click.prevent="redirectToLiquidar"
                  >
                    Liquidar empleado
                  </button>
                </div>
              </div>
            </div>
          </header>
        </div>
      </div>

      <div v-if="menu.infoGeneral" class="row">
        <div class="col-md-8">
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="card">
                <div class="card-title pl-4 pt-4 row">
                  <h1 class="titulo col-md-6">Datos Básicos</h1>
                  <div class="col-md-6 text-right">
                    <button
                      class="btn btn-link pt-0 edit"
                      @click="mostarModal('datosBasicos')"
                    >
                      <i class="simple-icon-pencil"></i> Editar
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <!-- /************************************************************************************************************* */ -->
                    <div class="col-md-4">
                      <p class="font-weight-bold text-mute item-subtitle">
                        Nombre completo
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Documento de identidad
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Correo electrónico
                      </p>

                      <p class="font-weight-bold text-mute item-subtitle">
                        Título de estudio
                      </p>

                      <p class="font-weight-bold text-mute item-subtitle">
                        Fecha de nacimiento
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Género
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Estado civil
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Dirección de residencia
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Celular
                      </p>
                    </div>
                    <!-- /************************************************************************************************************* */ -->
                    <div class="col-md-7 offset-1 item-data">
                      <p class="font-weight-bold">
                        {{ funcionario.nombres }} {{ funcionario.apellidos }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.identidad }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.email || "No registrado" }}
                      </p>

                      <p class="font-weight-bold">
                        {{ funcionario.titulo_estudio || "No registrado" }}
                      </p>

                      <p class="font-weight-bold">
                        {{ fechaNacimiento }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.sexo }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.estado_civil || "No registrado" }}
                      </p>
                      <p class="font-weight-bold">
                        {{
                          funcionario.direccion_residencia || "No registrado"
                        }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.celular || "No registrado" }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <modal id="datosBasicos" ref="datosBasicos" :esLg="true">
              <template slot="titulo">Edición de datos básicos</template>
              <template slot="contenido">
                <datos-basicos
                  :funcionarioEditar="funcionario"
                  @mensaje="mostrarMensaje"
                  @cambiarRuta="CambiaRutaImagen"
                ></datos-basicos>
              </template>
            </modal>
          </div>

          <div class="row mt-3">
            <div class="col-md-12">
              <div class="card">
                <div class="card-title pl-4 pt-4 row">
                  <h2 class="titulo col-md-6">Datos de empresa</h2>
                  <div class="col-md-6 text-right">
                    <button
                      class="btn btn-link pt-0 edit"
                      @click="mostarModal('datosEmpresa')"
                    >
                      <i class="simple-icon-pencil"></i> Editar
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <p class="font-weight-bold text-mute item-subtitle">
                        Centro de costos
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Dependencia
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Cargo
                      </p>
                      <p class="font-weight-bold text-mute item-subtitle">
                        Tipo de turno
                      </p>
                      <p
                        class="font-weight-bold text-mute item-subtitle"
                        v-if="funcionario.tipo_turno == 'Fijo'"
                      >
                        Turno asignado
                      </p>
                    </div>
                    <div class="col-md-7 offset-1 item-data">
                      <p class="font-weight-bold">
                        {{
                          funcionario.contractultimate.cargo.dependencia
                            .centro_costo.nombre
                        }}
                      </p>
                      <p class="font-weight-bold">
                        {{
                          funcionario.contractultimate.cargo.dependencia.nombre
                        }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.contractultimate.cargo.nombre }}
                      </p>
                      <p class="font-weight-bold">
                        {{ funcionario.tipo_turno }}
                      </p>
                      <p
                        class="font-weight-bold"
                        v-if="funcionario.tipo_turno == 'Fijo'"
                      >
                        {{ funcionario.turno_fijo.nombre }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <modal id="datosEmpresa" ref="datosEmpresa" :esLg="true">
              <template slot="titulo">Edición Datos de Empresa</template>
              <template slot="contenido">
                <template slot="titulo">
                  Creación Datos de Contacto de Emergencia</template
                >
                <datos-empresa
                  :funcionarioEditar="funcionario"
                  @notificar="mostrarMensaje"
                ></datos-empresa>
              </template>
            </modal>
          </div>
        </div>

        <div class="col-md-4 mt-4">
          <div class="row mb-5">
            <div class="col-md-12">
              <div class="card">
                <div class="card-title ml-2 pt-4 row">
                  <h4 class="titulo col-md-6">Salario</h4>
                  <div class="col-md-6 text-right">
                    <button
                      class="btn btn-link pt-0 edit"
                      @click="mostarModal('datosSalario')"
                    >
                      <i class="simple-icon-pencil"></i> Editar
                    </button>
                  </div>
                </div>

                <div class="card-body">
                  <p id="employee-salary">
                    {{ funcionario.contractultimate.salario | moneda }}
                  </p>

                  <p class="text-mute font-weight-bold item-subtitle2">
                    Tipo de Contrato
                  </p>

                  <p class="font-weight-bold">
                    {{
                      funcionario.contractultimate.tipo_contrato.nombre ||
                      "No registrado"
                    }}
                  </p>

                  <p class="text-mute font-weight-bold item-subtitle2">
                    Fecha de ingreso
                  </p>

                  <p class="font-weight-bold">
                    {{ funcionario.contractultimate.fecha_inicio }}
                  </p>

                  <p
                    class="text-mute font-weight-bold item-subtitle2"
                    v-if="funcionario.contractultimate.fecha_fin"
                  >
                    Fecha de retiro
                  </p>

                  <p
                    class="font-weight-bold"
                    v-if="funcionario.contractultimate.fecha_fin"
                  >
                    {{ funcionario.contractultimate.fecha_fin }}
                  </p>

                  <p class="ml-1 text-mute font-weight-bold item-subtitle2">
                    Periodo de pago
                  </p>
                  <p
                    class="ml-1 font-weight-bold titulo"
                    v-if="frecuencia_pago"
                  >
                    {{ frecuencia_pago }}
                  </p>

                  <hr />

                  <div class="card-title pt-4 row">
                    <h3 class="font-weight-bold col-md-6 mb-5">
                      Bonificacion recurrente
                    </h3>

                    <div class="col-md-6 text-right">
                      <button
                        class="btn btn-link pt-0 edit"
                        @click="customShowModal('datosBono')"
                      >
                        <i class="simple-icon-pencil"></i> Nuevo
                      </button>
                    </div>

                    <br />

                    <div class="table-responsive">
                      <table>
                        <tr
                          v-for="(bono, index) in funcionario.contractultimate
                            .bonificaciones"
                          :key="index"
                        >
                          <th>
                            <p class="ml-1 font-weight-bold text-mute">
                              {{ bono.contable_ingreso.concepto }}
                            </p>
                          </th>
                          <td>
                            <p class="font-weight-bold">
                              {{ bono.valor }}
                            </p>
                          </td>
                          <td>
                            <button
                              class="btn btn-link pt-1 text-danger"
                              style="font-size: 0.8em"
                              @click="customDeleteBono(bono.id)"
                            >
                              <i class="simple-icon-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <modal id="datosBono" ref="datosBono" :esLg="false">
              <template slot="titulo">Nueva Bonificación</template>
              <template slot="contenido">
                <datos-bono
                  ref="datosBono"
                  :contract="funcionario.contractultimate"
                  @update="updateFuncioonario"
                  @mensaje="mostrarMensaje"
                ></datos-bono>
              </template>
            </modal>

            <modal id="datosSalario" ref="datosSalario" :esLg="false">
              <template slot="titulo">Edición Salario</template>
              <template slot="contenido">
                <datos-salario
                  ref="datosSalario"
                  :funcionarioEditar="funcionario"
                  :salarioBase="funcionario.contractultimate.salario"
                  @mensaje="mostrarMensaje"
                ></datos-salario>
              </template>
            </modal>
          </div>

          <div class="row mt-5">
            <div class="col-md-12">
              <div class="card">
                <div class="card-title pl-4 pt-4 row">
                  <p class="titulo col-md-6">Afiliaciones</p>
                  <div class="col-md-6 text-right">
                    <button
                      class="btn btn-link pt-0 edit"
                      @click="mostarModal('datosAfiliaciones')"
                    >
                      <i class="simple-icon-pencil"></i> Editar
                    </button>
                  </div>
                </div>

                <div class="card-body">
                  <p class="font-weight-bold text-mute item-subtitle2">EPS</p>
                  <p class="font-weight-bold">{{ funcionario.eps.nombre }}</p>

                  <p class="font-weight-bold text-mute item-subtitle2">
                    Fondo de pensiones
                  </p>
                  <p class="font-weight-bold">
                    {{ funcionario.pensiones.nombre }}
                  </p>

                  <p class="font-weight-bold text-mute item-subtitle2">
                    Fondo de cesantías
                  </p>
                  <p class="font-weight-bold">
                    {{ funcionario.cesantias.nombre }}
                  </p>

                  <p class="font-weight-bold text-mute item-subtitle2">
                    Caja de Compensación
                  </p>
                  <p class="font-weight-bold">
                    {{ funcionario.caja_compensacion.nombre }}
                  </p>
                </div>
              </div>
            </div>
            <modal id="datosAfiliaciones" ref="datosAfiliaciones">
              <template slot="titulo">Edición datos Afiliaciones</template>
              <template slot="contenido">
                <datos-afiliaciones
                  :funcionarioEditar="funcionario"
                  @notificar="mostrarMensaje"
                ></datos-afiliaciones>
              </template>
            </modal>
          </div>
        </div>

        <notifications group="notificaciones" position="bottom right" />
      </div>

      <detalles-contacto
        :funcionario="funcionario"
        v-if="menu.mostrarContatoEmergencia"
      />

      <detalles-experiencia
        :funcionario="funcionario"
        v-if="menu.mostrarExperiencia"
      />

      <div v-if="menu.documentacion">
        <documento-component
          :idFuncionario="funcionario.id"
        ></documento-component>
      </div>
    </div>

    <div class="overlay" v-else>
      <div class="container-spinner">
        <atom-spinner :animation-duration="500" :size="70" color="#fff" />
        <p class="text-center font-weight-bold text-spinner">
          Cargando módulo...
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { AtomSpinner } from "epic-spinners";
import DatosBasicos from "./formularios/DatosBasicos";
import DatosEmpresa from "./formularios/DatosEmpresa";
import DatosPersonales from "./formularios/DatosPersonales";
import DatosContacto from "./formularios/DatosContacto";
import DatosSalario from "./formularios/DatosSalario";
import DatosBono from "./formularios/DatosBono";
import DatosAfiliaciones from "./formularios/DatosAfiliaciones";

import DetallesContacto from "./../Opciones/contacto";
import DetallesExperiencia from "./../Opciones/experiencias";

import Modal from "../../utiles/Modal";

export default {
  components: {
    AtomSpinner,
    DetallesContacto,
    DatosBasicos,
    DetallesExperiencia,
    DatosEmpresa,
    DatosPersonales,
    DatosContacto,
    DatosSalario,
    DatosBono,
    DatosAfiliaciones,
    Modal,
  },
  data() {
    return {
      funcionario: "",
      arl: "",
      frecuencia_pago: "",
      mostrarInfo: false,
      mostrarModalExperiencia: false,
      rutaImagen: "",
      salarioBase: "",
      menu: {
        infoGeneral: true,
        documentacion: false,
        mostrarExperiencia: false,
        mostrarContatoEmergencia: false,
      },
    };
  },
  created() {
    this.obtenerDatosEmpresa();
    this.obtenerFuncionario(this.$route.params.id);
  },

  methods: {
    obtenerFuncionario(identidad) {
      axios
        .get(
          `/api/${localStorage.getItem(
            "tenant"
          )}/funcionarios/${identidad}/mostrar`
        )
        .then((datos) => {
          this.funcionario = datos.data;

          if (this.funcionario.image) {
            this.rutaImagen = `${this.funcionario.image}`;
          } else {
            this.rutaImagen = `/img/robot.jpg`;
          }
          this.mostrarInfo = true;
        });
    },

    updateFuncioonario() {
      this.obtenerFuncionario(this.$route.params.id);
    },

    customEditBono(id) {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/bonos/${id}/edit`)
        .then((datos) => {
          console.log(datos);
        });
    },

    customDeleteBono(id) {
      this.$swal
        .fire({
          title: "¿Está seguro?",
          text:
            "Se dispone a eliminar una bonificacion recurrente, esto afectara la nomina, desea coninuar?.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, continuar!",
        })
        .then((result) => {
          if (result.value) {
            axios
              .delete(`/api/${localStorage.getItem("tenant")}/bonos/${id}`)
              .then((datos) => {
                this.updateFuncioonario();
              });
          }
        });
    },

    obtenerDatosEmpresa() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/general/empresa/global`)
        .then((datos) => {
          this.salarioBase = datos.data.salario_base;
          this.frecuencia_pago = datos.data.frecuencia_pago;
        });
    },

    mostarModal(referencia) {
      setTimeout(() => {
        this.$refs[referencia].mostrarModal();
      }, 500);
    },

    customShowModal(modal) {
      this.$refs[modal].mostrarModal();
    },

    refrescarFuncionario(identidad) {
      this.obtenerFuncionario(identidad);
    },

    redirectToLiquidar() {
      this.$router.push({
        name: "LiquidacionPrincipal",
        params: { id: this.funcionario.id },
      });
    },
    redirectTodocumentos() {
      this.$router.push({
        name: "Documentos",
        params: { id: this.funcionario.id },
      });
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

    CambiaRutaImagen(ruta) {
      this.rutaImagen = `${ruta}`;
    },

    verDocumentos() {
      this.menu.mostrarExperiencia = false;
      this.menu.mostrarContatoEmergencia = false;
      this.menu.infoGeneral = false;
      this.menu.documentacion = true;
    },

    verPerfil() {
      this.menu.mostrarExperiencia = false;
      this.menu.mostrarContatoEmergencia = false;
      this.menu.infoGeneral = true;
      this.menu.documentacion = false;
    },

    verContactoEmergencia() {
      this.menu.mostrarExperiencia = false;
      this.menu.infoGeneral = false;
      this.menu.documentacion = false;
      this.menu.mostrarContatoEmergencia = true;
    },

    verExperiencia() {
      this.menu.mostrarExperiencia = true;
      this.menu.infoGeneral = false;
      this.menu.documentacion = false;
      this.menu.mostrarContatoEmergencia = false;
    },
  },
  computed: {
    fechaNacimiento() {
      return moment(this.funcionario.fecha_nacimiento).format("DD-MM-YYYY");
    },
    fechaIngreso() {
      return moment(this.funcionario.contractultimate.fecha_inicio).format(
        "DD-MM-YYYY"
      );
    },
    fechaRetiro() {
      return moment(this.funcionario.contractultimate.fecha_fin).format(
        "DD-MM-YYYY"
      );
    },
  },
};
</script>

<style scoped>
#img-funcionario {
  width: 96px;
  height: 96px;
  border-radius: 50%;
}
.salary-vacations-item,
#employee-salary {
  font-size: 1.28rem;
  font-weight: 700;
}
#employee-position {
  font-size: 0.75rem;
  font-weight: 600;
}
.titulo {
  font-size: 1.25rem;
  font-family: "Montserrat";
  font-weight: 700;
}
.card-title {
  margin-bottom: 0;
}
.card .card-body {
  padding-top: 0.5rem;
}
.text-mute {
  color: dimgray;
}
.item-subtitle {
  text-align: right;
  font-family: "Lato";
  color: #275c9c;
}
.item-subtitle2 {
  font-family: "Lato";
  color: #275c9c;
}
.edit {
  font-size: 1.1rem;
  color: #2a93d5;
  font-weight: bold;
}
.data-icon {
  padding-top: 10px;
  font-size: 2rem;
}
.svg-icon {
  width: 60px;
  display: block;
  text-align: center;
}
.link-item {
  padding: 5px;
  font-weight: 700;
  color: #000;
}
.link-liq {
  color: rgb(177, 15, 15);
  font-weight: bold;
  text-decoration: underline;
}
.extra-option {
  padding: 6px;
  color: #000;
  font-weight: 700;
}
.item-data p {
  font-family: "Montserrat";
  font-size: 0.8rem;
}
@media (max-width: 767px) {
  .text-xs-center {
    text-align: center;
  }
}
</style>
