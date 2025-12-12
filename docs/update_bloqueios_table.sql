-- Migração da tabela bloqueios (estrutura antiga → nova)
-- Autor: Rafael Dias - doisr.com.br
-- Data: 11/12/2024

-- Estrutura ANTIGA: id, profissional_id, data, hora_inicio, hora_fim, dia_todo, motivo
-- Estrutura NOVA: id, profissional_id, tipo, data_inicio, data_fim, hora_inicio, hora_fim, motivo, criado_por

-- 1. Adicionar novas colunas
ALTER TABLE bloqueios
ADD COLUMN tipo ENUM('dia', 'periodo', 'horario') NOT NULL DEFAULT 'dia' AFTER profissional_id;

ALTER TABLE bloqueios
ADD COLUMN data_inicio DATE NULL AFTER tipo;

ALTER TABLE bloqueios
ADD COLUMN data_fim DATE NULL AFTER data_inicio;

ALTER TABLE bloqueios
ADD COLUMN criado_por ENUM('profissional', 'estabelecimento') DEFAULT 'profissional' AFTER motivo;

ALTER TABLE bloqueios
ADD COLUMN criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER criado_por;

ALTER TABLE bloqueios
ADD COLUMN atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER criado_em;

-- 2. Migrar dados da coluna antiga 'data' para 'data_inicio'
UPDATE bloqueios SET data_inicio = data WHERE data_inicio IS NULL;

-- 3. Definir tipo baseado em 'dia_todo'
UPDATE bloqueios
SET tipo = CASE
    WHEN dia_todo = 1 THEN 'dia'
    WHEN hora_inicio IS NOT NULL AND hora_fim IS NOT NULL THEN 'horario'
    ELSE 'dia'
END;

-- 4. Para bloqueios de horário, data_fim = data_inicio
UPDATE bloqueios
SET data_fim = data_inicio
WHERE tipo = 'horario';

-- 5. Remover coluna antiga 'data' (CUIDADO: backup antes!)
-- ALTER TABLE bloqueios DROP COLUMN data;

-- 6. Remover coluna antiga 'dia_todo'
-- ALTER TABLE bloqueios DROP COLUMN dia_todo;

-- 7. Adicionar índices
ALTER TABLE bloqueios ADD INDEX idx_profissional (profissional_id);
ALTER TABLE bloqueios ADD INDEX idx_tipo (tipo);
ALTER TABLE bloqueios ADD INDEX idx_data_inicio (data_inicio);
ALTER TABLE bloqueios ADD INDEX idx_datas (data_inicio, data_fim);
ALTER TABLE bloqueios ADD INDEX idx_criado_por (criado_por);

-- 8. Tornar data_inicio obrigatório
ALTER TABLE bloqueios MODIFY data_inicio DATE NOT NULL;

-- Verificar resultado
SELECT * FROM bloqueios LIMIT 5;
DESCRIBE bloqueios;
