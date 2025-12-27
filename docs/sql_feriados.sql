-- ============================================
-- Sistema de Feriados - AgendaPro
-- Autor: Rafael Dias - doisr.com.br
-- Data: 27/12/2024
-- ============================================

-- Criar tabela de feriados
CREATE TABLE IF NOT EXISTS feriados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NULL,
    nome VARCHAR(100) NOT NULL,
    data DATE NOT NULL,
    tipo ENUM('nacional', 'facultativo', 'municipal', 'personalizado') DEFAULT 'nacional',
    recorrente TINYINT(1) DEFAULT 1 COMMENT '1=Repete todo ano, 0=Data única',
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_estabelecimento (estabelecimento_id),
    INDEX idx_data (data),
    INDEX idx_tipo (tipo),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Inserir Feriados Nacionais Fixos (2026)
-- ============================================

INSERT INTO feriados (nome, data, tipo, recorrente, ativo) VALUES
('Confraternização Universal', '2026-01-01', 'nacional', 1, 1),
('Tiradentes', '2026-04-21', 'nacional', 1, 1),
('Dia do Trabalho', '2026-05-01', 'nacional', 1, 1),
('Independência do Brasil', '2026-09-07', 'nacional', 1, 1),
('Nossa Senhora Aparecida', '2026-10-12', 'nacional', 1, 1),
('Finados', '2026-11-02', 'nacional', 1, 1),
('Proclamação da República', '2026-11-15', 'nacional', 1, 1),
('Dia da Consciência Negra', '2026-11-20', 'nacional', 1, 1),
('Natal', '2026-12-25', 'nacional', 1, 1);

-- ============================================
-- Inserir Feriados Móveis (2026)
-- ============================================

INSERT INTO feriados (nome, data, tipo, recorrente, ativo) VALUES
('Sexta-feira Santa', '2026-04-03', 'nacional', 0, 1);

-- ============================================
-- Inserir Pontos Facultativos (2026)
-- ============================================

INSERT INTO feriados (nome, data, tipo, recorrente, ativo) VALUES
('Carnaval - Segunda', '2026-02-16', 'facultativo', 0, 1),
('Carnaval - Terça', '2026-02-17', 'facultativo', 0, 1),
('Corpus Christi', '2026-06-04', 'facultativo', 0, 1);

-- ============================================
-- Verificar inserções
-- ============================================

SELECT
    tipo,
    COUNT(*) as total,
    GROUP_CONCAT(nome ORDER BY data SEPARATOR ', ') as feriados
FROM feriados
WHERE estabelecimento_id IS NULL
GROUP BY tipo
ORDER BY FIELD(tipo, 'nacional', 'facultativo', 'municipal', 'personalizado');
