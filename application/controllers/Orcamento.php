<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Público de Orçamento - Le Cortine
 * 
 * Formulário multi-step conforme roteiro oficial
 * Fluxo: Dados → Tipo Atendimento → Produto → Tecido/Cor → Largura → Altura → Blackout → Endereço → Resumo
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024 20:35
 */
class Orcamento extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Orcamento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Produto_model');
        $this->load->model('Tecido_model');
        $this->load->model('Extra_model');
        $this->load->model('Preco_model');
        $this->load->library('session');
    }

    /**
     * Página inicial do formulário
     */
    public function index() {
        // Limpar sessão anterior
        $this->session->unset_userdata('orcamento_dados');
        redirect('orcamento/etapa1');
    }

    /**
     * Etapa 1: Dados do Cliente
     */
    public function etapa1() {
        $data['titulo'] = 'Solicite seu Orçamento - Le Cortine';
        $data['etapa_atual'] = 1;
        $data['total_etapas'] = 8;
        
        // Recuperar dados da sessão se existir
        $dados_sessao = $this->session->userdata('orcamento_dados');
        $data['dados'] = $dados_sessao ?? [];
        
        // Processar formulário
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('telefone', 'Telefone', 'required');
            $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'email' => $this->input->post('email'),
                    'telefone' => $this->input->post('telefone'),
                    'whatsapp' => $this->input->post('whatsapp')
                ];
                
                $this->session->set_userdata('orcamento_dados', $dados);
                redirect('orcamento/etapa2');
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa1', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 2: Tipo de Atendimento
     */
    public function etapa2() {
        if (!$this->session->userdata('orcamento_dados')) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Tipo de Atendimento - Le Cortine';
        $data['etapa_atual'] = 2;
        $data['total_etapas'] = 8;
        $dados_sessao = $this->session->userdata('orcamento_dados');
        $data['dados'] = $dados_sessao;
        
        if ($this->input->method() === 'post') {
            $tipo = $this->input->post('tipo_atendimento');
            
            if ($tipo === 'orcamento') {
                $dados_sessao['tipo_atendimento'] = 'orcamento';
                $this->session->set_userdata('orcamento_dados', $dados_sessao);
                redirect('orcamento/etapa3');
            } elseif ($tipo === 'consultoria') {
                $dados_sessao['tipo_atendimento'] = 'consultoria';
                $this->session->set_userdata('orcamento_dados', $dados_sessao);
                redirect('orcamento/consultoria');
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa2', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 3: Escolha do Produto
     */
    public function etapa3() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['tipo_atendimento'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Escolha o Produto - Le Cortine';
        $data['etapa_atual'] = 3;
        $data['total_etapas'] = 8;
        $data['dados'] = $dados_sessao;
        $data['produtos'] = $this->Produto_model->get_all(['status' => 'ativo']);
        
        if ($this->input->method() === 'post') {
            $produto_id = $this->input->post('produto_id');
            
            if ($produto_id) {
                // Produtos 4 e 5 (Toldos e Motorizadas) vão para consultoria
                if (in_array($produto_id, ['4', '5'])) {
                    $dados_sessao['produto_id'] = $produto_id;
                    $this->session->set_userdata('orcamento_dados', $dados_sessao);
                    redirect('orcamento/consultoria');
                } else {
                    $dados_sessao['produto_id'] = $produto_id;
                    $this->session->set_userdata('orcamento_dados', $dados_sessao);
                    redirect('orcamento/etapa4');
                }
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa3', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 4: Escolha de Tecido e Cor
     */
    public function etapa4() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['produto_id'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Escolha o Tecido e Cor';
        $data['etapa_atual'] = 4;
        $data['total_etapas'] = 8;
        $data['dados'] = $dados_sessao;
        $data['produto'] = $this->Produto_model->get($dados_sessao['produto_id']);
        
        // Buscar tecidos filtrados por produto
        $data['tecidos'] = $this->Tecido_model->get_all([
            'status' => 'ativo',
            'produto_id' => $dados_sessao['produto_id']
        ]);
        
        if ($this->input->method() === 'post') {
            $tecido_id = $this->input->post('tecido_id');
            $cor_id = $this->input->post('cor_id');
            
            if ($tecido_id && $cor_id) {
                $dados_sessao['tecido_id'] = $tecido_id;
                $dados_sessao['cor_id'] = $cor_id;
                $this->session->set_userdata('orcamento_dados', $dados_sessao);
                redirect('orcamento/etapa5');
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa4', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 5: Largura
     */
    public function etapa5() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['tecido_id'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Informe a Largura - Le Cortine';
        $data['etapa_atual'] = 5;
        $data['total_etapas'] = 8;
        $data['dados'] = $dados_sessao;
        $data['produto'] = $this->Produto_model->get($dados_sessao['produto_id']);
        
        if ($this->input->method() === 'post') {
            $faixa_largura = $this->input->post('faixa_largura');
            $largura_exata = $this->input->post('largura_exata');
            
            if ($faixa_largura === 'maior_5m') {
                redirect('orcamento/consultoria');
            } elseif ($faixa_largura && $largura_exata) {
                $dados_sessao['faixa_largura'] = $faixa_largura;
                $dados_sessao['largura'] = $largura_exata;
                $this->session->set_userdata('orcamento_dados', $dados_sessao);
                redirect('orcamento/etapa6');
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa5', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 6: Altura
     */
    public function etapa6() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['largura'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Informe a Altura - Le Cortine';
        $data['etapa_atual'] = 6;
        $data['total_etapas'] = 8;
        $data['dados'] = $dados_sessao;
        
        if ($this->input->method() === 'post') {
            $altura_opcao = $this->input->post('altura_opcao');
            $altura_exata = $this->input->post('altura_exata');
            
            if ($altura_opcao === 'maior_280') {
                redirect('orcamento/consultoria');
            } elseif ($altura_opcao === 'ate_280' && $altura_exata) {
                $dados_sessao['altura'] = $altura_exata;
                $this->session->set_userdata('orcamento_dados', $dados_sessao);
                
                // Se for Cortina em Tecido (ID 1), vai para blackout
                if ($dados_sessao['produto_id'] == 1) {
                    redirect('orcamento/etapa7');
                } else {
                    // Rolô e Duplex vão direto para endereço
                    redirect('orcamento/etapa8');
                }
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa6', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 7: Blackout Adicional (apenas para Cortina em Tecido)
     */
    public function etapa7() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['altura'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Deseja Blackout? - Le Cortine';
        $data['etapa_atual'] = 7;
        $data['total_etapas'] = 8;
        $data['dados'] = $dados_sessao;
        
        if ($this->input->method() === 'post') {
            $blackout = $this->input->post('blackout');
            
            if ($blackout === 'sim') {
                // Determinar qual extra de blackout conforme largura
                $faixa = $dados_sessao['faixa_largura'];
                $extra_id = match($faixa) {
                    'ate_2m' => 1,
                    'ate_3m' => 2,
                    'ate_4m' => 3,
                    'ate_5m' => 4,
                    default => null
                };
                
                if ($extra_id) {
                    $dados_sessao['blackout_extra_id'] = $extra_id;
                }
            }
            
            $this->session->set_userdata('orcamento_dados', $dados_sessao);
            redirect('orcamento/etapa8');
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa7', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Etapa 8: Endereço para Frete
     */
    public function etapa8() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['altura'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Endereço para Frete - Le Cortine';
        $data['etapa_atual'] = 8;
        $data['total_etapas'] = 8;
        $data['dados'] = $dados_sessao;
        
        if ($this->input->method() === 'post') {
            $tipo_entrega = $this->input->post('tipo_entrega');
            $dados_sessao['tipo_entrega'] = $tipo_entrega;
            
            // Se for retirada, não precisa validar endereço
            if ($tipo_entrega === 'retirada') {
                $dados_sessao['cep'] = null;
                $dados_sessao['endereco'] = null;
                $dados_sessao['numero'] = null;
                $dados_sessao['complemento'] = null;
                $dados_sessao['bairro'] = null;
                $dados_sessao['cidade'] = null;
                $dados_sessao['estado'] = null;
                
                $this->session->set_userdata('orcamento_dados', $dados_sessao);
                redirect('orcamento/resumo');
            } else {
                // Validar campos de endereço
                $this->form_validation->set_rules('cep', 'CEP', 'required');
                $this->form_validation->set_rules('endereco', 'Endereço', 'required');
                $this->form_validation->set_rules('numero', 'Número', 'required');
                $this->form_validation->set_rules('bairro', 'Bairro', 'required');
                $this->form_validation->set_rules('cidade', 'Cidade', 'required');
                $this->form_validation->set_rules('estado', 'Estado', 'required');
                
                if ($this->form_validation->run()) {
                    $dados_sessao['cep'] = $this->input->post('cep');
                    $dados_sessao['endereco'] = $this->input->post('endereco');
                    $dados_sessao['numero'] = $this->input->post('numero');
                    $dados_sessao['complemento'] = $this->input->post('complemento');
                    $dados_sessao['bairro'] = $this->input->post('bairro');
                    $dados_sessao['cidade'] = $this->input->post('cidade');
                    $dados_sessao['estado'] = $this->input->post('estado');
                    
                    $this->session->set_userdata('orcamento_dados', $dados_sessao);
                    redirect('orcamento/resumo');
                }
            }
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/etapa8', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Resumo e Finalização
     */
    public function resumo() {
        $dados_sessao = $this->session->userdata('orcamento_dados');
        if (!$dados_sessao || !isset($dados_sessao['tipo_entrega'])) {
            redirect('orcamento/etapa1');
        }
        
        $data['titulo'] = 'Resumo do Orçamento - Le Cortine';
        $data['dados'] = $dados_sessao;
        
        // Buscar informações completas
        $data['produto'] = $this->Produto_model->get($dados_sessao['produto_id']);
        $data['tecido'] = $this->Tecido_model->get($dados_sessao['tecido_id']);
        $data['cor'] = $this->Tecido_model->get_cor($dados_sessao['cor_id']);
        
        // Calcular preço
        $data['valor_calculado'] = $this->calcular_valor_final($dados_sessao);
        
        // Verificar se já enviou notificação nesta sessão
        $notificacao_enviada = $this->session->userdata('notificacao_enviada');
        
        if (!$notificacao_enviada) {
            // Salvar orçamento temporário no banco
            $orcamento_id = $this->salvar_orcamento_temporario($dados_sessao, $data['valor_calculado']);
            
            // Enviar notificações (e-mail para empresa e cliente)
            $this->enviar_notificacoes($orcamento_id, $dados_sessao, $data);
            
            // Marcar que já enviou notificação
            $this->session->set_userdata('notificacao_enviada', true);
            $this->session->set_userdata('orcamento_id', $orcamento_id);
        } else {
            // Pegar ID do orçamento já salvo
            $orcamento_id = $this->session->userdata('orcamento_id');
        }
        
        $data['orcamento_id'] = $orcamento_id;
        
        if ($this->input->method() === 'post') {
            $this->finalizar_orcamento($dados_sessao, $data['valor_calculado']);
        }
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/resumo', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Calcular valor final do orçamento
     */
    private function calcular_valor_final($dados) {
        $produto_id = $dados['produto_id'];
        $largura = $dados['largura'];
        $altura = $dados['altura'];
        
        $valor_base = 0;
        
        // Cortina em Tecido - Preço fixo por faixa
        if ($produto_id == 1) {
            $faixa = $dados['faixa_largura'];
            $valor_base = match($faixa) {
                'ate_2m' => 442.00,
                'ate_3m' => 585.00,
                'ate_4m' => 824.00,
                'ate_5m' => 1192.00,
                default => 0
            };
            
            // Adicionar blackout se selecionado
            if (isset($dados['blackout_extra_id'])) {
                $extra = $this->Extra_model->get($dados['blackout_extra_id']);
                if ($extra) {
                    $valor_base += $extra->valor;
                }
            }
        }
        // Cortina Rolô e Duplex - Preço por m²
        else {
            $m2 = $largura * $altura;
            $preco_m2 = $this->Preco_model->get_preco_tecido($produto_id, $dados['tecido_id']);
            $valor_base = $m2 * $preco_m2;
        }
        
        return $valor_base;
    }

    /**
     * Finalizar e salvar orçamento
     */
    private function finalizar_orcamento($dados, $valor) {
        // Criar ou buscar cliente
        $cliente = $this->Cliente_model->get_by_email($dados['email']);
        
        if (!$cliente) {
            // Montar endereço completo
            $endereco_completo = $dados['endereco'] ?? '';
            if (isset($dados['numero']) && !empty($dados['numero'])) {
                $endereco_completo .= ', ' . $dados['numero'];
            }
            if (isset($dados['complemento']) && !empty($dados['complemento'])) {
                $endereco_completo .= ' - ' . $dados['complemento'];
            }
            if (isset($dados['bairro']) && !empty($dados['bairro'])) {
                $endereco_completo .= ' - ' . $dados['bairro'];
            }
            
            $cliente_id = $this->Cliente_model->insert([
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone'],
                'whatsapp' => $dados['whatsapp'],
                'cep' => $dados['cep'] ?? null,
                'endereco' => $endereco_completo,
                'cidade' => $dados['cidade'],
                'estado' => $dados['estado']
            ]);
        } else {
            $cliente_id = $cliente->id;
        }
        
        // Criar orçamento
        $orcamento_id = $this->Orcamento_model->insert([
            'cliente_id' => $cliente_id,
            'status' => 'pendente',
            'tipo_atendimento' => 'online',
            'valor_total' => $valor,
            'valor_final' => $valor
        ]);
        
        $numero = $this->Orcamento_model->get($orcamento_id)->numero;
        
        // Limpar sessão
        $this->session->unset_userdata('orcamento_dados');
        
        // Redirecionar para WhatsApp
        $this->enviar_whatsapp($dados, $numero, $valor);
    }

    /**
     * Redirecionar para WhatsApp
     */
    private function enviar_whatsapp($dados, $numero, $valor) {
        $produto = $this->Produto_model->get($dados['produto_id']);
        $tecido = $this->Tecido_model->get($dados['tecido_id']);
        $cor = $this->Tecido_model->get_cor($dados['cor_id']);
        
        $mensagem = "🎯 *ORÇAMENTO #{$numero}*\n\n";
        $mensagem .= "👤 *Cliente:* {$dados['nome']}\n";
        $mensagem .= "📧 *E-mail:* {$dados['email']}\n";
        $mensagem .= "📱 *WhatsApp:* {$dados['whatsapp']}\n\n";
        $mensagem .= "🛋️ *Produto:* {$produto->nome}\n";
        $mensagem .= "🎨 *Tecido:* {$tecido->nome}\n";
        $mensagem .= "🌈 *Cor:* {$cor->nome}\n\n";
        $mensagem .= "📏 *Dimensões:*\n";
        $mensagem .= "• Largura: {$dados['largura']}m\n";
        $mensagem .= "• Altura: {$dados['altura']}m\n\n";
        $mensagem .= "💰 *Valor:* R$ " . number_format($valor, 2, ',', '.') . "\n\n";
        $mensagem .= "📍 *Endereço:*\n{$dados['endereco']}, {$dados['numero']}\n{$dados['bairro']} - {$dados['cidade']}/{$dados['estado']}\nCEP: {$dados['cep']}";
        
        $whatsapp_numero = '5511999999999'; // Número da Le Cortine
        $url = "https://api.whatsapp.com/send?phone={$whatsapp_numero}&text=" . urlencode($mensagem);
        
        redirect($url);
    }

    /**
     * Salvar orçamento temporário no banco
     */
    private function salvar_orcamento_temporario($dados, $valor) {
        // Verificar se cliente já existe
        $cliente = $this->Cliente_model->get_by_email($dados['email']);
        
        if (!$cliente) {
            // Criar novo cliente
            $cliente_id = $this->Cliente_model->insert([
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone'],
                'whatsapp' => $dados['whatsapp'],
                'cep' => $dados['cep'] ?? null,
                'endereco' => $dados['endereco'] ?? null,
                'numero' => $dados['numero'] ?? null,
                'complemento' => $dados['complemento'] ?? null,
                'bairro' => $dados['bairro'] ?? null,
                'cidade' => $dados['cidade'] ?? null,
                'estado' => $dados['estado'] ?? null
            ]);
        } else {
            $cliente_id = $cliente->id;
        }
        
        // Criar orçamento
        $orcamento_id = $this->Orcamento_model->insert([
            'cliente_id' => $cliente_id,
            'status' => 'pendente',
            'tipo_atendimento' => 'online',
            'valor_total' => $valor,
            'valor_final' => $valor
        ]);
        
        return $orcamento_id;
    }
    
    /**
     * Enviar notificações por e-mail
     */
    private function enviar_notificacoes($orcamento_id, $dados, $data_completa) {
        // Carregar library de e-mail
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
        
        // Preparar dados para o e-mail
        $dados_email = array(
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'telefone' => $dados['telefone'],
            'whatsapp' => $dados['whatsapp'] ?? '',
            'produto' => $data_completa['produto']->nome,
            'tecido' => $data_completa['tecido']->nome,
            'largura' => number_format($dados['largura'], 2, ',', '.') . 'm',
            'altura' => number_format($dados['altura'], 2, ',', '.') . 'm',
            'tipo_entrega' => $dados['tipo_entrega'] ?? 'entrega'
        );
        
        // Adicionar endereço se for entrega
        if ($dados_email['tipo_entrega'] == 'entrega') {
            $dados_email['cidade'] = $dados['cidade'];
            $dados_email['estado'] = $dados['estado'];
            $dados_email['endereco_completo'] = $dados['endereco'] . ', ' . $dados['numero'] . 
                ($dados['complemento'] ? ' - ' . $dados['complemento'] : '') . '<br>' .
                $dados['bairro'] . ' - ' . $dados['cidade'] . '/' . $dados['estado'] . '<br>' .
                'CEP: ' . $dados['cep'];
        }
        
        // 1. E-mail para a EMPRESA
        try {
            // Buscar e-mail de notificações
            $this->load->model('Configuracao_model');
            $email_notificacao = $this->Configuracao_model->get_by_chave('email_destinatario');
            $email_destino = $email_notificacao ? $email_notificacao->valor : 'contato@lecortine.com.br';
            
            $this->email->clear();
            $this->email->from('nao-responder@lecortine.com.br', 'Le Cortine - Sistema');
            $this->email->to($email_destino);
            $this->email->subject('🎉 Novo Orçamento #' . $orcamento_id . ' - Le Cortine');
            $this->email->message($this->template_email_empresa($orcamento_id, $dados_email, $data_completa['valor_calculado']));
            $this->email->send();
        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar e-mail para empresa: ' . $e->getMessage());
        }
        
        // 2. E-mail para o CLIENTE
        try {
            $this->email->clear();
            $this->email->from('nao-responder@lecortine.com.br', 'Le Cortine');
            $this->email->to($dados['email']);
            $this->email->subject('✅ Seu Orçamento foi Recebido - Le Cortine');
            $this->email->message($this->template_email_cliente($orcamento_id, $dados_email));
            $this->email->send();
        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar e-mail para cliente: ' . $e->getMessage());
        }
    }
    
    /**
     * Template de e-mail para a EMPRESA
     */
    private function template_email_empresa($orcamento_id, $dados, $valor) {
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
                .valor { font-size: 24px; color: #28a745; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Novo Orçamento!</h1>
                    <p style="margin: 0; font-size: 18px;">Orçamento #' . $orcamento_id . '</p>
                </div>
                <div class="content">
                    <p><strong>Um novo cliente solicitou orçamento!</strong></p>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #667eea;">👤 Dados do Cliente</h3>
                        <div class="info-row"><span class="label">Nome:</span> ' . $dados['nome'] . '</div>
                        <div class="info-row"><span class="label">E-mail:</span> ' . $dados['email'] . '</div>
                        <div class="info-row"><span class="label">Telefone:</span> ' . $dados['telefone'] . '</div>
                        ' . (!empty($dados['whatsapp']) ? '
                        <div class="info-row"><span class="label">WhatsApp:</span> ' . $dados['whatsapp'] . '</div>
                        <div style="margin-top: 15px; text-align: center;">
                            <a href="https://api.whatsapp.com/send?phone=' . preg_replace('/[^0-9]/', '', $dados['whatsapp']) . '&text=Olá ' . urlencode($dados['nome']) . ', tudo bem? Aqui é da Le Cortine! Vi que você solicitou um orçamento. Vamos conversar?" 
                               style="display: inline-block; background: #25D366; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                📱 Chamar no WhatsApp
                            </a>
                        </div>' : '') . '
                    </div>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #667eea;">🛍️ Detalhes do Pedido</h3>
                        <div class="info-row"><span class="label">Produto:</span> ' . $dados['produto'] . '</div>
                        <div class="info-row"><span class="label">Tecido:</span> ' . $dados['tecido'] . '</div>
                        <div class="info-row"><span class="label">Dimensões:</span> ' . $dados['largura'] . ' x ' . $dados['altura'] . '</div>
                        ' . ($dados['tipo_entrega'] == 'retirada' ? 
                            '<div class="info-row"><span class="label">Entrega:</span> 🏪 Retirada no Local</div>' : 
                            '<div class="info-row"><span class="label">Entrega:</span> 🚚 ' . $dados['endereco_completo'] . '</div>') . '
                    </div>
                    
                    <div class="info-box" style="text-align: center;">
                        <h3 style="margin-top: 0; color: #667eea;">💰 Valor Estimado</h3>
                        <div class="valor">R$ ' . number_format($valor, 2, ',', '.') . '</div>
                        <small style="color: #666;">*Frete não incluso</small>
                    </div>
                    
                    <p style="margin-top: 30px; color: #666;">
                        <strong>Próximos passos:</strong><br>
                        1. Acesse o painel admin para ver detalhes<br>
                        2. Entre em contato com o cliente via WhatsApp<br>
                        3. Calcule o frete e envie o orçamento final
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Template de e-mail para o CLIENTE
     */
    private function template_email_cliente($orcamento_id, $dados) {
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
                .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .btn-whatsapp { display: inline-block; background: #25D366; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
                .contatos { background: #fff3cd; border: 1px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Obrigado!</h1>
                    <p style="margin: 0; font-size: 18px;">Seu orçamento foi recebido com sucesso</p>
                </div>
                <div class="content">
                    <p>Olá <strong>' . $dados['nome'] . '</strong>,</p>
                    
                    <p style="font-size: 16px; line-height: 1.8;">
                        Muito obrigado por escolher a <strong>Le Cortine</strong> para transformar seu ambiente! 
                        Ficamos muito felizes em saber do seu interesse em nossos produtos.
                    </p>
                    
                    <div class="success">
                        <strong>✅ Seu orçamento foi recebido!</strong><br>
                        Número do orçamento: <strong>#' . $orcamento_id . '</strong>
                    </div>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #667eea;">📋 Resumo do seu Pedido</h3>
                        <p><strong>Produto:</strong> ' . $dados['produto'] . '</p>
                        <p><strong>Tecido:</strong> ' . $dados['tecido'] . '</p>
                        <p><strong>Dimensões:</strong> ' . $dados['largura'] . ' x ' . $dados['altura'] . '</p>
                    </div>
                    
                    <h3 style="color: #667eea;">📝 Próximos Passos</h3>
                    <ol style="line-height: 2;">
                        <li>Nossa equipe está analisando seu pedido com todo carinho</li>
                        <li>Entraremos em contato via WhatsApp em breve</li>
                        <li>Enviaremos o valor do frete e prazo de entrega</li>
                        <li>Após sua confirmação, iniciaremos a produção</li>
                    </ol>
                    
                    <div class="contatos">
                        <h3 style="margin-top: 0; color: #856404;">💬 Ficou com alguma dúvida?</h3>
                        <p style="margin-bottom: 15px;">
                            Não se preocupe! Nossa equipe está pronta para te atender e esclarecer qualquer questão sobre seu orçamento.
                        </p>
                        
                        <div style="text-align: center; margin: 20px 0;">
                            <a href="https://api.whatsapp.com/send?phone=5575988890006&text=Olá! Fiz um orçamento (#' . $orcamento_id . ') e gostaria de tirar algumas dúvidas." 
                               class="btn-whatsapp">
                                📱 Falar no WhatsApp
                            </a>
                        </div>
                        
                        <p style="text-align: center; margin-top: 20px; color: #666;">
                            <strong>Outros canais de atendimento:</strong><br>
                            📞 Telefone: (75) 98889-0006<br>
                            📧 E-mail: contato@lecortine.com.br<br>
                            🕐 Horário: Segunda a Sexta, 8h às 18h
                        </p>
                    </div>
                    
                    <p style="margin-top: 30px; text-align: center; color: #666; font-size: 14px;">
                        <em>Agradecemos pela sua preferência e confiança!<br>
                        Estamos ansiosos para tornar seu projeto realidade.</em>
                    </p>
                    
                    <p style="text-align: center; margin-top: 30px; color: #999; font-size: 12px;">
                        Le Cortine - Transformando ambientes com elegância e qualidade
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Página de Consultoria Personalizada
     */
    public function consultoria() {
        $data['titulo'] = 'Consultoria Personalizada - Le Cortine';
        $data['dados'] = $this->session->userdata('orcamento_dados');
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/consultoria', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * Página de Agradecimento (Pós-Consultoria)
     */
    public function agradecimento() {
        $data['titulo'] = 'Obrigado! - Le Cortine';
        $data['dados'] = $this->session->userdata('orcamento_dados');
        $data['numero'] = 'CONS-' . date('YmdHis');
        
        $this->load->view('public/layout/header', $data);
        $this->load->view('public/orcamento/agradecimento', $data);
        $this->load->view('public/layout/footer', $data);
    }

    /**
     * AJAX: Buscar cores de um tecido
     */
    public function ajax_cores($tecido_id) {
        $cores = $this->Tecido_model->get_cores($tecido_id);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($cores));
    }
}
