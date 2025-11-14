<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ============================================================================
 * EMAIL LIBRARY - LE CORTINE
 * ============================================================================
 * Autor: Rafael Dias - doisr.com.br
 * Data: 14/11/2024
 * Descrição: Library para envio de e-mails com templates
 * ============================================================================
 */

class Email_lib {
    
    protected $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
        
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
        
        $this->CI->load->library('email', $config);
        $this->CI->load->model('Configuracao_model');
    }
    
    /**
     * Envia e-mail de novo orçamento
     */
    public function enviar_novo_orcamento($orcamento_id, $dados_orcamento) {
        // Verificar se notificações por e-mail estão ativas
        if (!$this->notificacoes_ativas('email_novo_orcamento')) {
            return false;
        }
        
        $destinatario = $this->CI->Configuracao_model->get_by_chave('email_destinatario');
        
        if (!$destinatario) {
            log_message('error', 'E-mail destinatário não configurado');
            return false;
        }
        
        $assunto = "🎉 Novo Orçamento #" . $orcamento_id . " - Le Cortine";
        $mensagem = $this->template_novo_orcamento($orcamento_id, $dados_orcamento);
        
        return $this->enviar($destinatario->valor, $assunto, $mensagem);
    }
    
    /**
     * Envia e-mail de pagamento aprovado
     */
    public function enviar_pagamento_aprovado($orcamento_id, $dados_pagamento) {
        // Verificar se notificações por e-mail estão ativas
        if (!$this->notificacoes_ativas('email_pagamento_aprovado')) {
            return false;
        }
        
        $destinatario = $this->CI->Configuracao_model->get_by_chave('email_destinatario');
        
        if (!$destinatario) {
            log_message('error', 'E-mail destinatário não configurado');
            return false;
        }
        
        $assunto = "💰 Pagamento Aprovado - Orçamento #" . $orcamento_id;
        $mensagem = $this->template_pagamento_aprovado($orcamento_id, $dados_pagamento);
        
        return $this->enviar($destinatario->valor, $assunto, $mensagem);
    }
    
    /**
     * Envia e-mail de confirmação para o cliente
     */
    public function enviar_confirmacao_cliente($email_cliente, $orcamento_id, $dados_orcamento) {
        $assunto = "Seu Orçamento #" . $orcamento_id . " - Le Cortine";
        $mensagem = $this->template_confirmacao_cliente($orcamento_id, $dados_orcamento);
        
        return $this->enviar($email_cliente, $assunto, $mensagem);
    }
    
    /**
     * Método genérico para enviar e-mail
     */
    private function enviar($destinatario, $assunto, $mensagem) {
        try {
            $this->CI->email->clear();
            $this->CI->email->from('nao-responder@lecortine.com.br', 'Le Cortine - Sistema de Orçamentos');
            $this->CI->email->to($destinatario);
            $this->CI->email->subject($assunto);
            $this->CI->email->message($mensagem);
            
            if ($this->CI->email->send()) {
                log_message('info', "E-mail enviado para: {$destinatario}");
                return true;
            } else {
                log_message('error', "Erro ao enviar e-mail: " . $this->CI->email->print_debugger());
                return false;
            }
        } catch (Exception $e) {
            log_message('error', "Exceção ao enviar e-mail: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se notificações estão ativas
     */
    private function notificacoes_ativas($tipo) {
        $email_ativo = $this->CI->Configuracao_model->get_by_chave('email_notificacoes_ativo');
        $tipo_ativo = $this->CI->Configuracao_model->get_by_chave($tipo);
        
        return ($email_ativo && $email_ativo->valor == '1') && 
               ($tipo_ativo && $tipo_ativo->valor == '1');
    }
    
    /**
     * Template: Novo Orçamento
     */
    private function template_novo_orcamento($orcamento_id, $dados) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }
                .info-row { margin: 10px 0; }
                .label { font-weight: bold; color: #667eea; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Novo Orçamento Recebido!</h1>
                    <p style="margin: 0; font-size: 18px;">Orçamento #' . $orcamento_id . '</p>
                </div>
                <div class="content">
                    <p>Olá! Você recebeu um novo orçamento através do sistema.</p>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #667eea;">📋 Dados do Cliente</h3>
                        <div class="info-row"><span class="label">Nome:</span> ' . $dados['nome'] . '</div>
                        <div class="info-row"><span class="label">E-mail:</span> ' . $dados['email'] . '</div>
                        <div class="info-row"><span class="label">Telefone:</span> ' . $dados['telefone'] . '</div>
                    </div>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #667eea;">🛍️ Detalhes do Pedido</h3>
                        <div class="info-row"><span class="label">Produto:</span> ' . $dados['produto'] . '</div>
                        <div class="info-row"><span class="label">Tecido:</span> ' . $dados['tecido'] . '</div>
                        <div class="info-row"><span class="label">Dimensões:</span> ' . $dados['largura'] . ' x ' . $dados['altura'] . '</div>
                        ' . (isset($dados['tipo_entrega']) && $dados['tipo_entrega'] == 'retirada' ? 
                            '<div class="info-row"><span class="label">Entrega:</span> 🏪 Retirada no Local</div>' : 
                            '<div class="info-row"><span class="label">Entrega:</span> 🚚 ' . $dados['cidade'] . '/' . $dados['estado'] . '</div>') . '
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="' . base_url('admin/orcamentos/ver/' . $orcamento_id) . '" class="button">
                            Ver Orçamento Completo
                        </a>
                    </div>
                    
                    <p style="margin-top: 30px; color: #666;">
                        <strong>Próximos passos:</strong><br>
                        1. Acesse o painel admin<br>
                        2. Revise os detalhes do orçamento<br>
                        3. Entre em contato com o cliente via WhatsApp
                    </p>
                </div>
                <div class="footer">
                    <p>Le Cortine - Sistema de Orçamentos<br>
                    Este é um e-mail automático, não responda.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Template: Pagamento Aprovado
     */
    private function template_pagamento_aprovado($orcamento_id, $dados) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #11998e; }
                .info-row { margin: 10px 0; }
                .label { font-weight: bold; color: #11998e; }
                .valor { font-size: 24px; color: #11998e; font-weight: bold; }
                .button { display: inline-block; background: #11998e; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>💰 Pagamento Aprovado!</h1>
                    <p style="margin: 0; font-size: 18px;">Orçamento #' . $orcamento_id . '</p>
                </div>
                <div class="content">
                    <p>Ótimas notícias! Um pagamento foi aprovado.</p>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #11998e;">💳 Informações do Pagamento</h3>
                        <div class="info-row"><span class="label">Valor:</span> <span class="valor">R$ ' . number_format($dados['valor'], 2, ',', '.') . '</span></div>
                        <div class="info-row"><span class="label">Método:</span> ' . $dados['metodo'] . '</div>
                        <div class="info-row"><span class="label">Status:</span> ✅ Aprovado</div>
                        <div class="info-row"><span class="label">Data:</span> ' . date('d/m/Y H:i') . '</div>
                    </div>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #11998e;">👤 Cliente</h3>
                        <div class="info-row"><span class="label">Nome:</span> ' . $dados['cliente_nome'] . '</div>
                        <div class="info-row"><span class="label">E-mail:</span> ' . $dados['cliente_email'] . '</div>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="' . base_url('admin/orcamentos/ver/' . $orcamento_id) . '" class="button">
                            Ver Detalhes do Pedido
                        </a>
                    </div>
                    
                    <p style="margin-top: 30px; color: #666;">
                        <strong>Próximos passos:</strong><br>
                        1. Confirme o pedido com o cliente<br>
                        2. Inicie a produção<br>
                        3. Agende a entrega/instalação
                    </p>
                </div>
                <div class="footer">
                    <p>Le Cortine - Sistema de Orçamentos<br>
                    Este é um e-mail automático, não responda.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Template: Confirmação para Cliente
     */
    private function template_confirmacao_cliente($orcamento_id, $dados) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
                .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Orçamento Recebido!</h1>
                    <p style="margin: 0;">Obrigado por escolher a Le Cortine</p>
                </div>
                <div class="content">
                    <p>Olá <strong>' . $dados['nome'] . '</strong>,</p>
                    <p>Recebemos seu pedido de orçamento com sucesso!</p>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #667eea;">📋 Seu Orçamento</h3>
                        <p><strong>Número:</strong> #' . $orcamento_id . '</p>
                        <p><strong>Produto:</strong> ' . $dados['produto'] . '</p>
                        <p><strong>Tecido:</strong> ' . $dados['tecido'] . '</p>
                    </div>
                    
                    <p>Nossa equipe entrará em contato em breve via WhatsApp para:</p>
                    <ul>
                        <li>Confirmar os detalhes do seu pedido</li>
                        <li>Calcular o valor do frete</li>
                        <li>Enviar o orçamento final</li>
                        <li>Agendar medição (se necessário)</li>
                    </ul>
                    
                    <p style="margin-top: 30px;">
                        <strong>Dúvidas?</strong><br>
                        WhatsApp: (75) 98889-0006<br>
                        E-mail: contato@lecortine.com.br
                    </p>
                </div>
                <div class="footer">
                    <p>Le Cortine - Cortinas e Persianas<br>
                    www.lecortine.com.br</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
