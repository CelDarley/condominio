#!/usr/bin/env python3

from app import app, db, RegistroPresenca, Usuario, PontoBase
from datetime import datetime, date

def debug_presenca():
    with app.app_context():
        print("🔍 Debug do sistema de presença...")
        
        # 1. Verificar usuário atual (simular)
        usuario = Usuario.query.first()
        print(f"Usuário: {usuario.nome} (ID: {usuario.id})")
        
        # 2. Verificar pontos base
        pontos = PontoBase.query.filter_by(posto_id=1).all()
        print(f"\nPontos base do posto 1: {len(pontos)}")
        for ponto in pontos:
            print(f"  - ID: {ponto.id}, Nome: {ponto.nome}")
        
        # 3. Verificar registros de presença para hoje
        hoje = date.today()
        print(f"\n📅 Data de hoje: {hoje}")
        
        registros = RegistroPresenca.query.filter(
            RegistroPresenca.timestamp_chegada >= hoje
        ).all()
        
        print(f"Registros de presença para hoje: {len(registros)}")
        for registro in registros:
            print(f"  - ID: {registro.id}")
            print(f"    Usuário: {registro.usuario_id}")
            print(f"    Ponto: {registro.ponto_id}")
            print(f"    Chegada: {registro.timestamp_chegada}")
            print(f"    Saída: {registro.timestamp_saida}")
            print(f"    Tempo no ponto: {registro.timestamp_saida - registro.timestamp_chegada if registro.timestamp_saida else 'Ainda presente'}")
        
        # 4. Testar consulta específica (como na função posto)
        print(f"\n🧪 Testando consulta como na função posto...")
        
        for ponto in pontos[:3]:  # Testar apenas os primeiros 3 pontos
            registro = RegistroPresenca.query.filter_by(
                usuario_id=usuario.id,
                ponto_id=ponto.id
            ).filter(
                RegistroPresenca.timestamp_chegada >= hoje
            ).first()
            
            if registro:
                if registro.timestamp_saida:
                    status = 'concluido'
                    print(f"  Ponto {ponto.nome} (ID: {ponto.id}): {status}")
                    print(f"    Chegada: {registro.timestamp_chegada}")
                    print(f"    Saída: {registro.timestamp_saida}")
                else:
                    status = 'presente'
                    print(f"  Ponto {ponto.nome} (ID: {ponto.id}): {status}")
                    print(f"    Chegada: {registro.timestamp_chegada}")
                    print(f"    Saída: N/A")
            else:
                status = 'pendente'
                print(f"  Ponto {ponto.nome} (ID: {ponto.id}): {status}")
        
        # 5. Verificar se há problemas de timezone
        print(f"\n⏰ Verificando timezone...")
        agora = datetime.utcnow()
        agora_local = datetime.now()
        print(f"  UTC: {agora}")
        print(f"  Local: {agora_local}")
        print(f"  Diferença: {agora_local - agora}")

if __name__ == '__main__':
    debug_presenca()

