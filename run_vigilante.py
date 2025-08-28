#!/usr/bin/env python3
"""
Script para rodar o app do vigilante no IP específico
"""

import os
import sys
from app import app

if __name__ == '__main__':
    # Configurar variáveis de ambiente para o IP específico
    os.environ['FLASK_HOST'] = '10.100.0.58'
    os.environ['FLASK_PORT'] = '5001'
    os.environ['FLASK_DEBUG'] = 'True'
    
    # Criar contexto da aplicação
    with app.app_context():
        from app import db
        db.create_all()
    
    print("🚀 SegCond - App do Vigilante iniciando...")
    print(f"📱 Acesse em: http://10.100.0.58:5001")
    print(f"🌐 IP Local: http://localhost:5001")
    print(f"🔧 Modo debug: True")
    
    # Rodar a aplicação
    app.run(
        debug=True,
        host='10.100.0.58',
        port=5001,
        threaded=True
    )
