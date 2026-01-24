-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 17/01/2026 às 14:47
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
(3, 4, 'Mazinho', '', '(75) 997058104', '75997058104', '', NULL, 'vip', 70, '2025-12-11 13:46:45', '2026-01-17 14:17:49'),
(5, 4, 'Mary', '044.596.125-26', '75988890006', '75988890006', 'rafaeldiastecinfo@gmail.com', NULL, 'vip', 16, '2025-12-28 13:36:12', '2026-01-17 14:17:49'),
(6, 4, 'Rafael Dias', NULL, '557588890006', '557588890006', NULL, NULL, 'vip', 65, '2025-12-29 09:03:38', '2026-01-17 14:17:49'),
(9, 4, 'Cliente WhatsApp', NULL, '557597058104', '557597058104', NULL, NULL, 'novo', 1, '2026-01-09 21:14:44', '2026-01-17 14:17:49'),
(10, 4, 'Railda Oliveira', NULL, '108259113467972@lid', '108259113467972', NULL, NULL, 'novo', 1, '2026-01-16 22:26:19', '2026-01-17 14:17:49'),
(11, 4, 'Josmar Almeida', NULL, '166554637451296@lid', '166554637451296', NULL, NULL, 'recorrente', 2, '2026-01-17 14:44:29', '2026-01-17 14:46:06');

--
-- Índices para tabelas despejadas
--

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
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_telefone` (`telefone`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `estabelecimentos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
