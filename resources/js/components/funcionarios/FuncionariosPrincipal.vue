<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="mb-2">
          <h1 class="font-weight-bold">Lista de Funcionarios</h1>
          <div class="float-sm-right text-zero">
            <router-link to="/funcionarios/registro" class="btn btn-link mr-3">
              <i class="simple-icon-user-following"></i> Nuevo funcionario
            </router-link>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="mb-2">
          <a
            class="btn pt-0 pl-0 d-inline-block d-md-none"
            data-toggle="collapse"
            href="#displayOptions"
            role="button"
            aria-expanded="true"
            aria-controls="displayOptions"
          >
            <i class="simple-icon-arrow-down align-middle"></i>
          </a>
          <div class="collapse d-md-block" id="displayOptions">
            <div class="d-block d-md-inline-block">
              <div class="search-sm d-inline-block float-md-left mr-1 mb-1 align-top">
                <input placeholder="Buscar..." v-model="buscador" />
              </div>
              <select class="select-estado" v-model="estado">
                <option value="Activo">Activos</option>
                <option value="Inactivo">Inactivos</option>
                <option value="Liquidado">Liquidados</option>
              </select>

              <!-- <div class="col-md-12 mt-2"> -->
              <!-- <div class="form-group"> -->
              <button class="btn btn-secondary default btn-xs text-light" @click="filterOfficial()">
                <i class="simple-icon-magnifier"></i> Filtrar
              </button>
              <!-- </div> -->
              <!-- </div> -->
            </div>
            <div class="float-md-right" style="margin-right: 1rem">
              <span class="text-muted text-small">Cantidad de Funcionarios: {{ total }}</span>
            </div>
          </div>
        </div>
        <div class="separator mb-5"></div>
      </div>
    </div>

    <!-- Listado de Funcionarios-->
    <div
      class="row list disable-text-selection"
      data-check-all="checkAll"
      id="lista_funcionarios"
      v-if="total > 0"
    >
      <div
        class="col-xl-2 col-lg-2 col-12 col-md-2 col-sm-6 mb-4"
        v-for="funcionario in funcionariosDatos"
        :key="funcionario.id"
      >
        <tarjeta
          :id="funcionario.id"
          :fecha_ingreso="funcionario.contractultimate.fecha_inicio"
          :image="funcionario.image"
          :nombres="funcionario.nombres"
          :apellidos="funcionario.apellidos"
          :identidad="funcionario.identidad"
          :cargo="funcionario.contractultimate.cargo.nombre"
        ></tarjeta>
      </div>

      <div class="w-100 row"></div>

      <div class="col-12 d-flex justify-content-center">
        <nav aria-label>
          <ul class="pagination">
            <li class="page-item">
              <a class="page-link" style="cursor: pointer" v-on:click="changePage(page - 1)">Ant</a>
            </li>
            <li class="page-item">
              <a class="page-link" style="cursor: pointer">{{ page }}</a>
            </li>
            <li class="page-item">
              <a class="page-link" style="cursor: pointer" v-on:click="changePage(page + 1)">Sig</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <div class="alert alert-secondary" v-else>
      <div class="row warning-icon mb-1">
        <div class="col-12 text-center">
          <img src="/img/personal.svg" style="width: 500px; margin: 20px auto" class="mx-auto" />
          <p class="font-weight-bold mx-auto">
            No hay funcionarios con estos criterios de búsqueda, intente
            nuevamente.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Tarjeta from "./detalles/Tarjeta";

export default {
  components: { Tarjeta },
  data() {
    return {
      buscador: "",
      pages: 1,
      page: 1,
      total: 0,
      estado: "Activo",
      mostrarRegistro: false,
      nameFilter: "",
      funcionariosDatos: []
    };
  },

  created() {
    this.cargarFuncionarios();
  },

  methods: {
    changePage(page) {
      this.page = page <= 0 || page > this.pages ? this.page : page;
      this.cargarFuncionarios();
    },
    cargarFuncionarios() {
      const params = {
        page: this.page,
        nameFilter: this.buscador,
        estado: this.estado
      };
      axios
        .get(`/api/${localStorage.getItem("tenant")}/funcionarios/datos/`, {
          params
        })
        .then(datos => {
          console.log(datos.data);
          this.pages = datos.data.last_page;
          this.funcionariosDatos = datos.data.data;
          this.total = datos.data.total;
        })
        .catch(err => {
          if (err.response) {
            this.$swal.fire(
              "Error!",
              "Han ocurrido errores, por favor intente más tarde",
              "error"
            );
          }
        });
    },

    filterOfficial() {
      this.cargarFuncionarios();
    }
  },
  computed: {
    // items() {
    //   return this.funcionariosDatos.filter((item) => {
    //     return (
    //       (item.nombres.toLowerCase().includes(this.buscador.toLowerCase()) ||
    //         item.apellidos
    //           .toLowerCase()
    //           .includes(this.buscador.toLowerCase()) ||
    //         item.identidad.toString().includes(this.buscador.toLowerCase()) ||
    //         item.contractultimate.cargo.nombre
    //           .toLowerCase()
    //           .includes(this.buscador.toLowerCase())) &&
    //       item.estado.toLowerCase().includes(this.estado.toLowerCase())
    //     );
    //   });
    // },
  }
};
</script>

<style scoped>
#lista_funcionarios .card {
  height: 38%;
}
.warning-icon {
  font-size: 2rem;
}
.select-estado {
  background: none;
  outline: initial !important;
  border-radius: 15px;
  padding: 0.25rem 0.75rem 0.45rem 0.9rem;
  font-size: 0.76rem;
  line-height: 0;
  border: 1px solid #8f8f8f;
  color: #212121;
  height: 28px;
}
</style>
