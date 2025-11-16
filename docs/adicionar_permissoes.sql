-- ============================================================================
-- Sistema de Permissões por Módulo
-- Autor: Rafael Dias - doisr.com.br
-- Data: 16/11/2024
-- ============================================================================

-- 1. Criar tabela de permissões
CREATE TABLE IF NOT EXISTS `usuario_permissoes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `modulo` varchar(50) NOT NULL COMMENT 'Nome do módulo (usuarios, configuracoes, logs, etc)',
  `pode_visualizar` tinyint(1) DEFAULT 0,
  `pode_criar` tinyint(1) DEFAULT 0,
  `pode_editar` tinyint(1) DEFAULT 0,
  `pode_excluir` tinyint(1) DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_modulo` (`usuario_id`, `modulo`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_modulo` (`modulo`),
  CONSTRAINT `fk_permissoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Alterar enum de nível para usar 'usuario' ao invés de 'atendente'
ALTER TABLE `usuarios`
MODIFY COLUMN `nivel` enum('admin','usuario') DEFAULT 'usuario'
COMMENT 'Nível de acesso do usuário';

-- 3. Atualizar usuários existentes que possam ter 'atendente'
UPDATE `usuarios` SET `nivel` = 'usuario' WHERE `nivel` NOT IN ('admin', 'usuario');

-- Verificar estrutura
DESCRIBE usuario_permissoes;
DESCRIBE usuarios;
