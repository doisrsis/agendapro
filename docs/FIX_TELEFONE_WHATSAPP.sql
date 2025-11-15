-- ============================================================================
-- CORRIGIR TELEFONE E WHATSAPP
-- ============================================================================
-- Execute este SQL no phpMyAdmin
-- ============================================================================

-- Primeiro, vamos ver se existem
SELECT * FROM configuracoes WHERE chave IN ('empresa_telefone', 'empresa_whatsapp');

-- Se não aparecer nada acima, execute os INSERTs abaixo:

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_telefone', '(75) 98889-0006', 'texto', 'geral', 'Telefone principal')
ON DUPLICATE KEY UPDATE `descricao` = 'Telefone principal';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_whatsapp', '5575988890006', 'texto', 'geral', 'WhatsApp (com DDI e DDD)')
ON DUPLICATE KEY UPDATE `descricao` = 'WhatsApp (com DDI e DDD)';

-- Verificar se foi inserido
SELECT * FROM configuracoes WHERE chave IN ('empresa_telefone', 'empresa_whatsapp');
