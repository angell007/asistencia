<template>
  <form @submit.prevent="saveRecurrentBond">
    <div class="row col-12">
      <div class="form-group col-12">
        <label class="custom-label" for="">Tipo de Bono</label>
        <multiselect
          :max-height="200"
          placeholder="Seleccione una opción"
          selectLabel="Enter para seleccionar"
          deselectLabel="Enter para remover"
          v-validate="'required'"
          data-vv-as="Tipo de bono"
          v-model="tipo"
          :options="tipos"
          label="llave"
          @input="filtrarTipos()"
        >
          <template slot="noOptions">La lista está vacía</template>
          <template slot="noResult">No existen resultados!</template>
        </multiselect>
        <small class="invalid col-md-7" v-if="errors.has('tipo_bono_id')">{{
          errors.first("tipo_bono_id")
        }}</small>
      </div>

      <div class="form-group col-12">
        <label class="custom-label" for="tipo_bono_id">Bono</label>
        <multiselect
          :max-height="200"
          name="tipo_bono_id"
          placeholder="Seleccione una opción"
          selectLabel="Enter para seleccionar"
          deselectLabel="Enter para remover"
          v-validate="'required'"
          data-vv-as="Tipo de bono"
          v-model="bono.contable_ingreso_id"
          :options="bonosDisponibles"
          label="concepto"
        >
          <!-- @input="toggleFechaRetiro(bono.contable_ingreso_id)" -->
          <template slot="noOptions">La lista está vacía</template>
          <template slot="noResult">No existen resultados!</template>
        </multiselect>
        <small class="invalid col-md-7" v-if="errors.has('tipo_bono_id')">{{
          errors.first("tipo_bono_id")
        }}</small>
      </div>

      <div class="w-100"></div>

      <div class="form-group col-12">
        <label class="custom-label" for="fecha_retiro">Valor </label>
        <input
          type="text"
          name="valor"
          class="form-control"
          id="valor"
          placeholder="0.00"
          v-model="bono.valor"
        />
      </div>
    </div>
    <div class="form-group text-right">
      <button type="submit" class="btn btn-secondary raised">Guardar</button>
    </div>
  </form>
</template>

<script>
import Calendario from "../../../utiles/Calendario";
import DatosPersonales from "./DatosPersonales";
import estados from "../../registro/formularios/datos/atributosFuncionario";

export default {
  props: {
    contract: Object,
  },

  data() {
    return {
      bono: {
        valor: "",
        contable_ingreso_id: "",
        contrato_id: "",
      },
      bonosDisponibles: [],
      dataTiposBono: [],
      tipos: [],
      tipo: "",
    };
  },

  mounted() {
    this.getTiposBono();
    this.tipos = [
      { llave: "Constitutivo", valor: "Ingresos Prestacionales" },
      { llave: "No Constitutivo", valor: "Ingresos No Prestacionales" },
    ];
  },

  methods: {
    saveRecurrentBond() {
      this.bono.contable_ingreso_id = this.bono.contable_ingreso_id.id;
      this.bono.contrato_id = this.contract.id;

      this.mostarAlertaBono().then((resultado) => {
        if (resultado.value) {
          axios
            .post(`/api/${localStorage.getItem("tenant")}/bonos`, this.bono)
            .then((respuesta) => {
              this.$emit("mensaje", respuesta.data.message);
              this.$emit("update");
              modalEmitter.$emit("cerrar", "datosBono");
            })
            .catch((error) => {
              this.$swal.fire(
                "Oops!",
                "Ha ocurrido un error, por favor intente más tarde",
                "error"
              );
            });
        }
      });
    },

    mostarAlertaBono() {
      return this.$swal(
        "Tenga en cuenta!",
        "Esto afectará los cálculos de nómina del periodo actual",
        "warning"
      );
    },

    /**
     * se filtran los bonos segun el tipo, segun tabla conable_ingresos
     * @return bonos filtrados bonosDisponibles
     */

    async filtrarTipos() {
      this.bonosDisponibles = await this.dataTiposBono.filter((item) => {
        return item.tipo == this.tipo.llave;
      });
    },

    /**
     * Se obtienen los tipos de la tabla contable ingreso
     * @return tipos bonosDisponibles
     */
    getTiposBono() {
      axios
        .get(`/api/${localStorage.getItem("tenant")}/contable-ingresos`)
        .then((datos) => {
          this.dataTiposBono = datos.data;
        });
    },
  },
};
</script>
