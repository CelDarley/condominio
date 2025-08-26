#!/usr/bin/env python3
"""
Script de configuração inicial para SegCond
Execute este script para configurar o sistema pela primeira vez
"""

import os
import sys
import secrets
import subprocess
from pathlib import Path

def print_banner():
    """Exibe o banner do sistema"""
    print("""
    ╔══════════════════════════════════════════════════════════════╗
    ║                    SegCond - Setup                           ║
    ║              Sistema de Vigilância para Condomínios          ║
    ╚══════════════════════════════════════════════════════════════╝
    """)

def check_python_version():
    """Verifica a versão do Python"""
    if sys.version_info < (3, 8):
        print("❌ Erro: Python 3.8+ é necessário")
        print(f"   Versão atual: {sys.version}")
        return False
    print("✅ Python 3.8+ detectado")
    return True

def create_virtual_env():
    """Cria ambiente virtual"""
    if Path("venv").exists():
        print("✅ Ambiente virtual já existe")
        return True
    
    print("🔧 Criando ambiente virtual...")
    try:
        subprocess.run([sys.executable, "-m", "venv", "venv"], check=True)
        print("✅ Ambiente virtual criado com sucesso")
        return True
    except subprocess.CalledProcessError:
        print("❌ Erro ao criar ambiente virtual")
        return False

def install_dependencies():
    """Instala as dependências"""
    print("📦 Instalando dependências...")
    
    # Determinar o comando pip correto
    if os.name == 'nt':  # Windows
        pip_cmd = "venv\\Scripts\\pip"
    else:  # Linux/Mac
        pip_cmd = "venv/bin/pip"
    
    try:
        subprocess.run([pip_cmd, "install", "-r", "requirements.txt"], check=True)
        print("✅ Dependências instaladas com sucesso")
        return True
    except subprocess.CalledProcessError:
        print("❌ Erro ao instalar dependências")
        return False

def create_env_file():
    """Cria arquivo .env com configurações padrão"""
    if Path(".env").exists():
        print("✅ Arquivo .env já existe")
        return True
    
    print("🔧 Criando arquivo .env...")
    
    # Gerar chave secreta
    secret_key = secrets.token_hex(32)
    
    env_content = f"""# Configurações de ambiente para SegCond
# Arquivo gerado automaticamente pelo setup.py

# Chave secreta para sessões Flask
SECRET_KEY={secret_key}

# Configuração do banco de dados MySQL
DATABASE_URL=mysql+pymysql://root:password@localhost/segcond_db

# Configurações de desenvolvimento
FLASK_ENV=development
FLASK_DEBUG=True

# Configurações de segurança
SESSION_COOKIE_SECURE=False
SESSION_COOKIE_HTTPONLY=True
PERMANENT_SESSION_LIFETIME=3600

# Configurações de log
LOG_LEVEL=INFO
LOG_FILE=logs/segcond.log

# Configurações de PWA
PWA_NAME=SegCond
PWA_SHORT_NAME=SegCond
PWA_DESCRIPTION=Sistema de Vigilância para Condomínios
PWA_THEME_COLOR=#2196F3
PWA_BACKGROUND_COLOR=#2196F3
"""
    
    try:
        with open(".env", "w", encoding="utf-8") as f:
            f.write(env_content)
        print("✅ Arquivo .env criado com sucesso")
        print(f"   Chave secreta gerada: {secret_key[:16]}...")
        return True
    except Exception as e:
        print(f"❌ Erro ao criar arquivo .env: {e}")
        return False

def create_directories():
    """Cria diretórios necessários"""
    print("📁 Criando diretórios...")
    
    directories = [
        "logs",
        "static/icons",
        "static/screenshots",
        "backups"
    ]
    
    for directory in directories:
        Path(directory).mkdir(parents=True, exist_ok=True)
    
    print("✅ Diretórios criados com sucesso")

def create_sample_icons():
    """Cria ícones de exemplo para PWA"""
    print("🎨 Criando ícones de exemplo...")
    
    # Criar ícones simples usando Python (placeholder)
    icon_content = """# Placeholder para ícones PWA
# Substitua estes arquivos por ícones reais em PNG
# Tamanhos necessários: 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512
"""
    
    icon_sizes = [72, 96, 128, 144, 152, 192, 384, 512]
    
    for size in icon_sizes:
        icon_file = f"static/icons/icon-{size}x{size}.png"
        if not Path(icon_file).exists():
            # Criar arquivo placeholder
            with open(icon_file, "w") as f:
                f.write(f"# Placeholder para ícone {size}x{size}")
    
    print("✅ Ícones de exemplo criados")
    print("   ⚠️  Substitua por ícones reais em PNG antes de usar em produção")

def print_next_steps():
    """Exibe os próximos passos"""
    print("""
    ╔══════════════════════════════════════════════════════════════╗
    ║                    Próximos Passos                           ║
    ╚══════════════════════════════════════════════════════════════╝
    
    1. 📊 Configure o banco de dados MySQL:
       - Execute: mysql -u root -p < database_setup.sql
       - Ou crie manualmente seguindo o README.md
    
    2. 🔧 Configure o arquivo .env:
       - Ajuste DATABASE_URL com suas credenciais
       - Configure outras variáveis conforme necessário
    
    3. 🚀 Execute a aplicação:
       - Ative o ambiente virtual: source venv/bin/activate (Linux/Mac) ou venv\\Scripts\\activate (Windows)
       - Execute: python app.py
    
    4. 📱 Acesse no navegador:
       - URL: http://localhost:5000
       - Login padrão: admin@segcond.com / 123456
    
    5. 🎨 Personalize os ícones:
       - Substitua os placeholders em static/icons/ por ícones reais
    
    ╔══════════════════════════════════════════════════════════════╗
    ║                    Configuração Completa!                    ║
    ╚══════════════════════════════════════════════════════════════╝
    """)

def main():
    """Função principal"""
    print_banner()
    
    print("🔍 Verificando requisitos...")
    
    # Verificar versão do Python
    if not check_python_version():
        sys.exit(1)
    
    print("\n🚀 Iniciando configuração...")
    
    # Criar ambiente virtual
    if not create_virtual_env():
        sys.exit(1)
    
    # Instalar dependências
    if not install_dependencies():
        sys.exit(1)
    
    # Criar arquivo .env
    if not create_env_file():
        sys.exit(1)
    
    # Criar diretórios
    create_directories()
    
    # Criar ícones de exemplo
    create_sample_icons()
    
    print("\n" + "="*60)
    print_next_steps()

if __name__ == "__main__":
    main()
