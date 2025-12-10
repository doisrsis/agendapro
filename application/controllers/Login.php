<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Login
 * Descrição: Autenticação multi-tenant do sistema
 *
 * Gerencia login/logout para todos os tipos de usuários:
 * - super_admin → /admin/dashboard
 * - estabelecimento → /painel/dashboard
 * - profissional → /agenda/dashboard
 *
 * @author Rafael Dias - doisr.com.br
 * @date 10/12/2024
 */
class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->library('auth_check');
        $this->load->library('form_validation');
    }

    /**
     * Página de login
     */
    public function index() {
        // Se já estiver logado, redirecionar para painel
        if ($this->auth_check->check_login(false)) {
            $this->auth_check->redirecionar_painel();
            return;
        }

        // Processar formulário se for POST
        if ($this->input->method() === 'post') {
            $this->processar_login();
            return;
        }

        // Verificar se há cookie "lembrar-me"
        $email_lembrado = $this->input->cookie('email_lembrado');

        // Exibir formulário de login
        $data['titulo'] = 'Login - ' . get_nome_sistema();
        $data['email_lembrado'] = $email_lembrado;

        $this->load->view('auth/login', $data);
    }

    /**
     * Processar login
     */
    private function processar_login() {
        // Validação
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('senha', 'Senha', 'required');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('login');
            return;
        }

        $email = $this->input->post('email');
        $senha = $this->input->post('senha');
        $lembrar = $this->input->post('lembrar');

        // Tentar autenticar
        $usuario = $this->Usuario_model->autenticar($email, $senha);

        if (!$usuario) {
            $this->session->set_flashdata('erro', 'E-mail ou senha inválidos.');
            redirect('login');
            return;
        }

        // Verificar se usuário está ativo
        if ($usuario->ativo != 1) {
            $this->session->set_flashdata('erro', 'Sua conta está inativa. Entre em contato com o suporte.');
            redirect('login');
            return;
        }

        // Fazer login
        $this->auth_check->fazer_login($usuario);

        // Salvar cookie "lembrar-me" se marcado
        if ($lembrar) {
            $this->input->set_cookie([
                'name' => 'email_lembrado',
                'value' => $email,
                'expire' => 60 * 60 * 24 * 30 // 30 dias
            ]);
        } else {
            // Remover cookie se desmarcado
            delete_cookie('email_lembrado');
        }

        // Registrar log
        $this->registrar_log('login', 'usuarios', $usuario->id);

        // Verificar se há URL de redirecionamento
        $redirect = $this->input->get('redirect');
        if ($redirect) {
            redirect($redirect);
            return;
        }

        // Redirecionar para painel correto
        $this->auth_check->redirecionar_painel();
    }

    /**
     * Logout
     */
    public function logout() {
        // Registrar log antes de fazer logout
        if ($this->auth_check->check_login(false)) {
            $this->registrar_log('logout', 'usuarios', $this->auth_check->get_usuario_id());
        }

        // Fazer logout
        $this->auth_check->fazer_logout();

        $this->session->set_flashdata('sucesso', 'Você saiu do sistema com sucesso.');
        redirect('login');
    }

    /**
     * Recuperar senha
     */
    public function recuperar_senha() {
        // Se já estiver logado, redirecionar
        if ($this->auth_check->check_login(false)) {
            $this->auth_check->redirecionar_painel();
            return;
        }

        // Processar formulário se for POST
        if ($this->input->method() === 'post') {
            $this->processar_recuperar_senha();
            return;
        }

        // Exibir formulário
        $data['titulo'] = 'Recuperar Senha';
        $this->load->view('auth/recuperar_senha', $data);
    }

    /**
     * Processar recuperação de senha
     */
    private function processar_recuperar_senha() {
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('recuperar-senha');
            return;
        }

        $email = $this->input->post('email');

        // Gerar token de reset
        $token = $this->Usuario_model->gerar_token_reset($email);

        if ($token) {
            // Enviar e-mail com link de reset
            $this->load->library('email_lib');

            $link = base_url('resetar-senha/' . $token);

            $assunto = 'Recuperação de Senha - ' . get_nome_sistema();
            $mensagem = "
                <h2>Recuperação de Senha</h2>
                <p>Você solicitou a recuperação de senha.</p>
                <p>Clique no link abaixo para criar uma nova senha:</p>
                <p><a href='{$link}' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Redefinir Senha</a></p>
                <p>Ou copie e cole este link no navegador:</p>
                <p>{$link}</p>
                <p><strong>Este link é válido por 1 hora.</strong></p>
                <p>Se você não solicitou esta recuperação, ignore este e-mail.</p>
            ";

            if ($this->email_lib->enviar($email, $assunto, $mensagem)) {
                $this->session->set_flashdata('sucesso', 'E-mail de recuperação enviado! Verifique sua caixa de entrada.');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao enviar e-mail. Tente novamente mais tarde.');
            }
        } else {
            // Por segurança, não informar se e-mail existe ou não
            $this->session->set_flashdata('sucesso', 'Se o e-mail estiver cadastrado, você receberá as instruções de recuperação.');
        }

        redirect('recuperar-senha');
    }

    /**
     * Resetar senha
     */
    public function resetar_senha($token = null) {
        if (!$token) {
            show_404();
            return;
        }

        // Validar token
        $usuario = $this->Usuario_model->validar_token_reset($token);

        if (!$usuario) {
            $this->session->set_flashdata('erro', 'Link de recuperação inválido ou expirado.');
            redirect('login');
            return;
        }

        // Processar formulário se for POST
        if ($this->input->method() === 'post') {
            $this->processar_resetar_senha($usuario->id, $token);
            return;
        }

        // Exibir formulário
        $data['titulo'] = 'Nova Senha';
        $data['token'] = $token;
        $this->load->view('auth/resetar_senha', $data);
    }

    /**
     * Processar reset de senha
     */
    private function processar_resetar_senha($usuario_id, $token) {
        $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');
        $this->form_validation->set_rules('senha_confirmar', 'Confirmação de Senha', 'required|matches[senha]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('resetar-senha/' . $token);
            return;
        }

        $nova_senha = $this->input->post('senha');

        // Atualizar senha
        if ($this->Usuario_model->atualizar_senha($usuario_id, $nova_senha)) {
            // Limpar token
            $this->Usuario_model->limpar_token_reset($usuario_id);

            // Registrar log
            $this->registrar_log('reset_senha', 'usuarios', $usuario_id);

            $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso! Faça login com sua nova senha.');
            redirect('login');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao alterar senha. Tente novamente.');
            redirect('resetar-senha/' . $token);
        }
    }

    /**
     * Registrar log de ação
     */
    private function registrar_log($acao, $tabela, $registro_id) {
        $this->load->model('Log_model');

        $dados = [
            'usuario_id' => $registro_id,
            'acao' => $acao,
            'tabela' => $tabela,
            'registro_id' => $registro_id,
            'ip' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'criado_em' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('logs', $dados);
    }
}
