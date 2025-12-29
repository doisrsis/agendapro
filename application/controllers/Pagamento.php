<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Pagamento
 *
 * Página pública de pagamento PIX
 * Não requer autenticação - acesso via token único
 *
 * @author Rafael Dias - doisr.com.br
 * @date 28/12/2024
 */
class Pagamento extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Pagamento_model');
        $this->load->model('Estabelecimento_model');
    }

    /**
     * Página de pagamento via token
     *
     * URL: /pagamento/{token}
     *
     * @param string $token Token único do pagamento
     */
    public function index($token = null) {
        if (empty($token)) {
            show_404();
            return;
        }

        // Buscar agendamento pelo token
        $agendamento = $this->Agendamento_model->get_by_pagamento_token($token);

        if (!$agendamento) {
            $this->load->view('pagamento/erro', [
                'titulo' => 'Link Inválido',
                'mensagem' => 'Este link de pagamento não é válido ou já expirou.'
            ]);
            return;
        }

        // Verificar status do pagamento
        if ($agendamento->pagamento_status === 'pago') {
            $this->load->view('pagamento/sucesso', [
                'agendamento' => $agendamento
            ]);
            return;
        }

        if ($agendamento->pagamento_status === 'expirado' || $agendamento->status === 'cancelado') {
            $this->load->view('pagamento/erro', [
                'titulo' => 'Pagamento Expirado',
                'mensagem' => 'O prazo para pagamento deste agendamento já expirou.',
                'agendamento' => $agendamento
            ]);
            return;
        }

        // Determinar qual expiração usar (adicional ou original)
        $expira_em = $agendamento->pagamento_expira_adicional_em ?? $agendamento->pagamento_expira_em;

        // Verificar se ainda está no prazo
        if ($expira_em && strtotime($expira_em) < time()) {
            $this->load->view('pagamento/erro', [
                'titulo' => 'Tempo Esgotado',
                'mensagem' => 'O prazo para pagamento deste agendamento já expirou.',
                'agendamento' => $agendamento
            ]);
            return;
        }

        // Exibir página de pagamento
        $data = [
            'titulo' => 'Pagamento - ' . $agendamento->estabelecimento_nome,
            'agendamento' => $agendamento,
            'token' => $token,
            'expira_em' => $expira_em
        ];

        $this->load->view('pagamento/index', $data);
    }

    /**
     * Verificar status do pagamento (AJAX)
     *
     * URL: /pagamento/verificar/{token}
     *
     * @param string $token Token único do pagamento
     */
    public function verificar($token = null) {
        header('Content-Type: application/json');

        if (empty($token)) {
            echo json_encode(['error' => 'Token inválido']);
            return;
        }

        $agendamento = $this->Agendamento_model->get_by_pagamento_token($token);

        if (!$agendamento) {
            echo json_encode(['error' => 'Agendamento não encontrado']);
            return;
        }

        // Se já está pago, retornar sucesso
        if ($agendamento->pagamento_status === 'pago') {
            echo json_encode([
                'status' => 'pago',
                'mensagem' => 'Pagamento confirmado!'
            ]);
            return;
        }

        // Se expirou ou cancelado
        if ($agendamento->pagamento_status === 'expirado' || $agendamento->status === 'cancelado') {
            echo json_encode([
                'status' => 'expirado',
                'mensagem' => 'Pagamento expirado'
            ]);
            return;
        }

        // Verificar expiração
        $expira_em = $agendamento->pagamento_expira_adicional_em ?? $agendamento->pagamento_expira_em;
        if ($expira_em && strtotime($expira_em) < time()) {
            echo json_encode([
                'status' => 'expirado',
                'mensagem' => 'Tempo esgotado'
            ]);
            return;
        }

        // Consultar status no Mercado Pago
        $pagamento = $this->Pagamento_model->get_by_agendamento($agendamento->id);

        if ($pagamento && $pagamento->mercadopago_id) {
            $this->load->library('mercadopago_lib');

            $estabelecimento = $this->Estabelecimento_model->get($agendamento->estabelecimento_id);

            $access_token = $estabelecimento->mp_sandbox
                ? $estabelecimento->mp_access_token_test
                : $estabelecimento->mp_access_token_prod;

            $this->mercadopago_lib->set_credentials($access_token, '');

            $mp_payment = $this->mercadopago_lib->get_pagamento($pagamento->mercadopago_id);

            if ($mp_payment && isset($mp_payment['response'])) {
                $mp_status = $mp_payment['response']['status'];

                if ($mp_status === 'approved') {
                    // Confirmar pagamento
                    $this->Pagamento_model->confirmar_agendamento($agendamento->id);

                    log_message('debug', 'Pagamento: Enviando notificações WhatsApp para agendamento #' . $agendamento->id);

                    // Enviar notificações
                    $resultado_cliente = $this->Agendamento_model->enviar_notificacao_whatsapp($agendamento->id, 'confirmacao');
                    log_message('debug', 'Pagamento: Resultado notificação cliente = ' . ($resultado_cliente ? 'OK' : 'FALHOU'));

                    $resultado_prof = $this->Agendamento_model->enviar_notificacao_whatsapp($agendamento->id, 'profissional_novo');
                    log_message('debug', 'Pagamento: Resultado notificação profissional = ' . ($resultado_prof ? 'OK' : 'FALHOU'));

                    echo json_encode([
                        'status' => 'pago',
                        'mensagem' => 'Pagamento confirmado!'
                    ]);
                    return;
                }
            }
        }

        // Ainda pendente
        echo json_encode([
            'status' => 'pendente',
            'valor' => $agendamento->pagamento_valor
        ]);
    }
}
