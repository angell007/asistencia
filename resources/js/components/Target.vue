<template>
  <div class="col-md-2 col-xs-12 mb-2">
    <div class="card">
      <div class="card-body text-center d-float flex-column card-file" > 
        <div class="preview">
          <img v-if="extension=='jpg'||extension=='png'" :src="`${path}`" class="img-recortada"  />
          <i v-else :class="(extension=='doc'||extension=='docx' ? 'iconsmind-File-Word' : (extension=='xls'||extension=='xlsx' ? 'iconsmind-File-Excel' : 'iconsmind-File-TextImage'))" ></i>
        </div>
        <p class="nombre-archivo text-center">{{ nombre }}.{{extension}}</p>
        <div class="botones align-content-end">
          <a
            :href="path"
            target="_blank"
            class="boton-archivo btn-info justify-content-center"
          >
            <i class="simple-icon-cloud-download"></i
          ></a>
          <a
            href="#"
            target="_blank"
            class="boton-archivo btn-danger justify-content-center"
            @click.prevent="deleteFile(id)"
          >
            <i class="simple-icon-trash"></i
          ></a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: { nombre: String, path: String, icon: String, id: Number, extension: String },

  methods: {
    deleteFile(id) {
      this.$swal
        .fire({
          title: "¿Está seguro(a)?",
          text: "Asegurese que desea realmente eliminar este archivo",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si,eliminarlo!",
        })
        .then(async (resultado) => {
          if (resultado.value) {
            const data = await axios.post(
              `/api/${localStorage.getItem(
                "tenant"
              )}/funcionarios/documento/deleteFiles/${id}`
            );
            this.$swal.fire("Bien hecho!", data.data, "success");
            this.$emit("getFiles");
          }
        });
    },
  },
};
</script>

<style scoped>
.card-file{
  padding: 10px; 
  height: 200px; 
  box-sizing: content-box;
}
.preview{
  width: 100%;
  height: 135px;
  max-height: 135px;
  overflow: hidden;
  position: relative;
}
.img-recortada{
  max-width:100%; 
  position:absolute;
  left: -100%;
  right: -100%;
  top: -100%;
  bottom: -100%;
  margin: auto;
  min-height: 100%;
  min-width: 100%; 
}
.nombre-archivo{
  font-family: "Lato", sans-serif;
  font-size: 10px;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
  height: 25px;
  line-height: 25px;
  margin: 0;
}
.botones{
  margin-top: 10px;
}
.boton-archivo{
  border-radius: 5px;
  height: auto;
  display: inline-block;
  padding: 3px 5px;
  margin: 0 5px;
}
.boton-archivo i{
  font-size: 15px;
  line-height: 20px;
}
.card .preview i {
  font-size: 125px;
  line-height: 135px;
  color: #4bb1f0;
}
.card .preview .iconsmind-File-TextImage{
  color:red;
}
.card .preview .iconsmind-File-Excel{
  color:green;
}

</style>
