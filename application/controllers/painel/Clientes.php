<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Clientes (Painel)
 *
 * Gestão de clientes do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Clientes extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Cliente_model');
    }

    /**
     * Listagem de clientes
     */
    public function index() {
        $data['titulo'] = 'Clientes';
        $data['menu_ativo'] = 'clientes';

        $filtros = ['estabelecimento_id' => $this->estabelecimento_id];

        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }

        $data['clientes'] = $this->Cliente_model->get_all($filtros);
        $data['total'] = count($data['clientes']);
        $data['filtros'] = $filtros;
        $data['pagination'] = '';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/clientes/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar cliente
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'nome' => $this->input->post('nome'),
                    'cpf' => $this->input->post('cpf'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                ];

                if ($this->Cliente_model->criar($dados)) {
                    $this->session->set_flashdata('sucesso', 'Cliente criado com sucesso!');
                    redirect('painel/clientes');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar cliente.');
                }
            }
        }

        $data['titulo'] = 'Novo Cliente';
        $data['menu_ativo'] = 'clientes';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/clientes/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar cliente
     */
    public function editar($id) {
        $cliente = $this->Cliente_model->get($id);

        if (!$cliente || $cliente->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('painel/clientes');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'cpf' => $this->input->post('cpf'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                ];

                if ($this->Cliente_model->atualizar($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Cliente atualizado com sucesso!');
                    redirect('painel/clientes');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar cliente.');
                }
            }
        }

        $data['titulo'] = 'Editar Cliente';
        $data['menu_ativo'] = 'clientes';
        $data['cliente'] = $cliente;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/clientes/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Excluir cliente
     */
    public function excluir($id) {
        $cliente = $this->Cliente_model->get($id);

        if (!$cliente || $cliente->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('painel/clientes');
        }

        if ($this->Cliente_model->excluir($id)) {
            $this->session->set_flashdata('sucesso', 'Cliente excluído com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao excluir cliente.');
        }

        redirect('painel/clientes');
    }

    /**
     * Alias para excluir (compatibilidade com views)
     */
    public function deletar($id) {
        $this->excluir($id);
    }

    /**
     * Visualizar cliente
     */
    public function visualizar($id) {
        $cliente = $this->Cliente_model->get($id);

        if (!$cliente || $cliente->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('painel/clientes');
        }

        $data['titulo'] = 'Visualizar Cliente';
        $data['menu_ativo'] = 'clientes';
        $data['cliente'] = $cliente;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/clientes/visualizar', $data);
        $this->load->view('painel/layout/footer');
    }
}
