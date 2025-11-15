-- Adicionar coluna valor_unitario na tabela orcamento_itens
-- Autor: Rafael Dias - doisr.com.br
-- Data: 15/11/2024

USE cecriativocom_lecortine_orc;

-- Verificar estrutura atual
DESCRIBE orcamento_itens;

-- Adicionar coluna valor_unitario (se não existir)
ALTER TABLE `orcamento_itens` 
ADD COLUMN `valor_unitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `quantidade`;

-- Verificar estrutura atualizada
DESCRIBE orcamento_itens;

-- Atualizar registros existentes (copiar preco_total para valor_unitario)
UPDATE `orcamento_itens` 
SET `valor_unitario` = `preco_total` / `quantidade` 
WHERE `quantidade` > 0;

SELECT 'Coluna valor_unitario adicionada com sucesso!' as status;
