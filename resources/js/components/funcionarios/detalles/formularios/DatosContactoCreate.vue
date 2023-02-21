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
          v-validate="'required'"
          data-vv-as="nombre completo"
          v-model="contactos_emergencia.nombre_completo"
        />

        <div v-if="errors.has('nombre_completo')">
          <small class="invalid">{{ errors.first("nombre_completo") }}</small>
        </div>
      </div>
      <div class="form-group col-md-4">
        <label for="parentesco">Parentesco</label>
        <input
          type="text"
          name="parentesco"
          class="form-control"
          id="parentesco"
          v-validate="'required'"
          data-vv-as="parentesco"
          v-model="contactos_emergencia.parentesco"
        />

        <div v-if="errors.has('parentesco')">
          <small class="invalid">{{ errors.first("parentesco") }}</small>
        </div>
      </div>
      <div class="form-group col-md-4">
        <label for="telefono">Teléfono</label>
        <input
          type="number"
          name="telefono"
          class="form-control"
          id="telefono"
          placeholder="Teléfono"
          v-validate="'required'"
          data-vv-as="Teléfono"
          v-model="contactos_emergencia.telefono"
        />

        <div v-if="errors.has('telefono')">
          <small class="invalid">{{ errors.first("telefono") }}</small>
        </div>
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
          v-validate="'required'"
          data-vv-as="celular"
          v-model="contactos_emergencia.celular"
        />

        <div v-if="errors.has('celular')">
          <small class="invalid">{{ errors.first("celular") }}</small>
        </div>
      </div>
      <div class="form-group col-md-8">
        <label for="direccion">Dirección de residencia</label>
        <input
          type="text"
          name="direccion"
          class="form-control"
          id="direccion"
          v-validate="'required'"
          data-vv-as="direccion"
          v-model="contactos_emergencia.direccion"
        />

        <div v-if="errors.has('direccion')">
          <small class="invalid">{{ errors.first("direccion") }}</small>
        </div>
      </div>
    </div>

    <button
      type="submit"
      ref="sendDataContactEmergency"
      class="btn btn-secondary btn-sm d-block mt-3"
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
      mostrarModal: true,
      message: "Guardar",
      contactos_emergencia: {
        nombre_completo: "",
        parentesco: "",
        telefono: "",
        celular: "",
        direccion: "",
        funcionario_id: this.funcionarioEditar.id
      }
    };
  },

  mounted() {},

  methods: {
    async guardarDatosContacto() {
      let validado = await this.$validator.validateAll();
      if (validado) {
        console.log("update");
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
          `/api/${localStorage.getItem("tenant")}/contactos-emergencia`,
          this.contactos_emergencia
        )
        .then(res => {
          console.log("response");
          this.message = "Guardar";
          this.$emit("mensaje", res.data.message);
          this.$emit("update", res.data.data);
          modalEmitter.$emit("cerrar", "datosContacto");
        })
        .catch(error => {
          console.log(error.response.data);
          this.message = "Guardar";
        });

      this.$refs["sendDataContactEmergency"].disabled = false;
    }
  }
};
</script>
