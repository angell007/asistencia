<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form @submit.prevent>
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label for="nombre_empresa">Nombre de la Empresa</label>
                  <input
                    type="text"
                    name="nombre_empresa"
                    class="form-control"
                    id="nombre_empresa"
                    placeholder=""
                    v-validate="'required'"
                    data-vv-as="nombre empresa"
                    v-model="experiencia.nombre_empresa"
                  />
                  <small
                    class="invalid col-md-7"
                    v-if="errors.has('nombre_empresa')"
                    >{{ errors.first("nombre_empresa") }}</small
                  >
                </div>
                <div class="form-group col-md-3">
                  <label for="Cargo">Cargo</label>
                  <input
                    type="text"
                    name="cargo"
                    class="form-control"
                    id="cargo"
                    placeholder=""
                    v-validate="'required'"
                    v-model="experiencia.cargo"
                  />
                  <small class="invalid col-md-7" v-if="errors.has('cargo')">{{
                    errors.first("cargo")
                  }}</small>
                </div>
                <div class="form-group col-md-3">
                  <label for="fecha_ingreso">Fecha de ingreso</label>
                  <input
                    type="date"
                    name="fecha_ingreso"
                    class="form-control"
                    id="fecha_ingreso"
                    placeholder=""
                    v-validate="'required'"
                    data-vv-as="fecha ingreso"
                    v-model="experiencia.fecha_ingreso"
                  />
                  <small
                    class="invalid col-md-7"
                    v-if="errors.has('fecha_ingreso')"
                    >{{ errors.first("fecha_ingreso") }}</small
                  >
                </div>
                <div class="form-group col-md-3">
                  <label for="fecha_retiro">Fecha de retiro</label>
                  <input
                    type="date"
                    name="fecha_retiro"
                    class="form-control"
                    id="fecha_retiro"
                    placeholder=""
                    v-validate="'required'"
                    data-vv-as="fecha retiro"
                    v-model="experiencia.fecha_retiro"
                  />
                  <small
                    class="invalid col-md-7"
                    v-if="errors.has('fecha_retiro')"
                    >{{ errors.first("fecha_retiro") }}</small
                  >
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="nombre_jefe">Nombre del Jefe Inmediato</label>
                  <input
                    type="text"
                    name="nombre_jefe"
                    class="form-control"
                    id="nombre_jefe"
                    v-validate="'required'"
                    data-vv-as="nombre jefe"
                    v-model="experiencia.nombre_jefe"
                  />
                  <small
                    class="invalid col-md-7"
                    v-if="errors.has('nombre_jefe')"
                    >{{ errors.first("nombre_jefe") }}</small
                  >
                </div>

                <div class="form-group col-md-6">
                  <label for="telefono_empresa">Telefono de la empresa</label>
                  <input
                    type="number"
                    name="telefono_empresa"
                    class="form-control"
                    id="telefono_empresa"
                    v-validate="'required|numeric'"
                    data-vv-as="telefono empresa"
                    v-model="experiencia.telefono_empresa"
                  />
                  <small
                    class="invalid col-md-7"
                    v-if="errors.has('telefono_empresa')"
                    >{{ errors.first("telefono_empresa") }}</small
                  >
                </div>

                <div class="form-group col-md-12">
                  <label for="labores">Labores</label>
                  <textarea
                    class="form-control"
                    name="labores"
                    id="labores"
                    rows="10"
                    v-validate="'required'"
                    data-vv-as="labores"
                    v-model="experiencia.labores"
                  ></textarea>
                  <small
                    class="invalid col-md-7"
                    v-if="errors.has('labores')"
                    >{{ errors.first("labores") }}</small
                  >
                </div>
              </div>

              <button
                type="submit"
                ref="sendDataContactEmergency"
                class="btn btn-secondary btn-sm d-block mt-3"
                @click="guardarDatosExperiencia"
              >
                {{ message }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>



<script>
export default {
  props: {
    funcionarioEditar: Object,
  },
  data() {
    return {
      mostrarModal: true,
      message: "Guardar",
      experiencia: {
        nombre_empresa: "",
        cargo: "",
        fecha_ingreso: "",
        fecha_retiro: "",
        nombre_jefe: "",
        telefono_empresa: "",
        labores: "",
        funcionario_id: this.funcionarioEditar.id,
      },
    };
  },

  mounted() {},

  methods: {
    async guardarDatosExperiencia() {
      let validado = await this.$validator.validateAll();
      if (validado) {
        this.sendPost();
        return;
      }
      this.$swal.fire(
        "Oops!",
        "Debe corregir los errores antes de enviar",
        "error"
      );
    },

    sendPost() {
      this.$refs["sendDataContactEmergency"].disabled = true;
      this.message = "Enviando...";

      axios
        .post(
          `/api/${localStorage.getItem("tenant")}/funcionarios/${
            this.funcionarioEditar.identidad
          }/experiencia/crear`,
          this.experiencia
        )
        .then((res) => {
          this.message = "Guardar";
          this.$emit("mensaje", res.data.message);
          this.$emit("update", res.data.data);
          modalEmitter.$emit("cerrar", "datosExperiencia");
        })
        .catch((error) => {
          console.log(error.response.data);
          this.message = "Guardar";
        });

      this.$refs["sendDataContactEmergency"].disabled = false;
    },
  },
};
</script>
