import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface Vehicle {
  ID: string
  Tipo: string
  'Hora Detecção': string
  Placa: string
  'Confiança Placa': string
  Cor: string
  'Bounding Box': string
  Frame: string
  Sentido: string
}

export const useVehiclesStore = defineStore('vehicles', () => {
  // Estado
  const vehicles = ref<Vehicle[]>([])
  const loading = ref(false)
  const filters = ref({
    data: '',
    hora: '',
    placa: '',
    sentido: '',
    showPlacasDetectadas: true,
    showPlacasNaoDetectadas: false
  })

  // Dados mockados (simulando o CSV)
  const mockVehicles: Vehicle[] = [
    {ID: "1872", Tipo: "Carro", "Hora Detecção": "2025-08-25 22:01:29", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "657,118,1136,677", Frame: "138305", Sentido: "descendo"},
    {ID: "1875", Tipo: "Carro", "Hora Detecção": "2025-08-25 22:01:33", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "657,118,1145,680", Frame: "138306", Sentido: "N/D"},
    {ID: "1936", Tipo: "Carro", "Hora Detecção": "2025-08-25 23:21:14", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "625,120,1133,666", Frame: "138315", Sentido: "descendo"},
    {ID: "1967", Tipo: "Carro", "Hora Detecção": "2025-08-26 05:19:17", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "500,98,1507,679", Frame: "148986", Sentido: "descendo"},
    {ID: "2015", Tipo: "Carro", "Hora Detecção": "2025-08-26 06:15:27", Placa: "SHS9E93", "Confiança Placa": "0.82", Cor: "branco", "Bounding Box": "1626,10,1898,367", Frame: "151155", Sentido: "descendo"},
    {ID: "2019", Tipo: "Caminhão/Caminhonete", "Hora Detecção": "2025-08-26 06:19:14", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "611,16,1636,631", Frame: "155078", Sentido: "subindo"},
    {ID: "2028", Tipo: "Carro", "Hora Detecção": "2025-08-26 06:27:46", Placa: "RMM5F48", "Confiança Placa": "0.78", Cor: "preto", "Bounding Box": "360,38,1499,704", Frame: "155084", Sentido: "descendo"},
    {ID: "2044", Tipo: "Carro", "Hora Detecção": "2025-08-26 06:52:30", Placa: "OXZ3F93", "Confiança Placa": "0.64", Cor: "preto", "Bounding Box": "397,289,1682,1032", Frame: "157273", Sentido: "descendo"},
    {ID: "2054", Tipo: "Moto", "Hora Detecção": "2025-08-26 06:55:25", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "443,283,1708,1015", Frame: "157274", Sentido: "N/D"},
    {ID: "2070", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:01:44", Placa: "DPI0429", "Confiança Placa": "0.97", Cor: "preto", "Bounding Box": "444,219,1800,1049", Frame: "158106", Sentido: "subindo"},
    {ID: "2072", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:05:36", Placa: "RFI8C67", "Confiança Placa": "0.61", Cor: "vermelho", "Bounding Box": "490,192,1851,1029", Frame: "158107", Sentido: "N/D"},
    {ID: "2082", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:09:07", Placa: "N/D", "Confiança Placa": "N/D", Cor: "cinza", "Bounding Box": "1528,9,1908,406", Frame: "158525", Sentido: "subindo"},
    {ID: "2086", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:09:35", Placa: "RVZ1A38", "Confiança Placa": "0.95", Cor: "preto", "Bounding Box": "310,169,1567,937", Frame: "160835", Sentido: "subindo"},
    {ID: "2093", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:12:04", Placa: "RML9044", "Confiança Placa": "0.72", Cor: "branco", "Bounding Box": "761,101,1912,760", Frame: "160844", Sentido: "descendo"},
    {ID: "2098", Tipo: "Moto", "Hora Detecção": "2025-08-26 07:20:34", Placa: "N/D", "Confiança Placa": "N/D", Cor: "cinza", "Bounding Box": "1169,182,1907,674", Frame: "161230", Sentido: "N/D"},
    {ID: "2109", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:25:01", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "345,37,1400,672", Frame: "161703", Sentido: "subindo"},
    {ID: "2124", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:26:49", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "237,49,1328,702", Frame: "161705", Sentido: "subindo"},
    {ID: "2132", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:29:01", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "386,50,1509,702", Frame: "161950", Sentido: "descendo"},
    {ID: "2139", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:33:18", Placa: "SHN0H13", "Confiança Placa": "0.77", Cor: "branco", "Bounding Box": "1757,12,1876,339", Frame: "162979", Sentido: "descendo"},
    {ID: "2138", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:33:25", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "369,121,1410,743", Frame: "163021", Sentido: "descendo"},
    {ID: "2142", Tipo: "Caminhão/Caminhonete", "Hora Detecção": "2025-08-26 07:34:44", Placa: "OPN9952", "Confiança Placa": "0.98", Cor: "preto", "Bounding Box": "105,152,1239,818", Frame: "163027", Sentido: "subindo"},
    {ID: "2145", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:38:05", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "266,305,1492,1043", Frame: "163383", Sentido: "descendo"},
    {ID: "2148", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:38:31", Placa: "PHL7F20", "Confiança Placa": "0.63", Cor: "preto", "Bounding Box": "563,255,1715,935", Frame: "163390", Sentido: "subindo"},
    {ID: "2150", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:39:49", Placa: "AIZ4074", "Confiança Placa": "0.70", Cor: "preto", "Bounding Box": "446,102,1468,658", Frame: "163839", Sentido: "subindo"},
    {ID: "2154", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:42:08", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "983,14,1853,537", Frame: "164096", Sentido: "descendo"},
    {ID: "2156", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:42:26", Placa: "FOF3089", "Confiança Placa": "0.79", Cor: "preto", "Bounding Box": "511,56,1546,652", Frame: "164106", Sentido: "subindo"},
    {ID: "2159", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:46:45", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "344,194,1529,923", Frame: "165686", Sentido: "descendo"},
    {ID: "2171", Tipo: "Moto", "Hora Detecção": "2025-08-26 07:50:02", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "392,186,1563,901", Frame: "165687", Sentido: "N/D"},
    {ID: "2176", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:52:01", Placa: "N/D", "Confiança Placa": "N/D", Cor: "cinza", "Bounding Box": "533,162,1654,836", Frame: "165690", Sentido: "descendo"},
    {ID: "2180", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:53:19", Placa: "SYA3D78", "Confiança Placa": "0.55", Cor: "preto", "Bounding Box": "775,187,1810,777", Frame: "165786", Sentido: "subindo"},
    {ID: "2186", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:54:03", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "356,156,1812,891", Frame: "165862", Sentido: "subindo"},
    {ID: "2194", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:54:41", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "977,157,1854,649", Frame: "166008", Sentido: "subindo"},
    {ID: "2192", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:54:36", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "107,44,1210,794", Frame: "166210", Sentido: "subindo"},
    {ID: "2202", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:55:29", Placa: "HJU7289", "Confiança Placa": "0.89", Cor: "preto", "Bounding Box": "205,30,1252,751", Frame: "166217", Sentido: "subindo"},
    {ID: "2207", Tipo: "Carro", "Hora Detecção": "2025-08-26 07:56:24", Placa: "PUH3013", "Confiança Placa": "0.94", Cor: "preto", "Bounding Box": "497,27,1599,697", Frame: "167478", Sentido: "subindo"},
    {ID: "2210", Tipo: "Moto", "Hora Detecção": "2025-08-26 08:03:30", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "461,31,1571,702", Frame: "167479", Sentido: "N/D"},
    {ID: "2213", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:04:37", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "244,45,1461,764", Frame: "167484", Sentido: "descendo"},
    {ID: "2216", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:05:28", Placa: "SYH3033", "Confiança Placa": "0.77", Cor: "branco", "Bounding Box": "339,244,1669,1011", Frame: "168565", Sentido: "descendo"},
    {ID: "2221", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:08:49", Placa: "SOZ3880", "Confiança Placa": "0.63", Cor: "branco", "Bounding Box": "574,201,1807,907", Frame: "168569", Sentido: "subindo"},
    {ID: "2224", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:10:00", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "226,39,1405,703", Frame: "169554", Sentido: "subindo"},
    {ID: "2237", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:18:53", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "591,324,1823,1032", Frame: "169670", Sentido: "descendo"},
    {ID: "2242", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:19:35", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "384,92,1430,682", Frame: "170233", Sentido: "subindo"},
    {ID: "2243", Tipo: "Caminhão/Caminhonete", "Hora Detecção": "2025-08-26 08:19:39", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "157,243,1408,1036", Frame: "171005", Sentido: "subindo"},
    {ID: "2249", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:22:58", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "434,197,1650,916", Frame: "171012", Sentido: "descendo"},
    {ID: "2253", Tipo: "Moto", "Hora Detecção": "2025-08-26 08:24:42", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "468,189,1688,907", Frame: "171013", Sentido: "N/D"},
    {ID: "2258", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:30:49", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "647,102,1659,679", Frame: "171812", Sentido: "subindo"},
    {ID: "2269", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:34:24", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "608,107,1630,691", Frame: "171813", Sentido: "N/D"},
    {ID: "2274", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:35:51", Placa: "N/D", "Confiança Placa": "N/D", Cor: "cinza", "Bounding Box": "15,282,1640,1069", Frame: "172568", Sentido: "descendo"},
    {ID: "2282", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:39:25", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "26,282,1649,1070", Frame: "172569", Sentido: "N/D"},
    {ID: "2283", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:43:40", Placa: "PPI9324", "Confiança Placa": "0.78", Cor: "branco", "Bounding Box": "1200,39,1895,726", Frame: "172821", Sentido: "subindo"},
    {ID: "2293", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:45:45", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "5,407,1379,1071", Frame: "174485", Sentido: "parado"},
    {ID: "2336", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:56:46", Placa: "EKS2339", "Confiança Placa": "0.79", Cor: "cinza", "Bounding Box": "9,411,1413,1071", Frame: "174532", Sentido: "descendo"},
    {ID: "2337", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:56:47", Placa: "N/D", "Confiança Placa": "N/D", Cor: "azul", "Bounding Box": "9,411,1413,1071", Frame: "174532", Sentido: "parado"},
    {ID: "2346", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:58:14", Placa: "N/D", "Confiança Placa": "N/D", Cor: "branco", "Bounding Box": "13,414,1501,1071", Frame: "174551", Sentido: "parado"},
    {ID: "2348", Tipo: "Carro", "Hora Detecção": "2025-08-26 08:59:14", Placa: "N/D", "Confiança Placa": "N/D", Cor: "azul", "Bounding Box": "628,193,1745,856", Frame: "177436", Sentido: "subindo"},
    {ID: "2361", Tipo: "Moto", "Hora Detecção": "2025-08-26 09:05:32", Placa: "N/D", "Confiança Placa": "N/D", Cor: "cinza", "Bounding Box": "736,187,1812,826", Frame: "177438", Sentido: "N/D"},
    {ID: "2374", Tipo: "Carro", "Hora Detecção": "2025-08-26 09:13:50", Placa: "N/D", "Confiança Placa": "N/D", Cor: "preto", "Bounding Box": "1728,23,1875,360", Frame: "178826", Sentido: "subindo"}
  ]

  // Getters
  const filteredVehicles = computed(() => {
    return vehicles.value.filter(vehicle => {
      let match = true

      // Filtro por data
      if (filters.value.data && vehicle["Hora Detecção"].split(' ')[0] !== filters.value.data) {
        match = false
      }

      // Filtro por hora
      if (filters.value.hora && !vehicle["Hora Detecção"].split(' ')[1].startsWith(filters.value.hora)) {
        match = false
      }

      // Filtro por placa
      if (filters.value.placa && vehicle.Placa !== "N/D" && !vehicle.Placa.includes(filters.value.placa.toUpperCase())) {
        match = false
      }

      // Filtro por sentido
      if (filters.value.sentido && vehicle.Sentido !== filters.value.sentido) {
        match = false
      }

      // Filtro por status da placa
      if (vehicle.Placa === "N/D" && !filters.value.showPlacasNaoDetectadas) {
        match = false
      }
      if (vehicle.Placa !== "N/D" && !filters.value.showPlacasDetectadas) {
        match = false
      }

      return match
    })
  })

  const totalVehicles = computed(() => vehicles.value.length)
  const filteredTotal = computed(() => filteredVehicles.value.length)
  const placasDetectadas = computed(() => filteredVehicles.value.filter(v => v.Placa !== "N/D").length)
  const placasNaoDetectadas = computed(() => filteredVehicles.value.filter(v => v.Placa === "N/D").length)

  // Ações
  const loadVehicles = async () => {
    loading.value = true
    try {
      // Simular carregamento de API
      await new Promise(resolve => setTimeout(resolve, 1000))
      vehicles.value = mockVehicles
    } catch (error) {
      console.error('Erro ao carregar veículos:', error)
    } finally {
      loading.value = false
    }
  }

  const updateFilters = (newFilters: Partial<typeof filters.value>) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const resetFilters = () => {
    filters.value = {
      data: '',
      hora: '',
      placa: '',
      sentido: '',
      showPlacasDetectadas: true,
      showPlacasNaoDetectadas: false
    }
  }

  const exportToCSV = () => {
    const headers = ['ID', 'Tipo', 'Data', 'Hora', 'Placa', 'Confiança Placa', 'Cor', 'Bounding Box', 'Frame', 'Sentido']
    const csvContent = [
      headers.join(','),
      ...filteredVehicles.value.map(vehicle => {
        const [data, hora] = vehicle["Hora Detecção"].split(' ')
        const dataFormatada = formatarData(data)
        return [
          vehicle.ID,
          vehicle.Tipo,
          `"${dataFormatada}"`,
          `"${hora}"`,
          vehicle.Placa,
          vehicle["Confiança Placa"],
          vehicle.Cor,
          `"${vehicle["Bounding Box"]}"`,
          vehicle.Frame,
          vehicle.Sentido
        ].join(',')
      })
    ].join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)
    link.setAttribute('href', url)
    link.setAttribute('download', 'relatorio_veiculos.csv')
    link.style.visibility = 'hidden'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }

  const formatarData = (data: string) => {
    const [ano, mes, dia] = data.split('-')
    return `${dia}-${mes}-${ano}`
  }

  return {
    // Estado
    vehicles,
    loading,
    filters,
    
    // Getters
    filteredVehicles,
    totalVehicles,
    filteredTotal,
    placasDetectadas,
    placasNaoDetectadas,
    
    // Ações
    loadVehicles,
    updateFilters,
    resetFilters,
    exportToCSV,
    formatarData
  }
})
