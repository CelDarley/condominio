#!/bin/bash

# Script para configurar rede e firewall para acesso externo ao SegCond

echo "🌐 Configurando rede para acesso externo ao SegCond..."
echo ""

# Verificar se está rodando como root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Este script precisa ser executado como root (sudo)"
    echo "   Execute: sudo ./setup_network.sh"
    exit 1
fi

# Obter IP local
LOCAL_IP=$(hostname -I | awk '{print $1}')
PORT=5000

echo "📊 Informações de Rede:"
echo "   IP Local: $LOCAL_IP"
echo "   Porta: $PORT"
echo ""

# Configurar UFW (Uncomplicated Firewall)
echo "🔥 Configurando firewall UFW..."

# Verificar se UFW está instalado
if ! command -v ufw &> /dev/null; then
    echo "📦 Instalando UFW..."
    apt update
    apt install -y ufw
fi

# Habilitar UFW
ufw --force enable

# Permitir SSH (importante para não perder acesso)
ufw allow ssh

# Permitir porta do SegCond
ufw allow $PORT/tcp

# Permitir acesso HTTP/HTTPS (opcional)
ufw allow 80/tcp
ufw allow 443/tcp

echo "✅ Firewall configurado"
echo ""

# Mostrar status do firewall
echo "📋 Status do Firewall:"
ufw status numbered
echo ""

# Configurar iptables (alternativa)
echo "🔧 Configurando iptables..."

# Permitir conexões na porta do SegCond
iptables -A INPUT -p tcp --dport $PORT -j ACCEPT
iptables -A OUTPUT -p tcp --sport $PORT -j ACCEPT

echo "✅ iptables configurado"
echo ""

# Verificar se a porta está aberta
echo "🔍 Verificando se a porta $PORT está acessível..."
if netstat -tuln | grep ":$PORT " > /dev/null; then
    echo "✅ Porta $PORT está configurada para aceitar conexões"
else
    echo "⚠️  Porta $PORT não está configurada. Execute o SegCond primeiro."
fi

echo ""
echo "🌐 Configuração de Rede Concluída!"
echo ""
echo "📱 Para acessar de um smartphone externo:"
echo "   URL: http://$LOCAL_IP:$PORT"
echo ""
echo "🔒 Configurações de Segurança:"
echo "   - Firewall UFW habilitado"
echo "   - Porta $PORT liberada"
echo "   - Apenas conexões TCP permitidas"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   - Certifique-se de que sua rede permite conexões externas"
echo "   - Se estiver atrás de um roteador, configure port forwarding"
echo "   - Para produção, considere usar HTTPS"
echo ""
echo "🚀 Para executar o SegCond:"
echo "   python3 run.py"
