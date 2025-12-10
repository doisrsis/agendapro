-- ============================================================================
-- CORREÇÃO DE ERROS DA MIGRAÇÃO SPRINT 1
-- Executar apenas se encontrou os erros específicos
-- ============================================================================

-- ----------------------------------------------------------------------------
-- ERRO 1: Foreign Key duplicada em assinaturas
-- Causa: Constraint já existe (possivelmente de execução anterior)
-- ----------------------------------------------------------------------------

-- Verificar se constraints já existem
SELECT CONSTRAINT_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND TABLE_NAME = 'assinaturas'
AND CONSTRAINT_TYPE = 'FOREIGN KEY';

-- Se aparecer fk_assinaturas_estabelecimento ou fk_assinaturas_plano, elas já existem!
-- Neste caso, pule a criação das FKs

-- Se precisar recriar, primeiro remova:
-- ALTER TABLE `assinaturas` DROP FOREIGN KEY `fk_assinaturas_estabelecimento`;
-- ALTER TABLE `assinaturas` DROP FOREIGN KEY `fk_assinaturas_plano`;

-- Depois adicione novamente:
-- ALTER TABLE `assinaturas`
-- ADD CONSTRAINT `fk_assinaturas_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `fk_assinaturas_plano` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`) ON DELETE RESTRICT;

-- ----------------------------------------------------------------------------
-- ERRO 2: Dados duplicados em templates_notificacao
-- Causa: Templates já foram migrados anteriormente
-- ----------------------------------------------------------------------------

-- Verificar templates existentes
SELECT estabelecimento_id, tipo, canal, COUNT(*) as total
FROM templates_notificacao
GROUP BY estabelecimento_id, tipo, canal
HAVING COUNT(*) > 1;

-- Se houver duplicatas, limpe primeiro:
DELETE FROM templates_notificacao
WHERE id NOT IN (
    SELECT MIN(id)
    FROM (SELECT * FROM templates_notificacao) as t
    GROUP BY estabelecimento_id, tipo, canal
);

-- Agora migre novamente (se necessário):
INSERT IGNORE INTO `templates_notificacao` (`estabelecimento_id`, `tipo`, `canal`, `mensagem`, `ativo`)
SELECT
  nc.estabelecimento_id,
  nc.tipo,
  'whatsapp' as canal,
  nc.template as mensagem,
  nc.ativo
FROM notificacoes_config nc;

-- ----------------------------------------------------------------------------
-- VERIFICAÇÃO FINAL
-- ----------------------------------------------------------------------------

-- Verificar se tudo está OK agora
SELECT 'Assinaturas FKs' as tabela, COUNT(*) as total
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'dois8950_agendapro'
AND TABLE_NAME = 'assinaturas'
AND CONSTRAINT_TYPE = 'FOREIGN KEY'

UNION ALL

SELECT 'Templates únicos' as tabela, COUNT(*) as total
FROM templates_notificacao;

-- Deve mostrar:
-- Assinaturas FKs: 2 (fk_assinaturas_estabelecimento e fk_assinaturas_plano)
-- Templates únicos: 7 (ou quantos templates você tem)
