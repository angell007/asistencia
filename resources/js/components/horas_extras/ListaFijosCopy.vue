<template>
  <div>
    <div v-if="funcionarioRender != null">
      <div v-for="(day, indexD) in days" :key="indexD" class="row pl-3">
        <fijo
          :ref="'fijo' + funcionario"
          :info="funcionarioRender"
          :funcionario="funcionario"
          :day="day"
        ></fijo>
      </div>
    </div>
    <!-- <div v-if="funcionarioRender != null">
      <div
        v-for="(diario, indexD) in funcionarioRender.tipo_turno == turnoDefault
          ? funcionarioRender.diarios_turno_rotativo
          : funcionarioRender.diarios_turno_fijo"
        :key="indexD"
        class="row pl-3"
      >
        <fijo
          :ref="'fijo' + funcionario"
          :funcionario="funcionario"
          :diario="diario"
          :info = funcionarioRender
        ></fijo>
      </div>
    </div> -->
  </div>
</template>


<script>
import ColillaPagoVue from "../nomina/pago_nomina/calculos/colilla_pago/ColillaPago.vue";
import Fijo from "./Fijo";

export default {
  props: { funcionario: Object },
  components: { Fijo },

  data() {
    return {
      turnoDefault: "Rotativo",
      days: [
        "Lunes",
        "Martes",
        "Miercoles",
        "Jueves",
        "Viernes",
        "Sabado",
        "domingo",
      ],
    };
  },

  asyncComputed: {
    async funcionarioRender() {
      const data = await axios.post(
        `/api/${localStorage.getItem("tenant")}/funcionario/getInfoTotal`,
        { id: this.funcionario.id, tipo: this.funcionario.tipo_turno }
      );
      console.log(data.data);
      return data.data;
    },
  },
};
</script>