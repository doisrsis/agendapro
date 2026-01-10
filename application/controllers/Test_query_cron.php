<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_query_cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Agendamento_model');
    }

    public function index() {
        echo "<h1>Teste da Query do Cron</h1>";
        echo "<p><strong>Hora atual:</strong> " . date('Y-m-d H:i:s') . "</p>";
        echo "<hr>";

        // Executar a query exata do model
        $agendamentos = $this->Agendamento_model->get_pendentes_confirmacao();

        echo "<h2>Resultado da Query:</h2>";
        echo "<p><strong>Total encontrado:</strong> " . count($agendamentos) . "</p>";

        if (count($agendamentos) > 0) {
            echo "<p style='color: green; font-weight: bold;'>✅ Query encontrou agendamentos!</p>";
            foreach ($agendamentos as $ag) {
                echo "<div style='border: 2px solid green; padding: 10px; margin: 10px 0;'>";
                echo "<h3>Agendamento #{$ag->id}</h3>";
                echo "<p>Cliente: {$ag->cliente_nome}</p>";
                echo "<p>Data: {$ag->data} {$ag->hora_inicio}</p>";
                echo "<p>Tentativas: {$ag->confirmacao_tentativas}</p>";
                echo "<p>Max tentativas: {$ag->confirmacao_max_tentativas}</p>";
                echo "<p>Intervalo: {$ag->confirmacao_intervalo_tentativas_minutos} min</p>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ Query NÃO encontrou agendamentos!</p>";

            // Debug: verificar cada condição
            echo "<h3>Debug das Condições:</h3>";

            $sql_debug = "
                SELECT
                    a.id,
                    a.status,
                    a.data,
                    a.confirmacao_tentativas,
                    e.agendamento_requer_pagamento,
                    e.solicitar_confirmacao,
                    e.confirmacao_dia_anterior,
                    e.confirmacao_horario_dia_anterior,
                    TIME(NOW()) as hora_atual,
                    DATE_ADD(CURDATE(), INTERVAL 1 DAY) as data_amanha,
                    (a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)) as eh_amanha,
                    (TIME(NOW()) >= e.confirmacao_horario_dia_anterior) as passou_horario,
                    (a.status = 'pendente') as status_ok,
                    (e.agendamento_requer_pagamento = 'nao') as sem_pagamento,
                    (e.solicitar_confirmacao = 1) as solicita,
                    (e.confirmacao_dia_anterior = 1) as dia_anterior_ativo
                FROM agendamentos a
                JOIN estabelecimentos e ON a.estabelecimento_id = e.id
                WHERE a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            ";

            $debug = $this->db->query($sql_debug)->result();

            foreach ($debug as $d) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
                echo "<p><strong>Agendamento #{$d->id}</strong></p>";
                echo "<ul>";
                echo "<li>Status = pendente: " . ($d->status_ok ? '✅' : '❌') . " (atual: {$d->status})</li>";
                echo "<li>Sem pagamento: " . ($d->sem_pagamento ? '✅' : '❌') . " (atual: {$d->agendamento_requer_pagamento})</li>";
                echo "<li>Solicita confirmação: " . ($d->solicita ? '✅' : '❌') . " (atual: {$d->solicitar_confirmacao})</li>";
                echo "<li>Dia anterior ativo: " . ($d->dia_anterior_ativo ? '✅' : '❌') . " (atual: {$d->confirmacao_dia_anterior})</li>";
                echo "<li>É amanhã: " . ($d->eh_amanha ? '✅' : '❌') . " (data: {$d->data}, amanhã: {$d->data_amanha})</li>";
                echo "<li>Passou horário: " . ($d->passou_horario ? '✅' : '❌') . " (config: {$d->confirmacao_horario_dia_anterior}, atual: {$d->hora_atual})</li>";
                echo "<li>Tentativas: {$d->confirmacao_tentativas}</li>";
                echo "</ul>";
                echo "</div>";
            }
        }
    }
}
