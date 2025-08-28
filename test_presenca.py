#!/usr/bin/env python3

from app import app, db, RegistroPresenca, Usuario, PontoBase
from datetime import datetime, date

def test_presenca_system():
    with app.app_context():
        print("🧪 Testando sistema de presença...")
        
        # 1. Verificar usuários
        usuarios = Usuario.query.all()
        print(f"Usuários encontrados: {len(usuarios)}")
        for user in usuarios:
            print(f"  - ID: {user.id}, Nome: {user.nome}, Email: {user.email}")
        
        # 2. Verificar pontos base
        pontos = PontoBase.query.all()
        print(f"\nPontos base encontrados: {len(pontos)}")
        for ponto in pontos:
            print(f"  - ID: {ponto.id}, Nome: {ponto.nome}, Posto: {ponto.posto_id}")
        
        # 3. Verificar registros de presença
        hoje = date.today()
        registros = RegistroPresenca.query.filter(
            RegistroPresenca.timestamp_chegada >= hoje
        ).all()
        
        print(f"\nRegistros de presença para hoje ({hoje}): {len(registros)}")
        for registro in registros:
            print(f"  - Usuário: {registro.usuario_id}, Ponto: {registro.ponto_id}")
            print(f"    Chegada: {registro.timestamp_chegada}")
            print(f"    Saída: {registro.timestamp_saida}")
        
        # 4. Testar criação de registro
        if usuarios and pontos:
            print(f"\n🧪 Criando registro de teste...")
            
            # Criar registro de chegada
            novo_registro = RegistroPresenca(
                usuario_id=usuarios[0].id,
                ponto_id=pontos[0].id
            )
            
            try:
                db.session.add(novo_registro)
                db.session.commit()
                print(f"✅ Registro criado com sucesso!")
                print(f"   ID: {novo_registro.id}")
                print(f"   Usuário: {novo_registro.usuario_id}")
                print(f"   Ponto: {novo_registro.ponto_id}")
                print(f"   Chegada: {novo_registro.timestamp_chegada}")
                
                # Simular saída
                novo_registro.timestamp_saida = datetime.utcnow()
                db.session.commit()
                print(f"✅ Saída registrada: {novo_registro.timestamp_saida}")
                
            except Exception as e:
                print(f"❌ Erro ao criar registro: {e}")
                db.session.rollback()

if __name__ == '__main__':
    test_presenca_system()
