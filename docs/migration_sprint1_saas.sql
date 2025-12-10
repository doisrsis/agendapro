-- ============================================================================
-- SPRINT 1: Migração para Arquitetura SaaS Multi-Tenant
-- AgendaPro - Sistema de Agendamento
--
-- Autor: Rafael Dias - doisr.com.br
-- Data: 09/12/2024
--
-- ATENÇÃO: Faça backup completo do banco antes de executar!
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================================
-- PARTE 1: CRIAR NOVAS TABELAS
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Tabela: planos
-- Descrição: Planos de assinatura disponíveis
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `planos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `max_profissionais` int(11) DEFAULT 1,
  `max_agendamentos_mes` int(11) DEFAULT 100,
  `recursos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recursos`)),
  `ativo` tinyint(1) DEFAULT 1,
  `ordem` int(11) DEFAULT 0,
  `trial_dias` int(11) DEFAULT 7,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_ativo` (`ativo`),
  KEY `idx_ordem` (`ordem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir planos padrão
INSERT INTO `planos` (`nome`, `slug`, `descricao`, `valor_mensal`, `max_profissionais`, `max_agendamentos_mes`, `recursos`, `ordem`, `trial_dias`) VALUES
('Autônomo', 'autonomo', 'Ideal para profissionais independentes', 29.90, 1, 100,
 '{"whatsapp": true, "mercadopago": true, "relatorios_basicos": true, "suporte": "email"}', 1, 7),
('Básico', 'basico', 'Para pequenos estabelecimentos', 79.90, 3, 300,
 '{"whatsapp": true, "mercadopago": true, "relatorios_basicos": true, "multi_profissionais": true, "suporte": "email"}', 2, 7),
('Profissional', 'profissional', 'Para estabelecimentos em crescimento', 149.90, 10, 1000,
 '{"whatsapp": true, "mercadopago": true, "relatorios_avancados": true, "multi_profissionais": true, "api_acesso": true, "suporte": "chat"}', 3, 7),
('Premium', 'premium', 'Recursos ilimitados', 299.90, 999, 999999,
 '{"whatsapp": true, "mercadopago": true, "relatorios_avancados": true, "multi_profissionais": true, "api_acesso": true, "suporte_prioritario": true, "personalizacao": true, "suporte": "telefone"}', 4, 7);

-- ----------------------------------------------------------------------------
-- Tabela: assinaturas
-- Descrição: Controle de assinaturas dos estabelecimentos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `assinaturas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `plano_id` int(11) UNSIGNED NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('ativa','trial','cancelada','vencida','suspensa') DEFAULT 'trial',
  `mercadopago_subscription_id` varchar(100) DEFAULT NULL,
  `mercadopago_preapproval_id` varchar(100) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `auto_renovar` tinyint(1) DEFAULT 1,
  `cancelada_em` datetime DEFAULT NULL,
  `motivo_cancelamento` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_plano` (`plano_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data_fim` (`data_fim`),
  KEY `idx_mercadopago` (`mercadopago_subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabela: templates_notificacao
-- Descrição: Templates personalizados de notificações por estabelecimento
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `templates_notificacao` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `tipo` enum('confirmacao','lembrete','cancelamento','reagendamento','boas_vindas','pagamento','feedback') NOT NULL,
  `canal` enum('whatsapp','email','sms') NOT NULL,
  `assunto` varchar(255) DEFAULT NULL COMMENT 'Apenas para email',
  `mensagem` text NOT NULL,
  `variaveis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variaveis`)),
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_estabelecimento_tipo_canal` (`estabelecimento_id`,`tipo`,`canal`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_canal` (`canal`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PARTE 2: MODIFICAR TABELA USUARIOS (Adicionar campos multi-tenant)
-- ============================================================================

-- Renomear tabela atual para backup
RENAME TABLE `usuarios` TO `usuarios_backup`;

-- Criar nova tabela usuarios com estrutura multi-tenant
CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('super_admin','estabelecimento','profissional') NOT NULL DEFAULT 'estabelecimento',
  `estabelecimento_id` int(11) UNSIGNED DEFAULT NULL,
  `profissional_id` int(11) UNSIGNED DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `primeiro_acesso` tinyint(1) DEFAULT 1,
  `token_reset_senha` varchar(100) DEFAULT NULL,
  `token_expiracao` datetime DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_profissional` (`profissional_id`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrar usuários existentes como super_admin
INSERT INTO `usuarios` (`id`, `email`, `senha`, `tipo`, `nome`, `telefone`, `avatar`, `ativo`, `ultimo_acesso`, `criado_em`, `atualizado_em`)
SELECT
  `id`,
  `email`,
  `senha`,
  'super_admin' as tipo,
  `nome`,
  `telefone`,
  `avatar`,
  IF(`status` = 'ativo', 1, 0) as ativo,
  `ultimo_acesso`,
  `criado_em`,
  `atualizado_em`
FROM `usuarios_backup`;

-- ============================================================================
-- PARTE 3: MODIFICAR TABELA ESTABELECIMENTOS
-- ============================================================================

-- Adicionar novos campos
ALTER TABLE `estabelecimentos`
-- Relacionamentos
ADD COLUMN `usuario_id` int(11) UNSIGNED DEFAULT NULL AFTER `id`,
ADD COLUMN `plano_id` int(11) UNSIGNED DEFAULT NULL AFTER `usuario_id`,

-- Configurações Mercado Pago (INDIVIDUAIS)
ADD COLUMN `mp_access_token_test` varchar(255) DEFAULT NULL AFTER `plano_id`,
ADD COLUMN `mp_public_key_test` varchar(255) DEFAULT NULL AFTER `mp_access_token_test`,
ADD COLUMN `mp_access_token_prod` varchar(255) DEFAULT NULL AFTER `mp_public_key_test`,
ADD COLUMN `mp_public_key_prod` varchar(255) DEFAULT NULL AFTER `mp_access_token_prod`,
ADD COLUMN `mp_webhook_url` varchar(255) DEFAULT NULL AFTER `mp_public_key_prod`,
ADD COLUMN `mp_sandbox` tinyint(1) DEFAULT 1 AFTER `mp_webhook_url`,

-- Configurações WhatsApp/Evolution API (INDIVIDUAIS)
ADD COLUMN `evolution_api_url` varchar(255) DEFAULT NULL AFTER `mp_sandbox`,
ADD COLUMN `evolution_api_key` varchar(255) DEFAULT NULL AFTER `evolution_api_url`,
ADD COLUMN `evolution_instance_name` varchar(100) DEFAULT NULL AFTER `evolution_api_key`,
ADD COLUMN `whatsapp_numero` varchar(20) DEFAULT NULL AFTER `evolution_instance_name`,
ADD COLUMN `whatsapp_conectado` tinyint(1) DEFAULT 0 AFTER `whatsapp_numero`,

-- Configurações de Notificações
ADD COLUMN `notificar_whatsapp` tinyint(1) DEFAULT 1 AFTER `whatsapp_conectado`,
ADD COLUMN `notificar_email` tinyint(1) DEFAULT 1 AFTER `notificar_whatsapp`,

-- Controle
ADD COLUMN `data_cadastro` datetime DEFAULT current_timestamp() AFTER `notificar_email`;

-- Modificar campo status
ALTER TABLE `estabelecimentos`
MODIFY COLUMN `status` enum('ativo','inativo','suspenso','cancelado') DEFAULT 'ativo';

-- Adicionar índices
ALTER TABLE `estabelecimentos`
ADD INDEX `idx_usuario` (`usuario_id`),
ADD INDEX `idx_plano` (`plano_id`);

-- ============================================================================
-- PARTE 4: MODIFICAR TABELA PROFISSIONAIS
-- ============================================================================

ALTER TABLE `profissionais`
ADD COLUMN `usuario_id` int(11) UNSIGNED DEFAULT NULL AFTER `id`,
ADD COLUMN `tipo` enum('vinculado','autonomo') DEFAULT 'vinculado' AFTER `usuario_id`,
ADD INDEX `idx_usuario` (`usuario_id`),
ADD INDEX `idx_tipo` (`tipo`);

-- ============================================================================
-- PARTE 5: ADICIONAR FOREIGN KEYS
-- ============================================================================

-- Assinaturas
ALTER TABLE `assinaturas`
ADD CONSTRAINT `fk_assinaturas_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_assinaturas_plano` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`) ON DELETE RESTRICT;

-- Templates Notificação
ALTER TABLE `templates_notificacao`
ADD CONSTRAINT `fk_templates_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

-- Estabelecimentos
ALTER TABLE `estabelecimentos`
ADD CONSTRAINT `fk_estabelecimentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_estabelecimentos_plano` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`) ON DELETE SET NULL;

-- Profissionais
ALTER TABLE `profissionais`
ADD CONSTRAINT `fk_profissionais_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

-- Usuários
ALTER TABLE `usuarios`
ADD CONSTRAINT `fk_usuarios_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_usuarios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

-- ============================================================================
-- PARTE 6: MIGRAR DADOS EXISTENTES
-- ============================================================================

-- Criar assinatura trial para estabelecimento existente
INSERT INTO `assinaturas` (`estabelecimento_id`, `plano_id`, `data_inicio`, `data_fim`, `status`, `valor_pago`)
SELECT
  e.id,
  (SELECT id FROM planos WHERE slug = 'basico' LIMIT 1) as plano_id,
  CURDATE() as data_inicio,
  DATE_ADD(CURDATE(), INTERVAL 30 DAY) as data_fim,
  'trial' as status,
  0.00 as valor_pago
FROM estabelecimentos e
WHERE NOT EXISTS (SELECT 1 FROM assinaturas WHERE estabelecimento_id = e.id);

-- Atualizar plano_id nos estabelecimentos
UPDATE estabelecimentos e
SET e.plano_id = (SELECT id FROM planos WHERE slug = 'basico' LIMIT 1)
WHERE e.plano_id IS NULL;

-- Migrar templates de notificações_config para templates_notificacao
INSERT INTO `templates_notificacao` (`estabelecimento_id`, `tipo`, `canal`, `mensagem`, `ativo`)
SELECT
  nc.estabelecimento_id,
  nc.tipo,
  'whatsapp' as canal,
  nc.template as mensagem,
  nc.ativo
FROM notificacoes_config nc
WHERE NOT EXISTS (
  SELECT 1 FROM templates_notificacao tn
  WHERE tn.estabelecimento_id = nc.estabelecimento_id
  AND tn.tipo = nc.tipo
  AND tn.canal = 'whatsapp'
);

-- ============================================================================
-- PARTE 7: MIGRAR CONFIGURAÇÕES MERCADO PAGO
-- ============================================================================

-- Migrar configurações globais do Mercado Pago para o estabelecimento
UPDATE estabelecimentos e
SET
  e.mp_access_token_test = (SELECT valor FROM configuracoes WHERE chave = 'mercadopago_access_token_test' LIMIT 1),
  e.mp_public_key_test = (SELECT valor FROM configuracoes WHERE chave = 'mercadopago_public_key_test' LIMIT 1),
  e.mp_access_token_prod = (SELECT valor FROM configuracoes WHERE chave = 'mercadopago_access_token_prod' LIMIT 1),
  e.mp_public_key_prod = (SELECT valor FROM configuracoes WHERE chave = 'mercadopago_public_key_prod' LIMIT 1),
  e.mp_webhook_url = (SELECT valor FROM configuracoes WHERE chave = 'mercadopago_webhook_url_prod' LIMIT 1),
  e.mp_sandbox = (SELECT IF(valor = '1', 1, 0) FROM configuracoes WHERE chave = 'mercadopago_sandbox' LIMIT 1)
WHERE e.id = 1; -- Apenas para o estabelecimento existente

-- ============================================================================
-- PARTE 8: LIMPEZA (OPCIONAL - COMENTADO POR SEGURANÇA)
-- ============================================================================

-- ATENÇÃO: Descomente apenas após confirmar que tudo está funcionando!

-- Remover tabela backup de usuários
-- DROP TABLE IF EXISTS `usuarios_backup`;

-- Remover configurações antigas do Mercado Pago (agora estão no estabelecimento)
-- DELETE FROM configuracoes WHERE chave LIKE 'mercadopago_%';

-- Remover tabela antiga de notificações_config (migrada para templates_notificacao)
-- DROP TABLE IF EXISTS `notificacoes_config`;

-- ============================================================================
-- PARTE 9: CRIAR ÍNDICES ADICIONAIS PARA PERFORMANCE
-- ============================================================================

-- Logs - melhorar performance de consultas
ALTER TABLE `logs`
ADD INDEX `idx_usuario_acao` (`usuario_id`, `acao`),
ADD INDEX `idx_tabela_registro` (`tabela`, `registro_id`);

-- Agendamentos - melhorar performance de consultas
ALTER TABLE `agendamentos`
ADD INDEX `idx_estabelecimento_data` (`estabelecimento_id`, `data`),
ADD INDEX `idx_profissional_status` (`profissional_id`, `status`);

-- Clientes - melhorar busca
ALTER TABLE `clientes`
ADD INDEX `idx_estabelecimento_tipo` (`estabelecimento_id`, `tipo`),
ADD INDEX `idx_email` (`email`);

COMMIT;

-- ============================================================================
-- FIM DA MIGRAÇÃO
-- ============================================================================

-- Verificar estrutura
SELECT 'Migração concluída com sucesso!' as status;
SELECT COUNT(*) as total_planos FROM planos;
SELECT COUNT(*) as total_usuarios FROM usuarios;
SELECT COUNT(*) as total_assinaturas FROM assinaturas;
