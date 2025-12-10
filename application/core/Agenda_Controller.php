<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Controller: Agenda_Controller
 * Descrição: Controller base para agenda do profissional
 *
 * Todos os controllers da agenda devem estender esta classe
 *
 * @author Rafael Dias - doisr.com.br
 * @date 09/12/2024
 */
class Agenda_Controller extends CI_Controller {

    protected $profissional_id;
    protected $profissional;
    protected $estabelecimento_id;
    protected $estabelecimento;
    protected $usuario;

    public function __construct() {
        parent::__construct();

        // Carregar libraries e models
        $this->load->library('auth_middleware');
        $this->load->model('Profissional_model');
        $this->load->model('Estabelecimento_model');

        // Verificar autenticação (apenas profissional)
        $this->auth_middleware->verificar_acesso('profissional');

        // Carregar dados do profissional
        $this->usuario = $this->auth_middleware->get_usuario();
        $this->profissional_id = $this->usuario->profissional_id;
        $this->profissional = $this->Profissional_model->get($this->profissional_id);

        // Carregar estabelecimento
        $this->estabelecimento_id = $this->auth_middleware->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);

        // Disponibilizar para views
        $this->load->vars([
            'profissional_id' => $this->profissional_id,
            'profissional' => $this->profissional,
            'estabelecimento_id' => $this->estabelecimento_id,
            'estabelecimento' => $this->estabelecimento,
            'usuario_logado' => $this->usuario
        ]);
    }
}
