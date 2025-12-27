-- Verificar estrutura da tabela
DESCRIBE estabelecimentos;

-- Verificar valores atuais
SELECT id, nome, usar_intervalo_fixo, intervalo_agendamento, tempo_minimo_agendamento
FROM estabelecimentos;

-- Testar update manual
UPDATE estabelecimentos
SET usar_intervalo_fixo = 1,
    intervalo_agendamento = 15
WHERE id = 1;

-- Verificar se salvou
SELECT id, nome, usar_intervalo_fixo, intervalo_agendamento
FROM estabelecimentos
WHERE id = 1;
