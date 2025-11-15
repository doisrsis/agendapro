-- Verificar dados do orçamento #5
-- Autor: Rafael Dias - doisr.com.br
-- Data: 15/11/2024

-- 1. Verificar o orçamento
SELECT * FROM orcamentos WHERE id = 5;

-- 2. Verificar os itens do orçamento
SELECT * FROM orcamento_itens WHERE orcamento_id = 5;

-- 3. Verificar com JOIN (como o sistema faz)
SELECT 
    orcamento_itens.*,
    produtos.nome as produto_nome,
    produtos.imagem_principal as produto_imagem,
    tecidos.nome as tecido_nome,
    cores.nome as cor_nome
FROM orcamento_itens
LEFT JOIN produtos ON produtos.id = orcamento_itens.produto_id
LEFT JOIN tecidos ON tecidos.id = orcamento_itens.tecido_id
LEFT JOIN cores ON cores.id = orcamento_itens.cor_id
WHERE orcamento_itens.orcamento_id = 5;

-- 4. Verificar extras dos itens
SELECT 
    orcamento_extras.*,
    extras.nome as extra_nome
FROM orcamento_extras
INNER JOIN orcamento_itens ON orcamento_itens.id = orcamento_extras.item_id
LEFT JOIN extras ON extras.id = orcamento_extras.extra_id
WHERE orcamento_itens.orcamento_id = 5;

-- 5. Contar itens
SELECT COUNT(*) as total_itens FROM orcamento_itens WHERE orcamento_id = 5;
