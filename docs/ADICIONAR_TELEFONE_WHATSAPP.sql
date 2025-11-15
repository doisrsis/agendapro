-- ============================================================================
-- ADICIONAR CONFIGURAÇÕES DE TELEFONE E WHATSAPP
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 14/11/2024
-- Descrição: Adiciona ou atualiza as configurações de telefone e WhatsApp
-- ============================================================================

-- Telefone da empresa
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_telefone', '(75) 98889-0006', 'texto', 'geral', 'Telefone principal')
ON DUPLICATE KEY UPDATE `descricao` = 'Telefone principal';

-- WhatsApp da empresa
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_whatsapp', '5575988890006', 'texto', 'geral', 'WhatsApp (com DDI e DDD)')
ON DUPLICATE KEY UPDATE `descricao` = 'WhatsApp (com DDI e DDD)';

-- ============================================================================
-- SUCESSO!
-- ============================================================================
SELECT 'Configurações de telefone e WhatsApp adicionadas com sucesso!' AS Mensagem;
