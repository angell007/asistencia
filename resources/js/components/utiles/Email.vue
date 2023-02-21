<template>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <form method="POST" action>
              <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail</label>

                <div class="col-md-8">
                  <input
                    id="email"
                    type="email"
                    class="form-control"
                    name="email"
                    value
                    v-model="email"
                    required
                  />

                  <span class="invalid-feedback" role="alert"></span>
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-md-8 offset-md-4">
                  <button
                    v-on:click.prevent="sendEmail()"
                    class="btn btn-primary"
                  >Enviar link de recuperación</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: ""
    };
  },
  methods: {
    sendEmail() {
      axios
        .post("/auth/pass-reset", { email: this.email })
        .then(datos => {
            this.$notify({
            group: "notificaciones",
            title: "Notificación Importante",
            text: "Revise su email porfavor"
          });
          console.log(datos);
          modalEmitter.$emit("cerrar", "passreset");
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

<style>
</style>