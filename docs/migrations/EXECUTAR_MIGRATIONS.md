# 📋 INSTRUÇÕES PARA EXECUTAR MIGRATIONS

**Autor:** Rafael Dias - doisr.com.br
**Data:** 03/01/2026

---

## ⚠️ IMPORTANTE

Execute as migrations **NA ORDEM** indicada abaixo.

---

## 🗄️ MIGRATIONS DISPONÍVEIS

### **1. Migration 001: Campos em `agendamentos`**
**Arquivo:** `001_adicionar_campos_confirmacao_agendamentos.sql`

**O que faz:**
- Adiciona 5 novos campos na tabela `agendamentos`
- Campos para controlar confirmação e lembretes

**Executar:**
```sql
-- Copie e cole cada comando SEPARADAMENTE no phpMyAdmin

ALTER TABLE agendamentos
ADD COLUMN confirmacao_enviada TINYINT(1) DEFAULT 0 COMMENT 'Flag se pedido de confirmação foi enviado';

ALTER TABLE agendamentos
ADD COLUMN confirmacao_enviada_em DATETIME NULL COMMENT 'Quando o pedido foi enviado';

ALTER TABLE agendamentos
ADD COLUMN confirmado_em DATETIME NULL COMMENT 'Quando o cliente confirmou presença';

ALTER TABLE agendamentos
ADD COLUMN lembrete_enviado TINYINT(1) DEFAULT 0 COMMENT 'Flag se lembrete pré-atendimento foi enviado';

ALTER TABLE agendamentos
ADD COLUMN lembrete_enviado_em DATETIME NULL COMMENT 'Quando o lembrete foi enviado';
```

---

### **2. Migration 002: Campos em `estabelecimentos`**
**Arquivo:** `002_adicionar_campos_confirmacao_estabelecimentos.sql`

**O que faz:**
- Adiciona 9 novos campos na tabela `estabelecimentos`
- Campos de configuração para confirmações e lembretes

**Executar:**
```sql
-- Copie e cole cada comando SEPARADAMENTE no phpMyAdmin

ALTER TABLE estabelecimentos
ADD COLUMN solicitar_confirmacao TINYINT(1) DEFAULT 1 COMMENT 'Se deve solicitar confirmação do cliente';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_horas_antes INT DEFAULT 24 COMMENT 'Quantas horas antes solicitar confirmação';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_dia_anterior TINYINT(1) DEFAULT 1 COMMENT 'Se envia no dia anterior';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_horario_dia_anterior TIME DEFAULT '18:00:00' COMMENT 'Horário para enviar no dia anterior';

ALTER TABLE estabelecimentos
ADD COLUMN enviar_lembrete_pre_atendimento TINYINT(1) DEFAULT 1 COMMENT 'Se envia lembrete antes do atendimento';

ALTER TABLE estabelecimentos
ADD COLUMN lembrete_minutos_antes INT DEFAULT 60 COMMENT 'Quantos minutos antes enviar lembrete';

ALTER TABLE estabelecimentos
ADD COLUMN lembrete_antecedencia_chegada INT DEFAULT 10 COMMENT 'Minutos de antecedência para pedir ao cliente';

ALTER TABLE estabelecimentos
ADD COLUMN cancelar_nao_confirmados TINYINT(1) DEFAULT 0 COMMENT 'Se cancela automaticamente não confirmados';

ALTER TABLE estabelecimentos
ADD COLUMN cancelar_nao_confirmados_horas INT DEFAULT 2 COMMENT 'Quantas horas antes cancelar se não confirmar';
```

---

## ✅ VERIFICAR SE DEU CERTO

Após executar, rode estas queries para verificar:

```sql
-- Verificar campos em agendamentos
SHOW COLUMNS FROM agendamentos LIKE '%confirmacao%';
SHOW COLUMNS FROM agendamentos LIKE '%lembrete%';

-- Verificar campos em estabelecimentos
SHOW COLUMNS FROM estabelecimentos LIKE '%confirmacao%';
SHOW COLUMNS FROM estabelecimentos LIKE '%lembrete%';
SHOW COLUMNS FROM estabelecimentos LIKE '%cancelar%';
```

Deve retornar **14 linhas no total** (5 + 9).

---

## 🔄 ROLLBACK (Reverter)

Se precisar desfazer as alterações:

```sql
-- Remover campos de agendamentos
ALTER TABLE agendamentos DROP COLUMN confirmacao_enviada;
ALTER TABLE agendamentos DROP COLUMN confirmacao_enviada_em;
ALTER TABLE agendamentos DROP COLUMN confirmado_em;
ALTER TABLE agendamentos DROP COLUMN lembrete_enviado;
ALTER TABLE agendamentos DROP COLUMN lembrete_enviado_em;

-- Remover campos de estabelecimentos
ALTER TABLE estabelecimentos DROP COLUMN solicitar_confirmacao;
ALTER TABLE estabelecimentos DROP COLUMN confirmacao_horas_antes;
ALTER TABLE estabelecimentos DROP COLUMN confirmacao_dia_anterior;
ALTER TABLE estabelecimentos DROP COLUMN confirmacao_horario_dia_anterior;
ALTER TABLE estabelecimentos DROP COLUMN enviar_lembrete_pre_atendimento;
ALTER TABLE estabelecimentos DROP COLUMN lembrete_minutos_antes;
ALTER TABLE estabelecimentos DROP COLUMN lembrete_antecedencia_chegada;
ALTER TABLE estabelecimentos DROP COLUMN cancelar_nao_confirmados;
ALTER TABLE estabelecimentos DROP COLUMN cancelar_nao_confirmados_horas;
```

---

## 📝 PRÓXIMOS PASSOS

Após executar as migrations com sucesso:

1. ✅ Implementar tela de configurações no painel
2. ✅ Criar cron jobs de confirmação e lembretes
3. ✅ Integrar com bot WhatsApp
4. ✅ Testar fluxo completo

---

**Dúvidas?** Consulte a proposta completa em `Proposta_Sistema_Confirmacao_Agendamentos.md`
