-- ============================================================================
-- SISTEMA DE AGENDAMENTO SAAS - BANCO DE DADOS
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 05/12/2024 23:30
-- Descrição: Estrutura completa do banco de dados para sistema SaaS de
--            agendamento para salões de beleza, barbearias e similares
--
-- Funcionalidades:
-- - Multi-tenant (estabelecimentos)
-- - Gestão de profissionais e serviços
-- - Sistema de agendamentos com disponibilidade
-- - Integração com Mercado Pago (pagamentos)
-- - Integração com Evolution API (WhatsApp)
-- - Sistema de promoções e avaliações
-- - Notificações personalizáveis
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- TABELA: estabelecimentos
-- Descrição: Cadastro de estabelecimentos (multi-tenant)
-- ============================================================================

CREATE TABLE `estabelecimentos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `cnpj_cpf` varchar(18) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `plano` enum('trimestral','semestral','anual') DEFAULT 'trimestral',
  `plano_vencimento` date DEFAULT NULL,
  `status` enum('ativo','inativo','suspenso') DEFAULT 'ativo',
  `tempo_minimo_agendamento` int(11) DEFAULT 60 COMMENT 'Minutos antes do serviço',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cnpj_cpf` (`cnpj_cpf`),
  KEY `idx_status` (`status`),
  KEY `idx_plano_vencimento` (`plano_vencimento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: profissionais
-- Descrição: Cadastro de profissionais vinculados aos estabelecimentos
-- ============================================================================

CREATE TABLE `profissionais` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_profissionais_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: servicos
-- Descrição: Serviços oferecidos pelos estabelecimentos
-- ============================================================================

CREATE TABLE `servicos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `duracao` int(11) NOT NULL COMMENT 'Duração em minutos',
  `preco` decimal(10,2) NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_servicos_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: profissional_servicos
-- Descrição: Relacionamento N:N entre profissionais e serviços
-- ============================================================================

CREATE TABLE `profissional_servicos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `servico_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_profissional_servico` (`profissional_id`,`servico_id`),
  KEY `fk_ps_servico` (`servico_id`),
  CONSTRAINT `fk_ps_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ps_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: clientes
-- Descrição: Cadastro de clientes dos estabelecimentos
-- ============================================================================

CREATE TABLE `clientes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tipo` enum('novo','recorrente','vip') DEFAULT 'novo',
  `total_agendamentos` int(11) DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  KEY `idx_whatsapp` (`whatsapp`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  CONSTRAINT `fk_clientes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: agendamentos
-- Descrição: Registro de todos os agendamentos
-- ============================================================================

CREATE TABLE `agendamentos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `servico_id` int(11) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `status` enum('pendente','confirmado','cancelado','reagendado','finalizado') DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `cancelado_por` enum('cliente','profissional','admin','sistema') DEFAULT NULL,
  `motivo_cancelamento` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_data` (`data`),
  KEY `idx_profissional_data` (`profissional_id`,`data`),
  KEY `idx_status` (`status`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_servico` (`servico_id`),
  CONSTRAINT `fk_agendamentos_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agendamentos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agendamentos_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agendamentos_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: disponibilidade
-- Descrição: Horários de disponibilidade dos profissionais
-- ============================================================================

CREATE TABLE `disponibilidade` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT '0=Domingo, 6=Sábado',
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_profissional` (`profissional_id`),
  CONSTRAINT `fk_disponibilidade_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: bloqueios
-- Descrição: Bloqueios de horários dos profissionais
-- ============================================================================

CREATE TABLE `bloqueios` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `dia_todo` tinyint(1) DEFAULT 0,
  `motivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_profissional_data` (`profissional_id`,`data`),
  CONSTRAINT `fk_bloqueios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: pagamentos
-- Descrição: Registro de pagamentos dos agendamentos
-- ============================================================================

CREATE TABLE `pagamentos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agendamento_id` int(11) UNSIGNED NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `tipo` enum('pix','cartao','dinheiro','outro') NOT NULL,
  `forma_pagamento` enum('pre_agendamento','total') DEFAULT 'total',
  `status` enum('pendente','aprovado','recusado','cancelado') DEFAULT 'pendente',
  `mercadopago_id` varchar(100) DEFAULT NULL,
  `mercadopago_payment_id` varchar(100) DEFAULT NULL,
  `link_pagamento` text DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `pago_em` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_mercadopago_id` (`mercadopago_id`),
  KEY `idx_agendamento` (`agendamento_id`),
  CONSTRAINT `fk_pagamentos_agendamento` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: avaliacoes
-- Descrição: Avaliações dos clientes sobre os serviços
-- ============================================================================

CREATE TABLE `avaliacoes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agendamento_id` int(11) UNSIGNED NOT NULL,
  `nota` tinyint(4) NOT NULL COMMENT '1 a 5 estrelas',
  `comentario` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_nota` (`nota`),
  KEY `idx_agendamento` (`agendamento_id`),
  CONSTRAINT `fk_avaliacoes_agendamento` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: promocoes
-- Descrição: Promoções e descontos configuráveis
-- ============================================================================

CREATE TABLE `promocoes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('percentual','valor_fixo') NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `tipo_cliente` enum('todos','novo','recorrente','vip') DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_datas` (`data_inicio`,`data_fim`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  CONSTRAINT `fk_promocoes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: whatsapp_sessoes
-- Descrição: Sessões de WhatsApp dos estabelecimentos (Evolution API)
-- ============================================================================

CREATE TABLE `whatsapp_sessoes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL,
  `instance_name` varchar(100) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `status` enum('desconectado','conectado','erro') DEFAULT 'desconectado',
  `ultima_conexao` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_numero` (`numero`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  CONSTRAINT `fk_whatsapp_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: whatsapp_conversas
-- Descrição: Controle de conversas/sessões do bot de agendamento
-- ============================================================================

CREATE TABLE `whatsapp_conversas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) UNSIGNED DEFAULT NULL,
  `whatsapp_numero` varchar(20) NOT NULL,
  `etapa` varchar(50) DEFAULT NULL COMMENT 'servico, profissional, data, hora, confirmacao',
  `dados_temporarios` json DEFAULT NULL COMMENT 'Armazena escolhas durante a conversa',
  `ultima_interacao` datetime DEFAULT NULL,
  `expira_em` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_whatsapp` (`whatsapp_numero`),
  KEY `idx_expira` (`expira_em`),
  KEY `idx_cliente` (`cliente_id`),
  CONSTRAINT `fk_conversas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: notificacoes_config
-- Descrição: Configuração de templates de notificações
-- ============================================================================

CREATE TABLE `notificacoes_config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `tipo` enum('confirmacao','cancelamento','reagendamento','lembrete_1dia','lembrete_1hora','pagamento','feedback') NOT NULL,
  `template` text NOT NULL COMMENT 'Template com variáveis: {cliente}, {servico}, {data}, {hora}, etc',
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_estabelecimento_tipo` (`estabelecimento_id`,`tipo`),
  CONSTRAINT `fk_notificacoes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DADOS INICIAIS
-- ============================================================================

-- Inserir templates padrão de notificações (serão criados automaticamente ao cadastrar estabelecimento)

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- FIM DO SCRIPT
-- ============================================================================
--
-- INSTRUÇÕES DE USO:
-- 1. Crie um novo banco de dados no phpMyAdmin
-- 2. Importe este arquivo SQL
-- 3. O sistema já possui a tabela 'usuarios' da base anterior
-- 4. Execute as migrações conforme necessário
--
-- ============================================================================
