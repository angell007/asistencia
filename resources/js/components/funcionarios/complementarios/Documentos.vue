<template>
  <div class="documents container">
    <template class="row">
      <vue-dropzone
      class="d-flex justify-content-center"
        id="upload"
        style=""
        :options="getConfig()"
        @vdropzone-complete="enCompletado"
      >
        <div
          class="dropzone-custom-content  col-md-9 col-9 mx-2"
        >
          <h3 class="dropzone-custom-title">
            Arrastre y agregue los documentos!
          </h3>
        </div>
      </vue-dropzone>

      <div class="row">
        <target-component
          @getFiles="getFiles"
          v-for="(file, index) in files"
          :key="index"
          :nombre="file.nombre"
          :path="file.path"
          :id="file.id"
          :extension="file.extension"
        ></target-component>
      </div>
    </template>
  </div>
</template>

<script>
import vueDropzone from "vue2-dropzone";

export default {
  components: { vueDropzone },
  props: {
    idFuncionario: Number,
  },
  data() {
    return {
      id: "",
      funcionarioIdentidad: "",
      funcionarioCreado: false,
      identidad: 0,
      files: [],
    };
  },

  beforeMount() {
    this.getFiles();
  },

  created() {
    this.identidad = this.idFuncionario;
    // funcionarioEmitter.$on("creado", (datos) => {
    //   this.funcionarioCreado = datos.crear;
    //   this.funcionarioIdentidad = datos.identidad;
    // });
  },

  methods: {
    enCompletado(file) {
      this.getFiles();
    },
    getConfig() {
      return {
        url: `/api/${localStorage.getItem(
          "tenant"
        )}/funcionarios/documento/crear/${this.identidad}`,
        dictDefaultMessage:
          "Aquí podrá cargar los documentos que requiere tener del funcionario, como su contrato, afiliaciones hoja de vida, etc.",
        addRemoveLinks: true,
        maxFiles: 4,
        maxFilesize: 3,
        chunkSize: 500,
      };
    },

    async getFiles() {
      const data = await axios.post(
        `/api/${localStorage.getItem(
          "tenant"
        )}/funcionarios/documento/getFiles/${this.identidad}`
      );
      this.files = data.data;
    },
  },

  computed: {
    iconFont: function () {
      return this.message.split("").reverse().join("");
    },
  },
};
</script>

<style scoped>
</style>
