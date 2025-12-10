# Guia de Migração - Sprint 1: SaaS Multi-Tenant

**Autor:** Rafael Dias - doisr.com.br
**Data:** 09/12/2024

---

## ⚠️ ATENÇÃO - LEIA ANTES DE EXECUTAR

Este guia descreve o processo de migração do banco de dados para a arquitetura SaaS multi-tenant.

**CRÍTICO:**
1. ✅ Faça backup completo do banco antes de iniciar
2. ✅ Execute primeiro em ambiente de desenvolvimento/staging
3. ✅ Teste completamente antes de aplicar em produção
4. ✅ Tenha um plano de rollback preparado

---

## 📋 Resumo das Mudanças

### Novas Tabelas Criadas
1. **`planos`** - Planos de assinatura (Autônomo, Básico, Profissional, Premium)
2. **`assinaturas`** - Controle de assinaturas dos estabelecimentos
3. **`templates_notificacao`** - Templates personalizados por estabelecimento

### Tabelas Modificadas
1. **`usuarios`** - Reestruturada para multi-tenant (4 tipos de usuários)
2. **`estabelecimentos`** - Adicionados 15+ campos para configurações individuais
3. **`profissionais`** - Adicionados campos para vinculação com usuários

### Dados Migrados
- Usuários existentes → Super Admin
- Estabelecimento existente → Plano Básico (trial 30 dias)
- Templates de notificações → Nova estrutura
- Configurações MP globais → Estabelecimento individual

---

## 🗄️ Estrutura das Novas Tabelas

### 1. Planos

```sql
planos
├── id
├── nome (Autônomo, Básico, Profissional, Premium)
├── slug
├── valor_mensal
├── max_profissionais
├── max_agendamentos_mes
├── recursos (JSON)
├── trial_dias
└── ativo
```

**Planos Padrão:**
- **Autônomo:** R$ 29,90 - 1 profissional, 100 agendamentos/mês
- **Básico:** R$ 79,90 - 3 profissionais, 300 agendamentos/mês
- **Profissional:** R$ 149,90 - 10 profissionais, 1000 agendamentos/mês
- **Premium:** R$ 299,90 - Ilimitado

### 2. Assinaturas

```sql
assinaturas
├── id
├── estabelecimento_id (FK)
├── plano_id (FK)
├── data_inicio
├── data_fim
├── status (ativa, trial, cancelada, vencida, suspensa)
├── mercadopago_subscription_id
├── valor_pago
└── auto_renovar
```

### 3. Usuários (Reestruturado)

```sql
usuarios
├── id
├── email (UNIQUE)
├── senha
├── tipo (super_admin, estabelecimento, profissional)
├── estabelecimento_id (FK - NULL para super_admin)
├── profissional_id (FK - NULL se não for profissional)
├── nome
├── ativo
├── primeiro_acesso
└── token_reset_senha
```

### 4. Estabelecimentos (Novos Campos)

**Configurações Mercado Pago:**
- `mp_access_token_test`
- `mp_public_key_test`
- `mp_access_token_prod`
- `mp_public_key_prod`
- `mp_webhook_url`
- `mp_sandbox`

**Configurações WhatsApp/Evolution:**
- `evolution_api_url`
- `evolution_api_key`
- `evolution_instance_name`
- `whatsapp_numero`
- `whatsapp_conectado`

**Configurações Notificações:**
- `notificar_whatsapp`
- `notificar_email`

**Relacionamentos:**
- `usuario_id` (FK)
- `plano_id` (FK)

---

## 🚀 Passo a Passo da Migração

### Pré-requisitos

1. **Backup do Banco:**
```bash
# Via phpMyAdmin: Exportar > SQL > Salvar arquivo

# Ou via linha de comando:
mysqldump -u usuario -p dois8950_agendapro > backup_pre_migracao_$(date +%Y%m%d_%H%M%S).sql
```

2. **Verificar Versão do MySQL/MariaDB:**
```sql
SELECT VERSION();
-- Deve ser MariaDB 10.x ou MySQL 5.7+
```

### Passo 1: Executar Script de Migração

**Opção A: Via phpMyAdmin**
1. Acesse phpMyAdmin
2. Selecione o banco `dois8950_agendapro`
3. Vá em "SQL"
4. Cole o conteúdo de `migration_sprint1_saas.sql`
5. Clique em "Executar"

**Opção B: Via Linha de Comando**
```bash
mysql -u usuario -p dois8950_agendapro < docs/migration_sprint1_saas.sql
```

### Passo 2: Verificar Migração

Execute as queries de verificação:

```sql
-- Verificar planos criados
SELECT * FROM planos ORDER BY ordem;

-- Verificar usuários migrados
SELECT id, email, tipo, nome FROM usuarios;

-- Verificar estabelecimentos atualizados
SELECT id, nome, plano_id, mp_sandbox FROM estabelecimentos;

-- Verificar assinaturas criadas
SELECT e.nome, p.nome as plano, a.status, a.data_fim
FROM assinaturas a
JOIN estabelecimentos e ON a.estabelecimento_id = e.id
JOIN planos p ON a.plano_id = p.id;

-- Verificar templates migrados
SELECT estabelecimento_id, tipo, canal, ativo
FROM templates_notificacao;
```

### Passo 3: Validar Integridade

```sql
-- Verificar Foreign Keys
SELECT
  TABLE_NAME,
  CONSTRAINT_NAME,
  REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;

-- Verificar índices
SHOW INDEX FROM usuarios;
SHOW INDEX FROM estabelecimentos;
SHOW INDEX FROM assinaturas;
```

---

## 🧪 Testes Pós-Migração

### 1. Teste de Login

```sql
-- Verificar se usuários existentes ainda podem logar
SELECT id, email, senha, tipo FROM usuarios WHERE email = 'rafaeldiaswebdev@gmail.com';
```

### 2. Teste de Relacionamentos

```sql
-- Verificar se estabelecimento tem plano
SELECT e.nome, p.nome as plano
FROM estabelecimentos e
LEFT JOIN planos p ON e.plano_id = p.id;

-- Verificar se assinatura está ativa
SELECT * FROM assinaturas WHERE status = 'trial';
```

### 3. Teste de Configurações MP

```sql
-- Verificar se configurações MP foram migradas
SELECT
  id,
  nome,
  mp_sandbox,
  LENGTH(mp_access_token_test) as token_test_len,
  LENGTH(mp_access_token_prod) as token_prod_len
FROM estabelecimentos;
```

---

## 🔄 Plano de Rollback

Se algo der errado, execute:

```sql
-- 1. Restaurar backup
-- Via phpMyAdmin: Importar > Escolher arquivo de backup

-- Ou via linha de comando:
mysql -u usuario -p dois8950_agendapro < backup_pre_migracao_YYYYMMDD_HHMMSS.sql
```

---

## ⚠️ Problemas Conhecidos e Soluções

### Erro: "Foreign key constraint fails"

**Causa:** Tentando adicionar FK antes da tabela referenciada existir

**Solução:** Execute as partes do script na ordem correta:
1. Criar novas tabelas
2. Modificar tabelas existentes
3. Adicionar Foreign Keys

### Erro: "Duplicate entry for key 'email'"

**Causa:** E-mails duplicados na tabela usuarios_backup

**Solução:**
```sql
-- Verificar duplicatas
SELECT email, COUNT(*)
FROM usuarios_backup
GROUP BY email
HAVING COUNT(*) > 1;

-- Remover duplicatas antes de migrar
```

### Erro: "Column already exists"

**Causa:** Script executado parcialmente antes

**Solução:**
```sql
-- Verificar se coluna existe
SHOW COLUMNS FROM estabelecimentos LIKE 'mp_sandbox';

-- Se existir, pular essa parte do ALTER TABLE
```

---

## 📊 Checklist de Validação

Após a migração, verifique:

- [ ] Todas as 3 novas tabelas foram criadas
- [ ] Tabela `usuarios` foi reestruturada
- [ ] Tabela `estabelecimentos` tem novos campos
- [ ] Tabela `profissionais` tem novos campos
- [ ] 4 planos foram inseridos
- [ ] Usuários existentes foram migrados como super_admin
- [ ] Estabelecimento tem assinatura trial criada
- [ ] Configurações MP foram migradas
- [ ] Templates de notificação foram migrados
- [ ] Todas as Foreign Keys foram criadas
- [ ] Índices foram adicionados
- [ ] Login ainda funciona
- [ ] Dados existentes estão intactos

---

## 📝 Próximos Passos

Após a migração bem-sucedida:

1. ✅ **Testar Login** - Verificar se admin consegue logar
2. ✅ **Criar Models** - Usuario_model, Plano_model, Assinatura_model
3. ✅ **Criar Auth Controller** - Sistema de autenticação
4. ✅ **Criar Middleware** - Verificação de permissões
5. ✅ **Atualizar Controllers** - Adicionar filtros por estabelecimento

---

## 🆘 Suporte

Em caso de problemas:

1. **Verifique os logs do MySQL:**
```bash
tail -f /var/log/mysql/error.log
```

2. **Execute queries de diagnóstico:**
```sql
SHOW ENGINE INNODB STATUS;
SHOW WARNINGS;
```

3. **Restaure o backup** se necessário

---

**Migração preparada por:** Rafael Dias - doisr.com.br
**Data:** 09/12/2024
