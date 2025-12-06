<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library Mercado Pago
 *
 * Integração com API do Mercado Pago para processar pagamentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Mercadopago_lib {

    private $CI;
    private $access_token;
    private $public_key;
    private $sandbox = true;
    private $api_url = 'https://api.mercadopago.com';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('Configuracao_model');

        // Carregar credenciais do banco de dados
        $this->load_credentials();
    }

    /**
     * Carregar credenciais do Mercado Pago
     */
    private function load_credentials() {
        // Verificar se está em modo sandbox
        $mp_sandbox = $this->CI->Configuracao_model->get('mercadopago_sandbox');
        $this->sandbox = ($mp_sandbox == '1');

        // Carregar credenciais baseado no modo
        if ($this->sandbox) {
            // Credenciais de TESTE
            $mp_access_token = $this->CI->Configuracao_model->get('mercadopago_access_token_test');
            $mp_public_key = $this->CI->Configuracao_model->get('mercadopago_public_key_test');
        } else {
            // Credenciais de PRODUÇÃO
            $mp_access_token = $this->CI->Configuracao_model->get('mercadopago_access_token_prod');
            $mp_public_key = $this->CI->Configuracao_model->get('mercadopago_public_key_prod');
        }

        if ($mp_access_token) {
            $this->access_token = $mp_access_token;
        }

        if ($mp_public_key) {
            $this->public_key = $mp_public_key;
        }
    }

    /**
     * Criar pagamento PIX
     */
    public function criar_pagamento_pix($dados) {
        $payload = [
            'transaction_amount' => (float) $dados['valor'],
            'description' => $dados['descricao'],
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $dados['email'],
                'first_name' => $dados['nome'],
                'identification' => [
                    'type' => 'CPF',
                    'number' => $dados['cpf'] ?? ''
                ]
            ],
            'notification_url' => $dados['notification_url'] ?? base_url('webhook/mercadopago'),
            'external_reference' => $dados['external_reference'] ?? null,
        ];

        return $this->make_request('POST', '/v1/payments', $payload);
    }

    /**
     * Criar pagamento com cartão
     */
    public function criar_pagamento_cartao($dados) {
        $payload = [
            'transaction_amount' => (float) $dados['valor'],
            'token' => $dados['token'], // Token do cartão gerado pelo Mercado Pago JS
            'description' => $dados['descricao'],
            'installments' => (int) $dados['parcelas'],
            'payer' => [
                'email' => $dados['email'],
                'identification' => [
                    'type' => $dados['tipo_documento'] ?? 'CPF',
                    'number' => $dados['numero_documento']
                ]
            ],
            'notification_url' => $dados['notification_url'] ?? base_url('webhook/mercadopago'),
            'external_reference' => $dados['external_reference'] ?? null,
        ];

        // Adicionar payment_method_id e issuer_id apenas se fornecidos
        if (!empty($dados['payment_method_id'])) {
            $payload['payment_method_id'] = $dados['payment_method_id'];
        }
        if (!empty($dados['issuer_id'])) {
            $payload['issuer_id'] = (int) $dados['issuer_id'];
        }

        return $this->make_request('POST', '/v1/payments', $payload);
    }

    /**
     * Buscar pagamento por ID
     */
    public function get_pagamento($payment_id) {
        return $this->make_request('GET', "/v1/payments/{$payment_id}");
    }

    /**
     * Reembolsar pagamento
     */
    public function reembolsar_pagamento($payment_id, $valor = null) {
        $payload = [];

        if ($valor) {
            $payload['amount'] = (float) $valor;
        }

        return $this->make_request('POST', "/v1/payments/{$payment_id}/refunds", $payload);
    }

    /**
     * Cancelar pagamento
     */
    public function cancelar_pagamento($payment_id) {
        $payload = [
            'status' => 'cancelled'
        ];

        return $this->make_request('PUT', "/v1/payments/{$payment_id}", $payload);
    }

    /**
     * Buscar métodos de pagamento disponíveis
     */
    public function get_metodos_pagamento() {
        return $this->make_request('GET', '/v1/payment_methods');
    }

    /**
     * Buscar parcelas disponíveis
     */
    public function get_parcelas($valor, $payment_method_id) {
        $params = [
            'amount' => $valor,
            'payment_method_id' => $payment_method_id
        ];

        return $this->make_request('GET', '/v1/payment_methods/installments?' . http_build_query($params));
    }

    /**
     * Processar webhook do Mercado Pago
     */
    public function processar_webhook($data) {
        log_message('info', 'Mercadopago_lib::processar_webhook - Dados recebidos: ' . json_encode($data));

        // Verificar tipo de notificação
        if (!isset($data['type'])) {
            log_message('error', 'Mercadopago_lib::processar_webhook - Tipo não encontrado');
            return false;
        }

        // Extrair ID do pagamento (pode vir em data.id ou id)
        $payment_id = null;
        if (isset($data['data']['id'])) {
            $payment_id = $data['data']['id'];
        } elseif (isset($data['id'])) {
            $payment_id = $data['id'];
        }

        if (!$payment_id) {
            log_message('error', 'Mercadopago_lib::processar_webhook - ID do pagamento não encontrado');
            return false;
        }

        log_message('info', 'Mercadopago_lib::processar_webhook - Tipo: ' . $data['type'] . ', ID: ' . $payment_id);

        // Buscar informações do pagamento
        if ($data['type'] == 'payment' || $data['type'] == 'payment.updated') {
            $payment = $this->get_pagamento($payment_id);

            log_message('info', 'Mercadopago_lib::processar_webhook - Resposta API: ' . json_encode($payment));

            if ($payment && $payment['status'] == 200) {
                return $payment['response'];
            } else {
                log_message('error', 'Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: ' . json_encode($payment));
            }
        }

        return false;
    }

    /**
     * Fazer requisição para API do Mercado Pago
     */
    private function make_request($method, $endpoint, $data = []) {
        if (!$this->access_token) {
            return [
                'status' => 401,
                'error' => 'Access token não configurado'
            ];
        }

        $url = $this->api_url . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . uniqid()
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$this->sandbox);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'status' => 500,
                'error' => $error
            ];
        }

        return [
            'status' => $http_code,
            'response' => json_decode($response, true)
        ];
    }

    /**
     * Validar assinatura do webhook
     */
    public function validar_webhook_signature($data, $signature) {
        // Implementar validação de assinatura conforme documentação MP
        // Por segurança, sempre validar a assinatura em produção
        return true;
    }

    /**
     * Obter public key
     */
    public function get_public_key() {
        return $this->public_key;
    }

    /**
     * Verificar se está em modo sandbox
     */
    public function is_sandbox() {
        return $this->sandbox;
    }
}
