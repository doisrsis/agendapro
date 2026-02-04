<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library de Integração com WAHA - WhatsApp HTTP API
 *
 * Gerencia conexão, envio de mensagens e webhooks da API WAHA
 * Documentação: https://waha.devlike.pro/docs/
 *
 * @author Rafael Dias - doisr.com.br
 * @date 28/12/2025
 */
class Waha_lib {

    private $CI;
    private $api_url;
    private $api_key;
    private $session_name;
    private $timeout = 60;

    /**
     * Construtor - Carrega configurações padrão do SaaS
     */
    public function __construct($config = []) {
        $this->CI =& get_instance();

        if (!empty($config)) {
            $this->api_url = rtrim($config['api_url'] ?? '', '/');
            $this->api_key = $config['api_key'] ?? '';
            $this->session_name = $config['session_name'] ?? 'default';
        }
    }

    /**
     * Configura credenciais para um estabelecimento específico
     * Usa credenciais do SaaS Admin com sessão do estabelecimento
     *
     * @param object $estabelecimento Objeto do estabelecimento
     * @return bool
     */
    public function set_estabelecimento($estabelecimento) {
        // Buscar credenciais do SaaS Admin
        $this->CI->load->model('Configuracao_model');
        $configs = $this->CI->Configuracao_model->get_by_grupo('waha');

        if (empty($configs)) {
            log_message('error', 'WAHA: Configurações do SaaS Admin não encontradas');
            return false;
        }

        $config_array = [];
        foreach ($configs as $config) {
            $config_array[$config->chave] = $config->valor;
        }

        if (empty($config_array['waha_api_url']) || empty($config_array['waha_api_key'])) {
            log_message('error', 'WAHA: URL ou API Key do SaaS não configuradas');
            return false;
        }

        // Usar credenciais do SaaS com sessão do estabelecimento
        $this->api_url = rtrim($config_array['waha_api_url'], '/');
        $this->api_key = $config_array['waha_api_key'];
        $this->session_name = $estabelecimento->waha_session_name ?? 'est_' . $estabelecimento->id;

        log_message('debug', 'WAHA: Configurado para estabelecimento ' . $estabelecimento->id . ' - Sessão: ' . $this->session_name);

        return true;
    }

    /**
     * Configura credenciais do SaaS Admin (tabela configuracoes)
     *
     * @return bool
     */
    public function set_saas_admin() {
        $this->CI->load->model('Configuracao_model');

        $configs = $this->CI->Configuracao_model->get_by_grupo('waha');

        if (empty($configs)) {
            return false;
        }

        $config_array = [];
        foreach ($configs as $config) {
            $config_array[$config->chave] = $config->valor;
        }

        if (empty($config_array['waha_api_url']) || empty($config_array['waha_api_key'])) {
            return false;
        }

        $this->api_url = rtrim($config_array['waha_api_url'], '/');
        $this->api_key = $config_array['waha_api_key'];
        $this->session_name = $config_array['waha_session_name'] ?? 'saas_admin';

        return true;
    }

    /**
     * Define credenciais manualmente
     *
     * @param string $api_url URL da API
     * @param string $api_key Chave da API
     * @param string $session_name Nome da sessão
     */
    public function set_credentials($api_url, $api_key, $session_name = 'default') {
        $this->api_url = $api_url ? rtrim($api_url, '/') : '';
        $this->api_key = $api_key;
        $this->session_name = $session_name;
    }

    // =========================================================================
    // GERENCIAMENTO DE SESSÕES
    // =========================================================================

    /**
     * Criar nova sessão
     *
     * @param array $config Configurações da sessão
     * @return array
     */
    public function criar_sessao($config = []) {
        $data = [
            'name' => $this->session_name,
            'start' => true,
            'config' => [
                'webhooks' => []
            ]
        ];

        // Adicionar webhook se configurado
        if (!empty($config['webhook_url'])) {
            $data['config']['webhooks'][] = [
                'url' => $config['webhook_url'],
                'events' => [
                    'message',
                    'message.any',
                    'message.reaction',
                    'message.ack',
                    'message.waiting',
                    'message.revoked',
                    'session.status'
                ],
                'retries' => [
                    'delaySeconds' => 2,
                    'attempts' => 15,
                    'policy' => 'constant'
                ]
            ];
        }

        // Adicionar metadata se fornecido
        if (!empty($config['metadata'])) {
            $data['config']['metadata'] = $config['metadata'];
        }

        log_message('debug', 'WAHA criar_sessao - Data: ' . json_encode($data));

        return $this->request('POST', '/api/sessions/', $data);
    }

    /**
     * Listar todas as sessões
     *
     * @param bool $all Incluir sessões paradas
     * @return array
     */
    public function listar_sessoes($all = false) {
        $endpoint = '/api/sessions/';
        if ($all) {
            $endpoint .= '?all=true';
        }
        return $this->request('GET', $endpoint);
    }

    /**
     * Obter informações de uma sessão específica
     *
     * @param string $session_name Nome da sessão (opcional, usa o configurado)
     * @return array
     */
    public function get_sessao($session_name = null) {
        $session = $session_name ?? $this->session_name;
        return $this->request('GET', "/api/sessions/{$session}");
    }

    /**
     * Iniciar sessão
     *
     * @return array
     */
    public function iniciar_sessao() {
        return $this->request('POST', "/api/sessions/{$this->session_name}/start");
    }

    /**
     * Parar sessão
     *
     * @return array
     */
    public function parar_sessao() {
        return $this->request('POST', "/api/sessions/{$this->session_name}/stop");
    }

    /**
     * Reiniciar sessão
     *
     * @return array
     */
    public function reiniciar_sessao() {
        return $this->request('POST', "/api/sessions/{$this->session_name}/restart");
    }

    /**
     * Fazer logout da sessão
     *
     * @return array
     */
    public function logout_sessao() {
        return $this->request('POST', "/api/sessions/{$this->session_name}/logout");
    }

    /**
     * Deletar sessão
     *
     * @return array
     */
    public function deletar_sessao() {
        return $this->request('DELETE', "/api/sessions/{$this->session_name}");
    }

    /**
     * Atualizar configuração de webhook da sessão
     *
     * @param string $webhook_url URL do webhook
     * @return array
     */
    public function atualizar_webhook($webhook_url) {
        $data = [
            'config' => [
                'webhooks' => [
                    [
                        'url' => $webhook_url,
                        'events' => [
                            'message',
                            'message.any',
                            'message.reaction',
                            'message.ack',
                            'message.waiting',
                            'message.revoked',
                            'session.status'
                        ],
                        'retries' => [
                            'delaySeconds' => 2,
                            'attempts' => 15,
                            'policy' => 'constant'
                        ]
                    ]
                ]
            ]
        ];

        log_message('debug', 'WAHA atualizar_webhook - Data: ' . json_encode($data));

        return $this->request('PUT', "/api/sessions/{$this->session_name}", $data);
    }

    /**
     * Obter QR Code para autenticação
     *
     * @param string $format Formato: 'image' ou 'raw'
     * @return array
     */
    public function get_qr_code($format = 'image') {
        $endpoint = "/api/{$this->session_name}/auth/qr";
        if ($format === 'raw') {
            $endpoint .= '?format=raw';
        }

        return $this->request('GET', $endpoint, null, $format === 'image' ? 'application/json' : null);
    }

    /**
     * Obter informações do número conectado
     *
     * @return array
     */
    public function get_me() {
        return $this->request('GET', "/api/sessions/{$this->session_name}/me");
    }

    /**
     * Obter screenshot da sessão
     *
     * @return array
     */
    public function get_screenshot() {
        return $this->request('GET', "/api/screenshot?session={$this->session_name}");
    }

    // =========================================================================
    // ENVIO DE MENSAGENS
    // =========================================================================

    /**
     * Enviar mensagem de texto
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $mensagem Texto da mensagem
     * @param string $reply_to ID da mensagem para responder (opcional)
     * @return array
     */
    public function enviar_texto($numero, $mensagem, $reply_to = null) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'text' => $mensagem
        ];

        if ($reply_to) {
            $data['reply_to'] = $reply_to;
        }

        return $this->request('POST', '/api/sendText', $data);
    }

    /**
     * Enviar imagem
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $imagem URL ou base64 da imagem
     * @param string $caption Legenda (opcional)
     * @return array
     */
    public function enviar_imagem($numero, $imagem, $caption = '') {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'caption' => $caption
        ];

        // Verificar se é URL ou base64
        if (filter_var($imagem, FILTER_VALIDATE_URL)) {
            $data['file'] = ['url' => $imagem];
        } else {
            $data['file'] = ['data' => $imagem];
        }

        return $this->request('POST', '/api/sendImage', $data);
    }

    /**
     * Enviar arquivo/documento
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $arquivo URL ou base64 do arquivo
     * @param string $filename Nome do arquivo
     * @param string $caption Legenda (opcional)
     * @return array
     */
    public function enviar_arquivo($numero, $arquivo, $filename, $caption = '') {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'caption' => $caption,
            'filename' => $filename
        ];

        if (filter_var($arquivo, FILTER_VALIDATE_URL)) {
            $data['file'] = ['url' => $arquivo];
        } else {
            $data['file'] = ['data' => $arquivo];
        }

        return $this->request('POST', '/api/sendFile', $data);
    }

    /**
     * Enviar áudio/voz
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $audio URL ou base64 do áudio
     * @return array
     */
    public function enviar_audio($numero, $audio) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero)
        ];

        if (filter_var($audio, FILTER_VALIDATE_URL)) {
            $data['file'] = ['url' => $audio];
        } else {
            $data['file'] = ['data' => $audio];
        }

        return $this->request('POST', '/api/sendVoice', $data);
    }

    /**
     * Enviar localização
     *
     * @param string $numero Número no formato 5511999999999
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param string $title Título (opcional)
     * @param string $address Endereço (opcional)
     * @return array
     */
    public function enviar_localizacao($numero, $latitude, $longitude, $title = '', $address = '') {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'title' => $title,
            'address' => $address
        ];

        return $this->request('POST', '/api/sendLocation', $data);
    }

    /**
     * Enviar contato (vCard)
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $nome Nome do contato
     * @param string $telefone Telefone do contato
     * @return array
     */
    public function enviar_contato($numero, $nome, $telefone) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'contacts' => [
                [
                    'fullName' => $nome,
                    'phoneNumber' => $telefone
                ]
            ]
        ];

        return $this->request('POST', '/api/sendContactVcard', $data);
    }

    /**
     * Marcar mensagem como lida
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $message_id ID da mensagem
     * @return array
     */
    public function marcar_lida($numero, $message_id) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'messageId' => $message_id
        ];

        return $this->request('POST', '/api/sendSeen', $data);
    }

    /**
     * Reagir a uma mensagem
     *
     * @param string $numero Número no formato 5511999999999
     * @param string $message_id ID da mensagem
     * @param string $emoji Emoji da reação
     * @return array
     */
    public function reagir_mensagem($numero, $message_id, $emoji) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero),
            'messageId' => $message_id,
            'reaction' => $emoji
        ];

        return $this->request('POST', '/api/reaction', $data);
    }

    // =========================================================================
    // VERIFICAÇÕES E UTILITÁRIOS
    // =========================================================================

    /**
     * Obter mensagens de um chat
     *
     * @param string $numero Número no formato 5511999999999
     * @param int $limit Limite de mensagens
     * @return array
     */
    public function get_mensagens($numero, $limit = 100) {
        $chat_id = $this->formatar_chat_id($numero);
        return $this->request('GET', "/api/messages?session={$this->session_name}&chatId={$chat_id}&limit={$limit}");
    }

    /**
     * Iniciar digitação
     *
     * @param string $numero Número no formato 5511999999999
     * @return array
     */
    public function iniciar_digitacao($numero) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero)
        ];

        return $this->request('POST', '/api/startTyping', $data);
    }

    /**
     * Parar digitação
     *
     * @param string $numero Número no formato 5511999999999
     * @return array
     */
    public function parar_digitacao($numero) {
        $data = [
            'session' => $this->session_name,
            'chatId' => $this->formatar_chat_id($numero)
        ];

        return $this->request('POST', '/api/stopTyping', $data);
    }

    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================

    /**
     * Formatar número para chatId do WhatsApp
     *
     * @param string $numero Número (pode ter ou não formatação)
     * @return string ChatId no formato 5511999999999@c.us ou 108259113467972@lid
     */
    public function formatar_chat_id($numero) {
        // Se já tem @c.us ou @lid, retornar como está
        if (strpos($numero, '@c.us') !== false || strpos($numero, '@lid') !== false) {
            return $numero;
        }

        // Se já tem @s.whatsapp.net, converter para @c.us
        if (strpos($numero, '@s.whatsapp.net') !== false) {
            return str_replace('@s.whatsapp.net', '@c.us', $numero);
        }

        // Remover tudo que não for número
        $numero = preg_replace('/[^0-9]/', '', $numero);

        // Adicionar código do país se não tiver (números BR tem 10-11 dígitos)
        if (strlen($numero) <= 11) {
            $numero = '55' . $numero;
        }

        // Log para debug
        log_message('debug', 'WAHA formatar_chat_id: ' . $numero . '@c.us');

        return $numero . '@c.us';
    }

    /**
     * Verificar se número existe no WhatsApp
     * Usa endpoint GET /api/contacts/check-exists da WAHA
     *
     * @param string $numero Número para verificar
     * @return array ['exists' => bool, 'chatId' => string]
     */
    public function verificar_numero($numero) {
        $chat_id = $this->formatar_chat_id($numero);

        // Endpoint GET com query params
        $endpoint = "/api/contacts/check-exists?session={$this->session_name}&phone={$chat_id}";
        $result = $this->request('GET', $endpoint);

        if ($result['success'] && isset($result['response']['numberExists'])) {
            return [
                'exists' => $result['response']['numberExists'],
                'chatId' => $result['response']['chatId'] ?? $chat_id
            ];
        }

        return ['exists' => false, 'chatId' => $chat_id];
    }

    /**
     * Obter chatId válido para número brasileiro
     * Tenta com e sem o nono dígito (9) para números de celular
     *
     * @param string $numero Número no formato (XX) 9XXXX-XXXX ou similar
     * @return string|null ChatId válido ou null se não encontrar
     */
    public function obter_chat_id_valido($numero) {
        // Limpar número
        $numero_limpo = preg_replace('/[^0-9]/', '', $numero);

        // Adicionar código do país se necessário
        if (strlen($numero_limpo) <= 11) {
            $numero_limpo = '55' . $numero_limpo;
        }

        // Tentar número original primeiro
        $verificacao = $this->verificar_numero($numero_limpo);
        if ($verificacao['exists']) {
            log_message('debug', 'WAHA: Número encontrado (original): ' . $numero_limpo);
            return $verificacao['chatId'];
        }

        // Se número tem 13 dígitos (55 + DDD + 9 + 8 dígitos), tentar sem o nono dígito
        if (strlen($numero_limpo) == 13) {
            // Formato: 55 XX 9XXXXXXXX -> 55 XX XXXXXXXX
            $ddd = substr($numero_limpo, 2, 2);
            $resto = substr($numero_limpo, 5); // Pega os 8 últimos dígitos
            $numero_sem_9 = '55' . $ddd . $resto;

            $verificacao = $this->verificar_numero($numero_sem_9);
            if ($verificacao['exists']) {
                log_message('debug', 'WAHA: Número encontrado (sem nono dígito): ' . $numero_sem_9);
                return $verificacao['chatId'];
            }
        }

        // Se número tem 12 dígitos (55 + DDD + 8 dígitos), tentar com o nono dígito
        if (strlen($numero_limpo) == 12) {
            // Formato: 55 XX XXXXXXXX -> 55 XX 9XXXXXXXX
            $ddd = substr($numero_limpo, 2, 2);
            $resto = substr($numero_limpo, 4); // Pega os 8 últimos dígitos
            $numero_com_9 = '55' . $ddd . '9' . $resto;

            $verificacao = $this->verificar_numero($numero_com_9);
            if ($verificacao['exists']) {
                log_message('debug', 'WAHA: Número encontrado (com nono dígito): ' . $numero_com_9);
                return $verificacao['chatId'];
            }
        }

        // Não encontrou em nenhuma variação
        log_message('warning', 'WAHA: Número não encontrado no WhatsApp: ' . $numero_limpo);
        return null;
    }

    /**
     * Extrair número do chatId
     *
     * @param string $chat_id ChatId no formato 5511999999999@c.us
     * @return string Número limpo
     */
    public function extrair_numero($chat_id) {
        return preg_replace('/[^0-9]/', '', str_replace('@c.us', '', $chat_id));
    }

    /**
     * Fazer requisição HTTP para a API WAHA
     *
     * @param string $method Método HTTP (GET, POST, PUT, DELETE)
     * @param string $endpoint Endpoint da API
     * @param array $data Dados para enviar
     * @param string $accept Header Accept
     * @return array
     */
    private function request($method, $endpoint, $data = null, $accept = 'application/json') {
        if (empty($this->api_url)) {
            return [
                'success' => false,
                'error' => 'API URL não configurada'
            ];
        }

        $url = $this->api_url . $endpoint;

        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: ' . ($accept ?? 'application/json')
        ];

        // Adicionar API Key se configurada (WAHA aceita X-Api-Key ou Authorization)
        if (!empty($this->api_key)) {
            // Formato padrão WAHA
            $headers[] = 'X-Api-Key: ' . $this->api_key;
            // Alternativa: Authorization Bearer (algumas configurações)
            // $headers[] = 'Authorization: Bearer ' . $this->api_key;
        }

        // Log para debug
        log_message('debug', 'WAHA Request: ' . $method . ' ' . $url);
        log_message('debug', 'WAHA API Key (primeiros 20 chars): ' . substr($this->api_key, 0, 20) . '...');

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            log_message('error', "WAHA API Error: {$error}");
            return [
                'success' => false,
                'error' => $error,
                'http_code' => 0
            ];
        }

        $decoded = json_decode($response, true);

        // Log para debug
        log_message('debug', "WAHA API [{$method}] {$endpoint} - HTTP {$http_code}");

        // Log detalhado em caso de erro
        if ($http_code >= 400) {
            log_message('error', "WAHA API Error Response: " . $response);
            log_message('error', "WAHA API Request Data: " . json_encode($data));
        }

        return [
            'success' => $http_code >= 200 && $http_code < 300,
            'http_code' => $http_code,
            'response' => $decoded ?? $response,
            'error' => $http_code >= 400 ? ($decoded['message'] ?? $response) : null
        ];
    }

    /**
     * Testar conexão com a API
     *
     * @return array
     */
    public function testar_conexao() {
        $result = $this->listar_sessoes();

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Conexão com WAHA estabelecida com sucesso',
                'sessoes' => $result['response']
            ];
        }

        return [
            'success' => false,
            'message' => 'Falha ao conectar com WAHA: ' . ($result['error'] ?? 'Erro desconhecido'),
            'http_code' => $result['http_code'] ?? 0
        ];
    }

    /**
     * Obter status da sessão atual
     *
     * @return string Status: stopped, starting, scan_qr, working, failed
     */
    public function get_status() {
        $result = $this->get_sessao();

        // Log para debug
        log_message('debug', 'WAHA get_status response: ' . json_encode($result));

        if ($result['success']) {
            // Verificar diferentes estruturas de resposta
            if (isset($result['response']['status'])) {
                return strtolower($result['response']['status']);
            }
            // Algumas versões retornam direto no response
            if (isset($result['response']['engine']['state'])) {
                return strtolower($result['response']['engine']['state']);
            }
            // Se a sessão existe e tem 'me', está conectada
            if (isset($result['response']['me'])) {
                return 'working';
            }
        }

        // Se houve erro na requisição, logar
        if (isset($result['error'])) {
            log_message('error', 'WAHA get_status error: ' . $result['error']);
        }

        return 'unknown';
    }

    /**
     * Verificar se sessão está conectada e funcionando
     *
     * @return bool
     */
    public function esta_conectado() {
        $status = $this->get_status();
        return in_array($status, ['working', 'connected']);
    }

    /**
     * Debug: Retorna resposta bruta da sessão
     *
     * @return array
     */
    public function debug_sessao() {
        return $this->get_sessao();
    }
}
