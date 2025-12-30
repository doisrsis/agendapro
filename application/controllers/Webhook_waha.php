<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Webhook WAHA - WhatsApp HTTP API
 *
 * Recebe eventos e mensagens da API WAHA
 * Processa mensagens para bot de agendamento e notificações
 *
 * @author Rafael Dias - doisr.com.br
 * @date 28/12/2024
 */
class Webhook_waha extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Estabelecimento_model');
        $this->load->model('Configuracao_model');
        $this->load->model('Bot_conversa_model');
    }

    /**
     * Webhook principal para o SaaS Admin
     * Recebe eventos da sessão do administrador do SaaS
     */
    public function index() {
        $this->processar_webhook(null);
    }

    /**
     * Teste simples do webhook - verifica se está acessível
     */
    public function teste() {
        log_message('info', 'WAHA Webhook TESTE: Endpoint acessado com sucesso');

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Webhook está funcionando!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $_SERVER['REQUEST_METHOD'],
            'payload_recebido' => file_get_contents('php://input')
        ]);
    }

    /**
     * Ver logs do webhook para debug
     */
    public function ver_logs() {
        header('Content-Type: application/json');

        $log_file = APPPATH . 'logs/webhook_waha_debug.log';

        if (!file_exists($log_file)) {
            echo json_encode([
                'success' => false,
                'message' => 'Nenhum log encontrado. O webhook ainda não foi chamado.',
                'arquivo' => $log_file
            ]);
            return;
        }

        $logs = file_get_contents($log_file);
        $linhas = array_filter(explode("\n", $logs));
        $ultimos_logs = array_slice($linhas, -20); // Últimos 20 logs

        $logs_parsed = [];
        foreach ($ultimos_logs as $linha) {
            $decoded = json_decode($linha, true);
            if ($decoded) {
                $logs_parsed[] = $decoded;
            }
        }

        echo json_encode([
            'success' => true,
            'total_logs' => count($linhas),
            'ultimos_logs' => $logs_parsed
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Limpar logs do webhook
     */
    public function limpar_logs() {
        $log_file = APPPATH . 'logs/webhook_waha_debug.log';

        if (file_exists($log_file)) {
            unlink($log_file);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Logs limpos']);
    }

    /**
     * Simula recebimento de mensagem para testar o bot
     * Acesse: /webhook_waha/simular_mensagem/4/5511999999999/oi
     */
    public function simular_mensagem($estabelecimento_id = null, $numero = null, $mensagem = 'oi') {
        if (!$estabelecimento_id || !$numero) {
            echo json_encode(['error' => 'Uso: /webhook_waha/simular_mensagem/{estabelecimento_id}/{numero}/{mensagem}']);
            return;
        }

        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get_by_id($estabelecimento_id);

        if (!$estabelecimento) {
            echo json_encode(['error' => 'Estabelecimento não encontrado']);
            return;
        }

        header('Content-Type: application/json');

        // Simular processamento do bot
        $this->load->library('waha_lib');
        $this->load->model('Cliente_model');
        $this->load->model('Servico_model');
        $this->load->model('Profissional_model');

        // Configurar WAHA
        if (!$this->waha_lib->set_estabelecimento($estabelecimento)) {
            echo json_encode([
                'error' => 'Falha ao configurar WAHA',
                'estabelecimento' => $estabelecimento->nome,
                'waha_status' => $estabelecimento->waha_status
            ]);
            return;
        }

        // Verificar cliente
        $cliente = $this->Cliente_model->get_by_whatsapp($numero, $estabelecimento_id);

        // Processar mensagem
        $msg = strtolower(trim($mensagem));

        $resposta = '';
        if (in_array($msg, ['oi', 'olá', 'ola', 'menu', 'inicio', 'início', 'hi', 'hello'])) {
            $nome_cliente = $cliente ? $cliente->nome : 'Cliente';
            $primeiro_nome = explode(' ', $nome_cliente)[0];

            $resposta = "Olá, {$primeiro_nome}! 👋\n\n";
            $resposta .= "Bem-vindo(a) ao *{$estabelecimento->nome}*! 💈✨\n\n";
            $resposta .= "Como posso ajudar?\n\n";
            $resposta .= "1️⃣ *Agendar* - Fazer novo agendamento\n";
            $resposta .= "2️⃣ *Meus Agendamentos* - Ver agendamentos\n";
            $resposta .= "3️⃣ *Cancelar* - Cancelar agendamento\n";
            $resposta .= "0️⃣ *Sair* - Encerrar atendimento\n\n";
            $resposta .= "_Digite o número da opção desejada._";
        } else {
            $resposta = "Desculpe, não entendi. 🤔\n\nDigite *oi* para ver o menu de opções.";
        }

        // Tentar enviar a mensagem
        $resultado = $this->waha_lib->enviar_texto($numero, $resposta);

        echo json_encode([
            'success' => $resultado['success'],
            'estabelecimento' => $estabelecimento->nome,
            'numero' => $numero,
            'mensagem_recebida' => $mensagem,
            'resposta_enviada' => $resposta,
            'resultado_envio' => $resultado
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Webhook para estabelecimento específico
     *
     * @param int $estabelecimento_id ID do estabelecimento
     */
    public function estabelecimento($estabelecimento_id = null) {
        // Log de toda requisição recebida - SALVAR EM ARQUIVO PARA DEBUG
        $payload = file_get_contents('php://input');
        $log_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'estabelecimento_id' => $estabelecimento_id,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'unknown',
            'payload_length' => strlen($payload),
            'payload' => substr($payload, 0, 2000)
        ];

        // Salvar em arquivo de log específico para debug
        $log_file = APPPATH . 'logs/webhook_waha_debug.log';
        file_put_contents($log_file, json_encode($log_data) . "\n", FILE_APPEND);

        log_message('info', "WAHA Webhook RECEBIDO [est:{$estabelecimento_id}]: " . substr($payload, 0, 1000));

        if (!$estabelecimento_id) {
            log_message('error', 'WAHA Webhook: estabelecimento_id não informado');
            $this->output_json(['error' => 'estabelecimento_id required'], 400);
            return;
        }

        $this->processar_webhook($estabelecimento_id);
    }

    /**
     * Processa o webhook recebido
     *
     * @param int|null $estabelecimento_id ID do estabelecimento ou null para SaaS admin
     */
    private function processar_webhook($estabelecimento_id) {
        // Obter payload do webhook
        $payload = file_get_contents('php://input');

        // Log detalhado para debug
        log_message('info', "WAHA Webhook PROCESSANDO [est:{$estabelecimento_id}] - Payload length: " . strlen($payload));

        // Se não houver payload, retornar sucesso (pode ser health check)
        if (empty($payload)) {
            log_message('debug', 'WAHA Webhook: Payload vazio - provavelmente health check');
            $this->output_json(['success' => true, 'message' => 'OK']);
            return;
        }

        $data = json_decode($payload, true);

        if (!$data) {
            log_message('error', 'WAHA Webhook: Payload inválido - ' . substr($payload, 0, 200));
            $this->output_json(['error' => 'Invalid payload'], 400);
            return;
        }

        // Log do evento recebido
        $evento = $data['event'] ?? 'unknown';
        log_message('debug', "WAHA Webhook [{$evento}]: " . substr($payload, 0, 500));

        // Processar evento baseado no tipo
        switch ($evento) {
            case 'session.status':
                $this->processar_status_sessao($data, $estabelecimento_id);
                break;

            case 'message':
                $this->processar_mensagem($data, $estabelecimento_id);
                break;

            case 'message.ack':
                $this->processar_ack_mensagem($data, $estabelecimento_id);
                break;

            case 'message.reaction':
                // Reação a mensagem - apenas log por enquanto
                log_message('debug', 'WAHA Webhook: Reação recebida');
                break;

            default:
                log_message('debug', "WAHA Webhook: Evento não tratado: {$evento}");
        }

        $this->output_json(['success' => true]);
    }

    /**
     * Processa mudança de status da sessão
     */
    private function processar_status_sessao($data, $estabelecimento_id) {
        $session = $data['session'] ?? '';
        $status = strtolower($data['payload']['status'] ?? '');
        $me = $data['me'] ?? null;

        log_message('info', "WAHA Status: Sessão {$session} - Status: {$status}");

        // Mapear status WAHA para status do sistema
        $status_map = [
            'working' => 'conectado',
            'scan_qr_code' => 'conectando',
            'starting' => 'conectando',
            'stopped' => 'desconectado',
            'failed' => 'erro'
        ];

        $status_sistema = $status_map[$status] ?? $status;

        if ($estabelecimento_id) {
            // Atualizar status do estabelecimento
            $update_data = ['waha_status' => $status_sistema];

            if ($me && isset($me['id'])) {
                $update_data['waha_numero_conectado'] = $me['id'];
            }

            $this->Estabelecimento_model->update($estabelecimento_id, $update_data);
        } else {
            // Atualizar status do SaaS Admin
            $this->Configuracao_model->update_by_chave('waha_status', $status_sistema);

            if ($me && isset($me['id'])) {
                $this->Configuracao_model->update_by_chave('waha_numero_conectado', $me['id']);
            }
        }
    }

    /**
     * Processa mensagem recebida
     */
    private function processar_mensagem($data, $estabelecimento_id) {
        $payload = $data['payload'] ?? [];

        // Ignorar mensagens enviadas por nós mesmos
        if (isset($payload['fromMe']) && $payload['fromMe']) {
            return;
        }

        $from = $payload['from'] ?? '';
        $body = $payload['body'] ?? '';
        $message_id = $payload['id'] ?? '';
        $timestamp = $payload['timestamp'] ?? time();

        // Extrair número limpo
        $numero = preg_replace('/[^0-9]/', '', str_replace('@c.us', '', $from));

        log_message('info', "WAHA Mensagem de {$numero}: " . substr($body, 0, 100));

        // Salvar mensagem no log
        $this->salvar_log_mensagem([
            'estabelecimento_id' => $estabelecimento_id,
            'direcao' => 'entrada',
            'numero_destino' => $numero,
            'tipo_mensagem' => $this->detectar_tipo_mensagem($payload),
            'conteudo' => $body,
            'message_id' => $message_id,
            'status' => 'recebido'
        ]);

        // Se for estabelecimento com bot ativo, processar bot
        if ($estabelecimento_id) {
            $estabelecimento = $this->Estabelecimento_model->get_by_id($estabelecimento_id);

            if ($estabelecimento && $estabelecimento->waha_bot_ativo) {
                $this->processar_bot_agendamento($estabelecimento, $numero, $body, $message_id);
            }
        } else {
            // Mensagem para o SaaS Admin - bot de suporte
            $this->processar_bot_suporte($numero, $body, $message_id);
        }
    }

    /**
     * Processa confirmação de entrega/leitura de mensagem
     */
    private function processar_ack_mensagem($data, $estabelecimento_id) {
        $payload = $data['payload'] ?? [];
        $message_id = $payload['id'] ?? '';
        $ack = $payload['ack'] ?? 0;

        // Mapear ack para status
        $status_map = [
            1 => 'enviado',
            2 => 'entregue',
            3 => 'lido'
        ];

        $status = $status_map[$ack] ?? 'enviado';

        // Atualizar status da mensagem no log
        if ($message_id) {
            $this->db->where('message_id', $message_id);
            $this->db->update('whatsapp_mensagens', ['status' => $status]);
        }
    }

    /**
     * Bot de agendamento para estabelecimentos
     * Implementa máquina de estados para fluxo de conversa
     */
    private function processar_bot_agendamento($estabelecimento, $numero, $mensagem, $message_id) {
        $this->load->library('waha_lib');
        $this->load->model('Cliente_model');
        $this->load->model('Servico_model');
        $this->load->model('Profissional_model');
        $this->load->model('Agendamento_model');

        // Configurar WAHA para o estabelecimento
        if (!$this->waha_lib->set_estabelecimento($estabelecimento)) {
            log_message('error', 'Bot: Falha ao configurar WAHA para estabelecimento ' . $estabelecimento->id);
            return;
        }

        // Normalizar mensagem
        $msg = strtolower(trim($mensagem));

        // Obter ou criar conversa (máquina de estados)
        $conversa = $this->Bot_conversa_model->get_ou_criar($estabelecimento->id, $numero);

        // Verificar se é cliente existente
        $cliente = $this->Cliente_model->get_by_whatsapp($numero, $estabelecimento->id);

        // Atualizar cliente na conversa se encontrado
        if ($cliente && !$conversa->cliente_id) {
            $this->Bot_conversa_model->set_cliente($conversa->id, $cliente->id);
        }

        // Comandos globais (funcionam em qualquer estado)
        $comandos_inicio = ['oi', 'olá', 'ola', 'menu', 'inicio', 'início', 'hi', 'hello', 'bom dia', 'boa tarde', 'boa noite'];

        if (in_array($msg, $comandos_inicio)) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
            return;
        }

        if (in_array($msg, ['0', 'sair', 'tchau', 'obrigado', 'obrigada'])) {
            $this->Bot_conversa_model->encerrar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Obrigado por entrar em contato! 😊\n\n" .
                "Até a próxima! 👋\n\n" .
                "_Digite *oi* quando precisar de mim novamente._"
            );
            return;
        }

        // Processar baseado no estado atual
        switch ($conversa->estado) {
            case 'menu':
                $this->processar_estado_menu($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'aguardando_servico':
                $this->processar_estado_servico($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'aguardando_profissional':
                $this->processar_estado_profissional($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'aguardando_data':
                $this->processar_estado_data($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'aguardando_hora':
                $this->processar_estado_hora($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'confirmando':
                $this->processar_estado_confirmacao($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'aguardando_cancelamento':
                $this->processar_estado_cancelamento($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            default:
                $this->Bot_conversa_model->resetar($conversa->id);
                $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
        }
    }

    /**
     * Processa estado: Menu principal
     */
    private function processar_estado_menu($estabelecimento, $numero, $msg, $conversa, $cliente) {
        if (in_array($msg, ['1', 'agendar', 'agendamento'])) {
            $this->iniciar_agendamento($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

        if (in_array($msg, ['2', 'meus agendamentos', 'consultar', 'agendamentos'])) {
            $this->consultar_agendamentos($estabelecimento, $numero, $cliente);
            return;
        }

        if (in_array($msg, ['3', 'cancelar'])) {
            $this->iniciar_cancelamento($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

        // Não reconheceu comando no menu
        $this->waha_lib->enviar_texto($numero,
            "Desculpe, não entendi. 🤔\n\n" .
            "Digite *oi* para ver o menu de opções."
        );
    }

    /**
     * Processa estado: Aguardando seleção de serviço
     */
    private function processar_estado_servico($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $servicos = $this->Servico_model->get_by_estabelecimento($estabelecimento->id);

        // Verificar se é um número válido
        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($servicos[$indice])) {
                $servico = $servicos[$indice];

                // Salvar serviço selecionado
                $dados = $conversa->dados;
                $dados['servico_id'] = $servico->id;
                $dados['servico_nome'] = $servico->nome;
                $dados['servico_preco'] = $servico->preco;
                $dados['servico_duracao'] = $servico->duracao_minutos;

                // Buscar profissionais que fazem este serviço
                $profissionais = $this->Profissional_model->get_by_servico($servico->id, $estabelecimento->id);

                if (empty($profissionais)) {
                    $this->waha_lib->enviar_texto($numero,
                        "Desculpe, não há profissionais disponíveis para este serviço no momento. 😔\n\n" .
                        "_Digite *oi* para voltar ao menu._"
                    );
                    $this->Bot_conversa_model->resetar($conversa->id);
                    return;
                }

                // Se só tem um profissional, seleciona automaticamente
                if (count($profissionais) == 1) {
                    $prof = $profissionais[0];
                    $dados['profissional_id'] = $prof->id;
                    $dados['profissional_nome'] = $prof->nome;

                    $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_data', $dados);
                    $this->enviar_opcoes_data($estabelecimento, $numero, $dados);
                    return;
                }

                // Mostrar lista de profissionais
                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_profissional', $dados);
                $this->enviar_lista_profissionais($numero, $profissionais, $servico);
                return;
            }
        }

        // Opção inválida
        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do serviço desejado.\n\n" .
            "_Digite *0* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de profissional
     */
    private function processar_estado_profissional($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;
        $profissionais = $this->Profissional_model->get_by_servico($dados['servico_id'], $estabelecimento->id);

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($profissionais[$indice])) {
                $prof = $profissionais[$indice];

                $dados['profissional_id'] = $prof->id;
                $dados['profissional_nome'] = $prof->nome;

                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_data', $dados);
                $this->enviar_opcoes_data($estabelecimento, $numero, $dados);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do profissional.\n\n" .
            "_Digite *0* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de data
     */
    private function processar_estado_data($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;
        $datas_disponiveis = $this->obter_datas_disponiveis($estabelecimento, $dados['profissional_id'], 7);

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($datas_disponiveis[$indice])) {
                $data_selecionada = $datas_disponiveis[$indice];

                $dados['data'] = $data_selecionada;

                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_hora', $dados);
                $this->enviar_opcoes_hora($estabelecimento, $numero, $dados);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* da data.\n\n" .
            "_Digite *0* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de hora
     */
    private function processar_estado_hora($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;
        $horarios = $this->obter_horarios_disponiveis($estabelecimento, $dados['profissional_id'], $dados['data'], $dados['servico_duracao']);

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($horarios[$indice])) {
                $hora_selecionada = $horarios[$indice];

                $dados['hora'] = $hora_selecionada;

                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'confirmando', $dados);
                $this->enviar_confirmacao($estabelecimento, $numero, $dados, $cliente);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do horário.\n\n" .
            "_Digite *0* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Confirmação do agendamento
     */
    private function processar_estado_confirmacao($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        if (in_array($msg, ['sim', 's', '1', 'confirmar', 'confirmo'])) {
            $this->finalizar_agendamento($estabelecimento, $numero, $dados, $conversa, $cliente);
            return;
        }

        if (in_array($msg, ['não', 'nao', 'n', '2', 'cancelar'])) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Agendamento cancelado. ❌\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            return;
        }

        $this->waha_lib->enviar_texto($numero,
            "Por favor, responda:\n\n" .
            "*1* ou *Sim* - Para confirmar\n" .
            "*2* ou *Não* - Para cancelar\n\n" .
            "_Digite *0* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de agendamento para cancelar
     */
    private function processar_estado_cancelamento($estabelecimento, $numero, $msg, $conversa, $cliente) {
        if (!$cliente) {
            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        $agendamentos = $this->Agendamento_model->get_proximos_by_cliente($cliente->id, 5);

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($agendamentos[$indice])) {
                $ag = $agendamentos[$indice];

                // Cancelar o agendamento
                $this->Agendamento_model->update($ag->id, [
                    'status' => 'cancelado',
                    'cancelado_por' => 'cliente',
                    'motivo_cancelamento' => 'Cancelado via WhatsApp Bot'
                ]);

                $data = date('d/m/Y', strtotime($ag->data));
                $hora = date('H:i', strtotime($ag->hora_inicio));

                $this->waha_lib->enviar_texto($numero,
                    "✅ Agendamento cancelado com sucesso!\n\n" .
                    "📅 *{$data}* às *{$hora}*\n" .
                    "💇 {$ag->servico_nome}\n\n" .
                    "_Digite *oi* para voltar ao menu._"
                );

                $this->Bot_conversa_model->resetar($conversa->id);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do agendamento.\n\n" .
            "_Digite *0* para voltar ao menu._"
        );
    }

    /**
     * Envia menu principal do bot
     */
    private function enviar_menu_principal($estabelecimento, $numero, $cliente = null) {
        $nome_cliente = $cliente ? $cliente->nome : 'Cliente';
        $primeiro_nome = explode(' ', $nome_cliente)[0];

        $mensagem = "Olá, {$primeiro_nome}! 👋\n\n";
        $mensagem .= "Bem-vindo(a) ao *{$estabelecimento->nome}*! 💈✨\n\n";
        $mensagem .= "Como posso ajudar?\n\n";
        $mensagem .= "1️⃣ *Agendar* - Fazer novo agendamento\n";
        $mensagem .= "2️⃣ *Meus Agendamentos* - Ver agendamentos\n";
        $mensagem .= "3️⃣ *Cancelar* - Cancelar agendamento\n";
        $mensagem .= "0️⃣ *Sair* - Encerrar atendimento\n\n";
        $mensagem .= "_Digite o número da opção desejada._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Inicia fluxo de agendamento
     */
    private function iniciar_agendamento($estabelecimento, $numero, $conversa, $cliente) {
        // Buscar serviços ativos
        $servicos = $this->Servico_model->get_by_estabelecimento($estabelecimento->id);

        if (empty($servicos)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há serviços disponíveis no momento. 😔\n\n" .
                "Por favor, entre em contato diretamente com o estabelecimento."
            );
            return;
        }

        // Atualizar estado para aguardando serviço
        $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_servico', []);

        $mensagem = "📋 *Nossos Serviços:*\n\n";

        foreach ($servicos as $i => $servico) {
            $num = $i + 1;
            $preco = number_format($servico->preco, 2, ',', '.');
            $duracao = $servico->duracao_minutos;
            $mensagem .= "{$num}. *{$servico->nome}*\n";
            $mensagem .= "   💰 R$ {$preco} | ⏱️ {$duracao} min\n\n";
        }

        $mensagem .= "_Digite o número do serviço desejado._\n";
        $mensagem .= "_Ou digite *0* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Inicia fluxo de cancelamento
     */
    private function iniciar_cancelamento($estabelecimento, $numero, $conversa, $cliente) {
        if (!$cliente) {
            $this->waha_lib->enviar_texto($numero,
                "Não encontrei agendamentos para este número. 🔍\n\n" .
                "Se você já é cliente, verifique se o número está cadastrado corretamente.\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            return;
        }

        $this->load->model('Agendamento_model');
        $agendamentos = $this->Agendamento_model->get_proximos_by_cliente($cliente->id, 5);

        if (empty($agendamentos)) {
            $this->waha_lib->enviar_texto($numero,
                "Você não tem agendamentos futuros para cancelar. 📅\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            return;
        }

        // Atualizar estado para aguardando cancelamento
        $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_cancelamento', []);

        $mensagem = "❌ *Cancelar Agendamento*\n\n";
        $mensagem .= "Selecione o agendamento que deseja cancelar:\n\n";

        foreach ($agendamentos as $i => $ag) {
            $num = $i + 1;
            $data = date('d/m/Y', strtotime($ag->data));
            $hora = date('H:i', strtotime($ag->hora_inicio));

            $mensagem .= "{$num}. 📅 *{$data}* às *{$hora}*\n";
            $mensagem .= "   💇 {$ag->servico_nome}\n";
            $mensagem .= "   👤 {$ag->profissional_nome}\n\n";
        }

        $mensagem .= "_Digite o número do agendamento._\n";
        $mensagem .= "_Ou digite *0* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Envia lista de profissionais
     */
    private function enviar_lista_profissionais($numero, $profissionais, $servico) {
        $mensagem = "👤 *Escolha o Profissional:*\n\n";
        $mensagem .= "Serviço: *{$servico->nome}*\n\n";

        foreach ($profissionais as $i => $prof) {
            $num = $i + 1;
            $mensagem .= "{$num}. *{$prof->nome}*\n";
        }

        $mensagem .= "\n_Digite o número do profissional._\n";
        $mensagem .= "_Ou digite *0* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Envia opções de data disponíveis
     */
    private function enviar_opcoes_data($estabelecimento, $numero, $dados) {
        $datas = $this->obter_datas_disponiveis($estabelecimento, $dados['profissional_id'], 7);

        if (empty($datas)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há datas disponíveis nos próximos dias. 😔\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            return;
        }

        $mensagem = "📅 *Escolha a Data:*\n\n";
        $mensagem .= "Serviço: *{$dados['servico_nome']}*\n";
        $mensagem .= "Profissional: *{$dados['profissional_nome']}*\n\n";

        $dias_semana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        foreach ($datas as $i => $data) {
            $num = $i + 1;
            $data_formatada = date('d/m/Y', strtotime($data));
            $dia_semana = $dias_semana[date('w', strtotime($data))];
            $mensagem .= "{$num}. *{$data_formatada}* ({$dia_semana})\n";
        }

        $mensagem .= "\n_Digite o número da data._\n";
        $mensagem .= "_Ou digite *0* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Envia opções de horário disponíveis
     */
    private function enviar_opcoes_hora($estabelecimento, $numero, $dados) {
        $horarios = $this->obter_horarios_disponiveis(
            $estabelecimento,
            $dados['profissional_id'],
            $dados['data'],
            $dados['servico_duracao']
        );

        if (empty($horarios)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há horários disponíveis nesta data. 😔\n\n" .
                "Por favor, escolha outra data.\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            return;
        }

        $data_formatada = date('d/m/Y', strtotime($dados['data']));

        $mensagem = "⏰ *Escolha o Horário:*\n\n";
        $mensagem .= "Serviço: *{$dados['servico_nome']}*\n";
        $mensagem .= "Profissional: *{$dados['profissional_nome']}*\n";
        $mensagem .= "Data: *{$data_formatada}*\n\n";

        foreach ($horarios as $i => $hora) {
            $num = $i + 1;
            $mensagem .= "{$num}. *{$hora}*\n";
        }

        $mensagem .= "\n_Digite o número do horário._\n";
        $mensagem .= "_Ou digite *0* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Envia mensagem de confirmação do agendamento
     */
    private function enviar_confirmacao($estabelecimento, $numero, $dados, $cliente) {
        $data_formatada = date('d/m/Y', strtotime($dados['data']));
        $preco_formatado = number_format($dados['servico_preco'], 2, ',', '.');

        $mensagem = "✅ *Confirme seu Agendamento:*\n\n";
        $mensagem .= "📋 Serviço: *{$dados['servico_nome']}*\n";
        $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n";
        $mensagem .= "📅 Data: *{$data_formatada}*\n";
        $mensagem .= "⏰ Horário: *{$dados['hora']}*\n";
        $mensagem .= "💰 Valor: *R$ {$preco_formatado}*\n\n";
        $mensagem .= "Deseja confirmar?\n\n";
        $mensagem .= "*1* ou *Sim* - Confirmar ✅\n";
        $mensagem .= "*2* ou *Não* - Cancelar ❌";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Finaliza o agendamento criando no banco
     * Gera PIX via Mercado Pago se necessário
     */
    private function finalizar_agendamento($estabelecimento, $numero, $dados, $conversa, $cliente) {
        log_message('debug', 'Bot: finalizar_agendamento - iniciando');

        // Se não tem cliente, criar um novo
        if (!$cliente) {
            log_message('debug', 'Bot: criando novo cliente para ' . $numero);
            $cliente_id = $this->Cliente_model->create([
                'estabelecimento_id' => $estabelecimento->id,
                'nome' => 'Cliente WhatsApp',
                'whatsapp' => $numero,
                'origem' => 'whatsapp_bot'
            ]);
            $cliente = $this->Cliente_model->get_by_id($cliente_id);
        }

        // Calcular hora de término
        $hora_inicio = $dados['hora'] . ':00';
        $duracao = $dados['servico_duracao'];
        $hora_fim = date('H:i:s', strtotime($hora_inicio) + ($duracao * 60));

        log_message('debug', 'Bot: criando agendamento - data=' . $dados['data'] . ', hora=' . $hora_inicio);

        // Criar agendamento
        $agendamento_data = [
            'estabelecimento_id' => $estabelecimento->id,
            'cliente_id' => $cliente->id,
            'profissional_id' => $dados['profissional_id'],
            'servico_id' => $dados['servico_id'],
            'data' => $dados['data'],
            'hora_inicio' => $hora_inicio,
            'hora_fim' => $hora_fim,
            'status' => 'pendente',
            'observacoes' => 'Agendado via WhatsApp Bot'
        ];

        // Verificar se estabelecimento exige pagamento antecipado
        $requer_pagamento = $estabelecimento->agendamento_requer_pagamento &&
                           $estabelecimento->agendamento_requer_pagamento != 'nao';

        if ($requer_pagamento) {
            $agendamento_data['pagamento_status'] = 'pendente';
        } else {
            $agendamento_data['pagamento_status'] = 'nao_requerido';
        }

        log_message('debug', 'Bot: dados do agendamento: ' . json_encode($agendamento_data));

        $agendamento_id = $this->Agendamento_model->create($agendamento_data);

        log_message('debug', 'Bot: agendamento_id retornado: ' . ($agendamento_id ? $agendamento_id : 'FALHOU'));

        if (!$agendamento_id) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, ocorreu um erro ao criar o agendamento. 😔\n\n" .
                "Por favor, tente novamente ou entre em contato diretamente.\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        $data_formatada = date('d/m/Y', strtotime($dados['data']));
        $preco_formatado = number_format($dados['servico_preco'], 2, ',', '.');

        // Se exige pagamento antecipado, gerar PIX via Mercado Pago
        if ($requer_pagamento) {
            $this->load->library('mercadopago_lib');
            $this->load->model('Pagamento_model');

            // Calcular valor do pagamento
            $valor_pagamento = $dados['servico_preco'];
            if ($estabelecimento->agendamento_requer_pagamento == 'taxa_fixa') {
                $valor_pagamento = floatval($estabelecimento->agendamento_taxa_fixa);
            }

            // Usar credenciais do estabelecimento
            $access_token = $estabelecimento->mp_sandbox
                ? $estabelecimento->mp_access_token_test
                : $estabelecimento->mp_access_token_prod;
            $public_key = $estabelecimento->mp_sandbox
                ? $estabelecimento->mp_public_key_test
                : $estabelecimento->mp_public_key_prod;

            $this->mercadopago_lib->set_credentials($access_token, $public_key);

            log_message('debug', 'Bot: Gerando PIX - valor=' . $valor_pagamento);

            // Gerar PIX
            $pix_result = $this->mercadopago_lib->criar_pix_agendamento(
                $agendamento_id,
                $valor_pagamento,
                [
                    'nome' => $cliente->nome ?? 'Cliente WhatsApp',
                    'email' => $cliente->email ?: 'cliente@agendapro.com',
                    'cpf' => $cliente->cpf ?: ''
                ],
                $estabelecimento->id
            );

            log_message('debug', 'Bot: PIX resultado - status=' . ($pix_result['status'] ?? 'NULL'));

            if ($pix_result && isset($pix_result['response']) && in_array($pix_result['status'], [200, 201])) {
                $pix_data = $pix_result['response'];

                // Gerar token de pagamento
                $token = $this->Agendamento_model->gerar_token_pagamento();

                // Tempo de expiração configurado no estabelecimento
                $tempo_expiracao = $estabelecimento->agendamento_tempo_expiracao_pix ?? 30;
                $expira_em = date('Y-m-d H:i:s', strtotime("+{$tempo_expiracao} minutes"));

                // Salvar dados do PIX no agendamento
                $this->Agendamento_model->update($agendamento_id, [
                    'pagamento_status' => 'pendente',
                    'pagamento_valor' => $valor_pagamento,
                    'pagamento_pix_qrcode' => $pix_data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                    'pagamento_pix_copia_cola' => $pix_data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'pagamento_expira_em' => $expira_em,
                    'pagamento_token' => $token,
                    'pagamento_lembrete_enviado' => 0
                ]);

                // Criar registro de pagamento
                $this->Pagamento_model->criar_agendamento([
                    'estabelecimento_id' => $estabelecimento->id,
                    'agendamento_id' => $agendamento_id,
                    'valor' => $valor_pagamento,
                    'mercadopago_id' => $pix_data['id'],
                    'payment_data' => $pix_data
                ]);

                $link_pagamento = base_url('pagamento/' . $token);
                $valor_pag_formatado = number_format($valor_pagamento, 2, ',', '.');

                $mensagem = "🎉 *Agendamento Criado!*\n\n";
                $mensagem .= "📋 Serviço: *{$dados['servico_nome']}*\n";
                $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n";
                $mensagem .= "📅 Data: *{$data_formatada}*\n";
                $mensagem .= "⏰ Horário: *{$dados['hora']}*\n";
                $mensagem .= "💰 Valor do Serviço: *R$ {$preco_formatado}*\n\n";
                $mensagem .= "💳 *PAGAMENTO PENDENTE*\n";
                $mensagem .= "Valor a pagar: *R$ {$valor_pag_formatado}*\n";
                $mensagem .= "⏰ Expira em *{$tempo_expiracao} minutos*\n\n";
                $mensagem .= "🔗 *Acesse o link para pagar:*\n{$link_pagamento}\n\n";
                $mensagem .= "⚠️ _Seu agendamento só será confirmado após o pagamento._\n\n";
                $mensagem .= "_Digite *oi* para voltar ao menu._";

            } else {
                // Erro ao gerar PIX - cancelar agendamento
                log_message('error', 'Bot: Erro ao gerar PIX - ' . json_encode($pix_result));
                $this->Agendamento_model->update($agendamento_id, ['status' => 'cancelado']);

                $mensagem = "Desculpe, ocorreu um erro ao gerar o pagamento PIX. 😔\n\n";
                $mensagem .= "Por favor, tente novamente ou entre em contato diretamente.\n\n";
                $mensagem .= "_Digite *oi* para voltar ao menu._";
            }
        } else {
            // Não requer pagamento - confirmar automaticamente
            $this->Agendamento_model->update($agendamento_id, ['status' => 'confirmado']);

            $mensagem = "🎉 *Agendamento Confirmado!*\n\n";
            $mensagem .= "📋 Serviço: *{$dados['servico_nome']}*\n";
            $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n";
            $mensagem .= "📅 Data: *{$data_formatada}*\n";
            $mensagem .= "⏰ Horário: *{$dados['hora']}*\n";
            $mensagem .= "💰 Valor: *R$ {$preco_formatado}*\n\n";
            $mensagem .= "📍 *{$estabelecimento->nome}*\n";
            if ($estabelecimento->endereco) {
                $mensagem .= "📌 {$estabelecimento->endereco}\n";
            }
            $mensagem .= "\nAté lá! 👋\n\n";
            $mensagem .= "_Digite *oi* para voltar ao menu._";
        }

        $this->waha_lib->enviar_texto($numero, $mensagem);

        // Resetar conversa
        $this->Bot_conversa_model->resetar($conversa->id);
    }

    /**
     * Obtém datas disponíveis para agendamento
     * Usa horários do estabelecimento (tabela horarios_estabelecimento)
     */
    private function obter_datas_disponiveis($estabelecimento, $profissional_id, $dias = 7) {
        $this->load->model('Horario_estabelecimento_model');

        $datas = [];
        $data_atual = date('Y-m-d');

        log_message('debug', "Bot: obter_datas_disponiveis - estabelecimento_id={$estabelecimento->id}, data_atual={$data_atual}");

        for ($i = 0; $i < 14 && count($datas) < $dias; $i++) {
            $data = date('Y-m-d', strtotime($data_atual . " +{$i} days"));
            // horarios_estabelecimento usa 0=Domingo, 6=Sábado (formato PHP date('w'))
            $dia_semana = date('w', strtotime($data));

            // Verificar se estabelecimento está aberto neste dia
            $horario = $this->Horario_estabelecimento_model->get_by_dia($estabelecimento->id, $dia_semana);

            log_message('debug', "Bot: verificando data={$data}, dia_semana={$dia_semana}, horario=" . ($horario ? "encontrado (ativo={$horario->ativo})" : "NAO encontrado"));

            if ($horario && $horario->ativo) {
                $datas[] = $data;
            }
        }

        log_message('debug', "Bot: datas encontradas=" . count($datas));

        return $datas;
    }

    /**
     * Obtém horários disponíveis para uma data
     * Usa horários do estabelecimento (tabela horarios_estabelecimento)
     */
    private function obter_horarios_disponiveis($estabelecimento, $profissional_id, $data, $duracao_servico) {
        $this->load->model('Horario_estabelecimento_model');

        // horarios_estabelecimento usa 0=Domingo, 6=Sábado (formato PHP date('w'))
        $dia_semana = date('w', strtotime($data));
        $horario_dia = $this->Horario_estabelecimento_model->get_by_dia($estabelecimento->id, $dia_semana);

        if (!$horario_dia || !$horario_dia->ativo) {
            return [];
        }

        $horarios = [];
        $intervalo = $estabelecimento->intervalo_agendamento ?? 30;

        // Buscar agendamentos existentes
        $agendamentos_existentes = $this->Agendamento_model->get_by_profissional_data($profissional_id, $data);

        // Processar período da manhã (antes do almoço)
        $hora_atual = strtotime($horario_dia->hora_inicio);
        $hora_fim = strtotime($horario_dia->hora_fim);

        // Se tem almoço, dividir em dois períodos
        $almoco_inicio = null;
        $almoco_fim = null;
        if ($horario_dia->almoco_ativo && $horario_dia->almoco_inicio && $horario_dia->almoco_fim) {
            $almoco_inicio = strtotime($horario_dia->almoco_inicio);
            $almoco_fim = strtotime($horario_dia->almoco_fim);
        }

        // Se for hoje, começar do horário atual + 1 hora
        if ($data == date('Y-m-d')) {
            $hora_minima = strtotime('+1 hour');
            if ($hora_atual < $hora_minima) {
                $hora_atual = $hora_minima;
                // Arredondar para o próximo intervalo
                $minutos = date('i', $hora_atual);
                $resto = $minutos % $intervalo;
                if ($resto > 0) {
                    $hora_atual = strtotime('+' . ($intervalo - $resto) . ' minutes', $hora_atual);
                }
            }
        }

        while ($hora_atual + ($duracao_servico * 60) <= $hora_fim) {
            $hora_str = date('H:i', $hora_atual);
            $hora_fim_servico = $hora_atual + ($duracao_servico * 60);

            // Verificar se está no horário de almoço
            $no_almoco = false;
            if ($almoco_inicio && $almoco_fim) {
                if ($hora_atual < $almoco_fim && $hora_fim_servico > $almoco_inicio) {
                    $no_almoco = true;
                }
            }

            // Verificar se não conflita com agendamentos existentes
            $conflito = false;
            if (!$no_almoco) {
                foreach ($agendamentos_existentes as $ag) {
                    if ($ag->status == 'cancelado') continue;

                    $ag_inicio = strtotime($ag->hora_inicio);
                    $ag_fim = strtotime($ag->hora_fim);

                    if ($hora_atual < $ag_fim && $hora_fim_servico > $ag_inicio) {
                        $conflito = true;
                        break;
                    }
                }
            }

            if (!$no_almoco && !$conflito && !in_array($hora_str, $horarios)) {
                $horarios[] = $hora_str;
            }

            $hora_atual = strtotime("+{$intervalo} minutes", $hora_atual);
        }

        // Ordenar horários
        sort($horarios);

        return $horarios;
    }

    /**
     * Consulta agendamentos do cliente
     */
    private function consultar_agendamentos($estabelecimento, $numero, $cliente) {
        if (!$cliente) {
            $this->waha_lib->enviar_texto($numero,
                "Não encontrei agendamentos para este número. 🔍\n\n" .
                "Se você já é cliente, verifique se o número está cadastrado corretamente.\n\n" .
                "_Digite *1* para fazer um novo agendamento._"
            );
            return;
        }

        $this->load->model('Agendamento_model');
        $agendamentos = $this->Agendamento_model->get_proximos_by_cliente($cliente->id, 5);

        if (empty($agendamentos)) {
            $this->waha_lib->enviar_texto($numero,
                "Você não tem agendamentos futuros. 📅\n\n" .
                "_Digite *1* para fazer um novo agendamento._"
            );
            return;
        }

        $mensagem = "📅 *Seus Próximos Agendamentos:*\n\n";

        foreach ($agendamentos as $ag) {
            $data = date('d/m/Y', strtotime($ag->data));
            $hora = date('H:i', strtotime($ag->hora_inicio));
            $status_emoji = $ag->status == 'confirmado' ? '✅' : '⏳';

            $mensagem .= "{$status_emoji} *{$data}* às *{$hora}*\n";
            $mensagem .= "   {$ag->servico_nome}\n";
            $mensagem .= "   com {$ag->profissional_nome}\n\n";
        }

        $mensagem .= "_Digite *0* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }


    /**
     * Bot de suporte para o SaaS Admin
     */
    private function processar_bot_suporte($numero, $mensagem, $message_id) {
        $this->load->library('waha_lib');

        if (!$this->waha_lib->set_saas_admin()) {
            log_message('error', 'Bot Suporte: Falha ao configurar WAHA');
            return;
        }

        $msg = strtolower(trim($mensagem));

        // Menu de suporte
        if (in_array($msg, ['oi', 'olá', 'ola', 'menu', 'suporte', 'ajuda'])) {
            $this->waha_lib->enviar_texto($numero,
                "Olá! 👋\n\n" .
                "Bem-vindo ao suporte do *AgendaPro*! 🚀\n\n" .
                "Como posso ajudar?\n\n" .
                "1️⃣ Dúvidas sobre o sistema\n" .
                "2️⃣ Problemas técnicos\n" .
                "3️⃣ Falar com atendente\n\n" .
                "_Digite o número da opção._"
            );
            return;
        }

        // Resposta padrão
        $this->waha_lib->enviar_texto($numero,
            "Obrigado pela mensagem! 📩\n\n" .
            "Um de nossos atendentes irá responder em breve.\n\n" .
            "_Digite *suporte* para ver as opções de ajuda._"
        );
    }

    /**
     * Detecta o tipo de mensagem recebida
     */
    private function detectar_tipo_mensagem($payload) {
        if (isset($payload['hasMedia']) && $payload['hasMedia']) {
            $type = $payload['type'] ?? 'unknown';

            switch ($type) {
                case 'image':
                    return 'imagem';
                case 'audio':
                case 'ptt':
                    return 'audio';
                case 'document':
                    return 'documento';
                case 'location':
                    return 'localizacao';
                default:
                    return 'texto';
            }
        }

        return 'texto';
    }

    /**
     * Salva log de mensagem no banco
     */
    private function salvar_log_mensagem($dados) {
        // Verificar se tabela existe antes de inserir
        if ($this->db->table_exists('whatsapp_mensagens')) {
            $this->db->insert('whatsapp_mensagens', $dados);
        }
    }

    /**
     * Retorna resposta JSON
     */
    private function output_json($data, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
