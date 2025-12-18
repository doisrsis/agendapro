-- Verificar estrutura atual da tabela
DESCRIBE pagamentos;

-- Se a tabela tiver colunas diferentes, execute este comando para recriar:
DROP TABLE IF EXISTS `pagamentos`;

CREATE TABLE `pagamentos` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) UNSIGNED NOT NULL,
  `assinatura_id` int(11) UNSIGNED DEFAULT NULL,
  `plano_id` int(11) UNSIGNED NOT NULL,
  `mercadopago_id` varchar(100) DEFAULT NULL,
  `tipo` enum('pix','cartao','boleto') NOT NULL DEFAULT 'pix',
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled','refunded','in_process') DEFAULT 'pending',
  `status_detail` varchar(100) DEFAULT NULL,
  `qr_code` text,
  `qr_code_base64` text,
  `payment_data` text,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mercadopago_id` (`mercadopago_id`),
  KEY `idx_estabelecimento` (`estabelecimento_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
