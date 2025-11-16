-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 16/11/2025 às 19:13
-- Versão do servidor: 10.11.14-MariaDB-cll-lve
-- Versão do PHP: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cecriativocom_lecortine_orc`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`, `descricao`, `icone`, `imagem`, `ordem`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Cortinas', 'cortinas', 'Cortinas em tecido e rolô', NULL, NULL, 1, 'ativo', '2025-11-13 21:58:49', NULL),
(2, 'Toldos', 'toldos', 'Toldos e coberturas', NULL, NULL, 2, 'ativo', '2025-11-13 21:58:49', NULL),
(3, 'Motorizadas', 'motorizadas', 'Cortinas com automação', NULL, NULL, 3, 'ativo', '2025-11-13 21:58:49', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `origem` varchar(50) DEFAULT 'site',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `whatsapp`, `cpf_cnpj`, `endereco`, `cidade`, `estado`, `cep`, `observacoes`, `origem`, `criado_em`, `atualizado_em`) VALUES
(2, 'COMPRA DE PLUGINS', 'doisr.sistemas@gmail.com', '(75) 9888-9000', '(75) 98889-0006', NULL, 'Rua Nova Brasília, 162 - São Benedito', 'Santo Antônio de Jesus', 'SP', '44441-534', NULL, 'site', '2025-11-14 14:36:50', NULL),
(3, 'Rafael de Andrade Dias', 'rafaeldiaswebdev@gmail.com', '(75) 9888-9000', '(75) 99249-5077', '04459612526', 'Rua da Maçonaria, 126 - Farmácia Menor Preço - Centro', 'Laje', 'RJ', '45490-000', 'Farmácia Menor Preço', 'site', '2025-11-14 15:03:25', '2025-11-15 15:38:58'),
(4, 'Rafael de Andrade Dias', 'rafaeldiastecinfo@gmail.com', '(75) 9888-9000', '(75) 98889-0006', NULL, NULL, NULL, NULL, NULL, NULL, 'site', '2025-11-15 17:38:59', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `colecoes`
--

CREATE TABLE `colecoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `colecoes`
--

INSERT INTO `colecoes` (`id`, `nome`, `slug`, `descricao`, `imagem`, `ordem`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Tecidos Nobres', 'tecidos-nobres', 'Linho Rústico e Linen Light', NULL, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(2, 'Rolô Translúcida', 'rolo-translucida', 'Coleção translúcida para cortinas rolô', NULL, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(3, 'Rolô Blackout', 'rolo-blackout', 'Coleção blackout para cortinas rolô', NULL, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(4, 'Rolô Tela Solar', 'rolo-tela-solar', 'Tela solar 5% para cortinas rolô', NULL, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(5, 'Duplex Translúcida', 'duplex-translucida', 'Coleção translúcida para Duplex VIP', NULL, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(6, 'Rafael Dias', 'rafael-dias', '', NULL, 0, 'ativo', '2025-11-16 00:10:40', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json','arquivo') DEFAULT 'texto',
  `grupo` varchar(50) DEFAULT 'geral',
  `descricao` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `grupo`, `descricao`, `criado_em`, `atualizado_em`) VALUES
(1, 'empresa_nome', 'Le Cortine', 'texto', 'empresa', 'Nome da empresa', '2025-11-13 21:58:37', '2025-11-15 01:08:57'),
(2, 'empresa_email', 'contato@lecortine.com.br', 'texto', 'empresa', 'E-mail principal', '2025-11-13 21:58:37', '2025-11-15 01:08:57'),
(5, 'site_titulo', 'Le Cortine - Orçamento Online', 'texto', 'site', 'Título do site', '2025-11-13 21:58:37', NULL),
(6, 'orcamento_validade_dias', '15', 'numero', 'orcamento', 'Validade do orçamento em dias', '2025-11-13 21:58:37', NULL),
(9, 'empresa_cnpj', '23.316.772/0001-32', 'texto', 'geral', 'CNPJ da empresa', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(13, 'empresa_cep', '44330-074', 'texto', 'geral', 'CEP da empresa (origem do frete)', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(14, 'empresa_endereco', 'Av. Dois de Julho,100b - Centro', 'texto', 'geral', 'Endereço completo', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(15, 'empresa_cidade', 'Santo Antônio de Jesus', 'texto', 'geral', 'Cidade', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(16, 'empresa_estado', 'BA', 'texto', 'geral', 'Estado (UF)', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(17, 'retirada_local_ativa', '1', 'booleano', 'geral', 'Permitir retirada no local', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(18, 'retirada_local_endereco', 'Av. Dois de Julho,100b - Centro CEP: 44330-074 Santo Antônio de Jesus - Ba Em frente ao Shopping Itaguary', 'texto', 'geral', 'Endereço para retirada', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(19, 'frete_gratis_acima', '0', 'numero', 'geral', 'Valor mínimo para frete grátis (0 = desabilitado)', '2025-11-14 16:01:00', '2025-11-15 01:08:57'),
(20, 'correios_ativo', '0', 'booleano', 'correios', 'Ativar integração com Correios', '2025-11-14 16:01:00', NULL),
(21, 'correios_ambiente', 'teste', 'texto', 'correios', 'Ambiente: teste ou producao', '2025-11-14 16:01:00', NULL),
(22, 'correios_usuario', '', 'texto', 'correios', 'Usuário dos Correios (Código Administrativo)', '2025-11-14 16:01:00', NULL),
(23, 'correios_senha', '', 'texto', 'correios', 'Senha dos Correios', '2025-11-14 16:01:00', NULL),
(24, 'correios_contrato', '', 'texto', 'correios', 'Número do contrato', '2025-11-14 16:01:00', NULL),
(25, 'correios_cartao_postagem', '', 'texto', 'correios', 'Cartão de postagem', '2025-11-14 16:01:00', NULL),
(26, 'correios_servicos', 'PAC,SEDEX', 'texto', 'correios', 'Serviços disponíveis (separados por vírgula)', '2025-11-14 16:01:00', NULL),
(27, 'correios_prazo_adicional', '0', 'numero', 'correios', 'Dias adicionais ao prazo', '2025-11-14 16:01:00', NULL),
(28, 'correios_valor_adicional', '0', 'numero', 'correios', 'Valor adicional ao frete (R$)', '2025-11-14 16:01:00', NULL),
(29, 'correios_percentual_adicional', '0', 'numero', 'correios', 'Percentual adicional ao frete (%)', '2025-11-14 16:01:00', NULL),
(30, 'correios_formato', '1', 'numero', 'correios', 'Formato: 1=Caixa/Pacote, 2=Rolo/Prisma, 3=Envelope', '2025-11-14 16:01:00', NULL),
(31, 'correios_peso_padrao', '1', 'numero', 'correios', 'Peso padrão por m² (kg)', '2025-11-14 16:01:00', NULL),
(32, 'correios_comprimento', '30', 'numero', 'correios', 'Comprimento padrão (cm)', '2025-11-14 16:01:00', NULL),
(33, 'correios_largura', '20', 'numero', 'correios', 'Largura padrão (cm)', '2025-11-14 16:01:00', NULL),
(34, 'correios_altura', '10', 'numero', 'correios', 'Altura padrão (cm)', '2025-11-14 16:01:00', NULL),
(35, 'correios_mao_propria', '0', 'booleano', 'correios', 'Mão própria', '2025-11-14 16:01:00', NULL),
(36, 'correios_aviso_recebimento', '0', 'booleano', 'correios', 'Aviso de recebimento', '2025-11-14 16:01:00', NULL),
(37, 'correios_valor_declarado', '0', 'booleano', 'correios', 'Declarar valor da mercadoria', '2025-11-14 16:01:00', NULL),
(38, 'mercadopago_ativo', '0', 'booleano', 'mercadopago', 'Ativar integração com Mercado Pago', '2025-11-14 16:01:00', NULL),
(39, 'mercadopago_ambiente', 'teste', 'texto', 'mercadopago', 'Ambiente: teste ou producao', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(40, 'mercadopago_public_key_teste', 'APP_USR-8b1a2bb5-2cff-4f04-a791-d9a118205b75', 'texto', 'mercadopago', 'Public Key (Teste)', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(41, 'mercadopago_access_token_teste', 'APP_USR-5284058023661787-111419-1344e667deb750270eae6b1d80506ca3-1487175873', 'texto', 'mercadopago', 'Access Token (Teste)', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(42, 'mercadopago_public_key_prod', '', 'texto', 'mercadopago', 'Public Key (Produção)', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(43, 'mercadopago_access_token_prod', '', 'texto', 'mercadopago', 'Access Token (Produção)', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(44, 'mercadopago_metodos', 'credit_card,debit_card,pix', 'texto', 'mercadopago', 'Métodos de pagamento aceitos', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(45, 'mercadopago_max_parcelas', '12', 'numero', 'mercadopago', 'Número máximo de parcelas', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(46, 'mercadopago_parcela_minima', '5', 'numero', 'mercadopago', 'Valor mínimo da parcela (R$)', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(47, 'mercadopago_juros', '0', 'numero', 'mercadopago', 'Taxa de juros (% ao mês)', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(48, 'mercadopago_url_sucesso', '', 'texto', 'mercadopago', 'URL de retorno (sucesso)', '2025-11-14 16:01:00', '2025-11-15 19:27:42'),
(49, 'mercadopago_url_pendente', '', 'texto', 'mercadopago', 'URL de retorno (pendente)', '2025-11-14 16:01:00', '2025-11-15 19:27:42'),
(50, 'mercadopago_url_falha', '', 'texto', 'mercadopago', 'URL de retorno (falha)', '2025-11-14 16:01:00', '2025-11-15 19:27:42'),
(51, 'mercadopago_url_webhook', '', 'texto', 'mercadopago', 'URL para webhook (notificações)', '2025-11-14 16:01:00', '2025-11-15 19:27:42'),
(52, 'mercadopago_statement_descriptor', 'LE CORTINE', 'texto', 'mercadopago', 'Nome na fatura do cartão', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(53, 'mercadopago_auto_return', '1', 'booleano', 'mercadopago', 'Retorno automático após pagamento', '2025-11-14 16:01:00', '2025-11-15 19:27:41'),
(54, 'mercadopago_binary_mode', '0', 'booleano', 'mercadopago', 'Modo binário (aprovado/rejeitado apenas)', '2025-11-14 16:01:00', NULL),
(55, 'email_notificacoes_ativo', '1', 'booleano', 'notificacoes', 'Enviar e-mails de notificação', '2025-11-14 16:01:00', '2025-11-15 00:51:13'),
(56, 'email_novo_orcamento', '1', 'booleano', 'notificacoes', 'Notificar novos orçamentos', '2025-11-14 16:01:00', '2025-11-15 00:51:14'),
(57, 'email_pagamento_aprovado', '1', 'booleano', 'notificacoes', 'Notificar pagamentos aprovados', '2025-11-14 16:01:00', '2025-11-15 00:51:14'),
(58, 'email_destinatario', 'orcamentos@lecortine.com.br', 'texto', 'notificacoes', 'E-mail para receber notificações', '2025-11-14 16:01:00', '2025-11-15 00:51:14'),
(59, 'whatsapp_notificacoes_ativo', '1', 'booleano', 'notificacoes', 'Enviar notificações via WhatsApp', '2025-11-14 16:01:00', '2025-11-15 00:51:14'),
(60, 'whatsapp_numero_notificacao', '5575992495077', 'texto', 'notificacoes', 'Número para receber notificações', '2025-11-14 16:01:00', '2025-11-15 00:51:14'),
(61, 'notif_sistema_novo_orcamento', '1', 'texto', 'notificacoes', NULL, '2025-11-14 20:10:04', '2025-11-15 00:51:14'),
(62, 'notif_sistema_pagamento', '1', 'texto', 'notificacoes', NULL, '2025-11-14 20:10:04', '2025-11-15 00:51:14'),
(63, 'notif_sistema_som', '1', 'texto', 'notificacoes', NULL, '2025-11-14 20:10:04', '2025-11-15 00:51:14'),
(70, 'telefone', '75992495077', 'texto', 'geral', 'Telefone principal da empresa', '2025-11-14 21:08:23', '2025-11-15 01:08:57'),
(71, 'whatsapp', '5575992495077', 'texto', 'geral', 'WhatsApp da empresa (com DDI e DDD)', '2025-11-14 21:08:23', '2025-11-15 01:08:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cores`
--

CREATE TABLE `cores` (
  `id` int(11) UNSIGNED NOT NULL,
  `tecido_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo_hex` varchar(7) DEFAULT NULL COMMENT 'Código hexadecimal da cor',
  `imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cores`
--

INSERT INTO `cores` (`id`, `tecido_id`, `nome`, `codigo_hex`, `imagem`, `ordem`, `status`, `criado_em`) VALUES
(1, 1, 'Bege Natural', '#D4C4A8', NULL, 1, 'ativo', '2025-11-13 21:58:49'),
(2, 1, 'Cinza Claro', '#C0C0C0', NULL, 2, 'ativo', '2025-11-13 21:58:49'),
(3, 1, 'Marrom Terra', '#8B7355', NULL, 3, 'ativo', '2025-11-13 21:58:49'),
(4, 1, 'Off White', '#F5F5DC', NULL, 4, 'ativo', '2025-11-13 21:58:49'),
(5, 1, 'Grafite', '#4A4A4A', NULL, 5, 'ativo', '2025-11-13 21:58:49'),
(6, 1, 'Areia', '#E6D7C3', NULL, 6, 'ativo', '2025-11-13 21:58:49'),
(7, 2, 'Branco', '#FFFFFF', NULL, 1, 'ativo', '2025-11-13 21:58:49'),
(8, 2, 'Champagne', '#F7E7CE', NULL, 2, 'ativo', '2025-11-13 21:58:49'),
(9, 2, 'Cinza Pérola', '#E8E8E8', NULL, 3, 'ativo', '2025-11-13 21:58:49'),
(10, 2, 'Bege Rosado', '#E8C4A8', NULL, 4, 'ativo', '2025-11-13 21:58:49'),
(11, 2, 'Taupe', '#B38B6D', NULL, 5, 'ativo', '2025-11-13 21:58:49'),
(12, 2, 'Cru Natural', '#F5F5DC', NULL, 6, 'ativo', '2025-11-13 21:58:49'),
(13, 3, 'Branco', '#FFFFFF', NULL, 1, 'ativo', '2025-11-13 21:58:49'),
(14, 3, 'Off White', '#FAF0E6', NULL, 2, 'ativo', '2025-11-13 21:58:49'),
(15, 3, 'Bege', '#F5F5DC', NULL, 3, 'ativo', '2025-11-13 21:58:49'),
(16, 3, 'Areia', '#E6D7C3', NULL, 4, 'ativo', '2025-11-13 21:58:49'),
(17, 3, 'Cinza Claro', '#D3D3D3', NULL, 5, 'ativo', '2025-11-13 21:58:49'),
(18, 3, 'Cinza Médio', '#A9A9A9', NULL, 6, 'ativo', '2025-11-13 21:58:49'),
(19, 3, 'Champagne', '#F7E7CE', NULL, 7, 'ativo', '2025-11-13 21:58:49'),
(20, 3, 'Palha', '#F0E68C', NULL, 8, 'ativo', '2025-11-13 21:58:49'),
(21, 4, 'Branco', '#FFFFFF', NULL, 1, 'ativo', '2025-11-13 21:58:49'),
(22, 4, 'Bege', '#F5F5DC', NULL, 2, 'ativo', '2025-11-13 21:58:49'),
(23, 4, 'Cinza Claro', '#D3D3D3', NULL, 3, 'ativo', '2025-11-13 21:58:49'),
(24, 4, 'Cinza Médio', '#808080', NULL, 4, 'ativo', '2025-11-13 21:58:49'),
(25, 4, 'Cinza Escuro', '#696969', NULL, 5, 'ativo', '2025-11-13 21:58:49'),
(26, 4, 'Grafite', '#4A4A4A', NULL, 6, 'ativo', '2025-11-13 21:58:49'),
(27, 4, 'Preto', '#000000', NULL, 7, 'ativo', '2025-11-13 21:58:49'),
(28, 4, 'Areia', '#E6D7C3', NULL, 8, 'ativo', '2025-11-13 21:58:49'),
(29, 5, 'Branco', '#FFFFFF', NULL, 1, 'ativo', '2025-11-13 21:58:49'),
(30, 5, 'Cinza Claro', '#D3D3D3', NULL, 2, 'ativo', '2025-11-13 21:58:49'),
(31, 5, 'Cinza Médio', '#A9A9A9', NULL, 3, 'ativo', '2025-11-13 21:58:49'),
(32, 5, 'Cinza Escuro', '#696969', NULL, 4, 'ativo', '2025-11-13 21:58:49'),
(33, 5, 'Grafite', '#4A4A4A', NULL, 5, 'ativo', '2025-11-13 21:58:49'),
(34, 5, 'Preto', '#000000', NULL, 6, 'ativo', '2025-11-13 21:58:49'),
(35, 6, 'Branco', '#FFFFFF', NULL, 1, 'ativo', '2025-11-13 21:58:49'),
(36, 6, 'Off White', '#FAF0E6', NULL, 2, 'ativo', '2025-11-13 21:58:49'),
(37, 6, 'Bege', '#F5F5DC', NULL, 3, 'ativo', '2025-11-13 21:58:49'),
(38, 6, 'Areia', '#E6D7C3', NULL, 4, 'ativo', '2025-11-13 21:58:49'),
(39, 6, 'Cinza Claro', '#D3D3D3', NULL, 5, 'ativo', '2025-11-13 21:58:49'),
(40, 6, 'Cinza Médio', '#A9A9A9', NULL, 6, 'ativo', '2025-11-13 21:58:49'),
(41, 6, 'Champagne', '#F7E7CE', NULL, 7, 'ativo', '2025-11-13 21:58:49'),
(42, 6, 'Pérola', '#E8E8E8', NULL, 8, 'ativo', '2025-11-13 21:58:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `extras`
--

CREATE TABLE `extras` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo_preco` enum('fixo','percentual','por_m2') DEFAULT 'fixo',
  `valor` decimal(10,2) NOT NULL,
  `aplicavel_a` text DEFAULT NULL COMMENT 'JSON com IDs de produtos aplicáveis',
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `extras`
--

INSERT INTO `extras` (`id`, `nome`, `descricao`, `tipo_preco`, `valor`, `aplicavel_a`, `ordem`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Blackout até 2,00m', 'Forro blackout com 80% de vedação de luz - Largura até 2,00m', 'fixo', 250.00, '1', 1, 'ativo', '2025-11-13 21:58:49', NULL),
(2, 'Blackout até 3,00m', 'Forro blackout com 80% de vedação de luz - Largura até 3,00m', 'fixo', 300.00, '1', 2, 'ativo', '2025-11-13 21:58:49', NULL),
(3, 'Blackout até 4,00m', 'Forro blackout com 80% de vedação de luz - Largura até 4,00m', 'fixo', 360.00, '1', 3, 'ativo', '2025-11-13 21:58:49', NULL),
(4, 'Blackout até 5,00m', 'Forro blackout com 80% de vedação de luz - Largura até 5,00m', 'fixo', 395.00, '1', 4, 'ativo', '2025-11-13 21:58:49', NULL),
(5, 'Motorização', 'Motor elétrico com controle remoto', 'fixo', 850.00, '1,2,3', 5, 'ativo', '2025-11-13 21:58:49', NULL),
(6, 'Instalação Profissional', 'Serviço de instalação por profissional', 'fixo', 150.00, '1,2,3', 6, 'ativo', '2025-11-13 21:58:49', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `tabela` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
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
(1, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 14:30:47'),
(2, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 14:43:30'),
(3, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 14:44:22'),
(4, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 14:50:04'),
(5, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 14:52:02'),
(6, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 19:18:21'),
(7, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 23:32:37'),
(8, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 00:48:10'),
(9, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 15:16:26'),
(10, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 15:39:57'),
(11, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 15:57:42'),
(12, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 15:58:12'),
(13, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 15:58:26'),
(14, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 15:59:29'),
(15, 1, 'login', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 16:01:52'),
(16, 1, 'logout', 'usuarios', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 16:03:46'),
(17, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 16:06:49'),
(18, 2, 'logout', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 16:09:47'),
(19, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 16:10:25'),
(20, 2, 'login', 'usuarios', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 20:14:36'),
(21, 2, 'criar', 'colecoes', 6, NULL, '{\"nome\":\"Rafael Dias\",\"descricao\":\"\",\"status\":\"ativo\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-16 00:10:40'),
(22, 2, 'criar', 'tecidos', 7, NULL, '{\"nome\":\"Doisr Sistemas\",\"colecao_id\":\"6\",\"codigo\":\"\",\"descricao\":\"\",\"composicao\":\"\",\"largura_padrao\":null,\"tipo\":\"outro\",\"preco_adicional\":0,\"status\":\"ativo\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-16 00:11:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `tipo` enum('info','sucesso','aviso','erro') DEFAULT 'info',
  `titulo` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos`
--

CREATE TABLE `orcamentos` (
  `id` int(11) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL COMMENT 'Número único do orçamento',
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `tipo_atendimento` enum('orcamento','consultoria') DEFAULT 'orcamento',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `valor_final` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacoes_cliente` text DEFAULT NULL,
  `observacoes_internas` text DEFAULT NULL,
  `status` enum('pendente','em_analise','aprovado','recusado','cancelado','em_preparacao','pedido_postado') NOT NULL DEFAULT 'pendente',
  `enviado_whatsapp` tinyint(1) DEFAULT 0,
  `enviado_email` tinyint(1) DEFAULT 0,
  `data_envio_whatsapp` datetime DEFAULT NULL,
  `data_envio_email` datetime DEFAULT NULL,
  `ip_cliente` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamentos`
--

INSERT INTO `orcamentos` (`id`, `numero`, `cliente_id`, `tipo_atendimento`, `valor_total`, `desconto`, `valor_final`, `observacoes_cliente`, `observacoes_internas`, `status`, `enviado_whatsapp`, `enviado_email`, `data_envio_whatsapp`, `data_envio_email`, `ip_cliente`, `user_agent`, `criado_em`, `atualizado_em`) VALUES
(1, 'ORC-202511-0001', 2, '', 2410.87, 0.00, 2410.87, NULL, NULL, 'pendente', 1, 0, '2025-11-14 14:46:19', NULL, NULL, NULL, '2025-11-14 14:36:50', '2025-11-14 14:46:19'),
(2, 'ORC-202511-0002', 3, '', 1587.00, 0.00, 1587.00, NULL, 'Desistiu do Blackout', 'pendente', 1, 0, '2025-11-14 15:05:29', NULL, NULL, NULL, '2025-11-14 15:03:25', '2025-11-14 15:05:59'),
(3, 'ORC-202511-0003', 3, '', 1191.10, 0.00, 1191.10, NULL, NULL, 'pendente', 0, 0, NULL, NULL, NULL, NULL, '2025-11-14 23:42:14', NULL),
(4, 'ORC-202511-0004', 3, '', 1478.13, 0.00, 1478.13, NULL, NULL, 'pendente', 0, 0, NULL, NULL, NULL, NULL, '2025-11-15 00:12:59', NULL),
(5, 'ORC-202511-0005', 3, '', 3324.88, 0.00, 3324.88, NULL, NULL, 'aprovado', 0, 0, NULL, NULL, NULL, NULL, '2025-11-15 01:11:22', '2025-11-15 20:16:22'),
(10, 'ORC-202511-0010', 3, '', 692.00, 0.00, 692.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 16:51:40', '2025-11-15 16:51:40'),
(11, 'ORC-202511-0011', 3, '', 585.00, 0.00, 585.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 17:11:31', '2025-11-15 17:11:31'),
(12, 'ORC-202511-0012', 3, '', 585.00, 0.00, 585.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 17:33:42', '2025-11-15 17:33:42'),
(13, 'ORC-202511-0013', 4, '', 585.00, 0.00, 585.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 17:38:59', '2025-11-15 17:38:59'),
(14, 'ORC-202511-0014', 3, '', 885.00, 0.00, 885.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 17:41:11', '2025-11-15 17:41:12'),
(15, 'ORC-202511-0015', 3, 'orcamento', 692.00, 0.00, 692.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 19:44:43', '2025-11-15 19:44:43'),
(16, 'ORC-202511-0016', 3, 'orcamento', 885.00, 0.00, 885.00, NULL, NULL, 'pendente', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 20:10:37', '2025-11-15 20:10:37'),
(17, 'ORC-202511-0017', 3, 'orcamento', 1587.00, 0.00, 1587.00, NULL, NULL, 'pedido_postado', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 20:16:41', '2025-11-15 21:52:54'),
(18, 'ORC-202511-0018', 3, 'orcamento', 2341.78, 0.00, 2341.78, NULL, NULL, 'em_analise', 0, 0, NULL, NULL, '::1', NULL, '2025-11-15 23:50:17', '2025-11-15 23:51:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_email_logs`
--

CREATE TABLE `orcamento_email_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `orcamento_id` int(10) UNSIGNED NOT NULL,
  `tipo` varchar(40) NOT NULL,
  `destinatario` varchar(255) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `status` enum('sucesso','erro') NOT NULL DEFAULT 'sucesso',
  `preview` varchar(500) DEFAULT NULL,
  `corpo` mediumtext DEFAULT NULL,
  `erro` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamento_email_logs`
--

INSERT INTO `orcamento_email_logs` (`id`, `orcamento_id`, `tipo`, `destinatario`, `assunto`, `status`, `preview`, `corpo`, `erro`, `criado_em`) VALUES
(1, 17, 'status_em_analise', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0017 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Orçamento em Análise</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Em analise\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Seu orçamento está sendo analisado por nossa equipe. Aguarde nosso retorno!</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0017<br>\n                        <strong>Data:</strong> 15/11/2025 20:16<br>\n                        <strong>Valor:</strong> R$ 1.587,00\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 20:36:53'),
(2, 17, 'status_aprovado', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0017 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #28a745; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Orçamento Aprovado! ????</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Aprovado\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Ótimas notícias! Seu orçamento foi aprovado. Em breve entraremos em contato para finalizar os detalhes.</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0017<br>\n                        <strong>Data:</strong> 15/11/2025 20:16<br>\n                        <strong>Valor:</strong> R$ 1.587,00\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 20:38:39'),
(3, 17, 'status_em_preparacao', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0017 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #ffc107; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Orçamento Recebido</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Em preparacao\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Recebemos seu orçamento e estamos analisando. Em breve entraremos em contato!</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0017<br>\n                        <strong>Data:</strong> 15/11/2025 20:16<br>\n                        <strong>Valor:</strong> R$ 1.587,00\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 20:59:02'),
(4, 17, 'status_pedido_postado', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0017 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #ffc107; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Orçamento Recebido</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Pedido postado\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Recebemos seu orçamento e estamos analisando. Em breve entraremos em contato!</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0017<br>\n                        <strong>Data:</strong> 15/11/2025 20:16<br>\n                        <strong>Valor:</strong> R$ 1.587,00\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 21:00:00'),
(5, 17, 'status_em_preparacao', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0017 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Seu Pedido Está em Preparação</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Em preparacao\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Estamos preparando seu pedido com todo o cuidado. Assim que estiver pronto para envio, avisaremos você!</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0017<br>\n                        <strong>Data:</strong> 15/11/2025 20:16<br>\n                        <strong>Valor:</strong> R$ 1.587,00\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 21:52:18'),
(6, 17, 'status_pedido_postado', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0017 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #6f42c1; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Pedido Postado ????</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Pedido postado\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Ótimas notícias! Seu pedido já foi postado. Em breve você receberá o código de rastreio para acompanhar a entrega.</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0017<br>\n                        <strong>Data:</strong> 15/11/2025 20:16<br>\n                        <strong>Valor:</strong> R$ 1.587,00\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 21:52:55'),
(7, 18, 'novo_orcamento_empresa', 'orcamentos@lecortine.com.br', '???? Novo Orçamento #18 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }\n                .info-box { background: white; padding: 20px; margin: 20px 0; borde', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"UTF-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }\n                .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }\n                .info-row { margin: 10px 0; }\n                .label { font-weight: bold; color: #667eea; }\n                .valor { font-size: 24px; color: #28a745; font-weight: bold; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>???? Novo Orçamento!</h1>\n                    <p style=\"margin: 0; font-size: 18px;\">Orçamento #18</p>\n                </div>\n                <div class=\"content\">\n                    <p><strong>Um novo cliente solicitou orçamento!</strong></p>\n                    \n                    <div class=\"info-box\">\n                        <h3 style=\"margin-top: 0; color: #667eea;\">???? Dados do Cliente</h3>\n                        <div class=\"info-row\"><span class=\"label\">Nome:</span> Rafael de Andrade Dias</div>\n                        <div class=\"info-row\"><span class=\"label\">E-mail:</span> rafaeldiaswebdev@gmail.com</div>\n                        <div class=\"info-row\"><span class=\"label\">Telefone:</span> (75) 9888-9000</div>\n                        \n                        <div class=\"info-row\"><span class=\"label\">WhatsApp:</span> (75) 98889-0006</div>\n                        <div style=\"margin-top: 15px; text-align: center;\">\n                            <a href=\"https://api.whatsapp.com/send?phone=75988890006&text=Olá Rafael+de+Andrade+Dias, tudo bem? Aqui é da Le Cortine! Vi que você solicitou um orçamento. Vamos conversar?\" \n                               style=\"display: inline-block; background: #25D366; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;\">\n                                ???? Chamar no WhatsApp\n                            </a>\n                        </div>\n                    </div>\n                    \n                    <div class=\"info-box\">\n                        <h3 style=\"margin-top: 0; color: #667eea;\">????️ Detalhes do Pedido</h3>\n                        <div class=\"info-row\"><span class=\"label\">Produto:</span> Cortina Rolô</div>\n                        <div class=\"info-row\"><span class=\"label\">Tecido:</span> Rolô Translúcida</div>\n                        <div class=\"info-row\"><span class=\"label\">Dimensões:</span> 3,89m x 2,80m</div>\n                        <div class=\"info-row\"><span class=\"label\">Entrega:</span> ???? Retirada no Local</div>\n                    </div>\n                    \n                    <div class=\"info-box\" style=\"text-align: center;\">\n                        <h3 style=\"margin-top: 0; color: #667eea;\">???? Valor Estimado</h3>\n                        <div class=\"valor\">R$ 2.341,78</div>\n                        <small style=\"color: #666;\">*Frete não incluso</small>\n                    </div>\n                    \n                    <p style=\"margin-top: 30px; color: #666;\">\n                        <strong>Próximos passos:</strong><br>\n                        1. Acesse o painel admin para ver detalhes<br>\n                        2. Entre em contato com o cliente via WhatsApp<br>\n                        3. Calcule o frete e envie o orçamento final\n                    </p>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 23:50:18'),
(8, 18, 'novo_orcamento_cliente', 'rafaeldiaswebdev@gmail.com', '✅ Seu Orçamento foi Recebido - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }\n                .info-box { background: white; padding: 20px; margin: 20px 0; borde', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"UTF-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }\n                .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }\n                .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }\n                .btn-whatsapp { display: inline-block; background: #25D366; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }\n                .contatos { background: #fff3cd; border: 1px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>???? Obrigado!</h1>\n                    <p style=\"margin: 0; font-size: 18px;\">Seu orçamento foi recebido com sucesso</p>\n                </div>\n                <div class=\"content\">\n                    <p>Olá <strong>Rafael de Andrade Dias</strong>,</p>\n                    \n                    <p style=\"font-size: 16px; line-height: 1.8;\">\n                        Muito obrigado por escolher a <strong>Le Cortine</strong> para transformar seu ambiente! \n                        Ficamos muito felizes em saber do seu interesse em nossos produtos.\n                    </p>\n                    \n                    <div class=\"success\">\n                        <strong>✅ Seu orçamento foi recebido!</strong><br>\n                        Número do orçamento: <strong>#18</strong>\n                    </div>\n                    \n                    <div class=\"info-box\">\n                        <h3 style=\"margin-top: 0; color: #667eea;\">???? Resumo do seu Pedido</h3>\n                        <p><strong>Produto:</strong> Cortina Rolô</p>\n                        <p><strong>Tecido:</strong> Rolô Translúcida</p>\n                        <p><strong>Dimensões:</strong> 3,89m x 2,80m</p>\n                    </div>\n                    \n                    <h3 style=\"color: #667eea;\">???? Próximos Passos</h3>\n                    <ol style=\"line-height: 2;\">\n                        <li>Nossa equipe está analisando seu pedido com todo carinho</li>\n                        <li>Entraremos em contato via WhatsApp em breve</li>\n                        <li>Enviaremos o valor do frete e prazo de entrega</li>\n                        <li>Após sua confirmação, iniciaremos a produção</li>\n                    </ol>\n                    \n                    <div class=\"contatos\">\n                        <h3 style=\"margin-top: 0; color: #856404;\">???? Ficou com alguma dúvida?</h3>\n                        <p style=\"margin-bottom: 15px;\">\n                            Não se preocupe! Nossa equipe está pronta para te atender e esclarecer qualquer questão sobre seu orçamento.\n                        </p>\n                        \n                        <div style=\"text-align: center; margin: 20px 0;\">\n                            <a href=\"https://api.whatsapp.com/send?phone=5575988890006&text=Olá! Fiz um orçamento (#18) e gostaria de tirar algumas dúvidas.\" \n                               class=\"btn-whatsapp\">\n                                ???? Falar no WhatsApp\n                            </a>\n                        </div>\n                        \n                        <p style=\"text-align: center; margin-top: 20px; color: #666;\">\n                            <strong>Outros canais de atendimento:</strong><br>\n                            ???? Telefone: (75) 98889-0006<br>\n                            ???? E-mail: contato@lecortine.com.br<br>\n                            ???? Horário: Segunda a Sexta, 8h às 18h\n                        </p>\n                    </div>\n                    \n                    <p style=\"margin-top: 30px; text-align: center; color: #666; font-size: 14px;\">\n                        <em>Agradecemos pela sua preferência e confiança!<br>\n                        Estamos ansiosos para tornar seu projeto realidade.</em>\n                    </p>\n                    \n                    <p style=\"text-align: center; margin-top: 30px; color: #999; font-size: 12px;\">\n                        Le Cortine - Transformando ambientes com elegância e qualidade\n                    </p>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 23:50:18'),
(9, 18, 'status_em_analise', 'rafaeldiaswebdev@gmail.com', 'Atualização do Orçamento #ORC-202511-0018 - Le Cortine', 'sucesso', 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; backgrou', '\n        <!DOCTYPE html>\n        <html>\n        <head>\n            <meta charset=\"utf-8\">\n            <style>\n                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\n                .container { max-width: 600px; margin: 0 auto; padding: 20px; }\n                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }\n                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }\n                .status-badge { display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; border-radius: 5px; font-weight: bold; margin: 20px 0; }\n                .info-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #667eea; }\n                .footer { text-align: center; margin-top: 30px; color: #6c757d; font-size: 12px; }\n                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }\n            </style>\n        </head>\n        <body>\n            <div class=\"container\">\n                <div class=\"header\">\n                    <h1>Le Cortine</h1>\n                    <p>Atualização do seu Orçamento</p>\n                </div>\n                <div class=\"content\">\n                    <h2>Orçamento em Análise</h2>\n                    \n                    <div class=\"status-badge\">\n                        Status: Em analise\n                    </div>\n                    \n                    <p>Olá, <strong>Rafael de Andrade Dias</strong>!</p>\n                    \n                    <p>Seu orçamento está sendo analisado por nossa equipe. Aguarde nosso retorno!</p>\n                    \n                    <div class=\"info-box\">\n                        <strong>Detalhes do Orçamento:</strong><br>\n                        <strong>Número:</strong> #ORC-202511-0018<br>\n                        <strong>Data:</strong> 15/11/2025 23:50<br>\n                        <strong>Valor:</strong> R$ 2.341,78\n                    </div>\n                    \n                    <p>Se tiver alguma dúvida, entre em contato conosco:</p>\n                    <p>\n                        ???? E-mail: contato@lecortine.com.br<br>\n                        ???? WhatsApp: (11) 99999-9999\n                    </p>\n                    \n                    <div class=\"footer\">\n                        <p>Este é um e-mail automático, por favor não responda.</p>\n                        <p>&copy; 2025 Le Cortine - Todos os direitos reservados</p>\n                    </div>\n                </div>\n            </div>\n        </body>\n        </html>', NULL, '2025-11-15 23:51:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_extras`
--

CREATE TABLE `orcamento_extras` (
  `id` int(11) UNSIGNED NOT NULL,
  `orcamento_item_id` int(11) UNSIGNED NOT NULL,
  `extra_id` int(11) UNSIGNED NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamento_extras`
--

INSERT INTO `orcamento_extras` (`id`, `orcamento_item_id`, `extra_id`, `valor`, `criado_em`) VALUES
(1, 9, 4, 395.00, '2025-11-15 20:16:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_itens`
--

CREATE TABLE `orcamento_itens` (
  `id` int(11) UNSIGNED NOT NULL,
  `orcamento_id` int(11) UNSIGNED NOT NULL,
  `produto_id` int(11) UNSIGNED NOT NULL,
  `tecido_id` int(11) UNSIGNED DEFAULT NULL,
  `cor_id` int(11) UNSIGNED DEFAULT NULL,
  `largura` decimal(10,2) NOT NULL COMMENT 'Largura em metros',
  `altura` decimal(10,2) NOT NULL COMMENT 'Altura em metros',
  `area_m2` decimal(10,2) NOT NULL COMMENT 'Área em metros quadrados',
  `quantidade` int(11) DEFAULT 1,
  `valor_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `preco_unitario` decimal(10,2) NOT NULL,
  `preco_total` decimal(10,2) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamento_itens`
--

INSERT INTO `orcamento_itens` (`id`, `orcamento_id`, `produto_id`, `tecido_id`, `cor_id`, `largura`, `altura`, `area_m2`, `quantidade`, `valor_unitario`, `preco_unitario`, `preco_total`, `observacoes`, `criado_em`) VALUES
(2, 10, 1, 2, 7, 2.89, 2.80, 0.00, 1, 692.00, 0.00, 692.00, NULL, '2025-11-15 16:51:40'),
(3, 11, 1, 2, 7, 3.00, 2.80, 0.00, 1, 585.00, 0.00, 585.00, NULL, '2025-11-15 17:11:31'),
(4, 12, 1, 2, 7, 3.00, 2.80, 0.00, 1, 585.00, 0.00, 585.00, NULL, '2025-11-15 17:33:42'),
(5, 13, 1, 2, 7, 3.00, 2.80, 0.00, 1, 585.00, 0.00, 585.00, NULL, '2025-11-15 17:38:59'),
(6, 14, 1, 2, 7, 3.00, 2.80, 0.00, 1, 885.00, 0.00, 885.00, NULL, '2025-11-15 17:41:11'),
(7, 15, 1, 2, 9, 2.50, 2.80, 0.00, 1, 692.00, 0.00, 692.00, NULL, '2025-11-15 19:44:43'),
(8, 16, 1, 1, 3, 3.89, 2.80, 0.00, 1, 885.00, 0.00, 885.00, NULL, '2025-11-15 20:10:37'),
(9, 17, 1, 2, 9, 4.89, 2.80, 0.00, 1, 1587.00, 0.00, 1587.00, NULL, '2025-11-15 20:16:41'),
(10, 18, 2, 3, 16, 3.89, 2.80, 0.00, 1, 2341.78, 0.00, 2341.78, NULL, '2025-11-15 23:50:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `precos`
--

CREATE TABLE `precos` (
  `id` int(11) UNSIGNED NOT NULL,
  `produto_id` int(11) UNSIGNED NOT NULL,
  `largura_min` decimal(10,2) NOT NULL COMMENT 'Largura mínima em metros',
  `largura_max` decimal(10,2) NOT NULL COMMENT 'Largura máxima em metros',
  `altura_min` decimal(10,2) NOT NULL COMMENT 'Altura mínima em metros',
  `altura_max` decimal(10,2) NOT NULL COMMENT 'Altura máxima em metros',
  `preco_m2` decimal(10,2) NOT NULL COMMENT 'Preço por metro quadrado',
  `preco_ml` decimal(10,2) DEFAULT NULL COMMENT 'Preço por metro linear',
  `preco_fixo` decimal(10,2) DEFAULT NULL COMMENT 'Preço fixo (unidade)',
  `observacoes` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `precos`
--

INSERT INTO `precos` (`id`, `produto_id`, `largura_min`, `largura_max`, `altura_min`, `altura_max`, `preco_m2`, `preco_ml`, `preco_fixo`, `observacoes`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 0.00, 2.00, 0.00, 2.80, 0.00, NULL, 442.00, NULL, '2025-11-13 21:58:49', NULL),
(2, 1, 2.01, 3.00, 0.00, 2.80, 0.00, NULL, 585.00, NULL, '2025-11-13 21:58:49', NULL),
(3, 1, 3.01, 4.00, 0.00, 2.80, 0.00, NULL, 824.00, NULL, '2025-11-13 21:58:49', NULL),
(4, 1, 4.01, 5.00, 0.00, 2.80, 0.00, NULL, 1192.00, NULL, '2025-11-13 21:58:49', NULL),
(5, 2, 0.00, 10.00, 0.00, 10.00, 215.00, NULL, NULL, NULL, '2025-11-13 21:58:49', NULL),
(6, 3, 0.00, 10.00, 0.00, 10.00, 299.00, NULL, NULL, NULL, '2025-11-13 21:58:49', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) UNSIGNED NOT NULL,
  `categoria_id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `descricao_curta` varchar(255) DEFAULT NULL,
  `imagem_principal` varchar(255) DEFAULT NULL,
  `caracteristicas` text DEFAULT NULL,
  `tipo_calculo` enum('metro_quadrado','metro_linear','unidade') DEFAULT 'metro_quadrado',
  `preco_base` decimal(10,2) DEFAULT 0.00,
  `ordem` int(11) DEFAULT 0,
  `destaque` tinyint(1) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `categoria_id`, `nome`, `slug`, `descricao`, `descricao_curta`, `imagem_principal`, `caracteristicas`, `tipo_calculo`, `preco_base`, `ordem`, `destaque`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 'Cortina em Tecido', 'cortina-tecido', 'Cortina confeccionada com prega Victoria, ideal para ambientes elegantes. Disponível em Linho Rústico e Linen Light.', 'Cortina Prega Victoria em tecido nobre', NULL, NULL, 'unidade', 442.00, 1, 1, 'ativo', '2025-11-13 21:58:49', NULL),
(2, 1, 'Cortina Rolô', 'cortina-rolo', 'Cortina rolô com acionamento manual ou motorizado. Disponível em Translúcida, Blackout e Tela Solar.', 'Cortina Rolô prática e moderna', NULL, NULL, 'metro_quadrado', 215.00, 2, 1, 'ativo', '2025-11-13 21:58:49', NULL),
(3, 1, 'Cortina Duplex VIP', 'cortina-duplex-vip', 'Sistema exclusivo com duas cortinas rolô em um único suporte. Máxima versatilidade e elegância.', 'Sistema duplo de cortinas rolô', NULL, NULL, 'metro_quadrado', 299.00, 3, 1, 'ativo', '2025-11-13 21:58:49', NULL),
(4, 2, 'Toldos', 'toldos', 'Toldos personalizados para área externa. Solicite consultoria personalizada.', 'Toldos sob medida', NULL, NULL, 'unidade', 0.00, 4, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(5, 3, 'Cortinas Motorizadas', 'cortinas-motorizadas', 'Sistema completo de automação para cortinas. Solicite consultoria personalizada.', 'Automação de cortinas', NULL, NULL, 'unidade', 0.00, 5, 0, 'ativo', '2025-11-13 21:58:49', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_imagens`
--

CREATE TABLE `produto_imagens` (
  `id` int(11) UNSIGNED NOT NULL,
  `produto_id` int(11) UNSIGNED NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tecidos`
--

CREATE TABLE `tecidos` (
  `id` int(11) UNSIGNED NOT NULL,
  `colecao_id` int(11) UNSIGNED DEFAULT NULL,
  `produto_id` int(11) UNSIGNED DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `tipo` enum('blackout','translucido','transparente','linho','voil','outro') DEFAULT 'outro',
  `composicao` varchar(255) DEFAULT NULL,
  `largura_padrao` decimal(10,2) DEFAULT NULL COMMENT 'Largura em metros',
  `preco_adicional` decimal(10,2) DEFAULT 0.00,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tecidos`
--

INSERT INTO `tecidos` (`id`, `colecao_id`, `produto_id`, `nome`, `codigo`, `descricao`, `imagem`, `tipo`, `composicao`, `largura_padrao`, `preco_adicional`, `ordem`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 1, 'Linho Rústico', 'LR-001', NULL, NULL, 'linho', '100% Linho Natural', 2.80, 0.00, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(2, 1, 1, 'Linen Light', 'LL-001', NULL, NULL, 'linho', '70% Linho, 30% Poliéster', 2.80, 0.00, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(3, 2, 2, 'Rolô Translúcida', 'RT-001', NULL, NULL, 'translucido', '100% Poliéster', 2.80, 0.00, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(4, 3, 2, 'Rolô Blackout', 'RB-001', NULL, NULL, 'blackout', '100% Poliéster Blackout', 2.80, 0.00, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(5, 4, 2, 'Rolô Tela Solar 5%', 'RTS-001', NULL, NULL, 'translucido', 'Tela Solar com proteção UV', 2.80, 0.00, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(6, 5, 3, 'Duplex Translúcida', 'DT-001', NULL, NULL, 'translucido', '100% Poliéster', 2.80, 0.00, 0, 'ativo', '2025-11-13 21:58:49', NULL),
(7, 6, NULL, 'Doisr Sistemas', '', '', NULL, 'outro', '', NULL, 0.00, 0, 'ativo', '2025-11-16 00:11:03', NULL);

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
  `nivel` enum('admin','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `ultimo_acesso` datetime DEFAULT NULL,
  `token_recuperacao` varchar(100) DEFAULT NULL,
  `token_expiracao` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `avatar`, `nivel`, `status`, `ultimo_acesso`, `token_recuperacao`, `token_expiracao`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@lecortine.com.br', '$2y$10$JTNUdyydaB7uARn1WIjjKOtMq27s49sTys2lrq2s3sI.do6S7KLM6', '(11) 99999-9999', NULL, 'admin', 'ativo', '2025-11-15 16:01:52', NULL, NULL, '2025-11-13 21:58:37', '2025-11-15 16:10:38'),
(2, 'Administrador', 'rafaeldiastecinfo@gmail.com', '$2y$10$U64Cx5OEmm8NomJMEKcXJuNU3S5WHNJqRFnqBR2QX/LADiXyZWgFi', NULL, NULL, 'admin', 'ativo', '2025-11-15 20:14:36', NULL, NULL, '2025-11-15 12:03:30', '2025-11-15 16:14:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_permissoes`
--

CREATE TABLE `usuario_permissoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `modulo` varchar(50) NOT NULL COMMENT 'Nome do módulo (produtos, clientes, orcamentos, etc)',
  `permissoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Permissões: {"visualizar":true,"criar":false,"editar":false,"excluir":false}' CHECK (json_valid(`permissoes`)),
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_whatsapp` (`whatsapp`),
  ADD KEY `idx_criado_em` (`criado_em`);

--
-- Índices de tabela `colecoes`
--
ALTER TABLE `colecoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`),
  ADD KEY `idx_grupo` (`grupo`);

--
-- Índices de tabela `cores`
--
ALTER TABLE `cores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tecido` (`tecido_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices de tabela `extras`
--
ALTER TABLE `extras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ordem` (`ordem`);

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
-- Índices de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_criado_em` (`criado_em`),
  ADD KEY `idx_tipo_atendimento` (`tipo_atendimento`),
  ADD KEY `idx_orcamentos_cliente_data` (`cliente_id`,`criado_em`);

--
-- Índices de tabela `orcamento_email_logs`
--
ALTER TABLE `orcamento_email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orcamento` (`orcamento_id`);

--
-- Índices de tabela `orcamento_extras`
--
ALTER TABLE `orcamento_extras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orcamento_item` (`orcamento_item_id`),
  ADD KEY `idx_extra` (`extra_id`);

--
-- Índices de tabela `orcamento_itens`
--
ALTER TABLE `orcamento_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orcamento` (`orcamento_id`),
  ADD KEY `idx_produto` (`produto_id`),
  ADD KEY `idx_tecido` (`tecido_id`),
  ADD KEY `idx_cor` (`cor_id`);

--
-- Índices de tabela `precos`
--
ALTER TABLE `precos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_produto` (`produto_id`),
  ADD KEY `idx_dimensoes` (`largura_min`,`largura_max`,`altura_min`,`altura_max`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ordem` (`ordem`),
  ADD KEY `idx_destaque` (`destaque`),
  ADD KEY `idx_produtos_categoria_status` (`categoria_id`,`status`);

--
-- Índices de tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_produto` (`produto_id`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices de tabela `tecidos`
--
ALTER TABLE `tecidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_colecao` (`colecao_id`),
  ADD KEY `idx_produto` (`produto_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_ordem` (`ordem`),
  ADD KEY `idx_tecidos_colecao_status` (`colecao_id`,`status`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_nivel` (`nivel`);

--
-- Índices de tabela `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_modulo` (`usuario_id`,`modulo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `colecoes`
--
ALTER TABLE `colecoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de tabela `cores`
--
ALTER TABLE `cores`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `extras`
--
ALTER TABLE `extras`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `orcamento_email_logs`
--
ALTER TABLE `orcamento_email_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `orcamento_extras`
--
ALTER TABLE `orcamento_extras`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `orcamento_itens`
--
ALTER TABLE `orcamento_itens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `precos`
--
ALTER TABLE `precos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tecidos`
--
ALTER TABLE `tecidos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cores`
--
ALTER TABLE `cores`
  ADD CONSTRAINT `fk_cores_tecido` FOREIGN KEY (`tecido_id`) REFERENCES `tecidos` (`id`) ON DELETE CASCADE;

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
-- Restrições para tabelas `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD CONSTRAINT `fk_orcamentos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamento_email_logs`
--
ALTER TABLE `orcamento_email_logs`
  ADD CONSTRAINT `fk_email_logs_orcamento` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamento_extras`
--
ALTER TABLE `orcamento_extras`
  ADD CONSTRAINT `fk_orcamento_extras_extra` FOREIGN KEY (`extra_id`) REFERENCES `extras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orcamento_extras_item` FOREIGN KEY (`orcamento_item_id`) REFERENCES `orcamento_itens` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamento_itens`
--
ALTER TABLE `orcamento_itens`
  ADD CONSTRAINT `fk_orcamento_itens_cor` FOREIGN KEY (`cor_id`) REFERENCES `cores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orcamento_itens_orcamento` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orcamento_itens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orcamento_itens_tecido` FOREIGN KEY (`tecido_id`) REFERENCES `tecidos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `precos`
--
ALTER TABLE `precos`
  ADD CONSTRAINT `fk_precos_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD CONSTRAINT `fk_produto_imagens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tecidos`
--
ALTER TABLE `tecidos`
  ADD CONSTRAINT `fk_tecidos_colecao` FOREIGN KEY (`colecao_id`) REFERENCES `colecoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tecidos_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  ADD CONSTRAINT `fk_permissoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
