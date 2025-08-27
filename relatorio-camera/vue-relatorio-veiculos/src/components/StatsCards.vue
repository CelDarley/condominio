<template>
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card stats-card h-100">
        <div class="card-body text-center">
          <i class="bi bi-car-front-fill fs-1 mb-2"></i>
          <div class="stats-number">{{ totalVehicles }}</div>
          <div>Total de Veículos</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card stats-card h-100">
        <div class="card-body text-center">
          <i class="bi bi-camera-fill fs-1 mb-2"></i>
          <div class="stats-number">1</div>
          <div>Câmeras Ativas</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card stats-card h-100">
        <div class="card-body text-center">
          <i class="bi bi-calendar-check fs-1 mb-2"></i>
          <div class="stats-number">2</div>
          <div>Dias Monitorados</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card stats-card h-100">
        <div class="card-body text-center">
          <i class="bi bi-speedometer2 fs-1 mb-2"></i>
          <div class="stats-number">{{ avgVehiclesPerHour }}</div>
          <div>Veículos/h</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'

const store = useVehiclesStore()

const totalVehicles = computed(() => store.filteredTotal)
const avgVehiclesPerHour = computed(() => {
  if (store.filteredTotal === 0) return '-'
  
  const vehicles = store.filteredVehicles
  if (vehicles.length < 2) return vehicles.length
  
  const firstTime = new Date(vehicles[0]["Hora Detecção"])
  const lastTime = new Date(vehicles[vehicles.length - 1]["Hora Detecção"])
  const hoursDiff = (lastTime.getTime() - firstTime.getTime()) / (1000 * 60 * 60)
  
  if (hoursDiff <= 0) return vehicles.length
  
  return Math.round(vehicles.length / hoursDiff)
})
</script>
