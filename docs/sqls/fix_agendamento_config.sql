ALTER TABLE estabelecimentos ADD COLUMN usar_intervalo_fixo TINYINT(1) DEFAULT 1;
ALTER TABLE estabelecimentos ADD COLUMN confirmacao_automatica TINYINT(1) DEFAULT 0;
