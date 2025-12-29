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

        // Enviar notificação de cancelamento
        $this->Agendamento_model->enviar_notificacao_whatsapp($agendamento->id, 'cancelamento', [
            'motivo' => 'Pagamento não realizado dentro do prazo'
        ]);
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

                        // Enviar notificações
                        $this->Agendamento_model->enviar_notificacao_whatsapp($pagamento->agendamento_id, 'confirmacao');
                        $this->Agendamento_model->enviar_notificacao_whatsapp($pagamento->agendamento_id, 'profissional_novo');

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
}
