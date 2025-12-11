<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Servicos (Painel)
 *
 * Gestão de serviços do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Servicos extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Servico_model');
    }

    /**
     * Listagem de serviços
     */
    public function index() {
        $data['titulo'] = 'Serviços';
        $data['menu_ativo'] = 'servicos';
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['total'] = count($data['servicos']);
        $data['filtros'] = ['estabelecimento_id' => $this->estabelecimento_id];
        $data['pagination'] = '';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/servicos/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar serviço
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('duracao', 'Duração', 'required|integer');
            $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'duracao' => $this->input->post('duracao'),
                    'preco' => $this->input->post('preco'),
                    'status' => 'ativo',
                ];

                if ($this->Servico_model->criar($dados)) {
                    $this->session->set_flashdata('sucesso', 'Serviço criado com sucesso!');
                    redirect('painel/servicos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar serviço.');
                }
            }
        }

        $data['titulo'] = 'Novo Serviço';
        $data['menu_ativo'] = 'servicos';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/servicos/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar serviço
     */
    public function editar($id) {
        $servico = $this->Servico_model->get($id);

        if (!$servico || $servico->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Serviço não encontrado.');
            redirect('painel/servicos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('duracao', 'Duração', 'required|integer');
            $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'duracao' => $this->input->post('duracao'),
                    'preco' => $this->input->post('preco'),
                ];

                if ($this->Servico_model->atualizar($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Serviço atualizado com sucesso!');
                    redirect('painel/servicos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar serviço.');
                }
            }
        }

        $data['titulo'] = 'Editar Serviço';
        $data['menu_ativo'] = 'servicos';
        $data['servico'] = $servico;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/servicos/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Desativar serviço
     */
    public function desativar($id) {
        $servico = $this->Servico_model->get($id);

        if (!$servico || $servico->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Serviço não encontrado.');
            redirect('painel/servicos');
        }

        if ($this->Servico_model->atualizar($id, ['status' => 'inativo'])) {
            $this->session->set_flashdata('sucesso', 'Serviço desativado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao desativar serviço.');
        }

        redirect('painel/servicos');
    }

    /**
     * Alias para desativar
     */
    public function deletar($id) {
        $this->desativar($id);
    }

    /**
     * Alias para desativar
     */
    public function excluir($id) {
        $this->desativar($id);
    }

    /**
     * Visualizar serviço
     */
    public function visualizar($id) {
        $servico = $this->Servico_model->get($id);

        if (!$servico || $servico->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Serviço não encontrado.');
            redirect('painel/servicos');
        }

        $data['titulo'] = 'Visualizar Serviço';
        $data['menu_ativo'] = 'servicos';
        $data['servico'] = $servico;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/servicos/visualizar', $data);
        $this->load->view('painel/layout/footer');
    }
}
