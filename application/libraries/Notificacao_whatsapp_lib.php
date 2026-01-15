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

        if (!$estabelecimento) {
            log_message('error', 'Notificacao WhatsApp: Estabelecimento ' . $estabelecimento_id . ' não encontrado');
            return false;
        }

        log_message('debug', 'Notificacao WhatsApp: waha_status=' . ($estabelecimento->waha_status ?? 'NULL') . ', session=' . ($estabelecimento->waha_session_name ?? 'NULL'));

        // Verificar se tem sessão WAHA configurada
        if (empty($estabelecimento->waha_session_name)) {
            log_message('error', 'Notificacao WhatsApp: Estabelecimento ' . $estabelecimento_id . ' sem sessão WAHA configurada');
            return false;
        }

        // Verificar se WAHA está ativo para o estabelecimento
        if (!$estabelecimento->waha_ativo) {
            log_message('debug', 'Notificacao WhatsApp: WAHA não está ativo para estabelecimento ' . $estabelecimento_id);
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
        log_message('debug', 'Notificacao WhatsApp: enviar_confirmacao - Agendamento #' . $agendamento->id);
        log_message('debug', 'Notificacao WhatsApp: estabelecimento_id=' . ($agendamento->estabelecimento_id ?? 'NULL'));

        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            log_message('error', 'Notificacao WhatsApp: Falha ao configurar WAHA para estabelecimento ' . $agendamento->estabelecimento_id);
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
     * Enviar notificação de agendamento pendente (aguardando confirmação)
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @return array
     */
    public function enviar_pendente($agendamento) {
        log_message('debug', 'Notificacao WhatsApp: enviar_pendente - Agendamento #' . $agendamento->id);
        log_message('debug', 'Notificacao WhatsApp: estabelecimento_id=' . ($agendamento->estabelecimento_id ?? 'NULL'));

        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            log_message('error', 'Notificacao WhatsApp: Falha ao configurar WAHA para estabelecimento ' . $agendamento->estabelecimento_id);
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        log_message('debug', 'Notificacao WhatsApp: Numero original do cliente = ' . ($agendamento->cliente_whatsapp ?? 'NULL'));

        $numero = $this->limpar_numero($agendamento->cliente_whatsapp);
        if (!$numero) {
            log_message('warning', 'Notificacao WhatsApp: Cliente sem WhatsApp válido - Agendamento #' . $agendamento->id);
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        $chat_id = $this->CI->waha_lib->obter_chat_id_valido($numero);
        if (!$chat_id) {
            log_message('warning', 'Notificacao WhatsApp: Número não encontrado no WhatsApp - ' . $numero);
            $chat_id = $this->CI->waha_lib->formatar_chat_id($numero);
        }

        log_message('debug', 'Notificacao WhatsApp: ChatId final = ' . $chat_id);

        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));
        $valor_formatado = number_format($agendamento->servico_preco, 2, ',', '.');

        $mensagem = "🎉 *Agendamento Criado!*\n\n";
        $mensagem .= "📋 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n";
        $mensagem .= "📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💰 *Valor:* R$ {$valor_formatado}\n\n";
        $mensagem .= "📍 {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "✅ Você receberá uma mensagem para confirmar sua presença próximo à data do agendamento.\n\n";
        $mensagem .= "Até lá! 👋";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        $this->registrar_log($agendamento, 'pendente', $resultado);

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
     * Enviar notificação de início de atendimento
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @return array
     */
    public function enviar_inicio($agendamento) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $chat_id = $this->obter_chat_id_cliente($agendamento->cliente_whatsapp);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número do cliente não informado ou inválido'];
        }

        $mensagem = "▶️ *Atendimento Iniciado!*\n\n";
        $mensagem .= "Olá {$agendamento->cliente_nome},\n\n";
        $mensagem .= "Seu atendimento está começando agora!\n\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n\n";
        $mensagem .= "Relaxe e aproveite! 😊\n\n";
        $mensagem .= "📍 {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        // Log da notificação
        $this->registrar_log($agendamento, 'inicio', $resultado);

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
     * Enviar lembrete de pagamento pendente
     * Enviado quando o tempo inicial do PIX expira
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @param string $link_pagamento URL da página pública de pagamento
     * @param int $minutos_restantes Minutos restantes para pagar
     * @return array
     */
    public function enviar_lembrete_pagamento($agendamento, $link_pagamento, $minutos_restantes = 5) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $numero = $this->limpar_numero($agendamento->cliente_whatsapp);
        if (!$numero) {
            log_message('warning', 'Notificacao WhatsApp: Cliente sem WhatsApp - Lembrete pagamento #' . $agendamento->id);
            return ['success' => false, 'error' => 'Número do cliente não informado'];
        }

        $chat_id = $this->CI->waha_lib->obter_chat_id_valido($numero);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número não encontrado no WhatsApp'];
        }

        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));
        $valor_formatado = number_format($agendamento->pagamento_valor, 2, ',', '.');

        $mensagem = "⚠️ *Pagamento Pendente!*\n\n";
        $mensagem .= "Olá {$agendamento->cliente_nome},\n\n";
        $mensagem .= "Notamos que você ainda não concluiu o pagamento do seu agendamento:\n\n";
        $mensagem .= "📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "💰 *Valor:* R$ {$valor_formatado}\n\n";
        $mensagem .= "⏳ *Você tem mais {$minutos_restantes} minutos para pagar.*\n\n";
        $mensagem .= "🔗 *Clique no link abaixo para pagar:*\n";
        $mensagem .= "{$link_pagamento}\n\n";
        $mensagem .= "Após esse prazo, seu agendamento será cancelado automaticamente.\n\n";
        $mensagem .= "📍 {$agendamento->estabelecimento_nome}\n\n";
        $mensagem .= "_Mensagem automática - não responda._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);

        $this->registrar_log($agendamento, 'lembrete_pagamento', $resultado);

        return $resultado;
    }

    /**
     * Notificar profissional/estabelecimento sobre novo agendamento
     * Se estabelecimento tiver apenas 1 profissional, notifica só o estabelecimento
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @return array
     */
    public function notificar_profissional_novo_agendamento($agendamento) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        // Verificar quantidade de profissionais do estabelecimento
        $this->CI->load->model('Profissional_model');
        $profissionais = $this->CI->Profissional_model->get_by_estabelecimento($agendamento->estabelecimento_id);
        $total_profissionais = count($profissionais);

        // Determinar destinatário: se só 1 profissional, notifica estabelecimento
        if ($total_profissionais <= 1) {
            $this->CI->load->model('Estabelecimento_model');
            $estabelecimento = $this->CI->Estabelecimento_model->get_by_id($agendamento->estabelecimento_id);
            $numero_destino = $estabelecimento->whatsapp ?? null;
            $nome_destino = $estabelecimento->nome;
            $tipo_destino = 'estabelecimento';
        } else {
            $numero_destino = $agendamento->profissional_whatsapp ?? null;
            $nome_destino = $agendamento->profissional_nome;
            $tipo_destino = 'profissional';
        }

        if (empty($numero_destino)) {
            log_message('warning', "Notificacao WhatsApp: {$tipo_destino} sem WhatsApp - Agendamento #{$agendamento->id}");
            return ['success' => false, 'error' => "{$tipo_destino} sem WhatsApp cadastrado"];
        }

        $chat_id = $this->obter_chat_id_cliente($numero_destino);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número inválido'];
        }

        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));
        $valor_formatado = number_format($agendamento->servico_preco ?? 0, 2, ',', '.');

        $mensagem = "📅 *Novo Agendamento!*\n\n";
        $mensagem .= "👤 *Cliente:* {$agendamento->cliente_nome}\n";
        if (!empty($agendamento->cliente_whatsapp)) {
            $mensagem .= "📱 *WhatsApp:* {$agendamento->cliente_whatsapp}\n";
        }
        $mensagem .= "\n📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "💰 *Valor:* R$ {$valor_formatado}\n";
        if ($total_profissionais > 1) {
            $mensagem .= "👤 *Profissional:* {$agendamento->profissional_nome}\n";
        }
        $mensagem .= "\n_Mensagem automática do sistema._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);
        $this->registrar_log_interno($agendamento, "novo_agendamento_{$tipo_destino}", $numero_destino, $resultado);

        return $resultado;
    }

    /**
     * Notificar profissional/estabelecimento sobre cancelamento
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @param string $motivo Motivo do cancelamento
     * @return array
     */
    public function notificar_profissional_cancelamento($agendamento, $motivo = null) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $this->CI->load->model('Profissional_model');
        $profissionais = $this->CI->Profissional_model->get_by_estabelecimento($agendamento->estabelecimento_id);
        $total_profissionais = count($profissionais);

        if ($total_profissionais <= 1) {
            $this->CI->load->model('Estabelecimento_model');
            $estabelecimento = $this->CI->Estabelecimento_model->get_by_id($agendamento->estabelecimento_id);
            $numero_destino = $estabelecimento->whatsapp ?? null;
            $tipo_destino = 'estabelecimento';
        } else {
            $numero_destino = $agendamento->profissional_whatsapp ?? null;
            $tipo_destino = 'profissional';
        }

        if (empty($numero_destino)) {
            return ['success' => false, 'error' => "{$tipo_destino} sem WhatsApp cadastrado"];
        }

        $chat_id = $this->obter_chat_id_cliente($numero_destino);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número inválido'];
        }

        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));

        $mensagem = "❌ *Agendamento Cancelado*\n\n";
        $mensagem .= "👤 *Cliente:* {$agendamento->cliente_nome}\n";
        $mensagem .= "📅 *Data:* {$data_formatada}\n";
        $mensagem .= "⏰ *Horário:* {$hora_formatada}\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        if ($motivo) {
            $mensagem .= "\n📝 *Motivo:* {$motivo}\n";
        }
        $mensagem .= "\n_Mensagem automática do sistema._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);
        $this->registrar_log_interno($agendamento, "cancelamento_{$tipo_destino}", $numero_destino, $resultado);

        return $resultado;
    }

    /**
     * Notificar profissional/estabelecimento sobre reagendamento
     *
     * @param object $agendamento Objeto do agendamento com joins
     * @param string $data_anterior Data anterior
     * @param string $hora_anterior Hora anterior
     * @return array
     */
    public function notificar_profissional_reagendamento($agendamento, $data_anterior, $hora_anterior) {
        if (!$this->configurar_waha($agendamento->estabelecimento_id)) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        $this->CI->load->model('Profissional_model');
        $profissionais = $this->CI->Profissional_model->get_by_estabelecimento($agendamento->estabelecimento_id);
        $total_profissionais = count($profissionais);

        if ($total_profissionais <= 1) {
            $this->CI->load->model('Estabelecimento_model');
            $estabelecimento = $this->CI->Estabelecimento_model->get_by_id($agendamento->estabelecimento_id);
            $numero_destino = $estabelecimento->whatsapp ?? null;
            $tipo_destino = 'estabelecimento';
        } else {
            $numero_destino = $agendamento->profissional_whatsapp ?? null;
            $tipo_destino = 'profissional';
        }

        if (empty($numero_destino)) {
            return ['success' => false, 'error' => "{$tipo_destino} sem WhatsApp cadastrado"];
        }

        $chat_id = $this->obter_chat_id_cliente($numero_destino);
        if (!$chat_id) {
            return ['success' => false, 'error' => 'Número inválido'];
        }

        $data_anterior_fmt = date('d/m/Y', strtotime($data_anterior));
        $hora_anterior_fmt = date('H:i', strtotime($hora_anterior));
        $data_nova_fmt = date('d/m/Y', strtotime($agendamento->data));
        $hora_nova_fmt = date('H:i', strtotime($agendamento->hora_inicio));

        $mensagem = "🔄 *Agendamento Reagendado*\n\n";
        $mensagem .= "👤 *Cliente:* {$agendamento->cliente_nome}\n\n";
        $mensagem .= "❌ *Antes:* {$data_anterior_fmt} às {$hora_anterior_fmt}\n";
        $mensagem .= "✅ *Agora:* {$data_nova_fmt} às {$hora_nova_fmt}\n\n";
        $mensagem .= "💇 *Serviço:* {$agendamento->servico_nome}\n";
        $mensagem .= "\n_Mensagem automática do sistema._";

        $resultado = $this->CI->waha_lib->enviar_texto($chat_id, $mensagem);
        $this->registrar_log_interno($agendamento, "reagendamento_{$tipo_destino}", $numero_destino, $resultado);

        return $resultado;
    }

    /**
     * Registrar log interno para notificações de profissional/estabelecimento
     */
    private function registrar_log_interno($agendamento, $tipo, $numero_destino, $resultado) {
        $status = $resultado['success'] ? 'sucesso' : 'erro';
        $erro = $resultado['error'] ?? null;
        log_message('info', "Notificacao WhatsApp [{$tipo}] - Agendamento #{$agendamento->id} - Destino: {$numero_destino} - Status: {$status}" . ($erro ? " - Erro: {$erro}" : ""));
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
