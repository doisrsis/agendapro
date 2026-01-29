-- ============================================
-- Adicionar campos para Gatilho do Bot WhatsApp
-- Permite estabelecimentos configurarem quando o bot deve responder
-- Autor: Rafael Dias - doisr.com.br
-- Data: 25/01/2026
-- ============================================

-- Adicionar campos na tabela estabelecimentos
ALTER TABLE estabelecimentos
ADD COLUMN bot_modo_gatilho ENUM('sempre_ativo', 'palavra_chave')
    DEFAULT 'sempre_ativo'
    COMMENT 'Modo de ativação do bot: sempre_ativo ou palavra_chave'
    AFTER bot_timeout_minutos,

ADD COLUMN bot_palavras_chave TEXT NULL
    COMMENT 'Palavras-chave para ativar bot (JSON array) - usado quando modo = palavra_chave'
    AFTER bot_modo_gatilho;

-- Verificar estrutura atualizada
DESCRIBE estabelecimentos;

-- ============================================
-- NOTAS DE IMPLEMENTAÇÃO:
-- ============================================
-- 1. bot_modo_gatilho: Define quando o bot responde
--    - 'sempre_ativo': Bot responde a qualquer mensagem (padrão atual)
--    - 'palavra_chave': Bot só responde se mensagem contiver palavra-chave
--
-- 2. bot_palavras_chave: Array JSON de palavras-chave
--    Exemplo: ["agendar", "agendamento", "marcar", "horário"]
--    - Só usado quando bot_modo_gatilho = 'palavra_chave'
--    - Busca case-insensitive e sem acentos
--    - Se vazio, comporta como sempre_ativo
--
-- 3. Estados especiais SEMPRE respondem (ignoram gatilho):
--    - pos_nao_compareceu: Cliente respondendo notificação de falta
--    - aguardando_comprovante: Cliente enviando comprovante PIX
--    - Qualquer estado de conversa já iniciada
--
-- 4. Compatibilidade: Estabelecimentos existentes ficam com 'sempre_ativo' (default)
--
-- 5. Fluxo palavra-chave:
--    - Cliente: "Quero agendar um horário"
--    - Bot detecta "agendar" → Ativa bot → Mostra menu
--    - Cliente: "Obrigado" (após finalizar)
--    - Bot: Encerra conversa
--    - Cliente: "Tudo bem?"
--    - Bot: Não responde (sem palavra-chave)
-- ============================================
