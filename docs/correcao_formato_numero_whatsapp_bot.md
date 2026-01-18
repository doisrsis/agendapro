# Correção: Formato de Número WhatsApp no Bot
**Autor:** Rafael Dias - doisr.com.br
**Data:** 18/01/2026

---

## 🐛 PROBLEMA IDENTIFICADO

### **Sintoma:**
Bot perdia contexto de confirmação quando cliente respondia. Cliente recebia mensagem de confirmação, respondia "1" para confirmar, mas bot iniciava novo fluxo de agendamento ao invés de processar a confirmação.

### **Causa Raiz:**
Inconsistência no formato de números WhatsApp entre diferentes partes do sistema:

**Fluxo problemático:**
```
1. Cron envia confirmação
   └─> Remove sufixos: 557588890006@c.us → 557588890006
   └─> Salva conversa com: 557588890006

2. Cliente responde "1"
   └─> Webhook recebe de: 557588890006@c.us
   └─> Busca conversa com: 557588890006@c.us
   └─> ❌ NÃO ENCONTRA conversa do cron
   └─> Pega conversa antiga (835 minutos de inatividade)
   └─> Sessão expira → reseta para menu
```

**Resultado:** Bot perde contexto e inicia novo agendamento ❌

---

## ✅ SOLUÇÃO IMPLEMENTADA

### **Princípio:**
**Sempre preservar o formato completo do WhatsApp** (`@c.us` ou `@lid`) em todas as interações que envolvem o bot de conversação.

### **Arquivos Corrigidos:**

#### **1. Cron.php - Confirmações**
```php
// ❌ ANTES (linha 614)
$numero = preg_replace('/[^0-9]/', '', $agendamento->cliente_whatsapp);

// ✅ DEPOIS
$numero = $agendamento->cliente_whatsapp;
```

#### **2. Cron.php - Lembretes**
```php
// ❌ ANTES (linha 705)
$numero = preg_replace('/[^0-9]/', '', $agendamento->cliente_whatsapp);

// ✅ DEPOIS
$numero = $agendamento->cliente_whatsapp;
```

#### **3. Cron.php - Cancelamentos Automáticos**
```php
// ❌ ANTES (linha 761)
$numero = preg_replace('/[^0-9]/', '', $agendamento->cliente_whatsapp);

// ✅ DEPOIS
$numero = $agendamento->cliente_whatsapp;
```

---

## 🔍 ANÁLISE COMPLETA DO FLUXO

### **Componentes Verificados:**

| Componente | Status | Observação |
|------------|--------|------------|
| `Webhook_waha.php` | ✅ OK | Já usa `$numero_completo` preservando formato |
| `Cron.php` | ✅ CORRIGIDO | Agora preserva formato em todos os métodos |
| `Bot_conversa_model.php` | ✅ OK | Busca por número completo |
| `Waha_lib.php` | ✅ OK | `formatar_chat_id()` trata ambos formatos |
| `Notificacao_whatsapp_lib.php` | ✅ OK | Não interage com bot, apenas envia |

### **Fluxo Correto Após Correção:**

```
1. Cron envia confirmação
   └─> Usa formato completo: 557588890006@c.us
   └─> Salva conversa com: 557588890006@c.us
   └─> Estado: confirmando_agendamento

2. Cliente responde "1"
   └─> Webhook recebe de: 557588890006@c.us
   └─> Busca conversa com: 557588890006@c.us
   └─> ✅ ENCONTRA conversa do cron
   └─> Estado: confirmando_agendamento (preservado)
   └─> Processa confirmação corretamente
```

---

## 📊 FORMATOS DE NÚMERO WHATSAPP

### **Tipos de Formato:**

1. **@c.us** - Números antigos/tradicionais
   - Exemplo: `557588890006@c.us`
   - Formato: `[código_país][ddd][número]@c.us`

2. **@lid** - Números novos (LID = Local ID)
   - Exemplo: `108259113467972@lid`
   - Formato: `[id_interno]@lid`
   - Número real em: `SenderAlt` do webhook

3. **@s.whatsapp.net** - Formato alternativo
   - Convertido para `@c.us` pelo sistema

### **Onde Cada Formato é Usado:**

| Local | Formato | Motivo |
|-------|---------|--------|
| Banco `clientes.whatsapp` | `@c.us` ou `@lid` | Formato original recebido |
| Banco `bot_conversas.numero_whatsapp` | `@c.us` ou `@lid` | Chave de busca da conversa |
| Webhook payload `from` | `@c.us` ou `@lid` | Formato nativo da API |
| WAHA API `chatId` | `@c.us` ou `@lid` | Formato aceito pela API |

---

## 🎯 REGRAS DE IMPLEMENTAÇÃO

### **✅ FAZER:**

1. **Preservar formato completo** ao criar/buscar conversas do bot
2. **Usar `$numero_completo`** do webhook (não `$numero` limpo)
3. **Passar formato completo** para `Bot_conversa_model->get_ou_criar()`
4. **Manter sufixos** em todas as operações de bot

### **❌ NÃO FAZER:**

1. **Remover sufixos** antes de criar/buscar conversas
2. **Usar `preg_replace('/[^0-9]/', '', $numero)`** em fluxos de bot
3. **Assumir que número é sempre numérico** - pode ter `@lid`
4. **Misturar formatos** entre criação e busca de conversa

### **⚠️ EXCEÇÕES:**

- `Notificacao_whatsapp_lib.php` pode limpar números porque:
  - Não interage com bot de conversação
  - Apenas envia mensagens unidirecionais
  - `Waha_lib->formatar_chat_id()` adiciona sufixo de volta

---

## 🧪 TESTES REALIZADOS

### **Cenário 1: Confirmação de Agendamento**
```
✅ Cron envia confirmação → Cliente responde "1" → Bot confirma
❌ ANTES: Bot iniciava novo agendamento
✅ DEPOIS: Bot confirma corretamente
```

### **Cenário 2: Reagendamento**
```
✅ Cron envia confirmação → Cliente responde "2" → Bot oferece reagendamento
❌ ANTES: Bot iniciava novo agendamento
✅ DEPOIS: Bot oferece opções de reagendamento
```

### **Cenário 3: Cancelamento**
```
✅ Cron envia confirmação → Cliente responde "3" → Bot cancela
❌ ANTES: Bot iniciava novo agendamento
✅ DEPOIS: Bot cancela corretamente
```

### **Cenário 4: Cliente com @lid**
```
✅ Cliente @lid recebe lembrete → Responde → Bot mantém contexto
✅ Número real extraído do SenderAlt
✅ Conversa encontrada corretamente
```

---

## 📝 CHECKLIST DE VERIFICAÇÃO

Ao adicionar novos recursos que envolvem bot:

- [ ] Número está sendo preservado com sufixo `@c.us` ou `@lid`?
- [ ] `Bot_conversa_model->get_ou_criar()` recebe formato completo?
- [ ] Não há `preg_replace` removendo sufixos antes de buscar conversa?
- [ ] Logs mostram formato completo do número?
- [ ] Testado com números `@c.us` E `@lid`?

---

## 🔄 IMPACTO NAS FUNCIONALIDADES

### **Funcionalidades Corrigidas:**
- ✅ Confirmação de agendamento via bot
- ✅ Reagendamento via bot
- ✅ Cancelamento via bot
- ✅ Lembretes com resposta do cliente
- ✅ Notificações de cancelamento automático

### **Funcionalidades Não Afetadas:**
- ✅ Envio de notificações unidirecionais
- ✅ Novo agendamento via bot
- ✅ Menu principal do bot
- ✅ Consulta de agendamentos

---

## 📚 REFERÊNCIAS

### **Arquivos Relacionados:**
- `application/controllers/Cron.php` - Envio de confirmações/lembretes
- `application/controllers/Webhook_waha.php` - Processamento de mensagens
- `application/models/Bot_conversa_model.php` - Gerenciamento de conversas
- `application/libraries/Waha_lib.php` - Integração com WAHA API
- `application/libraries/Notificacao_whatsapp_lib.php` - Notificações

### **Documentos Relacionados:**
- `docs/melhoria_campo_telefone_clientes.md` - Campo telefone separado
- `docs/bug_cancelamento_apos_reagendamento.md` - Outro bug corrigido

---

## ✅ CONCLUSÃO

A correção garante que o bot mantenha contexto de conversação em todos os fluxos que dependem de resposta do cliente. O princípio fundamental é **sempre preservar o formato completo do número WhatsApp** (`@c.us` ou `@lid`) em todas as operações relacionadas ao bot de conversação.

**Status:** ✅ Implementado e testado
**Prioridade:** 🔴 Crítica (afetava confirmações de agendamento)
**Complexidade:** 🟡 Média (3 pontos de correção)
