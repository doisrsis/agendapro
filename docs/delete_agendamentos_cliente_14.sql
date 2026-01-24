-- SQL para apagar todos os agendamentos do cliente_id 14
-- Autor: Rafael Dias - doisr.com.br (21/01/2026)
-- ATENÇÃO: Esta operação é irreversível. Execute com cuidado!

-- 1. Verificar quantos agendamentos serão apagados
SELECT COUNT(*) as total_agendamentos,
       SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
       SUM(CASE WHEN status = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
       SUM(CASE WHEN status = 'reagendado' THEN 1 ELSE 0 END) as reagendados,
       SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
       SUM(CASE WHEN status = 'finalizado' THEN 1 ELSE 0 END) as finalizados
FROM agendamentos
WHERE cliente_id = 14;

-- 2. Visualizar os agendamentos que serão apagados
SELECT id, data, hora_inicio, status, servico_id, profissional_id, observacoes
FROM agendamentos
WHERE cliente_id = 14
ORDER BY data DESC, hora_inicio DESC;

-- 3. EXECUTAR DELEÇÃO (descomente a linha abaixo para executar)
-- DELETE FROM agendamentos WHERE cliente_id = 14;

-- 4. Verificar se foi apagado (após executar o DELETE)
-- SELECT COUNT(*) as total_restante FROM agendamentos WHERE cliente_id = 14;
