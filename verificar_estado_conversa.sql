-- Verificar estado da conversa do Rafael
SELECT
    bc.id,
    bc.cliente_id,
    bc.estado,
    bc.ultima_atividade,
    c.nome,
    c.telefone
FROM bot_conversas bc
JOIN clientes c ON bc.cliente_id = c.id
WHERE c.telefone = '557588890006'
ORDER BY bc.ultima_atividade DESC
LIMIT 1;
