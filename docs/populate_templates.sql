-- ============================================================================
-- POPULAR TEMPLATES_NOTIFICACAO MANUALMENTE
-- Execute este script para migrar os templates
-- ============================================================================

-- Limpar tabela (caso tenha algum dado)
TRUNCATE TABLE templates_notificacao;

-- Inserir templates do estabelecimento ID 1
INSERT INTO `templates_notificacao` (`estabelecimento_id`, `tipo`, `canal`, `mensagem`, `ativo`) VALUES
(1, 'confirmacao', 'whatsapp', 'Olá {cliente}! ✅ Seu agendamento foi confirmado!\n\n📅 Data: {data}\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}\n\nNos vemos em breve!', 1),
(1, 'cancelamento', 'whatsapp', 'Olá {cliente}. ❌ Seu agendamento foi cancelado.\n\n📅 Data: {data}\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n\nQualquer dúvida, entre em contato!', 1),
(1, 'reagendamento', 'whatsapp', 'Olá {cliente}! 🔄 Seu agendamento foi reagendado.\n\n📅 Nova Data: {data}\n🕐 Novo Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}', 1),
(1, 'lembrete', 'whatsapp', 'Olá {cliente}! 🔔 Lembrete: você tem um agendamento amanhã!\n\n📅 Data: {data}\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}\n\nTe esperamos!', 1),
(1, 'pagamento', 'whatsapp', 'Olá {cliente}! 💰 Pagamento confirmado!\n\n✅ Valor: R$ {valor}\n📅 Agendamento: {data} às {hora}\n\nObrigado pela preferência!', 1),
(1, 'feedback', 'whatsapp', 'Olá {cliente}! 🌟 Como foi sua experiência?\n\nGostaríamos de saber sua opinião sobre o atendimento de {profissional}.\n\nAvalie aqui: {link}', 1),
(1, 'boas_vindas', 'whatsapp', 'Olá {cliente}! 👋 Bem-vindo(a)!\n\nObrigado por se cadastrar. Estamos prontos para atendê-lo(a)!', 1);

-- Verificar se foram inseridos
SELECT
    'Templates Inseridos' as status,
    COUNT(*) as total,
    GROUP_CONCAT(tipo ORDER BY tipo SEPARATOR ', ') as tipos
FROM templates_notificacao
WHERE estabelecimento_id = 1;

-- Deve retornar: 7 templates com tipos: boas_vindas, cancelamento, confirmacao, feedback, lembrete, pagamento, reagendamento
