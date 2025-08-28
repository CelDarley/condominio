<template>
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h5 class="card-title mb-0">
        <i class="bi bi-search me-2"></i>
        Filtros de Pesquisa
      </h5>
    </div>
    <div class="card-body">
      <form @submit.prevent="handleSubmit">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="data" class="form-label">Data</label>
            <input 
              type="date" 
              class="form-control" 
              id="data" 
              v-model="localFilters.data"
            >
          </div>
          <div class="col-md-3 mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input 
              type="time" 
              class="form-control" 
              id="hora" 
              v-model="localFilters.hora"
            >
          </div>
          <div class="col-md-3 mb-3">
            <label for="placa" class="form-label">Placa</label>
            <input 
              type="text" 
              class="form-control" 
              id="placa" 
              v-model="localFilters.placa"
              placeholder="Digite a placa"
            >
          </div>
          <div class="col-md-3 mb-3">
            <label for="sentido" class="form-label">Sentido</label>
            <select 
              class="form-select" 
              id="sentido" 
              v-model="localFilters.sentido"
            >
              <option value="">Todos</option>
              <option value="subindo">Subindo</option>
              <option value="descendo">Descendo</option>
              <option value="parado">Parado</option>
              <option value="N/D">Não Definido</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                id="showPlacasDetectadas" 
                v-model="localFilters.showPlacasDetectadas"
                @change="handlePlacaFilterChange"
              >
              <label class="form-check-label" for="showPlacasDetectadas">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Mostrar placas detectadas
              </label>
              <small class="form-text text-muted d-block">
                <span class="badge bg-success">{{ placasDetectadas }}</span> placas detectadas
              </small>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                id="showPlacasNaoDetectadas" 
                v-model="localFilters.showPlacasNaoDetectadas"
                @change="handlePlacaFilterChange"
              >
              <label class="form-check-label" for="showPlacasNaoDetectadas">
                <i class="bi bi-x-circle-fill text-warning me-2"></i>
                Mostrar placas não detectadas
              </label>
              <small class="form-text text-muted d-block">
                <span class="badge bg-warning">{{ placasNaoDetectadas }}</span> placas não detectadas
              </small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-search me-2"></i>
              Pesquisar
            </button>
            <button type="button" class="btn btn-outline-secondary ms-2" @click="handleReset">
              <i class="bi bi-arrow-clockwise me-2"></i>
              Limpar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'

const store = useVehiclesStore()

const localFilters = ref({
  data: '',
  hora: '',
  placa: '',
  sentido: '',
  showPlacasDetectadas: true,
  showPlacasNaoDetectadas: false
})

const placasDetectadas = computed(() => store.placasDetectadas)
const placasNaoDetectadas = computed(() => store.placasNaoDetectadas)

// Sincronizar filtros locais com o store
watch(() => store.filters, (newFilters) => {
  localFilters.value = { ...newFilters }
}, { deep: true })

const handlePlacaFilterChange = () => {
  // Verificar se pelo menos um checkbox está marcado
  if (!localFilters.value.showPlacasDetectadas && !localFilters.value.showPlacasNaoDetectadas) {
    // Se nenhum estiver marcado, marcar o outro
    if (localFilters.value.showPlacasDetectadas === false) {
      localFilters.value.showPlacasNaoDetectadas = true
    } else {
      localFilters.value.showPlacasDetectadas = true
    }
  }
  
  // Aplicar filtros automaticamente
  store.updateFilters(localFilters.value)
}

const handleSubmit = () => {
  store.updateFilters(localFilters.value)
}

const handleReset = () => {
  store.resetFilters()
  localFilters.value = {
    data: '',
    hora: '',
    placa: '',
    sentido: '',
    showPlacasDetectadas: true,
    showPlacasNaoDetectadas: false
  }
}
</script>
