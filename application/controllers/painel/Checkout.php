<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Checkout
 *
 * Gerencia processo de checkout/pagamento de planos via Mercado Pago
 *
 * @author Rafael Dias - doisr.com.br
 * @date 18/12/2024
 */
class Checkout extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Plano_model');
        $this->load->model('Assinatura_model');
        $this->load->model('Pagamento_model');
        $this->load->library('auth_check');
        $this->load->library('mercadopago_lib');

        // Verificar se usuário está logado
        if (!$this->session->userdata('usuario_id')) {
            redirect('login');
        }
    }

    /**
     * Página de checkout do plano
     */
    public function index($plano_slug) {
        // Buscar plano
        $plano = $this->Plano_model->get_by_slug($plano_slug);

        if (!$plano) {
            $this->session->set_flashdata('erro', 'Plano não encontrado.');
            redirect('painel/assinatura_expirada');
        }

        $data['plano'] = $plano;
        $data['titulo'] = 'Checkout - ' . $plano->nome;

        // Buscar assinatura atual (se houver)
        $estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        if ($estabelecimento_id) {
            $assinaturas = $this->Assinatura_model->get_by_estabelecimento($estabelecimento_id);
            if (!empty($assinaturas)) {
                $data['assinatura_atual'] = $assinaturas[0];
            }
        }

        $this->load->view('painel/checkout', $data);
    }

    /**
     * Gerar pagamento PIX
     */
    public function gerar_pix() {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $plano_id = $this->input->post('plano_id');

            log_message('info', 'gerar_pix chamado - Plano ID: ' . $plano_id);

            if (!$plano_id) {
                echo json_encode(['success' => false, 'error' => 'Plano não selecionado.']);
                exit;
            }

            $plano = $this->Plano_model->get($plano_id);

            if (!$plano) {
                echo json_encode(['success' => false, 'error' => 'Plano não encontrado.']);
                exit;
            }

            $estabelecimento_id = $this->auth_check->get_estabelecimento_id();
            $usuario_email = $this->session->userdata('email');
            $usuario_nome = $this->session->userdata('nome');

            // Fallback para email se não estiver na sessão
            if (empty($usuario_email)) {
                $this->load->model('Usuario_model');
                $usuario = $this->Usuario_model->get($this->session->userdata('usuario_id'));
                $usuario_email = $usuario->email ?? 'contato@agendapro.com';
                $usuario_nome = $usuario->nome ?? 'Cliente';
            }

            log_message('info', 'Gerando PIX - Estabelecimento: ' . $estabelecimento_id . ' - Email: ' . $usuario_email . ' - Valor: ' . $plano->valor_mensal);

            // Criar pagamento PIX no Mercado Pago (usando chaves corretas da biblioteca)
            $resultado = $this->mercadopago_lib->criar_pagamento_pix([
                'valor' => (float) $plano->valor_mensal,
                'descricao' => 'Assinatura ' . $plano->nome . ' - AgendaPro',
                'email' => $usuario_email,
                'nome' => $usuario_nome,
                'external_reference' => 'PLANO_' . $plano_id . '_EST_' . $estabelecimento_id
            ]);

            log_message('info', 'Resultado MP: ' . json_encode($resultado));

            // A biblioteca retorna ['status' => code, 'response' => data]
            if ($resultado['status'] == 201 || $resultado['status'] == 200) {
                $payment_data = $resultado['response'];

                log_message('info', 'PIX gerado com sucesso - Payment ID: ' . $payment_data['id']);

                // Salvar pagamento no banco
                $pagamento_id = $this->Pagamento_model->criar([
                    'estabelecimento_id' => $estabelecimento_id,
                    'plano_id' => $plano_id,
                    'mercadopago_id' => $payment_data['id'],
                    'tipo' => 'pix',
                    'valor' => $plano->valor_mensal,
                    'status' => $payment_data['status'],
                    'qr_code' => $payment_data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'qr_code_base64' => $payment_data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                    'payment_data' => json_encode($payment_data)
                ]);

                log_message('info', 'Pagamento salvo - ID: ' . $pagamento_id);

                echo json_encode([
                    'success' => true,
                    'payment_id' => $payment_data['id'],
                    'qr_code' => $payment_data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'qr_code_base64' => $payment_data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                    'pagamento_id' => $pagamento_id
                ]);
            } else {
                $error_msg = $resultado['response']['message'] ?? $resultado['error'] ?? 'Erro ao gerar pagamento PIX.';
                log_message('error', 'Erro MP (Status ' . $resultado['status'] . '): ' . $error_msg);

                echo json_encode([
                    'success' => false,
                    'error' => $error_msg
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Exceção em gerar_pix: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Erro interno: ' . $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Verificar status do pagamento (polling)
     */
    public function status($payment_id) {
        header('Content-Type: application/json');

        try {
            // Buscar status no Mercado Pago
            $resultado = $this->mercadopago_lib->get_pagamento($payment_id);

            if ($resultado['status'] == 200) {
                $payment_data = $resultado['response'];

                // Buscar pagamento no banco
                $pagamento = $this->Pagamento_model->get_by_mercadopago_id($payment_id);

                if (!$pagamento) {
                    echo json_encode(['success' => false, 'error' => 'Pagamento não encontrado']);
                    exit;
                }

                // Atualizar status no banco
                $this->Pagamento_model->atualizar_status_by_mp_id(
                    $payment_id,
                    $payment_data['status'],
                    $payment_data['status_detail'] ?? null,
                    $payment_data
                );

                // Se pagamento foi aprovado e ainda não tem assinatura vinculada
                if ($payment_data['status'] == 'approved' && !$pagamento->assinatura_id) {
                    log_message('info', 'Pagamento aprovado - Processando assinatura para pagamento #' . $pagamento->id);

                    // Verificar se estabelecimento já tem assinatura
                    $assinaturas_existentes = $this->Assinatura_model->get_by_estabelecimento($pagamento->estabelecimento_id);

                    if (!empty($assinaturas_existentes)) {
                        // RENOVAR assinatura existente
                        $assinatura_existente = $assinaturas_existentes[0];

                        // Calcular nova data de fim (30 dias a partir de hoje ou da data de fim atual, o que for maior)
                        $data_atual = date('Y-m-d');
                        $data_fim_atual = $assinatura_existente->data_fim;

                        if ($data_fim_atual > $data_atual) {
                            // Se ainda não expirou, adicionar 30 dias à data de fim
                            $nova_data_fim = date('Y-m-d', strtotime($data_fim_atual . ' +30 days'));
                        } else {
                            // Se já expirou, 30 dias a partir de hoje
                            $nova_data_fim = date('Y-m-d', strtotime('+30 days'));
                        }

                        // Renovar assinatura
                        $this->Assinatura_model->ativar(
                            $assinatura_existente->id,
                            $nova_data_fim,
                            $pagamento->valor
                        );

                        // Vincular pagamento à assinatura existente
                        $this->Pagamento_model->vincular_assinatura($pagamento->id, $assinatura_existente->id);

                        log_message('info', 'Assinatura #' . $assinatura_existente->id . ' RENOVADA até ' . $nova_data_fim);
                    } else {
                        // CRIAR nova assinatura (primeira vez)
                        $assinatura_id = $this->Assinatura_model->criar([
                            'estabelecimento_id' => $pagamento->estabelecimento_id,
                            'plano_id' => $pagamento->plano_id,
                            'data_inicio' => date('Y-m-d'),
                            'data_fim' => date('Y-m-d', strtotime('+30 days')),
                            'status' => 'ativa',
                            'valor_pago' => $pagamento->valor,
                            'auto_renovar' => 1
                        ]);

                        // Vincular pagamento à nova assinatura
                        $this->Pagamento_model->vincular_assinatura($pagamento->id, $assinatura_id);

                        log_message('info', 'Nova assinatura #' . $assinatura_id . ' CRIADA e ativada!');
                    }
                }

                echo json_encode([
                    'success' => true,
                    'status' => $payment_data['status'],
                    'status_detail' => $payment_data['status_detail'] ?? null
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao verificar status do pagamento.'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro em status(): ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Erro: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Página de sucesso
     */
    public function sucesso() {
        $payment_id = $this->input->get('payment_id');

        if ($payment_id) {
            $pagamento = $this->Pagamento_model->get_by_mercadopago_id($payment_id);
            $data['pagamento'] = $pagamento;
        }

        $data['titulo'] = 'Pagamento Confirmado';
        $this->load->view('painel/checkout_sucesso', $data);
    }

    /**
     * Página de falha
     */
    public function falha() {
        $data['titulo'] = 'Pagamento Não Aprovado';
        $this->load->view('painel/checkout_falha', $data);
    }

    /**
     * Processar pagamento (método antigo - manter para compatibilidade)
     */
    public function processar() {
        $plano_id = $this->input->post('plano_id');

        if (!$plano_id) {
            $this->session->set_flashdata('erro', 'Plano não selecionado.');
            redirect('painel/assinatura_expirada');
        }

        $plano = $this->Plano_model->get($plano_id);

        if (!$plano) {
            $this->session->set_flashdata('erro', 'Plano não encontrado.');
            redirect('painel/assinatura_expirada');
        }

        // Redirecionar para checkout com PIX
        redirect('painel/checkout/' . $plano->slug);
    }
}
