<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Links extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Estabelecimento_model');
        $this->load->helper('url');
    }

    public function index($slug = null) {
        if (!$slug) {
            show_404();
        }

        // Tenta buscar por slug
        $estabelecimento = $this->Estabelecimento_model->get_by_slug($slug);

        // Se não achou, tenta pelo ID (caso o slug seja o ID no futuro ou fallback)
        if (!$estabelecimento) {
            $estabelecimento = $this->Estabelecimento_model->get($slug);
        }

        if (!$estabelecimento) {
            show_404();
        }

        $data['estabelecimento'] = $estabelecimento;
        $this->load->view('links/index', $data);
    }
}
