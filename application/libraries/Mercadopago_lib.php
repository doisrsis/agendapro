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
            'external_reference' => $dados['external_reference'] ?? null,
        ];

        // Adicionar notification_url apenas se não for localhost
        $notification_url = $dados['notification_url'] ?? base_url('webhook/mercadopago');
        if (strpos($notification_url, 'localhost') === false && strpos($notification_url, '127.0.0.1') === false) {
            $payload['notification_url'] = $notification_url;
        }

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

    // ========================================================================
    // MÉTODOS PARA ASSINATURAS RECORRENTES (PLANOS)
    // ========================================================================

    /**
     * Criar plano de assinatura no Mercado Pago
     *
     * @param array $dados ['nome', 'valor_mensal', 'descricao']
     * @return array
     */
    public function criar_plano($dados) {
        $payload = [
            'reason' => $dados['nome'] . ' - AgendaPro',
            'auto_recurring' => [
                'frequency' => 1,
                'frequency_type' => 'months',
                'transaction_amount' => (float) $dados['valor_mensal'],
                'currency_id' => 'BRL'
            ],
            'back_url' => base_url('painel/dashboard')
        ];

        $result = $this->make_request('POST', '/preapproval_plan', $payload);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }

    /**
     * Atualizar plano no Mercado Pago
     * ATENÇÃO: MP não permite alterar valor de planos ativos
     *
     * @param string $plan_id
     * @param array $dados
     * @return array
     */
    public function atualizar_plano($plan_id, $dados) {
        // MP só permite atualizar reason (descrição)
        $payload = [
            'reason' => $dados['nome'] . ' - AgendaPro'
        ];

        $result = $this->make_request('PUT', '/preapproval_plan/' . $plan_id, $payload);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }

    /**
     * Desativar plano no Mercado Pago
     *
     * @param string $plan_id
     * @return array
     */
    public function desativar_plano($plan_id) {
        $payload = [
            'status' => 'inactive'
        ];

        $result = $this->make_request('PUT', '/preapproval_plan/' . $plan_id, $payload);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }

    /**
     * Buscar plano no Mercado Pago
     *
     * @param string $plan_id
     * @return array
     */
    public function buscar_plano($plan_id) {
        $result = $this->make_request('GET', '/preapproval_plan/' . $plan_id);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }

    /**
     * Criar assinatura (subscription) no Mercado Pago
     *
     * @param array $dados ['plan_id', 'payer_email', 'valor']
     * @return array
     */
    public function criar_assinatura($dados) {
        $payload = [
            'preapproval_plan_id' => $dados['plan_id'],
            'reason' => 'Assinatura AgendaPro',
            'payer_email' => $dados['payer_email'],
            'back_url' => base_url('painel/dashboard'),
            'auto_recurring' => [
                'frequency' => 1,
                'frequency_type' => 'months',
                'transaction_amount' => (float) $dados['valor'],
                'currency_id' => 'BRL',
                'start_date' => date('Y-m-d\TH:i:s.000P'),
                'end_date' => date('Y-m-d\TH:i:s.000P', strtotime('+1 year'))
            ],
            'status' => 'authorized'
        ];

        $result = $this->make_request('POST', '/preapproval', $payload);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }

    /**
     * Cancelar assinatura no Mercado Pago
     *
     * @param string $subscription_id
     * @return array
     */
    public function cancelar_assinatura($subscription_id) {
        $payload = [
            'status' => 'cancelled'
        ];

        $result = $this->make_request('PUT', '/preapproval/' . $subscription_id, $payload);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }

    /**
     * Buscar assinatura no Mercado Pago
     *
     * @param string $subscription_id
     * @return array
     */
    public function buscar_assinatura($subscription_id) {
        $result = $this->make_request('GET', '/preapproval/' . $subscription_id);

        return [
            'success' => ($result['status'] >= 200 && $result['status'] < 300),
            'data' => $result['response'],
            'http_code' => $result['status']
        ];
    }
}
