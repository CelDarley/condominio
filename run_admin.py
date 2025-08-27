#!/usr/bin/env python3
"""
SegCond Admin - Script de Execução
Sistema administrativo para gerenciar o SegCond
"""

import os
import sys
import subprocess
from pathlib import Path

def main():
    print("🚀 SegCond Admin - Sistema de Administração")
    print("=" * 50)
    
    # Verificar se estamos no diretório correto
    if not Path("admin.py").exists():
        print("❌ Erro: Execute este script no diretório do SegCond")
        sys.exit(1)
    
    # Verificar se o ambiente virtual existe
    if not Path("venv").exists():
        print("❌ Ambiente virtual não encontrado. Execute 'python3 setup.py' primeiro.")
        sys.exit(1)
    
    # Verificar se as dependências estão instaladas
    try:
        import flask
        import flask_sqlalchemy
        import flask_login
        print("✅ Dependências verificadas")
    except ImportError as e:
        print(f"❌ Dependência faltando: {e}")
        print("Execute: source venv/bin/activate && pip install -r requirements.txt")
        sys.exit(1)
    
    # Verificar se o banco de dados está acessível
    try:
        from dotenv import load_dotenv
        load_dotenv()
        
        database_url = os.getenv('DATABASE_URL')
        if not database_url:
            print("❌ DATABASE_URL não configurado no arquivo .env")
            sys.exit(1)
        
        print("✅ Configuração do banco de dados verificada")
    except Exception as e:
        print(f"❌ Erro ao verificar configuração: {e}")
        sys.exit(1)
    
    # Verificar se a porta está livre
    admin_port = int(os.getenv('ADMIN_PORT', 5010))
    try:
        import socket
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        result = sock.connect_ex(('localhost', admin_port))
        sock.close()
        
        if result == 0:
            print(f"⚠️  Porta {admin_port} já está em uso")
            choice = input("Deseja tentar outra porta? (s/n): ").lower()
            if choice == 's':
                new_port = input(f"Digite uma nova porta (padrão: {admin_port + 1}): ").strip()
                if new_port:
                    admin_port = int(new_port)
                else:
                    admin_port += 1
                print(f"🔄 Tentando porta {admin_port}")
            else:
                print("❌ Operação cancelada")
                sys.exit(1)
    except Exception as e:
        print(f"⚠️  Erro ao verificar porta: {e}")
    
    # Atualizar arquivo .env com a porta do admin
    env_file = Path(".env")
    if env_file.exists():
        env_content = env_file.read_text()
        if "ADMIN_PORT=" not in env_content:
            env_content += f"\nADMIN_PORT={admin_port}\n"
        else:
            # Substituir porta existente
            import re
            env_content = re.sub(r'ADMIN_PORT=\d+', f'ADMIN_PORT={admin_port}', env_content)
        
        env_file.write_text(env_content)
        print(f"✅ Porta do admin configurada: {admin_port}")
    
    # Verificar se o banco de dados tem as tabelas necessárias
    print("🔍 Verificando estrutura do banco de dados...")
    try:
        from admin import app, db
        with app.app_context():
            # Tentar criar as tabelas se não existirem
            db.create_all()
            print("✅ Estrutura do banco de dados verificada")
    except Exception as e:
        print(f"❌ Erro ao verificar banco de dados: {e}")
        print("Verifique se o MySQL está rodando e as credenciais estão corretas")
        sys.exit(1)
    
    # Verificar se há usuário admin
    try:
        from admin import Usuario
        from werkzeug.security import generate_password_hash
        
        with app.app_context():
            admin_user = Usuario.query.filter_by(tipo='admin').first()
            if not admin_user:
                print("⚠️  Nenhum usuário administrador encontrado")
                print("Criando usuário admin padrão...")
                
                novo_admin = Usuario(
                    nome='Administrador',
                    email='admin@segcond.com',
                    senha_hash=generate_password_hash('admin123'),
                    tipo='admin'
                )
                db.session.add(novo_admin)
                db.session.commit()
                print("✅ Usuário admin criado:")
                print("   Email: admin@segcond.com")
                print("   Senha: admin123")
                print("   ⚠️  ALTERE A SENHA APÓS O PRIMEIRO LOGIN!")
            else:
                print("✅ Usuário administrador encontrado")
    except Exception as e:
        print(f"⚠️  Erro ao verificar usuário admin: {e}")
    
    # Iniciar o sistema administrativo
    print("\n🚀 Iniciando SegCond Admin...")
    print(f"📱 Acesse: http://localhost:{admin_port}/admin")
    print(f"🌐 Acesso externo: http://0.0.0.0:{admin_port}/admin")
    print("\n📋 Credenciais padrão:")
    print("   Email: admin@segcond.com")
    print("   Senha: admin123")
    print("\n⏹️  Para parar: Ctrl+C")
    print("=" * 50)
    
    try:
        # Executar o admin
        os.environ['ADMIN_PORT'] = str(admin_port)
        os.environ['ADMIN_DEBUG'] = 'True'
        
        from admin import app
        app.run(
            host='0.0.0.0',
            port=admin_port,
            debug=True
        )
    except KeyboardInterrupt:
        print("\n\n🛑 SegCond Admin parado pelo usuário")
    except Exception as e:
        print(f"\n❌ Erro ao executar SegCond Admin: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
