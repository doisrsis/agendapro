# Correção: Preservação do numero_real no Bot de Conversação
**Autor:** Rafael Dias - doisr.com.br
**Data:** 18/01/2026

---

## 🐛 PROBLEMA IDENTIFICADO

### **Sintoma:**
Novos clientes criados via bot com números `@lid` não tinham o campo `telefone` preenchido no banco de dados, mesmo com o `numero_real` sendo extraído corretamente do webhook.

### **Exemplo:**
```sql
-- Cliente Railda Oliveira (ID 15)
id: 15
nome: Railda Oliveira
whatsapp: 108259113467972@lid
telefone: NULL  ❌ (deveria ser 557599935560)
```

### **Causa Raiz:**
O método `Bot_conversa_model->atualizar_estado()` **não preservava** o campo `numero_real` ao atualizar os dados temporários da conversa durante o fluxo do bot.

**Fluxo problemático:**
```
1. Primeira mensagem (08:26:05)
   └─> Webhook extrai numero_real: 557599935560 ✅
   └─> Armazena em dados_conversa['numero_real'] ✅

2. Bot processa etapas (serviço, profissional, data, hora)
   └─> Cada etapa chama atualizar_estado()
   └─> atualizar_estado() preservava apenas pushName
   └─> numero_real era PERDIDO ❌

3. Finalizar agendamento (08:26:48)
   └─> $dados não contém numero_real
   └─> Cliente criado sem telefone ❌
```

---

## ✅ SOLUÇÃO IMPLEMENTADA

### **Arquivo Corrigido:**
`application/models/Bot_conversa_model.php`

### **Método:** `atualizar_estado()`

**ANTES:**
```php
if ($dados !== null) {
    // Preservar pushName se existir nos dados atuais
    $conversa = $this->db->where('id', $conversa_id)->get($this->table)->row();
    if ($conversa && $conversa->dados_temporarios) {
        $dados_atuais = json_decode($conversa->dados_temporarios, true) ?: [];
        // Se pushName existe nos dados atuais e não está nos novos dados, preservar
        if (isset($dados_atuais['pushName']) && !isset($dados['pushName'])) {
            $dados['pushName'] = $dados_atuais['pushName'];
        }
    }

    $update['dados_temporarios'] = json_encode($dados);
}
```

**DEPOIS:**
```php
if ($dados !== null) {
    // Preservar pushName e numero_real se existirem nos dados atuais
    $conversa = $this->db->where('id', $conversa_id)->get($this->table)->row();
    if ($conversa && $conversa->dados_temporarios) {
        $dados_atuais = json_decode($conversa->dados_temporarios, true) ?: [];

        // Se pushName existe nos dados atuais e não está nos novos dados, preservar
        if (isset($dados_atuais['pushName']) && !isset($dados['pushName'])) {
            $dados['pushName'] = $dados_atuais['pushName'];
        }

        // Se numero_real existe nos dados atuais e não está nos novos dados, preservar
        if (isset($dados_atuais['numero_real']) && !isset($dados['numero_real'])) {
            $dados['numero_real'] = $dados_atuais['numero_real'];
        }
    }

    $update['dados_temporarios'] = json_encode($dados);
}
```

---

## 🔍 ANÁLISE DETALHADA

### **Chamadas Críticas de atualizar_estado():**

O sistema tem **4 chamadas** que passam arrays vazios `[]` e poderiam perder o `numero_real`:

1. **Linha 490:** `atualizar_estado($conversa->id, 'confirmando_saida', [])`
2. **Linha 940:** `atualizar_estado($conversa->id, 'aguardando_servico', [])`
3. **Linha 982:** `atualizar_estado($conversa->id, 'aguardando_cancelamento', [])`
4. **Linha 1569:** `atualizar_estado($conversa->id, 'gerenciando_agendamento', [])`

**Com a correção:** Todas essas chamadas agora **preservam automaticamente** o `numero_real` dos dados anteriores.

### **Outras Chamadas:**

Há **20+ chamadas** de `atualizar_estado()` que passam `$dados` com campos específicos (serviço, profissional, data, hora, etc.). Todas agora preservam `numero_real` automaticamente se ele não estiver presente nos novos dados.

---

## 🎯 FLUXO CORRETO APÓS CORREÇÃO

```
1. Primeira mensagem
   └─> Webhook extrai numero_real: 557599935560 ✅
   └─> Armazena em dados_temporarios: {"numero_real": "557599935560"} ✅

2. Cliente escolhe serviço
   └─> atualizar_estado('aguardando_profissional', {'servico_id': 2})
   └─> PRESERVA numero_real ✅
   └─> Dados: {"numero_real": "557599935560", "servico_id": 2}

3. Cliente escolhe profissional
   └─> atualizar_estado('aguardando_data', {'profissional_id': 1})
   └─> PRESERVA numero_real ✅
   └─> Dados: {"numero_real": "557599935560", "servico_id": 2, "profissional_id": 1}

4. Cliente escolhe data
   └─> atualizar_estado('aguardando_hora', {'data': '2026-01-18'})
   └─> PRESERVA numero_real ✅
   └─> Dados: {"numero_real": "557599935560", ..., "data": "2026-01-18"}

5. Cliente escolhe hora
   └─> atualizar_estado('confirmando', {'hora': '11:30'})
   └─> PRESERVA numero_real ✅
   └─> Dados: {"numero_real": "557599935560", ..., "hora": "11:30"}

6. Cliente confirma
   └─> finalizar_agendamento() recebe $dados completo
   └─> numero_real está presente ✅
   └─> Cliente criado com telefone: 557599935560 ✅
```

---

## 📊 IMPACTO DA CORREÇÃO

### **Funcionalidades Corrigidas:**
- ✅ Novos clientes com `@lid` têm telefone real salvo
- ✅ Novos clientes com `@c.us` continuam funcionando
- ✅ Botões WhatsApp na view funcionam desde o primeiro agendamento
- ✅ Campo telefone sempre preenchido quando disponível

### **Funcionalidades Não Afetadas:**
- ✅ Clientes existentes (atualização já implementada)
- ✅ Fluxo de confirmação de agendamento
- ✅ Fluxo de reagendamento
- ✅ Fluxo de cancelamento

---

## 🔧 CORREÇÃO DE DADOS EXISTENTES

### **Cliente Railda Oliveira (ID 15):**

Execute o script SQL:
```sql
UPDATE `clientes`
SET `telefone` = '557599935560'
WHERE `id` = 15
  AND `whatsapp` = '108259113467972@lid';
```

Ou execute o arquivo:
```bash
SOURCE docs/corrigir_telefone_railda_id15.sql;
```

---

## 🧪 TESTES NECESSÁRIOS

### **Cenário 1: Novo Cliente @lid**
```
✅ Cliente envia primeira mensagem
✅ Bot extrai numero_real do SenderAlt
✅ Cliente completa agendamento
✅ Cliente criado com telefone preenchido
✅ View exibe telefone formatado + botões WhatsApp
```

### **Cenário 2: Novo Cliente @c.us**
```
✅ Cliente envia primeira mensagem
✅ Bot extrai numero_real do from
✅ Cliente completa agendamento
✅ Cliente criado com telefone preenchido
✅ View exibe telefone formatado + botões WhatsApp
```

### **Cenário 3: Cliente Existente @lid sem Telefone**
```
✅ Cliente envia mensagem
✅ Bot extrai numero_real
✅ Cliente completa agendamento
✅ Telefone atualizado no banco (lógica já implementada)
✅ View exibe telefone formatado + botões WhatsApp
```

---

## 📝 CHECKLIST DE VERIFICAÇÃO

Ao adicionar novos estados ou fluxos no bot:

- [ ] Verificar se `atualizar_estado()` é chamado
- [ ] Confirmar que campos críticos são preservados automaticamente
- [ ] Testar com números `@lid` E `@c.us`
- [ ] Verificar logs para confirmar `numero_real` presente
- [ ] Testar criação de novo cliente
- [ ] Verificar campo telefone no banco

---

## 🔄 CAMPOS PRESERVADOS AUTOMATICAMENTE

O método `atualizar_estado()` agora preserva automaticamente:

1. **`pushName`** - Nome do cliente do WhatsApp
2. **`numero_real`** - Telefone real extraído do SenderAlt

**Motivo:** Esses campos são definidos uma vez (na primeira mensagem) e devem persistir durante todo o fluxo da conversa, independente das etapas do bot.

---

## 📚 ARQUIVOS RELACIONADOS

### **Modificados:**
- `application/models/Bot_conversa_model.php` - Preservação de numero_real

### **Relacionados (não modificados):**
- `application/controllers/Webhook_waha.php` - Extração de numero_real
- `application/controllers/Cron.php` - Uso de formato completo
- `application/views/admin/clientes/visualizar.php` - Exibição de telefone

### **Documentos Relacionados:**
- `docs/correcao_formato_numero_whatsapp_bot.md` - Formato de números
- `docs/melhoria_campo_telefone_clientes.md` - Campo telefone separado

---

## ✅ CONCLUSÃO

A correção garante que o campo `numero_real` seja preservado durante todo o fluxo de conversação do bot, permitindo que novos clientes com números `@lid` tenham seus telefones reais salvos corretamente no banco de dados.

**Princípio fundamental:** Campos definidos uma vez (como `pushName` e `numero_real`) devem ser **preservados automaticamente** em todas as atualizações de estado, sem necessidade de passá-los explicitamente em cada chamada.

**Status:** ✅ Implementado e pronto para teste
**Prioridade:** 🔴 Crítica (afeta cadastro de novos clientes)
**Complexidade:** 🟢 Baixa (1 método corrigido)
