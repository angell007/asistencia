<template>
  <form @submit.prevent="validarAntesDeEnviar">
    <div class="form-row">
      <div class="col-md-5">
        <p class="custom-label">Foto de usuario / logo marca</p>
      </div>

      <div class="form-group col-md-2 text-center">
        <img
          id="imageOutput"
          :src="`${myempresa}`"
          alt="Imagen del funcionario"
          class="d-block m-auto"
        />
        <label for="image" class="subir-archivo mt-2" style=" width: 150px; height: 50px;">
          <i class="simple-icon-doc"></i>
          Cargar imagen
        </label>

        <input
          type="file"
          accept="image/*"
          name="image"
          v-validate="'required|size:800'"
          data-vv-as="Imagen"
          class="form-control-file"
          id="image"
          placeholder="Imagen"
          @change="vistaPreviaImagen"
        />

        <div class="col-md-12 text-center" v-if="errors.has('image')">
          <small class="invalid">{{errors.first('image')}}</small>
        </div>
      </div>

      <div class="form-group col-md-12">
        <label for="razon_social" class="custom-label">Razón social</label>
        <input
          type="text"
          v-validate="'required'"
          data-vv-as="Razón social"
          name="razon_social"
          class="form-control custom-control"
          v-model="empresaEditar.razon_social"
        />
        <small class="invalid" v-if="errors.has('razon_social')">
          {{
          errors.first("razon_social")
          }}
        </small>
      </div>
      <div class="form-group col-md-6">
        <label for="tipo_documento" class="custom-label">Tipo de documento</label>
        <select
          name="tipo_documento"
          class="form-control custom-control"
          v-model="empresaEditar.tipo_documento"
        >
          <option value="NIT">NIT</option>
          <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
          <option value="Cédula de extranjería">Cédula de extranjería</option>
          <option value="Tarjeta de identidad">Tarjeta de identidad</option>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="numero_documento" class="custom-label">Número de documento</label>
        <input
          type="text"
          name="numero_documento"
          v-validate="'required|max:20'"
          data-vv-as="Número de documento"
          class="form-control custom-control"
          v-model="empresaEditar.numero_documento"
        />
        <small class="invalid" v-if="errors.has('numero_documento')">
          {{
          errors.first("numero_documento")
          }}
        </small>
      </div>

      <div class="form-group col-md-6" v-if="empresaEditar.tipo_documento == 'NIT'">
        <label for="digito_verificacion" class="custom-label">Digito de verificacion</label>
        <input
          type="text"
          name="digito_verificacion"
          v-validate="'required'"
          data-vv-as="Digito de verificacion"
          class="form-control custom-control"
          v-model="empresaEditar.digito_verificacion"
        />
        <small class="invalid" v-if="errors.has('digito_verificacion')">
          {{
          errors.first("digito_verificacion")
          }}
        </small>
      </div>

      <div class="form-group col-md-12">
        <label for="fecha_constitucion" class="custom-label">Fecha de constitución</label>
        <calendario :fecha="empresaEditar.fecha_constitucion" ref="calendarioDatosBasicos"></calendario>
      </div>
      <div class="form-group col-md-7">
        <label for="email_contacto" class="custom-label">Email de contacto</label>
        <input
          type="email"
          name="email_contacto"
          v-validate="'required|email'"
          data-vv-as="Email de contacto"
          class="form-control custom-control"
          v-model="empresaEditar.email_contacto"
        />
        <small class="invalid" v-if="errors.has('email_contacto')">
          {{
          errors.first("email_contacto")
          }}
        </small>
      </div>
      <div class="form-group col-md-5">
        <label for="telefono_contacto" class="custom-label">Teléfono de contacto</label>
        <input
          type="text"
          name="telefono_contacto"
          v-validate="'required|min:6|max:20'"
          data-vv-as="Teléfono de contacto"
          class="form-control custom-control"
          v-model="empresaEditar.telefono_contacto"
        />
        <small class="invalid" v-if="errors.has('telefono_contacto')">
          {{
          errors.first("telefono_contacto")
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
import Calendario from "../../utiles/Calendario";

export default {
  components: { Calendario },
  props: {
    empresaEditar: Object
  },
  data() {
    return {
      imagenTemporal: "",
      bodyFormData: ""
    };
  },

  methods: {
    vistaPreviaImagen(event) {
      const image = document.getElementById("imageOutput");
      image.src = URL.createObjectURL(event.target.files[0]);
      if (event.target.files.length > 0) {
        this.imagenTemporal = event.target.files[0];
      }
    },

    validarAntesDeEnviar() {
      // this.$validator.validateAll().then(resultado => {
      //   if (resultado) {
      this.guardarDatosBasicos();
      //     return;
      //   }
      //   this.$swal.fire("Ooops!", "Corrige los errores", "error");
      // });
    },

    guardarDatosBasicos() {
      this.empresaEditar.fecha_constitucion = moment(
        this.$refs.calendarioDatosBasicos.fechaSeleccionada
      ).format("YYYY-MM-DD");

      let bodyFormData = new FormData();
      bodyFormData.append("empresa", JSON.stringify(this.empresaEditar));
      bodyFormData.append("image", this.imagenTemporal);

      this.bodyFormData = bodyFormData;

      axios
        .post(
          `/api/${localStorage.getItem("tenant")}/general/empresaupdate/${
            this.empresaEditar.id
          }/editar`,
          this.bodyFormData
        )
        .then(respuesta => {
          localStorage.removeItem("empresa");
          localStorage.setItem("empresa", JSON.stringify(respuesta.data.data));

          this.$validator.reset();
          modalEmitter.$emit("cerrar", "datosBasicos");
          this.$emit("notificar", respuesta.data.message);
          this.$emit("updateData");
        })
        .catch(error => {
          this.$swal.fire("Oops!", error.data.message, "error");
        });
    }
  },

  watch: {
    // "empresaEditar.numero_documento": function () {
    //   if (
    //     this.empresaEditar.numero_documento.split("-").join("").length % 3 ==
    //     0
    //   ) {
    //     this.empresaEditar.numero_documento = this.empresaEditar.numero_documento
    //       .toString()
    //       .replace(/\B(?=(\d{3})+(?!\d))/g, "-");
    //   }
    // },
  }
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
/* fieldset legend {
   font-size: 1.2rem; 
} */
#imageOutput {
  width: 150px;
  height: 50px;
  border-radius: 1%;
}
</style>

