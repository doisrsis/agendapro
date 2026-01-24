<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Cron
 *
 * Tarefas agendadas do sistema (Cron Jobs)
 * Executa verificações globais para todos os estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 28/12/2024
 */
class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Configuracao_model');
        $this->load->model('Bot_conversa_model');
        $this->load->library('notificacao_whatsapp_lib');
    }

    /**
     * Verificar token de segurança do cron
     *
     * @return bool
     */
    private function verificar_token() {
        $token_recebido = $this->input->get('token');

        if (empty($token_recebido)) {
            return false;
        }

        // Buscar token configurado
        $config = $this->Configuracao_model->get_by_chave('cron_token');

        if (!$config || $config->valor !== $token_recebido) {
            return false;
        }

        return true;
    }

    /**
     * Registrar log do cron
     *
     * @param string $tipo Tipo do cron
     * @param int $registros Quantidade de registros processados
     * @param string $detalhes Detalhes da execução
     */
    private function registrar_log($tipo, $registros, $detalhes = null) {
        $this->db->insert('cron_logs', [
            'tipo' => $tipo,
            'registros_processados' => $registros,
            'detalhes' => $detalhes,
            'executado_em' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Verificar pagamentos pendentes de todos os estabelecimentos
     *
     * Este cron deve ser executado a cada 1-2 minutos
     * URL: /cron/verificar_pagamentos?token=SEU_TOKEN
     *
     * Fluxo:
     * 1. Busca agendamentos com pagamento pendente que expiraram
     * 2. Envia lembrete WhatsApp com link para pagar
     * 3. Estende expiração pelo tempo adicional configurado
     * 4. Busca agendamentos com tempo adicional expirado
     * 5. Cancela esses agendamentos
     */
    public function verificar_pagamentos() {
        // Verificar token de segurança
        if (!$this->verificar_token()) {
            log_message('error', 'CRON: Tentativa de acesso sem token válido');
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando verificação de pagamentos pendentes');

        $resultado = [
            'lembretes_enviados' => 0,
            'agendamentos_cancelados' => 0,
            'erros' => []
        ];

        // =====================================================================
        // ETAPA 1: Enviar lembretes para pagamentos expirados
        // =====================================================================
        $pendentes_expirados = $this->Agendamento_model->get_pagamentos_pendentes_expirados();

        foreach ($pendentes_expirados as $agendamento) {
            try {
                // Gerar token único para acesso público
                $token = $this->Agendamento_model->gerar_token_pagamento();

                // Calcular nova expiração (tempo adicional)
                $tempo_adicional = $agendamento->agendamento_tempo_adicional_pix ?? 5;

                // Se tempo adicional for 0, cancelar direto
                if ($tempo_adicional <= 0) {
                    $this->cancelar_agendamento_expirado($agendamento);
                    $resultado['agendamentos_cancelados']++;
                    continue;
                }

                $nova_expiracao = date('Y-m-d H:i:s', strtotime("+{$tempo_adicional} minutes"));

                // Atualizar agendamento
                $this->Agendamento_model->update($agendamento->id, [
                    'pagamento_token' => $token,
                    'pagamento_lembrete_enviado' => 1,
                    'pagamento_expira_adicional_em' => $nova_expiracao
                ]);

                // Enviar notificação WhatsApp
                $link_pagamento = base_url('pagamento/' . $token);
                $this->notificacao_whatsapp_lib->enviar_lembrete_pagamento($agendamento, $link_pagamento, $tempo_adicional);

                $resultado['lembretes_enviados']++;

                log_message('info', "CRON: Lembrete enviado para agendamento #{$agendamento->id}");

            } catch (Exception $e) {
                $resultado['erros'][] = "Agendamento #{$agendamento->id}: " . $e->getMessage();
                log_message('error', "CRON: Erro no agendamento #{$agendamento->id}: " . $e->getMessage());
            }
        }

        // =====================================================================
        // ETAPA 2: Cancelar agendamentos com tempo adicional expirado
        // =====================================================================
        $tempo_adicional_expirado = $this->Agendamento_model->get_pagamentos_tempo_adicional_expirado();

        foreach ($tempo_adicional_expirado as $agendamento) {
            try {
                $this->cancelar_agendamento_expirado($agendamento);
                $resultado['agendamentos_cancelados']++;

                log_message('info', "CRON: Agendamento #{$agendamento->id} cancelado por falta de pagamento");

            } catch (Exception $e) {
                $resultado['erros'][] = "Cancelamento #{$agendamento->id}: " . $e->getMessage();
                log_message('error', "CRON: Erro ao cancelar #{$agendamento->id}: " . $e->getMessage());
            }
        }

        // Registrar log do cron
        $this->registrar_log(
            'verificar_pagamentos',
            $resultado['lembretes_enviados'] + $resultado['agendamentos_cancelados'],
            json_encode($resultado)
        );

        log_message('info', 'CRON: Verificação concluída - ' . json_encode($resultado));

        // Retornar resultado (útil para debug)
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'resultado' => $resultado
        ]);
    }

    /**
     * Cancelar agendamento por falta de pagamento
     *
     * @param object $agendamento Dados do agendamento
     */
    private function cancelar_agendamento_expirado($agendamento) {
        // Atualizar status do agendamento
        $this->Agendamento_model->update($agendamento->id, [
            'status' => 'cancelado',
            'pagamento_status' => 'expirado',
            'cancelado_por' => 'sistema',
            'motivo_cancelamento' => 'Pagamento não realizado dentro do prazo'
        ]);

        // Enviar notificação de cancelamento apenas para cliente (não para profissional)
        $this->Agendamento_model->enviar_notificacao_whatsapp($agendamento->id, 'cancelamento', [
            'motivo' => 'Pagamento não realizado dentro do prazo'
        ]);

        // Não notificar profissional em cancelamentos automáticos por falta de pagamento
    }

    /**
     * Verificar status de pagamentos no Mercado Pago
     *
     * Este cron consulta o MP para pagamentos pendentes
     * Útil caso o webhook não tenha funcionado
     *
     * URL: /cron/verificar_status_mp?token=SEU_TOKEN
     */
    public function verificar_status_mp() {
        // Verificar token de segurança
        if (!$this->verificar_token()) {
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando verificação de status no Mercado Pago');

        $this->load->model('Pagamento_model');
        $this->load->model('Estabelecimento_model');
        $this->load->library('mercadopago_lib');

        $resultado = [
            'verificados' => 0,
            'confirmados' => 0,
            'erros' => []
        ];

        // Buscar pagamentos pendentes (últimas 2 horas)
        $pagamentos_pendentes = $this->Pagamento_model->get_pendentes(null, 120);

        foreach ($pagamentos_pendentes as $pagamento) {
            try {
                if (!$pagamento->mercadopago_id || !$pagamento->agendamento_id) {
                    continue;
                }

                // Buscar estabelecimento para credenciais
                $agendamento = $this->Agendamento_model->get($pagamento->agendamento_id);
                if (!$agendamento) continue;

                $estabelecimento = $this->Estabelecimento_model->get($agendamento->estabelecimento_id);
                if (!$estabelecimento) continue;

                // Configurar credenciais
                $access_token = $estabelecimento->mp_sandbox
                    ? $estabelecimento->mp_access_token_test
                    : $estabelecimento->mp_access_token_prod;

                $this->mercadopago_lib->set_credentials($access_token, '');

                // Consultar status no MP
                $mp_payment = $this->mercadopago_lib->get_pagamento($pagamento->mercadopago_id);

                $resultado['verificados']++;

                if ($mp_payment && isset($mp_payment['response'])) {
                    $mp_status = $mp_payment['response']['status'];

                    if ($mp_status === 'approved') {
                        // Confirmar pagamento
                        $this->Pagamento_model->confirmar_agendamento($pagamento->agendamento_id);

                        // Enviar notificação apenas para cliente
                        $this->Agendamento_model->enviar_notificacao_whatsapp($pagamento->agendamento_id, 'confirmacao');
                        // Não notificar profissional aqui - já foi notificado na criação do agendamento

                        $resultado['confirmados']++;

                        log_message('info', "CRON MP: Pagamento #{$pagamento->id} confirmado");
                    }
                }

            } catch (Exception $e) {
                $resultado['erros'][] = "Pagamento #{$pagamento->id}: " . $e->getMessage();
            }
        }

        // Registrar log
        $this->registrar_log('verificar_status_mp', $resultado['verificados'], json_encode($resultado));

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'resultado' => $resultado
        ]);
    }

    /**
     * Limpar logs antigos do cron
     *
     * URL: /cron/limpar_logs?token=SEU_TOKEN&dias=30
     */
    public function limpar_logs() {
        if (!$this->verificar_token()) {
            show_404();
            return;
        }

        $dias = $this->input->get('dias') ?? 30;

        $this->db->where('executado_em <', date('Y-m-d H:i:s', strtotime("-{$dias} days")));
        $deletados = $this->db->delete('cron_logs');

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'logs_deletados' => $this->db->affected_rows()
        ]);
    }

    /**
     * Limpar conversas antigas do bot (mais de 24 horas)
     *
     * URL: /cron/limpar_conversas_bot?token=SEU_TOKEN
     */
    public function limpar_conversas_bot() {
        if (!$this->verificar_token()) {
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando limpeza de conversas antigas do bot');

        $removidos = $this->Bot_conversa_model->limpar_antigas();

        $this->registrar_log('limpar_conversas_bot', $removidos);

        log_message('info', "CRON: {$removidos} conversas antigas removidas");

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'conversas_removidas' => $removidos
        ]);
    }

    /**
     * Enviar pedidos de confirmação para agendamentos pendentes
     *
     * URL: /cron/enviar_confirmacoes?token=TOKEN
     * Frequência: A cada 1 hora
     */
    public function enviar_confirmacoes() {
        if (!$this->verificar_token()) {
            log_message('error', 'CRON: Tentativa de acesso sem token válido - enviar_confirmacoes');
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando envio de confirmações');
        log_message('info', 'CRON: Data/Hora atual: ' . date('Y-m-d H:i:s'));
        log_message('info', 'CRON: Data de amanhã: ' . date('Y-m-d', strtotime('+1 day')));

        $resultado = [
            'confirmacoes_enviadas' => 0,
            'erros' => []
        ];

        // Buscar agendamentos que precisam de confirmação
        $agendamentos = $this->Agendamento_model->get_pendentes_confirmacao();

        log_message('info', "CRON: Encontrados " . count($agendamentos) . " agendamentos para confirmar");

        foreach ($agendamentos as $agendamento) {
            try {
                // Incrementar contador de tentativas
                $nova_tentativa = $agendamento->confirmacao_tentativas + 1;
                $max_tentativas = $agendamento->confirmacao_max_tentativas ?? 3;

                // Determinar tipo de mensagem baseado na tentativa
                $tipo_mensagem = 'padrao';
                if ($nova_tentativa == 2) {
                    $tipo_mensagem = 'urgente';
                } elseif ($nova_tentativa >= $max_tentativas) {
                    $tipo_mensagem = 'ultima_chance';
                }

                // Enviar mensagem de confirmação via WhatsApp
                $this->enviar_mensagem_confirmacao($agendamento, $tipo_mensagem);

                // Atualizar tentativas e timestamp
                $this->Agendamento_model->update($agendamento->id, [
                    'confirmacao_tentativas' => $nova_tentativa,
                    'confirmacao_ultima_tentativa' => date('Y-m-d H:i:s'),
                    'confirmacao_enviada' => 1,
                    'confirmacao_enviada_em' => date('Y-m-d H:i:s')
                ]);

                $resultado['confirmacoes_enviadas']++;

                log_message('info', "CRON: Confirmação enviada para agendamento #{$agendamento->id} - Tentativa {$nova_tentativa}/{$max_tentativas} - Tipo: {$tipo_mensagem}");

            } catch (Exception $e) {
                $resultado['erros'][] = "Agendamento #{$agendamento->id}: " . $e->getMessage();
                log_message('error', "CRON: Erro ao enviar confirmação #{$agendamento->id}: " . $e->getMessage());
            }
        }

        // Registrar log
        $this->registrar_log('enviar_confirmacoes', $resultado['confirmacoes_enviadas'], json_encode($resultado));

        log_message('info', 'CRON: Confirmações concluídas - ' . json_encode($resultado));

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'resultado' => $resultado
        ]);
    }

    /**
     * Enviar lembretes pré-atendimento
     *
     * URL: /cron/enviar_lembretes?token=TOKEN
     * Frequência: A cada 15 minutos
     */
    public function enviar_lembretes() {
        if (!$this->verificar_token()) {
            log_message('error', 'CRON: Tentativa de acesso sem token válido - enviar_lembretes');
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando envio de lembretes');

        $resultado = [
            'lembretes_enviados' => 0,
            'erros' => []
        ];

        // Buscar agendamentos confirmados que precisam de lembrete
        $agendamentos = $this->Agendamento_model->get_para_lembrete();

        log_message('info', "CRON: Encontrados " . count($agendamentos) . " agendamentos para lembrete");

        foreach ($agendamentos as $agendamento) {
            try {
                // Enviar lembrete via WhatsApp
                $this->enviar_mensagem_lembrete($agendamento);

                // Atualizar flags
                $this->Agendamento_model->update($agendamento->id, [
                    'lembrete_enviado' => 1,
                    'lembrete_enviado_em' => date('Y-m-d H:i:s')
                ]);

                $resultado['lembretes_enviados']++;

                log_message('info', "CRON: Lembrete enviado para agendamento #{$agendamento->id}");

            } catch (Exception $e) {
                $resultado['erros'][] = "Agendamento #{$agendamento->id}: " . $e->getMessage();
                log_message('error', "CRON: Erro ao enviar lembrete #{$agendamento->id}: " . $e->getMessage());
            }
        }

        // Registrar log
        $this->registrar_log('enviar_lembretes', $resultado['lembretes_enviados'], json_encode($resultado));

        log_message('info', 'CRON: Lembretes concluídos - ' . json_encode($resultado));

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'resultado' => $resultado
        ]);
    }

    /**
     * Cancelar agendamentos não confirmados (OPCIONAL)
     *
     * URL: /cron/cancelar_nao_confirmados?token=TOKEN
     * Frequência: A cada 1 hora
     */
    public function cancelar_nao_confirmados() {
        if (!$this->verificar_token()) {
            log_message('error', 'CRON: Tentativa de acesso sem token válido - cancelar_nao_confirmados');
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando cancelamento de não confirmados');

        $resultado = [
            'cancelados' => 0,
            'erros' => []
        ];

        // Buscar agendamentos pendentes que expiraram
        $agendamentos = $this->Agendamento_model->get_nao_confirmados_expirados();

        log_message('info', "CRON: Encontrados " . count($agendamentos) . " agendamentos para cancelar");

        foreach ($agendamentos as $agendamento) {
            try {
                // Cancelar agendamento
                $this->Agendamento_model->update($agendamento->id, [
                    'status' => 'cancelado',
                    'cancelado_por' => 'sistema',
                    'motivo_cancelamento' => 'Não confirmado pelo cliente'
                ]);

                // Enviar notificação de cancelamento
                $this->enviar_notificacao_cancelamento_automatico($agendamento);

                $resultado['cancelados']++;

                log_message('info', "CRON: Agendamento #{$agendamento->id} cancelado por falta de confirmação");

            } catch (Exception $e) {
                $resultado['erros'][] = "Agendamento #{$agendamento->id}: " . $e->getMessage();
                log_message('error', "CRON: Erro ao cancelar #{$agendamento->id}: " . $e->getMessage());
            }
        }

        // Registrar log
        $this->registrar_log('cancelar_nao_confirmados', $resultado['cancelados'], json_encode($resultado));

        log_message('info', 'CRON: Cancelamentos concluídos - ' . json_encode($resultado));

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'resultado' => $resultado
        ]);
    }

    /**
     * Enviar mensagem de confirmação via WhatsApp
     *
     * @param object $agendamento Dados do agendamento
     * @param string $tipo_mensagem Tipo: 'padrao', 'urgente', 'ultima_chance'
     */
    private function enviar_mensagem_confirmacao($agendamento, $tipo_mensagem = 'padrao') {
        $this->load->library('waha_lib');
        $this->load->model('Estabelecimento_model');

        // Buscar estabelecimento completo
        $estabelecimento = $this->Estabelecimento_model->get($agendamento->estabelecimento_id);

        if (!$estabelecimento || !$estabelecimento->waha_ativo) {
            throw new Exception("Estabelecimento sem WAHA ativo");
        }

        // Configurar WAHA
        $this->waha_lib->set_credentials(
            $estabelecimento->waha_api_url,
            $estabelecimento->waha_api_key,
            $estabelecimento->waha_session_name
        );

        // Formatar dados
        $data_formatada = date('d/m/Y (l)', strtotime($agendamento->data));
        $data_formatada = str_replace(
            ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
            $data_formatada
        );

        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));

        // Saudação contextual
        $hora_atual = (int)date('H');
        if ($hora_atual >= 6 && $hora_atual < 12) {
            $saudacao = 'Bom dia';
        } elseif ($hora_atual >= 12 && $hora_atual < 18) {
            $saudacao = 'Boa tarde';
        } else {
            $saudacao = 'Boa noite';
        }

        $primeiro_nome = explode(' ', $agendamento->cliente_nome)[0];

        // Montar mensagem baseada no tipo
        if ($tipo_mensagem == 'urgente') {
            // Segunda tentativa - mais direto
            $mensagem = "{$saudacao}, {$primeiro_nome}! 👋\n\n";
            $mensagem .= "⚠️ *CONFIRMAÇÃO PENDENTE*\n\n";
            $mensagem .= "Ainda não recebemos sua confirmação para:\n\n";
            $mensagem .= "📆 Data: *{$data_formatada}*\n";
            $mensagem .= "🕐 Horário: *{$hora_formatada}*\n";
            $mensagem .= "💈 Serviço: *{$agendamento->servico_nome}*\n";
            $mensagem .= "👤 Profissional: *{$agendamento->profissional_nome}*\n\n";
            $mensagem .= "Por favor, responda agora:\n\n";
            $mensagem .= "1️⃣ *Confirmar* ✅\n";
            $mensagem .= "2️⃣ *Reagendar* 🔄\n";
            $mensagem .= "3️⃣ *Cancelar* ❌\n\n";
            $mensagem .= "Aguardamos sua resposta! 😊";
        } elseif ($tipo_mensagem == 'ultima_chance') {
            // Terceira tentativa - aviso de cancelamento
            $intervalo = $agendamento->confirmacao_intervalo_tentativas_minutos ?? 30;
            $mensagem = "{$saudacao}, {$primeiro_nome}! 👋\n\n";
            $mensagem .= "🚨 *ÚLTIMA CHANCE - AGENDAMENTO SERÁ CANCELADO*\n\n";
            $mensagem .= "Seu agendamento será *CANCELADO AUTOMATICAMENTE* em *{$intervalo} minutos* se não confirmar:\n\n";
            $mensagem .= "📆 Data: *{$data_formatada}*\n";
            $mensagem .= "🕐 Horário: *{$hora_formatada}*\n";
            $mensagem .= "💈 Serviço: *{$agendamento->servico_nome}*\n";
            $mensagem .= "👤 Profissional: *{$agendamento->profissional_nome}*\n\n";
            $mensagem .= "⏰ *RESPONDA AGORA:*\n\n";
            $mensagem .= "1️⃣ *Confirmar* ✅\n";
            $mensagem .= "2️⃣ *Reagendar* 🔄\n";
            $mensagem .= "3️⃣ *Cancelar* ❌";
        } else {
            // Primeira tentativa - padrão
            $mensagem = "{$saudacao}, {$primeiro_nome}! 👋\n\n";
            $mensagem .= "📅 *Confirmação de Agendamento*\n\n";
            $mensagem .= "Você tem um agendamento marcado:\n";
            $mensagem .= "📆 Data: *{$data_formatada}*\n";
            $mensagem .= "🕐 Horário: *{$hora_formatada}*\n";
            $mensagem .= "💈 Serviço: *{$agendamento->servico_nome}*\n";
            $mensagem .= "👤 Profissional: *{$agendamento->profissional_nome}*\n";
            $mensagem .= "📍 Local: *{$agendamento->estabelecimento_nome}*\n\n";
            $mensagem .= "Por favor, confirme sua presença:\n\n";
            $mensagem .= "1️⃣ *Confirmar* - Estarei presente ✅\n";
            $mensagem .= "2️⃣ *Reagendar* - Preciso mudar 🔄\n";
            $mensagem .= "3️⃣ *Cancelar* - Não poderei ir ❌\n\n";
            $mensagem .= "Aguardamos sua resposta! 😊";
        }

        // Usar número completo do WhatsApp (preservar @c.us ou @lid)
        $numero = $agendamento->cliente_whatsapp;

        // Log detalhado antes de enviar
        log_message('debug', "CRON Confirmacao: Tentando enviar para {$numero}");
        log_message('debug', "CRON Confirmacao: WAHA URL: {$estabelecimento->waha_api_url}");
        log_message('debug', "CRON Confirmacao: Session: {$estabelecimento->waha_session_name}");

        // Enviar mensagem
        try {
            $resultado = $this->waha_lib->enviar_texto($numero, $mensagem);
            log_message('debug', "CRON Confirmacao: Resultado WAHA: " . json_encode($resultado));
        } catch (Exception $e) {
            log_message('error', "CRON Confirmacao: Erro ao enviar via WAHA: " . $e->getMessage());
            throw $e;
        }

        // Criar conversa no bot para processar resposta
        $this->load->model('Bot_conversa_model');
        $conversa = $this->Bot_conversa_model->get_ou_criar(
            $agendamento->estabelecimento_id,
            $numero
        );

        // SEMPRE atualizar estado para confirmando_agendamento
        // Isso garante que mesmo se houver um estado anterior (ex: reagendando_hora de teste anterior)
        // o cliente possa responder à confirmação
        log_message('info', "CRON Confirmacao: Atualizando estado para confirmando_agendamento - agendamento_id={$agendamento->id}");

        $this->Bot_conversa_model->atualizar_estado(
            $conversa->id,
            'confirmando_agendamento',
            ['agendamento_id' => $agendamento->id]
        );
    }

    /**
     * Enviar mensagem de lembrete via WhatsApp
     */
    private function enviar_mensagem_lembrete($agendamento) {
        $this->load->library('waha_lib');
        $this->load->model('Estabelecimento_model');

        // Buscar estabelecimento completo
        $estabelecimento = $this->Estabelecimento_model->get($agendamento->estabelecimento_id);

        if (!$estabelecimento || !$estabelecimento->waha_ativo) {
            throw new Exception("Estabelecimento sem WAHA ativo");
        }

        // Configurar WAHA
        $this->waha_lib->set_credentials(
            $estabelecimento->waha_api_url,
            $estabelecimento->waha_api_key,
            $estabelecimento->waha_session_name
        );

        // Formatar dados
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));
        $minutos_faltando = round((strtotime($agendamento->data . ' ' . $agendamento->hora_inicio) - time()) / 60);

        // Saudação contextual
        $hora_atual = (int)date('H');
        if ($hora_atual >= 6 && $hora_atual < 12) {
            $saudacao = 'Bom dia';
        } elseif ($hora_atual >= 12 && $hora_atual < 18) {
            $saudacao = 'Boa tarde';
        } else {
            $saudacao = 'Boa noite';
        }

        $primeiro_nome = explode(' ', $agendamento->cliente_nome)[0];

        // Montar mensagem
        $mensagem = "{$saudacao}, {$primeiro_nome}! ⏰\n\n";
        $mensagem .= "🔔 *Lembrete de Agendamento*\n\n";
        $mensagem .= "Seu atendimento está chegando!\n";
        $mensagem .= "⏱️ Faltam aproximadamente *{$minutos_faltando} minutos*\n\n";
        $mensagem .= "🕐 Horário: *{$hora_formatada}*\n";
        $mensagem .= "💈 Serviço: *{$agendamento->servico_nome}*\n";
        $mensagem .= "👤 Profissional: *{$agendamento->profissional_nome}*\n";
        $mensagem .= "📍 Local: *{$agendamento->estabelecimento_nome}*\n";

        if ($agendamento->estabelecimento_endereco) {
            $mensagem .= "📌 {$agendamento->estabelecimento_endereco}\n";
        }

        $antecedencia = $agendamento->lembrete_antecedencia_chegada ?? 10;
        $mensagem .= "\n💡 Por favor, chegue com *{$antecedencia} minutos de antecedência*.\n\n";
        $mensagem .= "Até logo! 👋";

        // Usar número completo do WhatsApp (preservar @c.us ou @lid)
        $numero = $agendamento->cliente_whatsapp;

        // Log detalhado antes de enviar
        log_message('debug', "CRON Lembrete: Tentando enviar para {$numero}");
        log_message('debug', "CRON Lembrete: WAHA URL: {$estabelecimento->waha_api_url}");
        log_message('debug', "CRON Lembrete: Session: {$estabelecimento->waha_session_name}");
        log_message('debug', "CRON Lembrete: Mensagem: " . substr($mensagem, 0, 100) . "...");

        // Enviar mensagem
        try {
            $resultado = $this->waha_lib->enviar_texto($numero, $mensagem);
            log_message('debug', "CRON Lembrete: Resultado WAHA: " . json_encode($resultado));
        } catch (Exception $e) {
            log_message('error', "CRON Lembrete: Erro ao enviar via WAHA: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enviar notificação de cancelamento automático
     */
    private function enviar_notificacao_cancelamento_automatico($agendamento) {
        $this->load->library('waha_lib');
        $this->load->model('Estabelecimento_model');

        // Buscar estabelecimento completo
        $estabelecimento = $this->Estabelecimento_model->get($agendamento->estabelecimento_id);

        if (!$estabelecimento || !$estabelecimento->waha_ativo) {
            return; // Não lançar exceção, apenas não enviar
        }

        // Configurar WAHA
        $this->waha_lib->set_credentials(
            $estabelecimento->waha_api_url,
            $estabelecimento->waha_api_key,
            $estabelecimento->waha_session_name
        );

        // Formatar dados
        $data_formatada = date('d/m/Y', strtotime($agendamento->data));
        $hora_formatada = date('H:i', strtotime($agendamento->hora_inicio));

        $primeiro_nome = explode(' ', $agendamento->cliente_nome)[0];

        // Montar mensagem
        $mensagem = "Olá, {$primeiro_nome}! 😔\n\n";
        $mensagem .= "⚠️ *Agendamento Cancelado Automaticamente*\n\n";
        $mensagem .= "Seu agendamento foi cancelado por falta de confirmação:\n\n";
        $mensagem .= "📅 Data: {$data_formatada}\n";
        $mensagem .= "⏰ Horário: {$hora_formatada}\n";
        $mensagem .= "💈 Serviço: {$agendamento->servico_nome}\n\n";
        $mensagem .= "Se ainda tiver interesse, entre em contato para reagendar.\n\n";
        $mensagem .= "Digite *menu* para fazer um novo agendamento.";

        // Usar número completo do WhatsApp (preservar @c.us ou @lid)
        $numero = $agendamento->cliente_whatsapp;

        // Log detalhado antes de enviar
        log_message('debug', "CRON Cancelamento: Tentando enviar para {$numero}");
        log_message('debug', "CRON Cancelamento: WAHA URL: {$estabelecimento->waha_api_url}");
        log_message('debug', "CRON Cancelamento: Session: {$estabelecimento->waha_session_name}");

        // Enviar mensagem
        try {
            $resultado = $this->waha_lib->enviar_texto($numero, $mensagem);
            log_message('debug', "CRON Cancelamento: Resultado WAHA: " . json_encode($resultado));
        } catch (Exception $e) {
            log_message('error', "CRON Cancelamento: Erro ao enviar via WAHA: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Página de teste de cron jobs
     */
    public function test() {
        if (!$this->verificar_token()) {
            show_404();
            return;
        }

        $config = $this->Configuracao_model->get_by_chave('cron_token');
        $data['token'] = $config->valor;

        $this->load->view('painel/cron_test', $data);
    }

    /**
     * Debug: Listar agendamentos pendentes
     */
    public function debug_agendamentos_pendentes() {
        if (!$this->verificar_token()) {
            show_404();
            return;
        }

        $agendamentos = $this->Agendamento_model->get_pendentes_confirmacao();

        header('Content-Type: application/json');
        echo json_encode([
            'total' => count($agendamentos),
            'agendamentos' => $agendamentos
        ]);
    }

    /**
     * Debug: Listar agendamentos confirmados que precisam de lembrete
     */
    public function debug_agendamentos_confirmados() {
        if (!$this->verificar_token()) {
            show_404();
            return;
        }

        $agendamentos = $this->Agendamento_model->get_para_lembrete();

        header('Content-Type: application/json');
        echo json_encode([
            'total' => count($agendamentos),
            'agendamentos' => $agendamentos
        ]);
    }

    /**
     * Enviar resumos diários da agenda para profissionais
     *
     * Este cron deve ser executado a cada 15 minutos
     * URL: /cron/enviar_resumos_diarios?token=SEU_TOKEN
     *
     * Verifica horários configurados e envia resumo da agenda
     */
    public function enviar_resumos_diarios() {
        // Verificar token de segurança
        if (!$this->verificar_token()) {
            log_message('error', 'CRON: Tentativa de acesso sem token válido');
            show_404();
            return;
        }

        log_message('info', 'CRON: Iniciando envio de resumos diários');

        $resultado = [
            'resumos_enviados' => 0,
            'erros' => []
        ];

        // Buscar todos os estabelecimentos com notificação ativa
        $this->load->model('Estabelecimento_model');
        $this->load->model('Horario_estabelecimento_model');

        $this->db->select('id, nome, notif_prof_resumo_diario, notif_prof_resumo_manha, notif_prof_resumo_tarde');
        $this->db->where('notif_prof_resumo_diario', 1);
        $estabelecimentos = $this->db->get('estabelecimentos')->result();

        $hora_atual = date('H:i');
        $dia_semana = date('w'); // 0 = domingo, 6 = sábado

        foreach ($estabelecimentos as $estabelecimento) {
            try {
                // Buscar horário de funcionamento do dia
                $horario = $this->Horario_estabelecimento_model->get_by_dia($estabelecimento->id, $dia_semana);

                if (!$horario || !$horario->ativo) {
                    continue; // Estabelecimento fechado hoje
                }

                // Verificar se deve enviar resumo da manhã
                if (!empty($estabelecimento->notif_prof_resumo_manha)) {
                    $horario_manha = substr($estabelecimento->notif_prof_resumo_manha, 0, 5);

                    // Enviar se estiver dentro de 5 minutos do horário configurado
                    if ($this->horario_proximo($hora_atual, $horario_manha, 5)) {
                        $resultado_envio = $this->notificacao_whatsapp_lib->enviar_resumo_diario($estabelecimento->id, 'manha');

                        if ($resultado_envio['success']) {
                            $resultado['resumos_enviados']++;
                            log_message('info', "CRON: Resumo manhã enviado para estabelecimento #{$estabelecimento->id}");
                        } else {
                            log_message('warning', "CRON: Falha ao enviar resumo manhã para #{$estabelecimento->id}: {$resultado_envio['error']}");
                        }
                    }
                }

                // Verificar se deve enviar resumo da tarde
                if (!empty($estabelecimento->notif_prof_resumo_tarde)) {
                    $horario_tarde = substr($estabelecimento->notif_prof_resumo_tarde, 0, 5);

                    // Enviar se estiver dentro de 5 minutos do horário configurado
                    if ($this->horario_proximo($hora_atual, $horario_tarde, 5)) {
                        $resultado_envio = $this->notificacao_whatsapp_lib->enviar_resumo_diario($estabelecimento->id, 'tarde');

                        if ($resultado_envio['success']) {
                            $resultado['resumos_enviados']++;
                            log_message('info', "CRON: Resumo tarde enviado para estabelecimento #{$estabelecimento->id}");
                        } else {
                            log_message('warning', "CRON: Falha ao enviar resumo tarde para #{$estabelecimento->id}: {$resultado_envio['error']}");
                        }
                    }
                }

            } catch (Exception $e) {
                $resultado['erros'][] = "Estabelecimento #{$estabelecimento->id}: " . $e->getMessage();
                log_message('error', "CRON: Erro no estabelecimento #{$estabelecimento->id}: " . $e->getMessage());
            }
        }

        // Registrar log do cron
        $this->registrar_log(
            'enviar_resumos_diarios',
            $resultado['resumos_enviados'],
            json_encode($resultado)
        );

        log_message('info', 'CRON: Envio de resumos concluído - ' . json_encode($resultado));

        // Retornar resultado
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'resultado' => $resultado
        ]);
    }

    /**
     * Verificar se horário atual está próximo do horário alvo
     *
     * @param string $hora_atual Hora atual (H:i)
     * @param string $hora_alvo Hora alvo (H:i)
     * @param int $tolerancia_minutos Tolerância em minutos
     * @return bool
     */
    private function horario_proximo($hora_atual, $hora_alvo, $tolerancia_minutos = 5) {
        $timestamp_atual = strtotime($hora_atual);
        $timestamp_alvo = strtotime($hora_alvo);

        $diferenca_minutos = abs($timestamp_atual - $timestamp_alvo) / 60;

        return $diferenca_minutos <= $tolerancia_minutos;
    }
}
