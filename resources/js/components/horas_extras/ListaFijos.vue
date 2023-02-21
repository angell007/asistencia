<template>
  <div>
    <div v-if="funcionarioRender != null">
      <div
        v-for="(diario, indexD) in funcionarioRender.daysWork"
        :key="indexD"
        class="row pl-3"
      >
        <fijo
          :ref="'fijo' + funcionario"
          :funcionario="funcionario"
          :diario="diario[0] != undefined ? diario[0].day : {}"
          :info="funcionarioRender"
          :day="diario"
        ></fijo>
      </div>
    </div>
  </div>
</template>


<script>
import ColillaPagoVue from "../nomina/pago_nomina/calculos/colilla_pago/ColillaPago.vue";
import Fijo from "./Fijo";

export default {
  props: { funcionario: Object, primerDia: String, ultimodiaDia: String },
  components: { Fijo },
  asyncComputed: {
    async funcionarioRender() {
      const data = await axios.post(
        `/api/${localStorage.getItem("tenant")}/funcionario/getInfoTotal`,
        {
          id: this.funcionario.id,
          tipo: this.funcionario.tipo_turno,
          pd: this.primerDia,
          ud: this.ultimodiaDia,
        }
      );
      return data.data;
    },
  },
};
</script>