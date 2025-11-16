-- Registro de histórico de e-mails vinculados ao orçamento
-- Autor: Rafael Dias - doisr.com.br (15/11/2025 20:25)

CREATE TABLE `orcamento_email_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `orcamento_id` INT UNSIGNED NOT NULL,
    `tipo` VARCHAR(40) NOT NULL,
    `destinatario` VARCHAR(255) NOT NULL,
    `assunto` VARCHAR(255) NOT NULL,
    `status` ENUM('sucesso', 'erro') NOT NULL DEFAULT 'sucesso',
    `preview` VARCHAR(500) DEFAULT NULL,
    `corpo` MEDIUMTEXT,
    `erro` TEXT DEFAULT NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_orcamento` (`orcamento_id`),
    CONSTRAINT `fk_email_logs_orcamento` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
