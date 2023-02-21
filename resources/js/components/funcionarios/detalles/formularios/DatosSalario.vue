<template>
  <form @submit.prevent="actualizarSalario">
    <div class="row col-12">
      <div class="form-group col-12">
        <label class="custom-label" for="tipo_contrato_id">Tipo de Contrato</label>
        <multiselect
          :max-height="200"
          name="tipo_contrato_id"
          placeholder="Seleccione una opción"
          selectLabel="Enter para seleccionar"
          deselectLabel="Enter para remover"
          v-validate="'required'"
          data-vv-as="Tipo de contrato"
          v-model="contratoActual"
          :options="contratosDatos"
          label="nombre"
          @input="toggleFechaRetiro(contratoActual)"
        >
          <template slot="noOptions">La lista está vacía</template>
          <template slot="noResult">No existen resultados!</template>
        </multiselect>
        <small class="invalid col-md-7" v-if="errors.has('tipo_contrato_id')">
          {{
          errors.first("tipo_contrato_id")
          }}
        </small>
      </div>

      <div class="form-group col-12">
        <label for="salario" class="custom-label">Salario</label>
        <money
          type="text"
          v-model="mySalario"
          :class="'form-control custom-control'"
          name="salario"
          v-validate="'required|min_value:0|numeric'"
          data-vv-as="Salario"
        ></money>
        <small class="invalid" v-if="errors.has('salario')">
          {{
          errors.first("salario")
          }}
        </small>
      </div>

      <!-- v-on:keyup="salarioTransform()" -->

      <div class="w-100"></div>

      <div class="form-group col-12">
        <label class="custom-label" for="fecha_ingreso">Fecha de Ingreso</label>
        <calendario ref="calendarioIngreso" :fecha="fechaIngresoFormato"></calendario>
      </div>

      <div class="form-group col-12" v-show="showFechaRetiro">
        <label class="custom-label" for="fecha_retiro">Fecha de Retiro</label>
        <calendario ref="calendarioRetiro" :fecha="fechaRetiroFormato"></calendario>
      </div>

      <div class="w-100"></div>

      <div class="form-group col-12 form-check custom-control my-3">
        <input
          ref="checkSubsidio"
          type="checkbox"
          class="form-check-input"
          id="checkSubsidio"
          v-model="funcionarioEditar.contractultimate.subsidio_transporte"
        />
        <label
          class="form-check-label custom-label"
          name="subsidio_transporte"
          for="checkSubsidio"
        >¿Pagar auxilio ?</label>
        <small class="invalid" v-if="errors.has('subsidio_transporte')">
          {{
          errors.first("subsidio_transporte")
          }}
        </small>
      </div>
    </div>
    <div class="form-group text-right">
      <button type="submit" class="btn btn-secondary raised">Actualizar</button>
    </div>
  </form>
</template>

<script>
import Calendario from "../../../utiles/Calendario";
import DatosPersonales from "./DatosPersonales";
import estados from "../../registro/formularios/datos/atributosFuncionario";

export default {
  components: { Calendario, DatosPersonales },
  props: {
    funcionarioEditar: Object,
    salarioBase: [Number, String]
  },

  data() {
    return {
      mySalario: this.funcionarioEditar.contractultimate.salario,
      contratosDatos: [],
      contratoActual: "",
      showFechaRetiro: "",
      initialIdentidad: "",
      imagenTemporal: "",
      estados: "",
      estadoActual: "",
      tipoSangre: ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"],
      tipoActual: "",
      _salarioBase: this.salarioBase
    };
  },

  watch: {
    mySalario(nuevoValor, valorAnterior) {
      this.funcionarioEditar.contractultimate.salario = nuevoValor;
      if (nuevoValor > this._salarioBase) {
        this.$refs.checkSubsidio.setAttribute("disabled", "disabled");
        this.funcionarioEditar.contractultimate.subsidio_transporte = 0;
      } else {
        this.funcionarioEditar.contractultimate.subsidio_transporte = 1;
        this.$refs.checkSubsidio.removeAttribute("disabled");
      }
    }
  },

  mounted() {
    this.showFechaRetiro = this.funcionarioEditar.contractultimate.fecha_fin
      ? true
      : false;
    if (!this.funcionarioEditar.contractultimate.fecha_fin) {
      this.$refs.calendarioRetiro.fechaSeleccionada = moment.now();
    }
  },

  created() {
    this.getTiposContrato();
    this.obtenerDatosConfiguracion();
  },

  methods: {
    obtenerDatosConfiguracion() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/general/empresa/global`)
        .then(datos => {
          this._salarioBase = parseInt(datos.data.salario_base) * 2;
          if (
            this.funcionarioEditar.contractultimate.salario > this._salarioBase
          ) {
            this.$refs.checkSubsidio.setAttribute("disabled", "disabled");
            this.funcionarioEditar.contractultimate.subsidio_transporte = 0;
          }
        });
    },

    actualizarSalario() {
      this.setFuncionarioFechaRetiro();

      this.funcionarioEditar.contractultimate.tipo_contrato.id = this.contratoActual.id;
      this.funcionarioEditar.contractultimate.fecha_inicio = this.$refs.calendarioIngreso.formatearFecha();

      this.funcionarioEditar.image = "";
      delete this.funcionarioEditar["image"];
      this.mostarAlertaSalario().then(resultado => {
        if (resultado.value) {
          axios
            .post(
              `/api/${localStorage.getItem("tenant")}/funcionarios/${
                this.funcionarioEditar.id
              }/editar`,
              this.funcionarioEditar
            )
            .then(respuesta => {
              this.funcionarioEditar.contractultimate.tipo_contrato.nombre = this.contratoActual.nombre;
              this.$emit("notificar", respuesta.data.message);
              modalEmitter.$emit("cerrar", "datosSalario");
            })
            .catch(error => {
              this.$swal.fire(
                "Oops!",
                "Ha ocurrido un error, por favor intente más tarde",
                "error"
              );
            });
        }
      });
    },
    mostarAlertaSalario() {
      if (this.funcionarioEditar.contractultimate.salario < this._salarioBase) {
        return this.$swal.fire({
          title: "¿Está seguro(a)?",
          text: "El salario del funcionario será inferior al salario mínimo",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, continuar!",
          cancelButtonText: "Cancelar"
        });
      }
      return this.$swal(
        "Tenga en cuenta!",
        "Esto afectará los cálculos de nómina del periodo actual",
        "warning"
      );
    },

    setFuncionarioFechaRetiro() {
      if (this.contratoActual.id === 2 || this.contratoActual.id === 5) {
        this.funcionarioEditar.contractultimate.fecha_fin = "";
      } else {
        this.funcionarioEditar.contractultimate.fecha_fin = this.$refs.calendarioRetiro.formatearFecha();
      }
    },

    getContratoFuncionario() {
      this.contratoActual = this.contratosDatos.find(contrato => {
        return (
          contrato.id ==
          this.funcionarioEditar.contractultimate.tipo_contrato.id
        );
      });
    },

    getTiposContrato() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/contratos/datos`)
        .then(datos => {
          this.contratosDatos = datos.data;
          this.getContratoFuncionario();
        });
    },

    toggleFechaRetiro(contrato) {
      if (contrato) {
        this.showFechaRetiro = contrato.id !== 2 && contrato.id !== 5;
      }
    }
  },

  computed: {
    fechaIngresoFormato() {
      return moment(
        this.funcionarioEditar.contractultimate.fecha_inicio
      ).format("YYYY-MM-DD");
    },
    fechaRetiroFormato() {
      return moment(this.funcionarioEditar.contractultimate.fecha_fin).format(
        "YYYY-MM-DD"
      );
    },

    fechaDeNacimiento() {
      return moment(this.funcionarioEditar.fecha_nacimiento).format(
        "YYYY-MM-DD"
      );
    }
  }
};
</script>
