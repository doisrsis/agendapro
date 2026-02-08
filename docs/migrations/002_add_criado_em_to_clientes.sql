ALTER TABLE `clientes` ADD COLUMN `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `tipo`;
UPDATE `clientes` SET `criado_em` = NOW() WHERE `criado_em` IS NULL;
