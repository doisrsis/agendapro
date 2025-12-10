<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library: Auth_middleware
 * Descrição: Middleware para verificação de autenticação e permissões
 *
 * @author Rafael Dias - doisr.com.br
 * @date 09/12/2024
 */
class Auth_middleware {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('Usuario_model');
        $this->CI->load->model('Estabelecimento_model');
        $this->CI->load->model('Assinatura_model');
    }

    /**
     * Verificar se usuário está logado
     *
     * @return bool
     */
    public function esta_logado() {
        return $this->CI->session->userdata('logado') === true;
    }

    /**
     * Obter ID do usuário logado
     *
     * @return int|null
     */
    public function get_usuario_id() {
        return $this->CI->session->userdata('usuario_id');
    }

    /**
     * Obter tipo do usuário logado
     *
     * @return string|null
     */
    public function get_usuario_tipo() {
        return $this->CI->session->userdata('usuario_tipo');
    }

    /**
     * Obter ID do estabelecimento do usuário logado
     *
     * @return int|null
     */
    public function get_estabelecimento_id() {
        return $this->CI->session->userdata('estabelecimento_id');
    }

    /**
     * Obter dados completos do usuário logado
     *
     * @return object|null
     */
    public function get_usuario() {
        $usuario_id = $this->get_usuario_id();

        if (!$usuario_id) {
            return null;
        }

        return $this->CI->Usuario_model->get($usuario_id);
    }

    /**
     * Verificar acesso - redireciona para login se não autenticado
     *
     * @param string|array $tipos_permitidos Tipo(s) de usuário permitido(s)
     * @return void
     */
    public function verificar_acesso($tipos_permitidos = null) {
        // Verificar se está logado
        if (!$this->esta_logado()) {
            $this->CI->session->set_flashdata('erro', 'Você precisa fazer login para acessar esta página.');
            redirect('login');
        }

        // Verificar tipo de usuário
        if ($tipos_permitidos) {
            if (!is_array($tipos_permitidos)) {
                $tipos_permitidos = [$tipos_permitidos];
            }

            $tipo_usuario = $this->get_usuario_tipo();

            if (!in_array($tipo_usuario, $tipos_permitidos)) {
                show_error('Acesso negado. Você não tem permissão para acessar esta página.', 403);
            }
        }

        // Verificar se estabelecimento está ativo (apenas para estabelecimento e profissional)
        $tipo_usuario = $this->get_usuario_tipo();

        if (in_array($tipo_usuario, ['estabelecimento', 'profissional'])) {
            $this->verificar_estabelecimento_ativo();
        }
    }

    /**
     * Verificar se estabelecimento está ativo e assinatura válida
     *
     * @return void
     */
    private function verificar_estabelecimento_ativo() {
        $estabelecimento_id = $this->get_estabelecimento_id();

        if (!$estabelecimento_id) {
            show_error('Estabelecimento não encontrado.', 404);
        }

        $estabelecimento = $this->CI->Estabelecimento_model->get($estabelecimento_id);

        if (!$estabelecimento) {
            show_error('Estabelecimento não encontrado.', 404);
        }

        // Verificar status do estabelecimento
        if ($estabelecimento->status === 'suspenso') {
            $this->CI->session->set_flashdata('erro', 'Sua conta está suspensa. Entre em contato com o suporte.');
            redirect('painel/suspenso');
        }

        if ($estabelecimento->status === 'cancelado') {
            $this->CI->session->set_flashdata('erro', 'Sua conta foi cancelada.');
            redirect('painel/cancelado');
        }

        // Verificar assinatura
        if (!$this->CI->Assinatura_model->esta_ativa($estabelecimento_id)) {
            $this->CI->session->set_flashdata('erro', 'Sua assinatura expirou. Renove para continuar usando o sistema.');
            redirect('painel/assinatura-expirada');
        }
    }

    /**
     * Verificar se é super admin
     *
     * @return bool
     */
    public function is_super_admin() {
        return $this->get_usuario_tipo() === 'super_admin';
    }

    /**
     * Verificar se é estabelecimento
     *
     * @return bool
     */
    public function is_estabelecimento() {
        return $this->get_usuario_tipo() === 'estabelecimento';
    }

    /**
     * Verificar se é profissional
     *
     * @return bool
     */
    public function is_profissional() {
        return $this->get_usuario_tipo() === 'profissional';
    }

    /**
     * Verificar se usuário pode acessar recurso de um estabelecimento
     *
     * @param int $estabelecimento_id
     * @return bool
     */
    public function pode_acessar_estabelecimento($estabelecimento_id) {
        // Super admin pode acessar tudo
        if ($this->is_super_admin()) {
            return true;
        }

        // Verificar se o estabelecimento_id do usuário corresponde
        return $this->get_estabelecimento_id() == $estabelecimento_id;
    }

    /**
     * Verificar se pode criar mais profissionais (limite do plano)
     *
     * @param int $estabelecimento_id
     * @return bool
     */
    public function pode_criar_profissional($estabelecimento_id) {
        $assinatura = $this->CI->Assinatura_model->get_ativa($estabelecimento_id);

        if (!$assinatura) {
            return false;
        }

        // Contar profissionais atuais
        $this->CI->load->model('Profissional_model');
        $total_profissionais = $this->CI->Profissional_model->count_by_estabelecimento($estabelecimento_id);

        // Verificar limite
        $this->CI->load->model('Plano_model');
        return $this->CI->Plano_model->pode_criar_profissional($assinatura->plano_id, $total_profissionais);
    }

    /**
     * Verificar se pode criar mais agendamentos (limite do plano)
     *
     * @param int $estabelecimento_id
     * @return bool
     */
    public function pode_criar_agendamento($estabelecimento_id) {
        $assinatura = $this->CI->Assinatura_model->get_ativa($estabelecimento_id);

        if (!$assinatura) {
            return false;
        }

        // Contar agendamentos do mês atual
        $this->CI->load->model('Agendamento_model');
        $total_agendamentos = $this->CI->Agendamento_model->count_mes_atual($estabelecimento_id);

        // Verificar limite
        $this->CI->load->model('Plano_model');
        return $this->CI->Plano_model->pode_criar_agendamento($assinatura->plano_id, $total_agendamentos);
    }

    /**
     * Verificar se plano tem recurso específico
     *
     * @param string $recurso
     * @return bool
     */
    public function tem_recurso($recurso) {
        $estabelecimento_id = $this->get_estabelecimento_id();

        if (!$estabelecimento_id) {
            return false;
        }

        $assinatura = $this->CI->Assinatura_model->get_ativa($estabelecimento_id);

        if (!$assinatura) {
            return false;
        }

        $this->CI->load->model('Plano_model');
        return $this->CI->Plano_model->tem_recurso($assinatura->plano_id, $recurso);
    }

    /**
     * Fazer login do usuário
     *
     * @param object $usuario
     * @return void
     */
    public function fazer_login($usuario) {
        $sessao = [
            'usuario_id' => $usuario->id,
            'usuario_tipo' => $usuario->tipo,
            'usuario_nome' => $usuario->nome,
            'usuario_email' => $usuario->email,
            'estabelecimento_id' => $usuario->estabelecimento_id,
            'profissional_id' => $usuario->profissional_id,
            'logado' => true
        ];

        $this->CI->session->set_userdata($sessao);
    }

    /**
     * Fazer logout do usuário
     *
     * @return void
     */
    public function fazer_logout() {
        $this->CI->session->unset_userdata([
            'usuario_id',
            'usuario_tipo',
            'usuario_nome',
            'usuario_email',
            'estabelecimento_id',
            'profissional_id',
            'logado'
        ]);

        $this->CI->session->sess_destroy();
    }

    /**
     * Redirecionar para painel correto baseado no tipo de usuário
     *
     * @return void
     */
    public function redirecionar_painel() {
        $tipo = $this->get_usuario_tipo();

        switch ($tipo) {
            case 'super_admin':
                redirect('admin/dashboard');
                break;
            case 'estabelecimento':
                redirect('painel/dashboard');
                break;
            case 'profissional':
                redirect('agenda/dashboard');
                break;
            default:
                redirect('login');
        }
    }
}
