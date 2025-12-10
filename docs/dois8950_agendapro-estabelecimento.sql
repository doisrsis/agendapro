-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 10/12/2025 às 15:43
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
  `telefone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `plano` enum('trimestral','semestral','anual') DEFAULT 'trimestral',
  `plano_vencimento` date DEFAULT NULL,
  `status` enum('ativo','inativo','suspenso','cancelado') DEFAULT 'ativo',
  `tempo_minimo_agendamento` int(11) DEFAULT 60 COMMENT 'Minutos antes do serviço',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `estabelecimentos`
--

INSERT INTO `estabelecimentos` (`id`, `usuario_id`, `plano_id`, `mp_access_token_test`, `mp_public_key_test`, `mp_access_token_prod`, `mp_public_key_prod`, `mp_webhook_url`, `mp_sandbox`, `evolution_api_url`, `evolution_api_key`, `evolution_instance_name`, `whatsapp_numero`, `whatsapp_conectado`, `notificar_whatsapp`, `notificar_email`, `data_cadastro`, `nome`, `cnpj_cpf`, `endereco`, `cep`, `cidade`, `estado`, `telefone`, `whatsapp`, `email`, `logo`, `plano`, `plano_vencimento`, `status`, `tempo_minimo_agendamento`, `criado_em`, `atualizado_em`) VALUES
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 1, 1, '2025-12-10 15:11:36', 'Barbearia do Perfil', '30016264000100', '126 Rua da Maçonaria', '45490-000', 'Laje', 'BA', '75988890006', '(75) 98889-0006', 'rafaeldiastecinfo@gmail.com', '3f1910611744789aee75ca0b4b437574.png', 'trimestral', '2026-01-31', 'ativo', 60, '2025-12-10 15:11:36', '2025-12-10 15:29:55'),
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 1, 1, '2025-12-10 15:38:06', 'Barbearia Modelo', '', '126 Rua da Maçonaria', '45490-000', 'Laje', 'BA', '75988890006', '(75) 98889-0006', 'modelo@gmail.com', NULL, 'trimestral', '2025-12-31', 'ativo', 60, '2025-12-10 15:38:06', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `slug`, `descricao`, `valor_mensal`, `max_profissionais`, `max_agendamentos_mes`, `recursos`, `ativo`, `ordem`, `trial_dias`, `criado_em`, `atualizado_em`) VALUES
(1, 'Autônomo', 'autonomo', 'Ideal para profissionais independentes', 29.90, 1, 100, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_basicos\": true, \"suporte\": \"email\"}', 1, 1, 7, '2025-12-09 15:47:24', NULL),
(2, 'Básico', 'basico', 'Para pequenos estabelecimentos', 79.90, 3, 300, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_basicos\": true, \"multi_profissionais\": true, \"suporte\": \"email\"}', 1, 2, 7, '2025-12-09 15:47:24', NULL),
(3, 'Profissional', 'profissional', 'Para estabelecimentos em crescimento', 149.90, 10, 1000, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_avancados\": true, \"multi_profissionais\": true, \"api_acesso\": true, \"suporte\": \"chat\"}', 1, 3, 7, '2025-12-09 15:47:24', NULL),
(4, 'Premium', 'premium', 'Recursos ilimitados', 299.90, 999, 999999, '{\"whatsapp\": true, \"mercadopago\": true, \"relatorios_avancados\": true, \"multi_profissionais\": true, \"api_acesso\": true, \"suporte_prioritario\": true, \"personalizacao\": true, \"suporte\": \"telefone\"}', 1, 4, 7, '2025-12-09 15:47:24', NULL);

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
  `telefone` varchar(20) DEFAULT NULL,
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

INSERT INTO `usuarios` (`id`, `email`, `senha`, `tipo`, `estabelecimento_id`, `profissional_id`, `nome`, `telefone`, `avatar`, `ativo`, `primeiro_acesso`, `token_reset_senha`, `token_expiracao`, `ultimo_acesso`, `criado_em`, `atualizado_em`) VALUES
(1, 'admin@sistema.com.br', '$2y$10$JTNUdyydaB7uARn1WIjjKOtMq27s49sTys2lrq2s3sI.do6S7KLM6', 'super_admin', NULL, NULL, 'Administrador', NULL, NULL, 1, 1, NULL, NULL, '2025-12-05 23:19:53', '2025-12-06 02:15:45', '2025-12-05 23:19:52'),
(2, 'rafaeldiaswebdev@gmail.com', '$2y$10$.fnjQyarGRJndBsuxfx1rO.wruBb6fYBK9xW/Khu34MNS4x9qC6/a', 'super_admin', NULL, NULL, 'Rafael de Andrade Dias', '75988890006', NULL, 1, 1, NULL, NULL, '2025-12-10 15:37:17', '2025-12-05 23:22:40', '2025-12-10 15:37:17'),
(3, 'rafaeldiastecinfo@gmail.com', '$2y$10$gQF2S67E1CmOiQyZO8sAiORTTvPXOYL0S2..EnpueOpJ.P1fZSeI.', 'estabelecimento', 2, NULL, 'Barbearia do Perfil', '75988890006', NULL, 1, 1, NULL, NULL, '2025-12-10 15:36:54', '2025-12-10 15:11:36', '2025-12-10 15:36:54'),
(4, 'modelo@gmail.com', '$2y$10$3FgfreTY4CfupAUmJ2SUie1xMrgNdEIUJjw5LtavdVwtp4E49Tiga', 'estabelecimento', 3, NULL, 'Barbearia Modelo', '75988890006', NULL, 1, 1, NULL, NULL, '2025-12-10 15:38:21', '2025-12-10 15:38:06', '2025-12-10 15:38:21');

--
-- Índices para tabelas despejadas
--

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
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_ordem` (`ordem`);

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  ADD CONSTRAINT `fk_estabelecimentos_plano` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_estabelecimentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuarios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
