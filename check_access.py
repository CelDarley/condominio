#!/usr/bin/env python3
"""
Script para verificar se o SegCond está acessível externamente
"""

import socket
import requests
import sys
import time
from pathlib import Path

def get_local_ip():
    """Obtém o IP local da máquina"""
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        local_ip = s.getsockname()[0]
        s.close()
        return local_ip
    except Exception:
        return "127.0.0.1"

def check_port_open(host, port):
    """Verifica se uma porta está aberta"""
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(5)
        result = sock.connect_ex((host, port))
        sock.close()
        return result == 0
    except Exception:
        return False

def check_http_access(url):
    """Verifica se a aplicação está respondendo via HTTP"""
    try:
        response = requests.get(url, timeout=10)
        return response.status_code == 200
    except Exception as e:
        return False, str(e)

def check_pwa_features(url):
    """Verifica se as funcionalidades PWA estão funcionando"""
    features = {}
    
    # Verificar manifest.json
    try:
        response = requests.get(f"{url}/manifest.json", timeout=5)
        features['manifest'] = response.status_code == 200
    except:
        features['manifest'] = False
    
    # Verificar service worker
    try:
        response = requests.get(f"{url}/sw.js", timeout=5)
        features['service_worker'] = response.status_code == 200
    except:
        features['service_worker'] = response.status_code == 200
    
    # Verificar CSS
    try:
        response = requests.get(f"{url}/static/css/style.css", timeout=5)
        features['css'] = response.status_code == 200
    except:
        features['css'] = False
    
    # Verificar JavaScript
    try:
        response = requests.get(f"{url}/static/js/app.js", timeout=5)
        features['javascript'] = response.status_code == 200
    except:
        features['javascript'] = False
    
    return features

def main():
    """Função principal"""
    print("🔍 Verificando acessibilidade externa do SegCond...")
    print()
    
    local_ip = get_local_ip()
    port = 5000
    
    print(f"📊 Informações de Rede:")
    print(f"   IP Local: {local_ip}")
    print(f"   Porta: {port}")
    print(f"   URL Local: http://localhost:{port}")
    print(f"   URL Externa: http://{local_ip}:{port}")
    print()
    
    # Verificar se a porta está aberta
    print("🔌 Verificando porta...")
    if check_port_open(local_ip, port):
        print("✅ Porta está aberta e aceitando conexões")
    else:
        print("❌ Porta não está acessível")
        print("   Verifique se o SegCond está rodando")
        print("   Execute: python3 run.py")
        return
    
    # Verificar acesso HTTP local
    print("\n🌐 Verificando acesso HTTP local...")
    local_url = f"http://localhost:{port}"
    if check_http_access(local_url):
        print("✅ Acesso local funcionando")
    else:
        print("❌ Acesso local não funcionando")
        print("   Verifique se o SegCond está rodando corretamente")
        return
    
    # Verificar acesso HTTP externo
    print("\n📱 Verificando acesso HTTP externo...")
    external_url = f"http://{local_ip}:{port}"
    if check_http_access(external_url):
        print("✅ Acesso externo funcionando")
        print(f"   📱 Seu smartphone pode acessar: {external_url}")
    else:
        print("❌ Acesso externo não funcionando")
        print("   Possíveis causas:")
        print("   - Firewall bloqueando conexões externas")
        print("   - Roteador não permitindo acesso externo")
        print("   - Configuração de rede incorreta")
        print()
        print("   💡 Soluções:")
        print("   1. Execute: sudo ./setup_network.sh")
        print("   2. Configure port forwarding no roteador")
        print("   3. Verifique configurações de firewall")
        return
    
    # Verificar funcionalidades PWA
    print("\n📱 Verificando funcionalidades PWA...")
    pwa_features = check_pwa_features(external_url)
    
    for feature, status in pwa_features.items():
        if status:
            print(f"   ✅ {feature}")
        else:
            print(f"   ❌ {feature}")
    
    print()
    print("🎉 Verificação concluída!")
    print()
    print("📱 Para acessar de um smartphone externo:")
    print(f"   URL: {external_url}")
    print()
    print("🔒 Recomendações de segurança:")
    print("   - Use HTTPS em produção")
    print("   - Configure autenticação forte")
    print("   - Monitore logs de acesso")
    print("   - Mantenha o sistema atualizado")
    print()
    print("🚀 Para executar o SegCond:")
    print("   python3 run.py")

if __name__ == "__main__":
    main()
