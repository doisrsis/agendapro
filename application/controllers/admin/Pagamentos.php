<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Pagamentos
 *
 * Gerenciamento de pagamentos via Mercado Pago
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Pagamentos extends Admin_Controller {

    protected $modulo_atual = 'pagamentos';

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Cliente_model');
        $this->load->library('Mercadopago_lib');
    }

    /**
     * Listagem de pagamentos
     */
    public function index() {
        $data['titulo'] = 'Pagamentos';
        $data['menu_ativo'] = 'pagamentos';

        $filtros = [];

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('metodo')) {
            $filtros['metodo_pagamento'] = $this->input->get('metodo');
        }

        // Buscar pagamentos do banco
        $this->db->select('p.*, a.data, a.hora_inicio, c.nome as cliente_nome, s.nome as servico_nome');
        $this->db->from('pagamentos p');
        $this->db->join('agendamentos a', 'a.id = p.agendamento_id');
        $this->db->join('clientes c', 'c.id = a.cliente_id');
        $this->db->join('servicos s', 's.id = a.servico_id');

        if (!empty($filtros['status'])) {
            $this->db->where('p.status', $filtros['status']);
        }

        if (!empty($filtros['metodo_pagamento'])) {
            $this->db->where('p.metodo_pagamento', $filtros['metodo_pagamento']);
        }

        $this->db->order_by('p.criado_em', 'DESC');
        $this->db->limit(100);

        $data['pagamentos'] = $this->db->get()->result();
        $data['filtros'] = $filtros;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/pagamentos/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar pagamento PIX
     */
    public function criar_pix($agendamento_id) {
        $agendamento = $this->Agendamento_model->get_by_id($agendamento_id);

        if (!$agendamento) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('admin/agendamentos');
        }

        $cliente = $this->Cliente_model->get_by_id($agendamento->cliente_id);

        $dados_pagamento = [
            'valor' => $agendamento->servico_preco,
            'descricao' => 'Agendamento - ' . $agendamento->servico_nome,
            'email' => $cliente->email ?: 'cliente@email.com',
            'nome' => $cliente->nome,
            'cpf' => $cliente->cpf,
            'external_reference' => 'agendamento_' . $agendamento_id,
            'notification_url' => base_url('webhook/mercadopago')
        ];

        $resultado = $this->mercadopago_lib->criar_pagamento_pix($dados_pagamento);

        if ($resultado['status'] == 201) {
            // Salvar pagamento no banco
            $this->db->insert('pagamentos', [
                'agendamento_id' => $agendamento_id,
                'mercadopago_id' => $resultado['response']['id'],
                'status' => $resultado['response']['status'],
                'metodo_pagamento' => 'pix',
                'valor' => $resultado['response']['transaction_amount'],
                'qr_code' => $resultado['response']['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                'qr_code_base64' => $resultado['response']['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                'criado_em' => date('Y-m-d H:i:s')
            ]);

            $this->session->set_flashdata('sucesso', 'Pagamento PIX criado com sucesso!');
            $this->session->set_flashdata('qr_code', $resultado['response']['point_of_interaction']['transaction_data']['qr_code_base64']);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao criar pagamento: ' . ($resultado['response']['message'] ?? 'Erro desconhecido'));
        }

        redirect('admin/agendamentos');
    }

    /**
     * Reembolsar pagamento
     */
    public function reembolsar($pagamento_id) {
        $this->db->where('id', $pagamento_id);
        $pagamento = $this->db->get('pagamentos')->row();

        if (!$pagamento) {
            $this->session->set_flashdata('erro', 'Pagamento não encontrado.');
            redirect('admin/pagamentos');
        }

        $resultado = $this->mercadopago_lib->reembolsar_pagamento($pagamento->mercadopago_id);

        if ($resultado['status'] == 201) {
            // Atualizar status no banco
            $this->db->where('id', $pagamento_id);
            $this->db->update('pagamentos', [
                'status' => 'refunded',
                'atualizado_em' => date('Y-m-d H:i:s')
            ]);

            $this->session->set_flashdata('sucesso', 'Pagamento reembolsado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao reembolsar pagamento.');
        }

        redirect('admin/pagamentos');
    }
}
