<template>
  <div>
    <p>
      Convenciones:
      <br />
      <span>
        Para nombre empresa: @nombre_empresa@
        <br />Para nit empresa: @nit_empresa@
        <br />Para nombre funcionario: @nombre_funcionario@
        <br />Para documento funcionario: @documento_funcionario@
      </span>
    </p>
    <div class="col-md-12 text-justify">
      <div class="form-group">
        <label for="encabezado" class="col-md-12 col-form-label custom-label">Encabezado</label>
        <input
          v-model="dataForm['encabezado']"
          type="text"
          name="encabezado"
          v-validate="'required'"
          data-vv-as="Documento de encabezado"
          class="form-control custom-control col-md-12"
          placeholder="Encabezado"
        />

        <div class="col-md-12" v-if="errors.has('encabezado')">
          <small class="invalid">{{errors.first('encabezado')}}</small>
        </div>
      </div>

      <div class="form-group">
        <label for="responsable" class="col-md-12 col-form-label custom-label">Responsable</label>
        <input
          v-model="dataForm.responsable"
          type="text"
          name="responsable"
          v-validate="'required'"
          data-vv-as="Apellidos"
          class="form-control custom-control col-md-12"
          placeholder="Responsable"
        />
        <div class="col-md-12" v-if="errors.has('responsable')">
          <small class="invalid">{{errors.first('responsable')}}</small>
        </div>
      </div>

      <div class="form-group">
        <label
          for="informacion"
          class="col-md-12 col-form-label custom-label"
        >informacion (Opcional)</label>
        <input
          v-model="dataForm.informacion"
          type="text"
          name="informacion"
          v-validate="'required'"
          data-vv-as="Nombres"
          class="form-control custom-control col-md-12"
          placeholder="Informacion opcional"
        />
        <div class="col-md-12" v-if="errors.has('informacion')">
          <small class="invalid">{{errors.first('informacion')}}</small>
        </div>
      </div>

      <div class="form-group">
        <label for="cuerpo" class="col-md-12 col-form-label custom-label">Cuerpo de formato</label>
        <textarea
          v-model="dataForm.cuerpo"
          rows="15"
          cols="85"
          name="cuerpo"
          v-validate="'required'"
          class
          placeholder="
                  Certificamos que el señor @nombre_funcionario@ con cedula de ciudadania 
                  No @documento_funcionario@ se encuentra vinculado con la empresa  @nombre_empresa@
                  Nit. @nit_empresa@ como desarrollador full stack de la organizacion."
          id="mytextarea2"
        ></textarea>
        <div class="col-md-12 offset-4" v-if="errors.has('cuerpo')">
          <small class="invalid">{{errors.first('cuerpo')}}</small>
        </div>
      </div>

      <div class="form-group col-md-12 text-center">
        <img id="imageOutput2" :src="dataForm.firma" alt=" Firma digital " class="d-block m-auto" />
        <label for="firma" class="subir-archivo mt-2" style=" width: 150px; height: 50px;">
          <i class="simple-icon-doc"></i>
          Firma digital
        </label>

        <input
          type="file"
          accept="image/*"
          name="firma"
          v-validate="'required|size:800'"
          data-vv-as="Imagen"
          class="form-control-file"
          id="firma"
          placeholder="Imagen"
          @change="vistaPreviaImagen2"
        />

        <div class="col-md-12 text-center" v-if="errors.has('image')">
          <small class="invalid">{{errors.first('image')}}</small>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <button
          v-on:click="createdFormato()"
          class="btn btn-primary btn-lg btn-shadow"
          name="button"
          type="submit"
        >Guardar</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    dataForm: Object
  },

  data() {
    return {
      imagenTemporal2: "https://via.placeholder.com/150x50"
    };
  },

  created() {
    console.log(this.dataForm);
  },

  methods: {
    vistaPreviaImagen2(event) {
      const image = document.getElementById("imageOutput2");
      image.src = URL.createObjectURL(event.target.files[0]);
      if (event.target.files.length > 0) {
        this.imagenTemporal2 = event.target.files[0];
      }
    },
    createdFormato(modal) {
      this.formulario = Object.assign({}, this.dataForm);
      console.log(this.formulario);
      return false;
      const data = new FormData();
      data.append("cuerpo", this.formulario.cuerpo);
      data.append("encabezado", this.formulario.encabezado);
      data.append("responsable", this.formulario.responsable);
      data.append("informacion", this.formulario.informacion);

      // data.append("content", CKEDITOR.instances["mytextarea2"].getData());
      data.append("firma", this.imagenTemporal2);

      axios
        .put(
          `/api/${localStorage.getItem("tenant")}/formatos/${
            this.dataForm.id
          }/editar`,
          data
        )
        .then(datos => {});
    }
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
#imageOutput2 {
  height: 50px;
  width: 150px;
  border-radius: 1%;
}

.warning-icon {
  font-size: 2rem;
}
</style>
