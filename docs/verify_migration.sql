-- ============================================================================
-- VERIFICAÇÃO RÁPIDA PÓS-MIGRAÇÃO
-- Execute este script para verificar se tudo está OK
-- ============================================================================

-- 1. Verificar se Foreign Keys de assinaturas existem
SELECT
    'Foreign Keys Assinaturas' as verificacao,
    CONSTRAINT_NAME as nome,
    'OK' as status
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND TABLE_NAME = 'assinaturas'
AND CONSTRAINT_TYPE = 'FOREIGN KEY';

-- Resultado esperado: 2 linhas (fk_assinaturas_estabelecimento e fk_assinaturas_plano)
-- Se aparecer, está OK! O erro foi porque já existiam.

-- 2. Verificar templates_notificacao
SELECT
    'Templates Notificacao' as verificacao,
    COUNT(*) as total,
    CASE
        WHEN COUNT(*) > 0 THEN 'OK - Templates migrados'
        ELSE 'ERRO - Sem templates'
    END as status
FROM templates_notificacao;

-- 3. Verificar se há duplicatas em templates
SELECT
    'Duplicatas Templates' as verificacao,
    estabelecimento_id,
    tipo,
    canal,
    COUNT(*) as total
FROM templates_notificacao
GROUP BY estabelecimento_id, tipo, canal
HAVING COUNT(*) > 1;

-- Se retornar vazio, está OK! Se retornar linhas, há duplicatas.

-- 4. Verificar estrutura completa
SELECT
    'Planos' as tabela,
    COUNT(*) as total,
    CASE WHEN COUNT(*) = 4 THEN 'OK' ELSE 'VERIFICAR' END as status
FROM planos

UNION ALL

SELECT
    'Assinaturas' as tabela,
    COUNT(*) as total,
    CASE WHEN COUNT(*) > 0 THEN 'OK' ELSE 'VERIFICAR' END as status
FROM assinaturas

UNION ALL

SELECT
    'Usuarios' as tabela,
    COUNT(*) as total,
    CASE WHEN COUNT(*) > 0 THEN 'OK' ELSE 'VERIFICAR' END as status
FROM usuarios

UNION ALL

SELECT
    'Templates' as tabela,
    COUNT(*) as total,
    CASE WHEN COUNT(*) > 0 THEN 'OK' ELSE 'VERIFICAR' END as status
FROM templates_notificacao;

-- 5. Verificar campos novos em estabelecimentos
SELECT
    'Campos MP' as verificacao,
    COUNT(*) as total_estabelecimentos,
    SUM(CASE WHEN mp_access_token_test IS NOT NULL THEN 1 ELSE 0 END) as com_config_mp,
    CASE
        WHEN SUM(CASE WHEN mp_access_token_test IS NOT NULL THEN 1 ELSE 0 END) > 0
        THEN 'OK - Configs migradas'
        ELSE 'VERIFICAR - Sem configs MP'
    END as status
FROM estabelecimentos;

-- 6. Verificar tipos de usuários
SELECT
    'Tipos de Usuario' as verificacao,
    tipo,
    COUNT(*) as total
FROM usuarios
GROUP BY tipo;

-- Deve mostrar: super_admin com os usuários existentes
