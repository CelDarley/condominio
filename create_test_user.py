#!/usr/bin/env python3
"""
Script para criar usuário de teste no Flask
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from app import app, db, Usuario
from werkzeug.security import generate_password_hash

def create_test_user():
    """Cria um usuário de teste para o Flask"""
    with app.app_context():
        # Verificar se o usuário já existe
        existing_user = Usuario.query.filter_by(email='teste@segcond.com').first()
        if existing_user:
            print("✅ Usuário de teste já existe!")
            return
        
        # Criar novo usuário
        novo_usuario = Usuario(
            nome='Usuário Teste',
            email='teste@segcond.com',
            senha_hash=generate_password_hash('123456'),
            tipo='vigilante',
            ativo=True
        )
        
        db.session.add(novo_usuario)
        db.session.commit()
        
        print("✅ Usuário de teste criado com sucesso!")
        print("📧 Email: teste@segcond.com")
        print("🔑 Senha: 123456")

if __name__ == "__main__":
    create_test_user()
