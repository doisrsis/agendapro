-- =====================================================
-- SQL: Adicionar status 'em_atendimento' na tabela agendamentos
-- Autor: Rafael Dias - doisr.com.br
-- Data: 28/12/2024
-- =====================================================

-- Alterar ENUM do campo status para incluir 'em_atendimento'
ALTER TABLE `agendamentos`
MODIFY COLUMN `status` ENUM('pendente', 'confirmado', 'em_atendimento', 'cancelado', 'reagendado', 'finalizado')
NOT NULL DEFAULT 'pendente';

-- Adicionar campos para registrar hora real de início e fim do atendimento (opcional)
ALTER TABLE `agendamentos`
ADD COLUMN IF NOT EXISTS `hora_inicio_real` TIME NULL AFTER `hora_fim`,
ADD COLUMN IF NOT EXISTS `hora_fim_real` TIME NULL AFTER `hora_inicio_real`;
