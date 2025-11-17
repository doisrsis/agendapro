<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Configurações do Sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 14/11/2024
 */
class Configuracoes extends Admin_Controller {

    protected $modulo_atual = 'configuracoes';

    public function __construct() {
        parent::__construct();
        $this->load->model('Configuracao_model');
        $this->load->library('form_validation');
    }

    /**
     * Página principal de configurações (com abas)
     */
    public function index() {
        // Evitar cache
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');

        $data['titulo'] = 'Configurações';
        $data['menu_ativo'] = 'configuracoes';
        $data['aba_ativa'] = $this->input->get('aba') ?? 'geral';

        if ($this->input->method() === 'post') {
            $grupo = $this->input->post('grupo') ?? 'geral';
            $this->salvar_configuracoes($grupo);
            redirect('admin/configuracoes?aba=' . $grupo);
            return;
        }

        // Buscar configurações de todos os grupos necessários
        $data['configs_geral'] = $this->Configuracao_model->get_by_grupo('geral');
        $data['configs_smtp'] = $this->Configuracao_model->get_by_grupo('smtp');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/configuracoes/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Redirecionar para aba SMTP (compatibilidade)
     */
    public function smtp() {
        redirect('admin/configuracoes?aba=smtp');
    }

    /**
     * Redirecionar para aba Geral (compatibilidade)
     */
    public function geral() {
        redirect('admin/configuracoes?aba=geral');
    }

    /**
     * Testar envio de e-mail com configurações dinâmicas
     */
    public function testar_email() {
        try {
            // Buscar configurações SMTP do banco
            $smtp_ativo = $this->Configuracao_model->get_by_chave('smtp_ativo');

            if (!$smtp_ativo || $smtp_ativo->valor != '1') {
                $this->session->set_flashdata('aviso', 'SMTP está desativado. Ative nas configurações antes de testar.');
                redirect('admin/configuracoes?aba=smtp');
                return;
            }

            // Montar configurações SMTP do banco
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => $this->Configuracao_model->get_valor('smtp_host'),
                'smtp_port' => $this->Configuracao_model->get_valor('smtp_porta'),
                'smtp_user' => $this->Configuracao_model->get_valor('smtp_usuario'),
                'smtp_pass' => $this->Configuracao_model->get_valor('smtp_senha'),
                'smtp_crypto' => $this->Configuracao_model->get_valor('smtp_seguranca'),
                'smtp_timeout' => 30,
                'mailtype' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n",
                'crlf' => "\r\n",
                'wordwrap' => TRUE,
                'validate' => TRUE,
                'smtp_debug' => 2
            );

            // Carregar library de e-mail
            $this->load->library('email', $config);

            // Configurar remetente
            $remetente_email = $this->Configuracao_model->get_valor('smtp_remetente_email');
            $remetente_nome = $this->Configuracao_model->get_valor('smtp_remetente_nome');
            $this->email->from($remetente_email, $remetente_nome);

            // Destinatário - sempre usar o e-mail do usuário logado
            $email_destino = $this->session->userdata('usuario_email');

            // Validar e-mail
            if (!$email_destino || !filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('erro', 'E-mail do usuário logado é inválido. Atualize seu perfil com um e-mail válido.');
                redirect('admin/configuracoes?aba=smtp');
                return;
            }

            $this->email->to($email_destino);
            $this->email->subject('🧪 Teste de E-mail - Dashboard Administrativo');

            // Mensagem HTML
            $sistema_nome = $this->Configuracao_model->get_valor('sistema_nome');
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
                    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>🧪 Teste de E-mail</h1>
                        <p style="margin: 0;">' . $sistema_nome . '</p>
                    </div>
                    <div class="content">
                        <div class="success">
                            <h3 style="margin-top: 0;">✅ Configuração SMTP Funcionando!</h3>
                            <p>Se você está lendo este e-mail, significa que as configurações SMTP estão corretas e o sistema está pronto para enviar notificações.</p>
                        </div>

                        <h3>📋 Informações do Teste:</h3>
                        <ul>
                            <li><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</li>
                            <li><strong>Servidor SMTP:</strong> ' . $config['smtp_host'] . '</li>
                            <li><strong>Porta:</strong> ' . $config['smtp_port'] . ' (' . strtoupper($config['smtp_crypto']) . ')</li>
                            <li><strong>Remetente:</strong> ' . $remetente_email . '</li>
                            <li><strong>Destinatário:</strong> ' . $email_destino . '</li>
                        </ul>

                        <p style="margin-top: 30px; color: #666;">
                            <strong>Sistema configurado e pronto para uso!</strong>
                        </p>
                    </div>
                </div>
            </body>
            </html>';

            $this->email->message($mensagem);

            // Tentar enviar
            if ($this->email->send()) {
                $this->session->set_flashdata('sucesso', '✅ E-mail de teste enviado com sucesso para: ' . $email_destino . '. Verifique sua caixa de entrada (e spam)!');
            } else {
                $erro = $this->email->print_debugger();
                log_message('error', 'Erro ao enviar e-mail de teste: ' . $erro);

                // Mensagem de erro mais detalhada
                $msg_erro = '<strong>Erro ao enviar e-mail.</strong><br><br>';
                $msg_erro .= '<strong>Verifique:</strong><br>';
                $msg_erro .= '1. Host e Porta estão corretos<br>';
                $msg_erro .= '2. Usuário e Senha estão corretos<br>';
                $msg_erro .= '3. Tipo de segurança (TLS/SSL) está correto<br>';
                $msg_erro .= '4. Seu provedor permite envio via SMTP<br><br>';
                $msg_erro .= '<details><summary>Detalhes técnicos (clique para expandir)</summary><pre style="font-size: 11px; max-height: 200px; overflow: auto;">' . htmlspecialchars($erro) . '</pre></details>';

                $this->session->set_flashdata('erro', $msg_erro);
            }

        } catch (Exception $e) {
            log_message('error', 'Exceção ao enviar e-mail: ' . $e->getMessage());
            $this->session->set_flashdata('erro', '<strong>Exceção:</strong> ' . $e->getMessage());
        }

        redirect('admin/configuracoes?aba=smtp');
    }

    /**
     * Salvar configurações
     */
    private function salvar_configuracoes($grupo) {
        $configs = $this->input->post('config');

        if (!$configs && empty($_FILES['sistema_logo']['name'])) {
            $this->session->set_flashdata('erro', 'Nenhuma configuração foi enviada.');
            redirect('admin/configuracoes?aba=' . $grupo);
            return;
        }

        $sucesso = true;

        // Processar upload de logo (apenas para grupo geral)
        if ($grupo == 'geral') {
            // Remover logo se solicitado
            if ($this->input->post('remover_logo')) {
                $logo_antiga = $this->Configuracao_model->get_by_chave('sistema_logo');
                if ($logo_antiga && $logo_antiga->valor) {
                    $caminho_logo = './assets/img/logo/' . $logo_antiga->valor;
                    if (file_exists($caminho_logo)) {
                        @unlink($caminho_logo);
                    }
                    $this->Configuracao_model->update_by_chave('sistema_logo', '');
                }
            }
            // Upload de nova logo
            elseif (!empty($_FILES['sistema_logo']['name'])) {
                $config['upload_path'] = './assets/img/logo/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('sistema_logo')) {
                    $upload_data = $this->upload->data();
                    $nome_arquivo = $upload_data['file_name'];

                    // Remover logo antiga se existir
                    $logo_antiga = $this->Configuracao_model->get_by_chave('sistema_logo');
                    if ($logo_antiga && $logo_antiga->valor) {
                        $caminho_logo_antiga = './assets/img/logo/' . $logo_antiga->valor;
                        if (file_exists($caminho_logo_antiga)) {
                            @unlink($caminho_logo_antiga);
                        }
                    }

                    // Salvar nome do arquivo no banco
                    $config_logo = $this->Configuracao_model->get_by_chave('sistema_logo');
                    if ($config_logo) {
                        $this->Configuracao_model->update_by_chave('sistema_logo', $nome_arquivo);
                    } else {
                        $this->Configuracao_model->insert([
                            'chave' => 'sistema_logo',
                            'valor' => $nome_arquivo,
                            'grupo' => 'geral',
                            'tipo' => 'arquivo'
                        ]);
                    }
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao fazer upload da logo: ' . $this->upload->display_errors('', ''));
                    $sucesso = false;
                }
            }
        }

        // Salvar outras configurações
        if ($configs) {
            foreach ($configs as $chave => $valor) {
            // Verificar se configuração existe
            $config = $this->Configuracao_model->get_by_chave($chave);

            if ($config) {
                // Atualizar
                if (!$this->Configuracao_model->update_by_chave($chave, $valor)) {
                    $sucesso = false;
                }
            } else {
                // Inserir nova configuração
                $dados = [
                    'chave' => $chave,
                    'valor' => $valor,
                    'grupo' => $grupo,
                    'tipo' => 'texto'
                ];

                if (!$this->Configuracao_model->insert($dados)) {
                    $sucesso = false;
                }
            }
            }
        }

        if ($sucesso) {
            $this->session->set_flashdata('sucesso', 'Configurações salvas com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao salvar algumas configurações.');
        }

        redirect('admin/configuracoes?aba=' . $grupo);
    }
}
