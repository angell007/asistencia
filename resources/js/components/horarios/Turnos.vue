<template>
  <select
    class="form-control custom-control select-turno"
    v-model="turno"
    :style="{backgroundColor: withColor, color: '#FFF'}"
    @change="changeColor"
  >
    <option value="seleccione">Seleccione</option>
    <option value="0" :style="{backgroundColor: '#000', color: '#FFF'}">Descanso</option>
    <option
      v-for="turno in turnos"
      :key="turno.id"
      :value="turno.id"
      :style="{backgroundColor: turno.color, color: '#FFF'}"
    >{{turno.nombre}} ({{ turno.hora_inicio_uno}} {{turno.hora_fin_uno}})</option>
  </select>
</template>

<script>
export default {
  props: {
    turnos: Array,
  },

  data() { 
    return {
      turno: 'seleccione',
      active: false,
      withColor: '#9da4ad',
    }
  },
  methods: {
    changeColor() {
      if (this.turno === 'seleccione') {
        this.withColor = '#9da4ad'
        return
      }
      if (this.turno === '0') {
        this.withColor = '#000'
        return
      }

      this.withColor = this.turnos.find(turno => {
        return turno.id === this.turno
      }).color
    },
  },
}
</script>
<style scoped>
  .fecha-td .select-turno{
    height: 37px !important;
    border: 0 !important;
    border-radius: 0 !important;
  }
</style>

