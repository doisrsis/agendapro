-- ============================================================================
-- ATUALIZAR CONFIGURAÇÕES - LE CORTINE
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 14/11/2024
-- Descrição: Atualiza ou insere configurações (evita duplicatas)
-- ============================================================================

-- ============================================================================
-- CONFIGURAÇÕES GERAIS
-- ============================================================================

-- Dados da Empresa
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_nome', 'Le Cortine', 'texto', 'geral', 'Nome da empresa')
ON DUPLICATE KEY UPDATE `valor` = 'Le Cortine';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_cnpj', '', 'texto', 'geral', 'CNPJ da empresa')
ON DUPLICATE KEY UPDATE `descricao` = 'CNPJ da empresa';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_telefone', '(75) 98889-0006', 'texto', 'geral', 'Telefone principal')
ON DUPLICATE KEY UPDATE `valor` = '(75) 98889-0006';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_whatsapp', '5575988890006', 'texto', 'geral', 'WhatsApp (com DDI e DDD)')
ON DUPLICATE KEY UPDATE `valor` = '5575988890006';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_email', 'contato@lecortine.com.br', 'texto', 'geral', 'E-mail de contato')
ON DUPLICATE KEY UPDATE `valor` = 'contato@lecortine.com.br';

-- Endereço para Cálculo de Frete
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_cep', '', 'texto', 'geral', 'CEP da empresa (origem do frete)')
ON DUPLICATE KEY UPDATE `descricao` = 'CEP da empresa (origem do frete)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_endereco', '', 'texto', 'geral', 'Endereço completo')
ON DUPLICATE KEY UPDATE `descricao` = 'Endereço completo';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_cidade', '', 'texto', 'geral', 'Cidade')
ON DUPLICATE KEY UPDATE `descricao` = 'Cidade';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('empresa_estado', '', 'texto', 'geral', 'Estado (UF)')
ON DUPLICATE KEY UPDATE `descricao` = 'Estado (UF)';

-- Opções de Entrega
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('retirada_local_ativa', '1', 'booleano', 'geral', 'Permitir retirada no local')
ON DUPLICATE KEY UPDATE `valor` = '1';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('retirada_local_endereco', '', 'texto', 'geral', 'Endereço para retirada')
ON DUPLICATE KEY UPDATE `descricao` = 'Endereço para retirada';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('frete_gratis_acima', '0', 'numero', 'geral', 'Valor mínimo para frete grátis (0 = desabilitado)')
ON DUPLICATE KEY UPDATE `descricao` = 'Valor mínimo para frete grátis (0 = desabilitado)';

-- ============================================================================
-- CONFIGURAÇÕES CORREIOS
-- ============================================================================

-- Credenciais
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_ativo', '0', 'booleano', 'correios', 'Ativar integração com Correios')
ON DUPLICATE KEY UPDATE `descricao` = 'Ativar integração com Correios';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_ambiente', 'teste', 'texto', 'correios', 'Ambiente: teste ou producao')
ON DUPLICATE KEY UPDATE `descricao` = 'Ambiente: teste ou producao';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_usuario', '', 'texto', 'correios', 'Usuário dos Correios (Código Administrativo)')
ON DUPLICATE KEY UPDATE `descricao` = 'Usuário dos Correios (Código Administrativo)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_senha', '', 'texto', 'correios', 'Senha dos Correios')
ON DUPLICATE KEY UPDATE `descricao` = 'Senha dos Correios';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_contrato', '', 'texto', 'correios', 'Número do contrato')
ON DUPLICATE KEY UPDATE `descricao` = 'Número do contrato';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_cartao_postagem', '', 'texto', 'correios', 'Cartão de postagem')
ON DUPLICATE KEY UPDATE `descricao` = 'Cartão de postagem';

-- Configurações de Cálculo
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_servicos', 'PAC,SEDEX', 'texto', 'correios', 'Serviços disponíveis (separados por vírgula)')
ON DUPLICATE KEY UPDATE `valor` = 'PAC,SEDEX';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_prazo_adicional', '0', 'numero', 'correios', 'Dias adicionais ao prazo')
ON DUPLICATE KEY UPDATE `descricao` = 'Dias adicionais ao prazo';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_valor_adicional', '0', 'numero', 'correios', 'Valor adicional ao frete (R$)')
ON DUPLICATE KEY UPDATE `descricao` = 'Valor adicional ao frete (R$)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_percentual_adicional', '0', 'numero', 'correios', 'Percentual adicional ao frete (%)')
ON DUPLICATE KEY UPDATE `descricao` = 'Percentual adicional ao frete (%)';

-- Configurações de Pacote
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_formato', '1', 'numero', 'correios', 'Formato: 1=Caixa/Pacote, 2=Rolo/Prisma, 3=Envelope')
ON DUPLICATE KEY UPDATE `descricao` = 'Formato: 1=Caixa/Pacote, 2=Rolo/Prisma, 3=Envelope';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_peso_padrao', '1', 'numero', 'correios', 'Peso padrão por m² (kg)')
ON DUPLICATE KEY UPDATE `descricao` = 'Peso padrão por m² (kg)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_comprimento', '30', 'numero', 'correios', 'Comprimento padrão (cm)')
ON DUPLICATE KEY UPDATE `valor` = '30';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_largura', '20', 'numero', 'correios', 'Largura padrão (cm)')
ON DUPLICATE KEY UPDATE `valor` = '20';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_altura', '10', 'numero', 'correios', 'Altura padrão (cm)')
ON DUPLICATE KEY UPDATE `valor` = '10';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_mao_propria', '0', 'booleano', 'correios', 'Mão própria')
ON DUPLICATE KEY UPDATE `descricao` = 'Mão própria';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_aviso_recebimento', '0', 'booleano', 'correios', 'Aviso de recebimento')
ON DUPLICATE KEY UPDATE `descricao` = 'Aviso de recebimento';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('correios_valor_declarado', '0', 'booleano', 'correios', 'Declarar valor da mercadoria')
ON DUPLICATE KEY UPDATE `descricao` = 'Declarar valor da mercadoria';

-- ============================================================================
-- CONFIGURAÇÕES MERCADO PAGO
-- ============================================================================

-- Credenciais
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_ativo', '0', 'booleano', 'mercadopago', 'Ativar integração com Mercado Pago')
ON DUPLICATE KEY UPDATE `descricao` = 'Ativar integração com Mercado Pago';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_ambiente', 'teste', 'texto', 'mercadopago', 'Ambiente: teste ou producao')
ON DUPLICATE KEY UPDATE `descricao` = 'Ambiente: teste ou producao';

-- Credenciais de Teste
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_public_key_teste', '', 'texto', 'mercadopago', 'Public Key (Teste)')
ON DUPLICATE KEY UPDATE `descricao` = 'Public Key (Teste)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_access_token_teste', '', 'texto', 'mercadopago', 'Access Token (Teste)')
ON DUPLICATE KEY UPDATE `descricao` = 'Access Token (Teste)';

-- Credenciais de Produção
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_public_key_prod', '', 'texto', 'mercadopago', 'Public Key (Produção)')
ON DUPLICATE KEY UPDATE `descricao` = 'Public Key (Produção)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_access_token_prod', '', 'texto', 'mercadopago', 'Access Token (Produção)')
ON DUPLICATE KEY UPDATE `descricao` = 'Access Token (Produção)';

-- Configurações de Pagamento
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_metodos', 'credit_card,debit_card,pix', 'texto', 'mercadopago', 'Métodos de pagamento aceitos')
ON DUPLICATE KEY UPDATE `valor` = 'credit_card,debit_card,pix';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_max_parcelas', '12', 'numero', 'mercadopago', 'Número máximo de parcelas')
ON DUPLICATE KEY UPDATE `valor` = '12';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_parcela_minima', '5', 'numero', 'mercadopago', 'Valor mínimo da parcela (R$)')
ON DUPLICATE KEY UPDATE `valor` = '5';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_juros', '0', 'numero', 'mercadopago', 'Taxa de juros (% ao mês)')
ON DUPLICATE KEY UPDATE `descricao` = 'Taxa de juros (% ao mês)';

-- URLs de Retorno
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_url_sucesso', '', 'texto', 'mercadopago', 'URL de retorno (sucesso)')
ON DUPLICATE KEY UPDATE `descricao` = 'URL de retorno (sucesso)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_url_pendente', '', 'texto', 'mercadopago', 'URL de retorno (pendente)')
ON DUPLICATE KEY UPDATE `descricao` = 'URL de retorno (pendente)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_url_falha', '', 'texto', 'mercadopago', 'URL de retorno (falha)')
ON DUPLICATE KEY UPDATE `descricao` = 'URL de retorno (falha)';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_url_webhook', '', 'texto', 'mercadopago', 'URL para webhook (notificações)')
ON DUPLICATE KEY UPDATE `descricao` = 'URL para webhook (notificações)';

-- Configurações Adicionais
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_statement_descriptor', 'LE CORTINE', 'texto', 'mercadopago', 'Nome na fatura do cartão')
ON DUPLICATE KEY UPDATE `valor` = 'LE CORTINE';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_auto_return', '1', 'booleano', 'mercadopago', 'Retorno automático após pagamento')
ON DUPLICATE KEY UPDATE `descricao` = 'Retorno automático após pagamento';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('mercadopago_binary_mode', '0', 'booleano', 'mercadopago', 'Modo binário (aprovado/rejeitado apenas)')
ON DUPLICATE KEY UPDATE `descricao` = 'Modo binário (aprovado/rejeitado apenas)';

-- ============================================================================
-- CONFIGURAÇÕES DE NOTIFICAÇÕES
-- ============================================================================

-- E-mail
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('email_notificacoes_ativo', '1', 'booleano', 'notificacoes', 'Enviar e-mails de notificação')
ON DUPLICATE KEY UPDATE `descricao` = 'Enviar e-mails de notificação';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('email_novo_orcamento', '1', 'booleano', 'notificacoes', 'Notificar novos orçamentos')
ON DUPLICATE KEY UPDATE `descricao` = 'Notificar novos orçamentos';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('email_pagamento_aprovado', '1', 'booleano', 'notificacoes', 'Notificar pagamentos aprovados')
ON DUPLICATE KEY UPDATE `descricao` = 'Notificar pagamentos aprovados';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('email_destinatario', 'contato@lecortine.com.br', 'texto', 'notificacoes', 'E-mail para receber notificações')
ON DUPLICATE KEY UPDATE `valor` = 'contato@lecortine.com.br';

-- WhatsApp
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('whatsapp_notificacoes_ativo', '1', 'booleano', 'notificacoes', 'Enviar notificações via WhatsApp')
ON DUPLICATE KEY UPDATE `descricao` = 'Enviar notificações via WhatsApp';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('whatsapp_numero_notificacao', '5575988890006', 'texto', 'notificacoes', 'Número para receber notificações')
ON DUPLICATE KEY UPDATE `valor` = '5575988890006';

-- Notificações no Sistema
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('notif_sistema_novo_orcamento', '1', 'booleano', 'notificacoes', 'Notificar novos orçamentos no sistema')
ON DUPLICATE KEY UPDATE `descricao` = 'Notificar novos orçamentos no sistema';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('notif_sistema_pagamento', '1', 'booleano', 'notificacoes', 'Notificar novos pagamentos no sistema')
ON DUPLICATE KEY UPDATE `descricao` = 'Notificar novos pagamentos no sistema';

INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
('notif_sistema_som', '1', 'booleano', 'notificacoes', 'Tocar som nas notificações')
ON DUPLICATE KEY UPDATE `descricao` = 'Tocar som nas notificações';

-- ============================================================================
-- SUCESSO!
-- ============================================================================
SELECT 'Configurações atualizadas com sucesso!' AS Mensagem;
