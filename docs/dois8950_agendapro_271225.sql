-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 27/12/2025 às 17:04
-- Versão do servidor: 10.11.14-MariaDB-cll-lve
-- Versão do PHP: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dois8950_agendapro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `estabelecimento_id`, `cliente_id`, `profissional_id`, `servico_id`, `data`, `hora_inicio`, `hora_fim`, `status`, `observacoes`, `cancelado_por`, `motivo_cancelamento`, `criado_em`, `atualizado_em`) VALUES
(1, 4, 3, 2, 2, '2025-12-12', '09:00:00', '09:06:00', 'cancelado', '', NULL, NULL, '2025-12-11 18:50:31', '2025-12-11 18:57:26'),
(2, 4, 3, 2, 3, '2025-12-12', '09:35:00', '09:51:00', 'cancelado', '', NULL, NULL, '2025-12-11 18:59:55', '2025-12-12 15:54:22'),
(3, 4, 3, 2, 2, '2025-12-13', '10:05:00', '10:11:00', 'confirmado', '', NULL, NULL, '2025-12-11 19:02:32', '2025-12-11 19:10:18'),
(4, 4, 3, 2, 2, '2025-12-17', '09:24:00', '09:30:00', 'confirmado', '', NULL, NULL, '2025-12-11 23:24:38', NULL),
(5, 4, 3, 2, 2, '2025-12-13', '14:20:00', '14:26:00', 'confirmado', '', NULL, NULL, '2025-12-12 10:13:32', NULL),
(6, 4, 3, 2, 2, '2025-12-12', '14:26:00', '14:32:00', 'confirmado', '', NULL, NULL, '2025-12-12 10:22:44', NULL),
(8, 4, 3, 2, 2, '2025-12-13', '11:30:00', '11:36:00', 'confirmado', '', NULL, NULL, '2025-12-12 10:33:00', NULL),
(9, 4, 3, 2, 2, '2025-12-13', '16:30:00', '16:36:00', 'confirmado', '', NULL, NULL, '2025-12-12 10:36:32', NULL),
(10, 4, 3, 2, 3, '2025-12-20', '14:42:00', '14:58:00', 'confirmado', '', NULL, NULL, '2025-12-12 10:38:57', NULL),
(11, 4, 3, 2, 2, '2025-12-17', '11:00:00', '11:06:00', 'pendente', NULL, NULL, NULL, '2025-12-12 16:44:14', NULL),
(12, 4, 3, 2, 3, '2025-12-25', '09:00:00', '09:16:00', 'pendente', NULL, NULL, NULL, '2025-12-23 07:41:11', NULL),
(13, 4, 3, 2, 3, '2025-12-25', '09:30:00', '09:46:00', 'confirmado', '', NULL, NULL, '2025-12-23 12:59:51', NULL),
(14, 4, 3, 2, 2, '2025-12-25', '14:00:00', '14:06:00', 'confirmado', '', NULL, NULL, '2025-12-23 13:02:43', NULL),
(15, 4, 3, 2, 3, '2025-12-25', '12:30:00', '12:46:00', 'confirmado', '', NULL, NULL, '2025-12-23 13:03:18', NULL),
(16, 4, 3, 2, 3, '2025-12-25', '17:25:00', '17:41:00', 'cancelado', '', NULL, NULL, '2025-12-23 13:06:47', '2025-12-23 15:09:26'),
(17, 4, 3, 2, 2, '2025-12-23', '13:20:00', '13:26:00', 'confirmado', '', NULL, NULL, '2025-12-23 13:08:29', NULL),
(18, 4, 3, 2, 2, '2025-12-24', '08:00:00', '08:16:00', 'pendente', NULL, NULL, NULL, '2025-12-23 20:01:18', NULL),
(19, 4, 3, 2, 2, '2025-12-24', '08:30:00', '08:46:00', 'pendente', NULL, NULL, NULL, '2025-12-23 20:02:37', NULL),
(20, 4, 3, 2, 2, '2025-12-24', '09:00:00', '09:20:00', 'pendente', NULL, NULL, NULL, '2025-12-23 21:00:40', NULL),
(21, 4, 3, 2, 3, '2025-12-25', '10:00:00', '10:25:00', 'pendente', NULL, NULL, NULL, '2025-12-23 21:01:16', NULL),
(22, 4, 3, 2, 2, '2025-12-24', '15:00:00', '15:20:00', 'pendente', NULL, NULL, NULL, '2025-12-23 21:11:10', NULL),
(23, 4, 3, 2, 2, '2025-12-24', '09:30:00', '09:50:00', 'pendente', NULL, NULL, NULL, '2025-12-23 21:11:24', NULL),
(24, 4, 3, 2, 3, '2025-12-29', '08:00:00', '08:25:00', 'pendente', NULL, NULL, NULL, '2025-12-23 21:12:09', NULL),
(25, 4, 3, 2, 2, '2025-12-29', '08:30:00', '08:50:00', 'pendente', NULL, NULL, NULL, '2025-12-23 22:24:11', NULL),
(26, 4, 3, 2, 2, '2025-12-29', '09:00:00', '09:20:00', 'pendente', NULL, NULL, NULL, '2025-12-26 10:04:06', NULL),
(27, 4, 3, 2, 3, '2025-12-29', '09:30:00', '09:55:00', 'pendente', NULL, NULL, NULL, '2025-12-26 10:04:31', NULL),
(28, 4, 3, 2, 3, '2025-12-29', '09:55:00', '10:20:00', 'pendente', NULL, NULL, NULL, '2025-12-26 16:33:28', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `assinaturas`
--

CREATE TABLE `assinaturas` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `assinaturas`
--

INSERT INTO `assinaturas` (`id`, `estabelecimento_id`, `plano_id`, `data_inicio`, `data_fim`, `status`, `mercadopago_subscription_id`, `mercadopago_preapproval_id`, `valor_pago`, `forma_pagamento`, `auto_renovar`, `cancelada_em`, `motivo_cancelamento`, `criado_em`, `atualizado_em`) VALUES
(2, 4, 5, '2025-12-10', '2026-01-17', 'ativa', '2026-01-17', NULL, 1.00, NULL, 1, NULL, NULL, '2025-12-10 17:06:18', '2025-12-18 13:35:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `agendamento_id` int(11) UNSIGNED NOT NULL,
  `nota` tinyint(4) NOT NULL COMMENT '1 a 5 estrelas',
  `comentario` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bloqueios`
--

CREATE TABLE `bloqueios` (
  `id` int(11) UNSIGNED NOT NULL,
  `profissional_id` int(11) UNSIGNED DEFAULT NULL,
  `tipo` enum('dia','periodo','horario') NOT NULL DEFAULT 'dia',
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `data` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `dia_todo` tinyint(1) DEFAULT 0,
  `motivo` varchar(255) DEFAULT NULL,
  `criado_por` enum('profissional','estabelecimento') DEFAULT 'profissional',
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `servico_id` int(11) DEFAULT NULL,
  `criado_por_tipo` enum('profissional','estabelecimento') DEFAULT NULL,
  `criado_por_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `bloqueios`
--

INSERT INTO `bloqueios` (`id`, `profissional_id`, `tipo`, `data_inicio`, `data_fim`, `data`, `hora_inicio`, `hora_fim`, `dia_todo`, `motivo`, `criado_por`, `criado_em`, `atualizado_em`, `servico_id`, `criado_por_tipo`, `criado_por_id`) VALUES
(7, 2, 'horario', '2025-12-13', '2025-12-13', '0000-00-00', '15:00:00', '16:00:00', 0, 'Preciso ir no centro', 'profissional', '2025-12-12 13:23:40', '2025-12-12 13:23:40', NULL, NULL, NULL),
(9, 2, 'periodo', '2025-12-18', '2025-12-19', '0000-00-00', NULL, NULL, 0, 'Folga', 'profissional', '2025-12-12 13:37:29', '2025-12-12 13:37:29', NULL, NULL, NULL),
(10, 2, 'dia', '2025-12-15', NULL, '0000-00-00', NULL, NULL, 0, 'areae', 'profissional', '2025-12-12 13:41:28', '2025-12-12 18:09:28', NULL, NULL, NULL),
(11, 2, 'dia', '2025-12-22', NULL, '0000-00-00', NULL, NULL, 0, 'Folga', 'profissional', '2025-12-12 18:48:01', '2025-12-12 18:48:01', NULL, NULL, NULL),
(13, NULL, 'dia', '2025-12-24', NULL, '0000-00-00', NULL, NULL, 0, '', 'profissional', '2025-12-13 12:36:06', '2025-12-13 12:36:06', 3, 'estabelecimento', 5),
(15, 2, 'horario', '2025-12-23', '2025-12-23', '0000-00-00', '09:00:00', '11:06:00', 0, '', 'profissional', '2025-12-13 12:43:06', '2025-12-13 12:43:06', 2, 'profissional', 6),
(17, 2, 'horario', '2025-12-25', '2025-12-25', '0000-00-00', '11:00:00', '13:00:00', 0, '', 'profissional', '2025-12-23 16:01:10', '2025-12-23 16:01:10', 2, 'profissional', 6),
(18, 2, 'horario', '2025-12-25', '2025-12-25', '0000-00-00', '14:00:00', '15:00:00', 0, '', 'profissional', '2025-12-23 16:01:40', '2025-12-23 16:01:40', 3, 'profissional', 6),
(19, 2, 'horario', '2025-12-25', '2025-12-25', '0000-00-00', '16:05:00', '17:06:00', 0, '', 'profissional', '2025-12-23 16:06:09', '2025-12-23 16:06:09', NULL, 'profissional', 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `bloqueios_backup`
--

CREATE TABLE `bloqueios_backup` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `dia_todo` tinyint(1) DEFAULT 0,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tipo` enum('novo','recorrente','vip') DEFAULT 'novo',
  `total_agendamentos` int(11) DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `estabelecimento_id`, `nome`, `cpf`, `whatsapp`, `email`, `foto`, `tipo`, `total_agendamentos`, `criado_em`, `atualizado_em`) VALUES
(3, 4, 'Mazinho', '', '(75) 99249-5077', '', NULL, 'vip', 28, '2025-12-11 13:46:45', '2025-12-26 16:33:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json','arquivo') DEFAULT 'texto',
  `grupo` varchar(50) DEFAULT 'geral' COMMENT 'Agrupa configurações (geral, smtp, notificacoes)',
  `descricao` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `grupo`, `descricao`, `criado_em`, `atualizado_em`) VALUES
(1, 'sistema_nome', 'iafila', 'texto', 'geral', 'Nome do sistema', '2025-12-06 02:15:45', '2025-12-06 14:23:21'),
(2, 'sistema_email', 'doisr.sistemas@gmail.com', 'texto', 'geral', 'E-mail principal do sistema', '2025-12-06 02:15:45', '2025-12-06 14:23:21'),
(3, 'sistema_telefone', '7599448068', 'texto', 'geral', 'Telefone de contato', '2025-12-06 02:15:45', '2025-12-06 14:23:21'),
(4, 'sistema_endereco', 'Rua Nova Brasília, 162', 'texto', 'geral', 'Endereço completo', '2025-12-06 02:15:45', '2025-12-06 14:23:21'),
(5, 'sistema_logo', '', 'arquivo', 'geral', 'Logo do sistema', '2025-12-06 02:15:45', NULL),
(6, 'sistema_favicon', '', 'arquivo', 'geral', 'Favicon do sistema', '2025-12-06 02:15:45', NULL),
(7, 'smtp_ativo', '0', 'booleano', 'smtp', 'Ativar envio de e-mails via SMTP', '2025-12-06 02:15:45', NULL),
(8, 'smtp_host', '', 'texto', 'smtp', 'Servidor SMTP (ex: smtp.gmail.com)', '2025-12-06 02:15:45', NULL),
(9, 'smtp_porta', '587', 'numero', 'smtp', 'Porta SMTP (587 para TLS, 465 para SSL)', '2025-12-06 02:15:45', NULL),
(10, 'smtp_usuario', '', 'texto', 'smtp', 'Usuário SMTP (e-mail)', '2025-12-06 02:15:45', NULL),
(11, 'smtp_senha', '', 'texto', 'smtp', 'Senha SMTP', '2025-12-06 02:15:45', NULL),
(12, 'smtp_seguranca', 'tls', 'texto', 'smtp', 'Tipo de segurança (tls ou ssl)', '2025-12-06 02:15:45', NULL),
(13, 'smtp_remetente_email', '', 'texto', 'smtp', 'E-mail do remetente', '2025-12-06 02:15:45', NULL),
(14, 'smtp_remetente_nome', '', 'texto', 'smtp', 'Nome do remetente', '2025-12-06 02:15:45', NULL),
(15, 'notif_email_ativo', '1', 'booleano', 'notificacoes', 'Enviar notificações por e-mail', '2025-12-06 02:15:45', NULL),
(16, 'notif_email_destinatario', '', 'texto', 'notificacoes', 'E-mail para receber notificações do sistema', '2025-12-06 02:15:45', NULL),
(17, 'notif_sistema_som', '1', 'booleano', 'notificacoes', 'Ativar som nas notificações do sistema', '2025-12-06 02:15:45', NULL),
(20, 'mercadopago_sandbox', '0', 'texto', 'mercadopago', NULL, '2025-12-06 14:32:17', '2025-12-18 12:26:44'),
(21, 'mercadopago_access_token_test', 'TEST-8383394053049490-120613-7960752b164bee3710a580fae67c765f-426420888', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44'),
(22, 'mercadopago_public_key_test', 'TEST-77eeee04-0f59-4dbe-a0b3-66847ffd2f86', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44'),
(23, 'mercadopago_access_token_prod', 'APP_USR-8383394053049490-120613-d828c32bc0d495191bb6a1dd77be362b-426420888', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44'),
(24, 'mercadopago_public_key_prod', 'APP_USR-f07e3741-1415-4973-8645-e07b066a13c1', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44'),
(25, 'mercadopago_webhook_url_test', 'https://iafila.doisr.com.br/webhook/mercadopago', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44'),
(26, 'mercadopago_webhook_url_prod', 'https://iafila.doisr.com.br/webhook/mercadopago', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `disponibilidade`
--

CREATE TABLE `disponibilidade` (
  `id` int(11) UNSIGNED NOT NULL,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT '0=Domingo, 6=Sábado',
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estabelecimentos`
--

CREATE TABLE `estabelecimentos` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `plano_id` int(11) UNSIGNED DEFAULT NULL,
  `mp_access_token_test` varchar(255) DEFAULT NULL,
  `mp_public_key_test` varchar(255) DEFAULT NULL,
  `mp_access_token_prod` varchar(255) DEFAULT NULL,
  `mp_public_key_prod` varchar(255) DEFAULT NULL,
  `mp_webhook_url` varchar(255) DEFAULT NULL,
  `mp_sandbox` tinyint(1) DEFAULT 1,
  `evolution_api_url` varchar(255) DEFAULT NULL,
  `evolution_api_key` varchar(255) DEFAULT NULL,
  `evolution_instance_name` varchar(100) DEFAULT NULL,
  `whatsapp_numero` varchar(20) DEFAULT NULL,
  `whatsapp_conectado` tinyint(1) DEFAULT 0,
  `notificar_whatsapp` tinyint(1) DEFAULT 1,
  `notificar_email` tinyint(1) DEFAULT 1,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `nome` varchar(200) NOT NULL,
  `cnpj_cpf` varchar(18) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `plano` enum('trimestral','semestral','anual') DEFAULT 'trimestral',
  `plano_vencimento` date DEFAULT NULL,
  `status` enum('ativo','inativo','suspenso','cancelado') DEFAULT 'ativo',
  `tempo_minimo_agendamento` int(11) DEFAULT 60 COMMENT 'Minutos antes do serviço',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `limite_reagendamentos` tinyint(4) DEFAULT 3 COMMENT 'Quantidade máxima de reagendamentos permitidos por agendamento',
  `usar_intervalo_fixo` tinyint(1) DEFAULT 1,
  `intervalo_agendamento` int(11) DEFAULT 30 COMMENT 'Intervalo em minutos quando usar_intervalo_fixo = 1 (5, 10, 15, 30)',
  `dias_antecedencia_agenda` int(11) DEFAULT 30 COMMENT 'Quantos dias para frente o cliente pode agendar (0 = sem limite)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `estabelecimentos`
--

INSERT INTO `estabelecimentos` (`id`, `usuario_id`, `plano_id`, `mp_access_token_test`, `mp_public_key_test`, `mp_access_token_prod`, `mp_public_key_prod`, `mp_webhook_url`, `mp_sandbox`, `evolution_api_url`, `evolution_api_key`, `evolution_instance_name`, `whatsapp_numero`, `whatsapp_conectado`, `notificar_whatsapp`, `notificar_email`, `data_cadastro`, `nome`, `cnpj_cpf`, `endereco`, `cep`, `cidade`, `estado`, `whatsapp`, `email`, `logo`, `plano`, `plano_vencimento`, `status`, `tempo_minimo_agendamento`, `criado_em`, `atualizado_em`, `limite_reagendamentos`, `usar_intervalo_fixo`, `intervalo_agendamento`, `dias_antecedencia_agenda`) VALUES
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 1, 1, '2025-12-10 15:11:36', 'Barbearia do Perfil', '30016264000100', '126 Rua da Maçonaria', '45490-000', 'Laje', 'BA', '(75) 98889-0006', 'rafaeldiastecinfo@gmail.com', '3f1910611744789aee75ca0b4b437574.png', 'trimestral', '2026-01-31', 'ativo', 60, '2025-12-10 15:11:36', '2025-12-10 15:29:55', 3, 1, 30, 30),
(4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 1, 1, '2025-12-10 17:06:18', 'modelo barber', '', '', '', '', '', '', 'modelo@gmail.com', NULL, 'trimestral', NULL, 'ativo', 60, '2025-12-10 17:06:18', '2025-12-27 10:34:02', 3, 1, 30, 15);

-- --------------------------------------------------------

--
-- Estrutura para tabela `feriados`
--

CREATE TABLE `feriados` (
  `id` int(11) NOT NULL,
  `estabelecimento_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `tipo` enum('nacional','facultativo','municipal','personalizado') DEFAULT 'nacional',
  `recorrente` tinyint(1) DEFAULT 1 COMMENT '1=Repete todo ano, 0=Data única',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `feriados`
--

INSERT INTO `feriados` (`id`, `estabelecimento_id`, `nome`, `data`, `tipo`, `recorrente`, `ativo`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Confraternização Universal', '2026-01-01', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(2, NULL, 'Tiradentes', '2026-04-21', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(3, NULL, 'Dia do Trabalho', '2026-05-01', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(4, NULL, 'Independência do Brasil', '2026-09-07', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(5, NULL, 'Nossa Senhora Aparecida', '2026-10-12', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(6, NULL, 'Finados', '2026-11-02', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(7, NULL, 'Proclamação da República', '2026-11-15', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(8, NULL, 'Dia da Consciência Negra', '2026-11-20', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 15:29:23'),
(9, NULL, 'Natal', '2026-12-25', 'nacional', 1, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(10, NULL, 'Sexta-feira Santa', '2026-04-03', 'nacional', 0, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(11, NULL, 'Carnaval - Segunda', '2026-02-16', 'facultativo', 0, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(12, NULL, 'Carnaval - Terça', '2026-02-17', 'facultativo', 0, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(13, NULL, 'Corpus Christi', '2026-06-04', 'facultativo', 0, 1, '2025-12-27 14:28:23', '2025-12-27 14:28:23'),
(14, 4, 'Feriado de Laje', '2025-12-31', 'municipal', 0, 1, '2025-12-27 19:16:56', '2025-12-27 19:16:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios_estabelecimento`
--

CREATE TABLE `horarios_estabelecimento` (
  `id` int(11) NOT NULL,
  `estabelecimento_id` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT '0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado',
  `ativo` tinyint(1) DEFAULT 1 COMMENT '1=Ativo, 0=Inativo (fechado)',
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `almoco_ativo` tinyint(1) DEFAULT 0 COMMENT 'Se 1, tem intervalo de almoço',
  `almoco_inicio` time DEFAULT NULL COMMENT 'Início do intervalo de almoço',
  `almoco_fim` time DEFAULT NULL COMMENT 'Fim do intervalo de almoço'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Horários de funcionamento do estabelecimento por dia da semana';

--
-- Despejando dados para a tabela `horarios_estabelecimento`
--

INSERT INTO `horarios_estabelecimento` (`id`, `estabelecimento_id`, `dia_semana`, `ativo`, `hora_inicio`, `hora_fim`, `criado_em`, `atualizado_em`, `almoco_ativo`, `almoco_inicio`, `almoco_fim`) VALUES
(1, 2, 0, 0, '08:00:00', '18:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(3, 2, 1, 1, '08:00:00', '18:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(5, 2, 2, 1, '08:00:00', '18:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(7, 2, 3, 1, '08:00:00', '18:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(9, 2, 4, 1, '08:00:00', '18:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(11, 2, 5, 1, '08:00:00', '18:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(13, 2, 6, 1, '08:00:00', '14:00:00', '2025-12-12 01:59:37', '2025-12-12 01:59:37', 0, NULL, NULL),
(190, 4, 0, 0, '08:00:00', '18:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 0, '12:00:00', '13:00:00'),
(191, 4, 1, 1, '08:00:00', '18:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 1, '12:00:00', '13:30:00'),
(192, 4, 2, 1, '08:00:00', '18:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 1, '12:00:00', '13:00:00'),
(193, 4, 3, 1, '08:00:00', '18:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 1, '12:00:00', '13:00:00'),
(194, 4, 4, 1, '08:00:00', '18:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 1, '12:00:00', '13:00:00'),
(195, 4, 5, 1, '08:00:00', '18:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 1, '12:00:00', '13:00:00'),
(196, 4, 6, 1, '08:00:00', '17:00:00', '2025-12-27 13:34:02', '2025-12-27 13:34:02', 1, '12:00:00', '13:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `acao` varchar(100) NOT NULL COMMENT 'Tipo de ação (login, logout, criar, editar, excluir)',
  `tabela` varchar(50) DEFAULT NULL COMMENT 'Tabela afetada pela ação',
  `registro_id` int(11) DEFAULT NULL COMMENT 'ID do registro afetado',
  `dados_antigos` text DEFAULT NULL COMMENT 'JSON com dados antes da alteração',
  `dados_novos` text DEFAULT NULL COMMENT 'JSON com dados após a alteração',
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `tabela`, `registro_id`, `dados_antigos`, `dados_novos`, `ip`, `user_agent`, `criado_em`) VALUES
(1, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:19:53'),
(2, 1, 'criar', 'usuarios', 2, NULL, '{\"nome\":\"Rafael de Andrade Dias\",\"email\":\"rafaeldiaswebdev@gmail.com\",\"telefone\":\"75988890006\",\"nivel\":\"admin\",\"status\":\"ativo\",\"senha\":\"102030\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:22:40'),
(3, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:22:45'),
(4, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:22:58'),
(5, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:45:43'),
(6, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 14:13:54'),
(7, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.39.246', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 15:14:32'),
(8, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 14:02:05'),
(9, 2, 'atualizar', 'clientes', 1, '{\"id\":\"1\",\"estabelecimento_id\":\"1\",\"nome\":\"Rodnei\",\"cpf\":\"\",\"whatsapp\":\"(75) 98889-0006\",\"telefone\":\"\",\"email\":\"\",\"foto\":null,\"tipo\":\"novo\",\"total_agendamentos\":\"0\",\"criado_em\":\"2025-12-06 16:19:15\",\"atualizado_em\":null,\"estabelecimento_nome\":\"Barbearia do Bruxo\"}', '{\"nome\":\"Rodney\",\"cpf\":\"\",\"whatsapp\":\"(75) 98889-0006\",\"telefone\":\"\",\"email\":\"\",\"tipo\":\"novo\"}', '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 14:12:08'),
(10, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:09:39'),
(11, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:10:28'),
(12, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:11:45'),
(14, 3, 'login', 'usuarios', 3, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:33:23'),
(15, 3, 'login', 'usuarios', 3, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:33:52'),
(16, 3, 'login', 'usuarios', 3, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:36:54'),
(17, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:37:17'),
(18, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:38:16'),
(19, NULL, 'login', 'usuarios', 4, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:38:21'),
(20, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 15:47:37'),
(21, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 17:06:54'),
(22, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-10 17:07:15'),
(23, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 09:17:37'),
(24, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 09:59:00'),
(25, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 09:59:05'),
(26, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 10:07:27'),
(27, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 10:09:10'),
(28, 5, 'logout', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 11:33:15'),
(29, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 11:33:21'),
(30, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 11:40:21'),
(31, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 11:40:28'),
(32, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:06:26'),
(33, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:11:01'),
(34, 3, 'login', 'usuarios', 3, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:11:13'),
(35, 5, 'logout', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:12:17'),
(36, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:12:23'),
(37, 3, 'login', 'usuarios', 3, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:15:02'),
(38, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:30:58'),
(39, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:33:55'),
(40, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:34:04'),
(41, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:49:32'),
(42, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 14:49:38'),
(43, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 15:49:22'),
(44, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 15:49:34'),
(45, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 16:34:51'),
(46, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 16:35:03'),
(47, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:17:10'),
(48, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:17:18'),
(49, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:19:02'),
(50, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:19:12'),
(51, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:36:14'),
(52, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:36:19'),
(53, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 17:37:04'),
(54, 6, 'logout', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 19:17:45'),
(55, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 19:17:56'),
(56, 2, 'logout', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 19:21:29'),
(57, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 19:21:42'),
(58, 5, 'logout', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 19:22:05'),
(59, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 19:22:12'),
(60, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 22:19:05'),
(61, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 22:21:03'),
(62, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 22:21:22'),
(63, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 22:27:26'),
(64, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 22:27:42'),
(65, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 23:08:57'),
(66, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 23:09:06'),
(67, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 23:09:27'),
(68, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:06:18'),
(69, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:08:16'),
(70, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:08:27'),
(71, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:31:55'),
(72, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:32:02'),
(73, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:32:22'),
(74, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.38.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:32:31'),
(75, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 10:35:57'),
(76, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 15:08:51'),
(77, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 15:09:10'),
(78, 6, 'logout', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 15:54:00'),
(79, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 15:54:07'),
(80, 5, 'logout', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 16:48:09'),
(81, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 16:48:14'),
(82, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 07:26:42'),
(83, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 07:33:18'),
(84, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.36.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 07:57:44'),
(85, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 07:57:53'),
(86, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 07:49:30'),
(87, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 07:49:39'),
(88, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:13:20'),
(89, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:18:49'),
(90, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:25:30'),
(91, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:25:40'),
(92, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:40:43'),
(93, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:40:50'),
(94, 6, 'logout', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:47:21'),
(95, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:47:27'),
(96, 6, 'logout', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:48:04'),
(97, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 08:48:18'),
(98, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 11:01:00'),
(99, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.37.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 11:01:10'),
(100, 2, 'logout', 'usuarios', 2, NULL, NULL, '177.128.136.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 11:54:39'),
(101, 5, 'login', 'usuarios', 5, NULL, NULL, '177.128.136.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 11:55:00'),
(102, 5, 'logout', 'usuarios', 5, NULL, NULL, '177.128.136.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 12:26:26'),
(103, 2, 'login', 'usuarios', 2, NULL, NULL, '177.128.136.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 12:26:36'),
(104, 6, 'logout', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 12:27:33'),
(105, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 12:27:39'),
(106, 6, 'logout', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 12:27:43'),
(107, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 12:27:51'),
(108, 5, 'logout', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 14:18:00'),
(109, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-18 14:18:08'),
(110, 5, 'login', 'usuarios', 5, NULL, NULL, '189.0.152.246', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/143.0.7499.151 Mobile/15E148 Safari/604.1', '2025-12-23 07:37:38'),
(111, 5, 'logout', 'usuarios', 5, NULL, NULL, '189.0.152.246', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/143.0.7499.151 Mobile/15E148 Safari/604.1', '2025-12-23 07:41:40'),
(112, 5, 'login', 'usuarios', 5, NULL, NULL, '189.0.152.246', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/143.0.7499.151 Mobile/15E148 Safari/604.1', '2025-12-23 07:41:45'),
(113, 5, 'logout', 'usuarios', 5, NULL, NULL, '189.0.152.246', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/143.0.7499.151 Mobile/15E148 Safari/604.1', '2025-12-23 07:42:56'),
(114, 6, 'login', 'usuarios', 6, NULL, NULL, '189.0.152.246', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/143.0.7499.151 Mobile/15E148 Safari/604.1', '2025-12-23 07:43:09'),
(115, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 12:56:19'),
(116, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 15:00:45'),
(117, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 15:00:51'),
(118, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 17:35:32'),
(119, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 17:58:29'),
(120, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 17:58:37'),
(121, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 19:38:38'),
(122, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 19:38:47'),
(123, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 09:46:12'),
(124, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 10:08:29'),
(125, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 10:08:47'),
(126, 2, 'logout', 'usuarios', 2, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 10:09:23'),
(127, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 10:09:33'),
(128, 5, 'logout', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 10:14:39'),
(129, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 10:14:46'),
(130, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 16:01:51'),
(131, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 16:23:01'),
(132, 6, 'login', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 10:11:32'),
(133, 6, 'logout', 'usuarios', 6, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 10:12:25'),
(134, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 10:12:36'),
(135, 5, 'login', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 10:24:06'),
(136, 5, 'logout', 'usuarios', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 10:40:50'),
(137, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 10:40:58'),
(138, 5, 'login', 'usuarios', 5, NULL, NULL, '170.239.36.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 16:16:06'),
(139, 6, 'login', 'usuarios', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 16:17:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'NULL = notificação para todos',
  `tipo` enum('info','sucesso','aviso','erro') DEFAULT 'info',
  `titulo` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `link` varchar(255) DEFAULT NULL COMMENT 'Link para ação relacionada',
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes_config`
--

CREATE TABLE `notificacoes_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `tipo` enum('confirmacao','cancelamento','reagendamento','lembrete_1dia','lembrete_1hora','pagamento','feedback') NOT NULL,
  `template` text NOT NULL COMMENT 'Template com variáveis: {cliente}, {servico}, {data}, {hora}, etc',
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `notificacoes_config`
--

INSERT INTO `notificacoes_config` (`id`, `estabelecimento_id`, `tipo`, `template`, `ativo`) VALUES
(8, 2, 'confirmacao', 'Olá {cliente}! ✅ Seu agendamento foi confirmado!\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nNos vemos em breve!', 1),
(9, 2, 'cancelamento', 'Olá {cliente}. ❌ Seu agendamento foi cancelado.\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n\\nQualquer dúvida, entre em contato!', 1),
(10, 2, 'reagendamento', 'Olá {cliente}! 🔄 Seu agendamento foi reagendado.\\n\\n📅 Nova Data: {data}\\n🕐 Novo Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}', 1),
(11, 2, 'lembrete_1dia', 'Olá {cliente}! 🔔 Lembrete: você tem um agendamento amanhã!\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nTe esperamos!', 1),
(12, 2, 'lembrete_1hora', 'Olá {cliente}! ⏰ Seu agendamento é daqui a 1 hora!\\n\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nEstamos te esperando!', 1),
(13, 2, 'pagamento', 'Olá {cliente}! 💰 Pagamento confirmado!\\n\\n✅ Valor: R$ {valor}\\n📅 Agendamento: {data} às {hora}\\n\\nObrigado pela preferência!', 1),
(14, 2, 'feedback', 'Olá {cliente}! 🌟 Como foi sua experiência?\\n\\nGostaríamos de saber sua opinião sobre o atendimento de {profissional}.\\n\\nAvalie aqui: {link}', 1),
(22, 4, 'confirmacao', 'Olá {cliente}! ✅ Seu agendamento foi confirmado!\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nNos vemos em breve!', 1),
(23, 4, 'cancelamento', 'Olá {cliente}. ❌ Seu agendamento foi cancelado.\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n\\nQualquer dúvida, entre em contato!', 1),
(24, 4, 'reagendamento', 'Olá {cliente}! 🔄 Seu agendamento foi reagendado.\\n\\n📅 Nova Data: {data}\\n🕐 Novo Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}', 1),
(25, 4, 'lembrete_1dia', 'Olá {cliente}! 🔔 Lembrete: você tem um agendamento amanhã!\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nTe esperamos!', 1),
(26, 4, 'lembrete_1hora', 'Olá {cliente}! ⏰ Seu agendamento é daqui a 1 hora!\\n\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nEstamos te esperando!', 1),
(27, 4, 'pagamento', 'Olá {cliente}! 💰 Pagamento confirmado!\\n\\n✅ Valor: R$ {valor}\\n📅 Agendamento: {data} às {hora}\\n\\nObrigado pela preferência!', 1),
(28, 4, 'feedback', 'Olá {cliente}! 🌟 Como foi sua experiência?\\n\\nGostaríamos de saber sua opinião sobre o atendimento de {profissional}.\\n\\nAvalie aqui: {link}', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `assinatura_id` int(11) UNSIGNED DEFAULT NULL,
  `plano_id` int(11) UNSIGNED NOT NULL,
  `mercadopago_id` varchar(100) DEFAULT NULL,
  `tipo` enum('pix','cartao','boleto') NOT NULL DEFAULT 'pix',
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled','refunded','in_process') DEFAULT 'pending',
  `status_detail` varchar(100) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `qr_code_base64` text DEFAULT NULL,
  `payment_data` text DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pagamentos`
--

INSERT INTO `pagamentos` (`id`, `estabelecimento_id`, `assinatura_id`, `plano_id`, `mercadopago_id`, `tipo`, `valor`, `status`, `status_detail`, `qr_code`, `qr_code_base64`, `payment_data`, `criado_em`, `atualizado_em`) VALUES
(1, 4, NULL, 6, '1325685424', 'pix', 1.00, 'pending', 'pending_waiting_transfer', '00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654041.005802BR5911DI68804Up616004TVWo62230519mpqrinter13256854246304500E', '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', '{\"accounts_info\":null,\"acquirer_reconciliation\":[],\"additional_info\":{\"tracking_id\":\"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none\"},\"authorization_code\":null,\"binary_mode\":false,\"brand_id\":null,\"build_version\":\"3.135.0-rc-1\",\"call_for_authorize_id\":null,\"callback_url\":null,\"captured\":true,\"card\":[],\"charges_details\":[{\"accounts\":{\"from\":\"collector\",\"to\":\"mp\"},\"amounts\":{\"original\":0.01000000000000000020816681711721685132943093776702880859375,\"refunded\":0},\"client_id\":0,\"date_created\":\"2025-12-18T11:24:58.000-04:00\",\"external_charge_id\":\"01KCS10XMTP7HCMYWYFYJ3NKZS\",\"id\":\"1325685424-001\",\"last_updated\":\"2025-12-18T11:24:58.000-04:00\",\"metadata\":{\"reason\":\"\",\"source\":\"proc-svc-charges\",\"source_detail\":\"processing_fee_charge\"},\"name\":\"mercadopago_fee\",\"refund_charges\":[],\"reserve_id\":null,\"type\":\"fee\"}],\"charges_execution_info\":{\"internal_execution\":{\"date\":\"2025-12-18T11:24:58.408-04:00\",\"execution_id\":\"01KCS10XM1BDZ2M60307QQ5ERS\"}},\"collector_id\":426420888,\"corporation_id\":null,\"counter_currency\":null,\"coupon_amount\":0,\"currency_id\":\"BRL\",\"date_approved\":null,\"date_created\":\"2025-12-18T11:24:58.000-04:00\",\"date_last_updated\":\"2025-12-18T11:24:58.000-04:00\",\"date_of_expiration\":\"2025-12-19T11:24:58.000-04:00\",\"deduction_schema\":null,\"description\":\"Assinatura Plano Teste 2 - AgendaPro\",\"differential_pricing_id\":null,\"external_reference\":\"PLANO_6_EST_4\",\"fee_details\":[],\"financing_group\":null,\"id\":1325685424,\"installments\":1,\"integrator_id\":null,\"issuer_id\":\"12501\",\"live_mode\":false,\"marketplace_owner\":null,\"merchant_account_id\":null,\"merchant_number\":null,\"metadata\":[],\"money_release_date\":null,\"money_release_schema\":null,\"money_release_status\":\"released\",\"notification_url\":\"https:\\/\\/iafila.doisr.com.br\\/webhook\\/mercadopago\",\"operation_type\":\"regular_payment\",\"order\":[],\"payer\":{\"identification\":{\"number\":null,\"type\":null},\"entity_type\":null,\"phone\":{\"number\":null,\"extension\":null,\"area_code\":null},\"last_name\":null,\"id\":\"2153563569\",\"type\":null,\"first_name\":null,\"email\":null},\"payment_method\":{\"id\":\"pix\",\"issuer_id\":\"12501\",\"type\":\"bank_transfer\"},\"payment_method_id\":\"pix\",\"payment_type_id\":\"bank_transfer\",\"platform_id\":null,\"point_of_interaction\":{\"application_data\":{\"name\":null,\"operating_system\":null,\"version\":null},\"business_info\":{\"branch\":\"Merchant Services\",\"sub_unit\":\"default\",\"unit\":\"online_payments\"},\"location\":{\"source\":null,\"state_id\":null},\"transaction_data\":{\"bank_info\":{\"collector\":{\"account_alias\":null,\"account_holder_name\":\"RaDicO gd ZgPlotZ SSas\",\"account_id\":null,\"long_name\":null,\"transfer_account_id\":null},\"is_same_bank_account_owner\":null,\"origin_bank_id\":null,\"origin_wallet_id\":null,\"payer\":{\"account_holder_name\":null,\"account_id\":null,\"branch\":null,\"external_account_id\":null,\"id\":null,\"identification\":{\"number\":null,\"type\":null},\"is_end_consumer\":null,\"long_name\":null}},\"bank_transfer_id\":null,\"e2e_id\":null,\"financial_institution\":null,\"infringement_notification\":{\"status\":null,\"type\":null},\"merchant_category_code\":null,\"qr_code\":\"00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654041.005802BR5911DI68804Up616004TVWo62230519mpqrinter13256854246304500E\",\"qr_code_base64\":\"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQAAAAB79iscAAAODklEQVR4Xu3XQbYbtw5FUc0g859lZqC\\/ggsUQIAlp\\/Hor3LObcgkAYK7Xs+v94Py96uffHPQngvac0F7LmjPBe25oD0XtOeC9lzQngvac0F7LmjPBe25oD0XtOeC9lzQngvac0F7LmjPBe25oD0XtOeC9lzQngvac0F7LmjPBe25oD0XtOeC9lzQngvac0F7LmjPBe25oD0XtOdSta+ev\\/45s5\\/YZl9e+2fIp6qdWTX6cmi9m9WWmIx2VO0M7WxDi1ZtaNGqDS1ataFFqza0aNX2zdo8z60NeV2Dx31tr1JU85qNWgrX7V7Ib6ktaDNoFbRoFbRoFbRoFbRoFbRolYdr8\\/7trezLt3Pb8IM3q7t329aDdnralFbdvdu2HrTT06a06u7dtvWgnZ42pVV377atB+30tCmtunu3bT1op6dNadXdu23rQTs9bUqr7t5tWw\\/a6WlTWnX3btt60E5Pm9Kqu3fb1oN2etqUVt2927aeP1BrJ9nnMcXyk6nGKNS+9uG7HwtatApatApatApatApatApatMofra3vZJ+dxY2m8AGRyospbXJtXqpo0XaGB+0StLdtPgRtqWbzUkWLtjM8aJegvW3zIWhLNZuXKlq0neEZ2rb1Ics7DZ+FNr2uWpaW3VkGLdq5RTufTaYHLVoFLVoFLVoFLVoF7ZdrWxbAb\\/yZDLQ\\/9TMZaH\\/qZzLQ\\/tTPZKD9qZ\\/JQPtTP5OB9qd+JgPtT\\/1MBtqf+pkMtD\\/1MxmP1+7zt1+ok+p9xTbZ51srxIDaklPy\\/6cx5UPQolXQolXQolXQolXQolXQolWerI23M3Zcn7BMz+5uTcoWvE9ZvmX3fR60u6CdL9ox2pIhQ2sFa0Hbzyxod0E7X7RjtCVDhtYK1oK2n1nQ7vJf19aZXiyUPHN8fsGyyuba13ivdahluTbe9ZZco0WroEWroEWroEWroEWroEWrPEvbOuoTs5DVhqrVuLb7Fr+badU941rarqNuC2iX6p5xLW3XUbcFtEt1z7iWtuuo2wLapbpnXEvbddRtAe1S3TOupe066raAdqnuGdfSdh11W0C7VPeMa2m7jrotoF2qe8a1tF1H3RbQLtU941rarqNuC2iX6p5xLW3XUbeFL9BGdhR\\/dil4cvCSvFEHLKkD2qdFFa2fLUF7LUvQqq+lDkCLFi3aOgAtWrRo6wC0X6OtlLyQL7YzSxZi5SPiCyqlJXm7P4FtY7IHrQUtWgUtWgUtWgUtWgUtWgXtg7VNZicNOqq2WmQ+Pc4aoH5p3l2+JQubu+tOHX7hjRatXXijRWsX3mjR2oU3WrR24Y0WrV14o0VrF97frPUJ8VimVT+0ZDXi1UDtqjmguccNtNOD1u\\/dUqL6oQUtWrWgRasWtGjVghatWtCiVctv1NbenB6TnGLbHJIty5nfiLRP+zzKk2do0aLdnPmNyI5SC2g\\/PYE2ztCiRbs58xuRHaUW0H56Am2coUX7WfuuT\\/jM5dbdED2bW0u7Vs+WlSdaBh4t2rL1yddSuXrUhnapZtCiVdCiVdCiVdCiVdB+jdZK9dnMcjXH1bffqyeNE5rb9saYl1BvyfV+iAVtf2g3D21OjwForSXX+yEWtP2h3Ty0OT0GoLWWXO+HWND2h3bz0Ob0GIDWWnK9H2JB2x\\/azUOb02PAb9F+Guyr7IvmmvgMy3DbmWUW8kZ7vJLRoo1r1xKtn6FFqzO0aHWGFq3O0KLVGdqnaVvHvLUexFk076BesMSq3s1mW1m1fQZatFffNeBaor0KFrTZgbY8hHY5Ww\\/iDC1atGjrGVq0\\/1dtti0X8u0PsmXlad+8TN49OT4ytxa0GbSRAUWLFu2QzSfRokWLFm19DC3a79JGmmeczW+p5Mz8yLyWzWObLQm1oM2gjdRbaNHqFlq0uoUWrW6hRatbaNHq1hO0WZyDPfGs39hVrbCssmr\\/1gH5LZZ416vLULReQItWBbRoVUCLVgW0aFVAi1YFtE\\/XtnFeyFs2LtK+Kqd4bEobNf8EtdlaWiGDFq2CFq2CFq2CFq2CFq2CFq3yXG3GZ+bgALRC9exQeS0B8X2Nd3vN1xa0S59vI2jRKmjRKmjRKmjRKmjRKmgfo62DLTl92XpT87THdqu8Fh858LuqBe3OOPuGBy1atOvWm6Zib5x9w4MWLdp1601TsTfOvuFBixbtuvWmqdgbZ9\\/woP1KbXj8gq2W6q9uNHfEt+0zJnlA\\/Wzd37xtq6X6qxto0aK9ztb9zdu2Wqq\\/uoEWLdrrbN3fvG2rpfqrG2jRor3O1v3N27Zaqr+6gRbtb9O2W\\/PHE7JMq2ay6tv5Gbu+TJ2HFq2CFq2CFq2CFq2CFq2CFq3yXO1OYbl6Y7t81e21ioqW\\/Ycv+JqlirYGbeT22as3tmjV14L25hraa4l2TEbbcvXGFq36WtDeXEN7LdGOyf9d7ctl7b4\\/0bbtJ6rN2Fa5rR85+\\/yh5Qzt\\/m20s8PP0KLVGVq0OkOLVmdo0eoMLVqdfaHWnq2TYlwWat+SnDEoGRsQ32fxG0vL7seDFq2CFq2CFq2CFq2CFq2CFq3yZG0kO9qFnOmxtyOtGoILtfuC1zVg+Yt4YfyVrmUJWrQKWrQKWrQKWrQKWrQKWrTKM7T2tnUsqWf5og2JG\\/u78aW1+f1hO+7GyoM2gzaze\\/GF9mY77sbKgzaDNrN78YX2ZjvuxsqDNoM2s3vxhfZmO+7GyoM2gzaze\\/H1zVpL3S6oevXt4xq04q1luVHPosWT28bIoEUbhXW3tKEtZ2jRokVbz9CiRYu2nqH9Vm0dvEA9f1Vebls1U+8uqd9i+Tyqqq4l2nbiQYtWQYtWQYtWQYtWQYtWQfsMbUzys0greDUm1U9bvqrK5ud6wc4scaOeZdCijWqu0aJV0KJV0KJV0KJV0KJVnqZdOnLc+IKU2Y30LM96Gj7TUO3G8nO1XEu0\\/wTtG60F7RutBe0brQXtG60F7RutBe37aVq\\/0IzLz4C+q9FPG2+ivDkfisLu7tqy7nQht2jRaosWrbZo0WqLFq22aNFqixattl+u3Y9rzy5D\\/Iml+faravMyb1TbKN9eS9uhRasdWrTaoUWrHVq02qFFqx1atNo9R5tX25kXchszs9reaaj2uXmjvdaa0bYz70OLVn1o0aoPLVr1oUWrPrRo1Yf26VpL3cbMVq3vLJT95+aLdrZA21eNoEWroEWroEWroEWroEWroEWr\\/Ana\\/f2lagX\\/edWvylVNm3Lzzaunb6+1bdcdWrTaoUWrHVq02qFFqx1atNqhRavdk7Tv68X05BPxdk0OtrvxLfXDX9eU1rxLezfnWdCiVdCiVdCiVdCiVdCiVdCiVZ6rbW35olfzLCnLF+SZH+Qqt8tZu1ufbG\\/42bVEi9ZT76NFi7Zu0aJFi7Zu0aJF+xxtm55DMjE9h9Tm\\/FIrJC8mjz\\/Gsh1fGj8etGgVtGgVtGgVtGgVtGgVtGiVJ2tj3FWMSVHNpLs2x3ZXqCirLg\\/ltj3pawvaaG4FtGgjaNEqaNEqaNEqaNEqaB+irfff162YlE9YIX\\/G92Xy2dnnhUjT5oCcgrYGbQZtHxV9XoigRaugRaugRaugRaug\\/SLtrmO3Hc2xqtoo5Oe2ebdD8y\\/SqmhbAa2f5zto13m3Q9HW5lihRRvT0aJF26poWwGtn+c7aNd5t0P\\/pdZSL7RVJt7OwamtsrjmI\\/LZTA6Yze2r0KLN5mup+C20aHULLVrdQotWt9Ci1S20aHXrCdq4Wrdx1dJWV11pLXWAVef26lSz\\/Vs\\/vAXtboBV5\\/bqVLP9ixYt2rK66kpr2fHa9upUs\\/2LFi3asrrqSmvZ8dr26lSz\\/YsWLdqyuupKa9nx2vbqVLP9i\\/YLtO8VYJ5A7asRnx6fO1qWKU3mLdkXP5s\\/37VU9ldfaG0e2hdam4f2hdbmoX2htXloX2htHtoXWpuH9vV92nahARqlTapnllZYUlvC7YknR8Gruc4haEtqC1q0aNHWFrRo0aKtLWjRov12bUzPmVUbZ3v8bZY+f+\\/zgPzcWKHdB22kbtEqnwegvX2sBW2kbtEqnwegvX2sBW2kbtEqnwegvX2sBW2kbr9Vu0u7NdwxuP34jdelyBetkMbYtr6s1qBFq6BFq6BFq6BFq6BFq6BFqzxXax01CYitzWw3fLW8WD1xo23zWpuyO\\/OgRaugRaugRaugRaugRaugRas8WZvnsbVbdUi05Ns7aLZkXzsbX5+jos\\/XGbRoFbRoFbRoFbRoFbRoFbRolYdrc9Iw2rjI7lpr2Q3IG7sB426sPGgzc0De2A0Yd2PlQZuZA\\/LGbsC4GysP2swckDd2A8bdWHnQZuaAvLEbMO7GyoM2Mwfkjd2AcTdWHrSZOSBv7AaMu7HyoM3MAXljN2DcjZUHbWYOyBu7AeNurDxoM3NA3tgNGHdj5fnTtC1x1TYJaAPyzAvRXKfYXTub35JT0LYBeeaFaK5T0LagRXsVvPmF1prRvtBaM9oXWmtG+0JrzWhfX6qt1Xp1aiPt7Q+raB7jow+tt0TQvtH6DbRodQMtWt1Ai1Y30KLVDbRP17Ztc\\/v0bFlknt12wddr2fyJ7EG726KdbWjRokVbt2jRokVbt2jRfr+2xdraY7uqZffYcq2RM7mtfTYvH7KgRaugRaugRaugRaugRaugRas8V\\/v9QXsuaM8F7bmgPRe054L2XNCeC9pzQXsuaM8F7bmgPRe054L2XNCeC9pzQXsuaM8F7bmgPRe054L2XNCeC9pzQXsuaM8F7bmgPRe054L2XNCeC9pzQXsuaM8F7bmgPRe054L2XNCey8O0\\/wO9LbHf4zYXcQAAAABJRU5ErkJggg==\",\"ticket_url\":\"https:\\/\\/www.mercadopago.com.br\\/sandbox\\/payments\\/1325685424\\/ticket?caller_id=2153563569&hash=a6bbce40-9476-4907-808a-2a772e0ad7a4\",\"transaction_id\":null},\"type\":\"OPENPLATFORM\"},\"pos_id\":null,\"processing_mode\":\"aggregator\",\"refunds\":[],\"release_info\":null,\"shipping_amount\":0,\"sponsor_id\":null,\"statement_descriptor\":null,\"status\":\"pending\",\"status_detail\":\"pending_waiting_transfer\",\"store_id\":null,\"tags\":null,\"taxes_amount\":0,\"transaction_amount\":1,\"transaction_amount_refunded\":0,\"transaction_details\":{\"acquirer_reference\":null,\"bank_transfer_id\":null,\"external_resource_url\":null,\"financial_institution\":null,\"installment_amount\":0,\"net_received_amount\":0,\"overpaid_amount\":0,\"payable_deferral_period\":null,\"payment_method_reference_id\":null,\"total_paid_amount\":1,\"transaction_id\":null}}', '2025-12-18 12:24:57', '2025-12-18 12:26:16'),
(2, 4, NULL, 6, '137802603007', 'pix', 1.00, 'approved', 'accredited', '00020126580014br.gov.bcb.pix0136420ab7c4-4d63-46d4-809e-cd3eebc129ec52040000530398654041.005802BR5911DIRA93473616004Laje62250521mpqrinter13780260300763045E28', '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', '{\"accounts_info\":null,\"acquirer_reconciliation\":[],\"additional_info\":{\"bank_info\":{\"is_same_bank_account_owner\":true},\"tracking_id\":\"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none\"},\"authorization_code\":null,\"binary_mode\":false,\"brand_id\":null,\"build_version\":\"3.135.0-rc-1\",\"call_for_authorize_id\":null,\"callback_url\":null,\"captured\":true,\"card\":[],\"charges_details\":[{\"accounts\":{\"from\":\"collector\",\"to\":\"mp\"},\"amounts\":{\"original\":0.01,\"refunded\":0},\"client_id\":0,\"date_created\":\"2025-12-18T11:26:55.000-04:00\",\"external_charge_id\":\"01KCS14FR5WMF5HEGXQ1N1J64E\",\"id\":\"137802603007-001\",\"last_updated\":\"2025-12-18T11:26:55.000-04:00\",\"metadata\":{\"reason\":\"\",\"source\":\"proc-svc-charges\",\"source_detail\":\"processing_fee_charge\"},\"name\":\"mercadopago_fee\",\"refund_charges\":[],\"reserve_id\":null,\"type\":\"fee\"}],\"charges_execution_info\":{\"internal_execution\":{\"date\":\"2025-12-18T11:26:55.254-04:00\",\"execution_id\":\"01KCS14FQFCHT72EYY3CST1M7G\"}},\"collector_id\":426420888,\"corporation_id\":null,\"counter_currency\":null,\"coupon_amount\":0,\"currency_id\":\"BRL\",\"date_approved\":\"2025-12-18T11:27:15.000-04:00\",\"date_created\":\"2025-12-18T11:26:55.000-04:00\",\"date_last_updated\":\"2025-12-18T11:27:15.000-04:00\",\"date_of_expiration\":\"2025-12-19T11:26:55.000-04:00\",\"deduction_schema\":null,\"description\":\"Assinatura Plano Teste 2 - AgendaPro\",\"differential_pricing_id\":null,\"external_reference\":\"PLANO_6_EST_4\",\"fee_details\":[{\"amount\":0.01,\"fee_payer\":\"collector\",\"type\":\"mercadopago_fee\"}],\"financing_group\":null,\"id\":137802603007,\"installments\":1,\"integrator_id\":null,\"issuer_id\":\"12501\",\"live_mode\":true,\"marketplace_owner\":null,\"merchant_account_id\":null,\"merchant_number\":null,\"metadata\":[],\"money_release_date\":\"2025-12-18T11:27:15.000-04:00\",\"money_release_schema\":null,\"money_release_status\":\"released\",\"notification_url\":null,\"operation_type\":\"regular_payment\",\"order\":[],\"payer\":{\"email\":\"XXXXXXXXXXX\",\"entity_type\":null,\"first_name\":null,\"id\":\"1670874902\",\"identification\":{\"number\":null,\"type\":\"CPF\"},\"last_name\":null,\"operator_id\":null,\"phone\":{\"number\":null,\"extension\":null,\"area_code\":null},\"type\":null},\"payment_method\":{\"id\":\"pix\",\"issuer_id\":\"12501\",\"type\":\"bank_transfer\"},\"payment_method_id\":\"pix\",\"payment_type_id\":\"bank_transfer\",\"platform_id\":null,\"point_of_interaction\":{\"application_data\":{\"name\":null,\"operating_system\":null,\"version\":null},\"business_info\":{\"branch\":\"Merchant Services\",\"sub_unit\":\"default\",\"unit\":\"online_payments\"},\"location\":{\"source\":null,\"state_id\":null},\"sub_type\":\"INTER_PSP\",\"transaction_data\":{\"bank_info\":{\"collector\":{\"account_alias\":null,\"account_holder_name\":\"Rafael de Andrade Dias\",\"account_id\":40388482513,\"long_name\":\"MERCADO PAGO INSTITUI\\u00c7\\u00c3O DE PAGAMENTO LTDA.\",\"transfer_account_id\":null},\"is_same_bank_account_owner\":true,\"origin_bank_id\":null,\"origin_wallet_id\":null,\"payer\":{\"account_id\":72268491,\"branch\":\"1\",\"external_account_id\":null,\"id\":null,\"identification\":[],\"is_end_consumer\":null,\"long_name\":\"NU PAGAMENTOS S.A. - INSTITUI\\u00c7\\u00c3O DE PAGAMENTO\"}},\"bank_transfer_id\":120650117714,\"e2e_id\":null,\"financial_institution\":1,\"infringement_notification\":{\"status\":null,\"type\":null},\"merchant_category_code\":null,\"qr_code\":\"00020126580014br.gov.bcb.pix0136420ab7c4-4d63-46d4-809e-cd3eebc129ec52040000530398654041.005802BR5911DIRA93473616004Laje62250521mpqrinter13780260300763045E28\",\"qr_code_base64\":\"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAJ8ElEQVR42uzdzZHiyhIGUBEseokJmIJpYBqmYAJLFgR6cXsoKbOqBMybvjdi4HyrGTVIR+zqL3MQERERERERERERERERERERERERERERERERERERERGR\\/zDbsc2h\\/tD+n6vX739u7p+5ff\\/v6\\/vfx38+cct3HIZVud3pfoPb9+fP8yeGdMeYIy0tLS0tLS0tLS0tLS3t52mP9YXDMJR7T9\\/81oYLq8IfhmE3P+bcf0R4\\/83Mn97\\/+JhES0tLS0tLS0tLS0tLS\\/v\\/a8PDijY+ff\\/PeHodhs\\/7eYR+ut\\/oF39TLox3zmYe86\\/G8ZJ+kKjd329+oaWlpaWlpaWlpaWlpaWlnbXl3tPDwnxCSb5wC7MFu3mFfHnd\\/vv9L7S0tLS0tLS0tLS0tLS0\\/7l2KLu8dzOu2TferKGPaZU9LqoXy9DZN05LS0tLS0tLS0tLS0tLS9vf5R4OZpc\\/r8vDhrSGng+DhzX026ML+1r7R3vyaWlpaWlpaWlpaWlpaWnfQdurvRYqpe3Sin+5sCoXTulCWPGPF\\/b3C5d0YR02FfxRpThaWlpaWlpaWlpaWlpa2t\\/Pwq2+R+jrUk181xmhl0H9mI5657PiPx5aWlpaWlpaWlpaWlpa2r9Zu5s3mPcqpe3mPelhyXzVOZge36dTe61ZZb\\/Wkvh2tLS0tLS0tLS0tLS0tLQfqG2W4\\/NB8339PiGhOvpYP2zdmT6YblDe\\/5wmJL6erfjT0tLS0tLS0tLS0tLS0v6mNt4qnOw+p0dfav7Y6QhWLKtQzW2cf5BedfQxdSF7YYROS0tLS0tLS0tLS0tLS\\/ue2jBbcErTB1PCJ0ot82t5dG\\/b+6NSaxk31nccaGlpaWlpaWlpaWlpaWl\\/SDuk5euSW3iZXHstZ5s2kue3iwe5QzW3hU\\/kSQBaWlpaWlpaWlpaWlpa2o\\/S5urowdKsocct6PtxIXnJfJpxOMwdwZoZisVVeVpaWlpaWlpaWlpaWlraz9IeZ8s58Ztj56EY261+3bjAn\\/fNh\\/5leYIhrvgH\\/msdxmlpaWlpaWlpaWlpaWlpn2jDgPySioFPI+6yCf5ann6cB\\/CXeQ29aQA2bXsfOyP03BEslDQfaGlpaWlpaWlpaWlpaWk\\/WFu2oC8nfz6cVN8m\\/jk8PeSQ3n9bX9ilnmFPQ0tLS0tLS0tLS0tLS0v7ltq8gp\\/5YX0+zydU2+TLfMKCdhx7HdGqO+bac7S0tLS0tLS0tLS0tLS0f6jNu9zzvb\\/qLehhk\\/otVErb3WuvjfU2+WtdHT3XXruGnt2lRVjc9k5LS0tLS0tLS0tLS0tL+2naR\\/MJzeRAWDLP7crifEJnmX7s9+we0kb6L1paWlpaWlpaWlpaWlran9OOD2qphY5guRjbLQ\\/qy7bvJ7XXLotD9qYLOC0tLS0tLS0tLS0tLS3tR2mbwX5vE3zo2d2bT3j0sM3c4St+ZZ+WzMd6gmGgpaWlpaWlpaWlpaWlpf1Q7a3fv2yZnx92aPcQTPzFFf8GcR1eCi0tLS0tLS0tLS0tLS3ti9rd3FB7rDuChR3n6\\/6F3vi73HEash\\/qam7lBtMPsn9plzstLS0tLS0tLS0tLS0t7btqt\\/WaeK5lvuk09Cq4a3jd5qR67tndpJmQeK1nNy0tLS0tLS0tLS0tLS3te2ubwX7QHudT42HFv5o+aM6h5xX\\/cOw8TEFU8wnlE6flqQ9aWlpaWlpaWlpaWlpa2te1eYB9nle8q13uocN2zmnmX5O2atFdLM1G+uYHeaHDOC0tLS0tLS0tLS0tLS3tu2qb+YSJH2YLci3zXw\\/bpnPom7QIv64\\/39Re6+1yf2E+gZaWlpaWlpaWlpaWlpb2JW2ovdbLMY3QhzQgf\\/SV5mR3bik29reV\\/069cVpaWlpaWlpaWlpaWlra99GGk92Xfv+uY6fDdiersAh\\/TNvkm7Pip86qfGg6dqKlpaWlpaWlpaWlpaWl\\/TxtzmZevl+F0f9haVf8ZZ5gqMqlNxMF4RGNNmxqp6WlpaWlpaWlpaWlpaX9Qe2uvtU+HfUuWde4IfXvmk52j3PP7oUK5qfOHcd62zstLS0tLS0tLS0tLS0t7Udpt53b5mPnu84W9H37lQVtrrf+VU8w5Edc07I+LS0tLS0tLS0tLS0tLe1naXt\\/avqXNXvS69F\\/pX30g4RKcWPQ5i0DtLS0tLS0tLS0tLS0tLR\\/qC1r6Kvy6G294n2sW26PaVN7GKE\\/PNmd982PnWX6UM3tSEtLS0tLS0tLS0tLS0v7YdqMaxIKo8V77+\\/dyXo9uxc3ta\\/qPezrflvwF3a509LS0tLS0tLS0tLS0tI+14ae3Rl3q5fMhzzirluEVe8fCpAP8xA\\/Hh4P+8Z\\/cz6BlpaWlpaWlpaWlpaWlvZ9tXm2YOxrp1vt05b1PP9wStve8y73S3qfXjW31+YTaGlpaWlpaWlpaWlpaWnfTbut66Bt+7XXOvfurfgPaZd709AstwVfqL1GS0tLS0tLS0tLS0tLS\\/sj2qH+5lB3BNv1z2HX2jhC33VbdI\\/5ZHeovTbM5d2erqHT0tLS0tLS0tLS0tLS0r6lNoz+c7uuVdAe6jX3UB19O\\/fsHusbXPs\\/SJ7DGNNR96c9u2lpaWlpaWlpaWlpaWlp31I73Af7TaW45WPkOb0V\\/+YTw+OT7ce5odnlYc9uWlpaWlpaWlpaWlpaWtrXtblS2thp19Xscg+r7Jdyg0bbeVTsGTbWtdfCD0JLS0tLS0tLS0tLS0tL+4HaXB19qL8ZZhyuqZZ5tULeNDTL73+oz6E3j6ClpaWlpaWlpaWlpaWl\\/Re05elTMfBOi+44ni5vtx4fpqm9Nm1NL7XXFm5wGp6GlpaWlpaWlpaWlpaWlvZdtc30QVNqbUiF0ao191x7bZg553rNPT\\/i8mgOg5aWlpaWlpaWlpaWlpb2c7TbzoD+UHNCqbXNrL2m0mm\\/LuTq6Av9y8a0b745h05LS0tLS0tLS0tLS0tL+1PaY33hMK94n+sW3ed5U3tVLrwzQs8\\/yK3TdKw5K\\/54DZ2WlpaWlpaWlpaWlpaW9r21Ycl8W5cuL8fI8zn0aVH9VHf1zi23w\\/TBKs8nNMnzD7S0tLS0tLS0tLS0tLS0tGkL+q4+mB7alX2Ve+\\/bXfFxC0DYN99wyh6CodPjm5aWlpaWlpaWlpaWlpb239Hm4XOTdX6fcpB7m2qvnetvhRH6tTMJ8LR\\/GS0tLS0tLS0tLS0tLS3tG2v7u9yX7z3Wu9xzrbZwDv1cH3Wf+PVB9tfnE2hpaWlpaWlpaWlpaWlpX9Q+qb12TNvEw8nuXyP0bSrGtilf6bxus8oeh\\/jjD1WKo6WlpaWlpaWlpaWlpaX9O7UiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiJ\\/S\\/4XAAD\\/\\/+R0jjgHocaNAAAAAElFTkSuQmCC\",\"ticket_url\":\"https:\\/\\/www.mercadopago.com.br\\/payments\\/137802603007\\/ticket?caller_id=1670874902&hash=18ba3900-cec3-4221-8561-f0066c95844f\",\"transaction_id\":\"PIXE18236120202512181527s08bb2ce58b\"},\"type\":\"OPENPLATFORM\"},\"pos_id\":null,\"processing_mode\":\"aggregator\",\"refunds\":[],\"release_info\":null,\"shipping_amount\":0,\"sponsor_id\":null,\"statement_descriptor\":null,\"status\":\"approved\",\"status_detail\":\"accredited\",\"store_id\":null,\"tags\":null,\"taxes_amount\":0,\"transaction_amount\":1,\"transaction_amount_refunded\":0,\"transaction_details\":{\"acquirer_reference\":null,\"bank_transfer_id\":120650117714,\"external_resource_url\":null,\"financial_institution\":\"1\",\"installment_amount\":0,\"net_received_amount\":0.99,\"overpaid_amount\":0,\"payable_deferral_period\":null,\"payment_method_reference_id\":null,\"total_paid_amount\":1,\"transaction_id\":\"PIXE18236120202512181527s08bb2ce58b\"}}', '2025-12-18 12:26:55', '2025-12-18 12:27:17'),
(3, 4, 2, 5, '138451704968', 'pix', 1.00, 'approved', 'accredited', '00020126580014br.gov.bcb.pix0136420ab7c4-4d63-46d4-809e-cd3eebc129ec52040000530398654041.005802BR5911DIRA93473616004Laje62250521mpqrinter1384517049686304D660', 'iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX///8AAABVwtN+AAAKDUlEQVR42uzdQXLiuhYGYFMMGLIElsLSyNJYCkvIkAGFX3WC7HMkOfG9ybtVDd8/6So3sT97JunoaBAREREREREREREREREREREREREREREREREREREREZH/MIexzVv9o9Ofq7d0afNxYffx+/OfX9zzHT9+8ZnL4wb3j9+/z7/4zL4jONPS0tLS0tLS0tLS0tLSvp72XF94G4Zy7+kvM65oP3OcH/Pef0R4//3Mv5UL569JtLS0tLS0tLS0tLS0tLT/XhseVrTx6ac/4+ltGj5PI/RLGrLvyy/GB2c/j/k343hNHyRqT4+bX2lpaWlpaWlpaWlpaWlpaWdtuff0sDCfUJIv3MOC+HlNrfrH+19paWlpaWlpaWlpaWlpaf9z7VCqvI8zLlR538M9jt2y701eVC+WoVM3TktLS0tLS0tLS0tLS0tL269yDxuzy39vy8OGx0bubWcz+L4sqocJht6FU639UU0+LS0tLS0tLS0tLS0tLe0zaHu910KntGNa8S8XYpV7uLAvF4Z04fS4cE0XtqGo4Eed4mhpaWlpaWlpaWlpaWlp/3kWbhVG6EVbjdDLoH6cR+jNXvFfDy0tLS0tLS0tLS0tLS3t36w9zgXmvU5px7kmfTfPOGw6G9Pj+3R6rzWr7LdaEt+OlpaWlpaWlpaWlpaWlvaltGEf+jUdLhY3mp/afegLh3g3T2+mD6YblPd/TxMSu+/qE2hpaWlpaWlpaWlpaWlpV2rLEDw2Nw87u9/To6+JP6bx9CY0bxsfRe3Nhdx7bVs3YB/XzifQ0tLS0tLS0tLS0tLS0j6rNs4nDGkNfUhF6uPcy/wWJiRObbv05VZrGdfccaClpaWlpaWlpaWlpaWl/SXtkJavS+7h0bn3WrMIXwrJmwPA4kbuTgPyeMfQjI2WlpaWlpaWlpaWlpaW9gW1uTv6wsOGugT9NC4kL5nf0vTBvdygt0jeWZWnpaWlpaWlpaWlpaWlpX0t7Xm2NEXqOdd6PT+8bpwtOKYF/oXzy/KK/zBvhl93wjgtLS0tLS0tLS0tLS0t7TfaIRW1hwXue10EP/VeOw/TRu7rvIbeHAA2nRkWRuhxQN7U2Xc+IC0tLS0tLS0tLS0tLS3ta2nDAnezJp5vlVe8w+g/Vrkf69u8pVX5Q33hmM4M+za0tLS0tLS0tLS0tLS0tE+pzSv4Q5pPaI4r29eD/TyfsKAdxy9PRCsr/rcV8wm0tLS0tLS0tLS0tLS0tCu1uco933tXl6CHXmr30CntmHqvHdIL5u7ou7R5/BbO7C5HhMWyd1paWlpaWlpaWlpaWlraV9P25xN2qbl53jW+CZ3SyoTEtmibGYe3zhREft3ydjtaWlpaWlpaWlpaWlpa2t/Tjl/0Ugsngk2neof/3oXC8LIgntuFh1ZrvUO88wdZs4ZOS0tLS0tLS0tLS0tLS/uU2mFeIb8uFsGHM7t78wn5YcMX3dGnPzmlJfOxnmAYaGlpaWlpaWlpaWlpaWlfVHvP55c1/GwJzdDzfEKoIYjzA2/pgxw63dzyI2hpaWlpaWlpaWlpaWlpf0V7rIfPYSN3s7M7lKDHA8CasvePC1l7zzu784D8tKrKnZaWlpaWlpaWlpaWlpb2WbWHek089zLfp97n4/ywTZ5gGOud6vnM7ibNhMS6M7tpaWlpaWlpaWlpaWlpaZ9b2wz2g/bcLXuvpg/yPvTQ+W2aLfj4d5emIKr5hPKLy/LUBy0tLS0tLS0tLS0tLS3tem0eYL93atJzCfq+HmtfZv4taasjuoulKaT//CCnVSN0WlpaWlpaWlpaWlpaWtpn1zbzCRM/zBY0B5pdy6PDfEJYEN/2D0ALa+i9KvcV8wm0tLS0tLS0tLS0tLS0tKu0ofdaL+c0Qh/SgHyXlsybvd/TiLs5UqxXN97MCtDS0tLS0tLS0tLS0tLSvo427Oy+piO3N+HRzQnb+QbjXPb+nurm85p7PLO7WZUPh45daGlpaWlpaWlpaWlpaWlfT5sTBvubevQ/5Kr1MPrPB5o17dVzZ7WMa7a609LS0tLS0tLS0tLS0tL+ovaYbnVJNekL53eFe4/zEP9WLOeOthmhhzseH++zXbHiT0tLS0tLS0tLS0tLS0v7lNpD57Z52/mxW4K+qf9kQZv7re/qCYb8iFta1qelpaWlpaWlpaWlpaWlfS1t77/y+WWncayP6L7Xo/9K+9UHuTzq5qdTwMN8wriqJp+WlpaWlpaWlpaWlpaW9nttWUPflEcf6hXvc33k9pjO7A4r5Nuvdnbnuvmxs0xfdnaP350IRktLS0tLS0tLS0tLS0v7fNqMaxJ2jcd7n9LkwLFeQ+8XtW/qGvZt/1jwFVXutLS0tLS0tLS0tLS0tLTfa8OaeMZVFzJ/7C6qx/cf5gbkwzzEj5vHQ934P5xPoKWlpaWlpaWlpaWlpaV9Xm0zW9DTTrfK8wkff9KbPrjVVe7X9D7bur36sHY+gZaWlpaWlpaWlpaWlpb2CbXDbPksUu/1Xvvq3mHFf0hV7s2BZvlY8IXea7S0tLS0tLS0tLS0tLS0/x/tUJ8Iduzvw+7z96XsPS+ZZ+2YWq2FZ65ZQ6elpaWlpaWlpaWlpaWlfUptXuDOndIm7dsDly80Z3aP6byvTWd+YJqyGNtubkN96BgtLS0tLS0tLS0tLS0t7WtpwwnbYypSX9hG3tuH/tYtah/Tiv/yzvbzfKDZdcWZ3bS0tLS0tLS0tLS0tLS0K7VhQN4kVLnHnOZB/eHx9ErbedT0/nmE3nwQWlpaWlpaWlpaWlpaWtoX1DbNzfNfTqkXuKsV8lPadn6c19D3/RXyU+f9aWlpaWlpaWlpaWlpaWl/W1uePjUD7x3RHcbT5e2245dpeq81HckXbnAZvg0tLS0tLS0tLS0tLS0t7bNqe9MHudXalxu5c++1Yea812vuTTO25TkMWlpaWlpaWlpaWlpaWtrX0R46A/q8SXyce69dy67xsp4fWqdNC/yhO/rC+WWlO1u84ykdkUZLS0tLS0tLS0tLS0tL+yvac30hr6E3/cZLUXvVLrwzQs8f5N45dCyO0FesodPS0tLS0tLS0tLS0tLSPrc24A6d1uUf0wG38LCwqH6pT/XOR27nM8auqb16lTz/QEtLS0tLS0tLS0tLS0tLO+8aL6eTNRMM04r/JU0HhJeJJQBD94i03gcZVqz409LS0tLS0tLS0tLS0tL+SNsMn5tMNelhlf09LZlv6xF979Dv0D/91l/Wp6WlpaWlpaWlpaWlpaV9FW2nyr060Czfe6yr3Ptvl/nL3dyydvgXNfm0tLS0tLS0tLS0tLS0tD1tv/faplMEvks7uz9H6Id+A/JwANi4pI1D/PFnneJoaWlpaWlpaWlpaWlpaf9yrYiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIyN+S/wUAAP//sNS1UFFAPP8AAAAASUVORK5CYII=', '{\"accounts_info\":null,\"acquirer_reconciliation\":[],\"additional_info\":{\"bank_info\":{\"is_same_bank_account_owner\":true},\"tracking_id\":\"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none\"},\"authorization_code\":null,\"binary_mode\":false,\"brand_id\":null,\"build_version\":\"3.135.0-rc-1\",\"call_for_authorize_id\":null,\"callback_url\":null,\"captured\":true,\"card\":[],\"charges_details\":[{\"accounts\":{\"from\":\"collector\",\"to\":\"mp\"},\"amounts\":{\"original\":0.01,\"refunded\":0},\"client_id\":0,\"date_created\":\"2025-12-18T12:35:01.000-04:00\",\"external_charge_id\":\"01KCS516FXAER8JAF4F172BQG4\",\"id\":\"138451704968-001\",\"last_updated\":\"2025-12-18T12:35:01.000-04:00\",\"metadata\":{\"reason\":\"\",\"source\":\"proc-svc-charges\",\"source_detail\":\"processing_fee_charge\"},\"name\":\"mercadopago_fee\",\"refund_charges\":[],\"reserve_id\":null,\"type\":\"fee\"}],\"charges_execution_info\":{\"internal_execution\":{\"date\":\"2025-12-18T12:35:01.766-04:00\",\"execution_id\":\"01KCS516F5M3WVAKPX5KX110KQ\"}},\"collector_id\":426420888,\"corporation_id\":null,\"counter_currency\":null,\"coupon_amount\":0,\"currency_id\":\"BRL\",\"date_approved\":\"2025-12-18T12:35:23.000-04:00\",\"date_created\":\"2025-12-18T12:35:01.000-04:00\",\"date_last_updated\":\"2025-12-18T12:35:23.000-04:00\",\"date_of_expiration\":\"2025-12-19T12:35:01.000-04:00\",\"deduction_schema\":null,\"description\":\"Assinatura Plano teste - AgendaPro\",\"differential_pricing_id\":null,\"external_reference\":\"PLANO_5_EST_4\",\"fee_details\":[{\"amount\":0.01,\"fee_payer\":\"collector\",\"type\":\"mercadopago_fee\"}],\"financing_group\":null,\"id\":138451704968,\"installments\":1,\"integrator_id\":null,\"issuer_id\":\"12501\",\"live_mode\":true,\"marketplace_owner\":null,\"merchant_account_id\":null,\"merchant_number\":null,\"metadata\":[],\"money_release_date\":\"2025-12-18T12:35:23.000-04:00\",\"money_release_schema\":null,\"money_release_status\":\"released\",\"notification_url\":null,\"operation_type\":\"regular_payment\",\"order\":[],\"payer\":{\"email\":\"XXXXXXXXXXX\",\"entity_type\":null,\"first_name\":null,\"id\":\"2153563569\",\"identification\":{\"number\":null,\"type\":\"CPF\"},\"last_name\":null,\"operator_id\":null,\"phone\":{\"number\":null,\"extension\":null,\"area_code\":null},\"type\":null},\"payment_method\":{\"id\":\"pix\",\"issuer_id\":\"12501\",\"type\":\"bank_transfer\"},\"payment_method_id\":\"pix\",\"payment_type_id\":\"bank_transfer\",\"platform_id\":null,\"point_of_interaction\":{\"application_data\":{\"name\":null,\"operating_system\":null,\"version\":null},\"business_info\":{\"branch\":\"Merchant Services\",\"sub_unit\":\"default\",\"unit\":\"online_payments\"},\"location\":{\"source\":null,\"state_id\":null},\"sub_type\":\"INTER_PSP\",\"transaction_data\":{\"bank_info\":{\"collector\":{\"account_alias\":null,\"account_holder_name\":\"Rafael de Andrade Dias\",\"account_id\":40388482513,\"long_name\":\"MERCADO PAGO INSTITUI\\u00c7\\u00c3O DE PAGAMENTO LTDA.\",\"transfer_account_id\":null},\"is_same_bank_account_owner\":true,\"origin_bank_id\":null,\"origin_wallet_id\":null,\"payer\":{\"account_id\":72268491,\"branch\":\"1\",\"external_account_id\":null,\"id\":null,\"identification\":[],\"is_end_consumer\":null,\"long_name\":\"NU PAGAMENTOS S.A. - INSTITUI\\u00c7\\u00c3O DE PAGAMENTO\"}},\"bank_transfer_id\":120607091671,\"e2e_id\":null,\"financial_institution\":1,\"infringement_notification\":{\"status\":null,\"type\":null},\"merchant_category_code\":null,\"qr_code\":\"00020126580014br.gov.bcb.pix0136420ab7c4-4d63-46d4-809e-cd3eebc129ec52040000530398654041.005802BR5911DIRA93473616004Laje62250521mpqrinter1384517049686304D660\",\"qr_code_base64\":\"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKDUlEQVR42uzdQXLiuhYGYFMMGLIElsLSyNJYCkvIkAGFX3WC7HMkOfG9ybtVDd8\\/6So3sT97JunoaBAREREREREREREREREREREREREREREREREREREREZH\\/MIexzVv9o9Ofq7d0afNxYffx+\\/OfX9zzHT9+8ZnL4wb3j9+\\/z7\\/4zL4jONPS0tLS0tLS0tLS0tLSvp72XF94G4Zy7+kvM65oP3OcH\\/Pef0R4\\/\\/3Mv5UL569JtLS0tLS0tLS0tLS0tLT\\/XhseVrTx6ac\\/4+ltGj5PI\\/RLGrLvyy\\/GB2c\\/j\\/k343hNHyRqT4+bX2lpaWlpaWlpaWlpaWlpaWdtuff0sDCfUJIv3MOC+HlNrfrH+19paWlpaWlpaWlpaWlpaf9z7VCqvI8zLlR538M9jt2y701eVC+WoVM3TktLS0tLS0tLS0tLS0tL269yDxuzy39vy8OGx0bubWcz+L4sqocJht6FU639UU0+LS0tLS0tLS0tLS0tLe0zaHu910KntGNa8S8XYpV7uLAvF4Z04fS4cE0XtqGo4Eed4mhpaWlpaWlpaWlpaWlp\\/3kWbhVG6EVbjdDLoH6cR+jNXvFfDy0tLS0tLS0tLS0tLS3t36w9zgXmvU5px7kmfTfPOGw6G9Pj+3R6rzWr7LdaEt+OlpaWlpaWlpaWlpaWlvaltGEf+jUdLhY3mp\\/afegLh3g3T2+mD6YblPd\\/TxMSu+\\/qE2hpaWlpaWlpaWlpaWlpV2rLEDw2Nw87u9\\/To6+JP6bx9CY0bxsfRe3Nhdx7bVs3YB\\/XzifQ0tLS0tLS0tLS0tLS0j6rNs4nDGkNfUhF6uPcy\\/wWJiRObbv05VZrGdfccaClpaWlpaWlpaWlpaWl\\/SXtkJavS+7h0bn3WrMIXwrJmwPA4kbuTgPyeMfQjI2WlpaWlpaWlpaWlpaW9gW1uTv6wsOGugT9NC4kL5nf0vTBvdygt0jeWZWnpaWlpaWlpaWlpaWlpX0t7Xm2NEXqOdd6PT+8bpwtOKYF\\/oXzy\\/KK\\/zBvhl93wjgtLS0tLS0tLS0tLS0t7TfaIRW1hwXue10EP\\/VeOw\\/TRu7rvIbeHAA2nRkWRuhxQN7U2Xc+IC0tLS0tLS0tLS0tLS3ta2nDAnezJp5vlVe8w+g\\/Vrkf69u8pVX5Q33hmM4M+za0tLS0tLS0tLS0tLS0tE+pzSv4Q5pPaI4r29eD\\/TyfsKAdxy9PRCsr\\/rcV8wm0tLS0tLS0tLS0tLS0tCu1uco933tXl6CHXmr30CntmHqvHdIL5u7ou7R5\\/BbO7C5HhMWyd1paWlpaWlpaWlpaWlraV9P25xN2qbl53jW+CZ3SyoTEtmibGYe3zhREft3ydjtaWlpaWlpaWlpaWlpa2t\\/Tjl\\/0Ugsngk2neof\\/3oXC8LIgntuFh1ZrvUO88wdZs4ZOS0tLS0tLS0tLS0tLS\\/uU2mFeIb8uFsGHM7t78wn5YcMX3dGnPzmlJfOxnmAYaGlpaWlpaWlpaWlpaWlfVHvP55c1\\/GwJzdDzfEKoIYjzA2\\/pgxw63dzyI2hpaWlpaWlpaWlpaWlpf0V7rIfPYSN3s7M7lKDHA8CasvePC1l7zzu784D8tKrKnZaWlpaWlpaWlpaWlpb2WbWHek089zLfp97n4\\/ywTZ5gGOud6vnM7ibNhMS6M7tpaWlpaWlpaWlpaWlpaZ9b2wz2g\\/bcLXuvpg\\/yPvTQ+W2aLfj4d5emIKr5hPKLy\\/LUBy0tLS0tLS0tLS0tLS3tem0eYL93atJzCfq+HmtfZv4taasjuoulKaT\\/\\/CCnVSN0WlpaWlpaWlpaWlpaWtpn1zbzCRM\\/zBY0B5pdy6PDfEJYEN\\/2D0ALa+i9KvcV8wm0tLS0tLS0tLS0tLS0tKu0ofdaL+c0Qh\\/SgHyXlsybvd\\/TiLs5UqxXN97MCtDS0tLS0tLS0tLS0tLSvo427Oy+piO3N+HRzQnb+QbjXPb+nurm85p7PLO7WZUPh45daGlpaWlpaWlpaWlpaWlfT5sTBvubevQ\\/5Kr1MPrPB5o17dVzZ7WMa7a609LS0tLS0tLS0tLS0tL+ovaYbnVJNekL53eFe4\\/zEP9WLOeOthmhhzseH++zXbHiT0tLS0tLS0tLS0tLS0v7lNpD57Z52\\/mxW4K+qf9kQZv7re\\/qCYb8iFta1qelpaWlpaWlpaWlpaWlfS1t77\\/y+WWncayP6L7Xo\\/9K+9UHuTzq5qdTwMN8wriqJp+WlpaWlpaWlpaWlpaW9nttWUPflEcf6hXvc33k9pjO7A4r5Nuvdnbnuvmxs0xfdnaP350IRktLS0tLS0tLS0tLS0v7fNqMaxJ2jcd7n9LkwLFeQ+8XtW\\/qGvZt\\/1jwFVXutLS0tLS0tLS0tLS0tLTfa8OaeMZVFzJ\\/7C6qx\\/cf5gbkwzzEj5vHQ934P5xPoKWlpaWlpaWlpaWlpaV9Xm0zW9DTTrfK8wkff9KbPrjVVe7X9D7bur36sHY+gZaWlpaWlpaWlpaWlpb2CbXDbPksUu\\/1Xvvq3mHFf0hV7s2BZvlY8IXea7S0tLS0tLS0tLS0tLS0\\/x\\/tUJ8Iduzvw+7z96XsPS+ZZ+2YWq2FZ65ZQ6elpaWlpaWlpaWlpaWlfUptXuDOndIm7dsDly80Z3aP6byvTWd+YJqyGNtubkN96BgtLS0tLS0tLS0tLS0t7WtpwwnbYypSX9hG3tuH\\/tYtah\\/Tiv\\/yzvbzfKDZdcWZ3bS0tLS0tLS0tLS0tLS0K7VhQN4kVLnHnOZB\\/eHx9ErbedT0\\/nmE3nwQWlpaWlpaWlpaWlpaWtoX1DbNzfNfTqkXuKsV8lPadn6c19D3\\/RXyU+f9aWlpaWlpaWlpaWlpaWl\\/W1uePjUD7x3RHcbT5e2245dpeq81HckXbnAZvg0tLS0tLS0tLS0tLS0t7bNqe9MHudXalxu5c++1Yea812vuTTO25TkMWlpaWlpaWlpaWlpaWtrX0R46A\\/q8SXyce69dy67xsp4fWqdNC\\/yhO\\/rC+WWlO1u84ykdkUZLS0tLS0tLS0tLS0tL+yvac30hr6E3\\/cZLUXvVLrwzQs8f5N45dCyO0FesodPS0tLS0tLS0tLS0tLSPrc24A6d1uUf0wG38LCwqH6pT\\/XOR27nM8auqb16lTz\\/QEtLS0tLS0tLS0tLS0tLO+8aL6eTNRMM04r\\/JU0HhJeJJQBD94i03gcZVqz409LS0tLS0tLS0tLS0tL+SNsMn5tMNelhlf09LZlv6xF979Dv0D\\/91l\\/Wp6WlpaWlpaWlpaWlpaV9FW2nyr060Czfe6yr3Ptvl\\/nL3dyydvgXNfm0tLS0tLS0tLS0tLS0tD1tv\\/faplMEvks7uz9H6Id+A\\/JwANi4pI1D\\/PFnneJoaWlpaWlpaWlpaWlpaf9yrYiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIyN+S\\/wUAAP\\/\\/sNS1UFFAPP8AAAAASUVORK5CYII=\",\"ticket_url\":\"https:\\/\\/www.mercadopago.com.br\\/payments\\/138451704968\\/ticket?caller_id=2153563569&hash=82fe2520-4d61-48d9-b1c6-b9ecc3c348bd\",\"transaction_id\":\"PIXE18236120202512181635s0835940ee0\"},\"type\":\"OPENPLATFORM\"},\"pos_id\":null,\"processing_mode\":\"aggregator\",\"refunds\":[],\"release_info\":null,\"shipping_amount\":0,\"sponsor_id\":null,\"statement_descriptor\":null,\"status\":\"approved\",\"status_detail\":\"accredited\",\"store_id\":null,\"tags\":null,\"taxes_amount\":0,\"transaction_amount\":1,\"transaction_amount_refunded\":0,\"transaction_details\":{\"acquirer_reference\":null,\"bank_transfer_id\":120607091671,\"external_resource_url\":null,\"financial_institution\":\"1\",\"installment_amount\":0,\"net_received_amount\":0.99,\"overpaid_amount\":0,\"payable_deferral_period\":null,\"payment_method_reference_id\":null,\"total_paid_amount\":1,\"transaction_id\":\"PIXE18236120202512181635s0835940ee0\"}}', '2025-12-18 13:35:02', '2025-12-18 13:35:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `mercadopago_plan_id` varchar(100) DEFAULT NULL,
  `mercadopago_preapproval_plan_id` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `max_profissionais` int(11) DEFAULT 1,
  `max_agendamentos_mes` int(11) DEFAULT 100,
  `recursos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recursos`)),
  `ativo` tinyint(1) DEFAULT 1,
  `ordem` int(11) DEFAULT 0,
  `trial_dias` int(11) DEFAULT 7,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `slug`, `mercadopago_plan_id`, `mercadopago_preapproval_plan_id`, `descricao`, `valor_mensal`, `max_profissionais`, `max_agendamentos_mes`, `recursos`, `ativo`, `ordem`, `trial_dias`, `criado_em`, `atualizado_em`) VALUES
(1, 'Autônomo', 'autonomo', NULL, NULL, 'Ideal para profissionais independentes', 29.90, 1, 100, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_basicos\": true, \"suporte\": \"email\"}', 0, 1, 7, '2025-12-09 15:47:24', '2025-12-10 16:46:39'),
(2, 'Básico', 'basico', NULL, NULL, 'Para pequenos estabelecimentos', 79.90, 3, 300, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_basicos\": true, \"multi_profissionais\": true, \"suporte\": \"email\"}', 0, 2, 7, '2025-12-09 15:47:24', '2025-12-10 16:46:49'),
(3, 'Profissional', 'profissional', NULL, NULL, 'Para estabelecimentos em crescimento', 149.90, 10, 1000, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_avancados\": true, \"multi_profissionais\": true, \"api_acesso\": true, \"suporte\": \"chat\"}', 0, 3, 7, '2025-12-09 15:47:24', '2025-12-10 16:47:02'),
(4, 'Premium', 'premium', NULL, NULL, 'Recursos ilimitados', 299.90, 999, 999999, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_avancados\": true, \"multi_profissionais\": true, \"api_acesso\": true, \"suporte_prioritario\": true, \"personalizacao\": true, \"suporte\": \"telefone\"}', 0, 4, 7, '2025-12-09 15:47:24', '2025-12-10 16:47:19'),
(5, 'Plano teste', 'plano-teste', '803c2af3a0ca4ed6836d2b9cd4c1c4a3', NULL, '', 1.00, 1, 100, NULL, 1, 0, 7, '2025-12-10 16:19:20', '2025-12-10 16:19:21'),
(6, 'Plano Teste 2', 'plano-teste-2', 'a3496d44c2224563953adfd9ded7b596', NULL, 'Plano semanal', 1.00, 2, 10, NULL, 1, 0, 7, '2025-12-18 08:26:57', '2025-12-18 13:34:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `tipo` enum('vinculado','autonomo') DEFAULT 'vinculado',
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id`, `usuario_id`, `tipo`, `estabelecimento_id`, `nome`, `foto`, `whatsapp`, `email`, `status`, `criado_em`, `atualizado_em`) VALUES
(2, NULL, 'vinculado', 4, 'Mago', NULL, '75988890006', 'mago@gmail.com', 'ativo', '2025-12-11 14:45:40', '2025-12-11 15:48:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissional_servicos`
--

CREATE TABLE `profissional_servicos` (
  `id` int(11) UNSIGNED NOT NULL,
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `servico_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `profissional_servicos`
--

INSERT INTO `profissional_servicos` (`id`, `profissional_id`, `servico_id`) VALUES
(4, 2, 2),
(5, 2, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `promocoes`
--

CREATE TABLE `promocoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('percentual','valor_fixo') NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `tipo_cliente` enum('todos','novo','recorrente','vip') DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `duracao` int(11) NOT NULL COMMENT 'Duração em minutos',
  `preco` decimal(10,2) NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `estabelecimento_id`, `nome`, `descricao`, `duracao`, `preco`, `status`, `criado_em`, `atualizado_em`) VALUES
(2, 4, 'Barba', '', 20, 15.00, 'ativo', '2025-12-11 13:48:05', '2025-12-23 20:59:50'),
(3, 4, 'Cabelo máquina', '', 25, 20.00, 'ativo', '2025-12-11 13:48:31', '2025-12-23 20:59:59'),
(4, 4, 'Sombra', '', 10, 5.00, 'ativo', '2025-12-11 13:49:03', '2025-12-23 21:00:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `templates_notificacao`
--

CREATE TABLE `templates_notificacao` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `tipo` enum('confirmacao','lembrete','cancelamento','reagendamento','boas_vindas','pagamento','feedback') NOT NULL,
  `canal` enum('whatsapp','email','sms') NOT NULL,
  `assunto` varchar(255) DEFAULT NULL COMMENT 'Apenas para email',
  `mensagem` text NOT NULL,
  `variaveis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variaveis`)),
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('super_admin','estabelecimento','profissional') NOT NULL DEFAULT 'estabelecimento',
  `estabelecimento_id` int(11) UNSIGNED DEFAULT NULL,
  `profissional_id` int(11) UNSIGNED DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `primeiro_acesso` tinyint(1) DEFAULT 1,
  `token_reset_senha` varchar(100) DEFAULT NULL,
  `token_expiracao` datetime DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `senha`, `tipo`, `estabelecimento_id`, `profissional_id`, `nome`, `whatsapp`, `avatar`, `ativo`, `primeiro_acesso`, `token_reset_senha`, `token_expiracao`, `ultimo_acesso`, `criado_em`, `atualizado_em`) VALUES
(1, 'admin@sistema.com.br', '$2y$10$JTNUdyydaB7uARn1WIjjKOtMq27s49sTys2lrq2s3sI.do6S7KLM6', 'super_admin', NULL, NULL, 'Administrador', NULL, NULL, 1, 1, NULL, NULL, '2025-12-05 23:19:53', '2025-12-06 02:15:45', '2025-12-05 23:19:52'),
(2, 'rafaeldiaswebdev@gmail.com', '$2y$10$.fnjQyarGRJndBsuxfx1rO.wruBb6fYBK9xW/Khu34MNS4x9qC6/a', 'super_admin', NULL, NULL, 'Rafael de Andrade Dias', '75988890006', NULL, 1, 1, NULL, NULL, '2025-12-26 10:08:47', '2025-12-05 23:22:40', '2025-12-26 10:08:47'),
(3, 'rafaeldiastecinfo@gmail.com', '$2y$10$gQF2S67E1CmOiQyZO8sAiORTTvPXOYL0S2..EnpueOpJ.P1fZSeI.', 'estabelecimento', 2, NULL, 'Barbearia do Perfil', '75988890006', NULL, 1, 1, NULL, NULL, '2025-12-11 14:15:02', '2025-12-10 15:11:36', '2025-12-11 14:15:02'),
(5, 'modelo@gmail.com', '$2y$10$PmIna00GgWeHervGmzIyzepdhvKJTrGvn6ywVWY98ICr8n7ch2g02', 'estabelecimento', 4, NULL, 'modelo barber', '', NULL, 1, 1, NULL, NULL, '2025-12-27 16:16:06', '2025-12-10 17:06:18', '2025-12-27 16:16:06'),
(6, 'mago@gmail.com', '$2y$10$tYpUKYjrVrdr9YLzmQUsv.trVAQ1YyOc7zIUHh2bnLG67keCPna86', 'profissional', 4, 2, 'Mago', '', NULL, 1, 1, NULL, NULL, '2025-12-27 16:17:07', '2025-12-11 14:45:40', '2025-12-27 16:17:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_backup`
--

CREATE TABLE `usuarios_backup` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `nivel` enum('admin','usuario') DEFAULT 'usuario' COMMENT 'Nível de acesso do usuário',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `ultimo_acesso` datetime DEFAULT NULL,
  `token_recuperacao` varchar(100) DEFAULT NULL COMMENT 'Token para recuperação de senha',
  `token_expiracao` datetime DEFAULT NULL COMMENT 'Data de expiração do token',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios_backup`
--

INSERT INTO `usuarios_backup` (`id`, `nome`, `email`, `senha`, `telefone`, `avatar`, `nivel`, `status`, `ultimo_acesso`, `token_recuperacao`, `token_expiracao`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@sistema.com.br', '$2y$10$JTNUdyydaB7uARn1WIjjKOtMq27s49sTys2lrq2s3sI.do6S7KLM6', NULL, NULL, 'admin', 'ativo', '2025-12-05 23:19:53', NULL, NULL, '2025-12-06 02:15:45', '2025-12-05 23:19:52'),
(2, 'Rafael de Andrade Dias', 'rafaeldiaswebdev@gmail.com', '$2y$10$.fnjQyarGRJndBsuxfx1rO.wruBb6fYBK9xW/Khu34MNS4x9qC6/a', '75988890006', NULL, 'admin', 'ativo', '2025-12-06 15:14:32', NULL, NULL, '2025-12-05 23:22:40', '2025-12-06 15:14:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `whatsapp_conversas`
--

CREATE TABLE `whatsapp_conversas` (
  `id` int(11) UNSIGNED NOT NULL,
  `cliente_id` int(11) UNSIGNED DEFAULT NULL,
  `whatsapp_numero` varchar(20) NOT NULL,
  `etapa` varchar(50) DEFAULT NULL COMMENT 'servico, profissional, data, hora, confirmacao',
  `dados_temporarios` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Armazena escolhas durante a conversa' CHECK (json_valid(`dados_temporarios`)),
  `ultima_interacao` datetime DEFAULT NULL,
  `expira_em` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `whatsapp_sessoes`
--

CREATE TABLE `whatsapp_sessoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL,
  `instance_name` varchar(100) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `status` enum('desconectado','conectado','erro') DEFAULT 'desconectado',
  `ultima_conexao` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data` (`data`),
  ADD KEY `idx_profissional_data` (`profissional_id`,`data`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_servico` (`servico_id`),
  ADD KEY `idx_estabelecimento_data` (`estabelecimento_id`,`data`),
  ADD KEY `idx_profissional_status` (`profissional_id`,`status`);

--
-- Índices de tabela `assinaturas`
--
ALTER TABLE `assinaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_plano` (`plano_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_data_fim` (`data_fim`),
  ADD KEY `idx_mercadopago` (`mercadopago_subscription_id`);

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nota` (`nota`),
  ADD KEY `idx_agendamento` (`agendamento_id`);

--
-- Índices de tabela `bloqueios`
--
ALTER TABLE `bloqueios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_profissional_data` (`profissional_id`,`data`),
  ADD KEY `idx_profissional` (`profissional_id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data_inicio` (`data_inicio`),
  ADD KEY `idx_datas` (`data_inicio`,`data_fim`),
  ADD KEY `idx_criado_por` (`criado_por`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_whatsapp` (`whatsapp`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_estabelecimento_tipo` (`estabelecimento_id`,`tipo`),
  ADD KEY `idx_email` (`email`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`),
  ADD KEY `idx_grupo` (`grupo`);

--
-- Índices de tabela `disponibilidade`
--
ALTER TABLE `disponibilidade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_profissional` (`profissional_id`);

--
-- Índices de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj_cpf` (`cnpj_cpf`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_plano_vencimento` (`plano_vencimento`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_plano` (`plano_id`);

--
-- Índices de tabela `feriados`
--
ALTER TABLE `feriados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_data` (`data`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `horarios_estabelecimento`
--
ALTER TABLE `horarios_estabelecimento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_estabelecimento_dia` (`estabelecimento_id`,`dia_semana`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_dia_semana` (`dia_semana`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_acao` (`acao`),
  ADD KEY `idx_tabela` (`tabela`),
  ADD KEY `idx_criado_em` (`criado_em`),
  ADD KEY `idx_usuario_acao` (`usuario_id`,`acao`),
  ADD KEY `idx_tabela_registro` (`tabela`,`registro_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_lida` (`lida`),
  ADD KEY `idx_criado_em` (`criado_em`);

--
-- Índices de tabela `notificacoes_config`
--
ALTER TABLE `notificacoes_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_estabelecimento_tipo` (`estabelecimento_id`,`tipo`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mercadopago_id` (`mercadopago_id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_ordem` (`ordem`),
  ADD KEY `idx_mp_plan` (`mercadopago_plan_id`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_tipo` (`tipo`);

--
-- Índices de tabela `profissional_servicos`
--
ALTER TABLE `profissional_servicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_profissional_servico` (`profissional_id`,`servico_id`),
  ADD KEY `fk_ps_servico` (`servico_id`);

--
-- Índices de tabela `promocoes`
--
ALTER TABLE `promocoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_datas` (`data_inicio`,`data_fim`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `templates_notificacao`
--
ALTER TABLE `templates_notificacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_estabelecimento_tipo_canal` (`estabelecimento_id`,`tipo`,`canal`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_canal` (`canal`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_profissional` (`profissional_id`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `usuarios_backup`
--
ALTER TABLE `usuarios_backup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_nivel` (`nivel`);

--
-- Índices de tabela `whatsapp_conversas`
--
ALTER TABLE `whatsapp_conversas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_whatsapp` (`whatsapp_numero`),
  ADD KEY `idx_expira` (`expira_em`),
  ADD KEY `idx_cliente` (`cliente_id`);

--
-- Índices de tabela `whatsapp_sessoes`
--
ALTER TABLE `whatsapp_sessoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_numero` (`numero`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `assinaturas`
--
ALTER TABLE `assinaturas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `bloqueios`
--
ALTER TABLE `bloqueios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `disponibilidade`
--
ALTER TABLE `disponibilidade`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `feriados`
--
ALTER TABLE `feriados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `horarios_estabelecimento`
--
ALTER TABLE `horarios_estabelecimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes_config`
--
ALTER TABLE `notificacoes_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `profissional_servicos`
--
ALTER TABLE `profissional_servicos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `promocoes`
--
ALTER TABLE `promocoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `templates_notificacao`
--
ALTER TABLE `templates_notificacao`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios_backup`
--
ALTER TABLE `usuarios_backup`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `whatsapp_conversas`
--
ALTER TABLE `whatsapp_conversas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `whatsapp_sessoes`
--
ALTER TABLE `whatsapp_sessoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `fk_agendamentos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_agendamentos_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_agendamentos_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_agendamentos_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `assinaturas`
--
ALTER TABLE `assinaturas`
  ADD CONSTRAINT `fk_assinaturas_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assinaturas_plano` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `fk_avaliacoes_agendamento` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `bloqueios`
--
ALTER TABLE `bloqueios`
  ADD CONSTRAINT `fk_bloqueios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `disponibilidade`
--
ALTER TABLE `disponibilidade`
  ADD CONSTRAINT `fk_disponibilidade_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  ADD CONSTRAINT `fk_estabelecimentos_plano` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_estabelecimentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notificacoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios_backup` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes_config`
--
ALTER TABLE `notificacoes_config`
  ADD CONSTRAINT `fk_notificacoes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `profissionais`
--
ALTER TABLE `profissionais`
  ADD CONSTRAINT `fk_profissionais_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_profissionais_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `profissional_servicos`
--
ALTER TABLE `profissional_servicos`
  ADD CONSTRAINT `fk_ps_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `promocoes`
--
ALTER TABLE `promocoes`
  ADD CONSTRAINT `fk_promocoes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `fk_servicos_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `templates_notificacao`
--
ALTER TABLE `templates_notificacao`
  ADD CONSTRAINT `fk_templates_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuarios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `whatsapp_conversas`
--
ALTER TABLE `whatsapp_conversas`
  ADD CONSTRAINT `fk_conversas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `whatsapp_sessoes`
--
ALTER TABLE `whatsapp_sessoes`
  ADD CONSTRAINT `fk_whatsapp_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
