<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Biblioteca de Verificação de Autenticação Multi-Tenant
 *
 * Middleware para proteger rotas e gerenciar autenticação multi-tenant
 *
 * @author Rafael Dias - doisr.com.br
 * @date 10/12/2024
 */
class Auth_check {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('Usuario_model');
        $this->CI->load->model('Estabelecimento_model');
        $this->CI->load->model('Assinatura_model');
    }

    /**
     * Verificar se usuário está logado
     */
    public function check_login($redirect = true) {
        if (!$this->CI->session->userdata('usuario_logado')) {
            if ($redirect) {
                $current_url = current_url();
                $this->CI->session->set_flashdata('erro', 'Você precisa estar logado para acessar esta página.');
                redirect('login?redirect=' . urlencode($current_url));
            }
            return false;
        }
        return true;
    }

    /**
     * Verificar nível de acesso (compatibilidade com código antigo)
     */
    public function check_nivel($niveis_permitidos = [], $redirect = true) {
        // Primeiro verificar se está logado
        if (!$this->check_login($redirect)) {
            return false;
        }

        $nivel_usuario = $this->CI->session->userdata('usuario_nivel');

        if (!in_array($nivel_usuario, $niveis_permitidos)) {
            if ($redirect) {
                $this->CI->session->set_flashdata('erro', 'Você não tem permissão para acessar esta página.');
                redirect('admin/dashboard');
            }
            return false;
        }

        return true;
    }

    /**
     * Verificar tipo de usuário (multi-tenant)
     *
     * @param string|array $tipos_permitidos Tipo(s) permitido(s): super_admin, estabelecimento, profissional
     * @param bool $redirect
     * @return bool
     */
    public function check_tipo($tipos_permitidos = [], $redirect = true) {
        // Primeiro verificar se está logado
        if (!$this->check_login($redirect)) {
            return false;
        }

        if (!is_array($tipos_permitidos)) {
            $tipos_permitidos = [$tipos_permitidos];
        }

        $tipo_usuario = $this->CI->session->userdata('usuario_tipo');

        if (!in_array($tipo_usuario, $tipos_permitidos)) {
            if ($redirect) {
                $this->CI->session->set_flashdata('erro', 'Você não tem permissão para acessar esta página.');

                // Redirecionar para painel correto
                switch ($tipo_usuario) {
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
            return false;
        }

        return true;
    }

    /**
     * Verificar se é admin (compatibilidade)
     */
    public function is_admin() {
        $nivel = $this->CI->session->userdata('usuario_nivel');
        $tipo = $this->CI->session->userdata('usuario_tipo');

        return $nivel === 'admin' || $tipo === 'super_admin';
    }

    /**
     * Verificar se é super admin (multi-tenant)
     */
    public function is_super_admin() {
        return $this->CI->session->userdata('usuario_tipo') === 'super_admin';
    }

    /**
     * Verificar se é estabelecimento
     */
    public function is_estabelecimento() {
        return $this->CI->session->userdata('usuario_tipo') === 'estabelecimento';
    }

    /**
     * Verificar se é profissional
     */
    public function is_profissional() {
        return $this->CI->session->userdata('usuario_tipo') === 'profissional';
    }

    /**
     * Verificar se é gerente ou admin (compatibilidade)
     */
    public function is_gerente_ou_admin() {
        $nivel = $this->CI->session->userdata('usuario_nivel');
        return in_array($nivel, ['admin', 'gerente']);
    }

    /**
     * Obter dados do usuário logado
     */
    public function get_usuario() {
        if (!$this->check_login(false)) {
            return null;
        }

        return (object) [
            'id' => $this->CI->session->userdata('usuario_id'),
            'nome' => $this->CI->session->userdata('usuario_nome'),
            'email' => $this->CI->session->userdata('usuario_email'),
            'tipo' => $this->CI->session->userdata('usuario_tipo'),
            'nivel' => $this->CI->session->userdata('usuario_nivel'), // Compatibilidade
            'estabelecimento_id' => $this->CI->session->userdata('estabelecimento_id'),
            'profissional_id' => $this->CI->session->userdata('profissional_id'),
            'avatar' => $this->CI->session->userdata('usuario_avatar')
        ];
    }

    /**
     * Obter ID do usuário logado
     */
    public function get_usuario_id() {
        return $this->CI->session->userdata('usuario_id');
    }

    /**
     * Obter nome do usuário logado
     */
    public function get_usuario_nome() {
        return $this->CI->session->userdata('usuario_nome');
    }

    /**
     * Obter nível do usuário logado (compatibilidade)
     */
    public function get_usuario_nivel() {
        return $this->CI->session->userdata('usuario_nivel');
    }

    /**
     * Obter tipo do usuário logado (multi-tenant)
     */
    public function get_usuario_tipo() {
        return $this->CI->session->userdata('usuario_tipo');
    }

    /**
     * Obter ID do estabelecimento do usuário logado
     */
    public function get_estabelecimento_id() {
        return $this->CI->session->userdata('estabelecimento_id');
    }

    /**
     * Obter ID do profissional do usuário logado
     */
    public function get_profissional_id() {
        return $this->CI->session->userdata('profissional_id');
    }

    /**
     * Verificar se estabelecimento está ativo e assinatura válida
     */
    public function verificar_estabelecimento_ativo() {
        $estabelecimento_id = $this->get_estabelecimento_id();

        if (!$estabelecimento_id) {
            return true; // Super admin não tem estabelecimento
        }

        $estabelecimento = $this->CI->Estabelecimento_model->get($estabelecimento_id);

        if (!$estabelecimento) {
            $this->CI->session->set_flashdata('erro', 'Estabelecimento não encontrado.');
            redirect('login');
            return false;
        }

        // Verificar status do estabelecimento
        if ($estabelecimento->status === 'suspenso') {
            $this->CI->session->set_flashdata('erro', 'Sua conta está suspensa. Entre em contato com o suporte.');
            redirect('painel/suspenso');
            return false;
        }

        if ($estabelecimento->status === 'cancelado') {
            $this->CI->session->set_flashdata('erro', 'Sua conta foi cancelada.');
            redirect('painel/cancelado');
            return false;
        }

        // Verificar assinatura
        if (!$this->CI->Assinatura_model->esta_ativa($estabelecimento_id)) {
            $this->CI->session->set_flashdata('erro', 'Sua assinatura expirou. Renove para continuar usando o sistema.');
            redirect('painel/assinatura_expirada');
            return false;
        }

        return true;
    }

    /**
     * Verificar se pode criar profissional (limite do plano)
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
     * Verificar se pode criar agendamento (limite do plano)
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
     */
    public function tem_recurso($recurso, $estabelecimento_id = null) {
        if (!$estabelecimento_id) {
            $estabelecimento_id = $this->get_estabelecimento_id();
        }

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
     */
    public function fazer_login($usuario) {
        $sessao = [
            'usuario_id' => $usuario->id,
            'usuario_tipo' => $usuario->tipo,
            'usuario_nome' => $usuario->nome ?? $usuario->email,
            'usuario_email' => $usuario->email,
            'usuario_nivel' => $usuario->tipo === 'super_admin' ? 'admin' : 'usuario', // Compatibilidade
            'estabelecimento_id' => $usuario->estabelecimento_id ?? null,
            'profissional_id' => $usuario->profissional_id ?? null,
            'usuario_logado' => true
        ];

        $this->CI->session->set_userdata($sessao);
    }

    /**
     * Fazer logout do usuário
     */
    public function fazer_logout() {
        $this->CI->session->unset_userdata([
            'usuario_id',
            'usuario_tipo',
            'usuario_nome',
            'usuario_email',
            'usuario_nivel',
            'estabelecimento_id',
            'profissional_id',
            'usuario_logado'
        ]);

        $this->CI->session->sess_destroy();
    }

    /**
     * Redirecionar para painel correto baseado no tipo de usuário
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
