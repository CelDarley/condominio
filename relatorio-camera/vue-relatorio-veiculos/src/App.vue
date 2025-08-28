<script setup lang="ts">
import { ref, onMounted } from 'vue'

const lastUpdate = ref('-')

const updateTimestamp = () => {
  const now = new Date()
  lastUpdate.value = now.toLocaleString('pt-BR')
}

onMounted(() => {
  updateTimestamp()
  setInterval(updateTimestamp, 30000)
})
</script>

<template>
  <div id="app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <router-link class="navbar-brand" to="/">
          <i class="bi bi-camera-video-fill me-2"></i>
          Sistema de Câmeras
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/">
                <i class="bi bi-house me-1"></i>Início
              </router-link>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="bi bi-gear me-1"></i>Configurações
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Conteúdo principal -->
    <main>
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <p class="mb-0 text-muted">
              <i class="bi bi-shield-check me-2"></i>
              Sistema de Monitoramento de Tráfego Urbano
            </p>
          </div>
          <div class="col-md-6 text-md-end">
            <p class="mb-0 text-muted">
              <i class="bi bi-clock me-2"></i>
              Última atualização: {{ lastUpdate }}
            </p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

.navbar-brand {
  text-decoration: none;
}

.nav-link {
  text-decoration: none;
}

.nav-link.router-link-active {
  font-weight: 600;
}
</style>
