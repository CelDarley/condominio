#!/usr/bin/env python3

from app import app, db, CartaoProgramaPonto, PontoBase, Escala, Usuario, CartaoPrograma

def test_posto_function():
    with app.app_context():
        print('Testando função posto modificada:')
        
        # Verificar usuário
        usuario = Usuario.query.first()
        print('Usuario encontrado:', usuario.email if usuario else 'Nenhum')
        
        # Verificar cartões programa para o posto 1
        posto_id = 1
        print(f'\nVerificando posto ID: {posto_id}')
        
        # Verificar se há cartões programa para este posto
        cartoes_programa = CartaoPrograma.query.filter_by(posto_trabalho_id=posto_id, ativo=True).all()
        print(f'Cartões programa encontrados para o posto: {len(cartoes_programa)}')
        
        if cartoes_programa:
            cartao_programa = cartoes_programa[0]
            print(f'Usando cartão programa: {cartao_programa.nome}')
            
            pontos_programa = CartaoProgramaPonto.query.filter_by(
                cartao_programa_id=cartao_programa.id
            ).order_by(CartaoProgramaPonto.ordem).all()
            
            print(f'Pontos programa encontrados: {len(pontos_programa)}')
            
            for ponto_programa in pontos_programa:
                ponto_base = PontoBase.query.get(ponto_programa.ponto_base_id)
                if ponto_base:
                    print(f'Ponto {ponto_base.nome}:')
                    print(f'  - Tempo permanencia: {ponto_programa.tempo_permanencia} min')
                    print(f'  - Tempo deslocamento: {ponto_programa.tempo_deslocamento} min')
                    print(f'  - Obrigatorio: {ponto_programa.obrigatorio}')
                    print(f'  - Ordem: {ponto_programa.ordem}')
        else:
            print('Nenhum cartão programa encontrado para este posto')

if __name__ == '__main__':
    test_posto_function()
