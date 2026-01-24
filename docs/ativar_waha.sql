-- =====================================================
-- ATIVAR INTEGRAÇÃO WAHA GLOBALMENTE
-- Autor: Rafael Dias - doisr.com.br
-- Data: 24/01/2026 01:56
-- =====================================================

-- Verificar configuração atual
SELECT * FROM configuracoes WHERE chave = 'waha_ativo';

-- Ativar WAHA globalmente
UPDATE configuracoes
SET valor = '1'
WHERE chave = 'waha_ativo';

-- Se não existir, criar
INSERT INTO configuracoes (chave, valor, descricao, tipo, created_at, updated_at)
SELECT 'waha_ativo', '1', 'Ativar/Desativar integração WAHA globalmente', 'boolean', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM configuracoes WHERE chave = 'waha_ativo');

-- Verificar resultado
SELECT * FROM configuracoes WHERE chave = 'waha_ativo';
