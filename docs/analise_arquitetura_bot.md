# 🔍 ANÁLISE DA ARQUITETURA ATUAL DO BOT WAHA

**Autor:** Rafael Dias - doisr.com.br
**Data:** 24/01/2026 01:59

---

## 📊 SITUAÇÃO ATUAL

### 1. Configuração Global (SaaS Admin)
**Tabela:** `configuracoes`
**Campo:** `waha_ativo` (chave/valor)

```php
// Webhook_waha.php linha 364-368
$waha_ativo = $this->Configuracao_model->get_by_chave('waha_ativo');
if (!$waha_ativo || $waha_ativo->valor != '1') {
    log_message('debug', 'WAHA Webhook: Integração WAHA desativada globalmente - mensagem ignorada');
    return; // ❌ PARA TUDO - Nenhum estabelecimento funciona
}
```

**Problema:** Esta configuração global está **bloqueando TODOS os estabelecimentos**, mesmo aqueles que têm o bot configurado e ativo.

---

### 2. Configuração por Estabelecimento
**Tabela:** `estabelecimentos`
**Campos existentes:**
- `waha_ativo` (tinyint) - Se WAHA está ativo para o estabelecimento
- `waha_bot_ativo` (tinyint) - Se o bot de agendamento está ativo
- `waha_status` (enum) - Status da conexão (desconectado/conectando/conectado/erro)
- `waha_session_name` (varchar) - Nome da sessão WAHA
- `waha_numero_conectado` (varchar) - Número WhatsApp conectado
- `waha_webhook_url` (varchar) - URL do webhook
- `bot_timeout_minutos` (int) - Timeout da sessão do bot

**Uso atual:**
```php
// Webhook_waha.php linha 378 e 409
if ($estabelecimento && $estabelecimento->waha_bot_ativo) {
    // Processa bot apenas se waha_bot_ativo = 1
}
```

---

## 🎯 PROBLEMA IDENTIFICADO

### Fluxo Atual (INCORRETO):
```
Webhook recebe mensagem
    ↓
Verifica waha_ativo GLOBAL ← ❌ SE DESATIVADO, PARA AQUI
    ↓
Verifica estabelecimento->waha_bot_ativo
    ↓
Processa bot
```

### Propósito Original (CORRETO):
- **`waha_ativo` global:** Deveria controlar apenas o bot do **Admin SaaS** (se houver)
- **`waha_bot_ativo` por estabelecimento:** Deveria controlar o bot de **cada estabelecimento**

---

## ✅ SOLUÇÃO PROPOSTA

### Opção 1: Remover Verificação Global (RECOMENDADA)
**Vantagem:** Simples, rápido, resolve imediatamente
**Desvantagem:** Se houver bot do Admin SaaS, ele sempre estará ativo

```php
// Webhook_waha.php - REMOVER linhas 363-368
// Comentar ou deletar:
// $waha_ativo = $this->Configuracao_model->get_by_chave('waha_ativo');
// if (!$waha_ativo || $waha_ativo->valor != '1') {
//     return;
// }
```

**Controle passa a ser 100% por estabelecimento:**
- `waha_ativo` = 1 → WAHA conectado
- `waha_bot_ativo` = 1 → Bot de agendamento ativo

---

### Opção 2: Separar Bot Admin do Bot Estabelecimento
**Vantagem:** Controle granular, arquitetura correta
**Desvantagem:** Mais complexo, requer refatoração

```php
// Webhook_waha.php - Modificar lógica
if ($estabelecimento_id) {
    // Bot de ESTABELECIMENTO - usa waha_bot_ativo do estabelecimento
    if ($estabelecimento && $estabelecimento->waha_bot_ativo) {
        $this->processar_bot_agendamento(...);
    }
} else {
    // Bot do ADMIN SAAS - usa waha_ativo global
    $waha_ativo = $this->Configuracao_model->get_by_chave('waha_ativo');
    if ($waha_ativo && $waha_ativo->valor == '1') {
        $this->processar_bot_admin(...);
    }
}
```

---

## 📋 CAMPOS EXISTENTES NA TABELA `estabelecimentos`

```sql
-- Campos relacionados ao WAHA/Bot já existentes:
`waha_ativo` tinyint(1) DEFAULT 0
    COMMENT 'Se WAHA está ativo para este estabelecimento'

`waha_bot_ativo` tinyint(1) DEFAULT 0
    COMMENT 'Se o bot de agendamento está ativo'

`waha_status` enum('desconectado','conectando','conectado','erro') DEFAULT 'desconectado'
    COMMENT 'Status da conexão WAHA'

`waha_session_name` varchar(100) DEFAULT NULL
    COMMENT 'Nome da sessão WAHA'

`waha_numero_conectado` varchar(20) DEFAULT NULL
    COMMENT 'Número conectado via WAHA'

`waha_webhook_url` varchar(255) DEFAULT NULL
    COMMENT 'URL do webhook WAHA'

`bot_timeout_minutos` int(11) DEFAULT 30
    COMMENT 'Tempo em minutos para expirar sessão do bot'
```

**✅ JÁ TEMOS TODOS OS CAMPOS NECESSÁRIOS!**

Não precisamos criar novos campos. Apenas ajustar a lógica de verificação.

---

## 🎯 RECOMENDAÇÃO FINAL

### Implementar Opção 1 (Remover verificação global)

**Motivo:**
1. ✅ Já existe `waha_bot_ativo` por estabelecimento
2. ✅ Controle granular já está implementado
3. ✅ Solução rápida e eficaz
4. ✅ Não quebra funcionalidades existentes
5. ✅ Cada estabelecimento controla seu próprio bot

**Código a modificar:**
```php
// application/controllers/Webhook_waha.php
// Linhas 363-368

// ANTES (INCORRETO):
$waha_ativo = $this->Configuracao_model->get_by_chave('waha_ativo');
if (!$waha_ativo || $waha_ativo->valor != '1') {
    log_message('debug', 'WAHA Webhook: Integração WAHA desativada globalmente - mensagem ignorada');
    return;
}

// DEPOIS (CORRETO):
// Remover completamente esta verificação
// O controle é feito por estabelecimento via waha_bot_ativo
```

---

## 📝 IMPACTO DA MUDANÇA

### ✅ Positivo:
- Cada estabelecimento controla seu bot independentemente
- Admin pode desativar bot de um estabelecimento específico
- Não afeta outros estabelecimentos
- Mantém toda a infraestrutura existente

### ⚠️ Atenção:
- Se houver bot do Admin SaaS (não identificado no código), ele sempre estará ativo
- Solução: Implementar Opção 2 se necessário no futuro

---

## 🚀 PRÓXIMOS PASSOS

1. **Aguardar aprovação do usuário**
2. Remover verificação global do `waha_ativo`
3. Testar com estabelecimento ID 4
4. Documentar mudança

---

## 💡 OBSERVAÇÃO IMPORTANTE

O sistema **JÁ ESTÁ PREPARADO** para controle por estabelecimento:
- Interface de configuração existe
- Campos no banco existem
- Lógica de verificação existe (linha 378 e 409)

**O único problema é a verificação global que bloqueia tudo antes de chegar na verificação por estabelecimento.**
