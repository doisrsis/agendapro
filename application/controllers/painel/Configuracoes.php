<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Configurações do Estabelecimento
 *
 * Gerenciamento de configurações gerais do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Configuracoes extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');
        $this->auth_check->check_tipo(['estabelecimento']);

        // Carregar models
        $this->load->model('Estabelecimento_model');
        $this->load->model('Horario_estabelecimento_model');

        // Obter dados do estabelecimento
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get_by_id($this->estabelecimento_id);
    }

    /**
     * Página de configurações
     */
    public function index() {
        $aba = $this->input->get('aba') ?? 'geral';

        if ($this->input->method() === 'post') {
            $aba_post = $this->input->post('aba');

            switch ($aba_post) {
                case 'geral':
                    $this->salvar_dados_gerais();
                    break;
                case 'agendamento':
                    $this->salvar_configuracoes_agendamento();
                    break;
                case 'whatsapp':
                    $this->salvar_integracao_whatsapp();
                    break;
                case 'mercadopago':
                    $this->salvar_integracao_mercadopago();
                    break;
            }
        }

        $data['titulo'] = 'Configurações';
        $data['menu_ativo'] = 'configuracoes';
        $data['estabelecimento'] = $this->estabelecimento;
        $data['aba_ativa'] = $aba;
        $data['horarios'] = $this->Horario_estabelecimento_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['dias_semana'] = $this->Horario_estabelecimento_model->get_dias_semana();

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/configuracoes/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Salvar dados gerais
     */
    private function salvar_dados_gerais() {
        $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
        $this->form_validation->set_rules('cnpj_cpf', 'CNPJ/CPF', 'max_length[18]');
        $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'max_length[20]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

        if ($this->form_validation->run()) {
            $dados = [
                'nome' => $this->input->post('nome'),
                'cnpj_cpf' => $this->input->post('cnpj_cpf'),
                'whatsapp' => $this->input->post('whatsapp'),
                'email' => $this->input->post('email'),
                'endereco' => $this->input->post('endereco'),
                'cidade' => $this->input->post('cidade'),
                'estado' => $this->input->post('estado'),
                'cep' => $this->input->post('cep')
            ];

            if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
                $this->session->set_flashdata('sucesso', 'Dados atualizados com sucesso!');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao atualizar dados.');
            }
        }

        redirect('painel/configuracoes?aba=geral');
    }

    /**
     * Salvar configurações de agendamento
     */
    private function salvar_configuracoes_agendamento() {
        // Salvar horários por dia da semana
        $horarios = [];
        for ($dia = 0; $dia <= 6; $dia++) {
            $horarios[$dia] = [
                'ativo' => $this->input->post("dia_{$dia}_ativo") ? 1 : 0,
                'hora_inicio' => $this->input->post("dia_{$dia}_inicio") ?? '08:00:00',
                'hora_fim' => $this->input->post("dia_{$dia}_fim") ?? '18:00:00',
                'almoco_ativo' => (int)$this->input->post("dia_{$dia}_almoco_ativo"),
                'almoco_inicio' => $this->input->post("dia_{$dia}_almoco_inicio") ?? null,
                'almoco_fim' => $this->input->post("dia_{$dia}_almoco_fim") ?? null
            ];
        }

        $this->Horario_estabelecimento_model->salvar_semana($this->estabelecimento_id, $horarios);

        // Salvar outras configurações
        $dados = [
            'tempo_minimo_agendamento' => $this->input->post('tempo_minimo_agendamento') ?? 60,
            'usar_intervalo_fixo' => (int)$this->input->post('usar_intervalo_fixo'),
            'intervalo_agendamento' => $this->input->post('intervalo_agendamento') ?? 30,
            'dias_antecedencia_agenda' => $this->input->post('dias_antecedencia_agenda') ?? 30,
            // Pagamento de agendamentos
            'agendamento_requer_pagamento' => $this->input->post('agendamento_requer_pagamento') ?? 'nao',
            'agendamento_taxa_fixa' => $this->input->post('agendamento_taxa_fixa') ?? 0.00,
            'agendamento_tempo_expiracao_pix' => $this->input->post('agendamento_tempo_expiracao_pix') ?? 30,
            'agendamento_tempo_adicional_pix' => $this->input->post('agendamento_tempo_adicional_pix') ?? 5
        ];

        // DEBUG: Log dos dados que serão salvos
        log_message('debug', 'Configuracoes/salvar_agendamento - Dados POST: ' . json_encode($_POST));
        log_message('debug', 'Configuracoes/salvar_agendamento - Dados para salvar: ' . json_encode($dados));

        if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
            log_message('debug', 'Configuracoes/salvar_agendamento - Salvo com sucesso');
            $this->session->set_flashdata('sucesso', 'Configurações de agendamento atualizadas!');
        } else {
            log_message('error', 'Configuracoes/salvar_agendamento - Erro ao salvar');
            $this->session->set_flashdata('erro', 'Erro ao atualizar configurações.');
        }

        redirect('painel/configuracoes?aba=agendamento');
    }

    /**
     * Salvar integração WhatsApp
     * Nota: A configuração agora é simplificada - o estabelecimento apenas conecta via QR Code
     * As credenciais da API são herdadas do Super Admin
     */
    private function salvar_integracao_whatsapp() {
        // Não há mais formulário para salvar - a conexão é feita via QR Code
        // Este método é mantido para compatibilidade caso haja POST na aba whatsapp
        redirect('painel/configuracoes?aba=whatsapp');
    }

    /**
     * Gerar nome da sessão baseado no nome do estabelecimento
     * Remove caracteres especiais e substitui espaços por underline
     */
    private function gerar_session_name() {
        $nome = $this->estabelecimento->nome;
        // Remover acentos
        $nome = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nome);
        // Converter para minúsculas
        $nome = strtolower($nome);
        // Substituir espaços por underline
        $nome = preg_replace('/\s+/', '_', $nome);
        // Remover caracteres especiais (manter apenas letras, números e underline)
        $nome = preg_replace('/[^a-z0-9_]/', '', $nome);
        // Limitar tamanho
        $nome = substr($nome, 0, 50);
        // Adicionar prefixo para evitar conflitos
        return 'est_' . $this->estabelecimento_id . '_' . $nome;
    }

    /**
     * Configurar WAHA usando credenciais do Super Admin
     * O estabelecimento usa a mesma API do SaaS, mas com sessão própria
     */
    private function configurar_waha_estabelecimento() {
        $this->load->model('Configuracao_model');
        $this->load->library('waha_lib');

        // Buscar configurações WAHA do Super Admin
        $configs = $this->Configuracao_model->get_by_grupo('waha');

        if (empty($configs)) {
            return false;
        }

        $config_array = [];
        foreach ($configs as $config) {
            $config_array[$config->chave] = $config->valor;
        }

        // Verificar se WAHA está ativo no SaaS
        if (empty($config_array['waha_api_url']) || empty($config_array['waha_api_key'])) {
            return false;
        }

        // Gerar nome da sessão baseado no estabelecimento
        $session_name = $this->estabelecimento->waha_session_name;
        if (empty($session_name)) {
            $session_name = $this->gerar_session_name();
            // Salvar o nome da sessão gerado
            $this->Estabelecimento_model->update($this->estabelecimento_id, [
                'waha_session_name' => $session_name
            ]);
        }

        // Configurar a library com credenciais do SaaS mas sessão do estabelecimento
        $this->waha_lib->set_credentials(
            $config_array['waha_api_url'],
            $config_array['waha_api_key'],
            $session_name
        );

        return true;
    }

    /**
     * Iniciar sessão WAHA para o estabelecimento
     * Usa configurações do Super Admin automaticamente
     */
    public function waha_iniciar_sessao() {
        if (!$this->configurar_waha_estabelecimento()) {
            $this->session->set_flashdata('erro', 'Integração WhatsApp não está configurada. Entre em contato com o suporte.');
            redirect('painel/configuracoes?aba=whatsapp');
            return;
        }

        // Verificar se já existe uma sessão e deletá-la primeiro
        $sessao_existente = $this->waha_lib->get_sessao();
        if ($sessao_existente['success'] && isset($sessao_existente['response']['name'])) {
            log_message('debug', 'WAHA: Sessão existente encontrada, deletando...');
            $this->waha_lib->deletar_sessao();
            sleep(1); // Aguardar um segundo para garantir que foi deletada
        }

        // Gerar URL do webhook para este estabelecimento
        $webhook_url = base_url('webhook_waha/estabelecimento/' . $this->estabelecimento_id);

        $resultado = $this->waha_lib->criar_sessao([
            'webhook_url' => $webhook_url,
            'metadata' => [
                'tipo' => 'estabelecimento',
                'estabelecimento_id' => $this->estabelecimento_id,
                'nome' => $this->estabelecimento->nome
            ]
        ]);

        if ($resultado['success']) {
            // Atualizar dados do estabelecimento
            $this->Estabelecimento_model->update($this->estabelecimento_id, [
                'waha_status' => 'conectando',
                'waha_webhook_url' => $webhook_url,
                'whatsapp_api_tipo' => 'waha',
                'waha_ativo' => 1,
                'waha_bot_ativo' => 1,
                'waha_numero_conectado' => ''
            ]);
            $this->session->set_flashdata('sucesso', 'Escaneie o QR Code com seu WhatsApp para conectar.');
        } else {
            $erro = $resultado['response']['message'] ?? json_encode($resultado);
            log_message('error', 'WAHA criar_sessao erro: ' . $erro);
            $this->session->set_flashdata('erro', 'Erro ao iniciar sessão: ' . $erro);
        }

        redirect('painel/configuracoes?aba=whatsapp');
    }

    /**
     * Desconectar sessão WAHA do estabelecimento
     */
    public function waha_desconectar() {
        if (!$this->configurar_waha_estabelecimento()) {
            $this->session->set_flashdata('erro', 'Configurações WAHA não encontradas.');
            redirect('painel/configuracoes?aba=whatsapp');
            return;
        }

        $resultado = $this->waha_lib->logout_sessao();

        if ($resultado['success']) {
            $this->Estabelecimento_model->update($this->estabelecimento_id, [
                'waha_status' => 'desconectado',
                'waha_numero_conectado' => ''
            ]);
            $this->session->set_flashdata('sucesso', 'WhatsApp desconectado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao desconectar.');
        }

        redirect('painel/configuracoes?aba=whatsapp');
    }

    /**
     * Obter QR Code WAHA via AJAX
     */
    public function waha_qrcode() {
        header('Content-Type: application/json');

        if (!$this->configurar_waha_estabelecimento()) {
            echo json_encode(['success' => false, 'error' => 'Configurações não encontradas']);
            return;
        }

        $status = $this->waha_lib->get_status();
        log_message('debug', 'WAHA QRCode - Status: ' . $status);

        if (in_array($status, ['working', 'connected', 'WORKING'])) {
            $me = $this->waha_lib->get_me();

            // Atualizar status no banco
            $this->Estabelecimento_model->update($this->estabelecimento_id, [
                'waha_status' => 'conectado',
                'waha_numero_conectado' => $me['response']['id'] ?? ''
            ]);

            echo json_encode([
                'success' => true,
                'status' => 'connected',
                'numero' => $me['response']['id'] ?? '',
                'nome' => $me['response']['pushName'] ?? ''
            ]);
            return;
        }

        // Se a sessão não existe ou está parada, tentar iniciar
        if (in_array($status, ['stopped', 'STOPPED', 'failed', 'FAILED', 'unknown', ''])) {
            log_message('debug', 'WAHA QRCode - Sessão parada/inexistente, iniciando...');
            $this->waha_lib->iniciar_sessao();
            sleep(2); // Aguardar sessão iniciar
            $status = $this->waha_lib->get_status();
        }

        $qr = $this->waha_lib->get_qr_code();
        log_message('debug', 'WAHA QRCode - Resultado: ' . json_encode($qr));

        if ($qr['success'] && isset($qr['response']['data'])) {
            echo json_encode([
                'success' => true,
                'status' => $status,
                'qrcode' => $qr['response']['data']
            ]);
        } elseif ($qr['success'] && isset($qr['response']['value'])) {
            // Formato alternativo do QR Code
            echo json_encode([
                'success' => true,
                'status' => $status,
                'qrcode' => $qr['response']['value']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'status' => $status,
                'error' => 'QR Code não disponível. Aguarde alguns segundos e tente novamente.',
                'debug' => $qr
            ]);
        }
    }

    /**
     * Salvar integração Mercado Pago
     */
    private function salvar_integracao_mercadopago() {
        $dados = [
            'mp_public_key_test' => $this->input->post('mp_public_key_test'),
            'mp_access_token_test' => $this->input->post('mp_access_token_test'),
            'mp_public_key_prod' => $this->input->post('mp_public_key_prod'),
            'mp_access_token_prod' => $this->input->post('mp_access_token_prod'),
            'mp_sandbox' => $this->input->post('mp_sandbox') ? 1 : 0
        ];

        // DEBUG: Log dos dados
        log_message('debug', 'Configuracoes/salvar_mercadopago - Dados POST: ' . json_encode($_POST));
        log_message('debug', 'Configuracoes/salvar_mercadopago - Dados para salvar: ' . json_encode($dados));

        if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
            log_message('debug', 'Configuracoes/salvar_mercadopago - Salvo com sucesso');
            $this->session->set_flashdata('sucesso', 'Integração Mercado Pago atualizada!');
        } else {
            log_message('error', 'Configuracoes/salvar_mercadopago - Erro ao salvar');
            $this->session->set_flashdata('erro', 'Erro ao atualizar integração.');
        }

        redirect('painel/configuracoes?aba=mercadopago');
    }

    /**
     * Debug: Testar envio de mensagem WhatsApp
     * Acesse: /painel/configuracoes/waha_teste_envio
     */
    public function waha_teste_envio() {
        header('Content-Type: application/json');

        // Verificar se estabelecimento está conectado
        if ($this->estabelecimento->waha_status != 'conectado') {
            echo json_encode([
                'success' => false,
                'error' => 'Estabelecimento não está conectado ao WhatsApp',
                'waha_status' => $this->estabelecimento->waha_status,
                'waha_session_name' => $this->estabelecimento->waha_session_name
            ]);
            return;
        }

        // Configurar WAHA
        if (!$this->configurar_waha_estabelecimento()) {
            echo json_encode([
                'success' => false,
                'error' => 'Falha ao configurar WAHA'
            ]);
            return;
        }

        // Dados de debug
        $debug = [
            'estabelecimento_id' => $this->estabelecimento_id,
            'estabelecimento_nome' => $this->estabelecimento->nome,
            'waha_session_name' => $this->estabelecimento->waha_session_name,
            'waha_status' => $this->estabelecimento->waha_status,
            'waha_numero_conectado' => $this->estabelecimento->waha_numero_conectado
        ];

        // Verificar status da sessão na API
        $status = $this->waha_lib->get_status();
        $debug['api_status'] = $status;

        // Tentar enviar mensagem de teste para o próprio número conectado
        $numero_teste = $this->estabelecimento->waha_numero_conectado;
        if (empty($numero_teste)) {
            echo json_encode([
                'success' => false,
                'error' => 'Número conectado não encontrado',
                'debug' => $debug
            ]);
            return;
        }

        // Limpar número (remover @c.us se existir)
        $numero_teste = str_replace('@c.us', '', $numero_teste);

        $mensagem = "🧪 *Teste de Notificação*\n\n";
        $mensagem .= "Esta é uma mensagem de teste do sistema AgendaPro.\n";
        $mensagem .= "Estabelecimento: {$this->estabelecimento->nome}\n";
        $mensagem .= "Data/Hora: " . date('d/m/Y H:i:s');

        $resultado = $this->waha_lib->enviar_texto($numero_teste, $mensagem);

        echo json_encode([
            'success' => $resultado['success'],
            'debug' => $debug,
            'numero_teste' => $numero_teste,
            'resultado_envio' => $resultado
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Atualizar webhook da sessão WAHA
     * Necessário se o webhook não foi configurado corretamente
     */
    public function waha_atualizar_webhook() {
        if (!$this->configurar_waha_estabelecimento()) {
            $this->session->set_flashdata('erro', 'Configurações WAHA não encontradas.');
            redirect('painel/configuracoes?aba=whatsapp');
            return;
        }

        // Gerar URL do webhook para este estabelecimento
        $webhook_url = base_url('webhook_waha/estabelecimento/' . $this->estabelecimento_id);

        $resultado = $this->waha_lib->atualizar_webhook($webhook_url);

        if ($resultado['success']) {
            $this->Estabelecimento_model->update($this->estabelecimento_id, [
                'waha_webhook_url' => $webhook_url
            ]);
            $this->session->set_flashdata('sucesso', 'Webhook atualizado com sucesso! URL: ' . $webhook_url);
        } else {
            $erro = $resultado['response']['message'] ?? json_encode($resultado);
            $this->session->set_flashdata('erro', 'Erro ao atualizar webhook: ' . $erro);
        }

        redirect('painel/configuracoes?aba=whatsapp');
    }

    /**
     * Diagnóstico completo do bot WhatsApp
     * Acesse: /painel/configuracoes/waha_diagnostico
     */
    public function waha_diagnostico() {
        header('Content-Type: application/json');

        $diagnostico = [
            'timestamp' => date('Y-m-d H:i:s'),
            'estabelecimento' => [
                'id' => $this->estabelecimento_id,
                'nome' => $this->estabelecimento->nome,
                'waha_status' => $this->estabelecimento->waha_status,
                'waha_bot_ativo' => $this->estabelecimento->waha_bot_ativo,
                'waha_session_name' => $this->estabelecimento->waha_session_name,
                'waha_numero_conectado' => $this->estabelecimento->waha_numero_conectado,
                'waha_webhook_url' => $this->estabelecimento->waha_webhook_url ?? 'NÃO CONFIGURADO'
            ],
            'verificacoes' => []
        ];

        // Verificar se bot está ativo
        $diagnostico['verificacoes']['bot_ativo'] = [
            'status' => $this->estabelecimento->waha_bot_ativo == 1 ? 'OK' : 'ERRO',
            'mensagem' => $this->estabelecimento->waha_bot_ativo == 1
                ? 'Bot está ativo'
                : 'Bot está DESATIVADO - ative nas configurações'
        ];

        // Verificar conexão WhatsApp
        $diagnostico['verificacoes']['conexao_whatsapp'] = [
            'status' => $this->estabelecimento->waha_status == 'conectado' ? 'OK' : 'ERRO',
            'mensagem' => $this->estabelecimento->waha_status == 'conectado'
                ? 'WhatsApp conectado'
                : 'WhatsApp NÃO conectado - status: ' . $this->estabelecimento->waha_status
        ];

        // Verificar configuração WAHA
        if ($this->configurar_waha_estabelecimento()) {
            $diagnostico['verificacoes']['config_waha'] = [
                'status' => 'OK',
                'mensagem' => 'Configurações WAHA carregadas'
            ];

            // Verificar status da sessão na API
            $api_status = $this->waha_lib->get_status();
            $diagnostico['verificacoes']['api_status'] = [
                'status' => in_array($api_status, ['working', 'connected']) ? 'OK' : 'ERRO',
                'mensagem' => 'Status na API: ' . $api_status
            ];

            // Verificar sessão
            $sessao = $this->waha_lib->get_sessao();
            $diagnostico['sessao_waha'] = $sessao;

        } else {
            $diagnostico['verificacoes']['config_waha'] = [
                'status' => 'ERRO',
                'mensagem' => 'Falha ao carregar configurações WAHA do SaaS Admin'
            ];
        }

        // URL esperada do webhook
        $webhook_esperado = base_url('webhook_waha/estabelecimento/' . $this->estabelecimento_id);
        $diagnostico['webhook'] = [
            'url_esperada' => $webhook_esperado,
            'url_configurada' => $this->estabelecimento->waha_webhook_url ?? 'NÃO CONFIGURADO',
            'match' => ($this->estabelecimento->waha_webhook_url ?? '') === $webhook_esperado
        ];

        // Instruções de correção
        $diagnostico['instrucoes'] = [];

        if ($this->estabelecimento->waha_bot_ativo != 1) {
            $diagnostico['instrucoes'][] = 'Ative o bot nas configurações do WhatsApp';
        }

        if ($this->estabelecimento->waha_status != 'conectado') {
            $diagnostico['instrucoes'][] = 'Reconecte o WhatsApp escaneando o QR Code';
        }

        if (($this->estabelecimento->waha_webhook_url ?? '') !== $webhook_esperado) {
            $diagnostico['instrucoes'][] = 'Atualize o webhook acessando: /painel/configuracoes/waha_atualizar_webhook';
        }

        echo json_encode($diagnostico, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Reiniciar sessão WAHA para aplicar configurações
     */
    public function waha_reiniciar() {
        if (!$this->configurar_waha_estabelecimento()) {
            $this->session->set_flashdata('erro', 'Configurações WAHA não encontradas.');
            redirect('painel/configuracoes?aba=whatsapp');
            return;
        }

        // Primeiro atualizar o webhook
        $webhook_url = base_url('webhook_waha/estabelecimento/' . $this->estabelecimento_id);
        $this->waha_lib->atualizar_webhook($webhook_url);

        // Reiniciar a sessão
        $resultado = $this->waha_lib->reiniciar_sessao();

        if ($resultado['success']) {
            $this->Estabelecimento_model->update($this->estabelecimento_id, [
                'waha_webhook_url' => $webhook_url
            ]);
            $this->session->set_flashdata('sucesso', 'Sessão reiniciada! Aguarde alguns segundos e teste novamente.');
        } else {
            $erro = $resultado['response']['message'] ?? json_encode($resultado);
            $this->session->set_flashdata('erro', 'Erro ao reiniciar: ' . $erro);
        }

        redirect('painel/configuracoes?aba=whatsapp');
    }
}
