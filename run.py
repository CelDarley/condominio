#!/usr/bin/env python3
"""
Script para executar o RBX-Security com informações de rede para acesso externo
"""

import os
import socket
import subprocess
import sys
from pathlib import Path

def get_local_ip():
    """Obtém o IP local da máquina"""
    try:
        # Conectar a um servidor externo para descobrir o IP local
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        local_ip = s.getsockname()[0]
        s.close()
        return local_ip
    except Exception:
        return "127.0.0.1"

def get_network_info():
    """Obtém informações de rede"""
    local_ip = get_local_ip()
    
    print("🌐 Informações de Rede:")
    print(f"   IP Local: {local_ip}")
    print(f"   Porta: 5000")
    print(f"   URL Local: http://localhost:5000")
    print(f"   URL Externa: http://{local_ip}:5000")
    print()

def check_dependencies():
    """Verifica se as dependências estão instaladas"""
    print("🔍 Verificando dependências...")
    
    try:
        import flask
        import flask_sqlalchemy
        import flask_login
        import flask_cors
        print("✅ Todas as dependências estão instaladas")
        return True
    except ImportError as e:
        print(f"❌ Dependência faltando: {e}")
        print("   Execute: pip install -r requirements.txt")
        return False

def check_database():
    """Verifica se o banco de dados está configurado"""
    print("🗄️ Verificando configuração do banco...")
    
    env_file = Path(".env")
    if not env_file.exists():
        print("❌ Arquivo .env não encontrado")
        print("   Execute: python3 setup.py")
        return False
    
    # Verificar se as variáveis de banco estão configuradas
    with open(env_file, 'r') as f:
        content = f.read()
        if 'DATABASE_URL' not in content:
            print("❌ DATABASE_URL não configurado no .env")
            return False
    
    print("✅ Configuração do banco OK")
    return True

def main():
    """Função principal"""
    print("""
    ╔══════════════════════════════════════════════════════════════╗
    ║                  RBX-Security - Runner                       ║
    ║              Sistema de Vigilância para Condomínios          ║
    ╚══════════════════════════════════════════════════════════════╝
    """)
    
    # Verificar dependências
    if not check_dependencies():
        sys.exit(1)
    
    # Verificar banco de dados
    if not check_database():
        sys.exit(1)
    
    print()
    
    # Mostrar informações de rede
    get_network_info()
    
    # Verificar se o ambiente virtual está ativo
    if not os.getenv('VIRTUAL_ENV'):
        print("⚠️  Ambiente virtual não está ativo")
        print("   Execute: source venv/bin/activate")
        print()
    
    print("🚀 Iniciando RBX-Security...")
    print("   Para parar: Ctrl+C")
    print("   Para acessar: Use a URL externa mostrada acima")
    print()
    
    # Executar a aplicação
    try:
        subprocess.run([sys.executable, "app.py"], check=True)
    except KeyboardInterrupt:
        print("\n👋 RBX-Security encerrado pelo usuário")
    except subprocess.CalledProcessError as e:
        print(f"\n❌ Erro ao executar RBX-Security: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
