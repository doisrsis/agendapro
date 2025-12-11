-- ============================================================================
-- Migração: Remover campo telefone e manter apenas whatsapp
-- Data: 11/12/2024
-- Autor: Rafael Dias - doisr.com.br
-- ============================================================================

-- IMPORTANTE: Tabela usuarios já foi alterada manualmente
-- Este script altera apenas: clientes, estabelecimentos, profissionais

-- ============================================================================
-- 1. MIGRAR DADOS DE TELEFONE PARA WHATSAPP (se necessário)
-- ============================================================================

-- Clientes: Copiar telefone para whatsapp onde whatsapp está vazio
UPDATE `clientes`
SET `whatsapp` = `telefone`
WHERE `telefone` IS NOT NULL
  AND `telefone` != ''
  AND (`whatsapp` IS NULL OR `whatsapp` = '');

-- Estabelecimentos: Copiar telefone para whatsapp onde whatsapp está vazio
UPDATE `estabelecimentos`
SET `whatsapp` = `telefone`
WHERE `telefone` IS NOT NULL
  AND `telefone` != ''
  AND (`whatsapp` IS NULL OR `whatsapp` = '');

-- Profissionais: Copiar telefone para whatsapp onde whatsapp está vazio
UPDATE `profissionais`
SET `whatsapp` = `telefone`
WHERE `telefone` IS NOT NULL
  AND `telefone` != ''
  AND (`whatsapp` IS NULL OR `whatsapp` = '');

-- ============================================================================
-- 2. REMOVER COLUNAS TELEFONE
-- ============================================================================

-- Remover coluna telefone da tabela clientes
ALTER TABLE `clientes` DROP COLUMN `telefone`;

-- Remover coluna telefone da tabela estabelecimentos
ALTER TABLE `estabelecimentos` DROP COLUMN `telefone`;

-- Remover coluna telefone da tabela profissionais
ALTER TABLE `profissionais` DROP COLUMN `telefone`;

-- ============================================================================
-- 3. VERIFICAÇÃO PÓS-MIGRAÇÃO
-- ============================================================================

-- Verificar estrutura das tabelas
SELECT 'clientes' as tabela, COUNT(*) as total_registros,
       SUM(CASE WHEN whatsapp IS NOT NULL AND whatsapp != '' THEN 1 ELSE 0 END) as com_whatsapp
FROM `clientes`
UNION ALL
SELECT 'estabelecimentos', COUNT(*),
       SUM(CASE WHEN whatsapp IS NOT NULL AND whatsapp != '' THEN 1 ELSE 0 END)
FROM `estabelecimentos`
UNION ALL
SELECT 'profissionais', COUNT(*),
       SUM(CASE WHEN whatsapp IS NOT NULL AND whatsapp != '' THEN 1 ELSE 0 END)
FROM `profissionais`
UNION ALL
SELECT 'usuarios', COUNT(*),
       SUM(CASE WHEN whatsapp IS NOT NULL AND whatsapp != '' THEN 1 ELSE 0 END)
FROM `usuarios`;

-- ============================================================================
-- FIM DA MIGRAÇÃO
-- ============================================================================
