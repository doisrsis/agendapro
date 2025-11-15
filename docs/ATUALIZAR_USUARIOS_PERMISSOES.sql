-- ============================================================================
-- ATUALIZAR SISTEMA DE USUÁRIOS E PERMISSÕES
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 15/11/2024
-- Descrição: Adiciona sistema de permissões por módulo para usuários
-- ============================================================================

-- 1. Atualizar tabela de usuários (mudar níveis)
ALTER TABLE `usuarios` 
MODIFY COLUMN `nivel` ENUM('admin', 'atendente') DEFAULT 'atendente';

-- 2. Criar tabela de permissões
CREATE TABLE IF NOT EXISTS `usuario_permissoes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) UNSIGNED NOT NULL,
  `modulo` VARCHAR(50) NOT NULL COMMENT 'Nome do módulo (produtos, clientes, orcamentos, etc)',
  `permissoes` JSON NOT NULL COMMENT 'Permissões: {"visualizar":true,"criar":false,"editar":false,"excluir":false}',
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_modulo` (`usuario_id`, `modulo`),
  CONSTRAINT `fk_permissoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Atualizar usuário admin existente para novo nível
UPDATE `usuarios` SET `nivel` = 'admin' WHERE `email` = 'admin@lecortine.com.br';

-- 4. Inserir permissões padrão para Admin (acesso total a tudo)
-- Admin não precisa de registro na tabela de permissões, tem acesso total por padrão

-- ============================================================================
-- MÓDULOS DISPONÍVEIS NO SISTEMA
-- ============================================================================
-- 
-- dashboard       - Dashboard e estatísticas
-- produtos        - Gerenciar produtos
-- tecidos         - Gerenciar tecidos e cores
-- colecoes        - Gerenciar coleções
-- categorias      - Gerenciar categorias
-- extras          - Gerenciar extras (blackout, motorização)
-- precos          - Gerenciar tabela de preços
-- orcamentos      - Visualizar e gerenciar orçamentos
-- clientes        - Gerenciar clientes
-- usuarios        - Gerenciar usuários (apenas admin)
-- configuracoes   - Configurações do sistema (apenas admin)
-- relatorios      - Relatórios e exportações
-- 
-- ============================================================================

-- Exemplo de permissões para um atendente (descomente para testar):
-- 
-- INSERT INTO `usuario_permissoes` (`usuario_id`, `modulo`, `permissoes`) VALUES
-- (2, 'dashboard', '{"visualizar":true}'),
-- (2, 'orcamentos', '{"visualizar":true,"criar":true,"editar":true,"excluir":false}'),
-- (2, 'clientes', '{"visualizar":true,"criar":true,"editar":true,"excluir":false}'),
-- (2, 'produtos', '{"visualizar":true,"criar":false,"editar":false,"excluir":false}');

-- ============================================================================
-- VERIFICAR ESTRUTURA
-- ============================================================================
SHOW COLUMNS FROM `usuarios`;
SHOW COLUMNS FROM `usuario_permissoes`;

SELECT 'Sistema de permissões criado com sucesso!' AS Mensagem;
