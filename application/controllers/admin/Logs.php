<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Logs do Sistema
 *
 * Visualização do histórico de ações dos usuários
 *
 * @author Rafael Dias - doisr.com.br
 * @date 16/11/2024
 */
class Logs extends Admin_Controller {

    protected $modulo_atual = 'logs';

    public function __construct() {
        parent::__construct();
        $this->load->model('Log_model');
        $this->load->model('Usuario_model');
    }

    /**
     * Listagem de logs
     */
    public function index() {
        $data['titulo'] = 'Logs do Sistema';
        $data['menu_ativo'] = 'logs';

        // Filtros
        $filtros = [];

        if ($this->input->get('usuario_id')) {
            $filtros['usuario_id'] = $this->input->get('usuario_id');
        }

        if ($this->input->get('acao')) {
            $filtros['acao'] = $this->input->get('acao');
        }

        if ($this->input->get('data_inicio')) {
            $filtros['data_inicio'] = $this->input->get('data_inicio');
        }

        if ($this->input->get('data_fim')) {
            $filtros['data_fim'] = $this->input->get('data_fim');
        }

        // Paginação
        $config['base_url'] = base_url('admin/logs');
        $config['total_rows'] = $this->Log_model->count($filtros);
        $config['per_page'] = 50;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;

        // Estilo da paginação
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        // Buscar logs
        $data['logs'] = $this->Log_model->get_all($filtros, $config['per_page'], $offset);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];

        // Buscar usuários para filtro
        $data['usuarios'] = $this->Usuario_model->get_all(['status' => 'ativo']);

        // Ações disponíveis
        $data['acoes'] = ['login', 'logout', 'criar', 'editar', 'excluir'];

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/logs/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Visualizar detalhes de um log
     */
    public function visualizar($id) {
        $log = $this->Log_model->get_by_id($id);

        if (!$log) {
            $this->session->set_flashdata('erro', 'Log não encontrado.');
            redirect('admin/logs');
        }

        $data['titulo'] = 'Detalhes do Log';
        $data['menu_ativo'] = 'logs';
        $data['log'] = $log;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/logs/visualizar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Limpar logs antigos
     */
    public function limpar() {
        // Apenas admin pode limpar logs
        if ($this->session->userdata('usuario_nivel') != 'admin') {
            $this->session->set_flashdata('erro', 'Você não tem permissão para esta ação.');
            redirect('admin/logs');
        }

        $dias = $this->input->post('dias') ?? 30;

        if ($this->Log_model->limpar_antigos($dias)) {
            $this->session->set_flashdata('sucesso', 'Logs antigos foram removidos com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao remover logs antigos.');
        }

        redirect('admin/logs');
    }
}
