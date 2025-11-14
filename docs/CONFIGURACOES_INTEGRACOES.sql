-- ============================================================================
-- CONFIGURAÇÕES DE INTEGRAÇÕES - LE CORTINE
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 14/11/2024
-- Descrição: Configurações para Correios e Mercado Pago
-- ============================================================================

-- ============================================================================
-- CONFIGURAÇÕES GERAIS
-- ============================================================================
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
-- Dados da Empresa
('empresa_nome', 'Le Cortine', 'texto', 'geral', 'Nome da empresa'),
('empresa_cnpj', '', 'texto', 'geral', 'CNPJ da empresa'),
('empresa_telefone', '(75) 98889-0006', 'texto', 'geral', 'Telefone principal'),
('empresa_whatsapp', '5575988890006', 'texto', 'geral', 'WhatsApp (com DDI e DDD)'),
('empresa_email', 'contato@lecortine.com.br', 'texto', 'geral', 'E-mail de contato'),

-- Endereço para Cálculo de Frete
('empresa_cep', '', 'texto', 'geral', 'CEP da empresa (origem do frete)'),
('empresa_endereco', '', 'texto', 'geral', 'Endereço completo'),
('empresa_cidade', '', 'texto', 'geral', 'Cidade'),
('empresa_estado', '', 'texto', 'geral', 'Estado (UF)'),

-- Opções de Entrega
('retirada_local_ativa', '1', 'booleano', 'geral', 'Permitir retirada no local'),
('retirada_local_endereco', '', 'texto', 'geral', 'Endereço para retirada'),
('frete_gratis_acima', '0', 'numero', 'geral', 'Valor mínimo para frete grátis (0 = desabilitado)');

-- ============================================================================
-- CONFIGURAÇÕES CORREIOS
-- ============================================================================
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
-- Credenciais
('correios_ativo', '0', 'booleano', 'correios', 'Ativar integração com Correios'),
('correios_ambiente', 'teste', 'texto', 'correios', 'Ambiente: teste ou producao'),
('correios_usuario', '', 'texto', 'correios', 'Usuário dos Correios (Código Administrativo)'),
('correios_senha', '', 'texto', 'correios', 'Senha dos Correios'),
('correios_contrato', '', 'texto', 'correios', 'Número do contrato'),
('correios_cartao_postagem', '', 'texto', 'correios', 'Cartão de postagem'),

-- Configurações de Cálculo
('correios_servicos', 'PAC,SEDEX', 'texto', 'correios', 'Serviços disponíveis (separados por vírgula)'),
('correios_prazo_adicional', '0', 'numero', 'correios', 'Dias adicionais ao prazo'),
('correios_valor_adicional', '0', 'numero', 'correios', 'Valor adicional ao frete (R$)'),
('correios_percentual_adicional', '0', 'numero', 'correios', 'Percentual adicional ao frete (%)'),

-- Configurações de Pacote
('correios_formato', '1', 'numero', 'correios', 'Formato: 1=Caixa/Pacote, 2=Rolo/Prisma, 3=Envelope'),
('correios_peso_padrao', '1', 'numero', 'correios', 'Peso padrão por m² (kg)'),
('correios_comprimento', '30', 'numero', 'correios', 'Comprimento padrão (cm)'),
('correios_largura', '20', 'numero', 'correios', 'Largura padrão (cm)'),
('correios_altura', '10', 'numero', 'correios', 'Altura padrão (cm)'),
('correios_mao_propria', '0', 'booleano', 'correios', 'Mão própria'),
('correios_aviso_recebimento', '0', 'booleano', 'correios', 'Aviso de recebimento'),
('correios_valor_declarado', '0', 'booleano', 'correios', 'Declarar valor da mercadoria');

-- ============================================================================
-- CONFIGURAÇÕES MERCADO PAGO
-- ============================================================================
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
-- Credenciais
('mercadopago_ativo', '0', 'booleano', 'mercadopago', 'Ativar integração com Mercado Pago'),
('mercadopago_ambiente', 'teste', 'texto', 'mercadopago', 'Ambiente: teste ou producao'),

-- Credenciais de Teste
('mercadopago_public_key_teste', '', 'texto', 'mercadopago', 'Public Key (Teste)'),
('mercadopago_access_token_teste', '', 'texto', 'mercadopago', 'Access Token (Teste)'),

-- Credenciais de Produção
('mercadopago_public_key_prod', '', 'texto', 'mercadopago', 'Public Key (Produção)'),
('mercadopago_access_token_prod', '', 'texto', 'mercadopago', 'Access Token (Produção)'),

-- Configurações de Pagamento
('mercadopago_metodos', 'credit_card,debit_card,pix', 'texto', 'mercadopago', 'Métodos de pagamento aceitos'),
('mercadopago_max_parcelas', '12', 'numero', 'mercadopago', 'Número máximo de parcelas'),
('mercadopago_parcela_minima', '5', 'numero', 'mercadopago', 'Valor mínimo da parcela (R$)'),
('mercadopago_juros', '0', 'numero', 'mercadopago', 'Taxa de juros (% ao mês)'),

-- URLs de Retorno
('mercadopago_url_sucesso', '', 'texto', 'mercadopago', 'URL de retorno (sucesso)'),
('mercadopago_url_pendente', '', 'texto', 'mercadopago', 'URL de retorno (pendente)'),
('mercadopago_url_falha', '', 'texto', 'mercadopago', 'URL de retorno (falha)'),
('mercadopago_url_webhook', '', 'texto', 'mercadopago', 'URL para webhook (notificações)'),

-- Configurações Adicionais
('mercadopago_statement_descriptor', 'LE CORTINE', 'texto', 'mercadopago', 'Nome na fatura do cartão'),
('mercadopago_auto_return', '1', 'booleano', 'mercadopago', 'Retorno automático após pagamento'),
('mercadopago_binary_mode', '0', 'booleano', 'mercadopago', 'Modo binário (aprovado/rejeitado apenas)');

-- ============================================================================
-- CONFIGURAÇÕES DE NOTIFICAÇÕES
-- ============================================================================
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
-- E-mail
('email_notificacoes_ativo', '1', 'booleano', 'notificacoes', 'Enviar e-mails de notificação'),
('email_novo_orcamento', '1', 'booleano', 'notificacoes', 'Notificar novos orçamentos'),
('email_pagamento_aprovado', '1', 'booleano', 'notificacoes', 'Notificar pagamentos aprovados'),
('email_destinatario', 'contato@lecortine.com.br', 'texto', 'notificacoes', 'E-mail para receber notificações'),

-- WhatsApp
('whatsapp_notificacoes_ativo', '1', 'booleano', 'notificacoes', 'Enviar notificações via WhatsApp'),
('whatsapp_numero_notificacao', '5575988890006', 'texto', 'notificacoes', 'Número para receber notificações');

-- ============================================================================
-- OBSERVAÇÕES
-- ============================================================================
-- 
-- CORREIOS:
-- Para obter credenciais dos Correios, acesse:
-- https://www.correios.com.br/enviar/precisa-de-ajuda/contrato-nacional
-- 
-- Você precisará:
-- 1. Ter um contrato com os Correios
-- 2. Código Administrativo (usuário)
-- 3. Senha de acesso ao webservice
-- 4. Número do contrato
-- 5. Cartão de postagem
-- 
-- MERCADO PAGO:
-- Para obter credenciais do Mercado Pago, acesse:
-- https://www.mercadopago.com.br/developers
-- 
-- Você precisará:
-- 1. Criar uma conta no Mercado Pago
-- 2. Acessar "Suas integrações" > "Credenciais"
-- 3. Copiar Public Key e Access Token (teste e produção)
-- 
-- IMPORTANTE:
-- - Nunca compartilhe suas credenciais
-- - Use ambiente de teste antes de produção
-- - Configure SSL/HTTPS para produção
-- - Teste todas as integrações antes de ativar
-- 
-- ============================================================================
