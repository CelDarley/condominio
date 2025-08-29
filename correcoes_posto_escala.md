# 🔧 Correções: Posto de Trabalho e Escalas

## 🗑️ **PROBLEMA 1: Posto de Trabalho não excluía**

### ❌ Problema:
- Método `destroy` não estava funcionando corretamente
- Faltavam validações adequadas 
- Mensagens de erro não eram claras

### ✅ Solução Implementada:

```php
public function destroy(PostoTrabalho $posto)
{
    try {
        // ✅ Verificar pontos base ativos
        $pontosAtivos = $posto->pontosBase()->where('ativo', true)->count();
        if ($pontosAtivos > 0) {
            return redirect()->route('admin.postos.index')
                ->with('error', "Não é possível desativar este posto pois ele possui {$pontosAtivos} ponto(s) base ativo(s).");
        }

        // ✅ Verificar cartões programa ativos  
        $cartoesAtivos = $posto->cartoesPrograma()->where('ativo', true)->count();
        if ($cartoesAtivos > 0) {
            return redirect()->route('admin.postos.index')
                ->with('error', "Não é possível desativar este posto pois ele possui {$cartoesAtivos} cartão(ões) programa ativo(s).");
        }

        // ✅ Verificar escalas ativas
        $escalasAtivas = $posto->escalas()->where('ativo', true)->count();
        if ($escalasAtivas > 0) {
            return redirect()->route('admin.postos.index')
                ->with('error', "Não é possível desativar este posto pois ele possui {$escalasAtivas} escala(s) ativa(s).");
        }

        // ✅ Soft delete com validação
        $resultado = $posto->update(['ativo' => false]);
        if (!$resultado) {
            throw new \Exception('Falha ao atualizar registro');
        }

        return redirect()->route('admin.postos.index')
            ->with('success', 'Posto de trabalho desativado com sucesso!');

    } catch (\Exception $e) {
        \Log::error('Erro ao desativar posto', ['error' => $e->getMessage()]);
        return redirect()->route('admin.postos.index')
            ->with('error', 'Erro ao desativar posto: ' . $e->getMessage());
    }
}
```

---

## 📅 **PROBLEMA 2: Escala limitada a um dia**

### ❌ Problema:
- Interface só permitia selecionar um dia da semana
- Não era possível associar cartões programa diferentes por dia
- Limitação desnecessária na criação de escalas

### ✅ Solução Implementada:

#### **Nova Interface:**
```html
<!-- ✅ Seleção múltipla de dias com cartões específicos -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-info">
            <i class="fas fa-calendar-week"></i> Selecionar Dias da Semana
        </h6>
    </div>
    <div class="card-body">
        @foreach($diasSemana as $numero => $nome)
        <div class="card dia-card">
            <div class="form-check mb-2">
                <input type="checkbox" 
                       class="form-check-input dia-checkbox" 
                       id="dia_{{ $numero }}" 
                       name="dias[{{ $numero }}][ativo]" 
                       onchange="toggleDiaOptions({{ $numero }})">
                <label class="form-check-label fw-bold">{{ $nome }}</label>
            </div>
            
            <div class="dia-options" id="options_{{ $numero }}">
                <label class="form-label small">Cartão Programa:</label>
                <select name="dias[{{ $numero }}][cartao_programa_id]" 
                        id="cartao_{{ $numero }}"
                        class="form-control form-control-sm cartao-select">
                    <option value="">Nenhum cartão específico</option>
                </select>
            </div>
        </div>
        @endforeach
    </div>
</div>
```

#### **Controller Atualizado:**
```php
public function store(Request $request)
{
    $request->validate([
        'usuario_id' => 'required|exists:usuario,id',
        'posto_trabalho_id' => 'required|exists:posto_trabalho,id',
        'dias' => 'required|array|min:1',
        'dias.*.ativo' => 'required',
        'dias.*.cartao_programa_id' => 'nullable|exists:cartao_programas,id'
    ]);

    \DB::beginTransaction();
    try {
        foreach ($dias as $dia_semana => $dadosDia) {
            if (!isset($dadosDia['ativo'])) continue;

            // ✅ Validar cartão programa por posto
            if (!empty($dadosDia['cartao_programa_id'])) {
                $cartao = CartaoPrograma::find($dadosDia['cartao_programa_id']);
                if ($cartao && $cartao->posto_trabalho_id != $posto_trabalho_id) {
                    $errors[] = "Cartão inválido para {$this->getNomeDiaSemana($dia_semana)}";
                    continue;
                }
            }

            // ✅ Verificar conflitos
            $escalaExistente = Escala::where('usuario_id', $usuario_id)
                ->where('dia_semana', $dia_semana)
                ->where('ativo', true)
                ->first();

            if ($escalaExistente) {
                $errors[] = "Já existe escala em {$this->getNomeDiaSemana($dia_semana)}";
                continue;
            }

            // ✅ Criar escala
            Escala::create([
                'usuario_id' => $usuario_id,
                'posto_trabalho_id' => $posto_trabalho_id,
                'cartao_programa_id' => $dadosDia['cartao_programa_id'] ?: null,
                'dia_semana' => $dia_semana,
                'ativo' => true
            ]);

            $escalasCreated++;
        }

        \DB::commit();
        return redirect()->route('admin.escalas.index')
            ->with('success', "Criada(s) {$escalasCreated} escala(s) com sucesso!");
    } catch (\Exception $e) {
        \DB::rollback();
        return back()->withErrors(['error' => 'Erro: ' . $e->getMessage()]);
    }
}
```

---

## 🎯 **Recursos Implementados:**

### ✅ **Posto de Trabalho:**
- **Validação Completa:** Verifica pontos base, cartões programa e escalas ativas
- **Soft Delete:** Desativação segura preservando dados
- **Mensagens Claras:** Erros específicos sobre o que impede a exclusão
- **Logs Detalhados:** Monitoramento completo das operações

### ✅ **Escalas:**
- **Seleção Múltipla:** Vários dias da semana em uma operação
- **Cartões por Dia:** Cartão programa diferente para cada dia
- **Interface Intuitiva:** Cards visuais com seleção por checkbox
- **Validação Robusta:** Verificação de conflitos e relacionamentos
- **Transações Seguras:** Rollback automático em caso de erro

---

## 🚀 **Como Usar:**

### **Postos de Trabalho:**
1. Para excluir: Primeiro desative pontos base, cartões programa e escalas relacionadas
2. Depois clique em "Excluir" - o posto será desativado (soft delete)

### **Escalas:**
1. Selecione profissional e posto de trabalho
2. Marque os dias desejados (múltipla seleção)
3. Para cada dia, escolha um cartão programa específico (opcional)
4. Clique "Salvar Escalas" - todas serão criadas de uma vez

---

**Status:** ✅ **AMBOS PROBLEMAS RESOLVIDOS** 