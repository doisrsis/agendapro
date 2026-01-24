# 🔧 CORREÇÃO: CONTROLE DE BOT POR ESTABELECIMENTO

**Autor:** Rafael Dias - doisr.com.br
**Data:** 24/01/2026 02:03

---

## 🐛 PROBLEMA IDENTIFICADO

O sistema tinha uma verificação global (`waha_ativo`) que **bloqueava TODOS os estabelecimentos** quando desativada no Admin SaaS, mesmo que o estabelecimento tivesse o bot configurado e ativo.

### Comportamento Incorreto:
```
Webhook recebe mensagem
    ↓
Verifica waha_ativo GLOBAL ← ❌ SE DESATIVADO, PARA AQUI
    ↓                           (nunca chegava na verificação por estabelecimento)
Verifica estabelecimento->waha_bot_ativo
    ↓
Processa bot
```

**Resultado:** Admin desativava `waha_ativo` global → **TODOS os bots paravam** ❌

---

## ✅ SOLUÇÃO IMPLEMENTADA

Removida a verificação global. Agora o controle é **100% por estabelecimento**.

### Comportamento Correto:
```
Webhook recebe mensagem
    ↓
Verifica estabelecimento->waha_bot_ativo ← ✅ Controle independente
    ↓
Se ativo: Processa bot
Se desativado: Ignora mensagem
```

**Resultado:** Cada estabelecimento controla seu bot independentemente ✅

---

## 📝 ALTERAÇÕES NO CÓDIGO

### Arquivo: `application/controllers/Webhook_waha.php`

#### Linhas 363-365 (ANTES - linhas 363-368):
```php
// REMOVIDO:
// Verificar se integração WAHA está ativa globalmente (configuração do SaaS Admin)
$waha_ativo = $this->Configuracao_model->get_by_chave('waha_ativo');
if (!$waha_ativo || $waha_ativo->valor != '1') {
    log_message('debug', 'WAHA Webhook: Integração WAHA desativada globalmente - mensagem ignorada');
    return;
}
```

#### Linhas 363-365 (DEPOIS):
```php
// ADICIONADO:
// NOTA: Controle de ativação do bot é feito por estabelecimento via waha_bot_ativo
// Cada estabelecimento tem controle independente do seu bot
// Verificação global removida para permitir controle granular por estabelecimento
```

#### Linhas 406-412 (ANTES - linha 409):
```php
// ANTES:
if ($estabelecimento && $estabelecimento->waha_bot_ativo) {
    $this->processar_bot_agendamento($estabelecimento, $numero_completo, $body, $message_id, $pushName, $numero_real);
}
```

#### Linhas 406-412 (DEPOIS):
```php
// DEPOIS:
if ($estabelecimento && $estabelecimento->waha_bot_ativo) {
    log_message('debug', 'WAHA Webhook: Bot ativo para estabelecimento ' . $estabelecimento_id . ' - processando mensagem');
    $this->processar_bot_agendamento($estabelecimento, $numero_completo, $body, $message_id, $pushName, $numero_real);
} else {
    log_message('debug', 'WAHA Webhook: Bot desativado para estabelecimento ' . $estabelecimento_id . ' - mensagem ignorada');
}
```

---

## 🎯 CAMPOS DE CONTROLE POR ESTABELECIMENTO

Tabela: `estabelecimentos`

```sql
-- Controle de ativação do bot (já existentes):
`waha_ativo` tinyint(1) DEFAULT 0
    COMMENT 'Se WAHA está ativo para este estabelecimento'

`waha_bot_ativo` tinyint(1) DEFAULT 0
    COMMENT 'Se o bot de agendamento está ativo'

`waha_status` enum('desconectado','conectando','conectado','erro') DEFAULT 'desconectado'
    COMMENT 'Status da conexão WAHA'
```

### Como Ativar/Desativar Bot por Estabelecimento:

```sql
-- Ativar bot do estabelecimento
UPDATE estabelecimentos
SET waha_bot_ativo = 1
WHERE id = 4;

-- Desativar bot do estabelecimento
UPDATE estabelecimentos
SET waha_bot_ativo = 0
WHERE id = 4;

-- Verificar status
SELECT id, nome, waha_ativo, waha_bot_ativo, waha_status
FROM estabelecimentos
WHERE id = 4;
```

---

## 📊 IMPACTO DA MUDANÇA

### ✅ Benefícios:
1. **Controle Independente:** Cada estabelecimento controla seu próprio bot
2. **Granularidade:** Admin pode desativar bot de estabelecimento específico
3. **Isolamento:** Desativar um bot não afeta outros estabelecimentos
4. **Logs Detalhados:** Rastreamento de ativação/desativação por estabelecimento

### ⚠️ Observações:
- Configuração global `waha_ativo` ainda existe na tabela `configuracoes`
- Ela não interfere mais no funcionamento dos bots dos estabelecimentos
- Se houver bot do Admin SaaS no futuro, implementar verificação separada

---

## 🧪 TESTES NECESSÁRIOS

### Teste 1: Bot Ativo
```sql
-- Garantir que bot está ativo
UPDATE estabelecimentos SET waha_bot_ativo = 1 WHERE id = 4;
```
1. Enviar "oi" no WhatsApp
2. Bot deve responder com menu principal ✅
3. Verificar log: `Bot ativo para estabelecimento 4 - processando mensagem`

### Teste 2: Bot Desativado
```sql
-- Desativar bot temporariamente
UPDATE estabelecimentos SET waha_bot_ativo = 0 WHERE id = 4;
```
1. Enviar "oi" no WhatsApp
2. Bot NÃO deve responder ✅
3. Verificar log: `Bot desativado para estabelecimento 4 - mensagem ignorada`

### Teste 3: Múltiplos Estabelecimentos
```sql
-- Estabelecimento 4 ativo, outros desativados
UPDATE estabelecimentos SET waha_bot_ativo = 1 WHERE id = 4;
UPDATE estabelecimentos SET waha_bot_ativo = 0 WHERE id != 4;
```
1. Enviar mensagem para estabelecimento 4 → Bot responde ✅
2. Enviar mensagem para outros → Bot não responde ✅

---

## 🚀 PRÓXIMOS PASSOS

1. ✅ Verificação global removida
2. ✅ Logs adicionados
3. ⏳ Testar bot do estabelecimento ID 4
4. ⏳ Verificar PIX Manual sendo gerado corretamente
5. ⏳ Confirmar reativação de conversas encerradas

---

## 📋 CORREÇÕES APLICADAS NESTA SESSÃO

1. ✅ **Reativação automática de conversas encerradas** - `Bot_conversa_model.php`
2. ✅ **Recarregamento de estabelecimento antes de gerar PIX** - `Webhook_waha.php`
3. ✅ **Logs detalhados para debug** - `Webhook_waha.php`
4. ✅ **Controle independente de bot por estabelecimento** - `Webhook_waha.php`

---

## 💡 INTERFACE DE CONTROLE

O painel do estabelecimento já possui interface para controlar o bot:

**Caminho:** Painel → Configurações → WhatsApp

**Campos disponíveis:**
- ☑️ Ativar WAHA (`waha_ativo`)
- ☑️ Ativar Bot de Agendamento (`waha_bot_ativo`)
- 🔄 Status da Conexão (`waha_status`)
- ⏱️ Timeout do Bot (minutos)

---

## 🎉 RESULTADO FINAL

**Antes:**
- Configuração global bloqueava todos os estabelecimentos ❌
- Sem controle granular ❌
- Um problema afetava todos ❌

**Depois:**
- Cada estabelecimento controla seu bot ✅
- Controle granular por estabelecimento ✅
- Isolamento total entre estabelecimentos ✅
- Logs detalhados para diagnóstico ✅
