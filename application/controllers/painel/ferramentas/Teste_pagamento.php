<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teste_pagamento extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Verificar se usuário está logado
        if (!$this->session->userdata('usuario_logado')) {
            redirect('login');
        }

        $this->load->model('Estabelecimento_model');
        $this->load->model('Agendamento_model');
        $this->load->library('Mercadopago_lib');
        $this->load->model('Cliente_model');
    }

    public function index() {
        $dados['estabelecimentos'] = $this->Estabelecimento_model->get_all();
        $dados['menu_ativo'] = 'ferramentas'; // Define o menu ativo para evitar erro no header
        $dados['titulo'] = 'Teste de Pagamento'; // Define o título da página

        $this->load->view('painel/layout/header', $dados);
        $this->load->view('painel/ferramentas/teste_pagamento', $dados);
        $this->load->view('painel/layout/footer');
    }

    public function gerar() {
        $estabelecimento_id = $this->input->post('estabelecimento_id');
        $valor = $this->input->post('valor');

        if (!$estabelecimento_id || !$valor) {
            echo json_encode(['error' => 'Selecione o estabelecimento e valor']);
            return;
        }

        try {
            // 1. Criar Agendamento Fake
            // Tenta pegar um cliente existente (ex: admin ou o primeiro que achar)
            $cliente = $this->Cliente_model->get_by_id(1);
            if (!$cliente) {
                // Fallback se não tiver cliente 1
                $clientes = $this->Cliente_model->get_all();
                $cliente = $clientes[0] ?? null;
            }

            if (!$cliente) {
                echo json_encode(['error' => 'Nenhum cliente encontrado para vincular ao teste']);
                return;
            }

            $dados_agendamento = [
                'estabelecimento_id' => $estabelecimento_id,
                'cliente_id' => $cliente->id,
                'profissional_id' => null, // Não precisa travar agenda
                'servico_id' => null,
                'data' => date('Y-m-d'),
                'hora_inicio' => date('H:i:s'),
                'hora_fim' => date('H:i:s', strtotime('+10 minutes')),
                'status' => 'pendente',
                'pagamento_status' => 'pendente',
                'forma_pagamento' => 'pix',
                'pagamento_valor' => $valor,
                'observacoes' => 'TESTE DE PAGAMENTO - Gerado em ' . date('d/m/Y H:i:s')
            ];

            // Inserir manualmente para bypassar validações de horário da model se necessário,
            // mas vamos tentar usar o create se possível, ou insert direto.
            // Agendamento_model->create faz muitas verificações. Vamos inserir direto para ser um "fake" seguro.
            $this->db->insert('agendamentos', $dados_agendamento);
            $agendamento_id = $this->db->insert_id();

            // 2. Gerar Preferência MP
            // Configurar a lib com o estabelecimento
            $this->mercadopago_lib->set_estabelecimento($estabelecimento_id);

            $payment_data = [
                'transaction_amount' => (float)$valor,
                'description' => 'Teste Pagamento #' . $agendamento_id,
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $cliente->email ?? 'teste@teste.com',
                    'first_name' => 'Teste',
                    'last_name' => 'Pagamento',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => '19119119100' // CPF genérico válido para testes muitas vezes, ou usar do cliente
                    ]
                ],
                'external_reference' => $agendamento_id,
                'notification_url' => base_url('webhook/mercadopago')
            ];

            $resposta = $this->mercadopago_lib->create_payment($payment_data);

            if ($resposta && isset($resposta['status']) && $resposta['status'] == 'pending') {
                 // Atualizar agendamento com dados do PIX
                 $update_data = [
                    'pagamento_id' => $resposta['id'],
                    'pagamento_pix_qrcode' => $resposta['point_of_interaction']['transaction_data']['qr_code_base64'],
                    'pagamento_pix_copia_cola' => $resposta['point_of_interaction']['transaction_data']['qr_code']
                 ];
                 $this->Agendamento_model->update($agendamento_id, $update_data);

                 echo json_encode([
                    'success' => true,
                    'agendamento_id' => $agendamento_id,
                    'qrcode_base64' => $update_data['pagamento_pix_qrcode'],
                    'copia_cola' => $update_data['pagamento_pix_copia_cola'],
                    'valor' => $valor
                 ]);
            } else {
                echo json_encode(['error' => 'Erro ao gerar PIX no Mercado Pago: ' . json_encode($resposta)]);
            }

        } catch (Exception $e) {
            echo json_encode(['error' => 'Exceção: ' . $e->getMessage()]);
        }
    }
}
