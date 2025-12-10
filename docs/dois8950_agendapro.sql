-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 09/12/2025 às 15:38
-- Versão do servidor: 10.11.14-MariaDB-cll-lve
-- Versão do PHP: 8.4.15

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
  `profissional_id` int(11) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `dia_todo` tinyint(1) DEFAULT 0,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `telefone` varchar(20) DEFAULT NULL,
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

INSERT INTO `clientes` (`id`, `estabelecimento_id`, `nome`, `cpf`, `whatsapp`, `telefone`, `email`, `foto`, `tipo`, `total_agendamentos`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 'Rodnei', '', '(75) 98889-0006', '', '', NULL, 'novo', 0, '2025-12-06 16:19:15', NULL);

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
(20, 'mercadopago_sandbox', '1', 'texto', 'mercadopago', NULL, '2025-12-06 14:32:17', '2025-12-06 17:26:40'),
(21, 'mercadopago_access_token_test', 'TEST-8383394053049490-120613-7960752b164bee3710a580fae67c765f-426420888', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-06 17:26:40'),
(22, 'mercadopago_public_key_test', 'TEST-77eeee04-0f59-4dbe-a0b3-66847ffd2f86', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-06 17:26:40'),
(23, 'mercadopago_access_token_prod', 'APP_USR-8383394053049490-120613-d828c32bc0d495191bb6a1dd77be362b-426420888', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-06 17:26:40'),
(24, 'mercadopago_public_key_prod', 'APP_USR-f07e3741-1415-4973-8645-e07b066a13c1', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-06 17:26:40'),
(25, 'mercadopago_webhook_url_test', 'https://iafila.doisr.com.br/webhook/mercadopago', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-06 17:26:40'),
(26, 'mercadopago_webhook_url_prod', 'https://iafila.doisr.com.br/webhook/mercadopago', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-06 17:26:40');

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
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `estabelecimentos`
--

INSERT INTO `estabelecimentos` (`id`, `nome`, `cnpj_cpf`, `endereco`, `cep`, `cidade`, `estado`, `telefone`, `whatsapp`, `email`, `logo`, `plano`, `plano_vencimento`, `status`, `tempo_minimo_agendamento`, `criado_em`, `atualizado_em`) VALUES
(1, 'Barbearia do Bruxo', '04459612526', '126 Rua da Maçonaria', '45490-000', 'Laje', 'BA', '75988890006', '75988890006', 'rafaeldiastecinfo@gmail.com', 'faa3230e6afdc262d7e5b160ca385965.png', 'trimestral', '0000-00-00', 'ativo', 120, '2025-12-05 23:49:45', NULL);

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
(7, 2, 'login', 'usuarios', 2, NULL, NULL, '170.239.39.246', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 15:14:32');

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
(1, 1, 'confirmacao', 'Olá {cliente}! ✅ Seu agendamento foi confirmado!\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nNos vemos em breve!', 1),
(2, 1, 'cancelamento', 'Olá {cliente}. ❌ Seu agendamento foi cancelado.\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n\\nQualquer dúvida, entre em contato!', 1),
(3, 1, 'reagendamento', 'Olá {cliente}! 🔄 Seu agendamento foi reagendado.\\n\\n📅 Nova Data: {data}\\n🕐 Novo Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}', 1),
(4, 1, 'lembrete_1dia', 'Olá {cliente}! 🔔 Lembrete: você tem um agendamento amanhã!\\n\\n📅 Data: {data}\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nTe esperamos!', 1),
(5, 1, 'lembrete_1hora', 'Olá {cliente}! ⏰ Seu agendamento é daqui a 1 hora!\\n\\n🕐 Horário: {hora}\\n💇 Serviço: {servico}\\n👤 Profissional: {profissional}\\n\\nEstamos te esperando!', 1),
(6, 1, 'pagamento', 'Olá {cliente}! 💰 Pagamento confirmado!\\n\\n✅ Valor: R$ {valor}\\n📅 Agendamento: {data} às {hora}\\n\\nObrigado pela preferência!', 1),
(7, 1, 'feedback', 'Olá {cliente}! 🌟 Como foi sua experiência?\\n\\nGostaríamos de saber sua opinião sobre o atendimento de {profissional}.\\n\\nAvalie aqui: {link}', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` int(11) UNSIGNED NOT NULL,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id`, `estabelecimento_id`, `nome`, `foto`, `whatsapp`, `telefone`, `email`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 'Rodrigo Barbosa', NULL, '75988890006', '75988890006', 'rafaeldiastecinfo@gmail.com', 'ativo', '2025-12-06 16:17:26', NULL);

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
(1, 1, 1);

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
(1, 1, 'Cabelo', '', 21, 15.00, 'ativo', '2025-12-06 16:18:21', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
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
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `avatar`, `nivel`, `status`, `ultimo_acesso`, `token_recuperacao`, `token_expiracao`, `criado_em`, `atualizado_em`) VALUES
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
  ADD KEY `idx_servico` (`servico_id`);

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
  ADD KEY `idx_profissional_data` (`profissional_id`,`data`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_whatsapp` (`whatsapp`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`);

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
  ADD KEY `idx_plano_vencimento` (`plano_vencimento`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_acao` (`acao`),
  ADD KEY `idx_tabela` (`tabela`),
  ADD KEY `idx_criado_em` (`criado_em`);

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
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_mercadopago_id` (`mercadopago_id`),
  ADD KEY `idx_agendamento` (`agendamento_id`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_status` (`status`);

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
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `bloqueios`
--
ALTER TABLE `bloqueios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes_config`
--
ALTER TABLE `notificacoes_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `profissional_servicos`
--
ALTER TABLE `profissional_servicos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `promocoes`
--
ALTER TABLE `promocoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
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
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `fk_avaliacoes_agendamento` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `bloqueios`
--
ALTER TABLE `bloqueios`
  ADD CONSTRAINT `fk_bloqueios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;

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
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notificacoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes_config`
--
ALTER TABLE `notificacoes_config`
  ADD CONSTRAINT `fk_notificacoes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `fk_pagamentos_agendamento` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `profissionais`
--
ALTER TABLE `profissionais`
  ADD CONSTRAINT `fk_profissionais_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;

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
