<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Controller Base
 *
 * Controller base para área administrativa
 * Verifica autenticação e permissões
 *
 * @author Rafael Dias - doisr.com.br
 * @date 16/11/2024
 */
class Admin_Controller extends CI_Controller {

    protected $modulo_atual = null;

    public function __construct() {
        parent::__construct();

        // Verificar se está logado
        if (!$this->session->userdata('usuario_logado')) {
            $redirect_url = uri_string();
            redirect('login?redirect=' . urlencode($redirect_url));
        }

        // Carregar dados do usuário logado
        $this->load->model('Usuario_model');
        $usuario_id = $this->session->userdata('usuario_id');
        $usuario = $this->Usuario_model->get($usuario_id);

        if (!$usuario || $usuario->status != 'ativo') {
            $this->session->sess_destroy();
            $this->session->set_flashdata('erro', 'Usuário inativo ou não encontrado.');
            redirect('login');
        }

        // Disponibilizar usuário logado para as views
        $this->load->vars(['usuario_logado' => $usuario]);

        // Verificar permissões (exceto para admin)
        if ($usuario->nivel != 'admin' && $this->modulo_atual) {
            $this->verificar_permissao($this->modulo_atual);
        }
    }

    /**
     * Verificar se usuário tem permissão para acessar o módulo
     */
    protected function verificar_permissao($modulo, $acao = 'visualizar') {
        $usuario_id = $this->session->userdata('usuario_id');

        if (!$this->Usuario_model->tem_permissao($usuario_id, $modulo, $acao)) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para acessar este módulo.');
            redirect('admin/dashboard');
        }
    }

    /**
     * Registrar log de ação
     */
    protected function registrar_log($acao, $tabela, $registro_id = null, $dados_antigos = null, $dados_novos = null) {
        $this->load->model('Log_model');

        $dados_log = [
            'usuario_id' => $this->session->userdata('usuario_id'),
            'acao' => $acao,
            'tabela' => $tabela,
            'registro_id' => $registro_id,
            'ip' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ];

        // Adicionar dados antigos e novos se fornecidos
        if ($dados_antigos) {
            $dados_log['dados_antigos'] = is_object($dados_antigos) ? json_encode($dados_antigos) : json_encode($dados_antigos);
        }

        if ($dados_novos) {
            $dados_log['dados_novos'] = json_encode($dados_novos);
        }

        $this->Log_model->insert($dados_log);
    }
}
