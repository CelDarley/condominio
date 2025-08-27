# Configuração do Gunicorn para produção
# Execute com: gunicorn -c gunicorn.conf.py app:app

import multiprocessing
import os

# Configurações básicas
bind = "0.0.0.0:8000"
workers = multiprocessing.cpu_count() * 2 + 1
worker_class = "sync"
worker_connections = 1000
max_requests = 1000
max_requests_jitter = 50
timeout = 30
keepalive = 2

# Configurações de logging
accesslog = "logs/gunicorn_access.log"
errorlog = "logs/gunicorn_error.log"
loglevel = "info"
access_log_format = '%(h)s %(l)s %(u)s %(t)s "%(r)s" %(s)s %(b)s "%(f)s" "%(a)s" %(D)s'

# Configurações de segurança
limit_request_line = 4094
limit_request_fields = 100
limit_request_field_size = 8190

# Configurações de processo
preload_app = True
daemon = False
pidfile = "gunicorn.pid"
user = None
group = None
tmp_upload_dir = None

# Configurações de SSL (descomente para HTTPS)
# keyfile = "certs/key.pem"
# certfile = "certs/cert.pem"

# Configurações de worker
worker_tmp_dir = "/dev/shm"
worker_exit_on_app_error = True

# Configurações de reload (desenvolvimento)
reload = False
reload_extra_files = []

# Configurações de stats
statsd_host = None
statsd_prefix = "gunicorn"

# Configurações de hooks
def on_starting(server):
    """Executado quando o servidor inicia"""
    server.log.info("Iniciando servidor SegCond...")

def on_reload(server):
    """Executado quando o servidor é recarregado"""
    server.log.info("Recarregando servidor SegCond...")

def on_exit(server):
    """Executado quando o servidor é encerrado"""
    server.log.info("Encerrando servidor SegCond...")

def worker_int(worker):
    """Executado quando um worker é interrompido"""
    worker.log.info("Worker interrompido: %s", worker.pid)

def pre_fork(server, worker):
    """Executado antes de criar um worker"""
    server.log.info("Criando worker: %s", worker.pid)

def post_fork(server, worker):
    """Executado após criar um worker"""
    server.log.info("Worker criado: %s", worker.pid)

def post_worker_init(worker):
    """Executado após inicializar um worker"""
    worker.log.info("Worker inicializado: %s", worker.pid)

def worker_abort(worker):
    """Executado quando um worker é abortado"""
    worker.log.info("Worker abortado: %s", worker.pid)

# Configurações específicas para SegCond
def when_ready(server):
    """Executado quando o servidor está pronto para receber conexões"""
    server.log.info("SegCond está rodando em http://0.0.0.0:8000")
    server.log.info("Workers ativos: %s", server.cfg.workers)
    server.log.info("Pid: %s", server.pid)

# Configurações de ambiente
raw_env = [
    "FLASK_ENV=production",
    "FLASK_DEBUG=False",
]

# Configurações de proxy (se estiver atrás de Nginx)
forwarded_allow_ips = "*"
secure_scheme_headers = {
    'X-FORWARDED-PROTOCOL': 'ssl',
    'X-FORWARDED-PROTO': 'https',
    'X-FORWARDED-SSL': 'on'
}
