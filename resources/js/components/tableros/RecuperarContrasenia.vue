<template>
  <div>
    <form @submit.prevent="validarAntesDeEnviar">
      <div class="form-group md-6">
        <label class="has-float-label">Contraseña</label>
        <input
          required
          class="form-control"
          type="password"
          name="password"
          v-validate="'required|min:6'"
          v-model="formulario.password"
          placeholder
        />
        <small class="invalid">{{ errors.first("password") }}</small>
      </div>

      <div class="w-100" />

      <div class="form-group md-6">
        <label class="has-float-label">Confirmar</label>

        <input
          class="form-control"
          type="password"
          name="password"
          v-validate="'required|min:6'"
          v-model="formulario.passwordConfirm"
          placeholder
        />
      </div>

      <input
        class="btn btn-outline-primary btn-sm"
        type="submit"
        name="submit"
        v-validate="'required|min:6'"
      />
    </form>

    <notifications group="notificaciones" position="bottom right" />
  </div>
</template>

<script>
export default {
  props: {},
  data() {
    return {
      formulario: {
        password: "",
        passwordConfirm: ""
      }
    };
  },
  mounted() {},
  methods: {
    validarAntesDeEnviar() {
      if (this.formulario.password != this.formulario.passwordConfirm) {
        this.$notify({
          group: "notificaciones",
          title: "Notificación Importante",
          text: "Las contraseñas no coinciden"
        });
        return false;
      }

      axios
        .post(`/api/${localStorage.getItem("tenant")}/reset-password`, {
          password: this.formulario.password
        })
        .then(respuesta => {
          localStorage.removeItem("usuario");
          localStorage.setItem("usuario", JSON.stringify(respuesta.data.data[0]));
          this.$emit("notificar", respuesta.data.message);
          modalEmitter.$emit("cerrar", "elementPassword");
        })
        .catch(error => {
          this.$swal.fire(
            "Oops!",
            "Ha ocurrido un error, por favor intente más tarde",
            "error"
          );
        });
    }
  }
};
</script>
