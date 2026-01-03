-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 03/01/2026 às 18:32
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
(26, 'mercadopago_webhook_url_prod', 'https://iafila.doisr.com.br/webhook/mercadopago', 'texto', 'mercadopago', NULL, '2025-12-06 15:41:29', '2025-12-18 12:26:44'),
(27, 'waha_api_url', 'https://zaptotal.doisrsistemas.com.br', 'texto', 'waha', 'URL da API WAHA', '2025-12-28 10:24:14', '2025-12-29 08:44:15'),
(28, 'waha_api_key', 'b781f3e57f4e4c4ba3a67df819050e6e', 'texto', 'waha', 'API Key para autenticação na WAHA', '2025-12-28 10:24:14', '2025-12-29 08:44:15'),
(29, 'waha_session_name', 'doisr', 'texto', 'waha', 'Nome da sessão WAHA para o SaaS', '2025-12-28 10:24:14', '2025-12-29 08:44:15'),
(30, 'waha_webhook_url', '', 'texto', 'waha', 'URL do webhook para receber mensagens', '2025-12-28 10:24:14', '2025-12-29 08:44:15'),
(31, 'waha_status', 'conectado', 'texto', 'waha', 'Status da conexão: desconectado, conectando, conectado', '2025-12-28 10:24:14', '2026-01-02 21:33:27'),
(32, 'waha_numero_conectado', '557599448068@c.us', 'texto', 'waha', 'Número conectado no formato 5511999999999', '2025-12-28 10:24:14', '2026-01-02 21:33:27'),
(33, 'waha_ativo', '0', 'texto', 'waha', 'Se a integração WAHA está ativa (0 ou 1)', '2025-12-28 10:24:14', '2025-12-29 08:44:15'),
(42, 'waha_usar_para_estabelecimentos', '1', 'texto', 'waha', 'Se os estabelecimentos devem usar a mesma API WAHA do SaaS', '2025-12-28 11:22:25', NULL),
(43, 'cron_token', 'b781f3e57f4e4c4ba3a67df819050e6e', 'texto', 'cron', 'Token de segurança para execução do cron', '2025-12-28 16:02:41', '2025-12-28 16:17:18');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`),
  ADD KEY `idx_grupo` (`grupo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
