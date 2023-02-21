<template      
>
  <div
    v-if="(deduccionesDatos.valor_total > 0 &&  brand  && datosEmpresa.configuracion_pago.pago_deducciones == 'final_periodo') || (deduccionesDatos.valor_total > 0 &&  datosEmpresa.configuracion_pago.pago_deducciones == 'proporcionales' )"
  >
    <p class="text-center text-uppercase mt-2">Deducciones</p>

    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th style="width:89%">Concepto</th>
          <th class="text-center">Valor</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(deduccion, index) in deducciones" :key="`H-${index}`">
          <td>{{deduccion.concepto}}</td>
          <td class="text-center">{{deduccion.valor | moneda}}</td>
        </tr>
        <tr class="font-weight-bold">
          <td>Total deducciones</td>
          <td class="text-center">{{deduccionesDatos.valor_total | moneda}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    deduccionesDatos: Object,
    datosEmpresa: Object,
    brand: Boolean
  },
  data() {
    return {
      deducciones: []
    };
  },
  created() {
    this.organizarDeducciones();
  },
  methods: {
    organizarDeducciones() {
      if (this.deduccionesDatos.deducciones !== null) {
        for (let prop in this.deduccionesDatos.deducciones) {
          this.deducciones.push({
            concepto: prop,
            valor: this.deduccionesDatos.deducciones[prop]
          });
        }
      }
    }
  }
};
</script>

<style scoped>
table thead,
tbody {
  font-size: 0.95rem;
  font-family: "Lato", sans-serif;
  line-height: 1.9;
}
p i {
  font-size: 28px;
}
</style>
