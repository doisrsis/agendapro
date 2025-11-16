<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Dashboard Administrativo - Projeto Base
 *
 * Painel principal com estatísticas e informações do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 16/11/2024
 */
class Dashboard extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->model('Notificacao_model');
        $this->load->model('Log_model');
    }

    /**
     * Página principal do dashboard
     */
    public function index() {
        $data['titulo'] = 'Dashboard';
        $data['menu_ativo'] = 'dashboard';

        // Estatísticas gerais
        $data['stats'] = $this->get_estatisticas();

        // Atividades recentes (logs)
        $data['atividades_recentes'] = $this->Log_model->get_all(10);

        // Notificações não lidas
        $usuario_id = $this->session->userdata('usuario_id');
        $data['notificacoes'] = $this->Notificacao_model->get_por_usuario($usuario_id, true, 5);

        // Carregar views
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Obter estatísticas gerais do sistema
     */
    private function get_estatisticas() {
        $stats = [];

        // Usuários
        $stats['usuarios_total'] = $this->Usuario_model->count(['status' => 'ativo']);
        $stats['usuarios_admin'] = $this->Usuario_model->count([
            'status' => 'ativo',
            'nivel' => 'admin'
        ]);

        // Usuários cadastrados hoje
        $this->db->where('DATE(criado_em)', date('Y-m-d'));
        $stats['usuarios_hoje'] = $this->db->count_all_results('usuarios');

        // Usuários cadastrados este mês
        $this->db->where('MONTH(criado_em)', date('m'));
        $this->db->where('YEAR(criado_em)', date('Y'));
        $stats['usuarios_mes'] = $this->db->count_all_results('usuarios');

        // Notificações não lidas
        $usuario_id = $this->session->userdata('usuario_id');
        $stats['notificacoes_nao_lidas'] = $this->Notificacao_model->contar_nao_lidas($usuario_id);

        // Logs de hoje
        $this->db->where('DATE(criado_em)', date('Y-m-d'));
        $stats['logs_hoje'] = $this->db->count_all_results('logs');

        // Último acesso
        $this->db->select_max('ultimo_acesso');
        $ultimo = $this->db->get('usuarios')->row();
        $stats['ultimo_acesso'] = $ultimo->ultimo_acesso ?? null;

        return $stats;
    }

    /**
     * API: Obter estatísticas em tempo real
     */
    public function api_stats() {
        $stats = $this->get_estatisticas();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Estatísticas obtidas',
                'data' => $stats
            ]));
    }
}
