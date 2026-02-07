ALTER TABLE `bloqueios`
ADD COLUMN `recorrencia` ENUM('nao', 'diario', 'semanal') DEFAULT 'nao' AFTER `motivo`,
ADD COLUMN `dia_semana` INT DEFAULT NULL AFTER `recorrencia`,
ADD COLUMN `data_limite` DATE DEFAULT NULL AFTER `dia_semana`;
