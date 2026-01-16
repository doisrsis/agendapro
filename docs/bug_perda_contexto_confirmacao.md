# 🐛 BUG: Perda de Contexto na Confirmação de Agendamento

**Autor:** Rafael Dias - doisr.com.br
**Data:** 16/01/2026
**Severidade:** 🔴 ALTA

---

## 📋 DESCRIÇÃO DO PROBLEMA

O bot perde o contexto da conversa quando o usuário responde à confirmação de agendamento, fazendo com que mensagens subsequentes sejam tratadas como novo início de conversa.

---

## 🔍 ANÁLISE DA CONVERSA

### Fluxo Observado:

```
[11:30] Bot → Confirmação de Agendamento (13:30)
        Estado: confirmando_agendamento
        Dados: {agendamento_id: 143}

[11:58] Usuário → "1" (confirmar)
        ✅ Agendamento confirmado
        ❌ Bot deleta a conversa

[11:58] Usuário → (qualquer mensagem)
        ❌ Bot cria NOVA conversa (estado: menu)
        ❌ Bot responde com lista de serviços
```

---

## 🐛 CAUSA RAIZ

### Arquivo: `application/controllers/Webhook_waha.php`

**Linha 2197:**
```php
private function processar_estado_confirmando_agendamento(...) {
    // ...
    if ($opcao == '1' || $opcao == 'sim' || $opcao == 'confirmar') {
        $this->Agendamento_model->update($agendamento_id, [
            'status' => 'confirmado',
            'confirmado_em' => date('Y-m-d H:i:s')
        ]);

        $this->waha_lib->enviar_texto($numero, "✅ Agendamento Confirmado!...");

        // ❌ PROBLEMA: Deleta a conversa ao invés de resetar
        $this->Bot_conversa_model->limpar($numero, $estabelecimento->id);
        return;
    }
}
```

### Arquivo: `application/models/Bot_conversa_model.php`

**Linha 239-243:**
```php
public function limpar($numero, $estabelecimento_id) {
    return $this->db
        ->where('numero_whatsapp', $numero)
        ->where('estabelecimento_id', $estabelecimento_id)
        ->delete($this->table);  // ← DELETA a conversa completamente
}
```

**Problema:**
- O método `limpar()` **DELETA** a conversa do banco de dados
- Quando usuário envia nova mensagem, bot cria **NOVA** conversa
- Nova conversa inicia em estado "menu"
- Bot responde com menu de serviços (perde contexto)

---

## ✅ SOLUÇÃO

### Opção 1: Resetar ao invés de Deletar (RECOMENDADA)

Trocar `limpar()` por `resetar()` para manter a conversa mas resetar o estado:

**Arquivo:** `application/controllers/Webhook_waha.php`

**Linhas a modificar:**
- Linha 2197 (confirmando_agendamento)
- Linha 2174 (erro ao processar)
- Linha 2211 (agendamento não encontrado)
- Linha 2243 (reagendamento indisponível)
- Todas as outras ocorrências de `limpar()`

**Trocar:**
```php
$this->Bot_conversa_model->limpar($numero, $estabelecimento->id);
```

**Por:**
```php
$this->Bot_conversa_model->resetar($conversa->id);
```

### Opção 2: Modificar método limpar() (ALTERNATIVA)

Renomear `limpar()` para `deletar()` e criar novo método `limpar()` que reseta:

**Arquivo:** `application/models/Bot_conversa_model.php`

```php
/**
 * Limpa a conversa (reseta para menu)
 */
public function limpar($numero, $estabelecimento_id) {
    $conversa = $this->db
        ->where('numero_whatsapp', $numero)
        ->where('estabelecimento_id', $estabelecimento_id)
        ->get($this->table)
        ->row();

    if ($conversa) {
        return $this->resetar($conversa->id);
    }
    return false;
}

/**
 * Deleta a conversa completamente
 */
public function deletar($numero, $estabelecimento_id) {
    return $this->db
        ->where('numero_whatsapp', $numero)
        ->where('estabelecimento_id', $estabelecimento_id)
        ->delete($this->table);
}
```

---

## 🎯 IMPACTO

**Afeta:**
- ✅ Confirmações de agendamento
- ✅ Reagendamentos
- ✅ Cancelamentos
- ✅ Qualquer fluxo que use `limpar()`

**Sintomas:**
- Bot responde com menu de serviços após confirmar
- Usuário não consegue continuar conversa
- Contexto perdido entre mensagens

---

## 🧪 TESTE

### Cenário de Teste:

1. Criar agendamento para daqui 2 horas
2. Aguardar cron enviar confirmação
3. Responder "1" para confirmar
4. ✅ Bot deve confirmar e resetar para menu
5. Enviar "menu" ou "oi"
6. ✅ Bot deve responder com menu principal (não lista de serviços)

### Resultado Esperado:

```
[11:30] Bot → Confirmação de Agendamento
[11:58] Usuário → "1"
[11:58] Bot → "✅ Agendamento Confirmado!"
        Estado: menu (resetado, não deletado)
[11:59] Usuário → "oi"
[11:59] Bot → Menu Principal (correto)
```

---

## 📝 ARQUIVOS AFETADOS

1. **`application/controllers/Webhook_waha.php`**
   - Linha 2197: `processar_estado_confirmando_agendamento()`
   - Linha 2174: Erro ao processar confirmação
   - Linha 2211: Agendamento não encontrado
   - Linha 2243: Reagendamento indisponível
   - Outras ocorrências de `limpar()`

2. **`application/models/Bot_conversa_model.php`**
   - Linha 239-243: Método `limpar()`

---

## 🔧 CORREÇÃO RECOMENDADA

**Usar `resetar()` ao invés de `limpar()`:**

```php
// ❌ ANTES (deleta conversa)
$this->Bot_conversa_model->limpar($numero, $estabelecimento->id);

// ✅ DEPOIS (reseta para menu)
$this->Bot_conversa_model->resetar($conversa->id);
```

**Vantagens:**
- ✅ Mantém histórico da conversa
- ✅ Preserva `cliente_id` associado
- ✅ Preserva `pushName` do usuário
- ✅ Evita criar múltiplas conversas
- ✅ Melhor para auditoria e logs

---

## 📊 OCORRÊNCIAS DE `limpar()`

Buscar todas as ocorrências no código:

```bash
grep -rn "Bot_conversa_model->limpar" application/controllers/
```

**Substituir todas por:**
```php
$this->Bot_conversa_model->resetar($conversa->id);
```

---

## ⚠️ ATENÇÃO

O método `limpar()` está sendo usado em vários lugares:
- Confirmação de agendamento
- Reagendamento
- Cancelamento
- Tratamento de erros

**Todos devem ser revisados e corrigidos.**

---

## 🚀 PRÓXIMOS PASSOS

1. ✅ Identificar todas as ocorrências de `limpar()`
2. ✅ Substituir por `resetar($conversa->id)`
3. ✅ Testar fluxo completo de confirmação
4. ✅ Testar fluxo de reagendamento
5. ✅ Testar fluxo de cancelamento
6. ✅ Verificar logs para confirmar correção

---

**Status:** 🔴 Aguardando Correção
**Prioridade:** ALTA
**Estimativa:** 30 minutos
