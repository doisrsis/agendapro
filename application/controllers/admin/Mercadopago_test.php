<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Testes do Mercado Pago
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Mercadopago_test extends Admin_Controller {

    protected $modulo_atual = 'configuracoes';

    public function __construct() {
        parent::__construct();
        $this->load->library('Mercadopago_lib');
        $this->load->model('Configuracao_model');
    }

    /**
     * Página de testes
     */
    public function index() {
        $data['titulo'] = 'Teste - Mercado Pago';
        $data['menu_ativo'] = 'configuracoes';

        // Verificar se credenciais estão configuradas
        $data['configurado'] = $this->verificar_configuracao();

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/mercadopago_test/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Testar conexão com API
     */
    public function testar_conexao() {
        $resultado = [
            'sucesso' => false,
            'mensagem' => '',
            'detalhes' => []
        ];

        try {
            // Verificar credenciais
            $sandbox = $this->Configuracao_model->get('mercadopago_sandbox') == '1';

            if ($sandbox) {
                $access_token = $this->Configuracao_model->get('mercadopago_access_token_test');
                $public_key = $this->Configuracao_model->get('mercadopago_public_key_test');
            } else {
                $access_token = $this->Configuracao_model->get('mercadopago_access_token_prod');
                $public_key = $this->Configuracao_model->get('mercadopago_public_key_prod');
            }

            $resultado['detalhes']['modo'] = $sandbox ? 'Teste (Sandbox)' : 'Produção';
            $resultado['detalhes']['access_token'] = $access_token ? substr($access_token, 0, 20) . '...' : 'Não configurado';
            $resultado['detalhes']['public_key'] = $public_key ? substr($public_key, 0, 20) . '...' : 'Não configurado';

            if (!$access_token || !$public_key) {
                $resultado['mensagem'] = 'Credenciais não configuradas. Configure em Admin > Configurações > Mercado Pago';
                echo json_encode($resultado);
                return;
            }

            // Testar API - buscar métodos de pagamento
            $metodos = $this->mercadopago_lib->get_metodos_pagamento();

            if ($metodos && isset($metodos['status']) && $metodos['status'] == 200) {
                $resultado['sucesso'] = true;
                $resultado['mensagem'] = 'Conexão com Mercado Pago estabelecida com sucesso!';
                $resultado['detalhes']['metodos_disponiveis'] = count($metodos['response']);
            } else {
                $resultado['mensagem'] = 'Erro ao conectar com Mercado Pago';
                $resultado['detalhes']['erro'] = $metodos['error'] ?? 'Erro desconhecido';
            }

        } catch (Exception $e) {
            $resultado['mensagem'] = 'Exceção: ' . $e->getMessage();
        }

        echo json_encode($resultado);
    }

    /**
     * Simular webhook
     */
    public function simular_webhook() {
        $resultado = [
            'sucesso' => false,
            'mensagem' => '',
            'detalhes' => []
        ];

        try {
            // Dados de exemplo do webhook (formato real do MP)
            $webhook_data = [
                'action' => 'payment.updated',
                'api_version' => 'v1',
                'data' => [
                    'id' => '123456789' // ID fictício para teste
                ],
                'date_created' => date('c'),
                'id' => uniqid(),
                'live_mode' => false,
                'type' => 'payment',
                'user_id' => 123456
            ];

            $resultado['detalhes']['webhook_enviado'] = $webhook_data;

            // Simular processamento
            $payment_data = $this->mercadopago_lib->processar_webhook($webhook_data);

            if ($payment_data) {
                $resultado['sucesso'] = true;
                $resultado['mensagem'] = 'Webhook processado com sucesso!';
                $resultado['detalhes']['payment_data'] = $payment_data;
            } else {
                $resultado['mensagem'] = 'Webhook recebido mas pagamento não encontrado (esperado para ID fictício)';
                $resultado['detalhes']['nota'] = 'Para testar com pagamento real, crie um pagamento primeiro e use o ID real';
            }

        } catch (Exception $e) {
            $resultado['mensagem'] = 'Exceção: ' . $e->getMessage();
        }

        echo json_encode($resultado);
    }

    /**
     * Verificar configuração
     */
    private function verificar_configuracao() {
        $sandbox = $this->Configuracao_model->get('mercadopago_sandbox') == '1';

        if ($sandbox) {
            $access_token = $this->Configuracao_model->get('mercadopago_access_token_test');
            $public_key = $this->Configuracao_model->get('mercadopago_public_key_test');
        } else {
            $access_token = $this->Configuracao_model->get('mercadopago_access_token_prod');
            $public_key = $this->Configuracao_model->get('mercadopago_public_key_prod');
        }

        return !empty($access_token) && !empty($public_key);
    }

    /**
     * Buscar logs recentes do Mercado Pago
     */
    public function logs() {
        $resultado = [
            'sucesso' => false,
            'logs' => [],
            'mensagem' => ''
        ];

        try {
            // Buscar arquivo de log de hoje
            $log_file = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';

            if (!file_exists($log_file)) {
                $resultado['mensagem'] = 'Arquivo de log de hoje não encontrado';
                echo json_encode($resultado);
                return;
            }

            // Ler arquivo
            $conteudo = file_get_contents($log_file);
            $linhas = explode("\n", $conteudo);

            // Filtrar apenas linhas do Mercado Pago
            $logs_mp = [];
            foreach ($linhas as $linha) {
                if (strpos($linha, 'Webhook MP') !== false || strpos($linha, 'Mercadopago_lib') !== false) {
                    // Extrair informações da linha
                    if (preg_match('/(\w+)\s+-\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+-->\s+(.+)/', $linha, $matches)) {
                        $logs_mp[] = [
                            'nivel' => $matches[1],
                            'data' => $matches[2],
                            'mensagem' => $matches[3]
                        ];
                    }
                }
            }

            // Pegar últimos 50 logs
            $logs_mp = array_slice(array_reverse($logs_mp), 0, 50);

            $resultado['sucesso'] = true;
            $resultado['logs'] = $logs_mp;
            $resultado['total'] = count($logs_mp);

        } catch (Exception $e) {
            $resultado['mensagem'] = 'Erro: ' . $e->getMessage();
        }

        echo json_encode($resultado);
    }
}
