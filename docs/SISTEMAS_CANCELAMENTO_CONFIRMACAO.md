# Sistemas de Cancelamento e Confirmação

**Autor:** Rafael Dias - doisr.com.br
**Data:** 09/01/2026 23:15

---

## 📋 Visão Geral

O sistema possui **DOIS** métodos de cancelamento automático que funcionam de forma **complementar**:

1. **Sistema de Tentativas Múltiplas** (NOVO) - Para confirmações no dia anterior
2. **Sistema por Horas Antes** (ANTIGO) - Para confirmações X horas antes

---

## 🆕 Sistema 1: Tentativas Múltiplas (NOVO)

### **Quando usar:**
- Quando `confirmacao_dia_anterior = 1` (ativado)
- Envia múltiplas tentativas no dia anterior
- Cancela após todas as tentativas sem resposta

### **Como funciona:**

```
DIA ANTERIOR - 19:00
├─ 1ª Tentativa (Mensagem Padrão)
│  └─ Cliente não responde
│
├─ 30 minutos depois (19:30)
├─ 2ª Tentativa (Mensagem Urgente)
│  └─ Cliente não responde
│
├─ 30 minutos depois (20:00)
├─ 3ª Tentativa (Última Chance - Aviso de Cancelamento)
│  └─ Cliente não responde
│
├─ 30 minutos depois (20:30)
└─ ❌ CANCELAMENTO AUTOMÁTICO
```

### **Configurações:**

| Campo | Padrão | Descrição |
|-------|--------|-----------|
| `confirmacao_max_tentativas` | 3 | Número máximo de tentativas |
| `confirmacao_intervalo_tentativas_minutos` | 30 | Intervalo entre tentativas (minutos) |
| `confirmacao_cancelar_automatico` | sim | Cancelar após todas as tentativas |

### **Vantagens:**
- ✅ Dá múltiplas chances ao cliente
- ✅ Avisa antes de cancelar
- ✅ Mensagens progressivas (neutro → urgente → última chance)
- ✅ Cancela no dia anterior, liberando horário com antecedência

---

## 🕐 Sistema 2: Por Horas Antes (ANTIGO)

### **Quando usar:**
- Quando `cancelar_nao_confirmados = 1` (ativado)
- Para agendamentos SEM confirmação dia anterior
- Como fallback/segurança adicional

### **Como funciona:**

```
AGENDAMENTO: 10/01/2026 08:30
CONFIGURAÇÃO: Cancelar 2 horas antes

DIA DO AGENDAMENTO - 06:30
└─ ❌ CANCELAMENTO AUTOMÁTICO
   (se ainda estiver pendente e não confirmado)
```

### **Configurações:**

| Campo | Padrão | Descrição |
|-------|--------|-----------|
| `cancelar_nao_confirmados` | 0 | Ativar cancelamento automático |
| `cancelar_nao_confirmados_horas` | 2 | Horas antes do horário para cancelar |

### **Vantagens:**
- ✅ Funciona para qualquer tipo de confirmação
- ✅ Simples de entender
- ✅ Cancela com tempo suficiente para liberar horário

---

## 🔄 Como os Dois Sistemas Trabalham Juntos

### **Cenário 1: Confirmação no Dia Anterior Ativada**

```
CONFIGURAÇÃO:
- confirmacao_dia_anterior = 1 ✅
- confirmacao_cancelar_automatico = sim ✅
- cancelar_nao_confirmados = 1 ✅
- cancelar_nao_confirmados_horas = 2

FLUXO:
1. Dia anterior 19:00 → Tenta confirmar (Sistema NOVO)
2. Dia anterior 19:30 → 2ª tentativa
3. Dia anterior 20:00 → 3ª tentativa
4. Dia anterior 20:30 → CANCELA (Sistema NOVO)

   OU (se o sistema novo falhar)

5. Dia do agendamento 06:30 → CANCELA (Sistema ANTIGO - fallback)
```

**Resultado:** Sistema NOVO cancela no dia anterior. Sistema ANTIGO serve como segurança.

---

### **Cenário 2: Apenas Confirmação X Horas Antes**

```
CONFIGURAÇÃO:
- confirmacao_dia_anterior = 0 ❌
- confirmacao_horas_antes = 1
- cancelar_nao_confirmados = 1 ✅
- cancelar_nao_confirmados_horas = 2

FLUXO:
1. 1 hora antes → Envia confirmação
2. Cliente não responde
3. 2 horas antes do horário → CANCELA (Sistema ANTIGO)
```

**Resultado:** Apenas Sistema ANTIGO funciona.

---

### **Cenário 3: Ambos Desativados**

```
CONFIGURAÇÃO:
- confirmacao_dia_anterior = 0 ❌
- confirmacao_cancelar_automatico = nao ❌
- cancelar_nao_confirmados = 0 ❌

FLUXO:
1. Envia confirmação
2. Cliente não responde
3. Agendamento permanece PENDENTE até o horário
```

**Resultado:** Nenhum cancelamento automático.

---

## ⚙️ Configuração Recomendada

### **Para Estabelecimentos com Alto Volume:**

```
✅ Confirmação Dia Anterior: SIM
   - Horário: 19:00
   - Max tentativas: 3
   - Intervalo: 30 minutos
   - Cancelar automaticamente: SIM

✅ Cancelamento por Horas: SIM (como fallback)
   - Horas antes: 2
```

**Motivo:** Dá múltiplas chances no dia anterior + segurança adicional.

---

### **Para Estabelecimentos com Baixo Volume:**

```
❌ Confirmação Dia Anterior: NÃO
   - Horas antes: 2

✅ Cancelamento por Horas: SIM
   - Horas antes: 1
```

**Motivo:** Mais simples, menos mensagens ao cliente.

---

## 🔍 Query de Cancelamento

A query do cron `cancelar_nao_confirmados` verifica **AMBOS** os sistemas:

```sql
WHERE a.status = 'pendente'
  AND a.data >= CURDATE()
  AND (
      -- Sistema NOVO: Tentativas múltiplas
      (e.confirmacao_dia_anterior = 1
       AND e.confirmacao_cancelar_automatico = 'sim'
       AND a.confirmacao_tentativas >= e.confirmacao_max_tentativas
       AND TIMESTAMPDIFF(MINUTE, a.confirmacao_ultima_tentativa, NOW()) >= e.confirmacao_intervalo_tentativas_minutos)
      OR
      -- Sistema ANTIGO: Horas antes
      (e.cancelar_nao_confirmados = 1
       AND a.confirmacao_enviada = 1
       AND TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) <= e.cancelar_nao_confirmados_horas)
  )
```

---

## 📊 Comparação

| Critério | Sistema NOVO | Sistema ANTIGO |
|----------|--------------|----------------|
| **Quando cancela** | Dia anterior após tentativas | X horas antes do horário |
| **Número de chances** | 3 (configurável) | 1 |
| **Mensagens** | Progressivas | Única |
| **Flexibilidade** | Alta | Média |
| **Complexidade** | Média | Baixa |
| **Recomendado para** | Confirmação dia anterior | Qualquer tipo |

---

## ✅ Recomendação Final

**MANTER AMBOS OS SISTEMAS:**

1. **Sistema NOVO** para confirmações no dia anterior (mais efetivo)
2. **Sistema ANTIGO** como fallback/segurança adicional

Isso garante que:
- ✅ Clientes têm múltiplas chances de confirmar
- ✅ Sistema tem redundância (se um falhar, outro funciona)
- ✅ Horários são liberados com antecedência
- ✅ Reduz no-shows significativamente

---

**Última atualização:** 09/01/2026 23:15
**Autor:** Rafael Dias - doisr.com.br
