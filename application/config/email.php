<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| ============================================================================
| CONFIGURAÇÕES DE E-MAIL - LE CORTINE
| ============================================================================
| Autor: Rafael Dias - doisr.com.br
| Data: 14/11/2024
| Descrição: Configurações SMTP para envio de e-mails
| ============================================================================
*/

$config['protocol'] = 'smtp';
$config['smtp_host'] = 'mail.lecortine.com.br';
$config['smtp_port'] = 465;
$config['smtp_user'] = 'nao-responder@lecortine.com.br';
$config['smtp_pass'] = 'a5)?O5qF+5!H@JaT2025';
$config['smtp_crypto'] = 'ssl';

// Configurações adicionais
$config['smtp_timeout'] = 30;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";
$config['wordwrap'] = TRUE;

// Remetente padrão
$config['from_email'] = 'nao-responder@lecortine.com.br';
$config['from_name'] = 'Le Cortine - Sistema de Orçamentos';

// Validação
$config['validate'] = TRUE;

// Debug (desative em produção)
$config['smtp_debug'] = 2; // 0 = desligado, 1 = erros, 2 = mensagens, 3 = detalhado, 4 = completo

/*
| ============================================================================
| INFORMAÇÕES DO SERVIDOR
| ============================================================================
| 
| Servidor de entrada: mail.lecortine.com.br
| - IMAP Port: 993
| - POP3 Port: 995
| 
| Servidor de saída: mail.lecortine.com.br
| - SMTP Port: 465 (SSL)
| 
| Autenticação: Obrigatória
| 
| ============================================================================
*/
