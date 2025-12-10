<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Controller: Painel_Controller
 * Descrição: Controller base para painel do estabelecimento
 *
 * Todos os controllers do painel devem estender esta classe
 *
 * @author Rafael Dias - doisr.com.br
 * @date 09/12/2024
 */
class Painel_Controller extends CI_Controller {

    protected $estabelecimento_id;
    protected $estabelecimento;
    protected $usuario;
    protected $assinatura;

    public function __construct() {
        parent::__construct();

        // Carregar libraries e models
        $this->load->library('auth_middleware');
        $this->load->model('Estabelecimento_model');
        $this->load->model('Assinatura_model');

        // Verificar autenticação (apenas estabelecimento)
        $this->auth_middleware->verificar_acesso('estabelecimento');

        // Carregar dados do estabelecimento
        $this->estabelecimento_id = $this->auth_middleware->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $this->usuario = $this->auth_middleware->get_usuario();
        $this->assinatura = $this->Assinatura_model->get_ativa($this->estabelecimento_id);

        // Disponibilizar para views
        $this->load->vars([
            'estabelecimento_id' => $this->estabelecimento_id,
            'estabelecimento' => $this->estabelecimento,
            'usuario_logado' => $this->usuario,
            'assinatura_ativa' => $this->assinatura
        ]);
    }

    /**
     * Verificar se pode criar profissional (limite do plano)
     *
     * @return bool
     */
    protected function pode_criar_profissional() {
        return $this->auth_middleware->pode_criar_profissional($this->estabelecimento_id);
    }

    /**
     * Verificar se pode criar agendamento (limite do plano)
     *
     * @return bool
     */
    protected function pode_criar_agendamento() {
        return $this->auth_middleware->pode_criar_agendamento($this->estabelecimento_id);
    }

    /**
     * Verificar se tem recurso específico
     *
     * @param string $recurso
     * @return bool
     */
    protected function tem_recurso($recurso) {
        return $this->auth_middleware->tem_recurso($recurso);
    }
}
