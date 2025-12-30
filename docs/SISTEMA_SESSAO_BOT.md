# Sistema de Sessão do Bot WhatsApp

**Autor:** Rafael Dias - doisr.com.br
**Data:** 29/12/2025

## 📋 Visão Geral

O bot WhatsApp utiliza um sistema de sessão baseado em estados para gerenciar conversas com clientes. Cada conversa é armazenada na tabela `bot_conversas` e possui um timeout automático.

---

## ⏱️ Timeout de Sessão

### Funcionamento:
- **Tempo de expiração:** 30 minutos de inatividade
- **Verificação:** Automática a cada nova mensagem
- **Ação:** Reseta conversa para o menu principal

### Implementação:
```php
// Bot_conversa_model::get_ou_criar()
$diferenca_minutos = ($agora - $ultima_interacao) / 60;

if ($diferenca_minutos > 30) {
    // Sessão expirada - resetar para menu
    $this->resetar($conversa->id);
}
```

---

## 🎯 Comandos de Início

O bot reconhece os seguintes comandos para iniciar/resetar conversa:

**Comandos aceitos:**
- `oi`, `olá`, `ola`
- `menu`, `inicio`, `início`
- `hi`, `hello`
- `bom dia`, `boa tarde`, `boa noite`

**Comportamento:**
1. Reseta o estado da conversa
2. Limpa dados temporários
3. Mostra o menu principal

---

## 🗑️ Limpeza Automática

### Conversas Antigas (24h+)

**Método:** `Bot_conversa_model::limpar_antigas()`

Remove conversas com última interação há mais de 24 horas.

**Execução via Cron:**
```
URL: /cron/limpar_conversas_bot?token=SEU_TOKEN
Frequência: 1x por dia (recomendado às 3h da manhã)
```

**Exemplo de configuração crontab:**
```bash
0 3 * * * curl "https://seusite.com.br/cron/limpar_conversas_bot?token=SEU_TOKEN"
```

---

## 📊 Estrutura da Tabela

```sql
CREATE TABLE `bot_conversas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED DEFAULT NULL,
  `numero_whatsapp` varchar(20) NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'menu',
  `dados_temporarios` TEXT DEFAULT NULL,
  `ultima_interacao` datetime NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_estabelecimento_numero` (`estabelecimento_id`, `numero_whatsapp`),
  KEY `idx_ultima_interacao` (`ultima_interacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🔄 Estados da Conversa

| Estado | Descrição |
|--------|-----------|
| `menu` | Menu principal (estado inicial) |
| `aguardando_servico` | Aguardando seleção de serviço |
| `aguardando_profissional` | Aguardando seleção de profissional |
| `aguardando_data` | Aguardando seleção de data |
| `aguardando_hora` | Aguardando seleção de horário |
| `confirmando` | Aguardando confirmação do agendamento |
| `aguardando_cancelamento` | Aguardando seleção de agendamento para cancelar |
| `gerenciando_agendamento` | Aguardando seleção de agendamento para gerenciar |
| `aguardando_acao_agendamento` | Aguardando ação (reagendar/cancelar) |
| `confirmando_cancelamento` | Aguardando confirmação de cancelamento |
| `reagendando_data` | Aguardando nova data para reagendamento |
| `reagendando_hora` | Aguardando novo horário para reagendamento |
| `confirmando_reagendamento` | Aguardando confirmação de reagendamento |

---

## 🛠️ Manutenção

### Monitoramento

**Verificar conversas ativas:**
```sql
SELECT COUNT(*) as total_ativas
FROM bot_conversas
WHERE ultima_interacao > DATE_SUB(NOW(), INTERVAL 30 MINUTE);
```

**Verificar conversas por estado:**
```sql
SELECT estado, COUNT(*) as total
FROM bot_conversas
WHERE ultima_interacao > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY estado;
```

### Limpeza Manual

**Remover conversas específicas:**
```sql
DELETE FROM bot_conversas
WHERE estabelecimento_id = 4
AND ultima_interacao < DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

**Resetar todas as conversas:**
```sql
UPDATE bot_conversas
SET estado = 'menu',
    dados_temporarios = '{}',
    ultima_interacao = NOW();
```

---

## 📝 Logs

O sistema registra logs importantes:

- **Sessão expirada:** `Bot: Sessão expirada para {numero} (última interação há X minutos)`
- **Limpeza de conversas:** `CRON: X conversas antigas removidas`

**Localização dos logs:**
```
application/logs/log-YYYY-MM-DD.php
```

---

## ⚙️ Configurações Recomendadas

| Parâmetro | Valor Recomendado | Descrição |
|-----------|-------------------|-----------|
| Timeout de sessão | 30 minutos | Tempo de inatividade antes de resetar |
| Limpeza de antigas | 24 horas | Tempo antes de remover conversa do banco |
| Frequência do cron | 1x por dia | Frequência de limpeza automática |

---

## 🔍 Troubleshooting

### Bot não reconhece comandos
- Verificar se a conversa não está em estado travado
- Executar limpeza manual se necessário
- Verificar logs para erros

### Sessão não expira
- Verificar se `ultima_interacao` está sendo atualizada
- Confirmar que o método `get_ou_criar` está sendo chamado
- Verificar timezone do servidor

### Muitas conversas no banco
- Configurar cron de limpeza
- Reduzir tempo de limpeza de 24h para 12h se necessário
- Executar limpeza manual
