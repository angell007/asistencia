<template>
  <div class="container" v-if="renderizar">
    <h1 class="row font-weight-bold">Asignación de Horarios</h1>
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="centroCosto" class="custom-label">Filtrar por centro de costos</label>
          <select
            name="centroCosto"
            class="form-control custom-control"
            v-model="filtroCentrosCosto"
            @change="filtrarCentroCostos(filtroCentrosCosto)"
          >
            <option value="todos">Todos</option>
            <option
              v-for="(centro, index) in datosGenerales"
              :key="index"
              :value="centro.id"
            >{{ centro.nombre }}</option>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="dependencia" class="custom-label">Filtrar por dependencia</label>
          <select
            name="dependencia"
            class="form-control custom-control"
            v-model="filtroDependencia"
            :disabled="filtroCentrosCosto == 'todos'"
          >
            <option value="todos">Todos</option>
            <option
              v-for="(dependencia, index) in filtroDependencias"
              :key="index"
              :value="dependencia.id"
            >{{ dependencia.nombre }}</option>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <label for="centroCosto" class="custom-label">Nombre funcionario</label>
        <input
          :readonly="filtroCentrosCosto == 'todos'"
          placeholder="Buscar..."
          class="custom-control custom"
          v-model="Namefilter"
        />
      </div>

      <div class="col-md-12 mt-2">
        <div class="form-group">
          <button
            class="btn btn-secondary default btn-xs"
            @click="filtrar(filtroDependencia, filtroCentrosCosto)"
          >
            <i class="simple-icon-magnifier"></i> Filtrar
          </button>
        </div>
      </div>

      <div class="col-md-12 my-2">
        <input
          type="week"
          name="semana"
          class="form-control custom-control text-center"
          v-model="semana"
          @change="makeRequestBySemana(semana)"
          required
        />
      </div>
    </div>

    <template v-if="datosGenerales.length">
      <div class="card text-justify" v-for="centroCosto in datosGenerales" :key="centroCosto.id">
        <div class="card-title custom-label mx-4 my-3">
          {{ centroCosto.nombre }}
          <div class="w-100"></div>

          <button
            class="btn rounded btn-info btn-xs"
            v-on:click="descargarInformeTurnos(centroCosto.id)"
          >
            <i class="iconsmind-File-HorizontalText data-icon"></i>
            Descargar
          </button>
        </div>

        <div class="card-body">
          <div v-for="dependencia in centroCosto.dependencias" :key="dependencia.id">
            <p class="custom-label">{{ dependencia.nombre }}</p>
            <!-- v-for="funcionario in dependencia.funcionarios" -->
            <semana
              :funcionarios="dependencia.funcionarios"
              :turnosRotativos="turnos"
              :diaInicial="diaInicialSemana"
              :diaFinal="diaFinalSemana"
              @asignados="postHorariosTurno"
            ></semana>
          </div>
        </div>
      </div>
    </template>

    <div class="alert alert-secondary" v-else>
      <p class="font-weight-bold text-center">
        Aún no existen centros de costos, puede agregarlos desde el menú
        "Configuración"
      </p>
    </div>
  </div>
  <overlay v-else></overlay>
</template>

<script>
import Overlay from "../utiles/Overlay";
import HorarioSemana from "./HorarioSemana";
import Semana from "./Semana";

export default {
  components: { HorarioSemana, Semana, Overlay },
  data() {
    return {
      renderizar: false,
      Namefilter: "",
      datosGenerales: [],
      datosGeneralesFiltro: [],
      turnos: [],
      semana: moment().format(moment.HTML5_FMT.WEEK),
      numeroSemana: moment().week(),
      diaInicialSemana: moment().startOf("week"),
      diaFinalSemana: moment().endOf("week"),
      horariosPostPromises: [],
      /** Filtros */
      filtroCentrosCosto: "todos",
      filtroDependencia: "todos",
      filtroDependencias: [],
      depenPartial: []
    };
  },

  created() {
    this.getAllData();
  },

  methods: {
    getAllData() {
      axios
        .all([
          axios(
            `/api/${localStorage.getItem("tenant")}/horarios/datos/generales/${
              this.numeroSemana
            }`
          ),
          axios(`/api/${localStorage.getItem("tenant")}/turnos/rotativos/datos`)
        ])
        .then(
          axios.spread((datosGenerales, turnos) => {
            console.log([datosGenerales.data, turnos.data]);

            this.datosGenerales = this.datosGeneralesFiltro =
              datosGenerales.data;
            this.turnos = turnos.data;
            this.renderizar = true;
          })
        );
    },
    /** Guardar datos de horario en Base de Datos */
    postHorariosTurno(horarios = []) {
      horarios.forEach(horario => {
        this.horariosPostPromises.push(this.postHorario(horario));
      });
      axios.all(this.horariosPostPromises).then(respuesta => {
        this.renderizar = false;
        this.getAllData();
      });
    },

    postHorario(horario) {
      axios.post(
        `/api/${localStorage.getItem("tenant")}/horario/turno_rotativo/crear`,
        horario
      );
    },

    makeRequestBySemana(semana) {
      this.numeroSemana = moment(semana).week();
      this.diaInicialSemana = moment(semana).startOf("week");
      this.diaFinalSemana = moment(semana).endOf("week");
      this.renderizar = false;
      this.getAllData();
    },

    /**
     * request for filter by centroCosto, Dependencia and name or last name
     * @return data filter from center costo with dependencia
     */

    filterByCenterCost(idCentroCosto, centroCosto, dependencias) {
      this.depenPartial = [];

      dependencias.forEach(depe => {
        axios
          .post(
            `/api/${localStorage.getItem("tenant")}/filter-asignacion-horarios`,
            {
              semana: this.numeroSemana,
              dependencia: depe.id,
              nombre: this.Namefilter
            }
          )
          .then(res => {
            console.log(typeof res.data);
            if (res.data) {
              this.depenPartial.push(res.data);
            }
          });

        this.datosGenerales = Array({
          id: idCentroCosto,
          nombre: centroCosto.nombre,
          dependencias: this.depenPartial
        });
      });
    },
    /*************************************************************************************Danilo************************************************************************************************ */

    descargarInformeTurnos(centro) {
      axios({
        method: "post",
        url: `/api/${localStorage.getItem("tenant")}/informe-asignacion-turnos`,
        params: {
          diaInicialSemana: moment(this.semana).startOf("week").format('YYYY-MM-DD'),
          diaFinalSemana: moment(this.semana).endOf("week").format('YYYY-MM-DD'),
          centro: centro
        },
        responseType: "arraybuffer"
      })
        .then(resp => {
          let blob = new Blob([resp.data], { type: "application/pdf" });
          let link = document.createElement("a");
          // const filename = contentDisposition.match(/filename="(.+)"/)[1];
          link.href = window.URL.createObjectURL(blob);
          link.download = `informe-funcionarios.pdf`;
          link.click();
        })
        .catch(err => {
          console.log(err);
        });
    },

    /************************************************************************************************************************************************************************************* */
    /** Filtros de búsqueda  */
    filtrarCentroCostos(idCentroCosto) {
      if (idCentroCosto == "todos") {
        this.filtroDependencia = "todos";
        this.filtroDependencias = [];
      } else {
        const centroCosto = this.datosGenerales.find(centro => {
          return centro.id == idCentroCosto;
        });

        this.filtroDependencias = centroCosto.dependencias;
      }
    },

    filtrar(idDependencia, idCentroCosto) {
      if (idCentroCosto == "todos") {
        this.datosGenerales = this.datosGeneralesFiltro;
        this.filtroDependencia = "todos";
      } else {
        const centroCosto = this.datosGeneralesFiltro.find(centroCosto => {
          return centroCosto.id === idCentroCosto;
        });
        let dependencias = null;
        if (idDependencia === "todos") {
          dependencias = centroCosto.dependencias;
        } else {
          dependencias = Array(
            centroCosto.dependencias.find(dependencia => {
              return dependencia.id === idDependencia;
            })
          );
        }
        this.filterByCenterCost(idCentroCosto, centroCosto, dependencias);
      }
    }
  }
};
</script>

<style scoped>
input[type="week"],
input[type="week"]:focus {
  border-color: #2a93d5;
  background-color: #2a93d5;
  color: #fff;
  font-size: 13px;
  padding: 0;
}

.custom {
  background: none;
  outline: initial !important;
  /* border-radius: 15px; */
  padding: 0.25rem 0.75rem 0.45rem 0.9rem;
  font-size: 0.76rem;
  line-height: 0;
  border: 1px solid #8f8f8f;
  color: #212121;
  height: 28px;
}
</style>
