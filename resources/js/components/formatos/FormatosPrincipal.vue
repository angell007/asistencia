<template>
  <div class="container">
    <h1 class="font-weight-bold">Formatos</h1>
    <div class="row text-center justify-content-center">
      <div class="form-group col-md-6 text-center">
        <img
          id="imageOutput"
          :src="`${myempresa}`"
          alt="Imagen del funcionario"
          class="d-block m-auto"
        />
        <label for="image" class="subir-archivo mt-2" style=" width: 290px; height: 30px;">
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
    </div>

    <div class="row text-center justify-content-center">
      <div class="alert alert-secondary col-md-12 text-center">
        <div class="d-flex justify-content-between align-items-center">
          <p>Lista de formatos</p>
          <button
            class="btn btn-primary btn-lg btn-shadow"
            name="button"
            type="submit"
            v-on:click="showModal('formato')"
          >Nuevo+</button>
        </div>

        <modal id="formato" ref="formato" :esLg="true">
          <template slot="titulo">Creando Formato</template>
          <template slot="contenido">
            <form-formato />
          </template>
        </modal>
      </div>
    </div>

    <div class="row d-flex justify-content table">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Reponsable</th>
            <th scope="col">Encabezado</th>
            <th scope="col">Ver</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(formatsAvailable, index) in formatsAvailables " :key="index">
            <th scope="row">{{index + 1}}</th>
            <th>{{formatsAvailable.responsable}}</th>
            <td>{{formatsAvailable.encabezado}}</td>
            <td class="text-center" @click="showFormat(formatsAvailable.id)" style="cursor:pointer">
              <i class="iconsmind-Full-View data-icon" style="font-size: 1.5em"></i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <modal id="formatoUpdate" ref="formatoUpdate" :esLg="true">
      <template slot="titulo">Creando Formato</template>
      <template slot="contenido">
        <div class="col-md-12 text-justify">
          <div class="form-group">
            <label for="encabezado" class="col-md-12 col-form-label custom-label">Encabezado</label>
            <input
              v-model="dataForm.encabezado"
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
            <img
              id="imageOutput2"
              :src="dataForm.firma"
              alt=" Firma digital "
              class="d-block m-auto"
            />
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

          <!-- <div class="d-flex justify-content-between align-items-center">
            <button
              v-on:click="updateFormato()"
              class="btn btn-primary btn-lg btn-shadow"
              name="button"
              type="submit"
            >Guardar</button>
          </div>-->
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Modal from "../utiles/Modal";
import FormFormato from "./FormFormato";
import FormFormatoUpdate from "./FormFormatoUpdate";

export default {
  components: { Modal, FormFormato, FormFormatoUpdate },

  data() {
    return {
      imagenTemporal: "https://via.placeholder.com/150x50",
      imagenTemporal2: "",
      formatsAvailables: Array,
      brand: false,
      formulario: {
        cuerpo: "",
        encabezado: "",
        responsable: "",
        informacion: ""
      },
      dataForm: {
        cuerpo: "",
        encabezado: "",
        responsable: "",
        informacion: ""
      }
    };
  },

  beforeMount() {
    this.showFormatsAvailable();
  },
  methods: {
    vistaPreviaImagen(event) {
      const image = document.getElementById("imageOutput");
      image.src = URL.createObjectURL(event.target.files[0]);
      if (event.target.files.length > 0) {
        this.imagenTemporal = event.target.files[0];
      }
    },

    vistaPreviaImagen2(event) {
      const image = document.getElementById("imageOutput2");
      image.src = URL.createObjectURL(event.target.files[0]);
      if (event.target.files.length > 0) {
        this.imagenTemporal2 = event.target.files[0];
      }
    },

    showModal(modal) {
      this.$refs[modal].mostrarModal();
    },

    showFormatsAvailable() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/formatos`)
        .then(datos => {
          this.formatsAvailables = datos.data.message;
        });
    },

    showFormat(id) {
      this.brand = true;
      this.dataForm = this.formatsAvailables.find(format => {
        return format.id == id;
      });
      CKEDITOR.instances["mytextarea2"].setData(this.dataForm.cuerpo);
      this.showModal("formatoUpdate");
    },

    updateFormato() {
      const data = new FormData();

      data.append("cuerpo", this.dataForm.cuerpo);
      data.append("encabezado", this.dataForm.encabezado);
      data.append("responsable", this.dataForm.responsable);
      data.append("informacion", this.dataForm.informacion);

      data.append("content", CKEDITOR.instances["mytextarea"].getData());
      data.append("firma", this.imagenTemporal2);

      axios
        .put(
          `/api/${localStorage.getItem("tenant")}/formatos/${
            this.dataForm.id
          }/edit`,
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
/* fieldset legend {
   font-size: 1.2rem; 
} */
#imageOutput {
  height: 280px;
  width: 290px;
  border-radius: 1%;
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
