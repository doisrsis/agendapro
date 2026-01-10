# Configuração de Cron Jobs - AgendaPro

**Autor:** Rafael Dias - doisr.com.br
**Data:** 09/01/2026 22:15

---

## 📋 Visão Geral

O sistema AgendaPro utiliza cron jobs para executar tarefas automáticas como:
- Envio de confirmações de agendamento
- Envio de lembretes pré-atendimento
- Verificação de pagamentos pendentes
- Cancelamento automático de agendamentos não confirmados

---

## 🔐 Token de Segurança

Todos os crons requerem um token de segurança configurado no banco de dados:

**Token atual:** `b781f3e57f4e4c4ba3a67df819050e6e`

Este token está armazenado na tabela `configuracoes` com a chave `cron_token`.

---

## ⚙️ Cron Jobs Necessários

### 1. Enviar Confirmações de Agendamento
**Frequência:** A cada 1 hora
**URL:** `https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=b781f3e57f4e4c4ba3a67df819050e6e`

**Função:**
- Envia pedidos de confirmação para agendamentos pendentes
- Respeita configurações de "X horas antes" ou "dia anterior em horário fixo"
- Atualiza estado da conversa do bot para `confirmando_agendamento`

**Configuração cPanel:**
```
0 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1
```

---

### 2. Enviar Lembretes Pré-Atendimento
**Frequência:** A cada 15 minutos
**URL:** `https://iafila.doisr.com.br/cron/enviar_lembretes?token=b781f3e57f4e4c4ba3a67df819050e6e`

**Função:**
- Envia lembretes para agendamentos já confirmados
- Envia X minutos antes do horário do agendamento
- Inclui informações de antecedência de chegada

**Configuração cPanel:**
```
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_lembretes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1
```

---

### 3. Verificar Pagamentos Pendentes
**Frequência:** A cada 2 minutos
**URL:** `https://iafila.doisr.com.br/cron/verificar_pagamentos?token=b781f3e57f4e4c4ba3a67df819050e6e`

**Função:**
- Envia lembretes de pagamento para PIX expirados
- Cancela agendamentos com tempo adicional expirado
- Atualiza status de pagamento

**Configuração cPanel:**
```
*/2 * * * * curl -s "https://iafila.doisr.com.br/cron/verificar_pagamentos?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1
```

---

### 4. Cancelar Agendamentos Não Confirmados
**Frequência:** A cada 1 hora
**URL:** `https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=b781f3e57f4e4c4ba3a67df819050e6e`

**Função:**
- Cancela automaticamente agendamentos não confirmados
- Respeita prazo de X horas antes do agendamento
- Libera horário para outros clientes

**Configuração cPanel:**
```
0 * * * * curl -s "https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1
```

---

## 🖥️ Como Configurar no cPanel

### Passo 1: Acessar Cron Jobs
1. Faça login no cPanel
2. Procure por "Cron Jobs" ou "Tarefas Cron"
3. Clique para abrir

### Passo 2: Adicionar Novo Cron
1. Selecione a frequência desejada
2. Cole o comando no campo "Command"
3. Clique em "Add New Cron Job"

### Passo 3: Verificar Execução
Após configurar, aguarde a execução e verifique os logs em:
- `application/logs/log-YYYY-MM-DD.php`
- Procure por linhas com `CRON:`

---

## 📊 Monitoramento

### Verificar Logs de Execução
```sql
SELECT * FROM cron_logs
ORDER BY executado_em DESC
LIMIT 20;
```

### Verificar Última Execução
```sql
SELECT tipo, MAX(executado_em) as ultima_execucao, registros_processados
FROM cron_logs
GROUP BY tipo;
```

---

## 🐛 Troubleshooting

### Problema: Cron não está executando
**Solução:**
1. Verifique se o comando está correto no cPanel
2. Verifique se o token está correto
3. Teste a URL manualmente no navegador
4. Verifique os logs do servidor

### Problema: Confirmações não estão sendo enviadas
**Solução:**
1. Verifique se `solicitar_confirmacao = 1` no estabelecimento
2. Verifique se `confirmacao_dia_anterior = 1` está ativado
3. Verifique se o horário configurado já passou (ex: 22:15)
4. Verifique se há agendamentos para amanhã
5. Verifique os logs: `grep "CRON: Iniciando envio de confirmações" application/logs/*`

### Problema: Token inválido
**Solução:**
```sql
SELECT * FROM configuracoes WHERE chave = 'cron_token';
-- Se não existir, criar:
INSERT INTO configuracoes (chave, valor) VALUES ('cron_token', 'b781f3e57f4e4c4ba3a67df819050e6e');
```

---

## 📝 Notas Importantes

1. **Todos os crons devem usar HTTPS** para segurança
2. **O token deve ser mantido em segredo** e não compartilhado
3. **Logs são essenciais** para debug - sempre verifique em caso de problemas
4. **Frequências recomendadas** foram testadas e otimizadas
5. **Não execute crons manualmente** com muita frequência para evitar sobrecarga

---

## 🔄 Resumo de Configuração Rápida

Copie e cole todos os comandos abaixo no cPanel:

```bash
# Confirmações (a cada 1 hora)
0 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_confirmacoes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Lembretes (a cada 15 minutos)
*/15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_lembretes?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Pagamentos (a cada 2 minutos)
*/2 * * * * curl -s "https://iafila.doisr.com.br/cron/verificar_pagamentos?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1

# Cancelamentos (a cada 1 hora)
0 * * * * curl -s "https://iafila.doisr.com.br/cron/cancelar_nao_confirmados?token=b781f3e57f4e4c4ba3a67df819050e6e" > /dev/null 2>&1
```

---

**Última atualização:** 09/01/2026 22:15
**Autor:** Rafael Dias - doisr.com.br
