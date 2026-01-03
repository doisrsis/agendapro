# 🤖 CONFIGURAR CRON JOBS - SISTEMA DE CONFIRMAÇÃO E LEMBRETES

**Autor:** Rafael Dias - doisr.com.br
**Data:** 03/01/2026
**Versão:** 1.0

---

## 📋 VISÃO GERAL

Este documento contém as instruções para configurar os cron jobs do sistema de confirmação e lembretes de agendamentos.

---

## 🔑 TOKEN DE SEGURANÇA

Todos os cron jobs requerem um token de segurança na URL.

**Localizar o token:**
1. Acesse o banco de dados
2. Tabela: `configuracoes`
3. Campo: `cron_token`

**Exemplo de token:** `abc123def456ghi789`

---

## 🤖 CRON JOBS DISPONÍVEIS

### **1. Enviar Confirmações**

**Objetivo:** Enviar pedido de confirmação para clientes com agendamentos pendentes

**URL:**
```
https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=SEU_TOKEN_AQUI
```

**Frequência Recomendada:** A cada 1 hora

**Configuração cPanel:**
```bash
0 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=SEU_TOKEN_AQUI" > /dev/null 2>&1
```

**O que faz:**
- Busca agendamentos com status `pendente`
- Sem pagamento obrigatório
- X horas antes do horário OU dia anterior às 18h
- Envia mensagem: "1-Confirmar | 2-Reagendar | 3-Cancelar"
- Marca como `confirmacao_enviada = 1`

**Resposta JSON:**
```json
{
  "success": true,
  "timestamp": "2026-01-03 10:00:00",
  "resultado": {
    "confirmacoes_enviadas": 5,
    "erros": []
  }
}
```

---

### **2. Enviar Lembretes**

**Objetivo:** Enviar lembrete pré-atendimento para clientes confirmados

**URL:**
```
https://iafila.doisr.com.br/cron/enviar_lembretes?token=SEU_TOKEN_AQUI
```

**Frequência Recomendada:** A cada 15 minutos

**Configuração cPanel:**
```bash
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_lembretes?token=SEU_TOKEN_AQUI" > /dev/null 2>&1
```

**O que faz:**
- Busca agendamentos com status `confirmado`
- X minutos antes do horário
- Envia lembrete com tempo faltando
- Pede antecedência de chegada
- Marca como `lembrete_enviado = 1`

**Resposta JSON:**
```json
{
  "success": true,
  "timestamp": "2026-01-03 10:15:00",
  "resultado": {
    "lembretes_enviados": 3,
    "erros": []
  }
}
```

---

### **3. Cancelar Não Confirmados (OPCIONAL)**

**Objetivo:** Cancelar automaticamente agendamentos não confirmados

**URL:**
```
https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=SEU_TOKEN_AQUI
```

**Frequência Recomendada:** A cada 1 hora

**Configuração cPanel:**
```bash
0 * * * * curl -s "https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=SEU_TOKEN_AQUI" > /dev/null 2>&1
```

**O que faz:**
- Busca agendamentos com status `pendente`
- Confirmação já enviada mas não respondida
- X horas antes do horário (configurável)
- Cancela automaticamente
- Envia notificação ao cliente
- Libera horário na agenda

**Resposta JSON:**
```json
{
  "success": true,
  "timestamp": "2026-01-03 10:30:00",
  "resultado": {
    "cancelados": 2,
    "erros": []
  }
}
```

---

## ⚙️ CONFIGURAR NO CPANEL

### **Passo 1: Acessar Cron Jobs**
1. Login no cPanel
2. Buscar "Cron Jobs"
3. Clicar em "Cron Jobs"

### **Passo 2: Adicionar Novo Cron Job**

**Para Confirmações (a cada 1 hora):**
- **Minuto:** 0
- **Hora:** * (todas)
- **Dia:** * (todos)
- **Mês:** * (todos)
- **Dia da Semana:** * (todos)
- **Comando:**
```bash
curl -s "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=SEU_TOKEN_AQUI" > /dev/null 2>&1
```

**Para Lembretes (a cada 15 minutos):**
- **Minuto:** */15
- **Hora:** * (todas)
- **Dia:** * (todos)
- **Mês:** * (todos)
- **Dia da Semana:** * (todos)
- **Comando:**
```bash
curl -s "https://iafila.doisr.com.br/cron/enviar_lembretes?token=SEU_TOKEN_AQUI" > /dev/null 2>&1
```

**Para Cancelamentos (a cada 1 hora) - OPCIONAL:**
- **Minuto:** 0
- **Hora:** * (todas)
- **Dia:** * (todos)
- **Mês:** * (todos)
- **Dia da Semana:** * (todos)
- **Comando:**
```bash
curl -s "https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=SEU_TOKEN_AQUI" > /dev/null 2>&1
```

### **Passo 3: Salvar**
Clicar em "Add New Cron Job"

---

## 📊 MONITORAMENTO

### **Verificar Logs**

**Arquivo de log:**
```
application/logs/log-YYYY-MM-DD.php
```

**Buscar por:**
```
CRON: Iniciando envio de confirmações
CRON: Confirmação enviada para agendamento #123
CRON: Erro ao enviar confirmação #123
```

### **Verificar Tabela cron_logs**

```sql
SELECT * FROM cron_logs
WHERE tipo IN ('enviar_confirmacoes', 'enviar_lembretes', 'cancelar_nao_confirmados')
ORDER BY executado_em DESC
LIMIT 20;
```

**Campos:**
- `tipo`: Nome do cron job
- `registros_processados`: Quantidade de agendamentos processados
- `detalhes`: JSON com resultado completo
- `executado_em`: Data/hora da execução

---

## 🧪 TESTAR MANUALMENTE

### **Via Navegador:**
```
https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=SEU_TOKEN_AQUI
```

### **Via Terminal (SSH):**
```bash
curl "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=SEU_TOKEN_AQUI"
```

### **Verificar Resposta:**
- `success: true` = Executou com sucesso
- `confirmacoes_enviadas: X` = Quantidade enviada
- `erros: []` = Sem erros

---

## ⚠️ TROUBLESHOOTING

### **Problema: Nenhuma confirmação enviada**

**Verificar:**
1. Existem agendamentos pendentes?
```sql
SELECT * FROM agendamentos
WHERE status = 'pendente'
AND confirmacao_enviada = 0
AND data >= CURDATE();
```

2. Estabelecimento tem `solicitar_confirmacao = 1`?
```sql
SELECT id, nome, solicitar_confirmacao
FROM estabelecimentos;
```

3. WAHA está ativo e conectado?
```sql
SELECT id, nome, waha_ativo, waha_status
FROM estabelecimentos;
```

### **Problema: Erro ao enviar mensagem**

**Verificar:**
1. Logs de erro:
```bash
tail -f application/logs/log-2026-01-03.php | grep "CRON: Erro"
```

2. Credenciais WAHA:
```sql
SELECT waha_api_url, waha_session_name, waha_status
FROM estabelecimentos
WHERE id = X;
```

3. Cliente tem WhatsApp válido?
```sql
SELECT id, nome, whatsapp
FROM clientes
WHERE id = X;
```

### **Problema: Token inválido**

**Erro:** `404 Not Found`

**Solução:**
1. Verificar token no banco:
```sql
SELECT cron_token FROM configuracoes LIMIT 1;
```

2. Atualizar URL com token correto

---

## 📈 MÉTRICAS RECOMENDADAS

Acompanhar semanalmente:

1. **Taxa de Confirmação:**
   - Quantos clientes confirmam vs. total de pedidos enviados

2. **Taxa de Comparecimento:**
   - Quantos confirmados comparecem vs. total confirmado

3. **Tempo Médio de Resposta:**
   - Quanto tempo cliente leva para confirmar

4. **Cancelamentos Antecipados:**
   - Quantos cancelam antes do horário

5. **Reagendamentos:**
   - Quantos preferem reagendar

---

## 🔄 MANUTENÇÃO

### **Diário:**
- Verificar logs de erro
- Conferir se cron jobs estão executando

### **Semanal:**
- Analisar métricas de confirmação
- Ajustar horários se necessário

### **Mensal:**
- Revisar configurações dos estabelecimentos
- Otimizar mensagens baseado em feedback

---

## 📞 SUPORTE

**Dúvidas ou problemas?**

- **Email:** rafaeldiastecinfo@gmail.com
- **WhatsApp:** (75) 98889-0006
- **Site:** doisr.com.br

---

**Última atualização:** 03/01/2026 10:50
**Autor:** Rafael Dias - doisr.com.br
