<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Testes de Pagamento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Pagamento_test extends Admin_Controller {

    protected $modulo_atual = 'configuracoes';

    public function __construct() {
        parent::__construct();
        $this->load->library('Mercadopago_lib');
        $this->load->model('Configuracao_model');
    }

    /**
     * Página de testes de pagamento
     */
    public function index() {
        $data['titulo'] = 'Teste de Pagamentos - Mercado Pago';
        $data['menu_ativo'] = 'configuracoes';
        $data['public_key'] = $this->mercadopago_lib->get_public_key();
        $data['is_sandbox'] = $this->mercadopago_lib->is_sandbox();

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/pagamento_test/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar pagamento PIX de teste
     */
    public function criar_pix() {
        $resultado = [
            'sucesso' => false,
            'mensagem' => '',
            'dados' => []
        ];

        try {
            $valor = $this->input->post('valor');
            $email = $this->input->post('email');
            $nome = $this->input->post('nome');

            if (!$valor || !$email || !$nome) {
                $resultado['mensagem'] = 'Preencha todos os campos';
                echo json_encode($resultado);
                return;
            }

            // Criar pagamento
            $dados_pagamento = [
                'valor' => $valor,
                'descricao' => 'Teste de Pagamento PIX - AgendaPro',
                'email' => $email,
                'nome' => $nome,
                'external_reference' => 'TEST_' . uniqid()
            ];

            $response = $this->mercadopago_lib->criar_pagamento_pix($dados_pagamento);

            if ($response && $response['status'] == 201) {
                $payment = $response['response'];

                $resultado['sucesso'] = true;
                $resultado['mensagem'] = 'Pagamento PIX criado com sucesso!';
                $resultado['dados'] = [
                    'id' => $payment['id'],
                    'status' => $payment['status'],
                    'qr_code' => $payment['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'qr_code_base64' => $payment['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                    'ticket_url' => $payment['point_of_interaction']['transaction_data']['ticket_url'] ?? null
                ];
            } else {
                $resultado['mensagem'] = 'Erro ao criar pagamento: ' . ($response['error'] ?? 'Erro desconhecido');
                $resultado['dados'] = $response;
            }

        } catch (Exception $e) {
            $resultado['mensagem'] = 'Exceção: ' . $e->getMessage();
        }

        echo json_encode($resultado);
    }

    /**
     * Criar pagamento com cartão de teste
     */
    public function criar_cartao() {
        $resultado = [
            'sucesso' => false,
            'mensagem' => '',
            'dados' => []
        ];

        try {
            $valor = $this->input->post('valor');
            $token = $this->input->post('token');
            $email = $this->input->post('email');
            $tipo_documento = $this->input->post('tipo_documento');
            $numero_documento = $this->input->post('numero_documento');
            $payment_method_id = $this->input->post('payment_method_id');
            $issuer_id = $this->input->post('issuer_id');
            $parcelas = $this->input->post('parcelas');

            if (!$valor || !$token || !$email) {
                $resultado['mensagem'] = 'Preencha todos os campos obrigatórios';
                echo json_encode($resultado);
                return;
            }

            // Criar pagamento
            $dados_pagamento = [
                'valor' => $valor,
                'token' => $token,
                'descricao' => 'Teste de Pagamento Cartão - AgendaPro',
                'parcelas' => $parcelas ?? 1,
                'email' => $email,
                'tipo_documento' => $tipo_documento ?? 'CPF',
                'numero_documento' => $numero_documento,
                'external_reference' => 'TEST_' . uniqid()
            ];

            $response = $this->mercadopago_lib->criar_pagamento_cartao($dados_pagamento);

            if ($response && $response['status'] == 201) {
                $payment = $response['response'];

                $resultado['sucesso'] = true;
                $resultado['mensagem'] = 'Pagamento com cartão criado com sucesso!';
                $resultado['dados'] = [
                    'id' => $payment['id'],
                    'status' => $payment['status'],
                    'status_detail' => $payment['status_detail'],
                    'transaction_amount' => $payment['transaction_amount']
                ];
            } else {
                $resultado['mensagem'] = 'Erro ao criar pagamento: ' . json_encode($response['response'] ?? $response);
                $resultado['dados'] = $response;
            }

        } catch (Exception $e) {
            $resultado['mensagem'] = 'Exceção: ' . $e->getMessage();
        }

        echo json_encode($resultado);
    }

    /**
     * Consultar status de pagamento
     */
    public function consultar($payment_id) {
        $resultado = [
            'sucesso' => false,
            'mensagem' => '',
            'dados' => []
        ];

        try {
            $response = $this->mercadopago_lib->get_pagamento($payment_id);

            if ($response && $response['status'] == 200) {
                $resultado['sucesso'] = true;
                $resultado['dados'] = $response['response'];
            } else {
                $resultado['mensagem'] = 'Erro ao consultar pagamento';
                $resultado['dados'] = $response;
            }

        } catch (Exception $e) {
            $resultado['mensagem'] = 'Exceção: ' . $e->getMessage();
        }

        echo json_encode($resultado);
    }
}
