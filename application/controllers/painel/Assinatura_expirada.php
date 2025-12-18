<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Assinatura Expirada
 *
 * Página informativa quando a assinatura está vencida
 *
 * @author Rafael Dias - doisr.com.br
 * @date 10/12/2024
 */
class Assinatura_expirada extends CI_Controller {

    public function index() {
        $data['titulo'] = 'Assinatura Expirada';

        // Carregar planos disponíveis
        $this->load->model('Plano_model');
        $data['planos'] = $this->Plano_model->get_planos_publicos();

        // Carregar informações da assinatura se usuário estiver logado
        if ($this->session->userdata('usuario_id')) {
            $this->load->model('Assinatura_model');
            $this->load->library('auth_check');

            $estabelecimento_id = $this->auth_check->get_estabelecimento_id();
            if ($estabelecimento_id) {
                $assinaturas = $this->Assinatura_model->get_by_estabelecimento($estabelecimento_id);
                // Pegar a assinatura mais recente (primeira do array)
                if (!empty($assinaturas)) {
                    $data['assinatura'] = $assinaturas[0];
                    // Detectar se é trial ou plano pago
                    $data['is_trial'] = ($data['assinatura']->status == 'trial');
                }
            }
        }

        $this->load->view('painel/assinatura_expirada', $data);
    }
}
