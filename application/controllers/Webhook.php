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
                    $this->db->where('mercadopago_id', $payment_data['id']);
                    $pagamento = $this->db->get('pagamentos')->row();

                    if ($pagamento) {
                        // Atualizar status do pagamento
                        $this->db->where('id', $pagamento->id);
                        $this->db->update('pagamentos', [
                            'status' => $payment_data['status'],
                            'status_detail' => $payment_data['status_detail'] ?? null,
                            'atualizado_em' => date('Y-m-d H:i:s')
                        ]);

                        log_message('info', 'Webhook MP: ✅ Pagamento #' . $pagamento->id . ' atualizado para ' . $payment_data['status']);

                        // Se pagamento aprovado, atualizar agendamento
                        if ($payment_data['status'] == 'approved') {
                            $this->db->where('id', $pagamento->agendamento_id);
                            $this->db->update('agendamentos', [
                                'status' => 'confirmado',
                                'atualizado_em' => date('Y-m-d H:i:s')
                            ]);

                            log_message('info', 'Webhook MP: ✅ Agendamento #' . $pagamento->agendamento_id . ' confirmado');
                        }

                        // Se pagamento cancelado/rejeitado
                        if (in_array($payment_data['status'], ['cancelled', 'rejected'])) {
                            log_message('info', 'Webhook MP: ⚠️ Pagamento rejeitado/cancelado - Agendamento #' . $pagamento->agendamento_id);
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
