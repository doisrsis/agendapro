<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library de Notificações WhatsApp para Agendamentos
 *
 * Envia notificações automáticas via WhatsApp usando WAHA API
 * - Confirmação de agendamento
 * - Lembrete antes do horário
 * - Cancelamento
 * - Reagendamento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 28/12/2025
 */
class Notificacao_whatsapp_lib {

    private $CI;
    private $waha_lib;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('waha_lib');
        $this->CI->load->model('Estabelecimento_model');
        $this->CI->load->model('Configuracao_model');
    }

    /**
     * Configurar WAHA para um estabelecimento específico
     * Usa credenciais do Super Admin com sessão do estabelecimento
     *
     * @param int $estabelecimento_id
     * @return bool
     */
    private function configurar_waha($estabelecimento_id) {
        // Buscar estabelecimento
        $estabelecimento = $this->CI->Estabelecimento_model->get_by_id($estabelecimento_id);

        if (!$estabelecimento || $estabelecimento->waha_status != 'conectado') {
            log_message('debug', 'Notificacao WhatsApp: Estabelecimento ' . $estabelecimento_id . ' não está conectado');
            return false;
        }

        // Buscar configurações WAHA do Super Admin
        $configs = $this->CI->Configuracao_model->get_by_grupo('waha');

        if (empty($configs)) {
            log_message('error', 'Notificacao WhatsApp: Configurações WAHA do SaaS não encontradas');
            return false;
        }

        $config_array = [];
        foreach ($configs as $config) {
            $config_array[$config->chave] = $config->valor;
        }

        if (empty($config_array['waha_api_url']) || empty($config_array['waha_api_key'])) {
            log_message('error', 'Notificacao WhatsApp: URL ou API Key do SaaS não configuradas');
            return false;
        }

        // Log para debug
        log_message('debug', 'Notificacao WhatsApp: Configurando para estabelecimento ' . $estabelecimento_id);
        log_message('debug', 'Notificacao WhatsApp: Session name = ' . ($estabelecimento->waha_session_name ?? 'NULL'));
        log_message('debug', 'Notificacao WhatsApp: API URL = ' . $config_array['waha_api_url']);

        // Configurar a library com credenciais do SaaS mas sessão do estabelecimento
        $this->CI->waha_lib->set_credentials(
            $config_array['waha_api_url'],
            $config_array['waha_api_key'],
            $estabelecimento->waha_session_name
        );

        return true;
    }

    /**
     * Enviar notificação de agendamento confirmado
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @return array
     */
    public function enviar_confirmacao($agendamento) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        // Log do número original
        log_message('debug', 'Notificacao WhatsApp: Numero original do cliente = ' . ($agendamento->cliente_whatsapp ?? 'NULL'));

        $numero = $this->limpar_numero($agendamento->cliente_whatsapp);
        if (!$numero) {
            log_message('warning', 'Notificacao WhatsApp: Cliente sem WhatsApp válido - Agendamento #' . $agendamento->id);
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        // Tentar obter chatId válido (verifica com e sem nono dígito)
        $chat_id = $this->CI->waha_lib->obter_chat_id_valido($numero);
        if (!$chat_id) {
            log_message('warning', 'Notificacao WhatsApp: Número não encontrado no WhatsApp - ' . $numero);
            // Usar número original como fallback
            $chat_id = $this->CI->waha_lib->formatar_chat_id($numero);
        }

        log_message('debug', 'Notificacao WhatsApp: ChatId final = ' . $chat_id);

        // Formatar data e hora
        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));
        $valor_formatado = number_format($agendamento->servico_preco, 2, ',', '.');

        $mensagem = "✅ *Agendamento Confirmado!*\n\n";
        $mensagem .= "📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n";
        $mensagem .= "💰 *Valor:* R$ {$valor_formatado}\n\n";
        $mensagem .= "📍 *Local:* {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "Caso precise cancelar ou reagendar, entre em contato conosco.\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        // Log da notificação
        $this->registrar_log($agendamento, 'confirmacao', $resultado);

        return $resultado;
    }

    /**
     * Enviar lembrete de agendamento
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @param int $horas_antes Quantas horas antes do agendamento
     * @return array
     */
    public function enviar_lembrete($agendamento, $horas_antes = 24) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $chat_id = $this->obter_chat_id_cliente($agendamento->cliente_whatsapp);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        // Formatar data e hora
        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));

        $tempo_texto = $horas_antes == 24 ? 'amanhã' : "em {$horas_antes} horas";

        $mensagem = "⏰ *Lembrete de Agendamento*\n\n";
        $mensagem .= "Olá {$agendamento->cliente_nome}!\n\n";
        $mensagem .= "Passando para lembrar do seu agendamento {$tempo_texto}:\n\n";
        $mensagem .= "📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n\n";
        $mensagem .= "📍 *Local:* {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "Te esperamos! 😊\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        // Log da notificação
        $this->registrar_log($agendamento, 'lembrete', $resultado);

        return $resultado;
    }

    /**
     * Enviar notificação de cancelamento
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @param string $motivo Motivo do cancelamento
     * @return array
     */
    public function enviar_cancelamento($agendamento, $motivo = null) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $chat_id = $this->obter_chat_id_cliente($agendamento->cliente_whatsapp);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        // Formatar data e hora
        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));

        $mensagem = "❌ *Agendamento Cancelado*\n\n";
        $mensagem .= "Olá {$agendamento->cliente_nome},\n\n";
        $mensagem .= "Seu agendamento foi cancelado:\n\n";
        $mensagem .= "📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";

        if ($motivo) {
            $mensagem .= "\n📝 *Motivo:* {$motivo}\n";
        }

        $mensagem .= "\nPara reagendar, entre em contato conosco.\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        // Log da notificação
        $this->registrar_log($agendamento, 'cancelamento', $resultado);

        return $resultado;
    }

    /**
     * Enviar notificação de reagendamento
     *
     * @param object $agendamento Objeto do agendamento com joins (novos dados)
     * @param string $data_anterior Data anterior do agendamento
     * @param string $hora_anterior Hora anterior do agendamento
     * @return array
     */
    public function enviar_reagendamento($agendamento, $data_anterior, $hora_anterior) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $chat_id = $this->obter_chat_id_cliente($agendamento->cliente_whatsapp);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        // Formatar datas e horas
        $data_anterior_fmt = date('d/m/Y', strtotime($data_anterior));
        $hora_anterior_fmt = date('H:i', strtotime($hora_anterior));
        $data_nova_fmt = date('d/m/Y', strtotime($agendamento->data));
        $hora_nova_fmt = date('H:i', strtotime($agendamento->hora_inicio));

        $mensagem = "🔄 *Agendamento Reagendado*\n\n";
        $mensagem .= "Olá {$agendamento->cliente_nome},\n\n";
        $mensagem .= "Seu agendamento foi alterado:\n\n";
        $mensagem .= "❌ *Antes:* {$data_anterior_fmt} às {$hora_anterior_fmt}\n";
        $mensagem .= "✅ *Agora:* {$data_nova_fmt} às {$hora_nova_fmt}\n\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n\n";
        $mensagem .= "📍 *Local:* {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "Te esperamos! 😊\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        // Log da notificação
        $this->registrar_log($agendamento, 'reagendamento', $resultado);

        return $resultado;
    }

    /**
     * Enviar notificação de agendamento finalizado (pedir avaliação)
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @return array
     */
    public function enviar_finalizacao($agendamento) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $chat_id = $this->obter_chat_id_cliente($agendamento->cliente_whatsapp);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        $mensagem = "⭐ *Obrigado pela visita!*\n\n";
        $mensagem .= "Olá {$agendamento->cliente_nome},\n\n";
        $mensagem .= "Esperamos que tenha gostado do atendimento!\n\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n\n";
        $mensagem .= "Sua opinião é muito importante para nós.\n";
        $mensagem .= "Volte sempre! 😊\n\n";
        $mensagem .= "📍 {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        // Log da notificação
        $this->registrar_log($agendamento, 'finalizacao', $resultado);

        return $resultado;
    }

    /**
     * Limpar e formatar número de telefone
     *
     * @param string $numero
     * @return string|null
     */
    private function limpar_numero($numero) {
        if (empty($numero)) {
            return null;
        }

        // Remover tudo que não for número
        $numero = preg_replace('/[^0-9]/', '', $numero);

        // Verificar se tem tamanho válido
        if (strlen($numero) < 10) {
            return null;
        }

        // Adicionar código do país se não tiver
        if (strlen($numero) <= 11) {
            $numero = '55' . $numero;
        }

        return $numero;
    }

    /**
     * Obter chatId válido para o cliente
     * Verifica se número existe no WhatsApp, tentando com e sem nono dígito
     *
     * @param string $numero_original Número original do cliente
     * @return string ChatId válido ou formatado
     */
    private function obter_chat_id_cliente($numero_original) {
        $numero = $this->limpar_numero($numero_original);
        if (!$numero) {
            return null;
        }

        // Tentar obter chatId válido (verifica com e sem nono dígito)
        $chat_id = $this->CI->waha_lib->obter_chat_id_valido($numero);

        if (!$chat_id) {
            log_message('warning', 'Notificacao WhatsApp: Número não encontrado no WhatsApp - ' . $numero);
            // Usar número original como fallback
            $chat_id = $this->CI->waha_lib->formatar_chat_id($numero);
        }

        return $chat_id;
    }

    /**
     * Registrar log da notificação enviada
     *
     * @param object $agendamento
     * @param string $tipo
     * @param array $resultado
     */
    private function registrar_log($agendamento, $tipo, $resultado) {
        $status = $resultado['success'] ? 'sucesso' : 'erro';
        $erro = $resultado['error'] ?? null;

        log_message('info', "Notificacao WhatsApp [{$tipo}] - Agendamento #{$agendamento->id} - Status: {$status}" . ($erro ? " - Erro: {$erro}" : ""));

        // Se a tabela whatsapp_mensagens existir, registrar lá também
        if ($this->CI->db->table_exists('whatsapp_mensagens')) {
            $this->CI->db->insert('whatsapp_mensagens', [
                'estabelecimento_id' => $agendamento->estabelecimento_id,
                'direcao' => 'saida',
                'numero_destino' => $this->limpar_numero($agendamento->cliente_whatsapp),
                'tipo_mensagem' => 'texto',
                'conteudo' => "Notificação de {$tipo} - Agendamento #{$agendamento->id}",
                'message_id' => $resultado['response']['key']['id'] ?? null,
                'status' => $resultado['success'] ? 'enviado' : 'erro',
                'erro_mensagem' => $erro
            ]);
        }
    }
}
