<template>
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h1 class="font-weight-bold">Pago de Nómina</h1>
      </div>
      <div class="col-md-4 offset-4">
        <span class="font-weight-bold">Periodo de pago</span>:
        <i class="simple-icon-calendar"></i>
        <span class="font-weight-bold">{{ inicioPeriodo }}</span> -
        <i class="simple-icon-calendar"></i>
        <span class="font-weight-bold">{{ finPeriodo }}</span>
        <!--<div class="rollback-payment text-right">
          <a class="btn btn-link btn-sm" href="#" @click.prevent>Pagar el periodo anterior</a>
        </div>-->
      </div>
    </div>

    <div class="alert alert-secondary" v-if="nomina.nomina_paga">
      <p class="font-weight-bold text-center">
        Este periodo ya ha sido pagado, sin embargo, puede editar y volver a
        guardar dando click en la siguiente opción:
      </p>
      <a
        href="#"
        id="editPayroll"
        class="d-block text-center"
        @click.prevent="deletePagoNomina"
      >Volver a editar el pago</a>
    </div>

    <div class="row icon-cards">
      <div class="card-body">
        <div class="row">
          <card-concepto :concepto="nomina.salarios" icon="iconsmind-Money-2">Salarios (neto)</card-concepto>
          <card-concepto :concepto="nomina.provisiones" icon="iconsmind-Bag-Coins">Provisiones</card-concepto>
          <card-concepto :concepto="nomina.seguridad_social" icon="iconsmind-Ambulance">Seg. Social</card-concepto>
          <card-concepto :concepto="nomina.parafiscales" icon="iconsmind-Mens">Parafiscales</card-concepto>
          <card-concepto :concepto="nomina.extras" icon="iconsmind-Clock-Forward">Horas Extras</card-concepto>
          <card-concepto :concepto="nomina.ingresos" icon="iconsmind-Coins-3">Ingresos Adicionales</card-concepto>
        </div>
      </div>
    </div>

    <div class="row d-flex justify-content-center">
      <p>
        Costo total empresa:
        <span class="custom-label">
          {{
          nomina.costo_total_empresa | moneda
          }}
        </span>
      </p>
    </div>

    <div class="alert alert-light" v-if="nomina.nomina_paga">
      <div class="row">
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-File-Excel icono-descargables"></i> Resumen de
            Nómina
          </a>
        </div>
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Bell icono-descargables"></i> Resumen de
            Novedades
          </a>
        </div>
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Download-2 icono-descargables"></i> Descargar
            Colillas de Pago
          </a>
        </div>
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Mail icono-descargables"></i> Notificar via
            email y SMS
          </a>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Umbrella-2 icono-descargables"></i> Archivo
            Seguridad Social
          </a>
        </div>
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Bank icono-descargables"></i> Archivo Pago
            Bancos
          </a>
        </div>
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Suitcase icono-descargables"></i> Archivo
            Contable
          </a>
        </div>
        <div class="col-3">
          <a href="#" class="text-center font-weight-bold nomina-descargables" @click.prevent>
            <i class="iconsmind-Clock icono-descargables"></i> Archivo de
            Horarios
          </a>
        </div>
      </div>
    </div>

    <div class="row" v-if="renderizar">
      <modal id="extrasRecargos" ref="extrasRecargos" :esLg="true">
        <template slot="titulo">Horas extras/recargos</template>
        <template slot="contenido">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th class="custom-label text-center">Tipo</th>
                <th class="custom-label text-center" style="width: 120px">Cantidad</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(extra, index) in horasExtras" :key="index">
                <td class="custom-label text-left">{{ NombresExtras[index] }}</td>
                <td class="custom-label text-center">{{ extra }} h</td>
              </tr>
            </tbody>
          </table>
        </template>
      </modal>

      <modal id="novedades" ref="novedades">
        <template slot="titulo">Novedades</template>
        <template slot="contenido">
          <table class="table table-bordered table-sm" v-if="novedades.length">
            <thead>
              <tr>
                <th class="custom-label">Concepto</th>
                <th class="custom-label text-center">Días</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(novedad, index) in novedades" :key="index">
                <td class="custom-label">{{ novedad.concepto }}</td>
                <td class="custom-label text-center">{{ novedad.dias }}</td>
              </tr>
            </tbody>
          </table>
          <p class="custom-label" v-else>El funcionario no presenta novedades en este periodo</p>
        </template>
      </modal>

      <modal id="IngresosP" ref="IngresosP">
        <template slot="titulo">Ingresos Prestacionales</template>
        <template slot="contenido">
          <ingreso-prestacional
            :funcionarioProp="FuncionarioSeleccionado"
            :nominaPaga="nomina.nomina_paga"
            @actualizar="getPagoNomina()"
            ref="ingresoPrestacional"
          ></ingreso-prestacional>
        </template>
      </modal>

      <modal id="IngresosNP" ref="IngresosNP">
        <template slot="titulo">Ingresos No Prestacionales</template>
        <template slot="contenido">
          <ingreso-no-prestacional
            :funcionarioProp="FuncionarioSeleccionado"
            :nominaPaga="nomina.nomina_paga"
            @actualizar="getPagoNomina()"
            ref="ingresoNoPrestacional"
          ></ingreso-no-prestacional>
        </template>
      </modal>

      <modal id="Deducciones" ref="Deducciones">
        <template slot="titulo">Deducciones</template>
        <template slot="contenido">
          <deduccion
            :funcionarioProp="FuncionarioSeleccionado"
            :nominaPaga="nomina.nomina_paga"
            ref="deduccion"
            @actualizar="getPagoNomina()"
          ></deduccion>
        </template>
      </modal>

      <div class="col-12">
        <h2 class="font-weight-bold">Lista de Funcionarios</h2>
        <!-- <div class="form-group row">
          <label
            for="centro_costos"
            class="custom-label col-form-label col-md-2 offset-3"
          >Filtro centro de costos</label>
          <select name="centro_costos" class="form-control custom-control col-md-4">
            <option value="seleccione">Seleccione</option>
          </select>
        </div>-->
        <div class="card">
          <div class="card-body">
            <table class="table table-striped tabla-funcionarios">
              <thead>
                <tr class="text-center">
                  <th colspan="2">Funcionario</th>
                  <th>Ingresos Prestacionales</th>
                  <th>Ingresos No Prestacionales</th>
                  <th>Deducciones</th>
                  <th>Pago a Empleado</th>
                  <th style="width: 60px">Acciones</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(funcionario, index) in funcionarios" :key="index" class="text-center">
                  <td style="padding: 3px; width: 48px">
                    <img
                      v-if="funcionario.original.image != null"
                      :src="`${funcionario.original.image}`"
                      class="img-funcionario"
                      alt
                    />
                    <img v-else :src="`/img/robot.jpg`" class="img-funcionario" alt />
                  </td>
                  <td style="width: 200px" class="text-left">
                    {{ funcionario.original.nombres.split(" ")[0] }}
                    {{ funcionario.original.apellidos.split(" ")[0] }}
                  </td>
                  <td>
                    {{
                    funcionario.original.valor_ingresos_salariales | moneda
                    }}
                  </td>
                  <td>
                    {{
                    funcionario.original.valor_ingresos_no_salariales | moneda
                    }}
                  </td>
                  <td>{{ funcionario.original.valor_deducciones | moneda }}</td>
                  <td>
                    <p class="custom-label">
                      {{ funcionario.original.salario_neto | moneda }} /
                      {{ nomina.frecuencia_pago }}
                    </p>
                  </td>
                  <td style="padding: 5px">
                    <div class="dropdown">
                      <a
                        class="btn btn-primary btn-sm dropdown-toggle mb-1"
                        href="#"
                        role="button"
                        id="dropdownMenuLink"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                      >Acciones</a>
                      <div
                        class="dropdown-menu"
                        aria-labelledby="dropdownMenuLink"
                        x-placement="bottom-start"
                      >
                        <a
                          href="#"
                          class="dropdown-item"
                          @click.prevent="
                            mostrarExtrasRecargos(funcionario.original)
                          "
                        >Extras y Recargos</a>
                        <a
                          href="#"
                          class="dropdown-item"
                          @click.prevent="
                            mostrarNovedades(funcionario.original)
                          "
                        >Vacac, Incap y Lic</a>
                        <a
                          href="#"
                          class="dropdown-item"
                          @click.prevent="
                            mostrarIngresosP(funcionario.original)
                          "
                        >Ingresos prestacionales</a>
                        <a
                          href="#"
                          class="dropdown-item"
                          @click.prevent="
                            mostrarIngresosNP(funcionario.original)
                          "
                        >Ingresos no prestacionales</a>
                        <a
                          href="#"
                          class="dropdown-item"
                          @click.prevent="
                            mostrarDeducciones(funcionario.original)
                          "
                        >Deducciones</a>
                        <div class="dropdown-divider"></div>
                        <router-link
                          :to="{
                            path:
                              '/nomina/pago/' + funcionario.original.identidad,
                            query: {
                              inicio: nomina.inicio_periodo,
                              fin: nomina.fin_periodo,
                            },
                          }"
                          class="dropdown-item"
                        >Ver Cálculos</router-link>
                        <a
                          v-if="nomina.nomina_paga"
                          href="#"
                          class="dropdown-item"
                          @click.prevent="getColilla(funcionario.original)"
                        >Descargar Colilla</a>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <!-- Modal Extras y recargos-->
            <div class="text-right" v-if="!nomina.nomina_paga">
              <button
                type="button"
                class="btn btn-secondary raised text-right"
                @click="postPagoNomina"
              >Guardar y Proceder</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <overlay v-else></overlay>
  </div>
</template>

<script>
import IngresoPrestacional from "./formularios/IngresoPrestacional";
import IngresoNoPrestacional from "./formularios/IngresoNoPrestacional";
import Deduccion from "./formularios/Deduccion";
import Modal from "./../../utiles/Modal";
import Overlay from "../../utiles/Overlay";
import CardConcepto from "./reutilizables/CardConcepto";

export default {
  components: {
    IngresoPrestacional,
    IngresoNoPrestacional,
    Deduccion,
    Modal,
    Overlay,
    CardConcepto
  },
  data() {
    return {
      id: "",
      nomina: "",
      funcionarios: [],
      dataPartial: [],
      horasExtras: "",
      novedades: [],
      pago: {},
      inicioParemeter: "",
      finParemeter: "",
      uriPagoNomina: "",
      uriPagoFuncionarios: "",
      renderizar: false,
      colillaUri: "",
      FuncionarioSeleccionado: {},
      Mensaje: "",
      NombresExtras: {
        hed: "Horas Extras Diurnas",
        hen: "Horas Extras Nocturnas",
        hedfd: "Horas Extras Diurnas Festivos/Dominicales",
        hedfn: "Horas Extras Nocturnas Festivos/Dominicales",
        rn: "Recargos Nocturnos",
        rf: "Recargos Festivos"
      }
    };
  },

  created() {
    console.log("entrando created");
    //Si existen parametros en la ruta del componente al momento de crearlo significa que viene de hisorial de pagos por lo tanto se requiere ver un pago antiguo,
    if (Object.keys(this.$route.params).length) {
      console.log("parameter");

      this.inicioParemeter = this.$route.params.inicio;
      this.finParemeter = this.$route.params.fin;
    }

    this.setUris();
    this.getPagoNomina();
  },

  mounted() {
    console.log("entrando mounted");
  },

  methods: {
    //Se settean las uris para que tome esos parámetros y cargue la nómina de esa fecha. Si no existen carga la nómina actual
    setUris() {
      console.log([this.inicioParemeter, this.finParemeter]);

      this.uriPagoNomina =
        this.inicioParemeter && this.finParemeter
          ? `/api/${localStorage.getItem("tenant")}/nomina/pago/${
              this.inicioParemeter
            }/${this.finParemeter}`
          : `/api/${localStorage.getItem("tenant")}/nomina/pago`;

      this.uriPagoFuncionarios =
        this.inicioParemeter && this.finParemeter
          ? `/api/${localStorage.getItem("tenant")}/nomina/pago/funcionarios/${
              this.inicioParemeter
            }/${this.finParemeter}`
          : `/api/${localStorage.getItem("tenant")}/nomina/pago/funcionarios`;
    },

    async getPagoNomina() {
      const req = await axios.get(this.uriPagoNomina);
      this.dataPartial = req.data.funcionarios;
      this.nomina = req.data;
      this.pago.id = this.nomina.nomina_paga_id
        ? this.nomina.nomina_paga_id
        : "";

      this.getFuncionarios();
      this.getUsuario();
    },

    getFuncionarios() {
      this.funcionarios = this.dataPartial;
      this.renderizar = true;
    },

    getUsuario() {
      this.pago.admin_id = 1;
    },

    mostrarExtrasRecargos(funcionario) {
      this.horasExtras = funcionario.horas_extras;
      this.$refs.extrasRecargos.mostrarModal();
    },

    mostrarNovedades(funcionario) {
      this.novedades = [];
      if (funcionario.novedades) {
        for (let novedad in funcionario.novedades) {
          this.novedades.push({
            concepto: novedad,
            dias: funcionario.novedades[novedad]
          });
        }
      }
      this.$refs.novedades.mostrarModal();
    },
    mostrarIngresosP(funcionario) {
      this.FuncionarioSeleccionado = funcionario;

      this.$refs.ingresoPrestacional.borrarDatos();
      this.$refs.ingresoPrestacional.getIngresosPrestacionales();
      this.$refs.IngresosP.mostrarModal();
    },
    mostrarIngresosNP(funcionario) {
      this.FuncionarioSeleccionado = funcionario;

      this.$refs.ingresoNoPrestacional.borrarDatos();
      this.$refs.ingresoNoPrestacional.getIngresosNPrestacionales();
      this.$refs.IngresosNP.mostrarModal();
    },

    mostrarDeducciones(funcionario) {
      this.FuncionarioSeleccionado = funcionario;

      this.$refs.deduccion.borrarDatos();
      this.$refs.deduccion.getDeducciones();
      this.$refs.Deducciones.mostrarModal();
    },
    postPagoNomina() {
      this.pago.inicio_periodo = this.nomina.inicio_periodo;
      this.pago.fin_periodo = this.nomina.fin_periodo;
      this.pago.total_salarios = this.nomina.salarios;
      this.pago.total_retenciones = this.nomina.retenciones;
      this.pago.total_provisiones = this.nomina.provisiones;
      this.pago.total_seguridad_social = this.nomina.seguridad_social;
      this.pago.total_parafiscales = this.nomina.parafiscales;
      this.pago.total_extras_recargos = this.nomina.extras;
      this.pago.total_ingresos = this.nomina.ingresos;
      this.pago.costo_total = this.nomina.costo_total_empresa;

      this.$swal
        .fire({
          title: "¿Está seguro?",
          text:
            "Se dispone a generar una nómina, revise que todo coincida antes de continuar.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, continuar!"
        })
        .then(result => {
          if (result.value) {
            this.renderizar = false;
            axios
              .post(
                `/api/${localStorage.getItem("tenant")}/nomina/pago/nomina`,
                this.pago
              )
              .then(respuesta => {
                this.$swal.fire("Correcto!", respuesta.data.message, "success");
                this.$router
                  .push({
                    path: "/nomina/historial-pagos",
                    query: {
                      inicio: respuesta.data.Data.inicio_periodo,
                      fin: respuesta.data.Data.fin_periodo
                    }
                  })
                  .catch(err => {
                    console.log(err);
                  });

                // this.getPagoNomina();
                // this.getFuncionarios();
              });
          }
        });
    },

    deletePagoNomina() {
      axios
        .delete(
          `/api/${localStorage.getItem("tenant")}/nomina/pago/${
            this.pago.id
          }/eliminar`
        )
        .then(respuesta => {
          this.renderizar = false;
          this.getPagoNomina();
        })
        .catch(error => {
          if (error.response.status === 400) {
            localStorage.removeItem("token");
            localStorage.removeItem("tenant");
          } else {
            this.$swal.fire(
              "Error",
              "Han ocurrido errores, por favor intente más tarde",
              "error"
            );
          }
        });
    },

    getColilla(funcionario) {
      axios({
        method: "get",
        responseType: "arraybuffer",
        url: `/api/${localStorage.getItem(
          "tenant"
        )}/nomina/colilla/funcionarios/${funcionario.id}/${
          this.nomina.inicio_periodo
        }/${this.nomina.fin_periodo}`
      }).then(respuesta => {
        //Se realiza de esta manera para no exponer información en el link
        let blob = new Blob([respuesta.data], { type: "application/pdf" });
        let link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `${funcionario.nombres}_${funcionario.apellidos}.pdf`;
        link.click();
      });
    }
  },
  computed: {
    inicioPeriodo() {
      return moment(this.nomina.inicio_periodo).format("DD/MM/YYYY");
    },
    finPeriodo() {
      return moment(this.nomina.fin_periodo).format("DD/MM/YYYY");
    }
  }
};
</script>

<style scoped>
.tabla-funcionarios {
  border-left: 2px solid #eee;
  border-right: 2px solid #eee;
}

.img-funcionario {
  width: 38px;
  border-radius: 50%;
  border: 1px solid #ccc;
}
.icono-descargables {
  font-weight: 600;
  font-size: 1rem;
}

.alert-light {
  background-color: #e8e7e7 !important;
  border-color: transparent !important;
}
#editPayroll {
  color: #9370db;
  font-size: 14px;
  font-weight: bold;
  text-decoration: underline;
}
</style>
 