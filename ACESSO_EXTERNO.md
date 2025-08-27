# 🌐 Guia de Acesso Externo ao SegCond

Este guia explica como configurar o SegCond para ser acessível de smartphones externos através da sua máquina principal.

## 🚀 Configuração Rápida

### 1. Execute o script de configuração de rede
```bash
sudo ./setup_network.sh
```

### 2. Execute o SegCond
```bash
python3 run.py
```

### 3. Verifique o acesso externo
```bash
python3 check_access.py
```

## 📱 Como Acessar de um Smartphone Externo

### Opção 1: Mesma Rede Wi-Fi
1. **Conecte o smartphone na mesma rede Wi-Fi da sua máquina**
2. **Descubra o IP da sua máquina:**
   ```bash
   hostname -I
   ```
3. **Acesse no smartphone:**
   ```
   http://SEU_IP:5000
   ```
   Exemplo: `http://192.168.1.100:5000`

### Opção 2: Rede Externa (Internet)
1. **Configure port forwarding no roteador:**
   - Porta externa: 5000 (ou outra de sua escolha)
   - Porta interna: 5000
   - IP interno: IP da sua máquina
   - Protocolo: TCP

2. **Descubra seu IP público:**
   ```bash
   curl ifconfig.me
   ```

3. **Acesse no smartphone:**
   ```
   http://SEU_IP_PUBLICO:5000
   ```

## 🔧 Configurações Detalhadas

### Firewall (UFW)
```bash
# Permitir porta do SegCond
sudo ufw allow 5000/tcp

# Verificar status
sudo ufw status
```

### iptables (Alternativa)
```bash
# Permitir conexões na porta 5000
sudo iptables -A INPUT -p tcp --dport 5000 -j ACCEPT
sudo iptables -A OUTPUT -p tcp --sport 5000 -j ACCEPT

# Salvar configurações
sudo iptables-save > /etc/iptables/rules.v4
```

### Configuração de Rede
```bash
# Verificar interfaces de rede
ip addr show

# Verificar roteamento
ip route show

# Verificar se a porta está aberta
netstat -tuln | grep :5000
```

## 🌐 Configuração com Nginx (Recomendado para Produção)

### 1. Instalar Nginx
```bash
sudo apt update
sudo apt install nginx
```

### 2. Configurar proxy reverso
```bash
# Copiar configuração
sudo cp nginx_segcond.conf /etc/nginx/sites-available/segcond

# Criar link simbólico
sudo ln -s /etc/nginx/sites-available/segcond /etc/nginx/sites-enabled/

# Testar configuração
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

### 3. Configurar firewall para Nginx
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

## 📱 Configuração PWA para Acesso Externo

### 1. Verificar manifest.json
```json
{
  "start_url": "/",
  "scope": "/",
  "display": "standalone"
}
```

### 2. Verificar Service Worker
- Certifique-se de que `/sw.js` está acessível
- Verifique se o cache está funcionando

### 3. Testar no Smartphone
- Acesse a URL externa
- Toque em "Adicionar à Tela Inicial"
- Verifique se funciona offline

## 🔒 Segurança para Acesso Externo

### 1. Autenticação Forte
- Use senhas complexas
- Implemente autenticação de dois fatores
- Limite tentativas de login

### 2. Firewall Restritivo
```bash
# Permitir apenas IPs específicos (opcional)
sudo ufw allow from 192.168.1.0/24 to any port 5000
```

### 3. HTTPS (Recomendado)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obter certificado SSL
sudo certbot --nginx -d seu-dominio.com
```

### 4. Monitoramento
```bash
# Ver logs de acesso
sudo tail -f /var/log/nginx/access.log

# Ver logs do Flask
tail -f logs/segcond.log
```

## 🐛 Solução de Problemas

### Problema: "Connection Refused"
**Causa:** Aplicação não está rodando ou porta bloqueada
**Solução:**
```bash
# Verificar se está rodando
ps aux | grep python

# Verificar porta
netstat -tuln | grep :5000

# Verificar firewall
sudo ufw status
```

### Problema: "Timeout"
**Causa:** Firewall ou roteador bloqueando
**Solução:**
```bash
# Verificar firewall
sudo ufw status

# Testar conectividade
telnet SEU_IP 5000
```

### Problema: PWA não instala
**Causa:** HTTPS não configurado ou problemas de cache
**Solução:**
- Use HTTPS em produção
- Limpe cache do navegador
- Verifique manifest.json

### Problema: Acesso lento
**Causa:** Configurações de rede ou servidor
**Solução:**
```bash
# Otimizar Nginx
sudo nano /etc/nginx/nginx.conf

# Verificar recursos do servidor
htop
free -h
```

## 📊 Verificação de Funcionamento

### 1. Teste Local
```bash
curl http://localhost:5000
```

### 2. Teste Externo (mesma rede)
```bash
curl http://SEU_IP:5000
```

### 3. Teste de Smartphone
- Acesse a URL externa
- Teste login
- Teste funcionalidades PWA
- Teste modo offline

## 🚀 Comandos Úteis

### Iniciar SegCond
```bash
python3 run.py
```

### Verificar rede
```bash
python3 check_access.py
```

### Configurar firewall
```bash
sudo ./setup_network.sh
```

### Ver logs
```bash
tail -f logs/segcond.log
```

### Parar aplicação
```bash
# Ctrl+C no terminal onde está rodando
# Ou
pkill -f "python.*app.py"
```

## 📱 Dicas para Smartphone

### Chrome/Edge
1. Acesse a URL externa
2. Toque no ícone de instalação na barra de endereços
3. Toque em "Instalar"

### Safari (iOS)
1. Acesse a URL externa
2. Toque em "Compartilhar"
3. Toque em "Adicionar à Tela Inicial"

### Firefox
1. Acesse a URL externa
2. Toque no menu (3 linhas)
3. Toque em "Instalar App"

## 🔄 Atualizações

### Atualizar aplicação
```bash
git pull origin main
pip install -r requirements.txt
```

### Reiniciar serviços
```bash
# Se usando Nginx
sudo systemctl restart nginx

# Se usando systemd
sudo systemctl restart segcond
```

---

**🎯 Objetivo:** Permitir que vigilantes acessem o SegCond de qualquer lugar através de seus smartphones.

**🔒 Segurança:** Sempre use HTTPS em produção e configure autenticação forte.

**📱 PWA:** Funciona como um aplicativo nativo no smartphone, com suporte offline.
