-- ============================================================================
-- VERIFICAÇÃO COMPLETA PÓS-MIGRAÇÃO - SPRINT 1
-- Execute este script para validar toda a migração
-- ============================================================================

-- 1. ESTRUTURA DE TABELAS
SELECT
    '1. Estrutura de Tabelas' as secao,
    TABLE_NAME as tabela,
    TABLE_ROWS as linhas_aprox,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as tamanho_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND TABLE_NAME IN ('planos', 'assinaturas', 'templates_notificacao', 'usuarios', 'estabelecimentos', 'profissionais')
ORDER BY TABLE_NAME;

-- 2. PLANOS CRIADOS
SELECT
    '2. Planos' as secao,
    id,
    nome,
    slug,
    valor_mensal,
    max_profissionais,
    max_agendamentos_mes,
    ativo
FROM planos
ORDER BY ordem;

-- 3. USUÁRIOS MIGRADOS
SELECT
    '3. Usuários' as secao,
    id,
    email,
    tipo,
    nome,
    ativo,
    estabelecimento_id,
    profissional_id
FROM usuarios
ORDER BY id;

-- 4. ESTABELECIMENTOS ATUALIZADOS
SELECT
    '4. Estabelecimentos' as secao,
    id,
    nome,
    plano_id,
    mp_sandbox,
    CASE WHEN mp_access_token_test IS NOT NULL THEN 'Sim' ELSE 'Não' END as tem_mp_test,
    CASE WHEN mp_access_token_prod IS NOT NULL THEN 'Sim' ELSE 'Não' END as tem_mp_prod,
    status
FROM estabelecimentos;

-- 5. ASSINATURAS CRIADAS
SELECT
    '5. Assinaturas' as secao,
    a.id,
    e.nome as estabelecimento,
    p.nome as plano,
    a.status,
    a.data_inicio,
    a.data_fim,
    DATEDIFF(a.data_fim, CURDATE()) as dias_restantes
FROM assinaturas a
JOIN estabelecimentos e ON a.estabelecimento_id = e.id
JOIN planos p ON a.plano_id = p.id;

-- 6. TEMPLATES DE NOTIFICAÇÃO
SELECT
    '6. Templates' as secao,
    COUNT(*) as total_templates,
    GROUP_CONCAT(DISTINCT tipo ORDER BY tipo) as tipos_disponiveis,
    GROUP_CONCAT(DISTINCT canal ORDER BY canal) as canais
FROM templates_notificacao;

-- 7. FOREIGN KEYS
SELECT
    '7. Foreign Keys' as secao,
    TABLE_NAME as tabela,
    CONSTRAINT_NAME as constraint,
    REFERENCED_TABLE_NAME as referencia
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND REFERENCED_TABLE_NAME IS NOT NULL
AND TABLE_NAME IN ('assinaturas', 'templates_notificacao', 'estabelecimentos', 'profissionais', 'usuarios')
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- 8. ÍNDICES CRIADOS
SELECT
    '8. Índices' as secao,
    TABLE_NAME as tabela,
    INDEX_NAME as indice,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as colunas
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND TABLE_NAME IN ('planos', 'assinaturas', 'templates_notificacao', 'usuarios', 'estabelecimentos')
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME, INDEX_NAME;

-- 9. RESUMO FINAL
SELECT
    '9. RESUMO FINAL' as secao,
    'Planos cadastrados' as item,
    COUNT(*) as valor
FROM planos
WHERE ativo = 1

UNION ALL

SELECT
    '9. RESUMO FINAL',
    'Usuários migrados',
    COUNT(*)
FROM usuarios

UNION ALL

SELECT
    '9. RESUMO FINAL',
    'Estabelecimentos ativos',
    COUNT(*)
FROM estabelecimentos
WHERE status = 'ativo'

UNION ALL

SELECT
    '9. RESUMO FINAL',
    'Assinaturas ativas/trial',
    COUNT(*)
FROM assinaturas
WHERE status IN ('ativa', 'trial')

UNION ALL

SELECT
    '9. RESUMO FINAL',
    'Templates configurados',
    COUNT(*)
FROM templates_notificacao
WHERE ativo = 1;

-- ============================================================================
-- CHECKLIST DE VALIDAÇÃO
-- ============================================================================
-- [ ] 4 planos criados
-- [ ] Usuários migrados como super_admin
-- [ ] Estabelecimento tem plano_id preenchido
-- [ ] Estabelecimento tem configs MP migradas
-- [ ] Assinatura trial criada
-- [ ] 7 templates de notificação criados
-- [ ] Foreign Keys criadas (verificar seção 7)
-- [ ] Índices criados (verificar seção 8)
