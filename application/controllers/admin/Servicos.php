<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Serviços
 *
 * Gerenciamento de serviços dos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Servicos extends Admin_Controller {

    protected $modulo_atual = 'servicos';

    public function __construct() {
        parent::__construct();
        $this->load->model('Servico_model');
        $this->load->model('Estabelecimento_model');
    }

    /**
     * Listagem de serviços
     */
    public function index() {
        $data['titulo'] = 'Serviços';
        $data['menu_ativo'] = 'servicos';

        $filtros = [];

        if ($this->input->get('estabelecimento_id')) {
            $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
        }

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }

        $data['servicos'] = $this->Servico_model->get_all($filtros);
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        $data['filtros'] = $filtros;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/servicos/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo serviço
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required|integer');
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('duracao', 'Duração', 'required|integer|greater_than[0]');
            $this->form_validation->set_rules('preco', 'Preço', 'required|decimal');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->input->post('estabelecimento_id'),
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'duracao' => $this->input->post('duracao'),
                    'preco' => $this->input->post('preco'),
                    'status' => $this->input->post('status') ?: 'ativo',
                ];

                $id = $this->Servico_model->create($dados);

                if ($id) {
                    $this->session->set_flashdata('sucesso', 'Serviço criado com sucesso!');
                    redirect('admin/servicos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar serviço.');
                }
            }
        }

        $data['titulo'] = 'Novo Serviço';
        $data['menu_ativo'] = 'servicos';
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/servicos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar serviço
     */
    public function editar($id) {
        $servico = $this->Servico_model->get_by_id($id);

        if (!$servico) {
            $this->session->set_flashdata('erro', 'Serviço não encontrado.');
            redirect('admin/servicos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('duracao', 'Duração', 'required|integer|greater_than[0]');
            $this->form_validation->set_rules('preco', 'Preço', 'required|decimal');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'duracao' => $this->input->post('duracao'),
                    'preco' => $this->input->post('preco'),
                    'status' => $this->input->post('status'),
                ];

                if ($this->Servico_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Serviço atualizado com sucesso!');
                    redirect('admin/servicos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar serviço.');
                }
            }
        }

        $data['titulo'] = 'Editar Serviço';
        $data['menu_ativo'] = 'servicos';
        $data['servico'] = $servico;
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/servicos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Deletar serviço
     */
    public function deletar($id) {
        $servico = $this->Servico_model->get_by_id($id);

        if (!$servico) {
            $this->session->set_flashdata('erro', 'Serviço não encontrado.');
            redirect('admin/servicos');
        }

        if ($this->Servico_model->delete($id)) {
            $this->session->set_flashdata('sucesso', 'Serviço deletado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar serviço.');
        }

        redirect('admin/servicos');
    }
}
