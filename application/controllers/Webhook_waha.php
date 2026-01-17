<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Webhook WAHA - WhatsApp HTTP API6
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
            $resposta = "Desculpe, não entendi. 🤔\n\nDigite *menu* para ver as opções.";
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

        // Extrair pushName (nome do contato no WhatsApp)
        $pushName = $payload['_data']['Info']['PushName'] ?? null;
        if (!$pushName && isset($payload['pushName'])) {
            $pushName = $payload['pushName'];
        }

        // Extrair número (preservar formato @lid ou @c.us para compatibilidade)
        // Números novos do WhatsApp usam @lid, números antigos usam @c.us
        $numero_completo = $from; // Preservar formato original
        $numero = preg_replace('/[^0-9]/', '', str_replace(['@c.us', '@lid', '@s.whatsapp.net'], '', $from));

        log_message('info', "WAHA Mensagem de {$numero}" . ($pushName ? " ({$pushName})" : "") . ": " . substr($body, 0, 100));

        // Verificação de Idempotência: Se mensagem já foi processada, ignorar
        if ($message_id && $this->db->table_exists('whatsapp_mensagens')) {
            $msg_existente = $this->db->where('message_id', $message_id)->count_all_results('whatsapp_mensagens');
            if ($msg_existente > 0) {
                log_message('warning', "WAHA Webhook: Mensagem duplicada ignorada - ID: {$message_id}");
                return;
            }
        }

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
                // Usar número completo (com @lid ou @c.us) para compatibilidade com novos números WhatsApp
                $this->processar_bot_agendamento($estabelecimento, $numero_completo, $body, $message_id, $pushName);
            }
        } else {
            // Mensagem para o SaaS Admin - bot de suporte
            $this->processar_bot_suporte($numero_completo, $body, $message_id);
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
    private function processar_bot_agendamento($estabelecimento, $numero, $mensagem, $message_id, $pushName = null) {
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

        // Armazenar pushName na conversa se disponível
        if ($pushName && $conversa) {
            // Verificar se dados já é array ou precisa decodificar
            $dados_conversa = is_array($conversa->dados) ? $conversa->dados : ($conversa->dados ? json_decode($conversa->dados, true) : []);
            if (!isset($dados_conversa['pushName'])) {
                $dados_conversa['pushName'] = $pushName;
                $this->Bot_conversa_model->atualizar_estado($conversa->id, $conversa->estado, $dados_conversa);
                log_message('info', "Bot: pushName armazenado na conversa - numero={$numero}, pushName={$pushName}");
            }
        }

        // Verificar se é cliente existente
        $cliente = $this->Cliente_model->get_by_whatsapp($numero, $estabelecimento->id);

        // Atualizar cliente na conversa se encontrado
        if ($cliente && !$conversa->cliente_id) {
            $this->Bot_conversa_model->set_cliente($conversa->id, $cliente->id);
        }

        // Se cliente existe mas tem nome genérico e temos pushName, atualizar
        if ($cliente && $pushName && $cliente->nome === 'Cliente WhatsApp') {
            $this->Cliente_model->update($cliente->id, ['nome' => $pushName]);
            log_message('info', "Bot: Nome do cliente atualizado - id={$cliente->id}, novo_nome={$pushName}");
            // Recarregar cliente com dados atualizados
            $cliente = $this->Cliente_model->get_by_id($cliente->id);
        }

        // Comandos globais (funcionam em qualquer estado)
        $comandos_inicio = ['oi', 'olá', 'ola', 'hi', 'hello', 'bom dia', 'boa tarde', 'boa noite'];
        $comandos_menu = ['menu', 'inicio', 'início'];
        $comandos_sair = ['0', 'sair', 'tchau', 'obrigado', 'obrigada'];

        // Comandos de início - resetam conversa e mostram menu
        if (in_array($msg, $comandos_inicio)) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
            return;
        }

        // Comandos para voltar ao menu - resetam sem encerrar
        // NOTA: "voltar" foi removido daqui para ser processado dentro de cada estado
        if (in_array($msg, $comandos_menu)) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Voltando ao menu principal... 🔙\n\n"
            );
            $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
            return;
        }

        // Comando para sair - pede confirmação se não estiver no menu
        if (in_array($msg, $comandos_sair)) {
            // Se já está no menu ou em estado encerrada, encerra direto
            if ($conversa->estado === 'menu' || $conversa->estado === 'encerrada') {
                $this->Bot_conversa_model->encerrar($conversa->id);
                $this->waha_lib->enviar_texto($numero,
                    "Obrigado por entrar em contato! 😊\n\n" .
                    "Até a próxima! 👋\n\n" .
                    "_Digite *oi* quando precisar de mim novamente._"
                );
                return;
            }

            // Se está em outro estado, pede confirmação
            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'confirmando_saida', []);
            $this->waha_lib->enviar_texto($numero,
                "Você tem certeza que deseja sair? 🤔\n\n" .
                "*1* ou *Sim* - Confirmar saída\n" .
                "*2* ou *Não* - Continuar conversa\n\n" .
                "_Ou digite *menu* para voltar ao menu principal._"
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

            case 'gerenciando_agendamento':
                $this->processar_estado_gerenciando($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'aguardando_acao_agendamento':
                $this->processar_estado_acao_agendamento($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'reagendando_data':
                $this->processar_estado_reagendando_data($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'reagendando_hora':
                $this->processar_estado_reagendando_hora($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'confirmando_reagendamento':
                $this->processar_estado_confirmando_reagendamento($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'confirmando_agendamento':
                $this->processar_estado_confirmando_agendamento($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'confirmando_cancelamento':
                $this->processar_estado_confirmando_cancelamento($estabelecimento, $numero, $msg, $conversa, $cliente);
                break;

            case 'confirmando_saida':
                $this->processar_estado_confirmando_saida($estabelecimento, $numero, $msg, $conversa, $cliente);
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
            $this->iniciar_gerenciar_agendamentos($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

        if (in_array($msg, ['3', 'cancelar'])) {
            $this->iniciar_cancelamento($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

        // Não reconheceu comando no menu
        $this->waha_lib->enviar_texto($numero,
            "Desculpe, não entendi. 🤔\n\n" .
            "Digite *menu* para ver as opções."
        );
    }

    /**
     * Processa estado: Aguardando seleção de serviço
     */
    private function processar_estado_servico($estabelecimento, $numero, $msg, $conversa, $cliente) {
        // Comando voltar - retorna para menu principal
        if (in_array($msg, ['voltar', 'anterior'])) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
            return;
        }

        // Buscar serviços e filtrar apenas os que têm profissionais
        $servicos_todos = $this->Servico_model->get_by_estabelecimento($estabelecimento->id);
        $servicos = [];
        foreach ($servicos_todos as $servico) {
            $profissionais = $this->Profissional_model->get_by_servico($servico->id, $estabelecimento->id);
            if (!empty($profissionais)) {
                $servicos[] = $servico;
            }
        }

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
                        "_Digite *menu* para voltar ao menu._"
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
            "_Digite *voltar* para o menu principal._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de profissional
     */
    private function processar_estado_profissional($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para seleção de serviço
        if (in_array($msg, ['voltar', 'anterior'])) {
            $this->iniciar_agendamento($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

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
            "_Digite *voltar* para escolher outro serviço ou *menu* para o menu principal._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de data
     */
    private function processar_estado_data($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para seleção de serviço
        if (in_array($msg, ['voltar', 'anterior'])) {
            $this->iniciar_agendamento($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

        $duracao = $dados['servico_duracao'] ?? 30;
        $datas_disponiveis = $this->obter_datas_disponiveis($estabelecimento, $dados['profissional_id'], 7, $duracao);

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
            "_Digite *voltar* para escolher outro serviço ou *menu* para o menu principal._"
        );
    }

    /**
     * Processa estado: Aguardando seleção de hora
     */
    private function processar_estado_hora($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para seleção de data
        if (in_array($msg, ['voltar', 'anterior'])) {
            // Remove a hora e volta para data
            unset($dados['hora']);
            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_data', $dados);
            $this->enviar_opcoes_data($estabelecimento, $numero, $dados);
            return;
        }

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
            "_Digite *voltar* para escolher outra data ou *menu* para o menu principal._"
        );
    }

    /**
     * Processa estado: Confirmação do agendamento
     */
    private function processar_estado_confirmacao($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para seleção de horário
        if (in_array($msg, ['voltar', 'anterior'])) {
            // Remove a hora e volta para seleção de horário
            unset($dados['hora']);
            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_hora', $dados);
            $this->enviar_opcoes_hora($estabelecimento, $numero, $dados);
            return;
        }

        if (in_array($msg, ['sim', 's', '1', 'confirmar', 'confirmo'])) {
            $this->finalizar_agendamento($estabelecimento, $numero, $dados, $conversa, $cliente);
            return;
        }

        if (in_array($msg, ['não', 'nao', 'n', '2', 'cancelar'])) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Agendamento cancelado. ❌\n\n" .
                "_Digite *menu* para voltar ao menu._"
            );
            return;
        }

        $this->waha_lib->enviar_texto($numero,
            "Por favor, responda:\n\n" .
            "*1* ou *Sim* - Para confirmar\n" .
            "*2* ou *Não* - Para cancelar\n\n" .
            "_Digite *voltar* para escolher outro horário ou *menu* para o menu principal._"
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
                    "_Digite *menu* para voltar ao menu ou *0* para sair._"
                );

                $this->Bot_conversa_model->resetar($conversa->id);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do agendamento.\n\n" .
            "_Digite *menu* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Confirmando saída
     */
    private function processar_estado_confirmando_saida($estabelecimento, $numero, $msg, $conversa, $cliente) {
        // Confirmar saída
        if (in_array($msg, ['1', 'sim', 's'])) {
            $this->Bot_conversa_model->encerrar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Obrigado por entrar em contato! 😊\n\n" .
                "Até a próxima! 👋\n\n" .
                "_Digite *oi* quando precisar de mim novamente._"
            );
            return;
        }

        // Continuar conversa - volta ao menu
        if (in_array($msg, ['2', 'não', 'nao', 'n'])) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Ok! Continuando... 😊\n\n"
            );
            $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
            return;
        }

        // Opção inválida
        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, escolha:\n\n" .
            "*1* ou *Sim* - Confirmar saída\n" .
            "*2* ou *Não* - Continuar conversa"
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
        $mensagem .= "💡 *Dica:* Digite *menu* a qualquer momento para retornar aqui.";

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

        // Filtrar apenas serviços que têm profissionais ativos
        $servicos_disponiveis = [];
        foreach ($servicos as $servico) {
            $profissionais = $this->Profissional_model->get_by_servico($servico->id, $estabelecimento->id);
            if (!empty($profissionais)) {
                $servicos_disponiveis[] = $servico;
            }
        }

        if (empty($servicos_disponiveis)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há serviços com profissionais disponíveis no momento. 😔\n\n" .
                "_Digite *menu* para voltar ao menu._"
            );
            return;
        }

        // Atualizar estado para aguardando serviço
        $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_servico', []);

        $mensagem = "📋 *Nossos Serviços:*\n\n";

        foreach ($servicos_disponiveis as $i => $servico) {
            $num = $i + 1;
            $preco = number_format($servico->preco, 2, ',', '.');
            $mensagem .= "{$num}. *{$servico->nome}*\n";
            $mensagem .= "   💰 R$ {$preco}\n\n";
        }

        $mensagem .= "_Digite o número do serviço desejado._\n";
        $mensagem .= "_Ou digite *voltar* para o menu principal._";

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
                "_Digite *menu* para voltar ao menu._"
            );
            return;
        }

        $this->load->model('Agendamento_model');
        $agendamentos = $this->Agendamento_model->get_proximos_by_cliente($cliente->id, 5);

        if (empty($agendamentos)) {
            $this->waha_lib->enviar_texto($numero,
                "Você não tem agendamentos futuros para cancelar. 📅\n\n" .
                "_Digite *menu* para voltar ao menu._"
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
        $mensagem .= "_Ou digite *menu* para voltar ao menu._";

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
        $mensagem .= "_Ou digite *menu* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Envia opções de data disponíveis
     */
    private function enviar_opcoes_data($estabelecimento, $numero, $dados) {
        $duracao = $dados['servico_duracao'] ?? 30;
        $datas = $this->obter_datas_disponiveis($estabelecimento, $dados['profissional_id'], 7, $duracao);

        if (empty($datas)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há datas disponíveis nos próximos dias. 😔\n\n" .
                "_Digite *menu* para voltar ao menu._"
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
        $mensagem .= "_Ou digite *voltar* para escolher outro serviço ou *menu* para o menu principal._";

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
                "_Digite *menu* para voltar ao menu._"
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
        $mensagem .= "_Ou digite *voltar* para escolher outra data ou *menu* para o menu principal._";

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
        $mensagem .= "*2* ou *Não* - Cancelar ❌\n\n";
        $mensagem .= "_Ou digite *voltar* para escolher outro horário._";

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
            // Tentar obter pushName da conversa
            $nome_cliente = 'Cliente WhatsApp';
            if (isset($dados['pushName']) && !empty($dados['pushName'])) {
                $nome_cliente = $dados['pushName'];
                log_message('info', 'Bot: pushName encontrado nos dados - ' . $nome_cliente);
            } else {
                log_message('warning', 'Bot: pushName NAO encontrado nos dados - usando fallback. Dados: ' . json_encode($dados));
            }

            log_message('info', 'Bot: criando novo cliente para ' . $numero . ' - Nome: ' . $nome_cliente);
            $cliente_id = $this->Cliente_model->create([
                'estabelecimento_id' => $estabelecimento->id,
                'nome' => $nome_cliente,
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

        $agendamento_id = $this->Agendamento_model->create($agendamento_data, false); // false = não enviar notificação automática

        log_message('debug', 'Bot: agendamento_id retornado: ' . ($agendamento_id ? $agendamento_id : 'FALHOU'));

        if (!$agendamento_id) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, ocorreu um erro ao criar o agendamento. 😔\n\n" .
                "Por favor, tente novamente ou entre em contato diretamente.\n\n" .
                "_Digite *menu* para voltar ao menu._"
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
                $mensagem .= "_Digite *menu* para voltar ao menu._";

            } else {
                // Erro ao gerar PIX - cancelar agendamento
                log_message('error', 'Bot: Erro ao gerar PIX - ' . json_encode($pix_result));
                $this->Agendamento_model->update($agendamento_id, ['status' => 'cancelado']);

                $mensagem = "Desculpe, ocorreu um erro ao gerar o pagamento PIX. 😔\n\n";
                $mensagem .= "Por favor, tente novamente ou entre em contato diretamente.\n\n";
                $mensagem .= "_Digite *menu* para voltar ao menu._";
            }
        } else {
            // Não requer pagamento - manter como pendente para confirmação posterior
            // Status permanece 'pendente' - será confirmado via cron job

            $mensagem = "🎉 *Agendamento Criado!*\n\n";
            $mensagem .= "📋 Serviço: *{$dados['servico_nome']}*\n";
            $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n";
            $mensagem .= "📅 Data: *{$data_formatada}*\n";
            $mensagem .= "⏰ Horário: *{$dados['hora']}*\n";
            $mensagem .= "💰 Valor: *R$ {$preco_formatado}*\n\n";
            $mensagem .= "📍 *{$estabelecimento->nome}*\n";
            if ($estabelecimento->endereco) {
                $mensagem .= "📌 {$estabelecimento->endereco}\n";
            }
            $mensagem .= "\n✅ Você receberá uma mensagem para confirmar sua presença próximo à data do agendamento.\n\n";
            $mensagem .= "Até lá! 👋\n\n";
            $mensagem .= "_Digite *menu* para voltar ao menu ou *0* para sair._";
        }

        $this->waha_lib->enviar_texto($numero, $mensagem);

        // Resetar conversa
        $this->Bot_conversa_model->resetar($conversa->id);
    }

    /**
     * Obtém datas disponíveis para agendamento
     * Usa horários do estabelecimento (tabela horarios_estabelecimento)
     * Retorna apenas datas que realmente têm horários disponíveis
     * Filtra feriados cadastrados
     */
    private function obter_datas_disponiveis($estabelecimento, $profissional_id, $dias = 7, $duracao_servico = 30, $excluir_agendamento_id = null) {
        $this->load->model('Horario_estabelecimento_model');
        $this->load->model('Feriado_model');

        $datas = [];
        $data_atual = date('Y-m-d');

        log_message('debug', "Bot: obter_datas_disponiveis - estabelecimento_id={$estabelecimento->id}, data_atual={$data_atual}");

        // Buscar até 30 dias para garantir que encontramos datas suficientes com horários disponíveis
        for ($i = 0; $i < 30 && count($datas) < $dias; $i++) {
            $data = date('Y-m-d', strtotime($data_atual . " +{$i} days"));
            // horarios_estabelecimento usa 0=Domingo, 6=Sábado (formato PHP date('w'))
            $dia_semana = date('w', strtotime($data));

            // Verificar se é feriado
            $eh_feriado = $this->Feriado_model->is_feriado($data, $estabelecimento->id);
            if ($eh_feriado) {
                log_message('debug', "Bot: data {$data} ignorada - é feriado");
                continue;
            }

            // Verificar se estabelecimento está aberto neste dia
            $horario = $this->Horario_estabelecimento_model->get_by_dia($estabelecimento->id, $dia_semana);

            log_message('debug', "Bot: verificando data={$data}, dia_semana={$dia_semana}, horario=" . ($horario ? "encontrado (ativo={$horario->ativo})" : "NAO encontrado"));

            if ($horario && $horario->ativo) {
                // Verificar se realmente existem horários disponíveis nesta data
                $horarios_disponiveis = $this->obter_horarios_disponiveis($estabelecimento, $profissional_id, $data, $duracao_servico, $excluir_agendamento_id);

                if (!empty($horarios_disponiveis)) {
                    $datas[] = $data;
                    log_message('debug', "Bot: data {$data} adicionada - " . count($horarios_disponiveis) . " horários disponíveis");
                } else {
                    log_message('debug', "Bot: data {$data} ignorada - sem horários disponíveis");
                }
            }
        }

        log_message('debug', "Bot: datas encontradas=" . count($datas));

        return $datas;
    }

    /**
     * Obtém horários disponíveis para uma data
     * Usa horários do estabelecimento (tabela horarios_estabelecimento)
     */
    private function obter_horarios_disponiveis($estabelecimento, $profissional_id, $data, $duracao_servico, $excluir_agendamento_id = null) {
        $this->load->model('Horario_estabelecimento_model');

        log_message('info', "Bot FILTRO INICIO: data={$data}, profissional_id={$profissional_id}, duracao={$duracao_servico}, excluir_id=" . ($excluir_agendamento_id ?? 'NULL'));

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

        log_message('info', "Bot FILTRO: agendamentos_existentes encontrados: " . count($agendamentos_existentes));
        foreach ($agendamentos_existentes as $ag) {
            log_message('info', "Bot FILTRO AG: id={$ag->id}, status={$ag->status}, data={$ag->data}, hora={$ag->hora_inicio}-{$ag->hora_fim}");
        }

        // Processar período da manhã (antes do almoço)
        $hora_atual = strtotime($horario_dia->hora_inicio);
        $hora_fim = strtotime($horario_dia->hora_fim);

        // Se tem almoço, dividir em dois períodos
        $almoco_inicio = null;
        $almoco_fim = null;
        if ($horario_dia->almoco_ativo && $horario_dia->almoco_inicio && $horario_dia->almoco_fim) {
            // CORREÇÃO: Usar data + hora para comparação correta de timestamps
            $almoco_inicio = strtotime($data . ' ' . $horario_dia->almoco_inicio);
            $almoco_fim = strtotime($data . ' ' . $horario_dia->almoco_fim);
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

            // CORREÇÃO: Usar data + hora para comparação de timestamps
            $slot_inicio_ts = strtotime($data . ' ' . $hora_str);
            $slot_fim_ts = strtotime("+{$duracao_servico} minutes", $slot_inicio_ts);

            // Verificar se está no horário de almoço
            $no_almoco = false;
            if ($almoco_inicio && $almoco_fim) {
                // Bloquear se o horário do serviço sobrepõe com o almoço
                if ($slot_inicio_ts < $almoco_fim && $slot_fim_ts > $almoco_inicio) {
                    $no_almoco = true;
                    log_message('debug', "Bot: horario={$hora_str} bloqueado por almoço (almoco: " . date('H:i', $almoco_inicio) . "-" . date('H:i', $almoco_fim) . ", servico_fim: " . date('H:i', $slot_fim_ts) . ")");
                }
            }

            // Verificar se não conflita com agendamentos existentes
            $conflito = false;
            if (!$no_almoco) {
                foreach ($agendamentos_existentes as $ag) {
                    // CORREÇÃO 3: Filtro de status mais robusto
                    if (!in_array($ag->status, ['confirmado', 'pendente', 'em_atendimento'])) {
                        continue;
                    }

                    // Excluir agendamento específico se necessário
                    if ($excluir_agendamento_id && $ag->id == $excluir_agendamento_id) {
                        continue;
                    }

                    // CORREÇÃO 2: Garantir que hora_inicio e hora_fim usem a data correta
                    $ag_inicio_str = (strlen($ag->hora_inicio) <= 8)
                        ? ($ag->data . ' ' . $ag->hora_inicio)
                        : $ag->hora_inicio;

                    $ag_fim_str = (strlen($ag->hora_fim) <= 8)
                        ? ($ag->data . ' ' . $ag->hora_fim)
                        : $ag->hora_fim;

                    $ag_inicio = strtotime($ag_inicio_str);
                    $ag_fim = strtotime($ag_fim_str);

                    // Verificar sobreposição: serviço inicia antes do fim do agendamento E termina depois do início
                    if ($slot_inicio_ts < $ag_fim && $slot_fim_ts > $ag_inicio) {
                        $conflito = true;
                        log_message('debug', "Bot: CONFLITO - horario={$hora_str}, ag_id={$ag->id}, ag_status={$ag->status}, ag_data={$ag->data}, ag_hora={$ag->hora_inicio}-{$ag->hora_fim}, slot_ts=" . date('Y-m-d H:i', $slot_inicio_ts) . ", ag_ts=" . date('Y-m-d H:i', $ag_inicio));
                        break;
                    }
                }
            }

            if (!$no_almoco && !$conflito && !in_array($hora_str, $horarios)) {
                $horarios[] = $hora_str;
                log_message('debug', "Bot: ADICIONADO - horario={$hora_str}");
            } else {
                $motivo = $no_almoco ? 'almoco' : ($conflito ? 'conflito' : 'duplicado');
                log_message('debug', "Bot: IGNORADO - horario={$hora_str}, motivo={$motivo}");
            }

            $hora_atual = strtotime("+{$intervalo} minutes", $hora_atual);
        }

        // Ordenar horários
        sort($horarios);

        log_message('debug', "Bot: horários disponíveis retornados: " . count($horarios) . " - " . implode(', ', $horarios));

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

        $mensagem .= "_Digite *menu* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Inicia fluxo de gerenciamento de agendamentos (visualizar/cancelar)
     * Autor: Rafael Dias - doisr.com.br (30/12/2025)
     */
    private function iniciar_gerenciar_agendamentos($estabelecimento, $numero, $conversa, $cliente) {
        if (!$cliente) {
            $this->waha_lib->enviar_texto($numero,
                "Não encontrei agendamentos para este número. 🔍\n\n" .
                "Se você já é cliente, verifique se o número está cadastrado corretamente.\n\n" .
                "_Digite *menu* para voltar ao menu._"
            );
            return;
        }

        $this->load->model('Agendamento_model');
        $agendamentos = $this->Agendamento_model->get_proximos_by_cliente($cliente->id, 5);

        if (empty($agendamentos)) {
            $this->waha_lib->enviar_texto($numero,
                "Você não tem agendamentos futuros. 📅\n\n" .
                "_Digite *1* para fazer um novo agendamento ou *menu* para o menu principal._"
            );
            return;
        }

        // Atualizar estado para gerenciando agendamento
        $this->Bot_conversa_model->atualizar_estado($conversa->id, 'gerenciando_agendamento', []);

        $mensagem = "📅 *Seus Próximos Agendamentos:*\n\n";

        foreach ($agendamentos as $i => $ag) {
            $num = $i + 1;
            $data = date('d/m/Y', strtotime($ag->data));
            $hora = date('H:i', strtotime($ag->hora_inicio));
            $status_emoji = $ag->status == 'confirmado' ? '✅' : '⏳';

            $mensagem .= "{$num}. {$status_emoji} *{$data}* às *{$hora}*\n";
            $mensagem .= "   💇 {$ag->servico_nome}\n";
            $mensagem .= "   👤 {$ag->profissional_nome}\n\n";
        }

        $mensagem .= "_Digite o número do agendamento para gerenciar._\n";
        $mensagem .= "_Ou digite *menu* para voltar ao menu._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Processa estado: Gerenciando agendamento (seleção do agendamento)
     */
    private function processar_estado_gerenciando($estabelecimento, $numero, $msg, $conversa, $cliente) {
        if (!$cliente) {
            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        $agendamentos = $this->Agendamento_model->get_proximos_by_cliente($cliente->id, 5);

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($agendamentos[$indice])) {
                $ag = $agendamentos[$indice];

                // Salvar agendamento selecionado nos dados
                $dados = [
                    'agendamento_id' => $ag->id,
                    'agendamento_data_original' => $ag->data,
                    'agendamento_hora_original' => $ag->hora_inicio,
                    'servico_id' => $ag->servico_id,
                    'servico_nome' => $ag->servico_nome,
                    'servico_duracao' => $ag->duracao_minutos,
                    'servico_preco' => $ag->preco,
                    'profissional_id' => $ag->profissional_id,
                    'profissional_nome' => $ag->profissional_nome
                ];

                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_acao_agendamento', $dados);

                $data = date('d/m/Y', strtotime($ag->data));
                $hora = date('H:i', strtotime($ag->hora_inicio));

                $mensagem = "📋 *Agendamento Selecionado:*\n\n";
                $mensagem .= "📅 Data: *{$data}*\n";
                $mensagem .= "⏰ Horário: *{$hora}*\n";
                $mensagem .= "💇 Serviço: *{$ag->servico_nome}*\n";
                $mensagem .= "👤 Profissional: *{$ag->profissional_nome}*\n\n";
                $mensagem .= "O que deseja fazer?\n\n";
                $mensagem .= "*1* - 🔄 Reagendar\n";
                $mensagem .= "*2* - ❌ Cancelar\n\n";
                $mensagem .= "_Ou digite *voltar* para ver outros agendamentos._";

                $this->waha_lib->enviar_texto($numero, $mensagem);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do agendamento.\n\n" .
            "_Digite *menu* para voltar ao menu._"
        );
    }

    /**
     * Processa estado: Aguardando ação sobre agendamento (cancelar)
     */
    private function processar_estado_acao_agendamento($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para lista de agendamentos
        if (in_array($msg, ['voltar', 'anterior'])) {
            $this->iniciar_gerenciar_agendamentos($estabelecimento, $numero, $conversa, $cliente);
            return;
        }

        // Se está confirmando cancelamento, processar resposta
        if (isset($dados['confirmando_cancelamento']) && $dados['confirmando_cancelamento']) {
            if (in_array($msg, ['1', 'sim', 's'])) {
                // Confirmar cancelamento
                $this->Agendamento_model->update($dados['agendamento_id'], [
                    'status' => 'cancelado',
                    'cancelado_por' => 'cliente',
                    'motivo_cancelamento' => 'Cancelado via WhatsApp Bot'
                ]);

                $data = date('d/m/Y', strtotime($dados['agendamento_data_original']));
                $hora = date('H:i', strtotime($dados['agendamento_hora_original']));

                $this->waha_lib->enviar_texto($numero,
                    "✅ Agendamento cancelado com sucesso!\n\n" .
                    "📅 *{$data}* às *{$hora}*\n" .
                    "💇 {$dados['servico_nome']}\n\n" .
                    "_Digite *menu* para voltar ao menu ou *0* para sair._"
                );

                $this->Bot_conversa_model->resetar($conversa->id);
                return;
            }

            if (in_array($msg, ['2', 'não', 'nao', 'n'])) {
                // Voltar para lista de agendamentos
                unset($dados['confirmando_cancelamento']);
                $this->iniciar_gerenciar_agendamentos($estabelecimento, $numero, $conversa, $cliente);
                return;
            }

            // Opção inválida na confirmação de cancelamento
            $this->waha_lib->enviar_texto($numero,
                "Por favor, escolha:\n\n" .
                "*1* - ❌ Sim, cancelar\n" .
                "*2* - ↩️ Não, voltar\n\n" .
                "_Ou digite *voltar* para escolher outro agendamento._"
            );
            return;
        }

        // Opção 1: Reagendar
        if (in_array($msg, ['1', 'reagendar'])) {
            // Verificar limite de reagendamentos
            $agendamento = $this->Agendamento_model->get_by_id($dados['agendamento_id']);
            $qtd_atual = isset($agendamento->qtd_reagendamentos) ? (int)$agendamento->qtd_reagendamentos : 0;
            $limite = isset($estabelecimento->limite_reagendamentos) ? (int)$estabelecimento->limite_reagendamentos : 0;

            if ($limite > 0 && $qtd_atual >= $limite) {
                $this->waha_lib->enviar_texto($numero,
                    "⚠️ *Limite de Reagendamentos Atingido*\n\n" .
                    "Este agendamento já foi reagendado *{$qtd_atual}* vez(es).\n" .
                    "Limite permitido: *{$limite}* reagendamento(s).\n\n" .
                    "Para alterar, por favor entre em contato diretamente com o estabelecimento.\n\n" .
                    "_Digite *menu* para voltar ao menu._"
                );
                return;
            }

            // Salvar estado de origem para o botão voltar funcionar corretamente
            $dados['origin_state'] = 'aguardando_acao_agendamento';

            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'reagendando_data', $dados);
            $this->enviar_opcoes_data_reagendamento($estabelecimento, $numero, $dados);
            return;
        }

        // Opção 2: Cancelar
        if (in_array($msg, ['2', 'cancelar'])) {
            // Perguntar se tem certeza
            $data = date('d/m/Y', strtotime($dados['agendamento_data_original']));
            $hora = date('H:i', strtotime($dados['agendamento_hora_original']));

            $mensagem = "⚠️ *Confirmar Cancelamento*\n\n";
            $mensagem .= "Você tem certeza que deseja cancelar o agendamento?\n\n";
            $mensagem .= "📅 *{$data}* às *{$hora}*\n";
            $mensagem .= "💇 {$dados['servico_nome']}\n\n";
            $mensagem .= "*1* - ❌ Sim, cancelar\n";
            $mensagem .= "*2* - ↩️ Não, voltar\n\n";
            $mensagem .= "_Ou digite *voltar* para escolher outro agendamento._";

            $dados['confirmando_cancelamento'] = true;
            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_acao_agendamento', $dados);
            $this->waha_lib->enviar_texto($numero, $mensagem);
            return;
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, escolha:\n\n" .
            "*1* - 🔄 Reagendar\n" .
            "*2* - ❌ Cancelar\n\n" .
            "_Ou digite *voltar* para escolher outro agendamento._"
        );
    }

    // ========================================================================
    // REAGENDAMENTO - REPLICANDO LÓGICA DO AGENDAMENTO NOVO QUE FUNCIONA
    // Autor: Rafael Dias - doisr.com.br (30/12/2025)
    // ========================================================================

    /**
     * Envia opções de data para reagendamento
     * REPLICA EXATAMENTE: enviar_opcoes_data (agendamento novo)
     */
    private function enviar_opcoes_data_reagendamento($estabelecimento, $numero, &$dados) {
        // Garantir que temos a duração do serviço
        if (empty($dados['servico_duracao']) || $dados['servico_duracao'] <= 0) {
            $this->load->model('Servico_model');
            // Tentar pegar do agendamento primeiro (mais confiável se salvo)
            if (!empty($dados['agendamento_id'])) {
                $ag = $this->Agendamento_model->get_by_id($dados['agendamento_id']);
                // Se agendamento tem servico_id, buscar serviço atualizado ou usar snapshot se houver
                if ($ag && $ag->servico_id) {
                    $servico = $this->Servico_model->get_by_id($ag->servico_id);
                    if ($servico) {
                        $dados['servico_duracao'] = $servico->duracao;
                        // Atualizar estado com a duração correta para persistir nas próximas etapas
                        $conversa_atual = $this->Bot_conversa_model->get_ou_criar($estabelecimento->id, $numero);
                        $this->Bot_conversa_model->atualizar_estado($conversa_atual->id, 'reagendando_data', $dados);
                        log_message('info', "Bot: Duração recuperada do banco: {$dados['servico_duracao']} min");
                    }
                }
            }
        }

        $duracao = $dados['servico_duracao'] ?? 30;

        // CORREÇÃO: Passar agendamento_id para excluir o agendamento atual
        $datas = $this->obter_datas_disponiveis(
            $estabelecimento,
            $dados['profissional_id'],
            7,
            $duracao,
            $dados['agendamento_id'] ?? null
        );

        if (empty($datas)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há datas disponíveis nos próximos dias. 😔\n\n" .
                "_Digite *menu* para voltar ao menu._"
            );
            return;
        }

        $data_original = date('d/m/Y', strtotime($dados['agendamento_data_original']));
        $hora_original = date('H:i', strtotime($dados['agendamento_hora_original']));

        $mensagem = "🔄 *Reagendar Agendamento*\n\n";
        $mensagem .= "📅 Agendamento atual: *{$data_original}* às *{$hora_original}*\n";
        $mensagem .= "💇 Serviço: *{$dados['servico_nome']}*\n";
        $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n\n";
        $mensagem .= "Escolha a nova data:\n\n";

        $dias_semana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        foreach ($datas as $i => $data) {
            $num = $i + 1;
            $data_formatada = date('d/m/Y', strtotime($data));
            $dia_semana = $dias_semana[date('w', strtotime($data))];
            $mensagem .= "{$num}. *{$data_formatada}* ({$dia_semana})\n";
        }

        $mensagem .= "\n_Digite o número da nova data._\n";
        $mensagem .= "_Ou digite *voltar* para escolher outra ação._";

        log_message('info', "Bot Reagendamento: Enviando opcoes de data - total_datas=" . count($datas));
        $this->waha_lib->enviar_texto($numero, $mensagem);
        log_message('info', "Bot Reagendamento: Mensagem de datas enviada via WAHA");
    }

    /**
     * Processa estado: Reagendando data
     * REPLICA EXATAMENTE: processar_estado_data (agendamento novo)
     */
    private function processar_estado_reagendando_data($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para ações do agendamento
        if (in_array($msg, ['voltar', 'anterior'])) {
            // Verificar origem para retornar ao fluxo correto
            if (isset($dados['origin_state']) && $dados['origin_state'] == 'confirmando_agendamento') {
                 $this->Bot_conversa_model->atualizar_estado($conversa->id, 'confirmando_agendamento', $dados);
                 // Precisamos reenviar as opções de confirmação (1. Sim, 2. Negar/Reagendar, 3. Cancelar)
                 // Reconstruir contexto de confirmação
                 $this->load->model('Agendamento_model');
                 $agendamento = $this->Agendamento_model->get_by_id($dados['agendamento_id']);

                 // Simular mensagem de confirmação inicial ou simplificada
                 $data_formatada = date('d/m/Y', strtotime($dados['agendamento_data_original']));
                 $hora_formatada = date('H:i', strtotime($dados['agendamento_hora_original']));

                 $mensagem = "🔔 *Confirmação de Agendamento*\n\n";
                 $mensagem .= "Recuperando opções para:\n";
                 $mensagem .= "📅 {$data_formatada} às {$hora_formatada}\n";
                 $mensagem .= "💇 {$dados['servico_nome']}\n\n";
                 $mensagem .= "Escolha:\n";
                 $mensagem .= "*1* - ✅ Confirmar\n";
                 $mensagem .= "*2* - 🔄 Reagendar\n";
                 $mensagem .= "*3* - ❌ Cancelar\n";

                 $this->waha_lib->enviar_texto($numero, $mensagem);
                 return;
            }

            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'aguardando_acao_agendamento', $dados);

            $data = date('d/m/Y', strtotime($dados['agendamento_data_original']));
            $hora = date('H:i', strtotime($dados['agendamento_hora_original']));

            $mensagem = "📋 *Agendamento Selecionado:*\n\n";
            $mensagem .= "📅 Data: *{$data}*\n";
            $mensagem .= "⏰ Horário: *{$hora}*\n";
            $mensagem .= "💇 Serviço: *{$dados['servico_nome']}*\n";
            $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n\n";
            $mensagem .= "O que deseja fazer?\n\n";
            $mensagem .= "*1* - 🔄 Reagendar\n";
            $mensagem .= "*2* - ❌ Cancelar\n\n";
            $mensagem .= "_Ou digite *voltar* para ver outros agendamentos._";

            $this->waha_lib->enviar_texto($numero, $mensagem);
            return;
        }

        $duracao = $dados['servico_duracao'] ?? 30;

        // CORREÇÃO: Passar agendamento_id para excluir o agendamento atual
        $datas_disponiveis = $this->obter_datas_disponiveis(
            $estabelecimento,
            $dados['profissional_id'],
            7,
            $duracao,
            $dados['agendamento_id'] ?? null
        );

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($datas_disponiveis[$indice])) {
                $data_selecionada = $datas_disponiveis[$indice];
                $dados['nova_data'] = $data_selecionada;

                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'reagendando_hora', $dados);
                $this->enviar_opcoes_hora_reagendamento($estabelecimento, $numero, $dados);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* da nova data.\n\n" .
            "_Digite *voltar* para escolher outra ação._"
        );
    }

    /**
     * Envia opções de horário para reagendamento
     * REPLICA EXATAMENTE: enviar_opcoes_hora (agendamento novo)
     */
    private function enviar_opcoes_hora_reagendamento($estabelecimento, $numero, &$dados) {
        // Garantir que temos a duração válida (>0) antes de buscar horários
        // O bug de disponibilidade ocorria porque duracao NULL ou 0 não gerava conflito
        if (empty($dados['servico_duracao']) || $dados['servico_duracao'] <= 0) {
            $this->load->model('Servico_model');
            if (!empty($dados['servico_id'])) {
                 $servico = $this->Servico_model->get_by_id($dados['servico_id']);
                 if ($servico) {
                     $dados['servico_duracao'] = $servico->duracao;
                 }
            }
            // Fallback final segura
            if (empty($dados['servico_duracao'])) $dados['servico_duracao'] = 30;
        }

        // LOG INFO: Rastrear execução do reagendamento
        log_message('info', "Bot REAGENDAMENTO INICIO: nova_data={$dados['nova_data']}, profissional_id={$dados['profissional_id']}, agendamento_id=" . ($dados['agendamento_id'] ?? 'NULL') . ", duracao={$dados['servico_duracao']}");

        // IMPORTANTE: Passar agendamento_id para EXCLUIR o agendamento atual da verificação
        $horarios = $this->obter_horarios_disponiveis(
            $estabelecimento,
            $dados['profissional_id'],
            $dados['nova_data'],
            $dados['servico_duracao'],
            $dados['agendamento_id'] ?? null // Excluir o agendamento que está sendo reagendado
        );

        log_message('info', "Bot REAGENDAMENTO FIM: horarios retornados=" . count($horarios) . " - Lista: " . implode(', ', $horarios));

        if (empty($horarios)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há horários disponíveis nesta data. 😔\n\n" .
                "Por favor, escolha outra data.\n\n" .
                "_Digite *voltar* para escolher outra data._"
            );
            return;
        }

        $data_formatada = date('d/m/Y', strtotime($dados['nova_data']));
        $data_original = date('d/m/Y', strtotime($dados['agendamento_data_original']));
        $hora_original = date('H:i', strtotime($dados['agendamento_hora_original']));

        $mensagem = "⏰ *Escolha o Novo Horário:*\n\n";
        $mensagem .= "📅 Agendamento atual: *{$data_original}* às *{$hora_original}*\n";
        $mensagem .= "🔄 Nova data: *{$data_formatada}*\n\n";
        $mensagem .= "Horários disponíveis:\n\n";

        foreach ($horarios as $i => $hora) {
            $num = $i + 1;
            $mensagem .= "{$num}. *{$hora}*\n";
        }

        $mensagem .= "\n_Digite o número do novo horário._\n";
        $mensagem .= "_Ou digite *voltar* para escolher outra data._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Processa estado: Reagendando hora
     * REPLICA EXATAMENTE: processar_estado_hora (agendamento novo)
     */
    private function processar_estado_reagendando_hora($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para seleção de data
        if (in_array($msg, ['voltar', 'anterior'])) {
            unset($dados['nova_data']);
            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'reagendando_data', $dados);
            $this->enviar_opcoes_data_reagendamento($estabelecimento, $numero, $dados);
            return;
        }

        // IMPORTANTE: Passar agendamento_id para EXCLUIR o agendamento atual da verificação
        $horarios = $this->obter_horarios_disponiveis(
            $estabelecimento,
            $dados['profissional_id'],
            $dados['nova_data'],
            $dados['servico_duracao'],
            $dados['agendamento_id'] // Excluir o agendamento que está sendo reagendado
        );

        if (is_numeric($msg)) {
            $indice = intval($msg) - 1;

            if (isset($horarios[$indice])) {
                $hora_selecionada = $horarios[$indice];
                $dados['nova_hora'] = $hora_selecionada;

                $this->Bot_conversa_model->atualizar_estado($conversa->id, 'confirmando_reagendamento', $dados);
                $this->enviar_confirmacao_reagendamento($estabelecimento, $numero, $dados, $cliente);
                return;
            }
        }

        $this->waha_lib->enviar_texto($numero,
            "Opção inválida. Por favor, digite o *número* do novo horário.\n\n" .
            "_Digite *voltar* para escolher outra data._"
        );
    }

    /**
     * Envia confirmação de reagendamento
     * REPLICA EXATAMENTE: enviar_confirmacao (agendamento novo)
     */
    private function enviar_confirmacao_reagendamento($estabelecimento, $numero, $dados, $cliente) {
        $data_original = date('d/m/Y', strtotime($dados['agendamento_data_original']));
        $hora_original = date('H:i', strtotime($dados['agendamento_hora_original']));
        $nova_data_formatada = date('d/m/Y', strtotime($dados['nova_data']));

        // Buscar informações de reagendamento
        $agendamento = $this->Agendamento_model->get_by_id($dados['agendamento_id']);
        $qtd_atual = isset($agendamento->qtd_reagendamentos) ? (int)$agendamento->qtd_reagendamentos : 0;
        $limite = isset($estabelecimento->limite_reagendamentos) ? (int)$estabelecimento->limite_reagendamentos : 0;

        $mensagem = "✅ *Confirme o Reagendamento:*\n\n";
        $mensagem .= "📋 Serviço: *{$dados['servico_nome']}*\n";
        $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n\n";
        $mensagem .= "❌ *De:* {$data_original} às {$hora_original}\n";
        $mensagem .= "✅ *Para:* {$nova_data_formatada} às {$dados['nova_hora']}\n\n";

        // Adicionar informações de contador
        if ($limite > 0) {
            $qtd_apos = $qtd_atual + 1;
            $restantes = $limite - $qtd_apos;
            $mensagem .= "ℹ️ *Reagendamentos:* {$qtd_atual} vez(es) | Após confirmar: {$qtd_apos}/{$limite}\n";
            if ($restantes > 0) {
                $mensagem .= "   Você ainda poderá reagendar *{$restantes}* vez(es) após este.\n\n";
            } else {
                $mensagem .= "   ⚠️ Este será seu último reagendamento permitido.\n\n";
            }
        } else {
            $mensagem .= "ℹ️ *Reagendamentos:* {$qtd_atual} vez(es) | Após confirmar: " . ($qtd_atual + 1) . "\n\n";
        }

        $mensagem .= "Deseja confirmar o reagendamento?\n\n";
        $mensagem .= "*1* ou *Sim* - Confirmar ✅\n";
        $mensagem .= "*2* ou *Não* - Cancelar ❌\n\n";
        $mensagem .= "_Ou digite *voltar* para escolher outro horário._";

        $this->waha_lib->enviar_texto($numero, $mensagem);
    }

    /**
     * Processa estado: Confirmando reagendamento
     * REPLICA EXATAMENTE: processar_estado_confirmacao (agendamento novo)
     */
    private function processar_estado_confirmando_reagendamento($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $dados = $conversa->dados;

        // Comando voltar - retorna para seleção de horário
        if (in_array($msg, ['voltar', 'anterior'])) {
            unset($dados['nova_hora']);
            $this->Bot_conversa_model->atualizar_estado($conversa->id, 'reagendando_hora', $dados);
            $this->enviar_opcoes_hora_reagendamento($estabelecimento, $numero, $dados);
            return;
        }

        if (in_array($msg, ['sim', 's', '1', 'confirmar', 'confirmo'])) {
            // Realizar reagendamento
            $agendamento_id = $dados['agendamento_id'];

            // Calcular hora_fim baseado na duração
            $hora_inicio = $dados['nova_hora'];
            $duracao = $dados['servico_duracao'];
            $hora_fim = date('H:i:s', strtotime($hora_inicio) + ($duracao * 60));

            // Buscar agendamento atual para incrementar contador
            $agendamento_atual = $this->Agendamento_model->get_by_id($agendamento_id);

            // Calcular novo contador de reagendamentos
            $qtd_atual = 0;
            if (isset($agendamento_atual->qtd_reagendamentos)) {
                $qtd_atual = (int)$agendamento_atual->qtd_reagendamentos;
            }

            // Atualizar agendamento
            $update_data = [
                'data' => $dados['nova_data'],
                'hora_inicio' => $hora_inicio,
                'hora_fim' => $hora_fim,
                'qtd_reagendamentos' => $qtd_atual + 1
            ];

            $this->Agendamento_model->update($agendamento_id, $update_data);

            log_message('info', "Bot: Reagendamento confirmado - ID: {$agendamento_id}, qtd_reagendamentos: " . ($qtd_atual + 1));

            $data_original = date('d/m/Y', strtotime($dados['agendamento_data_original']));
            $hora_original = date('H:i', strtotime($dados['agendamento_hora_original']));
            $nova_data_formatada = date('d/m/Y', strtotime($dados['nova_data']));

            $mensagem = "🎉 *Reagendamento Confirmado!*\n\n";
            $mensagem .= "📋 Serviço: *{$dados['servico_nome']}*\n";
            $mensagem .= "👤 Profissional: *{$dados['profissional_nome']}*\n\n";
            $mensagem .= "❌ *Era:* {$data_original} às {$hora_original}\n";
            $mensagem .= "✅ *Agora:* {$nova_data_formatada} às {$dados['nova_hora']}\n\n";
            $mensagem .= "📍 *{$estabelecimento->nome}*\n";
            if ($estabelecimento->endereco) {
                $mensagem .= "📌 {$estabelecimento->endereco}\n";
            }
            $mensagem .= "\nAté lá! 👋\n\n";
            $mensagem .= "_Digite *menu* para voltar ao menu ou *0* para sair._";

            $this->waha_lib->enviar_texto($numero, $mensagem);

            // Resetar conversa
            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        if (in_array($msg, ['não', 'nao', 'n', '2', 'cancelar'])) {
            $this->Bot_conversa_model->resetar($conversa->id);
            $this->waha_lib->enviar_texto($numero,
                "Reagendamento cancelado. ❌\n\n" .
                "_Digite *menu* para voltar ao menu ou *0* para sair._"
            );
            return;
        }

        $this->waha_lib->enviar_texto($numero,
            "Por favor, responda:\n\n" .
            "*1* ou *Sim* - Para confirmar\n" .
            "*2* ou *Não* - Para cancelar\n\n" .
            "_Digite *voltar* para escolher outro horário._"
        );
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
     * Processar resposta de confirmação de agendamento
     */
     private function processar_estado_confirmando_agendamento($estabelecimento, $numero, $msg, $conversa, $cliente) {
        // Dados já são decodificados pelo model em get_ou_criar
        $dados = $conversa->dados ?? [];
        $agendamento_id = $dados['agendamento_id'] ?? null;

        if (!$agendamento_id) {
            $this->waha_lib->enviar_texto($numero, "Erro ao processar confirmação. Por favor, entre em contato.");
            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        $opcao = strtolower(trim($msg));

        // 1 ou Sim ou Confirmar - Confirmar presença
        if ($opcao == '1' || $opcao == 'sim' || $opcao == 'confirmar') {
            $this->Agendamento_model->update($agendamento_id, [
                'status' => 'confirmado',
                'confirmado_em' => date('Y-m-d H:i:s')
            ]);

            $this->waha_lib->enviar_texto($numero,
                "✅ *Agendamento Confirmado!*\n\n" .
                "Obrigado por confirmar sua presença!\n\n" .
                "Você receberá um lembrete próximo ao horário do seu atendimento.\n\n" .
                "Até breve! 👋\n\n" .
                "_Digite *menu* para voltar ao menu principal._"
            );

            log_message('info', "Bot: Agendamento #{$agendamento_id} confirmado pelo cliente via bot");

            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        // 2 ou Reagendar - Iniciar fluxo de reagendamento
        if ($opcao == '2' || $opcao == 'reagendar') {
            log_message('info', "Bot Confirmacao: Cliente escolheu reagendar - agendamento_id={$agendamento_id}");

            // Buscar agendamento completo
            $agendamento = $this->Agendamento_model->get($agendamento_id);

            if (!$agendamento) {
                log_message('error', "Bot Confirmacao: Agendamento #{$agendamento_id} não encontrado");
                $this->waha_lib->enviar_texto($numero, "Agendamento não encontrado.");
                $this->Bot_conversa_model->resetar($conversa->id);
                return;
            }

            log_message('info', "Bot Confirmacao: Agendamento encontrado - status={$agendamento->status}");

            // Verificar limite de reagendamentos
            $limite_check = $this->Agendamento_model->pode_reagendar($agendamento_id);
            log_message('info', "Bot Confirmacao: Verificacao limite - pode_reagendar=" . ($limite_check['pode_reagendar'] ? 'SIM' : 'NAO') . ", motivo={$limite_check['motivo']}");

            if (!$limite_check['pode_reagendar']) {
                $motivo = $limite_check['motivo'] ?? 'Reagendamento não permitido';

                // Se for limite atingido, mostra mensagem detalhada de limite
                if (strpos($motivo, 'Limite') !== false || strpos($motivo, 'limite') !== false) {
                    $this->waha_lib->enviar_texto($numero,
                        "⚠️ *Limite de Reagendamentos Atingido*\n\n" .
                        "Este agendamento já foi reagendado *{$limite_check['qtd_atual']}* vez(es).\n" .
                        "Limite permitido: *{$limite_check['limite']}* reagendamento(s).\n\n" .
                        "Para alterações, entre em contato diretamente com o estabelecimento.\n\n" .
                        "_Digite *menu* para voltar ao menu principal._"
                    );
                } else {
                    // Outro motivo (ex: estabelecimento não permite)
                    $this->waha_lib->enviar_texto($numero,
                        "⚠️ *Reagendamento Indisponível*\n\n" .
                        "{$motivo}\n\n" .
                        "Para alterações, entre em contato diretamente com o estabelecimento.\n\n" .
                        "_Digite *menu* para voltar ao menu principal._"
                    );
                }

                $this->Bot_conversa_model->resetar($conversa->id);
                return;
            }

            // Iniciar fluxo de reagendamento
            log_message('info', "Bot Confirmacao: Iniciando reagendamento direto");
            $this->iniciar_reagendamento_direto($estabelecimento, $numero, $conversa, $cliente, $agendamento);
            log_message('info', "Bot Confirmacao: Reagendamento direto iniciado com sucesso");
            return;
        }

        // 3 ou Cancelar - Iniciar fluxo de cancelamento
        if ($opcao == '3' || $opcao == 'cancelar' || $opcao == 'nao' || $opcao == 'não') {

            // Salvar dados e mudar estado
            $this->Bot_conversa_model->criar_ou_atualizar(
                $numero,
                $estabelecimento->id,
                'confirmando_cancelamento',
                json_encode(['agendamento_id' => $agendamento_id])
            );

            $this->waha_lib->enviar_texto($numero,
                "⚠️ *Confirmar Cancelamento*\n\n" .
                "Tem certeza que deseja cancelar este agendamento?\n\n" .
                "1️⃣ *Sim, Cancelar* ❌\n" .
                "2️⃣ *Não, Voltar* 🔙"
            );
            return;
        }

        // Filtro de Contexto: Ignorar mensagens curtas de agradecimento/confirmação que não são comandos
        $msg_lower = strtolower(trim($msg));
        $ignorar = ['ok', 'ta', 'tá', 'bom', 'beleza', 'blz', 'obrigado', 'obrigada', 'valeu', 'vlw', 'top', 'show', 'certo'];

        if (in_array($msg_lower, $ignorar)) {
            // Apenas logar e ignorar (não enviar menu nem erro)
            log_message('debug', "Bot: Ignorando mensagem de contexto irrelevante: {$msg}");
            return;
        }

        // Opção inválida
        $this->waha_lib->enviar_texto($numero,
            "❌ *Opção inválida.*\n\n" .
            "Por favor, digite apenas o número:\n" .
            "1️⃣ para *Confirmar*\n" .
            "2️⃣ para *Reagendar*\n" .
            "3️⃣ para *Cancelar*"
        );
    }

    /**
     * Processar estado: Confirmando Cancelamento (Novo UX)
     */
    private function processar_estado_confirmando_cancelamento($estabelecimento, $numero, $msg, $conversa, $cliente) {
        $opcao = strtolower(trim($msg));
        $dados = $conversa->dados ?? [];
        $agendamento_id = $dados['agendamento_id'] ?? null;

        if (!$agendamento_id) {
            $this->waha_lib->enviar_texto($numero, "Erro ao identificar agendamento. Digite *menu* para reiniciar.");
            return;
        }

        // 1 ou Sim - Confirmar Cancelamento
        if ($opcao == '1' || $opcao == 'sim' || $opcao == 's' || $opcao == 'confirmar') {
            $this->Agendamento_model->update($agendamento_id, [
                'status' => 'cancelado',
                'cancelado_por' => 'cliente',
                'motivo_cancelamento' => 'Cancelado via confirmação WhatsApp'
            ]);

            $this->waha_lib->enviar_texto($numero,
                "❌ *Agendamento Cancelado*\n\n" .
                "Seu agendamento foi cancelado com sucesso.\n\n" .
                "Quando precisar, é só entrar em contato novamente! 👋\n\n" .
                "_Digite *menu* para voltar ao menu principal._"
            );

            log_message('info', "Bot: Agendamento #{$agendamento_id} cancelado pelo cliente via confirmação segura");
            $this->Bot_conversa_model->resetar($conversa->id);
            return;
        }

        // 2 ou Não/Voltar - Desistir do Cancelamento
        if ($opcao == '2' || $opcao == 'nao' || $opcao == 'não' || $opcao == 'n' || $opcao == 'voltar') {
            // Voltar para o estado anterior (confirmando_agendamento)
            $this->Bot_conversa_model->atualizar_estado(
                $conversa->id,
                'confirmando_agendamento',
                ['agendamento_id' => $agendamento_id]
            );

            // Reenviar as opções originais para o usuário se localizar
            $this->waha_lib->enviar_texto($numero,
                "👍 *Cancelamento Abortado*\n\n" .
                "Seu agendamento continua ativo!\n\n" .
                "O que deseja fazer?\n\n" .
                "1️⃣ *Confirmar Presença* ✅\n" .
                "2️⃣ *Reagendar* 🔄\n" .
                "3️⃣ *Cancelar* ❌"
            );
            return;
        }

        // Opção Inválida (no fluxo de cancelamento)
        $this->waha_lib->enviar_texto($numero,
            "⚠️ *Opção Inválida*\n\n" .
            "Tem certeza que deseja cancelar?\n\n" .
            "1️⃣ *Sim, Cancelar*\n" .
            "2️⃣ *Não, Voltar*"
        );
    }

    /**
     * Iniciar reagendamento direto (a partir da confirmação)
     */
    private function iniciar_reagendamento_direto($estabelecimento, $numero, $conversa, $cliente, $agendamento) {
        log_message('info', "Bot Reagendamento Direto: Iniciando - agendamento_id={$agendamento->id}");

        // Buscar duração do serviço
        $this->load->model('Servico_model');
        $servico = $this->Servico_model->get_by_id($agendamento->servico_id);
        $duracao_servico = $servico ? $servico->duracao : 30;

        // Salvar dados do agendamento na conversa
        $dados = [
            'agendamento_id' => $agendamento->id,
            'agendamento_data_original' => $agendamento->data,
            'agendamento_hora_original' => $agendamento->hora_inicio,
            'servico_id' => $agendamento->servico_id,
            'servico_nome' => $agendamento->servico_nome,
            'servico_duracao' => $duracao_servico,
            'servico_preco' => $agendamento->servico_preco ?? 0,
            'profissional_id' => $agendamento->profissional_id,
            'profissional_nome' => $agendamento->profissional_nome,
            'origin_state' => 'confirmando_agendamento' // Para botão voltar funcionar corretamente
        ];

        log_message('info', "Bot Reagendamento Direto: Dados preparados - " . json_encode($dados));

        // CORREÇÃO: Não fazer json_encode pois criar_ou_atualizar já faz internamente
        $this->Bot_conversa_model->criar_ou_atualizar(
            $numero,
            $estabelecimento->id,
            'reagendando_data',
            $dados
        );

        log_message('info', "Bot Reagendamento Direto: Estado atualizado para reagendando_data");

        // Enviar opções de data
        $this->enviar_opcoes_data_reagendamento($estabelecimento, $numero, $dados);

        log_message('info', "Bot Reagendamento Direto: Opcoes de data enviadas");
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
