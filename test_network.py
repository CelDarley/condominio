#!/usr/bin/env python3
"""
Script para testar conectividade de rede
"""

import socket
import requests

def test_local_binding():
    """Testa se conseguimos fazer binding em diferentes interfaces"""
    print("🔍 Testando binding de rede...")
    
    # Teste 1: Binding em localhost
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind(('127.0.0.1', 5001))
        sock.listen(1)
        print("✅ Binding em 127.0.0.1:5001 - OK")
        sock.close()
    except Exception as e:
        print(f"❌ Binding em 127.0.0.1:5001 - Falhou: {e}")
    
    # Teste 2: Binding em 0.0.0.0
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind(('0.0.0.0', 5002))
        sock.listen(1)
        print("✅ Binding em 0.0.0.0:5002 - OK")
        sock.close()
    except Exception as e:
        print(f"❌ Binding em 0.0.0.0:5002 - Falhou: {e}")
    
    # Teste 3: Binding no IP específico
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind(('10.100.0.58', 5003))
        sock.listen(1)
        print("✅ Binding em 10.100.0.58:5003 - OK")
        sock.close()
    except Exception as e:
        print(f"❌ Binding em 10.100.0.58:5003 - Falhou: {e}")

def test_connectivity():
    """Testa conectividade para diferentes IPs"""
    print("\n🌐 Testando conectividade...")
    
    # Teste localhost
    try:
        response = requests.get('http://127.0.0.1:5000', timeout=5)
        print(f"✅ localhost:5000 - Status: {response.status_code}")
    except Exception as e:
        print(f"❌ localhost:5000 - Falhou: {e}")
    
    # Teste IP específico
    try:
        response = requests.get('http://10.100.0.58:5000', timeout=5)
        print(f"✅ 10.100.0.58:5000 - Status: {response.status_code}")
    except Exception as e:
        print(f"❌ 10.100.0.58:5000 - Falhou: {e}")

if __name__ == "__main__":
    test_local_binding()
    test_connectivity()
