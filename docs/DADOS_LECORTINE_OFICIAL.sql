-- ============================================================================
-- DADOS OFICIAIS - Sistema de Orçamento Le Cortine
-- Baseado no roteiro e tabelas de preços oficiais
-- Autor: Rafael Dias - doisr.com.br
-- Data: 13/11/2024 21:10
-- ============================================================================

USE `cecriativocom_lecortine_orc`;

-- Desabilitar verificação de FK temporariamente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpar dados anteriores (ordem inversa das dependências)
DELETE FROM `orcamento_extras`;
DELETE FROM `orcamento_itens`;
DELETE FROM `orcamentos`;
DELETE FROM `cores`;
DELETE FROM `produto_imagens`;
DELETE FROM `precos`;
DELETE FROM `extras`;
DELETE FROM `tecidos`;
DELETE FROM `produtos`;
DELETE FROM `colecoes`;
DELETE FROM `categorias`;
DELETE FROM `clientes`;

-- Resetar AUTO_INCREMENT
ALTER TABLE `orcamento_extras` AUTO_INCREMENT = 1;
ALTER TABLE `orcamento_itens` AUTO_INCREMENT = 1;
ALTER TABLE `orcamentos` AUTO_INCREMENT = 1;
ALTER TABLE `cores` AUTO_INCREMENT = 1;
ALTER TABLE `produto_imagens` AUTO_INCREMENT = 1;
ALTER TABLE `precos` AUTO_INCREMENT = 1;
ALTER TABLE `extras` AUTO_INCREMENT = 1;
ALTER TABLE `tecidos` AUTO_INCREMENT = 1;
ALTER TABLE `produtos` AUTO_INCREMENT = 1;
ALTER TABLE `colecoes` AUTO_INCREMENT = 1;
ALTER TABLE `categorias` AUTO_INCREMENT = 1;
ALTER TABLE `clientes` AUTO_INCREMENT = 1;

-- Reabilitar verificação de FK
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- CATEGORIAS
-- ============================================================================
INSERT INTO `categorias` (`nome`, `slug`, `descricao`, `status`, `ordem`, `criado_em`) VALUES
('Cortinas', 'cortinas', 'Cortinas em tecido e rolô', 'ativo', 1, NOW()),
('Toldos', 'toldos', 'Toldos e coberturas', 'ativo', 2, NOW()),
('Motorizadas', 'motorizadas', 'Cortinas com automação', 'ativo', 3, NOW());

-- ============================================================================
-- PRODUTOS (Conforme roteiro)
-- ============================================================================
INSERT INTO `produtos` (`categoria_id`, `nome`, `slug`, `descricao_curta`, `descricao`, `preco_base`, `tipo_calculo`, `status`, `destaque`, `ordem`, `criado_em`) VALUES
-- Produtos com formulário de orçamento
(1, 'Cortina em Tecido', 'cortina-tecido', 'Cortina Prega Victoria em tecido nobre', 'Cortina confeccionada com prega Victoria, ideal para ambientes elegantes. Disponível em Linho Rústico e Linen Light.', 442.00, 'unidade', 'ativo', 1, 1, NOW()),
(1, 'Cortina Rolô', 'cortina-rolo', 'Cortina Rolô prática e moderna', 'Cortina rolô com acionamento manual ou motorizado. Disponível em Translúcida, Blackout e Tela Solar.', 215.00, 'metro_quadrado', 'ativo', 1, 2, NOW()),
(1, 'Cortina Duplex VIP', 'cortina-duplex-vip', 'Sistema duplo de cortinas rolô', 'Sistema exclusivo com duas cortinas rolô em um único suporte. Máxima versatilidade e elegância.', 299.00, 'metro_quadrado', 'ativo', 1, 3, NOW()),

-- Produtos que direcionam para consultoria
(2, 'Toldos', 'toldos', 'Toldos sob medida', 'Toldos personalizados para área externa. Solicite consultoria personalizada.', 0.00, 'unidade', 'ativo', 0, 4, NOW()),
(3, 'Cortinas Motorizadas', 'cortinas-motorizadas', 'Automação de cortinas', 'Sistema completo de automação para cortinas. Solicite consultoria personalizada.', 0.00, 'unidade', 'ativo', 0, 5, NOW()),

-- Produto de Consultoria (para venda direta)
(2, 'Consultoria Online', 'consultoria-online', 'Consultoria personalizada com especialista', 'Atendimento especializado para projetos complexos, dimensões especiais ou soluções personalizadas. Inclui análise completa, recomendações e orçamento detalhado.', 150.00, 'unidade', 'ativo', 0, 6, NOW());

-- ============================================================================
-- COLEÇÕES
-- ============================================================================
INSERT INTO `colecoes` (`nome`, `slug`, `descricao`, `status`, `criado_em`) VALUES
('Tecidos Nobres', 'tecidos-nobres', 'Linho Rústico e Linen Light', 'ativo', NOW()),
('Rolô Translúcida', 'rolo-translucida', 'Coleção translúcida para cortinas rolô', 'ativo', NOW()),
('Rolô Blackout', 'rolo-blackout', 'Coleção blackout para cortinas rolô', 'ativo', NOW()),
('Rolô Tela Solar', 'rolo-tela-solar', 'Tela solar 5% para cortinas rolô', 'ativo', NOW()),
('Duplex Translúcida', 'duplex-translucida', 'Coleção translúcida para Duplex VIP', 'ativo', NOW());

-- ============================================================================
-- TECIDOS
-- ============================================================================
INSERT INTO `tecidos` (`colecao_id`, `produto_id`, `nome`, `codigo`, `composicao`, `largura_padrao`, `tipo`, `status`, `criado_em`) VALUES
-- Tecidos para Cortina em Tecido (Produto ID 1)
(1, 1, 'Linho Rústico', 'LR-001', '100% Linho Natural', 2.80, 'linho', 'ativo', NOW()),
(1, 1, 'Linen Light', 'LL-001', '70% Linho, 30% Poliéster', 2.80, 'linho', 'ativo', NOW()),

-- Tecidos para Cortina Rolô (Produto ID 2)
(2, 2, 'Rolô Translúcida', 'RT-001', '100% Poliéster', 2.80, 'translucido', 'ativo', NOW()),
(3, 2, 'Rolô Blackout', 'RB-001', '100% Poliéster Blackout', 2.80, 'blackout', 'ativo', NOW()),
(4, 2, 'Rolô Tela Solar 5%', 'RTS-001', 'Tela Solar com proteção UV', 2.80, 'translucido', 'ativo', NOW()),

-- Tecido para Duplex VIP (Produto ID 3)
(5, 3, 'Duplex Translúcida', 'DT-001', '100% Poliéster', 2.80, 'translucido', 'ativo', NOW());

-- ============================================================================
-- CORES
-- ============================================================================
INSERT INTO `cores` (`tecido_id`, `nome`, `codigo_hex`, `ordem`, `status`, `criado_em`) VALUES
-- Linho Rústico (Tecido ID 1) - 6 cores
(1, 'Bege Natural', '#D4C4A8', 1, 'ativo', NOW()),
(1, 'Cinza Claro', '#C0C0C0', 2, 'ativo', NOW()),
(1, 'Marrom Terra', '#8B7355', 3, 'ativo', NOW()),
(1, 'Off White', '#F5F5DC', 4, 'ativo', NOW()),
(1, 'Grafite', '#4A4A4A', 5, 'ativo', NOW()),
(1, 'Areia', '#E6D7C3', 6, 'ativo', NOW()),

-- Linen Light (Tecido ID 2) - 6 cores
(2, 'Branco', '#FFFFFF', 1, 'ativo', NOW()),
(2, 'Champagne', '#F7E7CE', 2, 'ativo', NOW()),
(2, 'Cinza Pérola', '#E8E8E8', 3, 'ativo', NOW()),
(2, 'Bege Rosado', '#E8C4A8', 4, 'ativo', NOW()),
(2, 'Taupe', '#B38B6D', 5, 'ativo', NOW()),
(2, 'Cru Natural', '#F5F5DC', 6, 'ativo', NOW()),

-- Rolô Translúcida (Tecido ID 3) - 8 cores
(3, 'Branco', '#FFFFFF', 1, 'ativo', NOW()),
(3, 'Off White', '#FAF0E6', 2, 'ativo', NOW()),
(3, 'Bege', '#F5F5DC', 3, 'ativo', NOW()),
(3, 'Areia', '#E6D7C3', 4, 'ativo', NOW()),
(3, 'Cinza Claro', '#D3D3D3', 5, 'ativo', NOW()),
(3, 'Cinza Médio', '#A9A9A9', 6, 'ativo', NOW()),
(3, 'Champagne', '#F7E7CE', 7, 'ativo', NOW()),
(3, 'Palha', '#F0E68C', 8, 'ativo', NOW()),

-- Rolô Blackout (Tecido ID 4) - 8 cores
(4, 'Branco', '#FFFFFF', 1, 'ativo', NOW()),
(4, 'Bege', '#F5F5DC', 2, 'ativo', NOW()),
(4, 'Cinza Claro', '#D3D3D3', 3, 'ativo', NOW()),
(4, 'Cinza Médio', '#808080', 4, 'ativo', NOW()),
(4, 'Cinza Escuro', '#696969', 5, 'ativo', NOW()),
(4, 'Grafite', '#4A4A4A', 6, 'ativo', NOW()),
(4, 'Preto', '#000000', 7, 'ativo', NOW()),
(4, 'Areia', '#E6D7C3', 8, 'ativo', NOW()),

-- Rolô Tela Solar (Tecido ID 5) - 6 cores
(5, 'Branco', '#FFFFFF', 1, 'ativo', NOW()),
(5, 'Cinza Claro', '#D3D3D3', 2, 'ativo', NOW()),
(5, 'Cinza Médio', '#A9A9A9', 3, 'ativo', NOW()),
(5, 'Cinza Escuro', '#696969', 4, 'ativo', NOW()),
(5, 'Grafite', '#4A4A4A', 5, 'ativo', NOW()),
(5, 'Preto', '#000000', 6, 'ativo', NOW()),

-- Duplex Translúcida (Tecido ID 6) - 8 cores
(6, 'Branco', '#FFFFFF', 1, 'ativo', NOW()),
(6, 'Off White', '#FAF0E6', 2, 'ativo', NOW()),
(6, 'Bege', '#F5F5DC', 3, 'ativo', NOW()),
(6, 'Areia', '#E6D7C3', 4, 'ativo', NOW()),
(6, 'Cinza Claro', '#D3D3D3', 5, 'ativo', NOW()),
(6, 'Cinza Médio', '#A9A9A9', 6, 'ativo', NOW()),
(6, 'Champagne', '#F7E7CE', 7, 'ativo', NOW()),
(6, 'Pérola', '#E8E8E8', 8, 'ativo', NOW());

-- ============================================================================
-- PREÇOS - CORTINA EM TECIDO (Prega Victoria)
-- Baseado em faixas de largura até 2,80m de altura
-- Preço FIXO por faixa (não é por m²)
-- ============================================================================
INSERT INTO `precos` (`produto_id`, `largura_min`, `largura_max`, `altura_min`, `altura_max`, `preco_m2`, `preco_fixo`, `criado_em`) VALUES
-- Cortina em Tecido - Produto ID 1
(1, 0.00, 2.00, 0.00, 2.80, 0.00, 442.00, NOW()),
(1, 2.01, 3.00, 0.00, 2.80, 0.00, 585.00, NOW()),
(1, 3.01, 4.00, 0.00, 2.80, 0.00, 824.00, NOW()),
(1, 4.01, 5.00, 0.00, 2.80, 0.00, 1192.00, NOW());

-- ============================================================================
-- PREÇOS - CORTINA ROLÔ
-- Preço por m² conforme tipo
-- ============================================================================
-- Rolô Translúcida - R$ 215,00/m²
INSERT INTO `precos` (`produto_id`, `largura_min`, `largura_max`, `altura_min`, `altura_max`, `preco_m2`, `criado_em`) VALUES
(2, 0.00, 10.00, 0.00, 10.00, 215.00, NOW());

-- ============================================================================
-- PREÇOS - DUPLEX VIP
-- Preço por m² - R$ 299,00/m²
-- ============================================================================
INSERT INTO `precos` (`produto_id`, `largura_min`, `largura_max`, `altura_min`, `altura_max`, `preco_m2`, `criado_em`) VALUES
(3, 0.00, 10.00, 0.00, 10.00, 299.00, NOW());

-- ============================================================================
-- EXTRAS - BLACKOUT ADICIONAL (para Cortina em Tecido)
-- Valores conforme largura
-- ============================================================================
INSERT INTO `extras` (`nome`, `descricao`, `tipo_preco`, `valor`, `aplicavel_a`, `status`, `ordem`, `criado_em`) VALUES
('Blackout até 2,00m', 'Forro blackout com 80% de vedação de luz - Largura até 2,00m', 'fixo', 250.00, '1', 'ativo', 1, NOW()),
('Blackout até 3,00m', 'Forro blackout com 80% de vedação de luz - Largura até 3,00m', 'fixo', 300.00, '1', 'ativo', 2, NOW()),
('Blackout até 4,00m', 'Forro blackout com 80% de vedação de luz - Largura até 4,00m', 'fixo', 360.00, '1', 'ativo', 3, NOW()),
('Blackout até 5,00m', 'Forro blackout com 80% de vedação de luz - Largura até 5,00m', 'fixo', 395.00, '1', 'ativo', 4, NOW()),
('Motorização', 'Motor elétrico com controle remoto', 'fixo', 850.00, '1,2,3', 'ativo', 5, NOW()),
('Instalação Profissional', 'Serviço de instalação por profissional', 'fixo', 150.00, '1,2,3', 'ativo', 6, NOW());

-- ============================================================================
-- CLIENTE DE TESTE
-- ============================================================================
INSERT INTO `clientes` (`nome`, `email`, `telefone`, `whatsapp`, `cidade`, `estado`, `criado_em`) VALUES
('Cliente Teste', 'teste@lecortine.com', '(11) 99999-9999', '11999999999', 'São Paulo', 'SP', NOW());

-- ============================================================================
-- FIM DO SCRIPT
-- ============================================================================
SELECT 'Dados oficiais Le Cortine inseridos com sucesso!' as Resultado;
SELECT 'Produtos cadastrados:' as Info, COUNT(*) as Total FROM produtos;
SELECT 'Tecidos cadastrados:' as Info, COUNT(*) as Total FROM tecidos;
SELECT 'Cores cadastradas:' as Info, COUNT(*) as Total FROM cores;
SELECT 'Preços cadastrados:' as Info, COUNT(*) as Total FROM precos;
SELECT 'Extras cadastrados:' as Info, COUNT(*) as Total FROM extras;
