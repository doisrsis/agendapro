-- ============================================================================
-- Adicionar campos do Mercado Pago na tabela planos
-- ============================================================================

ALTER TABLE `planos`
ADD COLUMN `mercadopago_plan_id` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `mercadopago_preapproval_plan_id` VARCHAR(100) DEFAULT NULL AFTER `mercadopago_plan_id`,
ADD INDEX `idx_mp_plan` (`mercadopago_plan_id`);

-- ============================================================================
-- FIM
-- ============================================================================
