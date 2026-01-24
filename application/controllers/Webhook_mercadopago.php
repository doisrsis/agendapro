<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Webhook_mercadopago
 *
 * Processar webhooks do Mercado Pago para pagamentos de agendamentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 27/12/2024
 */
class Webhook_mercadopago extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Pagamento_model');
        $this->load->model('Agendamento_model');
        $this->load->library('mercadopago_lib');
    }

    /**
     * Receber notificação de pagamento de agendamento
     *
     * @param int $estabelecimento_id ID do estabelecimento
     */
    public function agendamento($estabelecimento_id) {
        // Receber dados do webhook
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Log para debug
        log_message('debug', 'Webhook Agendamento: ' . $json);

        // Processar apenas eventos de payment
        if (!isset($data['type']) || $data['type'] !== 'payment') {
            http_response_code(200);
            echo json_encode(['message' => 'Event type not payment']);
            return;
        }

        // Obter ID do pagamento
        $payment_id = $data['data']['id'] ?? null;

        if (!$payment_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Payment ID not found']);
            return;
        }

        // Buscar dados completos do pagamento no Mercado Pago
        $payment_data = $this->mercadopago_lib->get_pagamento($payment_id);

        if (!$payment_data || !isset($payment_data['data'])) {
            log_message('error', 'Erro ao buscar pagamento no MP: ' . $payment_id);
            http_response_code(400);
            echo json_encode(['error' => 'Payment not found in MercadoPago']);
            return;
        }

        $payment = $payment_data['data'];
        $status = $payment['status'];
        $status_detail = $payment['status_detail'] ?? null;

        // Buscar pagamento local
        $pagamento_local = $this->Pagamento_model->get_by_mercadopago_id($payment_id);

        if (!$pagamento_local) {
            log_message('error', 'Pagamento não encontrado localmente: ' . $payment_id);
            http_response_code(404);
            echo json_encode(['error' => 'Payment not found locally']);
            return;
        }

        // Atualizar status do pagamento
        $this->Pagamento_model->atualizar_status(
            $pagamento_local->id,
            $status,
            $status_detail,
            $payment
        );

        // Se aprovado, confirmar agendamento
        if ($status === 'approved' && $pagamento_local->agendamento_id) {
            $this->Pagamento_model->confirmar_agendamento($pagamento_local->agendamento_id);

            log_message('info', "Agendamento #{$pagamento_local->agendamento_id} confirmado via pagamento");

            // Enviar notificação WhatsApp de confirmação para cliente
            $this->Agendamento_model->enviar_notificacao_whatsapp($pagamento_local->agendamento_id, 'confirmacao');

            // Não notificar profissional aqui - já foi notificado na criação do agendamento
        }

        // Responder sucesso
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'payment_id' => $payment_id,
            'status' => $status
        ]);
    }

    /**
     * Webhook genérico (assinaturas e outros)
     */
    public function index() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        log_message('debug', 'Webhook Genérico MP: ' . $json);

        // Processar conforme tipo
        $type = $data['type'] ?? null;

        if ($type === 'payment') {
            // Processar pagamento de assinatura
            $this->processar_assinatura($data);
        }

        http_response_code(200);
        echo json_encode(['success' => true]);
    }

    /**
     * Processar pagamento de assinatura
     *
     * @param array $data Dados do webhook
     */
    private function processar_assinatura($data) {
        $payment_id = $data['data']['id'] ?? null;

        if (!$payment_id) {
            return;
        }

        // Buscar dados do pagamento
        $payment_data = $this->mercadopago_lib->get_pagamento($payment_id);

        if (!$payment_data || !isset($payment_data['data'])) {
            return;
        }

        $payment = $payment_data['data'];
        $status = $payment['status'];

        // Buscar pagamento local
        $pagamento_local = $this->Pagamento_model->get_by_mercadopago_id($payment_id);

        if (!$pagamento_local) {
            return;
        }

        // Atualizar status
        $this->Pagamento_model->atualizar_status(
            $pagamento_local->id,
            $status,
            $payment['status_detail'] ?? null,
            $payment
        );

        // Se aprovado e tem assinatura, ativar
        if ($status === 'approved' && $pagamento_local->assinatura_id) {
            $this->load->model('Assinatura_model');
            $this->Assinatura_model->ativar($pagamento_local->assinatura_id);

            log_message('info', "Assinatura #{$pagamento_local->assinatura_id} ativada via pagamento");
        }
    }
}
