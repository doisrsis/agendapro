<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de teste para verificar tentativas de confirmação
 * Autor: Rafael Dias - doisr.com.br
 * Data: 09/01/2026 23:05
 */
class Test_tentativas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        echo "<h1>Verificação de Tentativas de Confirmação</h1>";
        echo "<p><strong>Data/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";
        echo "<hr>";

        // Verificar agendamentos para amanhã com tentativas
        $sql = "
            SELECT
                a.id,
                a.data,
                a.hora_inicio,
                a.status,
                a.confirmacao_tentativas,
                a.confirmacao_ultima_tentativa,
                a.confirmacao_enviada,
                a.confirmacao_enviada_em,
                c.nome as cliente_nome,
                c.whatsapp,
                e.confirmacao_max_tentativas,
                e.confirmacao_intervalo_tentativas_minutos,
                TIMESTAMPDIFF(MINUTE, a.confirmacao_ultima_tentativa, NOW()) as minutos_desde_ultima
            FROM agendamentos a
            JOIN clientes c ON a.cliente_id = c.id
            JOIN estabelecimentos e ON a.estabelecimento_id = e.id
            WHERE a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            ORDER BY a.hora_inicio
        ";

        $agendamentos = $this->db->query($sql)->result();

        echo "<h2>Agendamentos para Amanhã (10/01/2026)</h2>";
        echo "<p><strong>Total:</strong> " . count($agendamentos) . "</p>";

        foreach ($agendamentos as $ag) {
            echo "<div style='border: 2px solid #333; padding: 15px; margin: 15px 0; background: #f5f5f5;'>";
            echo "<h3>Agendamento #{$ag->id}</h3>";
            echo "<p><strong>Cliente:</strong> {$ag->cliente_nome} ({$ag->whatsapp})</p>";
            echo "<p><strong>Data/Hora:</strong> {$ag->data} {$ag->hora_inicio}</p>";
            echo "<p><strong>Status:</strong> {$ag->status}</p>";

            echo "<hr>";
            echo "<h4>📊 Informações de Confirmação:</h4>";
            echo "<ul>";
            echo "<li><strong>Tentativas:</strong> {$ag->confirmacao_tentativas} / {$ag->confirmacao_max_tentativas}</li>";
            echo "<li><strong>Última tentativa:</strong> " . ($ag->confirmacao_ultima_tentativa ?? 'Nunca') . "</li>";
            echo "<li><strong>Minutos desde última:</strong> " . ($ag->minutos_desde_ultima ?? 'N/A') . "</li>";
            echo "<li><strong>Intervalo configurado:</strong> {$ag->confirmacao_intervalo_tentativas_minutos} minutos</li>";
            echo "<li><strong>Confirmação enviada:</strong> {$ag->confirmacao_enviada}</li>";
            echo "<li><strong>Enviada em:</strong> " . ($ag->confirmacao_enviada_em ?? 'Nunca') . "</li>";
            echo "</ul>";

            // Verificar se pode enviar próxima tentativa
            $pode_enviar = false;
            $motivo = '';

            if ($ag->status != 'pendente') {
                $motivo = "Status não é pendente ({$ag->status})";
            } elseif ($ag->confirmacao_tentativas == 0) {
                $pode_enviar = true;
                $motivo = "Primeira tentativa - aguardando horário configurado";
            } elseif ($ag->confirmacao_tentativas >= $ag->confirmacao_max_tentativas) {
                $motivo = "Atingiu máximo de tentativas ({$ag->confirmacao_tentativas}/{$ag->confirmacao_max_tentativas})";
            } elseif ($ag->minutos_desde_ultima < $ag->confirmacao_intervalo_tentativas_minutos) {
                $faltam = $ag->confirmacao_intervalo_tentativas_minutos - $ag->minutos_desde_ultima;
                $motivo = "Aguardando intervalo (faltam {$faltam} minutos)";
            } else {
                $pode_enviar = true;
                $motivo = "Pronto para próxima tentativa";
            }

            echo "<hr>";
            echo "<p style='font-size: 18px;'><strong>Pode enviar próxima tentativa?</strong> ";
            if ($pode_enviar) {
                echo "<span style='color: green; font-weight: bold;'>✅ SIM</span>";
            } else {
                echo "<span style='color: red; font-weight: bold;'>❌ NÃO</span>";
            }
            echo "</p>";
            echo "<p><strong>Motivo:</strong> {$motivo}</p>";

            // Próxima ação
            if ($ag->confirmacao_tentativas > 0 && $ag->confirmacao_tentativas < $ag->confirmacao_max_tentativas) {
                $proxima_tentativa = $ag->confirmacao_tentativas + 1;
                $tipo = $proxima_tentativa == 2 ? 'URGENTE' : 'ÚLTIMA CHANCE';
                echo "<p><strong>Próxima tentativa:</strong> #{$proxima_tentativa} - Tipo: {$tipo}</p>";
            } elseif ($ag->confirmacao_tentativas >= $ag->confirmacao_max_tentativas) {
                echo "<p style='color: red; font-weight: bold;'>⚠️ Será cancelado automaticamente após intervalo</p>";
            }

            echo "</div>";
        }
    }
}
