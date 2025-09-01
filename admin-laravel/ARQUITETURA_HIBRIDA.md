# 🏗️ Arquitetura Híbrida - Sistema de Condomínio

## 📋 **Estratégia Implementada: Separação de Responsabilidades**

### 🎯 **Conceito Principal**

```
🔐 AUTENTICAÇÃO (usuario) + 📊 DADOS ESPECÍFICOS (moradores, veículos)
```

A estratégia híbrida separa claramente:
- **Autenticação e controle de acesso** → Tabela `usuario` 
- **Dados específicos de domínio** → Tabelas especializadas (`moradores`, `veiculos`)

## 🏗️ **Estrutura das Tabelas**

### 📌 **Tabela `usuario` (Autenticação Centralizada)**
```sql
usuario:
├── id (PK)
├── nome
├── email (unique)
├── senha_hash
├── tipo (admin|vigilante|morador)
├── telefone
├── ativo
├── data_criacao
└── data_atualizacao
```

### 📌 **Tabela `moradores` (Dados Específicos)**
```sql
moradores:
├── id (PK)
├── usuario_id (FK → usuario.id)
├── nome
├── email
├── telefone
├── endereco
├── apartamento
├── bloco
├── cpf
├── password (legacy)
└── ativo
```

### 📌 **Tabela `veiculos` (Relacionamento Específico)**
```sql
veiculos:
├── id (PK)
├── morador_id (FK → moradores.id)
├── marca
├── modelo
├── placa
├── cor
├── created_at
└── updated_at
```

## 🔗 **Relacionamentos Implementados**

### **Usuario ↔ Morador (1:1)**
```php
// Usuario.php
public function dadosMorador()
{
    return $this->hasOne(Morador::class, 'usuario_id')
                ->where('ativo', true);
}

// Morador.php
public function usuario()
{
    return $this->belongsTo(Usuario::class, 'usuario_id');
}
```

### **Morador ↔ Veículos (1:N)**
```php
// Morador.php
public function veiculos()
{
    return $this->hasMany(Veiculo::class);
}

// Veiculo.php
public function morador()
{
    return $this->belongsTo(Morador::class);
}
```

## 🚀 **Exemplos Práticos de Uso**

### **1. Autenticação (Simples e Unificada)**
```php
// Login centralizado
$usuario = Usuario::where('email', $email)
                 ->where('tipo', 'morador')
                 ->where('ativo', true)
                 ->first();

if ($usuario && Hash::check($password, $usuario->senha_hash)) {
    Auth::guard('morador')->login($usuario);
}
```

### **2. Dados Específicos de Morador**
```php
// Buscar morador com seus dados específicos
$usuario = Auth::user(); // Usuario autenticado

// Acessar dados específicos do morador
$dadosMorador = $usuario->dadosMorador;
$endereco = $dadosMorador->endereco;
$apartamento = $dadosMorador->apartamento;
$veiculos = $dadosMorador->veiculos;

// Ou usar helper method
$enderecoCompleto = $usuario->endereco_completo;
// "Rua X, Apt 101, Bloco A"
```

### **3. Lista de Veículos de um Morador**
```php
// Método 1: Via relacionamento direto
$usuario = Usuario::with(['dadosMorador.veiculos'])->find($id);
$veiculos = $usuario->dadosMorador->veiculos;

// Método 2: Via método helper
$veiculos = $usuario->veiculos();

// Método 3: Query direta quando necessário
$veiculos = Veiculo::whereHas('morador.usuario', function($query) use ($userId) {
    $query->where('id', $userId);
})->get();
```

### **4. Dashboard do Morador**
```php
public function dashboard()
{
    $usuario = Auth::user();
    
    if (!$usuario->isMorador()) {
        abort(403, 'Acesso restrito a moradores');
    }
    
    $dadosMorador = $usuario->dadosMorador;
    $veiculos = $dadosMorador->veiculos()->get();
    $alertasAtivos = $usuario->alertas()->where('ativo', true)->count();
    
    return view('dashboard', compact('usuario', 'dadosMorador', 'veiculos', 'alertasAtivos'));
}
```

### **5. Relatório Administrativo**
```php
// Listar todos os moradores com suas informações
$moradores = Usuario::where('tipo', 'morador')
                   ->where('ativo', true)
                   ->with(['dadosMorador.veiculos'])
                   ->get()
                   ->map(function($usuario) {
                       $dados = $usuario->dadosMorador;
                       return [
                           'nome' => $usuario->nome,
                           'email' => $usuario->email,
                           'apartamento' => $dados->apartamento,
                           'bloco' => $dados->bloco,
                           'telefone' => $dados->telefone,
                           'quantidade_veiculos' => $dados->veiculos->count(),
                           'ultimo_login' => $usuario->data_atualizacao
                       ];
                   });
```

## ✅ **Vantagens da Implementação Atual**

### **1. Separação Clara de Responsabilidades**
- ✅ **Autenticação:** Simples e unificada na tabela `usuario`
- ✅ **Dados específicos:** Organizados em tabelas especializadas
- ✅ **Relacionamentos:** Bem definidos e performáticos

### **2. Flexibilidade e Extensibilidade**
```php
// Fácil de estender para novos tipos
Usuario::create(['tipo' => 'porteiro']); // Novo tipo sem afetar estrutura

// Fácil de adicionar novos dados específicos
Schema::create('porteiros', function($table) {
    $table->id();
    $table->integer('usuario_id');
    $table->string('turno');
    $table->decimal('salario');
});
```

### **3. Performance Otimizada**
```php
// Queries eficientes com Eager Loading
$moradores = Usuario::where('tipo', 'morador')
                   ->with(['dadosMorador.veiculos'])
                   ->get();

// Sem dados desnecessários (vigilantes não carregam dados de morador)
$vigilantes = Usuario::where('tipo', 'vigilante')->get();
```

### **4. Validações Específicas**
```php
// Validações diferentes por contexto
class MoradorRequest extends FormRequest {
    public function rules() {
        return [
            'apartamento' => 'required|string|max:10',
            'bloco' => 'nullable|string|max:5',
            'cpf' => 'required|cpf|unique:moradores,cpf'
        ];
    }
}

class VigilanteRequest extends FormRequest {
    public function rules() {
        return [
            'carteira_trabalho' => 'required|string',
            'experiencia_anos' => 'integer|min:0'
        ];
    }
}
```

## 🔄 **Padrões de Uso Recomendados**

### **1. Controllers**
```php
class MoradorController extends Controller 
{
    public function show($id)
    {
        // Buscar usuário com dados específicos
        $usuario = Usuario::with('dadosMorador.veiculos')->findOrFail($id);
        
        if (!$usuario->isMorador()) {
            abort(404, 'Morador não encontrado');
        }
        
        return view('moradores.show', compact('usuario'));
    }
}
```

### **2. Views**
```blade
{{-- Acessar dados de autenticação --}}
<h1>Bem-vindo, {{ $usuario->nome }}!</h1>
<p>Email: {{ $usuario->email }}</p>

{{-- Acessar dados específicos --}}
@if($usuario->dadosMorador)
    <p>Apartamento: {{ $usuario->dadosMorador->apartamento }}</p>
    <p>Bloco: {{ $usuario->dadosMorador->bloco }}</p>
    <p>Endereço: {{ $usuario->endereco_completo }}</p>
    
    <h3>Veículos ({{ $usuario->dadosMorador->veiculos->count() }})</h3>
    @foreach($usuario->dadosMorador->veiculos as $veiculo)
        <p>{{ $veiculo->marca }} {{ $veiculo->modelo }} - {{ $veiculo->placa }}</p>
    @endforeach
@endif
```

### **3. Policies**
```php
class MoradorPolicy
{
    public function view(Usuario $usuario, Morador $morador)
    {
        // Admin pode ver qualquer morador
        if ($usuario->isAdmin()) {
            return true;
        }
        
        // Morador só pode ver seus próprios dados
        return $usuario->isMorador() && 
               $usuario->dadosMorador && 
               $usuario->dadosMorador->id === $morador->id;
    }
}
```

## 🎯 **Conclusão: Por que essa é a Melhor Estratégia?**

### ✅ **Argumentos Técnicos**
1. **Single Responsibility Principle:** Cada tabela tem uma responsabilidade clara
2. **Performance:** Sem campos nullable desnecessários
3. **Extensibilidade:** Fácil adicionar novos tipos e dados específicos
4. **Manutenibilidade:** Código organizado e relacionamentos claros
5. **Segurança:** Validações específicas por contexto

### ✅ **Argumentos de Negócio**
1. **Escalabilidade:** Sistema cresce sem impactar performance
2. **Flexibilidade:** Novos requisitos são facilmente implementados
3. **Clareza:** Stakeholders entendem facilmente a separação
4. **Auditoria:** Fácil rastrear mudanças em cada contexto

### 🏆 **Resultado Final**
```php
// ✅ Autenticação simples e unificada
Auth::attempt(['email' => $email, 'password' => $password]);

// ✅ Dados específicos organizados
$usuario->dadosMorador->veiculos;

// ✅ Relacionamentos performáticos
Usuario::with('dadosMorador.veiculos')->get();

// ✅ Validações contextuais
$request->validate($this->getMoradorRules());

// ✅ Policies claras
$this->authorize('view', $morador);
```

**A estratégia híbrida oferece o melhor dos dois mundos: simplicidade na autenticação e flexibilidade nos dados específicos! 🚀** 