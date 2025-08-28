#!/usr/bin/env python3

import socket
import subprocess
import os

def check_network_config():
    print("🔍 Verificando configurações de rede...")
    
    # 1. Verificar interfaces de rede
    print("\n1. Interfaces de rede:")
    try:
        result = subprocess.run(['ip', 'addr', 'show'], capture_output=True, text=True)
        print(result.stdout)
    except Exception as e:
        print(f"Erro ao verificar interfaces: {e}")
    
    # 2. Verificar roteamento
    print("\n2. Tabela de roteamento:")
    try:
        result = subprocess.run(['ip', 'route', 'show'], capture_output=True, text=True)
        print(result.stdout)
    except Exception as e:
        print(f"Erro ao verificar roteamento: {e}")
    
    # 3. Verificar conectividade
    print("\n3. Teste de conectividade:")
    
    # Teste localhost
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind(('127.0.0.1', 0))
        local_port = sock.getsockname()[1]
        sock.close()
        print(f"✅ Binding localhost OK - Porta disponível: {local_port}")
    except Exception as e:
        print(f"❌ Binding localhost falhou: {e}")
    
    # Teste 0.0.0.0
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind(('0.0.0.0', 0))
        any_port = sock.getsockname()[1]
        sock.close()
        print(f"✅ Binding 0.0.0.0 OK - Porta disponível: {any_port}")
    except Exception as e:
        print(f"❌ Binding 0.0.0.0 falhou: {e}")
    
    # Teste IP específico
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind(('10.100.0.58', 0))
        specific_port = sock.getsockname()[1]
        sock.close()
        print(f"✅ Binding 10.100.0.58 OK - Porta disponível: {specific_port}")
    except Exception as e:
        print(f"❌ Binding 10.100.0.58 falhou: {e}")
    
    # 4. Verificar processos na porta 5000
    print("\n4. Processos na porta 5000:")
    try:
        result = subprocess.run(['netstat', '-tlnp'], capture_output=True, text=True)
        lines = result.stdout.split('\n')
        for line in lines:
            if ':5000' in line:
                print(f"   {line}")
    except Exception as e:
        print(f"Erro ao verificar porta 5000: {e}")
    
    # 5. Verificar firewall
    print("\n5. Status do firewall:")
    try:
        result = subprocess.run(['ufw', 'status'], capture_output=True, text=True)
        print(result.stdout)
    except Exception as e:
        print(f"Erro ao verificar firewall: {e}")

if __name__ == '__main__':
    check_network_config()
