<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Logs
 * 
 * Visualização de logs de ações do sistema
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 15/11/2024
 */
class Logs extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Log_model');
        $this->load->library('pagination');
        
        // Apenas admin pode acessar
        if ($this->session->userdata('usuario_nivel') !== 'admin') {
            $this->session->set_flashdata('erro', 'Você não tem permissão para acessar esta área.');
            redirect('admin/dashboard');
        }
    }

    /**
     * Listagem de logs
     */
    public function index() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Logs do Sistema';
        $data['menu_ativo'] = 'logs';
        
        // Filtros
        $filtros = [
            'busca' => $this->input->get('busca'),
            'acao' => $this->input->get('acao'),
            'tabela' => $this->input->get('tabela'),
            'usuario_id' => $this->input->get('usuario_id'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim')
        ];
        
        // Paginação
        $page = $this->input->get('page') ?? 1;
        $per_page = 50;
        $offset = ($page - 1) * $per_page;
        
        // Buscar logs
        $data['logs'] = $this->Log_model->get_all($filtros, $per_page, $offset);
        $total = $this->Log_model->count_all($filtros);
        
        // Configurar paginação
        $config['base_url'] = base_url('admin/logs');
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;
        $config['use_page_numbers'] = TRUE;
        
        // Estilo da paginação
        $config['full_tag_open'] = '<ul class="pagination mb-0">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'Primeira';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Última';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'page-link'];
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        
        // Estatísticas
        $data['total_logs'] = $total;
        $data['filtros'] = $filtros;
        
        // Listas para filtros
        $data['acoes'] = $this->Log_model->get_acoes_distintas();
        $data['tabelas'] = $this->Log_model->get_tabelas_distintas();
        $data['usuarios'] = $this->Log_model->get_usuarios_distintos();
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/logs/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Visualizar detalhes do log
     */
    public function visualizar($id) {
        $data['titulo'] = 'Detalhes do Log';
        $data['menu_ativo'] = 'logs';
        
        $data['log'] = $this->Log_model->get($id);
        
        if (!$data['log']) {
            $this->session->set_flashdata('erro', 'Log não encontrado.');
            redirect('admin/logs');
        }
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/logs/visualizar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Limpar logs antigos
     */
    public function limpar() {
        if ($this->input->method() !== 'post') {
            redirect('admin/logs');
        }
        
        $dias = $this->input->post('dias') ?? 30;
        
        $deletados = $this->Log_model->limpar_antigos($dias);
        
        if ($deletados !== false) {
            $this->session->set_flashdata('sucesso', "Logs com mais de {$dias} dias foram removidos com sucesso!");
        } else {
            $this->session->set_flashdata('erro', 'Erro ao limpar logs.');
        }
        
        redirect('admin/logs');
    }

    /**
     * Exportar logs para CSV
     */
    public function exportar() {
        // Filtros
        $filtros = [
            'busca' => $this->input->get('busca'),
            'acao' => $this->input->get('acao'),
            'tabela' => $this->input->get('tabela'),
            'usuario_id' => $this->input->get('usuario_id'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim')
        ];
        
        $logs = $this->Log_model->get_all($filtros);
        
        // Gerar CSV
        $filename = 'logs_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho
        fputcsv($output, ['ID', 'Data/Hora', 'Usuário', 'Ação', 'Tabela', 'Registro ID', 'IP', 'User Agent'], ';');
        
        // Dados
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                date('d/m/Y H:i:s', strtotime($log->criado_em)),
                $log->usuario_nome ?? 'Sistema',
                $log->acao,
                $log->tabela,
                $log->registro_id,
                $log->ip,
                $log->user_agent
            ], ';');
        }
        
        fclose($output);
        exit;
    }
}
