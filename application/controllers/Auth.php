<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Autenticação
 * 
 * Gerencia login, logout e recuperação de senha
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->library('form_validation');
    }

    /**
     * Página de login
     */
    public function login() {
        // Se já estiver logado, redirecionar para admin
        if ($this->session->userdata('usuario_logado')) {
            redirect('admin/dashboard');
        }

        // Processar formulário
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('senha', 'Senha', 'required');

            if ($this->form_validation->run()) {
                $email = $this->input->post('email');
                $senha = $this->input->post('senha');
                $lembrar = $this->input->post('lembrar');

                $usuario = $this->Usuario_model->verificar_login($email, $senha);

                if ($usuario) {
                    // Criar sessão
                    $sessao_data = [
                        'usuario_id' => $usuario->id,
                        'usuario_nome' => $usuario->nome,
                        'usuario_email' => $usuario->email,
                        'usuario_nivel' => $usuario->nivel,
                        'usuario_avatar' => $usuario->avatar,
                        'usuario_logado' => true
                    ];

                    $this->session->set_userdata($sessao_data);

                    // Lembrar usuário (cookie)
                    if ($lembrar) {
                        set_cookie('usuario_email', $email, 86400 * 30); // 30 dias
                    }

                    // Registrar log
                    $this->registrar_log('login', 'usuarios', $usuario->id);

                    // Redirecionar
                    $redirect = $this->input->get('redirect') ?? 'admin/dashboard';
                    redirect($redirect);
                } else {
                    $this->session->set_flashdata('erro', 'E-mail ou senha incorretos.');
                }
            }
        }

        // Carregar view
        $data['titulo'] = 'Login - Le Cortine';
        $data['email_lembrado'] = get_cookie('usuario_email');
        
        $this->load->view('auth/login', $data);
    }

    /**
     * Logout
     */
    public function logout() {
        // Registrar log
        if ($this->session->userdata('usuario_id')) {
            $this->registrar_log('logout', 'usuarios', $this->session->userdata('usuario_id'));
        }

        // Destruir sessão
        $this->session->unset_userdata([
            'usuario_id',
            'usuario_nome',
            'usuario_email',
            'usuario_nivel',
            'usuario_avatar',
            'usuario_logado'
        ]);

        $this->session->set_flashdata('sucesso', 'Logout realizado com sucesso!');
        redirect('login');
    }

    /**
     * Recuperar senha
     */
    public function recuperar_senha() {
        // Se já estiver logado, redirecionar
        if ($this->session->userdata('usuario_logado')) {
            redirect('admin/dashboard');
        }

        // Processar formulário
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

            if ($this->form_validation->run()) {
                $email = $this->input->post('email');
                $token = $this->Usuario_model->gerar_token_recuperacao($email);

                if ($token) {
                    // Enviar e-mail com link de recuperação
                    $enviado = $this->enviar_email_recuperacao($email, $token);
                    
                    if ($enviado) {
                        $this->session->set_flashdata('sucesso', 'Instruções de recuperação enviadas para seu e-mail. Verifique também a pasta de spam.');
                        redirect('login');
                    } else {
                        log_message('error', 'Erro ao enviar e-mail de recuperação para: ' . $email);
                        $this->session->set_flashdata('erro', 'Erro ao enviar e-mail. Tente novamente ou contate o administrador.');
                    }
                } else {
                    $this->session->set_flashdata('erro', 'E-mail não encontrado.');
                }
            }
        }

        // Carregar view
        $data['titulo'] = 'Recuperar Senha - Le Cortine';
        $this->load->view('auth/recuperar_senha', $data);
    }

    /**
     * Resetar senha com token
     */
    public function resetar_senha($token = null) {
        if (!$token) {
            show_404();
        }

        // Verificar token
        $usuario = $this->Usuario_model->verificar_token($token);

        if (!$usuario) {
            $this->session->set_flashdata('erro', 'Token inválido ou expirado.');
            redirect('login');
        }

        // Processar formulário
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('senha', 'Nova senha', 'required|min_length[6]');
            $this->form_validation->set_rules('senha_confirma', 'Confirmação de senha', 'required|matches[senha]');

            if ($this->form_validation->run()) {
                $nova_senha = $this->input->post('senha');
                
                if ($this->Usuario_model->resetar_senha($token, $nova_senha)) {
                    $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso! Faça login com sua nova senha.');
                    redirect('login');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao resetar senha. Tente novamente.');
                }
            }
        }

        // Carregar view
        $data['titulo'] = 'Resetar Senha - Le Cortine';
        $data['token'] = $token;
        $data['usuario'] = $usuario;
        
        $this->load->view('auth/resetar_senha', $data);
    }

    /**
     * Enviar e-mail de recuperação
     */
    private function enviar_email_recuperacao($email, $token) {
        $this->load->library('email');
        
        // Configurações SMTP
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.lecortine.com.br',
            'smtp_port' => 465,
            'smtp_user' => 'nao-responder@lecortine.com.br',
            'smtp_pass' => 'a5)?O5qF+5!H@JaT2025',
            'smtp_crypto' => 'ssl',
            'smtp_timeout' => 30,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'crlf' => "\r\n",
            'wordwrap' => TRUE,
            'validate' => TRUE
        );
        
        $this->email->initialize($config);

        $link = base_url("auth/resetar_senha/{$token}");
        
        $mensagem = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 Recuperação de Senha</h1>
                    <p style="margin: 0;">Le Cortine - Sistema de Orçamentos</p>
                </div>
                <div class="content">
                    <p>Olá,</p>
                    <p>Você solicitou a recuperação de senha para sua conta no sistema Le Cortine.</p>
                    <p>Clique no botão abaixo para criar uma nova senha:</p>
                    <p style="text-align: center;">
                        <a href="' . $link . '" class="button">Criar Nova Senha</a>
                    </p>
                    <p>Ou copie e cole este link no navegador:</p>
                    <p style="word-break: break-all; background: #fff; padding: 10px; border-radius: 5px;">' . $link . '</p>
                    <div class="warning">
                        <strong>⏰ Atenção:</strong> Este link expira em 1 hora por questões de segurança.
                    </div>
                    <p><strong>Não solicitou esta recuperação?</strong><br>
                    Se você não solicitou a recuperação de senha, ignore este e-mail. Sua senha permanecerá inalterada.</p>
                </div>
            </div>
        </body>
        </html>';

        $this->email->from('nao-responder@lecortine.com.br', 'Le Cortine - Sistema');
        $this->email->to($email);
        $this->email->subject('🔐 Recuperação de Senha - Le Cortine');
        $this->email->message($mensagem);

        $enviado = $this->email->send();
        
        if (!$enviado) {
            log_message('error', 'Erro SMTP ao enviar email de recuperação: ' . $this->email->print_debugger());
        }
        
        return $enviado;
    }

    /**
     * Registrar log de ação
     */
    private function registrar_log($acao, $tabela, $registro_id) {
        $data = [
            'usuario_id' => $this->session->userdata('usuario_id'),
            'acao' => $acao,
            'tabela' => $tabela,
            'registro_id' => $registro_id,
            'ip' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'criado_em' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('logs', $data);
    }
}
