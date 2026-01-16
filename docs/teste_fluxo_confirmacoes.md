# 🧪 TESTE DE FLUXO DE CONFIRMAÇÕES

**Autor:** Rafael Dias - doisr.com.br
**Data:** 16/01/2026

---

## 📋 CONFIGURAÇÕES RECOMENDADAS

### Estabelecimento
```
tempo_minimo_agendamento = 120 (2 horas)
confirmacao_max_tentativas = 3
confirmacao_intervalo_tentativas_minutos = 20
confirmacao_horas_antes = 2
confirmacao_dia_anterior = 1
confirmacao_horario_dia_anterior = 18:00:00
confirmacao_cancelar_automatico = sim
cancelar_nao_confirmados = 1
cancelar_nao_confirmados_horas = 1
```

### 🎯 Lógica da Configuração

**Por que 2 horas antes para confirmação?**
- Cliente só pode agendar com 2h de antecedência mínima
- Sistema pede confirmação assim que entra na janela (2h antes)
- 3 tentativas em 40 minutos (ex: 15:00, 15:20, 15:40)

**Por que cancelar 1 hora antes?**
- Se não confirmar após 3 tentativas, cancela 1h antes do horário
- **Libera o horário** para outro cliente agendar
- Estabelecimento não perde o horário!

**Exemplo prático:**
- 13:00 → Cliente agenda para 17h
- 15:00 → 1ª tentativa (2h antes)
- 15:20 → 2ª tentativa
- 15:40 → 3ª tentativa
- 16:00 → Cancela (1h antes) → **Horário disponível novamente!**

### Cron Jobs
```bash
# Confirmações - A cada 15 minutos
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Lembretes - A cada 15 minutos
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_lembretes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Cancelamentos - A cada 30 minutos (para não conflitar)
*/30 * * * * curl -s "https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Limpeza de conversas - 1x por dia às 3h
0 3 * * * curl "https://iafila.doisr.com.br/cron/limpar_conversas_bot?token=b781f3e57f4e4c4ba3a67df819050e6e"
```

---

## 🧪 CENÁRIO 1: Confirmação 2 horas antes

### Setup
- Cliente agenda às **13:00** para **17:00** (4 horas depois)
- Sistema aguarda até **15:00** (2h antes do horário)

### Fluxo Esperado

**13:00 - Cliente agenda:**
- ✅ Agendamento criado com status `pendente`
- ✅ Cliente recebe: "🎉 Agendamento Criado!" (notificação inicial)
- Sistema: `confirmacao_enviada = 0`, `tentativas = 0`

**15:00 (2h antes do horário):**
- ✅ Cliente recebe: "📅 Confirmação de Agendamento" (mensagem padrão)
- Sistema: `tentativas = 1`, `ultima_tentativa = 15:00`, `confirmacao_enviada = 1`

**15:20 (1h40 antes):**
- ❌ Cliente não respondeu
- ✅ Cliente recebe: "⚠️ CONFIRMAÇÃO PENDENTE" (mensagem urgente)
- Sistema: `tentativas = 2`, `ultima_tentativa = 15:20`

**15:40 (1h20 antes):**
- ❌ Cliente não respondeu
- ✅ Cliente recebe: "🚨 ÚLTIMA CHANCE - SERÁ CANCELADO EM 20 MIN" (mensagem crítica)
- Sistema: `tentativas = 3`, `ultima_tentativa = 15:40`

**16:00 (1h antes do horário):**
- ❌ Cliente não respondeu após 3 tentativas
- ✅ Sistema cancela automaticamente: `status = cancelado`
- ✅ Cliente recebe: "⚠️ Agendamento Cancelado Automaticamente"
- ✅ **HORÁRIO DAS 17:00 FICA DISPONÍVEL NOVAMENTE!**

**16:01 em diante:**
- ✅ Outro cliente pode agendar o horário das 17:00
- ✅ Estabelecimento não perde o horário!

---

## 🧪 CENÁRIO 2: Confirmação dia anterior às 18h

### Setup
- Criar agendamento para **amanhã às 10h**
- Aguardar até **hoje às 18h**

### Fluxo Esperado

**Hoje 18:00:**
- ✅ Cliente recebe: "📅 Confirmação de Agendamento" (mensagem padrão)
- Sistema: `tentativas = 1`, `ultima_tentativa = 18:00`

**Hoje 18:20:**
- ❌ Cliente não respondeu
- ✅ Cliente recebe: "⚠️ CONFIRMAÇÃO PENDENTE" (mensagem urgente)
- Sistema: `tentativas = 2`, `ultima_tentativa = 18:20`

**Hoje 18:40:**
- ❌ Cliente não respondeu
- ✅ Cliente recebe: "🚨 ÚLTIMA CHANCE" (mensagem crítica)
- Sistema: `tentativas = 3`, `ultima_tentativa = 18:40`

**Hoje 19:00 (ou próximo cron de cancelamento):**
- ❌ Cliente não respondeu após 3 tentativas
- ✅ Sistema cancela automaticamente
- ✅ Cliente recebe: "⚠️ Agendamento Cancelado Automaticamente"

---

## 🧪 CENÁRIO 3: Cliente confirma na 2ª tentativa

### Setup
- Criar agendamento para **daqui a 2 horas**
- Aguardar até faltar **1 hora**

### Fluxo Esperado

**Hora 0 (1h antes):**
- ✅ Cliente recebe: "📅 Confirmação de Agendamento"
- Sistema: `tentativas = 1`

**Hora 0 + 20min:**
- ❌ Cliente não respondeu
- ✅ Cliente recebe: "⚠️ CONFIRMAÇÃO PENDENTE"
- Sistema: `tentativas = 2`

**Hora 0 + 25min:**
- ✅ **Cliente responde: "1"**
- ✅ Sistema confirma: `status = confirmado`
- ✅ Cliente recebe: "✅ Agendamento Confirmado!"
- ❌ **NÃO** envia 3ª tentativa
- ❌ **NÃO** cancela

---

## 📊 VALIDAÇÃO

### Verificar no Banco de Dados

```sql
-- Ver tentativas de um agendamento
SELECT
    id,
    data,
    hora_inicio,
    status,
    confirmacao_tentativas,
    confirmacao_ultima_tentativa,
    confirmacao_enviada,
    confirmado_em
FROM agendamentos
WHERE id = [ID_DO_AGENDAMENTO];
```

### Verificar nos Logs

```bash
# Ver logs de confirmação
grep "CRON: Agendamento #[ID]" application/logs/log-2026-01-16.php

# Ver logs de cancelamento
grep "CRON Cancelamento: Agendamento #[ID]" application/logs/log-2026-01-16.php
```

---

## ⚠️ PROBLEMAS CONHECIDOS

### 1. Cron rodando apenas às :00 e :15
**Sintoma:** Tentativas a cada 1 hora ao invés de 15-20 minutos
**Causa:** Cron configurado como `0,15` ao invés de `*/15`
**Solução:** Usar `*/15 * * * *`

### 2. Cancelamento rodando junto com confirmação
**Sintoma:** Agendamento cancelado antes de completar as tentativas
**Causa:** Ambos os crons rodando no mesmo horário
**Solução:** Cancelamento rodar a cada 30min (`*/30`)

### 3. Tentativas contadas incorretamente
**Sintoma:** Sistema mostra `2/2` mas só enviou 1 mensagem
**Causa:** Bug no incremento de tentativas
**Solução:** Corrigido no commit `f85d1f2`

---

## 📞 SUPORTE

**Dúvidas?**
- Email: rafaeldiastecinfo@gmail.com
- WhatsApp: (75) 98889-0006
- Site: doisr.com.br
