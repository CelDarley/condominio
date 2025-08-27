# 🚗 Relatório de Veículos - Sistema de Câmeras

Aplicação Vue 3 moderna para monitoramento e análise de tráfego urbano através de sistema de câmeras.

## ✨ Características

- **Interface Moderna**: Design responsivo com Bootstrap 5
- **Filtros Avançados**: Por data, hora, placa, sentido e status de detecção
- **Estatísticas em Tempo Real**: Contadores dinâmicos de veículos
- **Paginação Inteligente**: Navegação eficiente pelos dados
- **Exportação CSV**: Download dos dados filtrados
- **Impressão**: Relatórios para impressão
- **Estado Reativo**: Gerenciamento de estado com Pinia
- **TypeScript**: Código tipado e seguro

## 🚀 Tecnologias

- **Vue 3** - Framework JavaScript progressivo
- **TypeScript** - Superset JavaScript tipado
- **Pinia** - Gerenciamento de estado
- **Vue Router** - Roteamento da aplicação
- **Bootstrap 5** - Framework CSS responsivo
- **Bootstrap Icons** - Ícones vetoriais
- **Vite** - Build tool rápido

## 📋 Pré-requisitos

- Node.js 18+ 
- npm ou yarn

## 🛠️ Instalação

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
cd vue-relatorio-veiculos
```

2. **Instale as dependências**
```bash
npm install
```

3. **Execute em modo desenvolvimento**
```bash
npm run dev
```

4. **Acesse a aplicação**
```
http://localhost:5173
```

## 📁 Estrutura do Projeto

```
src/
├── assets/          # Estilos e recursos estáticos
├── components/      # Componentes Vue reutilizáveis
│   ├── StatsCards.vue      # Cards de estatísticas
│   ├── SearchForm.vue      # Formulário de filtros
│   └── VehiclesTable.vue   # Tabela de veículos
├── stores/          # Stores Pinia
│   └── vehicles.ts  # Store de veículos
├── views/           # Páginas da aplicação
│   └── HomeView.vue # Página principal
├── router/          # Configuração de rotas
├── App.vue          # Componente raiz
└── main.ts          # Ponto de entrada
```

## 🔧 Scripts Disponíveis

- **`npm run dev`** - Servidor de desenvolvimento
- **`npm run build`** - Build para produção
- **`npm run preview`** - Preview do build
- **`npm run format`** - Formatação do código
- **`npm run lint`** - Verificação de linting

## 📊 Funcionalidades

### 🎯 Filtros de Pesquisa
- **Data**: Filtro por data específica
- **Hora**: Filtro por horário
- **Placa**: Busca por placa do veículo
- **Sentido**: Filtro por direção do movimento
- **Status da Placa**: Mostrar/ocultar placas detectadas/não detectadas

### 📈 Estatísticas
- Total de veículos
- Câmeras ativas
- Dias monitorados
- Média de veículos por hora
- Contadores de placas detectadas/não detectadas

### 📋 Tabela de Dados
- Paginação automática (25 itens por página)
- Ordenação por colunas
- Badges coloridos para tipos de veículo
- Indicadores de confiança
- Ícones direcionais para sentido do movimento

### 💾 Exportação
- Download em formato CSV
- Respeita filtros aplicados
- Formato de data brasileiro (DD-MM-AAAA)

## 🎨 Componentes

### StatsCards
Exibe estatísticas em cards visuais com gradientes e ícones.

### SearchForm
Formulário de filtros com validação e sincronização automática.

### VehiclesTable
Tabela responsiva com paginação, ordenação e funcionalidades de exportação.

## 🔄 Estado da Aplicação

O estado é gerenciado pelo store Pinia (`vehicles.ts`) que inclui:

- **Dados dos veículos**: Array de veículos detectados
- **Filtros**: Configurações de filtragem
- **Estado de carregamento**: Indicadores de loading
- **Getters computados**: Estatísticas e filtros aplicados
- **Ações**: Carregar dados, aplicar filtros, exportar

## 📱 Responsividade

A aplicação é totalmente responsiva e funciona em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (até 767px)

## 🚀 Deploy

Para fazer deploy em produção:

```bash
npm run build
```

Os arquivos serão gerados na pasta `dist/` e podem ser servidos por qualquer servidor web estático.

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte ou dúvidas, entre em contato através de:
- Issues do GitHub
- Email: [seu-email@exemplo.com]

---

**Desenvolvido com ❤️ usando Vue 3 e TypeScript**
