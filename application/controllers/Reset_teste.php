<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_teste extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Agendamento_model');
    }

    public function index() {
        // Resetar agendamento 121 para testar sistema de tentativas
        $this->db->where('id', 121);
        $this->db->update('agendamentos', [
            'status' => 'pendente',
            'confirmacao_tentativas' => 0,
            'confirmacao_ultima_tentativa' => NULL,
            'confirmacao_enviada' => 0,
            'confirmacao_enviada_em' => NULL
        ]);

        echo "<h1>✅ Agendamento #121 Resetado</h1>";
        echo "<p>O agendamento foi resetado para status inicial.</p>";
        echo "<p>Agora você pode testar o fluxo completo de tentativas.</p>";
        echo "<hr>";
        echo "<p><a href='/test_tentativas'>Ver Status das Tentativas</a></p>";
        echo "<p><a href='/cron/enviar_confirmacoes?token=b781f3e57f4e4c4ba3a67df819050e6e'>Executar Cron Manualmente</a></p>";
    }
}
