-- ============================================================================
-- Migração: Adicionar campo telefone na tabela clientes
-- Autor: Rafael Dias - doisr.com.br
-- Data: 17/01/2026
-- ============================================================================

-- Descrição:
-- Adiciona campo 'telefone' para armazenar apenas dígitos do número do cliente
-- O campo 'whatsapp' continua armazenando o formato original (@lid ou apenas dígitos)
-- para compatibilidade com a API WAHA

-- ============================================================================
-- 1. ADICIONAR COLUNA TELEFONE
-- ============================================================================

ALTER TABLE `clientes`
ADD COLUMN `telefone` VARCHAR(20) NULL AFTER `whatsapp`,
ADD INDEX `idx_telefone` (`telefone`);

-- ============================================================================
-- 2. POPULAR CAMPO TELEFONE COM DADOS EXISTENTES
-- ============================================================================

-- Extrair apenas dígitos do campo whatsapp para o campo telefone
UPDATE `clientes`
SET `telefone` = REGEXP_REPLACE(`whatsapp`, '[^0-9]', '');

-- ============================================================================
-- 3. VERIFICAR RESULTADO
-- ============================================================================

-- Verificar alguns registros
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp Original',
    telefone AS 'Telefone (apenas dígitos)'
FROM clientes
LIMIT 10;

-- ============================================================================
-- ROLLBACK (caso necessário)
-- ============================================================================

-- Para reverter a migração, execute:
-- ALTER TABLE `clientes` DROP COLUMN `telefone`;
-- ALTER TABLE `clientes` DROP INDEX `idx_telefone`;

-- ============================================================================
-- OBSERVAÇÕES
-- ============================================================================

/*
ANTES:
- whatsapp: "108259113467972@lid" ou "557588890006"

DEPOIS:
- whatsapp: "108259113467972@lid" (mantém formato original para API)
- telefone: "557599935560" (apenas dígitos para formatação e botões)

VANTAGENS:
1. Campo whatsapp preserva formato @lid para compatibilidade com API WAHA
2. Campo telefone facilita formatação e validação
3. Botões WhatsApp usam telefone (mais confiável)
4. Busca por telefone mais eficiente com índice
*/
