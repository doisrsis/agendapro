-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 25/01/2026 às 09:52
-- Versão do servidor: 10.11.15-MariaDB-cll-lve
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
  `hora_inicio_real` time DEFAULT NULL,
  `hora_fim_real` time DEFAULT NULL,
  `status` enum('pendente','confirmado','em_atendimento','finalizado','cancelado','reagendado','nao_compareceu') NOT NULL DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `pagamento_id` int(11) DEFAULT NULL,
  `pagamento_status` enum('pendente','pago','expirado','cancelado','nao_requerido') DEFAULT 'nao_requerido',
  `forma_pagamento` enum('pix','presencial','cartao','nao_definido') DEFAULT 'nao_definido' COMMENT 'Forma de pagamento escolhida pelo cliente',
  `pagamento_valor` decimal(10,2) DEFAULT NULL,
  `pagamento_pix_qrcode` text DEFAULT NULL,
  `pagamento_pix_copia_cola` text DEFAULT NULL,
  `pagamento_expira_em` datetime DEFAULT NULL,
  `cancelado_por` enum('cliente','profissional','admin','sistema') DEFAULT NULL,
  `motivo_cancelamento` text DEFAULT NULL,
  `qtd_reagendamentos` tinyint(4) DEFAULT 0 COMMENT 'Quantidade de vezes que foi reagendado',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `pagamento_lembrete_enviado` tinyint(1) DEFAULT 0 COMMENT 'Flag indicando se lembrete de pagamento foi enviado',
  `pagamento_token` varchar(64) DEFAULT NULL COMMENT 'Token único para acesso público à página de pagamento',
  `pagamento_expira_adicional_em` datetime DEFAULT NULL COMMENT 'Data/hora de expiração após tempo adicional',
  `confirmacao_enviada` tinyint(1) DEFAULT 0 COMMENT 'Flag se pedido de confirmação foi enviado',
  `confirmacao_enviada_em` datetime DEFAULT NULL COMMENT 'Quando o pedido foi enviado',
  `confirmado_em` datetime DEFAULT NULL COMMENT 'Quando o cliente confirmou presença',
  `lembrete_enviado` tinyint(1) DEFAULT 0 COMMENT 'Flag se lembrete pré-atendimento foi enviado',
  `lembrete_enviado_em` datetime DEFAULT NULL COMMENT 'Quando o lembrete foi enviado',
  `confirmacao_tentativas` tinyint(1) UNSIGNED DEFAULT 0 COMMENT 'Número de tentativas de confirmação enviadas (0-3)',
  `confirmacao_ultima_tentativa` datetime DEFAULT NULL COMMENT 'Data/hora da última tentativa de confirmação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `estabelecimento_id`, `cliente_id`, `profissional_id`, `servico_id`, `data`, `hora_inicio`, `hora_fim`, `hora_inicio_real`, `hora_fim_real`, `status`, `observacoes`, `pagamento_id`, `pagamento_status`, `forma_pagamento`, `pagamento_valor`, `pagamento_pix_qrcode`, `pagamento_pix_copia_cola`, `pagamento_expira_em`, `cancelado_por`, `motivo_cancelamento`, `qtd_reagendamentos`, `criado_em`, `atualizado_em`, `pagamento_lembrete_enviado`, `pagamento_token`, `pagamento_expira_adicional_em`, `confirmacao_enviada`, `confirmacao_enviada_em`, `confirmado_em`, `lembrete_enviado`, `lembrete_enviado_em`, `confirmacao_tentativas`, `confirmacao_ultima_tentativa`) VALUES
(240, 4, 14, 2, 5, '2026-01-24', '13:00:00', '13:55:00', NULL, NULL, 'pendente', 'Agendado via WhatsApp Bot', NULL, 'pendente', 'pix', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-24 11:29:05', NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL),
(241, 4, 14, 2, 5, '2026-01-24', '14:00:00', '14:55:00', NULL, NULL, 'cancelado', 'Agendado via WhatsApp Bot', NULL, 'pendente', 'pix', 1.00, NULL, '0002010126720014br.gov.bcb.pix0132420ab7c44d6346d4809ecd3eebc129ec0214CABELO E BARBA52040000530398654041.005802BR5922RAFAEL DE ANDRADE DIAS6004LAJE62160512AG000000024163042E25', NULL, 'cliente', 'Cancelado via WhatsApp Bot', 0, '2026-01-24 11:42:14', '2026-01-24 16:31:45', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL),
(242, 4, 14, 2, 2, '2026-01-24', '15:00:00', '15:20:00', NULL, NULL, 'pendente', 'Agendado via WhatsApp Bot', NULL, 'pendente', 'pix', 1.00, NULL, '0002010126570014br.gov.bcb.pix0126rafaeldiaswebdev@gmail.com0205BARBA52040000530398654041.005802BR5922RAFAEL DE ANDRADE DIAS6004LAJE62160512AG0000000242630451E0', NULL, NULL, NULL, 0, '2026-01-24 11:44:31', '2026-01-24 11:44:31', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL),
(243, 4, 14, 2, 2, '2026-01-24', '15:30:00', '15:50:00', NULL, NULL, 'pendente', 'Agendado via WhatsApp Bot', NULL, 'pago', 'pix', 1.00, NULL, '00020101021126570014br.gov.bcb.pix0126rafaeldiaswebdev@gmail.com0205BARBA52040000530398654041.005802BR5922RAFAEL DE ANDRADE DIAS6004LAJE62160512AG000000024363044C79', NULL, NULL, NULL, 0, '2026-01-24 12:03:47', '2026-01-24 17:31:46', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL),
(244, 4, 14, 2, 2, '2026-01-24', '18:30:00', '18:50:00', NULL, NULL, 'finalizado', 'Agendado via WhatsApp Bot', NULL, 'pago', 'pix', 1.00, NULL, '00020101021126570014br.gov.bcb.pix0126rafaeldiaswebdev@gmail.com0205BARBA52040000530398654041.005802BR5922RAFAEL DE ANDRADE DIAS6004LAJE62160512AG000000024463042BAD', NULL, NULL, NULL, 0, '2026-01-24 17:11:17', '2026-01-24 17:22:04', 0, NULL, NULL, 0, NULL, '2026-01-24 17:20:56', 0, NULL, 0, NULL),
(245, 4, 14, 2, 2, '2026-01-24', '18:30:00', '18:50:00', NULL, NULL, 'finalizado', 'Agendado via WhatsApp Bot', NULL, 'pago', 'pix', 1.00, NULL, '00020101021126570014br.gov.bcb.pix0126rafaeldiaswebdev@gmail.com0205BARBA52040000530398654041.005802BR5922RAFAEL DE ANDRADE DIAS6004LAJE62160512AG0000000245630481FC', NULL, NULL, NULL, 0, '2026-01-24 17:29:04', '2026-01-24 17:30:13', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL),
(246, 4, 14, 2, 5, '2026-01-24', '19:00:00', '19:55:00', NULL, NULL, 'finalizado', 'Agendado via WhatsApp Bot', NULL, 'pago', 'pix', 1.00, NULL, '00020101021126660014br.gov.bcb.pix0126rafaeldiaswebdev@gmail.com0214CABELO E BARBA52040000530398654041.005802BR5922RAFAEL DE ANDRADE DIAS6004LAJE62160512AG00000002466304FB0D', NULL, NULL, NULL, 0, '2026-01-24 17:35:28', '2026-01-24 17:36:11', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL),
(247, 4, 14, 2, 2, '2026-01-25', '11:00:00', '11:20:00', NULL, NULL, 'reagendado', '', NULL, 'pendente', 'nao_definido', 1.00, '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', '00020126580014br.gov.bcb.pix0136420ab7c4-4d63-46d4-809e-cd3eebc129ec52040000530398654041.005802BR5911DIRA93473616004Laje62250521mpqrinter1434431715166304B4D1', '2026-01-25 09:50:43', 'cliente', 'Reagendado para 25/01/2026 às 11:00', 0, '2026-01-25 09:45:42', '2026-01-25 09:52:01', 1, 'a50cbcf67efe8b00653ff829b7673384', '2026-01-25 09:54:01', 0, NULL, NULL, 0, NULL, 0, NULL),
(248, 4, 14, 2, 2, '2026-01-25', '11:00:00', '11:20:00', NULL, NULL, 'confirmado', 'Reagendado de 25/01/2026 às 11:00', NULL, 'pendente', 'nao_definido', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-25 09:49:29', '2026-01-25 09:49:29', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL);

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
  ADD KEY `idx_profissional_status` (`profissional_id`,`status`),
  ADD KEY `idx_pagamento_status` (`pagamento_status`),
  ADD KEY `idx_pagamento_expira` (`pagamento_expira_em`),
  ADD KEY `idx_pagamento_pendente` (`pagamento_status`,`pagamento_expira_em`),
  ADD KEY `idx_pagamento_token` (`pagamento_token`),
  ADD KEY `idx_confirmacao_tentativas` (`status`,`data`,`confirmacao_tentativas`,`confirmacao_ultima_tentativa`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
