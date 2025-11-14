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
        if (!$dados_sessao || !isset($dados_sessao['cidade'])) {
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
