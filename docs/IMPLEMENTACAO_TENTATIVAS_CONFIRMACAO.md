# Implementação: Sistema de Tentativas Múltiplas de Confirmação

**Autor:** Rafael Dias - doisr.com.br
**Data:** 09/01/2026 22:55

---

## 📋 Resumo da Implementação

Sistema de **3 tentativas progressivas** de confirmação, todas enviadas **no dia anterior** ao agendamento, com intervalo configurável em **minutos**.

---

## ✅ Alterações Realizadas

### **1. Banco de Dados**

**Arquivo:** `docs/sql/add_confirmacao_tentativas.sql`

**Tabela `agendamentos`:**
```sql
ALTER TABLE `agendamentos`
ADD COLUMN `confirmacao_tentativas` TINYINT(1) UNSIGNED DEFAULT 0,
ADD COLUMN `confirmacao_ultima_tentativa` DATETIME NULL;
```

**Tabela `estabelecimentos`:**
```sql
ALTER TABLE `estabelecimentos`
ADD COLUMN `confirmacao_max_tentativas` TINYINT(1) UNSIGNED DEFAULT 3,
ADD COLUMN `confirmacao_intervalo_tentativas_minutos` SMALLINT(3) UNSIGNED DEFAULT 30,
ADD COLUMN `confirmacao_cancelar_automatico` ENUM('sim','nao') DEFAULT 'sim';
```

---

### **2. Model - Agendamento_model.php**

**Método `get_pendentes_confirmacao()`:**
- ✅ Filtra apenas agendamentos para **amanhã** (dia anterior)
- ✅ Primeira tentativa: quando passa o horário configurado
- ✅ Tentativas subsequentes: após intervalo em minutos
- ✅ Máximo de tentativas configurável (padrão: 3)

**Método `get_nao_confirmados_expirados()`:**
- ✅ Busca agendamentos que atingiram máximo de tentativas
- ✅ Aguarda intervalo após última tentativa
- ✅ Respeita configuração de cancelamento automático

---

### **3. Controller - Cron.php**

**Método `enviar_confirmacoes()`:**
- ✅ Incrementa contador de tentativas
- ✅ Determina tipo de mensagem (padrão/urgente/última chance)
- ✅ Atualiza timestamp da última tentativa
- ✅ Logs detalhados de cada tentativa

**Método `enviar_mensagem_confirmacao()`:**
- ✅ **Mensagem padrão** (1ª tentativa): tom neutro e informativo
- ✅ **Mensagem urgente** (2ª tentativa): mais direta, pede resposta
- ✅ **Mensagem última chance** (3ª tentativa): avisa cancelamento

**Método `cancelar_nao_confirmados()`:**
- ✅ Cancela após máximo de tentativas + intervalo
- ✅ Envia notificação de cancelamento
- ✅ Libera horário para outros clientes

---

## 🔄 Fluxo Completo

### **Exemplo com intervalo de 30 minutos:**

```
┌─────────────────────────────────────────────────────────────┐
│ DIA ANTERIOR - 19:00                                         │
│ ✅ 1ª TENTATIVA - Mensagem Padrão                           │
│ "📅 Confirmação de Agendamento"                             │
│ confirmacao_tentativas = 1                                   │
│ confirmacao_ultima_tentativa = 2026-01-09 19:00:00          │
└─────────────────────────────────────────────────────────────┘
                           ↓
              ┌────────────┴────────────┐
              │                         │
         RESPONDEU?                 NÃO RESPONDEU
              │                         │
              ↓                         ↓
    ┌─────────────────┐    ┌──────────────────────────┐
    │ status =        │    │ Aguarda 30 minutos       │
    │ confirmado      │    │ Cron roda a cada hora    │
    │ FIM ✅          │    └──────────────────────────┘
    └─────────────────┘                 ↓
                           ┌─────────────────────────────────────┐
                           │ DIA ANTERIOR - 19:30                 │
                           │ ⚠️ 2ª TENTATIVA - Mensagem Urgente  │
                           │ "⚠️ CONFIRMAÇÃO PENDENTE"            │
                           │ confirmacao_tentativas = 2           │
                           │ confirmacao_ultima_tentativa = 19:30 │
                           └─────────────────────────────────────┘
                                        ↓
                           ┌────────────┴────────────┐
                           │                         │
                      RESPONDEU?                 NÃO RESPONDEU
                           │                         │
                           ↓                         ↓
                 ┌─────────────────┐    ┌──────────────────────────┐
                 │ status =        │    │ Aguarda 30 minutos       │
                 │ confirmado      │    └──────────────────────────┘
                 │ FIM ✅          │                 ↓
                 └─────────────────┘    ┌─────────────────────────────────────┐
                                        │ DIA ANTERIOR - 20:00                 │
                                        │ 🚨 3ª TENTATIVA - Última Chance     │
                                        │ "🚨 SERÁ CANCELADO EM 30 MINUTOS"   │
                                        │ confirmacao_tentativas = 3           │
                                        │ confirmacao_ultima_tentativa = 20:00 │
                                        └─────────────────────────────────────┘
                                                     ↓
                                        ┌────────────┴────────────┐
                                        │                         │
                                   RESPONDEU?                 NÃO RESPONDEU
                                        │                         │
                                        ↓                         ↓
                              ┌─────────────────┐    ┌──────────────────────────┐
                              │ status =        │    │ Aguarda 30 minutos       │
                              │ confirmado      │    └──────────────────────────┘
                              │ FIM ✅          │                 ↓
                              └─────────────────┘    ┌─────────────────────────────────────┐
                                                     │ DIA ANTERIOR - 20:30                 │
                                                     │ ❌ CANCELAMENTO AUTOMÁTICO          │
                                                     │ status = cancelado                   │
                                                     │ cancelado_por = sistema              │
                                                     │ motivo = Não confirmado              │
                                                     │ Envia notificação ao cliente         │
                                                     │ FIM ❌                               │
                                                     └─────────────────────────────────────┘
```

---

## 📊 Configurações Padrão

| Campo | Valor Padrão | Descrição |
|-------|--------------|-----------|
| `confirmacao_max_tentativas` | 3 | Número máximo de tentativas |
| `confirmacao_intervalo_tentativas_minutos` | 30 | Intervalo entre tentativas (minutos) |
| `confirmacao_cancelar_automatico` | sim | Cancelar após todas as tentativas |
| `confirmacao_dia_anterior` | 1 | Enviar no dia anterior |
| `confirmacao_horario_dia_anterior` | 19:00:00 | Horário da primeira tentativa |

---

## 🎯 Mensagens Progressivas

### **1ª Tentativa - Padrão (19:00)**
```
Boa noite, Rafael! 👋

📅 Confirmação de Agendamento

Você tem um agendamento marcado:
📆 Data: 10/01/2026 (Sexta)
🕐 Horário: 08:30
💈 Serviço: Cabelo e Barba
👤 Profissional: Bruxo
📍 Local: modelo barber

Por favor, confirme sua presença:

1️⃣ Confirmar - Estarei presente ✅
2️⃣ Reagendar - Preciso mudar 🔄
3️⃣ Cancelar - Não poderei ir ❌

Aguardamos sua resposta! 😊
```

### **2ª Tentativa - Urgente (19:30)**
```
Boa noite, Rafael! 👋

⚠️ CONFIRMAÇÃO PENDENTE

Ainda não recebemos sua confirmação para:

📆 Data: 10/01/2026 (Sexta)
🕐 Horário: 08:30
💈 Serviço: Cabelo e Barba
👤 Profissional: Bruxo

Por favor, responda agora:

1️⃣ Confirmar ✅
2️⃣ Reagendar 🔄
3️⃣ Cancelar ❌

Aguardamos sua resposta! 😊
```

### **3ª Tentativa - Última Chance (20:00)**
```
Boa noite, Rafael! 👋

🚨 ÚLTIMA CHANCE - AGENDAMENTO SERÁ CANCELADO

Seu agendamento será CANCELADO AUTOMATICAMENTE em 30 minutos se não confirmar:

📆 Data: 10/01/2026 (Sexta)
🕐 Horário: 08:30
💈 Serviço: Cabelo e Barba
👤 Profissional: Bruxo

⏰ RESPONDA AGORA:

1️⃣ Confirmar ✅
2️⃣ Reagendar 🔄
3️⃣ Cancelar ❌
```

---

## ⚙️ Configuração do Cron

**Frequência recomendada:** A cada 15-30 minutos

```bash
# Executar a cada 15 minutos
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Cancelamentos (a cada 15 minutos também)
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1
```

**Importante:** Cron deve rodar com frequência menor que o intervalo entre tentativas para garantir precisão.

---

## 🔒 Garantias de Segurança

1. ✅ **Apenas dia anterior:** Não envia para agendamentos com 2+ dias de antecedência
2. ✅ **Máximo de tentativas:** Respeita limite configurado (padrão: 3)
3. ✅ **Intervalo respeitado:** Aguarda tempo configurado entre tentativas
4. ✅ **Sem duplicidade:** Contador impede envios duplicados
5. ✅ **Logs detalhados:** Rastreamento completo de cada tentativa

---

## 📝 Próximos Passos

1. ✅ **Executar migration SQL** no banco de dados
2. ✅ **Configurar cron** no cPanel (a cada 15 minutos)
3. ✅ **Ajustar configurações** no painel do estabelecimento
4. ✅ **Testar fluxo completo** com agendamento real
5. ✅ **Monitorar logs** para validar funcionamento

---

## 🧪 Como Testar

1. Criar agendamento para amanhã
2. Aguardar horário configurado (ex: 19:00)
3. Não responder à primeira mensagem
4. Aguardar intervalo (ex: 30 min)
5. Verificar segunda mensagem (urgente)
6. Não responder
7. Aguardar intervalo
8. Verificar terceira mensagem (última chance)
9. Não responder
10. Aguardar intervalo
11. Verificar cancelamento automático

---

## 📊 Monitoramento

### **Verificar tentativas de um agendamento:**
```sql
SELECT
    id,
    data,
    hora_inicio,
    status,
    confirmacao_tentativas,
    confirmacao_ultima_tentativa,
    confirmacao_enviada
FROM agendamentos
WHERE id = 121;
```

### **Verificar logs do cron:**
```bash
grep "CRON: Confirmação enviada" application/logs/log-2026-01-09.php
```

### **Agendamentos pendentes de confirmação:**
```sql
SELECT
    a.id,
    a.data,
    a.hora_inicio,
    a.confirmacao_tentativas,
    e.confirmacao_max_tentativas,
    TIMESTAMPDIFF(MINUTE, a.confirmacao_ultima_tentativa, NOW()) as minutos_desde_ultima
FROM agendamentos a
JOIN estabelecimentos e ON a.estabelecimento_id = e.id
WHERE a.status = 'pendente'
  AND a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
  AND e.confirmacao_dia_anterior = 1;
```

---

**Implementação concluída!** 🎉
