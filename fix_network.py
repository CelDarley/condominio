#!/usr/bin/env python3

import socket
import subprocess
import time
import requests
from flask import Flask, request

app = Flask(__name__)

@app.route('/')
def home():
    client_ip = request.remote_addr
    return f"""
    <html>
    <head><title>Teste de Rede - RBX Security</title></head>
    <body>
        <h1>🌐 Teste de Conectividade de Rede</h1>
        <h2>✅ Servidor funcionando!</h2>
        
        <h3>📊 Informações do Cliente:</h3>
        <ul>
            <li><strong>IP do Cliente:</strong> {client_ip}</li>
            <li><strong>User Agent:</strong> {request.headers.get('User-Agent', 'N/A')}</li>
            <li><strong>Host:</strong> {request.headers.get('Host', 'N/A')}</li>
        </ul>
        
        <h3>🔧 Informações do Servidor:</h3>
        <ul>
            <li><strong>IP do Servidor:</strong> {request.host}</li>
            <li><strong>Porta:</strong> 5000</li>
            <li><strong>Timestamp:</strong> {time.strftime('%d/%m/%Y %H:%M:%S')}</li>
        </ul>
        
        <h3>📱 URLs de Teste:</h3>
        <ul>
            <li><a href="http://localhost:5000">http://localhost:5000</a></li>
            <li><a href="http://127.0.0.1:5000">http://127.0.0.1:5000</a></li>
            <li><a href="http://10.100.0.58:5000">http://10.100.0.58:5000</a></li>
        </ul>
        
        <h3>🧪 Testes de Conectividade:</h3>
        <p>Se você consegue ver esta página, a conectividade está funcionando!</p>
    </body>
    </html>
    """

@app.route('/test')
def test():
    return f"""
    <h2>🧪 Rota de Teste</h2>
    <p>Cliente: {request.remote_addr}</p>
    <p>Timestamp: {time.strftime('%d/%m/%Y %H:%M:%S')}</p>
    <p>✅ Conectividade OK!</p>
    """

def test_connectivity():
    """Testa conectividade para diferentes endereços"""
    print("🔍 Testando conectividade de rede...")
    
    test_urls = [
        "http://localhost:5000",
        "http://127.0.0.1:5000", 
        "http://10.100.0.58:5000"
    ]
    
    for url in test_urls:
        try:
            response = requests.get(url, timeout=5)
            print(f"✅ {url} - Status: {response.status_code}")
        except Exception as e:
            print(f"❌ {url} - Erro: {e}")
    
    print("\n🌐 Iniciando servidor Flask...")
    print("📱 URLs de teste:")
    for url in test_urls:
        print(f"   - {url}")

if __name__ == '__main__':
    test_connectivity()
    
    print(f"\n🚀 Iniciando servidor em 0.0.0.0:5000")
    print(f"📱 Para acessar externamente:")
    print(f"   - http://localhost:5000")
    print(f"   - http://127.0.0.1:5000") 
    print(f"   - http://10.100.0.58:5000")
    print(f"\n⏹️  Pressione Ctrl+C para parar")
    
    app.run(host='0.0.0.0', port=5000, debug=False, threaded=True)
