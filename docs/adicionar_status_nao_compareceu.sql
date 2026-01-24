-- Adicionar status 'nao_compareceu' na tabela agendamentos
-- Data: 20/01/2026
-- Autor: Rafael Dias - doisr.com.br

-- Verificar estrutura atual da coluna status
SHOW COLUMNS FROM agendamentos LIKE 'status';

-- Alterar ENUM para incluir 'nao_compareceu'
ALTER TABLE agendamentos
MODIFY COLUMN status ENUM(
    'pendente',
    'confirmado',
    'em_atendimento',
    'finalizado',
    'cancelado',
    'reagendado',
    'nao_compareceu'
) NOT NULL DEFAULT 'pendente';

-- Verificar alteração
SHOW COLUMNS FROM agendamentos LIKE 'status';

-- Consultar agendamentos com novo status (deve estar vazio inicialmente)
SELECT COUNT(*) as total_nao_compareceu
FROM agendamentos
WHERE status = 'nao_compareceu';
