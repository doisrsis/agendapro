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
    }

    /**
     * Webhook principal para o SaaS Admin
     * Recebe eventos da sessão do administrador do SaaS
     */
    public function index() {
        $this->processar_webhook(null);
    }

    /**
     * Webhook para estabelecimento específico
     *
     * @param int $estabelecimento_id ID do estabelecimento
     */
    public function estabelecimento($estabelecimento_id = null) {
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
        $data = json_decode($payload, true);

        if (!$data) {
            log_message('error', 'WAHA Webhook: Payload inválido');
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
     */
    private function processar_bot_agendamento($estabelecimento, $numero, $mensagem, $message_id) {
        $this->load->library('waha_lib');
        $this->load->model('Cliente_model');
        $this->load->model('Servico_model');
        $this->load->model('Profissional_model');

        // Configurar WAHA para o estabelecimento
        if (!$this->waha_lib->set_estabelecimento($estabelecimento)) {
            log_message('error', 'Bot: Falha ao configurar WAHA para estabelecimento ' . $estabelecimento->id);
            return;
        }

        // Normalizar mensagem
        $msg = strtolower(trim($mensagem));

        // Verificar se é cliente existente
        $cliente = $this->Cliente_model->get_by_whatsapp($numero, $estabelecimento->id);

        // Comandos básicos do bot
        if (in_array($msg, ['oi', 'olá', 'ola', 'menu', 'inicio', 'início', 'hi', 'hello'])) {
            $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
            return;
        }

        if (in_array($msg, ['1', 'agendar', 'agendamento'])) {
            $this->iniciar_agendamento($estabelecimento, $numero, $cliente);
            return;
        }

        if (in_array($msg, ['2', 'meus agendamentos', 'consultar'])) {
            $this->consultar_agendamentos($estabelecimento, $numero, $cliente);
            return;
        }

        if (in_array($msg, ['3', 'cancelar'])) {
            $this->cancelar_agendamento($estabelecimento, $numero, $cliente);
            return;
        }

        if (in_array($msg, ['0', 'sair', 'tchau', 'obrigado', 'obrigada'])) {
            $this->waha_lib->enviar_texto($numero,
                "Obrigado por entrar em contato! 😊\n\n" .
                "Até a próxima! 👋\n\n" .
                "_Digite *oi* para voltar ao menu._"
            );
            return;
        }

        // Mensagem padrão se não reconhecer comando
        $this->waha_lib->enviar_texto($numero,
            "Desculpe, não entendi. 🤔\n\n" .
            "Digite *oi* para ver o menu de opções."
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
    private function iniciar_agendamento($estabelecimento, $numero, $cliente) {
        // Buscar serviços ativos
        $servicos = $this->Servico_model->get_by_estabelecimento($estabelecimento->id);

        if (empty($servicos)) {
            $this->waha_lib->enviar_texto($numero,
                "Desculpe, não há serviços disponíveis no momento. 😔\n\n" .
                "Por favor, entre em contato diretamente com o estabelecimento."
            );
            return;
        }

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

        // TODO: Implementar máquina de estados para continuar o fluxo
        // Por enquanto, apenas mostra os serviços
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
     * Inicia fluxo de cancelamento
     */
    private function cancelar_agendamento($estabelecimento, $numero, $cliente) {
        $this->waha_lib->enviar_texto($numero,
            "Para cancelar um agendamento, por favor entre em contato diretamente com o estabelecimento. 📞\n\n" .
            "📱 WhatsApp: {$estabelecimento->whatsapp}\n\n" .
            "_Digite *0* para voltar ao menu._"
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
