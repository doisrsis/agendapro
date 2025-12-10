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

        // Carregar informações da assinatura se usuário estiver logado
        if ($this->session->userdata('usuario_id')) {
            $this->load->model('Assinatura_model');
            $this->load->library('auth_check');

            $estabelecimento_id = $this->auth_check->get_estabelecimento_id();
            if ($estabelecimento_id) {
                $data['assinatura'] = $this->Assinatura_model->get_by_estabelecimento($estabelecimento_id);
            }
        }

        $this->load->view('painel/assinatura_expirada', $data);
    }
}
