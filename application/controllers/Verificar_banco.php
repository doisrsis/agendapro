<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verificar_banco extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        // Buscar diretamente do banco sem cache
        $sql = "SELECT * FROM agendamentos WHERE id = 121";
        $ag = $this->db->query($sql)->row();

        echo "<h1>Verificação Direta do Banco - Agendamento #121</h1>";
        echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . " (sem cache)</p>";
        echo "<hr>";

        if ($ag) {
            echo "<h2>Dados do Banco:</h2>";
            echo "<pre>";
            echo "ID: {$ag->id}\n";
            echo "Status: {$ag->status}\n";
            echo "Data: {$ag->data}\n";
            echo "Hora: {$ag->hora_inicio}\n";
            echo "\n--- CAMPOS DE CONFIRMAÇÃO ---\n";
            echo "confirmacao_tentativas: {$ag->confirmacao_tentativas}\n";
            echo "confirmacao_ultima_tentativa: " . ($ag->confirmacao_ultima_tentativa ?? 'NULL') . "\n";
            echo "confirmacao_enviada: {$ag->confirmacao_enviada}\n";
            echo "confirmacao_enviada_em: " . ($ag->confirmacao_enviada_em ?? 'NULL') . "\n";
            echo "</pre>";

            if ($ag->confirmacao_tentativas > 0) {
                echo "<p style='color: green; font-size: 20px; font-weight: bold;'>✅ SISTEMA FUNCIONANDO! Tentativas sendo contadas.</p>";
            } else {
                echo "<p style='color: red; font-size: 20px; font-weight: bold;'>❌ PROBLEMA: Tentativas não estão sendo incrementadas.</p>";
                echo "<p>Possíveis causas:</p>";
                echo "<ul>";
                echo "<li>O método update() não está funcionando</li>";
                echo "<li>Os campos não foram criados corretamente</li>";
                echo "<li>Há algum erro silencioso no cron</li>";
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>Agendamento não encontrado!</p>";
        }

        echo "<hr>";
        echo "<h2>Estrutura da Tabela:</h2>";
        $colunas = $this->db->query("SHOW COLUMNS FROM agendamentos LIKE 'confirmacao%'")->result();
        echo "<pre>";
        foreach ($colunas as $col) {
            print_r($col);
        }
        echo "</pre>";
    }
}
