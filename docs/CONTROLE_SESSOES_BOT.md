# Sistema de Controle de Sessões do Bot WhatsApp

**Autor:** Rafael Dias - doisr.com.br
**Data:** 30/12/2025

---

## 📋 Objetivo

Implementar controle adequado de sessões do bot, permitindo identificar quando uma conversa foi encerrada pelo usuário e gerenciar o ciclo de vida completo das conversas.

---

## 🔴 Problema Anterior

### **Comportamento Incorreto:**

Quando o usuário digitava `0` ou `sair`:
- ❌ Sessão apenas voltava para estado `menu`
- ❌ Registro permanecia ativo no banco indefinidamente
- ❌ Não havia registro de encerramento
- ❌ Impossível distinguir sessões ativas de encerradas

### **Impactos:**

- Sessões "fantasmas" acumulando no banco
- Impossibilidade de análise de métricas (tempo médio de sessão, taxa de conclusão, etc.)
- Consumo desnecessário de recursos
- Dificuldade em identificar sessões realmente ativas

---

## ✅ Solução Implementada

### **1. Novos Campos na Tabela `bot_conversas`**

```sql
ALTER TABLE bot_conversas
ADD COLUMN encerrada TINYINT(1) DEFAULT 0
  COMMENT 'Indica se a sessão foi encerrada pelo usuário',
ADD COLUMN data_encerramento DATETIME NULL
  COMMENT 'Data/hora que a sessão foi encerrada';
```

**Campos adicionados:**
- `encerrada` (TINYINT) - Flag indicando se sessão foi encerrada (0 = ativa, 1 = encerrada)
- `data_encerramento` (DATETIME) - Timestamp do encerramento

**Índices criados:**
- `idx_encerrada` - Para filtrar sessões ativas/encerradas
- `idx_ultima_interacao` - Para limpeza de sessões antigas

---

### **2. Novo Método `encerrar()` no Bot_conversa_model**

```php
public function encerrar($conversa_id)
{
    log_message('debug', "Bot: Encerrando sessão {$conversa_id}");

    return $this->db
        ->where('id', $conversa_id)
        ->update($this->table, [
            'estado' => 'encerrada',
            'dados_temporarios' => json_encode([]),
            'encerrada' => 1,
            'data_encerramento' => date('Y-m-d H:i:s'),
            'ultima_interacao' => date('Y-m-d H:i:s')
        ]);
}
```

**O que faz:**
1. Marca `encerrada = 1`
2. Define `estado = 'encerrada'`
3. Limpa `dados_temporarios`
4. Registra `data_encerramento`
5. Atualiza `ultima_interacao`
6. Gera log de debug

---

### **3. Modificação no Webhook_waha.php**

**Antes:**
```php
if (in_array($msg, ['0', 'sair', 'tchau', 'obrigado', 'obrigada'])) {
    $this->Bot_conversa_model->resetar($conversa->id);
    // ...
}
```

**Depois:**
```php
if (in_array($msg, ['0', 'sair', 'tchau', 'obrigado', 'obrigada'])) {
    $this->Bot_conversa_model->encerrar($conversa->id);
    // ...
}
```

**Mudança:** Usa `encerrar()` ao invés de `resetar()` para comandos de saída.

---

### **4. Melhoria no Método `limpar_antigas()`**

```php
public function limpar_antigas()
{
    // Remover conversas encerradas há mais de 7 dias
    $this->db
        ->where('encerrada', 1)
        ->where('data_encerramento <', date('Y-m-d H:i:s', strtotime('-7 days')))
        ->delete($this->table);

    $encerradas = $this->db->affected_rows();

    // Remover conversas inativas (não encerradas) há mais de 24 horas
    $this->db
        ->where('encerrada', 0)
        ->where('ultima_interacao <', date('Y-m-d H:i:s', strtotime('-24 hours')))
        ->delete($this->table);

    $inativas = $this->db->affected_rows();

    return $encerradas + $inativas;
}
```

**Estratégia de limpeza:**
1. **Sessões encerradas:** Remove após 7 dias (mantém histórico)
2. **Sessões inativas:** Remove após 24 horas (sem interação)

---

## 🔄 Ciclo de Vida de uma Sessão

```
┌─────────────────────────────────────────────────────────────┐
│                    CICLO DE VIDA DA SESSÃO                  │
└─────────────────────────────────────────────────────────────┘

1. CRIAÇÃO
   ↓
   Usuário envia mensagem
   → get_ou_criar() cria registro
   → estado = 'menu'
   → encerrada = 0

2. INTERAÇÃO
   ↓
   Usuário navega pelo bot
   → estado muda conforme fluxo
   → ultima_interacao atualizada
   → dados_temporarios armazenados

3. ENCERRAMENTO (Usuário digita 0/sair)
   ↓
   → encerrar() é chamado
   → estado = 'encerrada'
   → encerrada = 1
   → data_encerramento = NOW()
   → dados_temporarios = []

4. LIMPEZA (Cron Job)
   ↓
   Após 7 dias (encerradas) ou 24h (inativas)
   → Registro deletado do banco
```

---

## 📊 Estados da Sessão

| Estado | Descrição | encerrada |
|--------|-----------|-----------|
| `menu` | Menu principal | 0 |
| `aguardando_servico` | Escolhendo serviço | 0 |
| `aguardando_profissional` | Escolhendo profissional | 0 |
| `aguardando_data` | Escolhendo data | 0 |
| `aguardando_hora` | Escolhendo horário | 0 |
| `confirmando` | Confirmando agendamento | 0 |
| `encerrada` | **Sessão encerrada** | **1** |

---

## 🎯 Comandos que Encerram Sessão

Os seguintes comandos encerram a sessão:
- `0`
- `sair`
- `tchau`
- `obrigado`
- `obrigada`

**Mensagem de despedida:**
```
Obrigado por entrar em contato! 😊

Até a próxima! 👋

Digite *oi* quando precisar de mim novamente.
```

---

## 🔍 Consultas Úteis

### **Sessões ativas:**
```sql
SELECT * FROM bot_conversas
WHERE encerrada = 0
ORDER BY ultima_interacao DESC;
```

### **Sessões encerradas hoje:**
```sql
SELECT * FROM bot_conversas
WHERE encerrada = 1
AND DATE(data_encerramento) = CURDATE();
```

### **Tempo médio de sessão:**
```sql
SELECT
    AVG(TIMESTAMPDIFF(MINUTE, criado_em, data_encerramento)) as tempo_medio_minutos
FROM bot_conversas
WHERE encerrada = 1
AND data_encerramento IS NOT NULL;
```

### **Taxa de conclusão (agendamentos finalizados):**
```sql
SELECT
    COUNT(CASE WHEN estado = 'encerrada' THEN 1 END) as total_encerradas,
    COUNT(*) as total_sessoes,
    (COUNT(CASE WHEN estado = 'encerrada' THEN 1 END) / COUNT(*) * 100) as taxa_conclusao
FROM bot_conversas;
```

---

## 🚀 Benefícios

### **1. Gestão Adequada**
- ✅ Sessões claramente identificadas como ativas ou encerradas
- ✅ Histórico de encerramentos preservado por 7 dias
- ✅ Limpeza automática de dados antigos

### **2. Métricas e Análises**
- ✅ Tempo médio de sessão
- ✅ Taxa de conclusão de agendamentos
- ✅ Horários de maior atividade
- ✅ Análise de abandono de fluxo

### **3. Performance**
- ✅ Banco de dados mais limpo
- ✅ Consultas mais rápidas (índices)
- ✅ Menor consumo de recursos

### **4. Experiência do Usuário**
- ✅ Mensagem clara de despedida
- ✅ Possibilidade de retornar quando quiser
- ✅ Sessão limpa ao iniciar novamente

---

## 📝 Arquivos Modificados

1. **`docs/sql_controle_sessoes_bot.sql`** - SQL para adicionar campos
2. **`application/models/Bot_conversa_model.php`** - Métodos encerrar() e limpar_antigas()
3. **`application/controllers/Webhook_waha.php`** - Uso do método encerrar()

---

## ⚙️ Configuração do Cron Job

O cron job já existente em `application/controllers/Cron.php` executa a limpeza:

```php
public function limpar_conversas_bot()
{
    $removidos = $this->Bot_conversa_model->limpar_antigas();
    echo "Conversas removidas: {$removidos}\n";
}
```

**Executar diariamente:**
```bash
0 3 * * * curl https://iafila.doisr.com.br/cron/limpar_conversas_bot
```

---

## 🧪 Testes

### **Teste 1: Encerrar sessão**
1. Inicie conversa: "oi"
2. Digite: "0"
3. Verifique no banco: `encerrada = 1`, `data_encerramento` preenchida

### **Teste 2: Nova sessão após encerramento**
1. Encerre sessão: "0"
2. Inicie nova: "oi"
3. Verifique: Nova sessão criada (registro diferente)

### **Teste 3: Limpeza automática**
1. Execute cron: `/cron/limpar_conversas_bot`
2. Verifique: Sessões antigas removidas

---

## 📌 Próximos Passos

Com o controle de sessões implementado, podemos:
1. ✅ Implementar melhorias do documento `MELHORIAS_BOT_301225.md`
2. ✅ Adicionar confirmação ao sair
3. ✅ Implementar reagendamento
4. ✅ Melhorar comandos de navegação
5. ✅ Dashboard com métricas de uso do bot
