import pandas as pd

# Ler o arquivo CSV
df = pd.read_csv('vehicles (3).csv')

# Adicionar coluna com o endereço da câmera
df['Endereço da Câmera'] = 'Rua Gurupi, 28'

# Converter a coluna 'Hora Detecção' em duas colunas separadas para data e hora
df['Data'] = pd.to_datetime(df['Hora Detecção']).dt.date
df['Hora'] = pd.to_datetime(df['Hora Detecção']).dt.time

# Remover a coluna 'Frame'
df = df.drop('Frame', axis=1)

# Criar o relatório HTML
html = '''
<html>
<head>
    <title>Relatório de Veículos</title>
    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h1>Relatório de Veículos</h1>
    <form>
        <label for="data">Data:</label>
        <input type="date" id="data" name="data"><br><br>
        <label for="hora">Hora:</label>
        <input type="time" id="hora" name="hora"><br><br>
        <label for="placa">Placa:</label>
        <input type="text" id="placa" name="placa"><br><br>
        <label for="sentido">Sentido:</label>
        <input type="text" id="sentido" name="sentido"><br><br>
        <input type="submit" value="Pesquisar">
    </form>
    {}
</body>
</html>
'''.format(df.to_html(index=False))

# Salvar o relatório HTML em um arquivo
with open('relatorio.html', 'w') as f:
    f.write(html)
