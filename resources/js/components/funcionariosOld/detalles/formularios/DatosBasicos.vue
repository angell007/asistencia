<template>
  <form @submit.prevent="validarAntesDeEnviar1" class="row">
    <div class="form-group col-md-2 text-center">
      <img
        id="imageOutput"
        :src="`${this.funcionarioEditar.image}`"
        alt="Imagen del funcionario"
        class="d-block m-auto"
      />

      <label for="image" class="subir-archivo mt-2">
        Cargar imagen
        <i class="simple-icon-doc"></i>
      </label>

      <input
        type="file"
        accept="image/*"
        name="image"
        v-validate="'size:800'"
        data-vv-as="Imagen"
        class="form-control-file"
        id="image"
        placeholder="Imagen"
        @change="vistaPreviaImagen"
      />

      <div class="col-md-12 text-center" v-if="errors.has('image')">
        <small class="invalid">{{ errors.first("image") }}</small>
      </div>
    </div>
    <div class="form-group col-md-10 text-center">
      <div class="form-group row">
        <label
          class="custom-label text-right col-form-label col-md-2 offset-2"
          for="nombres"
          >Nombres</label
        >
        <input
          type="text"
          name="nombres"
          v-validate="'required|alpha_spaces|min:3'"
          data-vv-as="Nombres"
          class="form-control custom-control col-md-5"
          placeholder="Nombres"
          v-model="funcionarioEditar.nombres"
        />
        <small class="invalid col-md-5 offset-4" v-if="errors.has('nombres')">{{
          errors.first("nombres")
        }}</small>
      </div>
      <div class="form-group row">
        <label
          class="custom-label text-right col-form-label col-md-2 offset-2"
          for="apellidos"
          >Apellidos</label
        >
        <input
          type="text"
          name="apellidos"
          v-validate="'required|alpha_spaces|min:3'"
          data-vv-as="Apellidos"
          class="form-control custom-control col-md-5"
          placeholder="Apellidos"
          v-model="funcionarioEditar.apellidos"
        />
        <small
          class="invalid col-md-5 offset-4"
          v-if="errors.has('apellidos')"
          >{{ errors.first("apellidos") }}</small
        >
      </div>

      <div class="form-group row">
        <label class="custom-label text-right col-md-3 offset-1" for="identidad"
          >Documento de Identidad</label
        >
        <input
          type="text"
          name="identidad"
          v-validate="'required|numeric|min:6|max:15'"
          data-vv-as="Documento de identidad"
          class="form-control custom-control col-md-5"
          placeholder="Documento de Identidad"
          v-model="funcionarioEditar.identidad"
        />
        <small
          class="invalid col-md-5 offset-4"
          v-if="errors.has('identidad')"
          >{{ errors.first("identidad") }}</small
        >
      </div>

      <div class="form-group row">
        <label class="custom-label text-right col-md-2 offset-2" for="email"
          >Email</label
        >
        <input
          type="email"
          name="email"
          v-validate="'required|email'"
          class="form-control custom-control col-md-5"
          v-model="funcionarioEditar.email"
        />
        <small class="invalid col-md-5 offset-4" v-if="errors.has('email')">{{
          errors.first("email")
        }}</small>
      </div>
      <!-- <div class="form-group row">
        <label
          class="custom-label text-right col-form-label col-md-3 offset-1"
          for="fecha_ingreso"
          >Fecha de Ingreso</label
        >
        <calendario
          ref="calendarioIngreso"
          :fecha="fechaIngresoFormato"
        ></calendario>
      </div>
      <div class="form-group row" v-show="showFechaRetiro">
        <label
          class="custom-label text-right col-form-label col-md-3 offset-1"
          for="fecha_ingreso"
          >Fecha de Retiro</label
        >
        <calendario
          ref="calendarioRetiro"
          :fecha="fechaRetiroFormato"
        ></calendario>

        <small class="invalid" v-if="errors.has('fecha_retiro')">{{
          errors.first("fecha_retiro")
        }}</small>
      </div> -->

      <div class="form-group row">
        <label
          class="custom-label text-right col-form-label col-md-3 offset-1"
          for="titulo_estudio"
          >Título de estudio</label
        >
        <input
          type="text"
          name="titulo_estudio"
          v-validate="'alpha_spaces|min:3'"
          data-vv-as="Título de estudio"
          class="form-control custom-control col-md-5"
          v-model="funcionarioEditar.titulo_estudio"
        />
        <small
          class="invalid col-md-5 offset-4"
          v-if="errors.has('titulo_estudio')"
          >{{ errors.first("titulo_estudio") }}</small
        >
      </div>

      <div class="form-group row">
        <label
          class="custom-label text-right col-form-label col-md-3 offset-1"
          for="fecha_nacimiento"
          >Fecha de nacimiento</label
        >
        <calendario
          ref="calendario"
          customWidth="240"
          :fecha="fechaDeNacimiento"
        ></calendario>
      </div>
      <div class="form-group row">
        <label class="custom-label text-right col-form-label col-md-3 offset-1" for="sexo"
          >Género</label
        >
        <select
          name="sexo"
          v-model="funcionarioEditar.sexo"
          class="form-control custom-control col-md-6"
        >
          <option value="Masculino">Masculino</option>
          <option value="Femenino">Femenino</option>
        </select>
      </div>
      <div class="form-group row">
        <label class="custom-label text-right col-form-label col-md-3 offset-1" for="estado_civil"
          >Estado civil</label
        >
        <select
          v-model="estadoActual"
          class="form-control custom-control col-md-6"
        >
          <option
            v-for="(estado, index) in estados"
            :key="index"
            :value="estado.valor"
          >
            {{ estado.clave }}
          </option>
        </select>
      </div>

      <div class="form-group row">
        <label class="custom-label text-right col-form-label col-md-3 offset-1" for=""
          >Tipo de Sangre</label
        >
        <select
          v-model="funcionarioEditar.tipo_sangre"
          class="form-control custom-control col-md-6"
        >
          <option
            v-for="(tipo, index) in tipoSangre"
            :key="index"
            :value="tipo"
          >
            {{ tipo }}
          </option>
        </select>
      </div>

      <div class="form-group row">
        <label class="custom-label text-right col-form-label col-md-3 offset-1" for="celular"
          >Celular</label
        >
        <input
          type="text"
          name="celular"
          v-validate="'required|numeric|min:10|max:15'"
          data-vv-as="Celular"
          class="form-control custom-control col-md-6"
          v-model="funcionarioEditar.celular"
        />
        <small class="invalid col-md-6 offset-5" v-if="errors.has('celular')">{{
          errors.first("celular")
        }}</small>
      </div>
      <div class="form-group row">
        <label
          class="custom-label text-right col-form-label col-md-3 offset-1"
          for="direccion_residencia"
          >Dirección de residencia</label
        >
        <input
          type="text"
          name="direccion_residencia"
          v-validate="'required|min:3'"
          data-vv-as="Dirección de residencia"
          class="form-control custom-control col-md-6"
          v-model="funcionarioEditar.direccion_residencia"
        />
        <small
          class="invalid col-md-6 offset-5"
          v-if="errors.has('direccion_residencia')"
          >{{ errors.first("direccion_residencia") }}</small
        >
      </div>

      <div class="form-group text-right">
        <input
          type="submit"
          class="btn btn-secondary raised"
          value="Actualizar"
          id="btnSubmit"
        />
      </div>
    </div>
  </form>
</template>

<script>
import Calendario from "../../../utiles/Calendario";
import DatosPersonales from "./DatosPersonales";
import estados from "../../registro/formularios/datos/atributosFuncionario";

export default {
  components: { Calendario, DatosPersonales },
  name: "datos-basicos",
  props: {
    funcionarioEditar: Object,
  },
  data() {
    return {
      // contratosDatos: [],
      // contratoActual: "",
      showFechaRetiro: "",
      initialIdentidad: "",
      imagenTemporal: "",

      estados: "",
      estadoActual: "",
      tipoSangre: ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"],
      tipoActual: "",
    };
  },

  created() {
    this.initialIdentidad = this.funcionarioEditar.identidad;
    this.getTiposContrato();

    this.estados = estados.datos.estados;
    this.getEstadoActual();
  },

  mounted() {
    this.showFechaRetiro = this.funcionarioEditar.contractultimate.fecha_fin
      ? true
      : false;
    if (!this.funcionarioEditar.contractultimate.fecha_fin) {
      this.$refs.calendarioRetiro.fechaSeleccionada = moment.now();
    }
  },

  methods: {
    async validarAntesDeEnviar1() {
      let validado = await this.$validator.validateAll();
      if (validado) {
        this.putDatosBasicos();
        return;
      }
      this.$swal.fire(
        "Oops!",
        "Debe corregir los errores antes de enviar",
        "error"
      );
    },

    putDatosBasicos() {
      // this.setFuncionarioFechaRetiro();

      // this.funcionarioEditar.contractultimate.tipo_contrato.id = this.contratoActual.id;
      // this.funcionarioEditar.contractultimate.fecha_inicio = this.$refs.calendarioIngreso.formatearFecha();
      this.funcionarioEditar.image = "";

      const data = new FormData();
      data.append("image", this.imagenTemporal);
      data.append("fun", JSON.stringify(this.funcionarioEditar));

      document.getElementById("btnSubmit").value = "Enviando...";
      axios
        .post(
          `/api/${localStorage.getItem("tenant")}/funcionarios/${
            this.funcionarioEditar.id
          }/editar`,
          data
        )
        .then((respuesta) => {
          // this.funcionarioEditar.contractultimate.tipo_contrato.nombre = this.contratoActual.nombre;
          this.$emit("mensaje", respuesta.data.message);
          if (respuesta.data.imagen != "") {
            this.$emit("cambiarRuta", respuesta.data.imagen);
          }
          modalEmitter.$emit("cerrar", "datosBasicos");
          this.redirectIfIdentidadChanges();
        })
        .catch((error) => {
          this.$swal.fire("Oops!", error.response.data.message, "error");
        });
      document.getElementById("btnSubmit").value = "Actualizar";
    },

    // setFuncionarioFechaRetiro() {
    //   if (this.contratoActual.id === 2 || this.contratoActual.id === 5) {
    //     this.funcionarioEditar.contractultimate.fecha_fin = "";
    //   } else {
    //     this.funcionarioEditar.contractultimate.fecha_fin = this.$refs.calendarioRetiro.formatearFecha();
    //   }
    // },
    // getTiposContrato() {
    //   axios
    //     .get(`/api/${localStorage.getItem("tenant")}/contratos/datos`)
    //     .then((datos) => {
    //       this.contratosDatos = datos.data;
    //       this.getContratoFuncionario();
    //     });
    // },
    // getContratoFuncionario() {
    //   this.contratoActual = this.contratosDatos.find((contrato) => {
    //     return (
    //       contrato.id ==
    //       this.funcionarioEditar.contractultimate.tipo_contrato.id
    //     );
    //   });
    // },

    // toggleFechaRetiro(contrato) {
    //   if (contrato) {
    //     this.showFechaRetiro = contrato.id !== 2 && contrato.id !== 5;
    //   }
    // },

    redirectIfIdentidadChanges() {
      if (this.funcionarioEditar.identidad !== this.initialIdentidad) {
        this.$router.push({ name: "FuncionariosPrincipal" });
      }
    },

    vistaPreviaImagen(event) {
      const image = document.getElementById("imageOutput");
      image.src = URL.createObjectURL(event.target.files[0]);
      if (event.target.files.length > 0) {
        this.imagenTemporal = event.target.files[0];
      }
    },

    async validarAntesDeEnviar() {
      let validado = await this.$validator.validateAll();
      if (validado) {
        this.actualizarDatosPersonales();
        return;
      }
      this.$swal.fire(
        "Oops!",
        "Debe corregir los errores antes de enviar",
        "error"
      );
    },
    actualizarDatosPersonales() {
      this.funcionarioEditar.fecha_nacimiento = moment(
        this.funcionarioEditar.fecha_nacimiento
      ).format("YYYY-MM-DD");
      this.funcionarioEditar.estado_civil = this.estadoActual;
      this.funcionarioEditar.image = "";
      delete this.funcionarioEditar["image"];
      axios
        .post(
          `/api/${localStorage.getItem("tenant")}/funcionarios/${
            this.funcionarioEditar.id
          }/editar`,
          this.funcionarioEditar
        )
        .then((respuesta) => {
          this.$emit("notificar", respuesta.data.message);
          modalEmitter.$emit("cerrar", "datosPersonales");
        })
        .catch((error) => {
          this.$swal.fire(
            "Oops!",
            "Ha ocurrido un error, por favor intente más tarde",
            "error"
          );
        });
    },
    getEstadoActual() {
      this.estadoActual = this.estados.find((estado) => {
        return estado.valor === this.funcionarioEditar.estado_civil;
      }).valor;
    },
  },

  // computed: {
  //   fechaIngresoFormato() {
  //     return moment(
  //       this.funcionarioEditar.contractultimate.fecha_inicio
  //     ).format("YYYY-MM-DD");
  //   },
  //   fechaRetiroFormato() {
  //     return moment(this.funcionarioEditar.contractultimate.fecha_fin).format(
  //       "YYYY-MM-DD"
  //     );
  //   },

  //   fechaDeNacimiento() {
  //     return moment(this.funcionarioEditar.fecha_nacimiento).format(
  //       "YYYY-MM-DD"
  //     );
  //   },
  // },
};
</script>
<style scoped>
input[type="file"] {
  display: none;
}
.subir-archivo {
  border: 1px solid #ccc;
  display: inline-block;
  padding: 6px 12px;
  font-weight: bold;
  cursor: pointer;
}
.subir-archivo {
  font-family: "Lato";
  color: #7a7e7e;
}
fieldset legend {
  font-size: 1.2rem;
}
#imageOutput {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}
</style>
