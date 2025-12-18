-- Correção: Permitir profissional_id NULL para bloqueios de serviço
-- Data: 13/12/2024
-- Autor: Rafael Dias - doisr.com.br
-- IMPORTANTE: Execute UM comando por vez no phpMyAdmin

-- ========================================
-- COMANDO 1: Remover a foreign key
-- ========================================
ALTER TABLE `bloqueios` DROP FOREIGN KEY `fk_bloqueios_profissional`;

-- ========================================
-- COMANDO 2: Alterar coluna para aceitar NULL
-- ========================================
ALTER TABLE `bloqueios` MODIFY COLUMN `profissional_id` INT(11) NULL;

-- ========================================
-- COMANDO 3: Recriar a foreign key
-- ========================================
ALTER TABLE `bloqueios` ADD CONSTRAINT `fk_bloqueios_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE;
