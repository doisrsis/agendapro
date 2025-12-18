-- Tabela de Pagamentos para Mercado Pago
-- Execute cada comando SEPARADAMENTE no phpMyAdmin

-- 1. Criar tabela
CREATE TABLE IF NOT EXISTS `pagamentos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `assinatura_id` int(11) UNSIGNED DEFAULT NULL,
  `plano_id` int(11) UNSIGNED NOT NULL,
  `mercadopago_id` varchar(100) DEFAULT NULL COMMENT 'ID do pagamento no Mercado Pago',
  `tipo` enum('pix','cartao','boleto') NOT NULL DEFAULT 'pix',
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled','refunded','in_process') DEFAULT 'pending',
  `status_detail` varchar(100) DEFAULT NULL COMMENT 'Detalhes do status do MP',
  `qr_code` text COMMENT 'Código PIX para copiar',
  `qr_code_base64` text COMMENT 'QR Code em base64 para exibir',
  `payment_data` text COMMENT 'JSON com dados completos do MP',
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mercadopago_id` (`mercadopago_id`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_assinatura` (`assinatura_id`),
  KEY `idx_status` (`status`),
  KEY `idx_criado` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Adicionar índice composto (OPCIONAL - execute SOMENTE se a tabela foi criada com sucesso)
-- CREATE INDEX idx_pagamentos_lookup ON pagamentos(estabelecimento_id, status, criado_em);

-- NOTA: As Foreign Keys foram removidas para evitar problemas de dependência
-- Se quiser adicionar depois, execute:
-- ALTER TABLE pagamentos ADD CONSTRAINT fk_pagamentos_estabelecimento FOREIGN KEY (estabelecimento_id) REFERENCES estabelecimentos(id) ON DELETE CASCADE;
-- ALTER TABLE pagamentos ADD CONSTRAINT fk_pagamentos_assinatura FOREIGN KEY (assinatura_id) REFERENCES assinaturas(id) ON DELETE SET NULL;
-- ALTER TABLE pagamentos ADD CONSTRAINT fk_pagamentos_plano FOREIGN KEY (plano_id) REFERENCES planos(id) ON DELETE RESTRICT;
