<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de teste para verificar lógica de confirmações
 * Autor: Rafael Dias - doisr.com.br
 * Data: 09/01/2026 22:27
 */
class Test_confirmacoes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->database();
    }

    public function index() {
        echo "<h1>Teste de Confirmações - Dia Anterior</h1>";
        echo "<p><strong>Data/Hora atual:</strong> " . date('Y-m-d H:i:s') . "</p>";
        echo "<p><strong>Data de amanhã:</strong> " . date('Y-m-d', strtotime('+1 day')) . "</p>";
        echo "<hr>";

        // 1. Verificar configuração do estabelecimento
        echo "<h2>1. Configuração do Estabelecimento (ID 4)</h2>";
        $estabelecimento = $this->db->get_where('estabelecimentos', ['id' => 4])->row();
        echo "<pre>";
        echo "ID: {$estabelecimento->id}\n";
        echo "Nome: {$estabelecimento->nome}\n";
        echo "Solicitar confirmação: {$estabelecimento->solicitar_confirmacao}\n";
        echo "Confirmação horas antes: {$estabelecimento->confirmacao_horas_antes}\n";
        echo "Confirmação dia anterior: {$estabelecimento->confirmacao_dia_anterior}\n";
        echo "Horário dia anterior: {$estabelecimento->confirmacao_horario_dia_anterior}\n";
        echo "Requer pagamento: {$estabelecimento->agendamento_requer_pagamento}\n";
        echo "</pre>";

        // 2. Verificar agendamentos para amanhã
        echo "<h2>2. Agendamentos para Amanhã (10/01/2026)</h2>";
        $data_amanha = date('Y-m-d', strtotime('+1 day'));
        $agendamentos_amanha = $this->db
            ->select('id, data, hora_inicio, status, confirmacao_enviada, confirmacao_enviada_em, cliente_id')
            ->where('data', $data_amanha)
            ->order_by('hora_inicio')
            ->get('agendamentos')
            ->result();

        echo "<p><strong>Total:</strong> " . count($agendamentos_amanha) . "</p>";
        echo "<pre>";
        foreach ($agendamentos_amanha as $ag) {
            print_r($ag);
        }
        echo "</pre>";

        // 3. Testar query completa do cron com debug
        echo "<h2>3. Análise Detalhada (com debug)</h2>";
        $sql = "
            SELECT
                a.id,
                a.data,
                a.hora_inicio,
                a.status,
                a.confirmacao_enviada,
                e.nome as estabelecimento_nome,
                e.solicitar_confirmacao,
                e.confirmacao_horas_antes,
                e.confirmacao_dia_anterior,
                e.confirmacao_horario_dia_anterior,
                e.agendamento_requer_pagamento,
                c.nome as cliente_nome,
                c.whatsapp as cliente_whatsapp,
                -- Debug
                TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) as horas_ate_agendamento,
                DATE_ADD(CURDATE(), INTERVAL 1 DAY) as data_amanha,
                TIME(NOW()) as hora_atual,
                (a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)) as eh_amanha,
                (TIME(NOW()) >= e.confirmacao_horario_dia_anterior) as passou_horario,
                -- Verificar cada condição
                (a.status = 'pendente') as status_ok,
                (a.confirmacao_enviada = 0) as nao_enviado,
                (a.data >= CURDATE()) as data_futura,
                (e.agendamento_requer_pagamento = 'nao') as sem_pagamento,
                (e.solicitar_confirmacao = 1) as solicita_confirmacao,
                (e.confirmacao_dia_anterior = 1) as dia_anterior_ativo
            FROM agendamentos a
            JOIN estabelecimentos e ON a.estabelecimento_id = e.id
            JOIN clientes c ON a.cliente_id = c.id
            WHERE a.estabelecimento_id = 4
              AND a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            ORDER BY a.data, a.hora_inicio
        ";

        $resultados = $this->db->query($sql)->result();

        echo "<p><strong>Total de agendamentos encontrados:</strong> " . count($resultados) . "</p>";

        foreach ($resultados as $r) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h3>Agendamento #{$r->id}</h3>";
            echo "<p><strong>Data/Hora:</strong> {$r->data} {$r->hora_inicio}</p>";
            echo "<p><strong>Cliente:</strong> {$r->cliente_nome} ({$r->cliente_whatsapp})</p>";
            echo "<p><strong>Status:</strong> {$r->status}</p>";
            echo "<p><strong>Confirmação enviada:</strong> {$r->confirmacao_enviada}</p>";

            echo "<h4>Verificação de Condições:</h4>";
            echo "<ul>";
            echo "<li>Status = pendente: " . ($r->status_ok ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>Confirmação não enviada: " . ($r->nao_enviado ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>Data futura: " . ($r->data_futura ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>Sem pagamento obrigatório: " . ($r->sem_pagamento ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>Solicita confirmação: " . ($r->solicita_confirmacao ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>Dia anterior ativo: " . ($r->dia_anterior_ativo ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>É amanhã: " . ($r->eh_amanha ? '✅ SIM' : '❌ NÃO') . "</li>";
            echo "<li>Passou horário ({$r->confirmacao_horario_dia_anterior}): " . ($r->passou_horario ? '✅ SIM' : '❌ NÃO') . " (Hora atual: {$r->hora_atual})</li>";
            echo "<li>Horas até agendamento: {$r->horas_ate_agendamento}h (Config: {$r->confirmacao_horas_antes}h)</li>";
            echo "</ul>";

            // Verificar se passaria no filtro
            $passaria_filtro =
                $r->status_ok &&
                $r->nao_enviado &&
                $r->data_futura &&
                $r->sem_pagamento &&
                $r->solicita_confirmacao &&
                (
                    ($r->horas_ate_agendamento <= $r->confirmacao_horas_antes) ||
                    ($r->dia_anterior_ativo && $r->eh_amanha && $r->passou_horario)
                );

            echo "<p style='font-size: 18px; font-weight: bold;'><strong>PASSARIA NO FILTRO DO CRON?</strong> " . ($passaria_filtro ? '✅ SIM' : '❌ NÃO') . "</p>";

            if (!$passaria_filtro) {
                echo "<p style='color: red;'><strong>Motivo:</strong> ";
                if (!$r->status_ok) echo "Status não é pendente. ";
                if (!$r->nao_enviado) echo "Confirmação já foi enviada. ";
                if (!$r->data_futura) echo "Data não é futura. ";
                if (!$r->sem_pagamento) echo "Requer pagamento. ";
                if (!$r->solicita_confirmacao) echo "Não solicita confirmação. ";
                if (!($r->horas_ate_agendamento <= $r->confirmacao_horas_antes) &&
                    !($r->dia_anterior_ativo && $r->eh_amanha && $r->passou_horario)) {
                    echo "Não atende nem 'X horas antes' nem 'dia anterior no horário'. ";
                }
                echo "</p>";
            }
            echo "</div>";
        }

        // 4. Query exata do cron
        echo "<h2>4. Query Exata do Cron (resultado final)</h2>";
        $agendamentos_cron = $this->Agendamento_model->get_pendentes_confirmacao();

        echo "<p><strong>Total retornado pela query do cron:</strong> " . count($agendamentos_cron) . "</p>";
        if (count($agendamentos_cron) > 0) {
            echo "<pre>";
            foreach ($agendamentos_cron as $ag) {
                echo "ID: {$ag->id}, Data: {$ag->data}, Hora: {$ag->hora_inicio}, Cliente: {$ag->cliente_nome}\n";
            }
            echo "</pre>";
        } else {
            echo "<p style='color: red; font-size: 18px;'><strong>❌ NENHUM AGENDAMENTO ENCONTRADO!</strong></p>";
            echo "<p>Isso significa que alguma condição do WHERE não está sendo atendida.</p>";
        }
    }
}
