#!/usr/bin/env python3

import socket
import requests
from flask import Flask

app = Flask(__name__)

@app.route('/')
def hello():
    return "Teste de conectividade - Aplicação funcionando!"

@app.route('/test')
def test():
    return "Rota de teste funcionando!"

if __name__ == '__main__':
    # Obter informações de rede
    hostname = socket.gethostname()
    local_ip = socket.gethostbyname(hostname)
    
    print(f"🔍 Informações de rede:")
    print(f"   Hostname: {hostname}")
    print(f"   IP Local: {local_ip}")
    print(f"   IP Rede: 10.100.0.58")
    
    # Testar conectividade
    try:
        response = requests.get('http://localhost:5000', timeout=5)
        print(f"✅ Localhost:5000 - Status: {response.status_code}")
    except Exception as e:
        print(f"❌ Localhost:5000 - Erro: {e}")
    
    try:
        response = requests.get('http://10.100.0.58:5000', timeout=5)
        print(f"✅ 10.100.0.58:5000 - Status: {response.status_code}")
    except Exception as e:
        print(f"❌ 10.100.0.58:5000 - Erro: {e}")
    
    print(f"\n🚀 Iniciando aplicação Flask em 0.0.0.0:5000")
    print(f"📱 Acesse em:")
    print(f"   - http://localhost:5000")
    print(f"   - http://10.100.0.58:5000")
    print(f"   - http://{local_ip}:5000")
    
    app.run(host='0.0.0.0', port=5000, debug=False)
