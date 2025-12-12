-- Script de criação das tabelas de horários e bloqueios (Sem Foreign Keys)
-- Autor: Rafael Dias - doisr.com.br
-- Data: 11/12/2024

-- =====================================================
-- HORÁRIOS DO ESTABELECIMENTO POR DIA DA SEMANA
-- =====================================================
CREATE TABLE IF NOT EXISTS horarios_estabelecimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado',
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Inativo (fechado)',
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estabelecimento (estabelecimento_id),
    INDEX idx_dia_semana (dia_semana),
    INDEX idx_ativo (ativo),
    UNIQUE KEY unique_estabelecimento_dia (estabelecimento_id, dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Horários de funcionamento do estabelecimento por dia da semana';

-- =====================================================
-- BLOQUEIOS DOS PROFISSIONAIS
-- =====================================================
CREATE TABLE IF NOT EXISTS bloqueios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    tipo ENUM('dia', 'periodo', 'horario') NOT NULL COMMENT 'dia=Data única, periodo=Intervalo, horario=Horário específico',
    data_inicio DATE NOT NULL,
    data_fim DATE NULL COMMENT 'NULL para bloqueio de dia único',
    hora_inicio TIME NULL COMMENT 'Para bloqueio de horário específico',
    hora_fim TIME NULL COMMENT 'Para bloqueio de horário específico',
    motivo VARCHAR(200) NULL COMMENT 'Motivo do bloqueio (férias, compromisso, etc)',
    criado_por ENUM('profissional', 'estabelecimento') DEFAULT 'profissional' COMMENT 'Quem criou o bloqueio',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_profissional (profissional_id),
    INDEX idx_tipo (tipo),
    INDEX idx_datas (data_inicio, data_fim),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_criado_por (criado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bloqueios de agenda dos profissionais (folgas, férias, compromissos)';

-- =====================================================
-- ADICIONAR LIMITE DE REAGENDAMENTOS
-- =====================================================
ALTER TABLE estabelecimentos
ADD COLUMN IF NOT EXISTS limite_reagendamentos TINYINT DEFAULT 3
COMMENT 'Quantidade máxima de reagendamentos permitidos por agendamento';

-- =====================================================
-- INSERIR HORÁRIOS PADRÃO PARA ESTABELECIMENTOS
-- =====================================================
INSERT INTO horarios_estabelecimento (estabelecimento_id, dia_semana, ativo, hora_inicio, hora_fim)
SELECT
    e.id as estabelecimento_id,
    d.dia as dia_semana,
    CASE
        WHEN d.dia = 0 THEN 0  -- Domingo inativo
        ELSE 1                 -- Outros dias ativos
    END as ativo,
    CASE
        WHEN d.dia = 6 THEN '08:00:00'  -- Sábado
        ELSE '08:00:00'                 -- Outros dias
    END as hora_inicio,
    CASE
        WHEN d.dia = 6 THEN '14:00:00'  -- Sábado até 14h
        ELSE '18:00:00'                 -- Outros dias até 18h
    END as hora_fim
FROM estabelecimentos e
CROSS JOIN (
    SELECT 0 as dia UNION ALL  -- Domingo
    SELECT 1 UNION ALL         -- Segunda
    SELECT 2 UNION ALL         -- Terça
    SELECT 3 UNION ALL         -- Quarta
    SELECT 4 UNION ALL         -- Quinta
    SELECT 5 UNION ALL         -- Sexta
    SELECT 6                   -- Sábado
) d
WHERE NOT EXISTS (
    SELECT 1 FROM horarios_estabelecimento
    WHERE estabelecimento_id = e.id
    AND dia_semana = d.dia
);

-- =====================================================
-- VERIFICAÇÕES
-- =====================================================
SELECT
    'horarios_estabelecimento' as tabela,
    COUNT(*) as registros
FROM horarios_estabelecimento
UNION ALL
SELECT
    'bloqueios' as tabela,
    COUNT(*) as registros
FROM bloqueios;
