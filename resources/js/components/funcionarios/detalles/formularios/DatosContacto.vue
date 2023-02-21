<template>
  <form @submit.prevent>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="nombre_completo">Nombre completo</label>
        <input
          type="text"
          name="nombre_completo"
          class="form-control"
          id="nombre_completo"
          v-model="funcionarioEditar.contactos_emergencia.nombre_completo"
        />
      </div>
      <div class="form-group col-md-4">
        <label for="parentesco">Parentesco</label>
        <input
          type="text"
          name="parentesco"
          class="form-control"
          id="parentesco"
          v-model="funcionarioEditar.contactos_emergencia.parentesco"
        />
      </div>
      <div class="form-group col-md-4">
        <label for="telefono">Teléfono</label>
        <input
          type="number"
          name="telefono"
          class="form-control"
          id="telefono"
          placeholder="Teléfono"
          v-model="funcionarioEditar.contactos_emergencia.telefono"
        />
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="celular">Celular</label>
        <input
          type="number"
          name="celular"
          class="form-control"
          id="celular"
          v-model="funcionarioEditar.contactos_emergencia.celular"
        />
      </div>
      <div class="form-group col-md-8">
        <label for="direccion">Dirección de residencia</label>
        <input
          type="text"
          name="direccion"
          class="form-control"
          id="direccion"
          v-model="funcionarioEditar.contactos_emergencia.direccion"
        />
      </div>
    </div>

    <button
      type="submit"
      class="btn btn-secondary btn-sm d-block mt-3"
      ref="sendDataEditEmergenci"
      @click="guardarDatosContacto"
    >{{ message }}</button>
  </form>
</template>

<script>
export default {
  props: {
    funcionarioEditar: Object
  },
  data() {
    return {
      mostrarModal: false,
      message: "Guardar"
    };
  },

  mounted() {},
  methods: {
    guardarDatosContacto() {
      this.$refs["sendDataEditEmergenci"].disabled = true;
      this.message = "Enviando...";

      axios
        .put(
          `/api/${localStorage.getItem("tenant")}/funcionarios/${
            this.funcionarioEditar.id
          }/contacto/${this.funcionarioEditar.contactos_emergencia.id}/editar`,
          this.funcionarioEditar.contactos_emergencia
        )
        .then(respuesta => {
          this.$refs["sendDataEditEmergenci"].disabled = false;
          this.message = "Guardar";

          this.message = "Guardar";
          this.$emit("mensaje", respuesta.data.message);
          this.$emit("update", respuesta.data.data);
          modalEmitter.$emit("cerrar", "datosContacto");
        })
        .catch(error => {
          console.log(error.response.data);
          this.$refs["sendDataEditEmergenci"].disabled = false;
          this.message = "Guardar";
        });
    }
  }
};
</script>

<style>
</style>
