-- Migration: Adicionar campos de confirmação e lembretes na tabela agendamentos
-- Autor: Rafael Dias - doisr.com.br
-- Data: 03/01/2026

-- Adicionar campos de confirmação
ALTER TABLE agendamentos
ADD COLUMN confirmacao_enviada TINYINT(1) DEFAULT 0 COMMENT 'Flag se pedido de confirmação foi enviado';

ALTER TABLE agendamentos
ADD COLUMN confirmacao_enviada_em DATETIME NULL COMMENT 'Quando o pedido foi enviado';

ALTER TABLE agendamentos
ADD COLUMN confirmado_em DATETIME NULL COMMENT 'Quando o cliente confirmou presença';

-- Adicionar campos de lembrete
ALTER TABLE agendamentos
ADD COLUMN lembrete_enviado TINYINT(1) DEFAULT 0 COMMENT 'Flag se lembrete pré-atendimento foi enviado';

ALTER TABLE agendamentos
ADD COLUMN lembrete_enviado_em DATETIME NULL COMMENT 'Quando o lembrete foi enviado';
