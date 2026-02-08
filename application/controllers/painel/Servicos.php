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
        $filtros = ['estabelecimento_id' => $this->estabelecimento_id];

        // Configuração da Paginação
        $this->load->library('pagination');
        $config['base_url'] = base_url('painel/servicos/index');
        $config['total_rows'] = $this->Servico_model->count_all($filtros);
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Estilização do Bootstrap 5 / Tabler
        $config['full_tag_open'] = '<ul class="pagination m-0 ms-auto">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '<i class="ti ti-chevrons-left"></i>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '<i class="ti ti-chevrons-right"></i>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '<i class="ti ti-chevron-right"></i>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="ti ti-chevron-left"></i>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $page = $this->input->get('page') ? (int)$this->input->get('page') : 0;
        $config['cur_page'] = $page;

        $this->pagination->initialize($config);

        $data['servicos'] = $this->Servico_model->get_all($filtros, $config['per_page'], $page);
        $data['total'] = $config['total_rows'];
        $data['filtros'] = $filtros;
        $data['pagination'] = $this->pagination->create_links();

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
