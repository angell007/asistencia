<template>
  <div class="card mb-4 card-festivo">
    <div v-if="Festivos.length" class="card-title pl-3 pt-3 mb-1 font-weight-bold text-white">
      <span>Próximos Festivos</span>
      <img src="img/festives-icon.svg" class="svg-icon" alt="Festivo-icon">
    </div>
    <div class="card-body pl-3 pr-3 pb-3 pt-0" >
        <table class="table tabla-festivo">
            <tr v-for="(festivo,index) in Festivos" :key="index">
                <td style="width:45px;"><p class="font-weight-bold mb-0 fecha-festivo">{{festivo.fecha }}</p></td>
                <td class="text-white">{{festivo.nombre}}</td>
            </tr>
        </table>      
    </div>
  </div>
</template>

<script>
export default {
  props: {
    Festivos: Array,
  },
}
</script>

<style scoped>
.card-festivo{
  background: #a0cccd;
}
.fecha-festivo{
    background: #ffffff;
    border-radius: 10px;
    text-align: center;
    color: #275c9c !important;
    line-height: 15px;
    padding: 8px 0;
}
.tabla-festivo tr:first-child td:first-child{
  border:0 !important;
}
.tabla-festivo tr:first-child td {
  border:0 !important;
}
.tabla-festivo td, .tabla-festivo th{
  padding: 5px;
  vertical-align: middle;
}
.svg-icon {
  width: 38px;
  display: inline-block;
  padding-bottom: 10px;
  padding-left: 4px;
}
span.custom-label,
p.text-primary {
  font-family: 'Lato', sans-serif;
}
</style>
