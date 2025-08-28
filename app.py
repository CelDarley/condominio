from flask import Flask, render_template, request, jsonify, redirect, url_for, flash
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from werkzeug.security import generate_password_hash, check_password_hash
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'sua-chave-secreta-aqui')
app.config['SQLALCHEMY_DATABASE_URI'] = os.getenv('DATABASE_URL', 'mysql+pymysql://root:password@localhost/segcond_db')
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Configurações de CORS para acesso externo
from flask_cors import CORS
CORS(app, origins=os.getenv('CORS_ORIGINS', '*'), 
      methods=os.getenv('CORS_METHODS', 'GET,POST,PUT,DELETE,OPTIONS').split(','),
      allow_headers=os.getenv('CORS_ALLOW_HEADERS', 'Content-Type,Authorization').split(','))

db = SQLAlchemy(app)
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'index'

# Modelos do banco de dados
class Usuario(UserMixin, db.Model):
    __tablename__ = 'usuario'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    senha_hash = db.Column(db.String(255), nullable=False)
    tipo = db.Column(db.String(20), default='vigilante')  # vigilante ou morador
    ativo = db.Column(db.Boolean, default=True)

class PostoTrabalho(db.Model):
    __tablename__ = 'posto_trabalho'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    descricao = db.Column(db.Text)
    ativo = db.Column(db.Boolean, default=True)

class CartaoPrograma(db.Model):
    __tablename__ = 'cartao_programas'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    descricao = db.Column(db.Text)
    posto_trabalho_id = db.Column(db.Integer, db.ForeignKey('posto_trabalho.id'), nullable=False)
    horario_inicio = db.Column(db.Time, nullable=False)
    horario_fim = db.Column(db.Time, nullable=False)
    tempo_total_estimado = db.Column(db.Integer, default=0)
    ativo = db.Column(db.Boolean, default=True)
    configuracoes = db.Column(db.Text)  # JSON
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relacionamentos
    posto_trabalho = db.relationship('PostoTrabalho', backref='cartoes_programa')

class Escala(db.Model):
    __tablename__ = 'escala'
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    posto_trabalho_id = db.Column(db.Integer, db.ForeignKey('posto_trabalho.id'), nullable=False)  # Atualizado
    cartao_programa_id = db.Column(db.Integer, db.ForeignKey('cartao_programas.id'), nullable=True)  # Novo
    dia_semana = db.Column(db.Integer, nullable=False)  # 0=Segunda, 1=Terça, etc.
    ativo = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relacionamentos
    usuario = db.relationship('Usuario', backref='escalas')
    posto_trabalho = db.relationship('PostoTrabalho', backref='escalas')
    cartao_programa = db.relationship('CartaoPrograma', backref='escalas')

class PontoBase(db.Model):
    __tablename__ = 'ponto_base'
    id = db.Column(db.Integer, primary_key=True)
    posto_id = db.Column(db.Integer, db.ForeignKey('posto_trabalho.id'), nullable=False)
    nome = db.Column(db.String(100), nullable=False)
    descricao = db.Column(db.Text)
    horario_inicio = db.Column(db.Time, nullable=False)
    horario_fim = db.Column(db.Time, nullable=False)
    tempo_permanencia = db.Column(db.Integer, nullable=False)
    instrucoes = db.Column(db.Text)
    ordem = db.Column(db.Integer)
    endereco = db.Column(db.String(200), nullable=False, default='')
    ativo = db.Column(db.Boolean, default=True)

class CartaoProgramaPonto(db.Model):
    __tablename__ = 'cartao_programa_pontos'
    id = db.Column(db.Integer, primary_key=True)
    cartao_programa_id = db.Column(db.Integer, db.ForeignKey('cartao_programas.id'), nullable=False)
    ponto_base_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    ordem = db.Column(db.Integer, nullable=False)
    tempo_permanencia = db.Column(db.Integer, default=10)  # em minutos
    tempo_deslocamento = db.Column(db.Integer, default=5)  # em minutos
    instrucoes_especificas = db.Column(db.Text)
    obrigatorio = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relacionamentos
    cartao_programa = db.relationship('CartaoPrograma', backref='pontos')
    ponto_base = db.relationship('PontoBase', backref='cartao_programa_pontos')

class Itinerario(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    posto_id = db.Column(db.Integer, db.ForeignKey('posto_trabalho.id'), nullable=False)
    ponto_origem_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    ponto_destino_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    tempo_estimado = db.Column(db.Integer, nullable=False)  # em minutos
    instrucoes = db.Column(db.Text)

class RegistroPresenca(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    ponto_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    timestamp_chegada = db.Column(db.DateTime, default=datetime.utcnow)
    timestamp_saida = db.Column(db.DateTime)

class Aviso(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    titulo = db.Column(db.String(200), nullable=False)
    mensagem = db.Column(db.Text, nullable=False)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow)
    ativo = db.Column(db.Boolean, default=True)

class Alerta(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    morador_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    vigilante_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    mensagem = db.Column(db.Text, nullable=False)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow)
    atendido = db.Column(db.Boolean, default=False)

@login_manager.user_loader
def load_user(user_id):
    return Usuario.query.get(int(user_id))

@app.route('/', methods=['GET', 'POST'])
def index():
    if current_user.is_authenticated:
        return redirect(url_for('home'))
    
    if request.method == 'POST':
        email = request.form.get('email')
        senha = request.form.get('senha')
        
        usuario = Usuario.query.filter_by(email=email).first()
        if usuario and check_password_hash(usuario.senha_hash, senha):
            login_user(usuario)
            return redirect(url_for('home'))
        else:
            flash('Email ou senha inválidos')
    
    return render_template('login.html')



@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('index'))

@app.route('/home')
@login_required
def home():
    hoje = datetime.now().weekday()
    escala = Escala.query.filter_by(
        usuario_id=current_user.id,
        dia_semana=hoje,
        ativo=True
    ).first()
    
    if escala:
        postos = PostoTrabalho.query.filter_by(id=escala.posto_trabalho_id, ativo=True).all()
        # Incluir informações do cartão programa na escala
        cartao_programa = None
        if escala.cartao_programa_id:
            cartao_programa = CartaoPrograma.query.get(escala.cartao_programa_id)
    else:
        postos = []
        cartao_programa = None
    
    return render_template('home.html', postos=postos, hoje=hoje, escala=escala, cartao_programa=cartao_programa)

@app.route('/api/postos_por_dia/<int:dia_semana>')
@login_required
def postos_por_dia(dia_semana):
    """Retorna postos de trabalho para um dia específico da semana"""
    escala = Escala.query.filter_by(
        usuario_id=current_user.id,
        dia_semana=dia_semana,
        ativo=True
    ).first()
    
    if escala:
        posto = PostoTrabalho.query.filter_by(id=escala.posto_trabalho_id, ativo=True).first()
        cartao_programa = None
        if escala.cartao_programa_id:
            cartao_programa = CartaoPrograma.query.get(escala.cartao_programa_id)
            
        result = {
            'posto': {
                'id': posto.id,
                'nome': posto.nome,
                'descricao': posto.descricao
            } if posto else None,
            'cartao_programa': {
                'nome': cartao_programa.nome,
                'descricao': cartao_programa.descricao,
                'horario_inicio': cartao_programa.horario_inicio.strftime('%H:%M'),
                'horario_fim': cartao_programa.horario_fim.strftime('%H:%M')
            } if cartao_programa else None
        }
        return jsonify(result)
    else:
        return jsonify({'posto': None, 'cartao_programa': None})

@app.route('/posto/<int:posto_id>')
@login_required
def posto(posto_id):
    posto = PostoTrabalho.query.get_or_404(posto_id)
    
    # Verificar se o vigilante tem escala para hoje neste posto
    hoje = datetime.now().weekday()
    escala = Escala.query.filter_by(
        usuario_id=current_user.id,
        posto_trabalho_id=posto_id,
        dia_semana=hoje,
        ativo=True
    ).first()
    
    # Se há escala com cartão programa, usar os pontos do cartão
    if escala and escala.cartao_programa_id:
        cartao_programa = CartaoPrograma.query.get(escala.cartao_programa_id)
        pontos_programa = CartaoProgramaPonto.query.filter_by(
            cartao_programa_id=escala.cartao_programa_id
        ).order_by(CartaoProgramaPonto.ordem).all()
        
        pontos_base = []
        for ponto_programa in pontos_programa:
            ponto_base = PontoBase.query.get(ponto_programa.ponto_base_id)
            if ponto_base:
                # Adicionar informações do cartão programa ao ponto
                ponto_base.tempo_permanencia = ponto_programa.tempo_permanencia
                ponto_base.tempo_deslocamento = ponto_programa.tempo_deslocamento
                ponto_base.instrucoes_especificas = ponto_programa.instrucoes_especificas
                ponto_base.obrigatorio = ponto_programa.obrigatorio
                ponto_base.ordem = ponto_programa.ordem
                pontos_base.append(ponto_base)
    else:
        # Fallback: usar pontos do posto (modelo antigo)
        pontos_base = PontoBase.query.filter_by(posto_id=posto_id).all()
        cartao_programa = None
    
    # Buscar itinerários (mantido para compatibilidade)
    itinerarios = []
    for i in range(len(pontos_base) - 1):
        itinerario = Itinerario.query.filter_by(
            ponto_origem_id=pontos_base[i].id,
            ponto_destino_id=pontos_base[i + 1].id
        ).first()
        if itinerario:
            itinerarios.append(itinerario)
    
    return render_template('posto.html', posto=posto, pontos_base=pontos_base, itinerarios=itinerarios, cartao_programa=cartao_programa, escala=escala)

@app.route('/registrar_presenca/<int:ponto_id>', methods=['POST'])
@login_required
def registrar_presenca(ponto_id):
    # Verificar se já existe um registro de chegada
    registro = RegistroPresenca.query.filter_by(
        usuario_id=current_user.id,
        ponto_id=ponto_id
    ).filter(
        RegistroPresenca.timestamp_chegada >= datetime.now().date()
    ).first()
    
    if not registro:
        # Novo registro de chegada
        novo_registro = RegistroPresenca(
            usuario_id=current_user.id,
            ponto_id=ponto_id
        )
        db.session.add(novo_registro)
        db.session.commit()
        return jsonify({'status': 'chegada', 'message': 'Presença registrada com sucesso'})
    else:
        # Registrar saída
        registro.timestamp_saida = datetime.utcnow()
        db.session.commit()
        return jsonify({'status': 'saida', 'message': 'Saída registrada com sucesso'})

@app.route('/enviar_aviso', methods=['POST'])
@login_required
def enviar_aviso():
    titulo = request.form.get('titulo')
    mensagem = request.form.get('mensagem')
    
    if titulo and mensagem:
        aviso = Aviso(
            usuario_id=current_user.id,
            titulo=titulo,
            mensagem=mensagem
        )
        db.session.add(aviso)
        db.session.commit()
        return jsonify({'status': 'success', 'message': 'Aviso enviado com sucesso'})
    
    return jsonify({'status': 'error', 'message': 'Dados inválidos'})

@app.route('/botao_panico', methods=['POST'])
@login_required
def botao_panico():
    # Implementar lógica de pânico (notificações, etc.)
    return jsonify({'status': 'success', 'message': 'Alerta de pânico enviado'})

@app.route('/alertas')
@login_required
def alertas():
    alertas = Alerta.query.filter_by(
        vigilante_id=current_user.id,
        atendido=False
    ).order_by(Alerta.timestamp.desc()).all()
    
    return render_template('alertas.html', alertas=alertas)

@app.route('/api/alertas')
@login_required
def api_alertas():
    alertas = Alerta.query.filter_by(
        vigilante_id=current_user.id,
        atendido=False
    ).order_by(Alerta.timestamp.desc()).all()
    
    return jsonify([{
        'id': a.id,
        'morador': Usuario.query.get(a.morador_id).nome,
        'mensagem': a.mensagem,
        'timestamp': a.timestamp.isoformat()
    } for a in alertas])

@app.route('/atender_alerta/<int:alerta_id>', methods=['POST'])
@login_required
def atender_alerta(alerta_id):
    alerta = Alerta.query.get_or_404(alerta_id)
    alerta.atendido = True
    db.session.commit()
    return jsonify({'status': 'success', 'message': 'Alerta atendido'})

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    
    # Configurações de rede para acesso externo
    host = os.getenv('FLASK_HOST', '0.0.0.0')
    port = int(os.getenv('FLASK_PORT', 5000))
    debug = os.getenv('FLASK_DEBUG', 'True').lower() == 'true'
    
    print(f"🚀 RBX-Security iniciando em http://{host}:{port}")
    print(f"📱 Para acessar externamente, use o IP da sua máquina: http://SEU_IP:{port}")
    print(f"🔧 Modo debug: {debug}")
    
    app.run(debug=debug, host=host, port=port)
