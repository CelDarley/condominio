from flask import Flask, render_template, request, jsonify, redirect, url_for, flash, session
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, logout_user, login_required, current_user
from werkzeug.security import generate_password_hash, check_password_hash
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv

# Carregar variáveis de ambiente
load_dotenv()

app = Flask(__name__)
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'admin_secret_key_123')
app.config['SQLALCHEMY_DATABASE_URI'] = os.getenv('DATABASE_URL')
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'admin_login'

# Modelos do banco de dados (reutilizando os existentes)
class Usuario(UserMixin, db.Model):
    __tablename__ = 'usuario'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    senha_hash = db.Column(db.String(255), nullable=False)
    tipo = db.Column(db.String(20), nullable=False)  # admin, vigilante, morador
    telefone = db.Column(db.String(20))
    ativo = db.Column(db.Boolean, default=True)
    data_criacao = db.Column(db.DateTime, default=datetime.utcnow)

class PostoTrabalho(db.Model):
    __tablename__ = 'posto_trabalho'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    descricao = db.Column(db.Text)
    endereco = db.Column(db.String(200))
    ativo = db.Column(db.Boolean, default=True)

class PontoBase(db.Model):
    __tablename__ = 'ponto_base'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    endereco = db.Column(db.String(200), nullable=False)
    descricao = db.Column(db.Text)
    instrucoes = db.Column(db.Text)
    ativo = db.Column(db.Boolean, default=True)

class CartaoPrograma(db.Model):
    __tablename__ = 'cartao_programa'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    descricao = db.Column(db.Text)
    ativo = db.Column(db.Boolean, default=True)
    data_criacao = db.Column(db.DateTime, default=datetime.utcnow)

class Itinerario(db.Model):
    __tablename__ = 'itinerario'
    id = db.Column(db.Integer, primary_key=True)
    cartao_programa_id = db.Column(db.Integer, db.ForeignKey('cartao_programa.id'), nullable=False)
    ponto_origem_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    ponto_destino_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    ordem = db.Column(db.Integer, nullable=False)
    tempo_estimado = db.Column(db.Integer)  # em minutos
    instrucoes = db.Column(db.Text)
    
    # Relacionamentos
    cartao_programa = db.relationship('CartaoPrograma', backref='itinerarios')
    ponto_origem = db.relationship('PontoBase', foreign_keys=[ponto_origem_id])
    ponto_destino = db.relationship('PontoBase', foreign_keys=[ponto_destino_id])

class Escala(db.Model):
    __tablename__ = 'escala'
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    posto_trabalho_id = db.Column(db.Integer, db.ForeignKey('posto_trabalho.id'), nullable=False)
    dia_semana = db.Column(db.Integer, nullable=False)  # 0=Segunda, 1=Terça, etc.
    data_inicio = db.Column(db.Date, nullable=False)
    data_fim = db.Column(db.Date)
    ativo = db.Column(db.Boolean, default=True)
    
    # Relacionamentos
    usuario = db.relationship('Usuario', backref='escalas')
    posto_trabalho = db.relationship('PostoTrabalho', backref='escalas')

class HorarioPontoBase(db.Model):
    __tablename__ = 'horario_ponto_base'
    id = db.Column(db.Integer, primary_key=True)
    cartao_programa_id = db.Column(db.Integer, db.ForeignKey('cartao_programa.id'), nullable=False)
    ponto_base_id = db.Column(db.Integer, db.ForeignKey('ponto_base.id'), nullable=False)
    ordem = db.Column(db.Integer, nullable=False)
    horario_inicio = db.Column(db.Time, nullable=False)
    horario_fim = db.Column(db.Time, nullable=False)
    duracao_minutos = db.Column(db.Integer, nullable=False)
    instrucoes = db.Column(db.Text)
    
    # Relacionamentos
    cartao_programa = db.relationship('CartaoPrograma', backref='horarios_pontos')
    ponto_base = db.relationship('PontoBase', backref='horarios')

@login_manager.user_loader
def load_user(user_id):
    return Usuario.query.get(int(user_id))

# Rotas de autenticação
@app.route('/admin/login', methods=['GET', 'POST'])
def admin_login():
    if request.method == 'POST':
        email = request.form.get('email')
        senha = request.form.get('senha')
        
        usuario = Usuario.query.filter_by(email=email, tipo='admin').first()
        if usuario and check_password_hash(usuario.senha_hash, senha):
            login_user(usuario)
            return redirect(url_for('admin_dashboard'))
        else:
            flash('Credenciais inválidas!', 'error')
    
    return render_template('admin/login.html')

@app.route('/admin/logout')
@login_required
def admin_logout():
    logout_user()
    return redirect(url_for('admin_login'))

# Dashboard principal
@app.route('/admin')
@login_required
def admin_dashboard():
    total_usuarios = Usuario.query.filter_by(tipo='vigilante').count()
    total_pontos_base = PontoBase.query.count()
    total_cartoes_programa = CartaoPrograma.query.count()
    total_escalas = Escala.query.filter_by(ativo=True).count()
    
    return render_template('admin/dashboard.html',
                         total_usuarios=total_usuarios,
                         total_pontos_base=total_pontos_base,
                         total_cartoes_programa=total_cartoes_programa,
                         total_escalas=total_escalas)

# Gestão de Pontos Base
@app.route('/admin/pontos-base')
@login_required
def admin_pontos_base():
    pontos = PontoBase.query.filter_by(ativo=True).all()
    return render_template('admin/pontos_base.html', pontos=pontos)

@app.route('/admin/pontos-base/novo', methods=['GET', 'POST'])
@login_required
def admin_novo_ponto_base():
    if request.method == 'POST':
        nome = request.form.get('nome')
        endereco = request.form.get('endereco')
        descricao = request.form.get('descricao')
        instrucoes = request.form.get('instrucoes')
        
        novo_ponto = PontoBase(
            nome=nome,
            endereco=endereco,
            descricao=descricao,
            instrucoes=instrucoes
        )
        
        db.session.add(novo_ponto)
        db.session.commit()
        flash('Ponto base criado com sucesso!', 'success')
        return redirect(url_for('admin_pontos_base'))
    
    return render_template('admin/novo_ponto_base.html')

@app.route('/admin/pontos-base/<int:id>/editar', methods=['GET', 'POST'])
@login_required
def admin_editar_ponto_base(id):
    ponto = PontoBase.query.get_or_404(id)
    
    if request.method == 'POST':
        ponto.nome = request.form.get('nome')
        ponto.endereco = request.form.get('endereco')
        ponto.descricao = request.form.get('descricao')
        ponto.instrucoes = request.form.get('instrucoes')
        
        db.session.commit()
        flash('Ponto base atualizado com sucesso!', 'success')
        return redirect(url_for('admin_pontos_base'))
    
    return render_template('admin/editar_ponto_base.html', ponto=ponto)

# Gestão de Cartões Programa
@app.route('/admin/cartoes-programa')
@login_required
def admin_cartoes_programa():
    cartoes = CartaoPrograma.query.filter_by(ativo=True).all()
    return render_template('admin/cartoes_programa.html', cartoes=cartoes)

@app.route('/admin/cartoes-programa/novo', methods=['GET', 'POST'])
@login_required
def admin_novo_cartao_programa():
    if request.method == 'POST':
        nome = request.form.get('nome')
        descricao = request.form.get('descricao')
        
        novo_cartao = CartaoPrograma(nome=nome, descricao=descricao)
        db.session.add(novo_cartao)
        db.session.commit()
        
        flash('Cartão programa criado com sucesso!', 'success')
        return redirect(url_for('admin_cartoes_programa'))
    
    return render_template('admin/novo_cartao_programa.html')

@app.route('/admin/cartoes-programa/<int:id>/configurar', methods=['GET', 'POST'])
@login_required
def admin_configurar_cartao_programa(id):
    cartao = CartaoPrograma.query.get_or_404(id)
    pontos_base = PontoBase.query.filter_by(ativo=True).all()
    
    if request.method == 'POST':
        # Processar configuração dos pontos base e itinerários
        pontos_config = request.form.getlist('ponto_base_id')
        horarios_inicio = request.form.getlist('horario_inicio')
        horarios_fim = request.form.getlist('horario_fim')
        duracoes = request.form.getlist('duracao')
        instrucoes = request.form.getlist('instrucoes')
        
        # Limpar configurações existentes
        HorarioPontoBase.query.filter_by(cartao_programa_id=id).delete()
        Itinerario.query.filter_by(cartao_programa_id=id).delete()
        
        # Criar novas configurações
        for i, ponto_id in enumerate(pontos_config):
            if ponto_id and horarios_inicio[i] and horarios_fim[i]:
                horario = HorarioPontoBase(
                    cartao_programa_id=id,
                    ponto_base_id=int(ponto_id),
                    ordem=i+1,
                    horario_inicio=datetime.strptime(horarios_inicio[i], '%H:%M').time(),
                    horario_fim=datetime.strptime(horarios_fim[i], '%H:%M').time(),
                    duracao_minutos=int(duracoes[i]) if duracoes[i] else 0,
                    instrucoes=instrucoes[i] if instrucoes[i] else ''
                )
                db.session.add(horario)
        
        # Criar itinerários entre os pontos
        for i in range(len(pontos_config) - 1):
            if pontos_config[i] and pontos_config[i+1]:
                itinerario = Itinerario(
                    cartao_programa_id=id,
                    ponto_origem_id=int(pontos_config[i]),
                    ponto_destino_id=int(pontos_config[i+1]),
                    ordem=i+1
                )
                db.session.add(itinerario)
        
        db.session.commit()
        flash('Cartão programa configurado com sucesso!', 'success')
        return redirect(url_for('admin_cartoes_programa'))
    
    # Buscar configurações existentes
    horarios = HorarioPontoBase.query.filter_by(cartao_programa_id=id).order_by(HorarioPontoBase.ordem).all()
    itinerarios = Itinerario.query.filter_by(cartao_programa_id=id).order_by(Itinerario.ordem).all()
    
    return render_template('admin/configurar_cartao_programa.html', 
                         cartao=cartao, 
                         pontos_base=pontos_base,
                         horarios=horarios,
                         itinerarios=itinerarios)

# Gestão de Usuários (Segurança)
@app.route('/admin/usuarios')
@login_required
def admin_usuarios():
    usuarios = Usuario.query.filter_by(tipo='vigilante').all()
    return render_template('admin/usuarios.html', usuarios=usuarios)

@app.route('/admin/usuarios/novo', methods=['GET', 'POST'])
@login_required
def admin_novo_usuario():
    if request.method == 'POST':
        nome = request.form.get('nome')
        email = request.form.get('email')
        telefone = request.form.get('telefone')
        senha = request.form.get('senha')
        
        if Usuario.query.filter_by(email=email).first():
            flash('Email já cadastrado!', 'error')
            return render_template('admin/novo_usuario.html')
        
        novo_usuario = Usuario(
            nome=nome,
            email=email,
            telefone=telefone,
            senha_hash=generate_password_hash(senha),
            tipo='vigilante'
        )
        
        db.session.add(novo_usuario)
        db.session.commit()
        flash('Usuário criado com sucesso!', 'success')
        return redirect(url_for('admin_usuarios'))
    
    return render_template('admin/novo_usuario.html')

@app.route('/admin/usuarios/<int:id>/editar', methods=['GET', 'POST'])
@login_required
def admin_editar_usuario(id):
    usuario = Usuario.query.get_or_404(id)
    
    if request.method == 'POST':
        usuario.nome = request.form.get('nome')
        usuario.email = request.form.get('email')
        usuario.telefone = request.form.get('telefone')
        
        nova_senha = request.form.get('nova_senha')
        if nova_senha:
            usuario.senha_hash = generate_password_hash(nova_senha)
        
        db.session.commit()
        flash('Usuário atualizado com sucesso!', 'success')
        return redirect(url_for('admin_usuarios'))
    
    return render_template('admin/editar_usuario.html', usuario=usuario)

# Gestão de Escalas
@app.route('/admin/escalas')
@login_required
def admin_escalas():
    escalas = Escala.query.filter_by(ativo=True).all()
    return render_template('admin/escalas.html', escalas=escalas)

@app.route('/admin/escalas/nova', methods=['GET', 'POST'])
@login_required
def admin_nova_escala():
    if request.method == 'POST':
        usuario_id = request.form.get('usuario_id')
        posto_trabalho_id = request.form.get('posto_trabalho_id')
        dia_semana = request.form.get('dia_semana')
        data_inicio = datetime.strptime(request.form.get('data_inicio'), '%Y-%m-%d').date()
        data_fim = request.form.get('data_fim')
        
        nova_escala = Escala(
            usuario_id=int(usuario_id),
            posto_trabalho_id=int(posto_trabalho_id),
            dia_semana=int(dia_semana),
            data_inicio=data_inicio,
            data_fim=datetime.strptime(data_fim, '%Y-%m-%d').date() if data_fim else None
        )
        
        db.session.add(nova_escala)
        db.session.commit()
        flash('Escala criada com sucesso!', 'success')
        return redirect(url_for('admin_escalas'))
    
    usuarios = Usuario.query.filter_by(tipo='vigilante', ativo=True).all()
    postos = PostoTrabalho.query.filter_by(ativo=True).all()
    dias_semana = [
        (0, 'Segunda-feira'),
        (1, 'Terça-feira'),
        (2, 'Quarta-feira'),
        (3, 'Quinta-feira'),
        (4, 'Sexta-feira'),
        (5, 'Sábado'),
        (6, 'Domingo')
    ]
    
    return render_template('admin/nova_escala.html', 
                         usuarios=usuarios, 
                         postos=postos,
                         dias_semana=dias_semana)

@app.route('/admin/escalas/<int:id>/editar', methods=['GET', 'POST'])
@login_required
def admin_editar_escala(id):
    escala = Escala.query.get_or_404(id)
    
    if request.method == 'POST':
        escala.usuario_id = int(request.form.get('usuario_id'))
        escala.posto_trabalho_id = int(request.form.get('posto_trabalho_id'))
        escala.dia_semana = int(request.form.get('dia_semana'))
        escala.data_inicio = datetime.strptime(request.form.get('data_inicio'), '%Y-%m-%d').date()
        
        data_fim = request.form.get('data_fim')
        escala.data_fim = datetime.strptime(data_fim, '%Y-%m-%d').date() if data_fim else None
        
        db.session.commit()
        flash('Escala atualizada com sucesso!', 'success')
        return redirect(url_for('admin_escalas'))
    
    usuarios = Usuario.query.filter_by(tipo='vigilante', ativo=True).all()
    postos = PostoTrabalho.query.filter_by(ativo=True).all()
    dias_semana = [
        (0, 'Segunda-feira'),
        (1, 'Terça-feira'),
        (2, 'Quarta-feira'),
        (3, 'Quinta-feira'),
        (4, 'Sexta-feira'),
        (5, 'Sábado'),
        (6, 'Domingo')
    ]
    
    return render_template('admin/editar_escala.html', 
                         escala=escala,
                         usuarios=usuarios, 
                         postos=postos,
                         dias_semana=dias_semana)

# API para o aplicativo móvel
@app.route('/api/escalas/<int:usuario_id>/<int:dia_semana>')
def api_escalas_usuario(usuario_id, dia_semana):
    escalas = Escala.query.filter_by(
        usuario_id=usuario_id,
        dia_semana=dia_semana,
        ativo=True
    ).all()
    
    resultado = []
    for escala in escalas:
        resultado.append({
            'id': escala.id,
            'posto_trabalho': {
                'id': escala.posto_trabalho.id,
                'nome': escala.posto_trabalho.nome,
                'descricao': escala.posto_trabalho.descricao,
                'endereco': escala.posto_trabalho.endereco
            }
        })
    
    return jsonify(resultado)

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    
    host = os.getenv('ADMIN_HOST', '0.0.0.0')
    port = int(os.getenv('ADMIN_PORT', 5002))
    debug = os.getenv('ADMIN_DEBUG', 'True').lower() == 'true'
    
    print(f"🚀 SegCond Admin iniciando em http://{host}:{port}")
    print(f"📱 Acesse: http://localhost:{port}/admin")
    
    app.run(debug=debug, host=host, port=port)
