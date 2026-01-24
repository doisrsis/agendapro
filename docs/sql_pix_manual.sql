-- ============================================
-- Adicionar campos para PIX Manual
-- Permite estabelecimentos usarem PIX sem integração Mercado Pago
-- Autor: Rafael Dias - doisr.com.br
-- Data: 23/01/2026
-- ============================================

-- Adicionar campos na tabela estabelecimentos
ALTER TABLE estabelecimentos
ADD COLUMN pagamento_tipo ENUM('mercadopago', 'pix_manual') DEFAULT 'mercadopago'
    COMMENT 'Tipo de pagamento: mercadopago (integração) ou pix_manual (confirmação manual)'
    AFTER agendamento_requer_pagamento,

ADD COLUMN pix_chave VARCHAR(255) NULL
    COMMENT 'Chave PIX do estabelecimento (CPF, CNPJ, email, telefone ou aleatória)'
    AFTER pagamento_tipo,

ADD COLUMN pix_tipo_chave ENUM('cpf', 'cnpj', 'email', 'telefone', 'aleatoria') NULL
    COMMENT 'Tipo da chave PIX cadastrada'
    AFTER pix_chave,

ADD COLUMN pix_nome_recebedor VARCHAR(255) NULL
    COMMENT 'Nome do recebedor que aparecerá no PIX'
    AFTER pix_tipo_chave,

ADD COLUMN pix_cidade VARCHAR(100) NULL
    COMMENT 'Cidade do recebedor (obrigatório no padrão PIX)'
    AFTER pix_nome_recebedor;

-- Verificar estrutura atualizada
DESCRIBE estabelecimentos;

-- ============================================
-- NOTAS DE IMPLEMENTAÇÃO:
-- ============================================
-- 1. pagamento_tipo: Define se usa Mercado Pago ou PIX Manual
--    - 'mercadopago': Integração automática com webhook (padrão atual)
--    - 'pix_manual': Gera QR Code PIX mas confirmação é manual
--
-- 2. Campos PIX são opcionais (NULL) pois só são necessários se pagamento_tipo = 'pix_manual'
--
-- 3. Compatibilidade: Estabelecimentos existentes ficam com 'mercadopago' (default)
--
-- 4. Fluxo PIX Manual:
--    - Cliente agenda e escolhe "Pagar via PIX"
--    - Sistema gera BR Code (copia e cola) com valor fixo
--    - Cliente paga e envia comprovante
--    - Estabelecimento confirma manualmente no painel
-- ============================================
