<template>
  <form @submit.prevent="validarAntesDeEnviar">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label class="custom-label" for="funcionario_id">Funcionario</label>

        <multiselect
          selectedLabel="Actual"
          deselectLabel="Enter para remover"
          selectLabel="Enter para seleccionar"
          placeholder="Seleccione Funcionario"
          :custom-label="nombreCompleto"
          v-model="funcionario"
          :options="funcionariosDatos"
          name="funcionario_id"
          v-validate="'required|min:3'"
          data-vv-as="Funcionario"
          @input="asignarIdFuncionario"
          :disabled="deshabilitarCampo"
        ></multiselect>
        <small class="invalid" v-if="errors.has('funcionario_id')">
          {{
          errors.first("funcionario_id")
          }}
        </small>
      </div>

      <!-- <div class="form-group col-md-6">
        <label class="custom-label" for="contable_licencia_incapacidad_id"
          >Suma</label
        >
        <select
          class="form-control custom-control"
          name="contable_licencia_incapacidad_id"
          v-validate="'required'"
          data-vv-as="Novedad"
          v-model="opcionSuma"
          @change="getlistaNovedades()"
        >
          <option
            v-for="(opcion, index) in opciones"
            :key="index"
            :value="opcion"
          >
            {{ opcion }}
          </option>
        </select>
        <small
          class="invalid"
          v-if="errors.has('contable_licencia_incapacidad_id')"
          >{{ errors.first("contable_licencia_incapacidad_id") }}</small
        >
      </div>-->

      <div class="form-group col-md-6">
        <label class="custom-label" for="contable_licencia_incapacidad_id">Novedad</label>
        <select
          class="form-control custom-control"
          name="contable_licencia_incapacidad_id"
          v-validate="'required'"
          data-vv-as="Novedad"
          v-model="lista.contable_licencia_incapacidad_id"
          @change="obtenerTipoNovedad(lista.contable_licencia_incapacidad_id)"
        >
          <option
            v-for="(novedad, index) in nombresNovedadesDatos"
            :key="index"
            :value="novedad.id"
          >{{ novedad.concepto }}</option>
        </select>
        <small
          class="invalid"
          v-if="errors.has('contable_licencia_incapacidad_id')"
        >{{ errors.first("contable_licencia_incapacidad_id") }}</small>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label class="custom-label" for="tipo">Tipo de Novedad {{lista.modalidad}}</label>
        <input
          type="text"
          name="tipo"
          v-validate="'required|alpha_spaces|min:3'"
          data-vv-as="Tipo de novedad"
          v-model="lista.tipo"
          class="form-control custom-control"
          :disabled="true"
        />
      </div>

      <!-- <div class="form-group col-md-6">
        <label class="custom-label" for="modalidad">Modalidad de la novedad {{lista.modalidad}}</label>
        <select
          name="modalidad"
          v-model="lista.modalidad"
          class="form-control custom-control"
          v-validate="'required|min:3'"
          data-vv-as="Modalidad"
        >
          <option value="D??a">D??a</option>
          <option value="Hora">Hora</option>
        </select>
        <small class="invalid" v-if="errors.has('modalidad')">
          {{
          errors.first("modalidad")
          }}
        </small>
      </div>-->
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label class="custom-label" for="fecha_inicio">Fecha de Inicio</label>
        <input
          type="date"
          name="fecha_inicio"
          v-model="lista.fecha_inicio"
          class="form-control custom-control"
        />
        <!-- <input
          type="datetime-local"
          name="fecha_inicio"
          v-model="lista.fecha_inicio"
          class="form-control custom-control"
          v-else-if="lista.modalidad == 'Hora'"
        />-->
        <small class="invalid" v-if="errors.has('fecha_inicio')">
          {{
          errors.first("fecha_inicio")
          }}
        </small>
      </div>
      <div class="form-group col-md-6">
        <label class="custom-label" for="fecha_fin">Fecha de Fin</label>
        <input
          type="date"
          name="fecha_fin"
          v-model="lista.fecha_fin"
          class="form-control custom-control"
        />
        <!-- <input
          type="datetime-local"
          name="fecha_fin"
          v-model="lista.fecha_fin"
          class="form-control custom-control"
          v-else-if="lista.modalidad == 'Hora'"
        />-->
        <small class="invalid" v-if="errors.has('fecha_fin')">
          {{
          errors.first("fecha_fin")
          }}
        </small>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-12">
        <label class="custom-label" for="observacion">Observaci??n</label>
        <textarea
          name="observaci??n"
          v-model="lista.observacion"
          rows="5"
          class="form-control custom-control"
        ></textarea>
      </div>
    </div>
    <div class="form-group text-right">
      <button type="submit" class="btn btn-secondary raised">Guardar Novedad</button>
    </div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      funcionario: "",
      lista: {
        id: "",
        funcionario_id: "",
        contable_licencia_incapacidad_id: "",
        fecha_inicio: "",
        fecha_fin: "",
        tipo: "",
        modalidad: "",
        observacion: ""
      },
      funcionariosDatos: [],
      opciones: {
        Si: "Si",
        No: "No"
      },
      opcionSuma: "",
      nombresNovedadesDatos: [],
      nombresNovedadesDatosPartial: [],
      agregarObservacion: false,
      deshabilitarCampo: false
    };
  },
  mounted() {
    this.setDefault();
    this.lista.modalidad = "D??a";
  },
  methods: {
    async setDefault() {
      await this.cargarFuncionarios();
      await this.cargarNovedades();
      // await this.getlistaNovedades();
    },

    async validarAntesDeEnviar() {
      let validado = await this.$validator.validateAll();
      if (validado) {
        this.guardarNovedad();
        return;
      }
      this.$swal.fire(
        "Oops!",
        "Debe corregir los errores antes de enviar",
        "error"
      );
    },
    async cargarFuncionarios() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/novedades/funcionarios`)
        .then(funcionarios => {
          this.funcionariosDatos = funcionarios.data;
        });
    },
    async cargarNovedades() {
      axios
        .get(
          `/api/${localStorage.getItem("tenant")}/novedades/nombres_novedades`
        )
        .then(novedades => {
          this.nombresNovedadesDatosPartial = novedades.data;
          this.nombresNovedadesDatos = novedades.data;
        });
    },
    obtenerTipoNovedad(id) {
      let novedad = this.nombresNovedadesDatos.find(novedad => {
        return novedad.id === id;
      });
      let tipo = novedad.concepto.split(" ")[0];
      this.lista.tipo = tipo;
    },
    asignarIdFuncionario(funcionario) {
      this.lista.funcionario_id = !funcionario ? "" : funcionario.id;
    },
    nombreCompleto({ nombres, apellidos }) {
      return `${nombres} ${apellidos}`;
    },
    guardarNovedad() {
      this.lista.modalidad = "D??a";
      if (!this.lista.id) {
        axios
          .post(
            `/api/${localStorage.getItem("tenant")}/novedades/crear`,
            this.$data.lista
          )
          .then(respuesta => {
            this.customResponse(respuesta.data);
            this.guardadoDatosFin();
          })
          .catch(error => {
            this.errorDelServidor(error);
          });
      } else {
        axios
          .put(
            `/api/${localStorage.getItem("tenant")}/novedades/${
              this.lista.id
            }/editar`,
            this.$data.lista
          )
          .then(respuesta => {
            this.customResponse(respuesta.data);
            this.guardadoDatosFin();
          })
          .catch(error => {
            this.errorDelServidor(error);
          });
      }
    },
    guardadoDatosFin() {
      this.$emit("recargar");
      this.$emit("recargar2");
      modalEmitter.$emit("cerrar", this.$parent.id);
      this.limpiarFormulario();
      this.$validator.reset();
    },
    errorDelServidor(error) {
      if (error.response) {
        this.$swal.fire(
          "Error!",
          "Han ocurrido errores, por favor intente m??s tarde",
          "error"
        );
      }
    },
    customResponse(data) {
      if (data.success) {
        this.$swal.fire("Exito!", data.message, "success");
      } else {
        this.$swal.fire("Error!", data.message, "error");
      }
    },
    limpiarFormulario() {
      for (let propiedad in this.$data.lista) {
        this.$data.lista[propiedad] = "";
      }
      this.funcionario = "";
      this.deshabilitarCampo = false;
    }
  }
};
</script>

<style scoped>
input[type="date"]::-webkit-inner-spin-button,
input[type="datetime-local"]::-webkit-inner-spin-button {
  display: none;
  -webkit-appearance: none;
}
</style>