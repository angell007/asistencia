<template>
  <form @submit.prevent="actualizarSalario">
    <div class="form-group col-md-8">
      <label for="salario" class="custom-label">Salario</label>
      <money
        type="text"
        v-model="funcionarioEditar.contractultimate.salario"
        :class="'form-control custom-control'"
        name="salario"
        v-validate="'required|min_value:0|numeric'"
        data-vv-as="Salario"
      ></money>
      <small class="invalid" v-if="errors.has('salario')">{{
        errors.first("salario")
      }}</small>

      <div class="form-group col-md-8 ">
        <label
          class="custom-label text-right col-md-3 offset-1"
          for="tipo_contrato_id"
          >Tipo de Contrato</label
        >
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
          class="col-md-5 font-weight-bold"
          label="nombre"
          @input="toggleFechaRetiro(contratoActual)"
        >
          <template slot="noOptions">La lista está vacía</template>
          <template slot="noResult">No existen resultados!</template>
        </multiselect>
        <small
          class="invalid col-md-7 offset-4"
          v-if="errors.has('tipo_contrato_id')"
          >{{ errors.first("tipo_contrato_id") }}</small
        >
      </div>

      <div class="form-group col-md-8">
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
      <div class="form-group col-md-8" v-show="showFechaRetiro">
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
  props: {
    funcionarioEditar: Object,
    salarioBase: [Number, String],
  },

  data() {
    return {
      contratosDatos: [],
      contratoActual: "",
      showFechaRetiro: "",
      initialIdentidad: "",
      imagenTemporal: "",
      estados: "",
      estadoActual: "",
      tipoSangre: ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"],
      tipoActual: "",
    };
  },

  methods: {
    actualizarSalario() {
      this.setFuncionarioFechaRetiro();

      this.funcionarioEditar.contractultimate.tipo_contrato.id = this.contratoActual.id;
      this.funcionarioEditar.contractultimate.fecha_inicio = this.$refs.calendarioIngreso.formatearFecha();

      this.funcionarioEditar.image = "";
      delete this.funcionarioEditar["image"];
      this.mostarAlertaSalario().then((resultado) => {
        if (resultado.value) {
          axios
            .post(
              `/api/${localStorage.getItem("tenant")}/funcionarios/${
                this.funcionarioEditar.id
              }/editar`,
              this.funcionarioEditar
            )
            .then((respuesta) => {
              this.funcionarioEditar.contractultimate.tipo_contrato.nombre = this.contratoActual.nombre;
              this.$emit("notificar", respuesta.data.message);
              modalEmitter.$emit("cerrar", "datosSalario");
            })
            .catch((error) => {
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
      if (this.funcionarioEditar.contractultimate.salario < this.salarioBase) {
        return this.$swal.fire({
          title: "¿Está seguro(a)?",
          text: "El salario del funcionario será inferior al salario mínimo",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, continuar!",
          cancelButtonText: "Cancelar",
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
    getTiposContrato() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/contratos/datos`)
        .then((datos) => {
          this.contratosDatos = datos.data;
          this.getContratoFuncionario();
        });
    },

    toggleFechaRetiro(contrato) {
      if (contrato) {
        this.showFechaRetiro = contrato.id !== 2 && contrato.id !== 5;
      }
    },

    getContratoFuncionario() {
      this.contratoActual = this.contratosDatos.find((contrato) => {
        return (
          contrato.id ==
          this.funcionarioEditar.contractultimate.tipo_contrato.id
        );
      });
    },
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

    // fechaDeNacimiento() {
    //   return moment(this.funcionarioEditar.fecha_nacimiento).format(
    //     "YYYY-MM-DD"
    //   );
    // },
  },
};
</script>
