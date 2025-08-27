<script setup lang="ts">
import StatsCards from '@/components/StatsCards.vue'
import SearchForm from '@/components/SearchForm.vue'
import VehiclesTable from '@/components/VehiclesTable.vue'
</script>

<template>
  <div class="container mt-4">
    <!-- Título da Página -->
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="display-5 fw-bold text-primary">
          <i class="bi bi-clipboard-data me-3"></i>
          Relatório de Veículos
        </h1>
        <p class="lead text-muted">Sistema de monitoramento e análise de tráfego urbano</p>
      </div>
    </div>

    <!-- Cards de Estatísticas -->
    <StatsCards />

    <!-- Formulário de Pesquisa -->
    <SearchForm />

    <!-- Tabela de Dados -->
    <VehiclesTable />
  </div>
</template>
