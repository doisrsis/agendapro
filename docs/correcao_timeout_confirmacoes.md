# ✅ CORREÇÃO DEFINITIVA: Timeout em Confirmações de Agendamento

**Autor:** Rafael Dias - doisr.com.br
**Data:** 16/01/2026
**Status:** ✅ CORRIGIDO

---

## 🐛 PROBLEMA REAL IDENTIFICADO

O bot estava **expirando a sessão** antes do usuário responder à confirmação, causando perda de contexto.

### Timeline do Problema:

```
13:47 → Cliente agenda para 16:00
14:00 → Bot envia confirmação (2h antes)
        Estado: confirmando_agendamento
        Última interação: 14:00

14:30 → Bot envia 2ª tentativa (30min depois)
        Última interação: 14:30 (atualizada pelo cron)

14:54 → Usuário responde "1" (24min depois da 2ª tentativa)
        Diferença: 1h54min desde última interação do USUÁRIO
        Timeout: 30 minutos
        Resultado: SESSÃO EXPIRADA ❌
        Bot reseta para menu
        Bot responde com lista de serviços
```

---

## 🔍 CAUSA RAIZ

### Arquivo: `application/models/Bot_conversa_model.php`

**Linha 60-63 (ANTES):**
```php
if ($diferenca_minutos > $timeout_minutos) {
    // 114 minutos > 30 minutos = TRUE
    log_message('debug', "Bot: Sessão expirada...");
    $this->resetar($conversa->id);  // ← Reseta para menu
}
```

**Problema:**
- Timeout padrão: **30 minutos**
- Confirmações enviadas: **2 horas antes** do agendamento
- Usuário pode demorar para ver a mensagem
- Sessão expira antes da resposta
- Bot perde contexto

---

## ✅ CORREÇÃO APLICADA

### Solução: Desabilitar Timeout para Estados Críticos

Estados críticos são aqueles onde o bot está **aguardando resposta do usuário** para ações importantes:
- `confirmando_agendamento` - Aguardando confirmação
- `confirmando_cancelamento` - Aguardando confirmação de cancelamento
- `aguardando_acao_agendamento` - Aguardando ação (confirmar/reagendar/cancelar)

**Arquivo:** `application/models/Bot_conversa_model.php`

**Linhas 55-67 (DEPOIS):**
```php
// Estados críticos que NÃO devem expirar (aguardando resposta do usuário)
$estados_sem_timeout = [
    'confirmando_agendamento',
    'confirmando_cancelamento',
    'aguardando_acao_agendamento'
];

// Verificar timeout de sessão (exceto para estados críticos)
$ultima_interacao = strtotime($conversa->ultima_interacao);
$agora = time();
$diferenca_minutos = ($agora - $ultima_interacao) / 60;

if ($diferenca_minutos > $timeout_minutos && !in_array($conversa->estado, $estados_sem_timeout)) {
    // Sessão expirada - resetar para menu
    log_message('debug', "Bot: Sessão expirada...");
    $this->resetar($conversa->id);
}
```

**Lógica:**
- ✅ Estados críticos **NUNCA** expiram
- ✅ Usuário pode responder confirmação a qualquer momento
- ✅ Outros estados (menu, seleção de serviço, etc.) expiram normalmente após 30min

---

## 🎯 RESULTADO ESPERADO AGORA

```
14:00 → Bot envia confirmação
        Estado: confirmando_agendamento
        Timeout: DESABILITADO ✅

14:30 → Bot envia 2ª tentativa
        Estado: confirmando_agendamento
        Timeout: DESABILITADO ✅

15:00 → Bot envia 3ª tentativa
        Estado: confirmando_agendamento
        Timeout: DESABILITADO ✅

15:30 → Usuário responde "1" (1h30 depois)
        Estado: confirmando_agendamento
        Sessão: ATIVA ✅
        Bot: Confirma agendamento ✅
        Bot: Reseta para menu
```

---

## 📊 COMPARAÇÃO: ANTES vs DEPOIS

| Aspecto | Antes | Depois |
|---|---|---|
| **Timeout em confirmações** | 30 minutos | ∞ (sem timeout) |
| **Usuário demora 1h** | ❌ Sessão expira | ✅ Funciona |
| **Usuário demora 2h** | ❌ Sessão expira | ✅ Funciona |
| **Timeout em menu** | 30 minutos | 30 minutos |
| **Timeout em seleção** | 30 minutos | 30 minutos |

---

## 🔧 ARQUIVOS MODIFICADOS

### 1. `application/models/Bot_conversa_model.php`

**Mudanças:**
- Linha 55-60: Adicionado array de estados sem timeout
- Linha 67: Adicionado verificação `!in_array($conversa->estado, $estados_sem_timeout)`

### 2. `application/controllers/Webhook_waha.php` (correção anterior)

**Mudanças:**
- 5 ocorrências de `limpar()` substituídas por `resetar()`

---

## 🧪 TESTE RECOMENDADO

### Cenário 1: Resposta Rápida (< 30min)

1. Criar agendamento para daqui 2 horas
2. Aguardar confirmação (2h antes)
3. Responder "1" em 5 minutos
4. ✅ Bot deve confirmar normalmente

### Cenário 2: Resposta Tardia (> 30min)

1. Criar agendamento para daqui 2 horas
2. Aguardar confirmação (2h antes)
3. **Aguardar 1 hora**
4. Responder "1"
5. ✅ Bot deve confirmar normalmente (não expirar)

### Cenário 3: Resposta Muito Tardia (> 2h)

1. Criar agendamento para daqui 3 horas
2. Aguardar confirmação (2h antes)
3. **Aguardar 2 horas**
4. Responder "1"
5. ✅ Bot deve confirmar normalmente (não expirar)

### Cenário 4: Timeout em Menu (deve expirar)

1. Enviar "menu"
2. Bot responde com menu
3. **Aguardar 35 minutos**
4. Enviar "1"
5. ✅ Bot deve resetar e mostrar menu novamente (timeout funcionou)

---

## 📝 ESTADOS COM E SEM TIMEOUT

### ✅ Estados SEM Timeout (Críticos):

- `confirmando_agendamento` - Aguardando confirmação de agendamento
- `confirmando_cancelamento` - Aguardando confirmação de cancelamento
- `aguardando_acao_agendamento` - Aguardando escolha (confirmar/reagendar/cancelar)

**Motivo:** Usuário pode demorar para ver a mensagem, especialmente confirmações enviadas com antecedência.

### ⏱️ Estados COM Timeout (30min):

- `menu` - Menu principal
- `aguardando_servico` - Selecionando serviço
- `aguardando_profissional` - Selecionando profissional
- `aguardando_data` - Selecionando data
- `aguardando_hora` - Selecionando horário
- `confirmando` - Confirmando novo agendamento
- `aguardando_cancelamento` - Selecionando agendamento para cancelar
- `gerenciando_agendamento` - Gerenciando agendamentos
- `reagendando_data` - Selecionando nova data
- `reagendando_hora` - Selecionando novo horário
- `confirmando_reagendamento` - Confirmando reagendamento
- `confirmando_saida` - Confirmando saída

**Motivo:** Fluxos interativos onde o usuário está ativamente navegando. Se demorar muito, é melhor resetar.

---

## 🎯 BENEFÍCIOS DA CORREÇÃO

✅ **Confirmações funcionam sempre** - Usuário pode responder a qualquer momento
✅ **Melhor experiência** - Não perde contexto em ações críticas
✅ **Menos frustração** - Usuário não precisa reiniciar fluxo
✅ **Timeout mantido em fluxos normais** - Evita conversas travadas
✅ **Lógica inteligente** - Diferencia estados críticos de navegação

---

## ⚠️ OBSERVAÇÕES IMPORTANTES

### 1. Limpeza de Conversas Antigas

Conversas em estados críticos **não expiram**, mas são limpas pelo cron após **24 horas** de inatividade:

**Arquivo:** `application/models/Bot_conversa_model.php` (linha 219-223)
```php
// Remover conversas inativas (não encerradas) há mais de 24 horas
$this->db
    ->where('encerrada', 0)
    ->where('ultima_interacao <', date('Y-m-d H:i:s', strtotime('-24 hours')))
    ->delete($this->table);
```

### 2. Cancelamento Automático

Se o usuário não confirmar, o agendamento será **cancelado automaticamente** pelo cron `cancelar_nao_confirmados`, conforme configurado:

```
cancelar_nao_confirmados_horas = 1  (cancela 1h antes do horário)
```

Isso **libera o horário** para outro cliente agendar.

### 3. Timeout Configurável

O timeout para estados normais pode ser ajustado no banco:

```sql
UPDATE estabelecimentos
SET bot_timeout_minutos = 60  -- 1 hora
WHERE id = 4;
```

**Recomendação:** Manter em **30 minutos** para fluxos normais.

---

## 📊 FLUXO COMPLETO CORRIGIDO

```
13:47 → Cliente agenda para 16:00
        Status: pendente

14:00 → Cron envia confirmação (2h antes)
        Estado: confirmando_agendamento
        Timeout: DESABILITADO ✅
        Mensagem: "📅 Confirmação de Agendamento"

14:30 → Cron envia 2ª tentativa (30min depois)
        Estado: confirmando_agendamento
        Timeout: DESABILITADO ✅
        Mensagem: "⚠️ CONFIRMAÇÃO PENDENTE"

15:00 → Cron envia 3ª tentativa (30min depois)
        Estado: confirmando_agendamento
        Timeout: DESABILITADO ✅
        Mensagem: "🚨 ÚLTIMA CHANCE"

15:30 → Usuário responde "1" (1h30 depois da 1ª)
        Estado: confirmando_agendamento
        Sessão: ATIVA ✅
        Bot: Confirma agendamento
        Status: confirmado
        Bot: Reseta para menu
        Mensagem: "✅ Agendamento Confirmado!"

15:50 → Cron envia lembrete (30min antes)
        Mensagem: "🔔 Lembrete de Agendamento"

16:00 → Horário do atendimento
```

---

## 🚀 PRÓXIMOS PASSOS

1. ✅ Código corrigido
2. ✅ Documentação criada
3. ⏳ Testar em produção
4. ⏳ Monitorar logs
5. ⏳ Validar com usuários reais

---

## 📞 SUPORTE

**Dúvidas ou problemas?**
- Email: rafaeldiastecinfo@gmail.com
- WhatsApp: (75) 98889-0006
- Site: doisr.com.br

---

**Status:** ✅ CORRIGIDO
**Testado:** ⏳ Aguardando teste em produção
**Prioridade:** 🔴 CRÍTICA
