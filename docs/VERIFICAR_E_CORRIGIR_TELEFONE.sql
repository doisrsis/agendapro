-- ============================================================================
-- VERIFICAR E CORRIGIR TELEFONE E WHATSAPP
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 14/11/2024
-- ============================================================================

-- 1. Ver todas as configs do grupo 'geral'
SELECT * FROM configuracoes WHERE grupo = 'geral' ORDER BY chave;

-- 2. Ver especificamente telefone e whatsapp (qualquer grupo)
SELECT * FROM configuracoes WHERE chave IN ('empresa_telefone', 'empresa_whatsapp');

-- 3. DELETAR as configs problemáticas (se existirem)
DELETE FROM configuracoes WHERE chave IN ('empresa_telefone', 'empresa_whatsapp');

-- 4. RECRIAR com os dados corretos
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('telefone', '75992495077', 'texto', 'geral', 'Telefone principal da empresa');

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('whatsapp', '5575992495077', 'texto', 'geral', 'WhatsApp da empresa (com DDI e DDD)');

-- 5. Verificar se foi criado corretamente
SELECT * FROM configuracoes WHERE chave IN ('telefone', 'whatsapp');

-- ============================================================================
-- NOTA: Usei nomes mais simples (telefone, whatsapp) sem o prefixo empresa_
-- ============================================================================
