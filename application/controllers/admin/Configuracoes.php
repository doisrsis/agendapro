<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Configurações do Sistema
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 14/11/2024
 */
class Configuracoes extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Configuracao_model');
        $this->load->library('form_validation');
    }

    /**
     * Página principal de configurações
     */
    public function index() {
        redirect('admin/configuracoes/geral');
    }

    /**
     * Configurações Gerais
     */
    public function geral() {
        // Evitar cache
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Configurações Gerais';
        $data['menu_ativo'] = 'configuracoes';
        
        if ($this->input->method() === 'post') {
            $this->salvar_configuracoes('geral');
            return; // Importante: return após redirect
        }
        
        $data['configs'] = $this->Configuracao_model->get_by_grupo('geral');
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/configuracoes/geral', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Configurações dos Correios
     */
    public function correios() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Configurações dos Correios';
        $data['menu_ativo'] = 'configuracoes';
        
        if ($this->input->method() === 'post') {
            $this->salvar_configuracoes('correios');
            return;
        }
        
        $data['configs'] = $this->Configuracao_model->get_by_grupo('correios');
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/configuracoes/correios', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Configurações do Mercado Pago
     */
    public function mercadopago() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Configurações do Mercado Pago';
        $data['menu_ativo'] = 'configuracoes';
        
        if ($this->input->method() === 'post') {
            $this->salvar_configuracoes('mercadopago');
            return;
        }
        
        $data['configs'] = $this->Configuracao_model->get_by_grupo('mercadopago');
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/configuracoes/mercadopago', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Configurações de Notificações
     */
    public function notificacoes() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Configurações de Notificações';
        $data['menu_ativo'] = 'configuracoes';
        
        if ($this->input->method() === 'post') {
            $this->salvar_configuracoes('notificacoes');
            return;
        }
        
        $data['configs'] = $this->Configuracao_model->get_by_grupo('notificacoes');
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/configuracoes/notificacoes', $data);
        $this->load->view('admin/layout/footer');
    }
    
    /**
     * Testar envio de e-mail
     */
    public function testar_email() {
        
        try {
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
                'validate' => TRUE,
                'smtp_debug' => 2
            );
            
            // Carregar library de e-mail com configurações
            $this->load->library('email', $config);
            
            // Configurar remetente
            $this->email->from('nao-responder@lecortine.com.br', 'Le Cortine - Sistema de Orçamentos');
            
            // Destinatário
            $destinatario = $this->Configuracao_model->get_by_chave('email_destinatario');
            $email_destino = $destinatario ? $destinatario->valor : 'contato@lecortine.com.br';
            
            $this->email->to($email_destino);
            $this->email->subject('🧪 Teste de E-mail - Le Cortine');
            
            // Mensagem HTML
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
                        <p style="margin: 0;">Sistema de Orçamentos Le Cortine</p>
                    </div>
                    <div class="content">
                        <div class="success">
                            <h3 style="margin-top: 0;">✅ Configuração SMTP Funcionando!</h3>
                            <p>Se você está lendo este e-mail, significa que as configurações SMTP estão corretas e o sistema está pronto para enviar notificações.</p>
                        </div>
                        
                        <h3>📋 Informações do Teste:</h3>
                        <ul>
                            <li><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</li>
                            <li><strong>Servidor SMTP:</strong> mail.lecortine.com.br</li>
                            <li><strong>Porta:</strong> 465 (SSL)</li>
                            <li><strong>Remetente:</strong> nao-responder@lecortine.com.br</li>
                            <li><strong>Destinatário:</strong> ' . $email_destino . '</li>
                        </ul>
                        
                        <p style="margin-top: 30px; color: #666;">
                            <strong>Próximos passos:</strong><br>
                            ✅ Configure quais eventos deseja receber notificações<br>
                            ✅ Ative as notificações na página de configurações<br>
                            ✅ Teste fazendo um orçamento no sistema
                        </p>
                    </div>
                </div>
            </body>
            </html>';
            
            $this->email->message($mensagem);
            
            // Tentar enviar
            if ($this->email->send()) {
                $this->session->set_flashdata('sucesso', '✅ E-mail de teste enviado com sucesso para: ' . $email_destino . '. Verifique sua caixa de entrada (e spam também)!');
            } else {
                $erro = $this->email->print_debugger();
                log_message('error', 'Erro ao enviar e-mail de teste: ' . $erro);
                $this->session->set_flashdata('erro', 'Erro ao enviar e-mail. Verifique: <br>1. Credenciais SMTP<br>2. Porta 465 aberta<br>3. Firewall/Antivírus<br><br><small>' . nl2br(htmlspecialchars($erro)) . '</small>');
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exceção ao enviar e-mail: ' . $e->getMessage());
            $this->session->set_flashdata('erro', 'Exceção: ' . $e->getMessage());
        }
        
        redirect('admin/configuracoes/notificacoes');
    }

    /**
     * Salvar configurações
     */
    private function salvar_configuracoes($grupo) {
        $configs = $this->input->post('config');
        
        if (!$configs) {
            $this->session->set_flashdata('erro', 'Nenhuma configuração foi enviada.');
            redirect('admin/configuracoes/' . $grupo);
            return;
        }
        
        $sucesso = true;
        
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
        
        if ($sucesso) {
            $this->session->set_flashdata('sucesso', 'Configurações salvas com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao salvar algumas configurações.');
        }
        
        redirect('admin/configuracoes/' . $grupo);
    }

    /**
     * Testar conexão com Correios
     */
    public function testar_correios() {
        $this->load->library('Correios_lib');
        
        $resultado = $this->correios_lib->testar_conexao();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($resultado));
    }

    /**
     * Testar conexão com Mercado Pago
     */
    public function testar_mercadopago() {
        $this->load->library('Mercadopago_lib');
        
        $resultado = $this->mercadopago_lib->testar_conexao();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($resultado));
    }
}
