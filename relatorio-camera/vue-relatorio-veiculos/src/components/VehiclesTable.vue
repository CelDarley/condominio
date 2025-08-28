<template>
  <div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">
          <i class="bi bi-table me-2"></i>
          Registros de Detecção
        </h5>
        <small class="text-muted">
          {{ filteredTotal }} de {{ totalVehicles }} registros
        </small>
      </div>
      <div>
        <button class="btn btn-success btn-sm me-2" @click="handleExport">
          <i class="bi bi-download me-1"></i>
          Exportar
        </button>
        <button class="btn btn-info btn-sm" @click="handlePrint">
          <i class="bi bi-printer me-1"></i>
          Imprimir
        </button>
      </div>
    </div>
    <div class="card-body">
      <div v-if="loading" class="loading">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2">Carregando dados...</p>
      </div>

      <div v-else-if="filteredVehicles.length === 0" class="text-center py-4">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="mt-2 text-muted">Nenhum veículo encontrado com os filtros aplicados</p>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tipo</th>
              <th>Data</th>
              <th>Hora</th>
              <th>Placa</th>
              <th>Confiança</th>
              <th>Cor</th>
              <th>Bounding Box</th>
              <th>Frame</th>
              <th>Sentido</th>
              <th>Endereço</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="vehicle in paginatedVehicles" :key="vehicle.ID">
              <td><span class="badge bg-secondary">{{ vehicle.ID }}</span></td>
              <td>
                <span :class="`badge ${getVehicleTypeBadge(vehicle.Tipo).class} badge-vehicle-type`">
                  {{ getVehicleTypeBadge(vehicle.Tipo).text }}
                </span>
              </td>
              <td>{{ formatarData(vehicle["Hora Detecção"].split(' ')[0]) }}</td>
              <td>{{ vehicle["Hora Detecção"].split(' ')[1] }}</td>
              <td>
                <span v-if="vehicle.Placa === 'N/D'" class="text-muted">N/D</span>
                <span v-else class="badge bg-success">{{ vehicle.Placa }}</span>
              </td>
              <td>
                <span v-if="vehicle['Confiança Placa'] === 'N/D'" class="text-muted">N/D</span>
                <span v-else :class="getConfidenceClass(vehicle['Confiança Placa'])">
                  {{ vehicle["Confiança Placa"] }}
                </span>
              </td>
              <td>
                <span :class="`badge ${getColorBadge(vehicle.Cor).class}`">
                  {{ getColorBadge(vehicle.Cor).text }}
                </span>
              </td>
              <td><small class="text-muted">{{ vehicle["Bounding Box"] }}</small></td>
              <td><small class="text-muted">{{ vehicle.Frame }}</small></td>
              <td>
                <i v-if="getDirectionIcon(vehicle.Sentido) !== 'text-muted'" 
                   :class="`bi ${getDirectionIcon(vehicle.Sentido)}`"></i>
                <span v-else class="text-muted">N/D</span>
                {{ vehicle.Sentido }}
              </td>
              <td>Rua Gurupi, 28</td>
            </tr>
          </tbody>
        </table>

        <!-- Paginação -->
        <nav v-if="totalPages > 1" aria-label="Paginação da tabela">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button class="page-link" @click="currentPage = 1" :disabled="currentPage === 1">
                <i class="bi bi-chevron-double-left"></i>
              </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">
                <i class="bi bi-chevron-left"></i>
              </button>
            </li>

            <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === currentPage }">
              <button class="page-link" @click="currentPage = page">{{ page }}</button>
            </li>

            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <button class="page-link" @click="currentPage++" :disabled="currentPage === totalPages">
                <i class="bi bi-chevron-right"></i>
              </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <button class="page-link" @click="currentPage = totalPages" :disabled="currentPage === totalPages">
                <i class="bi bi-chevron-double-right"></i>
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'

const store = useVehiclesStore()

const currentPage = ref(1)
const itemsPerPage = 25

const loading = computed(() => store.loading)
const filteredVehicles = computed(() => store.filteredVehicles)
const totalVehicles = computed(() => store.totalVehicles)
const filteredTotal = computed(() => store.filteredTotal)

const totalPages = computed(() => Math.ceil(filteredTotal.value / itemsPerPage))
const paginatedVehicles = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredVehicles.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

// Resetar página quando filtros mudarem
watch(filteredTotal, () => {
  currentPage.value = 1
})

const getVehicleTypeBadge = (type: string) => {
  const typeMap: Record<string, string> = {
    'Carro': 'Carro',
    'Moto': 'Moto',
    'Caminhão/Caminhonete': 'Caminhão'
  }
  const badgeClass = type === 'Carro' ? 'bg-primary' : type === 'Moto' ? 'bg-info' : 'bg-warning'
  return { text: typeMap[type] || type, class: badgeClass }
}

const getConfidenceClass = (confidence: string) => {
  if (confidence === 'N/D') return ''
  const conf = parseFloat(confidence)
  if (conf >= 0.8) return 'badge confidence-high'
  if (conf >= 0.6) return 'badge confidence-medium'
  return 'badge confidence-low'
}

const getDirectionIcon = (direction: string) => {
  const iconMap: Record<string, string> = {
    'subindo': 'bi-arrow-up direction-up',
    'descendo': 'bi-arrow-down direction-down',
    'parado': 'bi-pause-circle direction-stopped'
  }
  return iconMap[direction] || 'text-muted'
}

const getColorBadge = (color: string) => {
  const colorMap: Record<string, string> = {
    'preto': 'bg-dark',
    'branco': 'bg-light text-dark',
    'cinza': 'bg-secondary',
    'vermelho': 'bg-danger',
    'azul': 'bg-primary'
  }
  return { text: color, class: colorMap[color] || 'bg-secondary' }
}

const formatarData = (data: string) => {
  const [ano, mes, dia] = data.split('-')
  return `${dia}-${mes}-${ano}`
}

const handleExport = () => {
  store.exportToCSV()
}

const handlePrint = () => {
  window.print()
}

onMounted(() => {
  store.loadVehicles()
})
</script>

<style scoped>
.pagination .page-link {
  color: var(--primary-color);
}

.pagination .page-item.active .page-link {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
}

.pagination .page-item.disabled .page-link {
  color: var(--secondary-color);
}
</style>
