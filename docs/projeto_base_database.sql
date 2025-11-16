-- ============================================================================
-- PROJETO BASE - DASHBOARD ADMINISTRATIVO
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 16/11/2024 19:21
-- Descrição: Banco de dados base para projetos com dashboard administrativo
--
-- Funcionalidades incluídas:
-- - Sistema de autenticação de usuários
-- - Recuperação de senha por e-mail
-- - Notificações do sistema
-- - Configurações SMTP dinâmicas
-- - Sistema de logs de ações
-- - Gerenciamento de usuários
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- TABELA: usuarios
-- Descrição: Armazena os usuários do sistema com autenticação
-- ============================================================================

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_nivel` (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário administrador padrão
-- Email: admin@sistema.com.br
-- Senha: admin123
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `avatar`, `nivel`, `status`, `ultimo_acesso`, `token_recuperacao`, `token_expiracao`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@sistema.com.br', '$2y$10$JTNUdyydaB7uARn1WIjjKOtMq27s49sTys2lrq2s3sI.do6S7KLM6', NULL, NULL, 'admin', 'ativo', NULL, NULL, NULL, NOW(), NULL);

-- ============================================================================
-- TABELA: notificacoes
-- Descrição: Sistema de notificações para usuários
-- ============================================================================

CREATE TABLE `notificacoes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'NULL = notificação para todos',
  `tipo` enum('info','sucesso','aviso','erro') DEFAULT 'info',
  `titulo` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `link` varchar(255) DEFAULT NULL COMMENT 'Link para ação relacionada',
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_lida` (`lida`),
  KEY `idx_criado_em` (`criado_em`),
  CONSTRAINT `fk_notificacoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABELA: configuracoes
-- Descrição: Configurações dinâmicas do sistema (SMTP, geral, etc)
-- ============================================================================

CREATE TABLE `configuracoes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json','arquivo') DEFAULT 'texto',
  `grupo` varchar(50) DEFAULT 'geral' COMMENT 'Agrupa configurações (geral, smtp, notificacoes)',
  `descricao` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações padrão do sistema
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`, `grupo`, `descricao`) VALUES
-- Configurações Gerais
('sistema_nome', 'Dashboard Administrativo', 'texto', 'geral', 'Nome do sistema'),
('sistema_email', 'contato@sistema.com.br', 'texto', 'geral', 'E-mail principal do sistema'),
('sistema_telefone', '', 'texto', 'geral', 'Telefone de contato'),
('sistema_endereco', '', 'texto', 'geral', 'Endereço completo'),
('sistema_logo', '', 'arquivo', 'geral', 'Logo do sistema'),
('sistema_favicon', '', 'arquivo', 'geral', 'Favicon do sistema'),

-- Configurações SMTP
('smtp_ativo', '0', 'booleano', 'smtp', 'Ativar envio de e-mails via SMTP'),
('smtp_host', '', 'texto', 'smtp', 'Servidor SMTP (ex: smtp.gmail.com)'),
('smtp_porta', '587', 'numero', 'smtp', 'Porta SMTP (587 para TLS, 465 para SSL)'),
('smtp_usuario', '', 'texto', 'smtp', 'Usuário SMTP (e-mail)'),
('smtp_senha', '', 'texto', 'smtp', 'Senha SMTP'),
('smtp_seguranca', 'tls', 'texto', 'smtp', 'Tipo de segurança (tls ou ssl)'),
('smtp_remetente_email', '', 'texto', 'smtp', 'E-mail do remetente'),
('smtp_remetente_nome', '', 'texto', 'smtp', 'Nome do remetente'),

-- Configurações de Notificações
('notif_email_ativo', '1', 'booleano', 'notificacoes', 'Enviar notificações por e-mail'),
('notif_email_destinatario', '', 'texto', 'notificacoes', 'E-mail para receber notificações do sistema'),
('notif_sistema_som', '1', 'booleano', 'notificacoes', 'Ativar som nas notificações do sistema');

-- ============================================================================
-- TABELA: logs
-- Descrição: Registra todas as ações importantes do sistema
-- ============================================================================

CREATE TABLE `logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `acao` varchar(100) NOT NULL COMMENT 'Tipo de ação (login, logout, criar, editar, excluir)',
  `tabela` varchar(50) DEFAULT NULL COMMENT 'Tabela afetada pela ação',
  `registro_id` int(11) DEFAULT NULL COMMENT 'ID do registro afetado',
  `dados_antigos` text DEFAULT NULL COMMENT 'JSON com dados antes da alteração',
  `dados_novos` text DEFAULT NULL COMMENT 'JSON com dados após a alteração',
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_acao` (`acao`),
  KEY `idx_tabela` (`tabela`),
  KEY `idx_criado_em` (`criado_em`),
  CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FINALIZANDO
-- ============================================================================

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
-- 3. Configure o arquivo application/config/database.php com suas credenciais
-- 4. Acesse o sistema com:
--    Email: admin@sistema.com.br
--    Senha: admin123
-- 5. Altere a senha padrão após o primeiro acesso
--
-- ============================================================================
