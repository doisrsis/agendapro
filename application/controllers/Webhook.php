<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Webhook
 *
 * Recebe notificações do Mercado Pago
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Webhook extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Mercadopago_lib');
    }

    /**
     * Webhook do Mercado Pago
     */
    public function mercadopago() {
        try {
            // Obter dados do webhook
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            // Log do webhook
            log_message('info', 'Webhook MP recebido: ' . $input);

            // JSON inválido - retornar 400 para MP tentar novamente
            if (!$data) {
                log_message('error', 'Webhook MP: JSON inválido - retornando 400');
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON']);
                return;
            }

            // Verificar tipo de notificação
            $tipo = $data['type'] ?? 'unknown';
            log_message('info', 'Webhook MP: Tipo de notificação - ' . $tipo);

            // Processar apenas notificações de pagamento
            if ($tipo == 'payment' || $tipo == 'payment.updated') {
                $payment_data = $this->mercadopago_lib->processar_webhook($data);

                if ($payment_data) {
                    // Buscar pagamento no banco
                    $this->load->model('Pagamento_model');
                    $pagamento = $this->Pagamento_model->get_by_mercadopago_id($payment_data['id']);

                    if ($pagamento) {
                        // Atualizar status do pagamento
                        $this->Pagamento_model->atualizar_status_by_mp_id(
                            $payment_data['id'],
                            $payment_data['status'],
                            $payment_data['status_detail'] ?? null,
                            $payment_data
                        );

                        log_message('info', 'Webhook MP: ✅ Pagamento #' . $pagamento->id . ' atualizado para ' . $payment_data['status']);

                        // Se pagamento aprovado
                        if ($payment_data['status'] == 'approved') {
                            // Verificar se é pagamento de assinatura ou agendamento
                            if ($pagamento->assinatura_id) {
                                // Já tem assinatura vinculada - renovar
                                $this->load->model('Assinatura_model');
                                $assinatura = $this->Assinatura_model->get($pagamento->assinatura_id);

                                $data_atual = date('Y-m-d');
                                $data_fim_atual = $assinatura->data_fim;

                                if ($data_fim_atual > $data_atual) {
                                    $nova_data_fim = date('Y-m-d', strtotime($data_fim_atual . ' +30 days'));
                                } else {
                                    $nova_data_fim = date('Y-m-d', strtotime('+30 days'));
                                }

                                $this->Assinatura_model->ativar(
                                    $pagamento->assinatura_id,
                                    $nova_data_fim,
                                    $pagamento->valor
                                );
                                log_message('info', 'Webhook MP: ✅ Assinatura #' . $pagamento->assinatura_id . ' RENOVADA até ' . $nova_data_fim);
                            } else {
                                // Verificar se estabelecimento já tem assinatura
                                $this->load->model('Assinatura_model');
                                $assinaturas_existentes = $this->Assinatura_model->get_by_estabelecimento($pagamento->estabelecimento_id);

                                if (!empty($assinaturas_existentes)) {
                                    // RENOVAR assinatura existente
                                    $assinatura_existente = $assinaturas_existentes[0];

                                    $data_atual = date('Y-m-d');
                                    $data_fim_atual = $assinatura_existente->data_fim;

                                    if ($data_fim_atual > $data_atual) {
                                        $nova_data_fim = date('Y-m-d', strtotime($data_fim_atual . ' +30 days'));
                                    } else {
                                        $nova_data_fim = date('Y-m-d', strtotime('+30 days'));
                                    }

                                    $this->Assinatura_model->ativar(
                                        $assinatura_existente->id,
                                        $nova_data_fim,
                                        $pagamento->valor
                                    );

                                    $this->Pagamento_model->vincular_assinatura($pagamento->id, $assinatura_existente->id);

                                    log_message('info', 'Webhook MP: ✅ Assinatura #' . $assinatura_existente->id . ' RENOVADA até ' . $nova_data_fim);
                                } else {
                                    // CRIAR nova assinatura (primeira vez)
                                    $assinatura_id = $this->Assinatura_model->criar([
                                        'estabelecimento_id' => $pagamento->estabelecimento_id,
                                        'plano_id' => $pagamento->plano_id,
                                        'data_inicio' => date('Y-m-d'),
                                        'data_fim' => date('Y-m-d', strtotime('+30 days')),
                                        'status' => 'ativa',
                                        'valor_pago' => $pagamento->valor,
                                        'auto_renovar' => 1
                                    ]);

                                    $this->Pagamento_model->vincular_assinatura($pagamento->id, $assinatura_id);

                                    log_message('info', 'Webhook MP: ✅ Nova assinatura #' . $assinatura_id . ' CRIADA e ativada');
                                }
                            }
                        }

                        // Se pagamento cancelado/rejeitado
                        if (in_array($payment_data['status'], ['cancelled', 'rejected'])) {
                            log_message('info', 'Webhook MP: ⚠️ Pagamento rejeitado/cancelado - Pagamento #' . $pagamento->id);
                        }

                        // Sucesso - retornar 200
                        http_response_code(200);
                        echo json_encode(['success' => true, 'processed' => true]);
                        return;
                    } else {
                        // Pagamento não encontrado - pode ser teste ou erro
                        log_message('warning', 'Webhook MP: ⚠️ Pagamento não encontrado no banco - ID: ' . $payment_data['id']);
                        // Retornar 200 para não reenviar (pode ser teste do MP)
                        http_response_code(200);
                        echo json_encode(['success' => true, 'processed' => false, 'reason' => 'payment_not_found']);
                        return;
                    }
                } else {
                    // Erro ao processar - pode ser ID inválido
                    log_message('warning', 'Webhook MP: ⚠️ Não foi possível buscar dados do pagamento na API');
                    // Retornar 200 para não reenviar
                    http_response_code(200);
                    echo json_encode(['success' => true, 'processed' => false, 'reason' => 'api_error']);
                    return;
                }
            } else {
                // Outros tipos de notificação (order, merchant_order, etc)
                log_message('info', 'Webhook MP: ℹ️ Tipo de notificação ignorado - ' . $tipo);
                // Retornar 200 - notificação válida mas não processamos esse tipo
                http_response_code(200);
                echo json_encode(['success' => true, 'processed' => false, 'reason' => 'type_not_supported']);
                return;
            }

        } catch (Exception $e) {
            // Exceção - erro grave no código
            log_message('error', 'Webhook MP: ❌ EXCEÇÃO - ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            // Retornar 500 para MP tentar novamente (pode ser problema temporário)
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
            return;
        }
    }
}
