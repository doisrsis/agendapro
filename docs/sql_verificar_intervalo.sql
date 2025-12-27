-- Verificar estrutura atual da tabela
DESCRIBE estabelecimentos;

-- OU

SHOW COLUMNS FROM estabelecimentos LIKE '%intervalo%';

-- Se as colunas NÃO existirem, execute:
-- (Caso já existam, pule para a próxima etapa)

-- Adicionar usar_intervalo_fixo (se não existir)
ALTER TABLE estabelecimentos
ADD COLUMN usar_intervalo_fixo TINYINT(1) DEFAULT 1
COMMENT 'Se 1, usa intervalo fixo. Se 0, usa intervalo dinâmico baseado na duração do serviço';

-- Adicionar intervalo_agendamento (se não existir)
ALTER TABLE estabelecimentos
ADD COLUMN intervalo_agendamento INT DEFAULT 30
COMMENT 'Intervalo em minutos quando usar_intervalo_fixo = 1 (5, 10, 15, 30)';
