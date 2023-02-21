<template>
  <div class="card">
    <div
      v-if="funcionarios.length"
      class="card-title pl-3 pt-3 mb-1 font-weight-bold"
    >
      <span>Contratos con pre aviso enviado, pero no renovados</span>
    </div>
    <div class="card-body pl-3 pr-3 pt-0 pb-3">
      <p class="text-center mt-4" v-if="!funcionarios.length">
        <img
          src="/img/vencimientos.png"
          class="img-fluid"
          alt="sin contratos"
        />
      </p>

      <table v-else class="table tabla-contratos">
        <tr v-for="(funcionario, index) in funcionarios" :key="index">
          <td style="width: 52px">
            <img
              v-if="funcionario.image != null"
              :src="`${funcionario.image}`"
              class="img-funcionario"
              alt=""
            />
            <img
              v-else
              :src="`/img/robot.jpg`"
              class="img-funcionario"
              alt=""
            />
          </td>
          <td>
            <p class="font-weight-bold mb-0">
              {{ funcionario.nombres.split(" ")[0] }}<br />{{
                funcionario.apellidos.split(" ")[0]
              }}
            </p>
            <small class="mb-1"
              >(En {{ funcionario.contratos[0].dias_restantes }} días)</small
            >
          </td>

          <td style="width: 52px">
            <a
              class="header-icon btn btn-empty btn-xs btn-send"
              type="button"
              v-tooltip.top="'Renovar'"
              @click.prevent="RenovarContrato(funcionario.id)"
            >
              <i class="iconsmind-Repeat-2" style="font-size: 2em"></i>
              <span class="counter">{{ funcionario.preavisos_count }}</span>
            </a>
          </td>

          <td style="width: 52px">
            <a
              style="cursor: auto"
              class="header-icon btn btn-empty btn-xs btn-send"
              type="button"
            >
              <i class="iconsmind-Paper-Plane" style="font-size: 2em"></i>
              <span class="counter">{{ funcionario.preavisos_count }}</span>
            </a>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Axios from "axios";
export default {
  props: { funcionarios: Array },

  created() {
    console.log(this.funcionarios);
  },

  methods: {
    RenovarContrato(funcionario) {
      this.$swal
        .fire({
          title: "¿Está seguro?",
          text:
            "Se dispone a renovar el contrato al funcionario por un período igual",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, renovar!",
        })
        .then(async (result) => {
          if (result.value) {
            const req = await Axios.post(
              `api/${localStorage.getItem("tenant")}/renovar-contrato`,
              { id: funcionario }
            );
            const data = req.data;
            this.$swal.fire(
              "Correcto!",
              "Preaviso Enviado de Manera Exitosa",
              "success"
            );
          }

          console.log("No estamos renovando contrato");

          this.$swal.fire(
            "Correcto!",
            "Contrato Renovado de Manera Exitosa",
            "success"
          );
        });
    },
  },
};
</script>

<style scoped>
.svg-icon {
  width: 38px;
  display: inline-block;
  padding-bottom: 10px;
  padding-left: 4px;
}
.btn-preaviso {
  font-size: 22px;
  color: #275c9c;
}
.tabla-contratos td,
.tabla-contratos th {
  padding: 5px;
  vertical-align: middle;
}
.tarjeta-contrato {
  border-bottom: 1px dotted #ccc;
}
.img-funcionario {
  width: 42px;
  border-radius: 50%;
  border: 1px solid #ccc;
}
.btn-send {
  position: relative;
}
.counter {
  font-size: 9px;
  color: #ffffff;
  background: red;
  border-radius: 10px;
  position: absolute;
  width: 18px;
  height: 15px;
  text-align: center;
  font-weight: 700;
  top: -3px;
  right: -3px;
  line-height: 14px;
}
</style>
