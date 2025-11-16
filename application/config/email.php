<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| ============================================================================
| CONFIGURAÇÕES DE E-MAIL - PROJETO BASE
| ============================================================================
| Autor: Rafael Dias - doisr.com.br
| Data: 16/11/2024
| Descrição: Configurações SMTP para envio de e-mails
|
| IMPORTANTE: As configurações SMTP são gerenciadas dinamicamente pelo
| sistema através do painel administrativo em:
| Admin > Configurações > SMTP
|
| Este arquivo contém apenas as configurações padrão do CodeIgniter.
| As configurações reais são carregadas do banco de dados.
| ============================================================================
*/

// Configurações padrão (serão sobrescritas pelas do banco de dados)
$config['protocol'] = 'smtp';
$config['smtp_timeout'] = 30;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";
$config['wordwrap'] = TRUE;
$config['validate'] = TRUE;

// Debug (0 = desligado, 1 = erros, 2 = mensagens, 3 = detalhado, 4 = completo)
$config['smtp_debug'] = 0;

/*
| ============================================================================
| COMO CONFIGURAR O SMTP
| ============================================================================
|
| 1. Acesse o painel administrativo
| 2. Vá em: Configurações > SMTP
| 3. Preencha os dados do seu servidor SMTP
| 4. Ative o SMTP
| 5. Teste o envio de e-mail
|
| Exemplos de configuração:
|
| GMAIL:
| - Host: smtp.gmail.com
| - Porta: 587
| - Segurança: TLS
|
| OUTLOOK:
| - Host: smtp-mail.outlook.com
| - Porta: 587
| - Segurança: TLS
|
| SERVIDOR PRÓPRIO (cPanel):
| - Host: mail.seudominio.com.br
| - Porta: 465
| - Segurança: SSL
|
| ============================================================================
*/
