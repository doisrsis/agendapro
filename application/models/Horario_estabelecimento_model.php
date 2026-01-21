<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Horário Estabelecimento
 *
 * Gerenciamento de horários de funcionamento por dia da semana
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Horario_estabelecimento_model extends CI_Model {

    protected $table = 'horarios_estabelecimento';

    /**
     * Obter horários de um estabelecimento
     */
    public function get_by_estabelecimento($estabelecimento_id) {
        $this->db->where('estabelecimento_id', $estabelecimento_id);
        $this->db->order_by('dia_semana', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Obter horário de um dia específico
     */
    public function get_by_dia($estabelecimento_id, $dia_semana) {
        $this->db->where('estabelecimento_id', $estabelecimento_id);
        $this->db->where('dia_semana', $dia_semana);
        return $this->db->get($this->table)->row();
    }

    /**
     * Salvar horários da semana
     */
    public function salvar_semana($estabelecimento_id, $horarios) {
        // Deletar horários existentes
        $this->db->where('estabelecimento_id', $estabelecimento_id);
        $this->db->delete($this->table);

        // Inserir novos horários
        $dados = [];
        foreach ($horarios as $dia => $horario) {
            $dados[] = [
                'estabelecimento_id' => $estabelecimento_id,
                'dia_semana' => $dia,
                'ativo' => $horario['ativo'] ?? 0,
                'hora_inicio' => $horario['hora_inicio'] ?? '08:00:00',
                'hora_fim' => $horario['hora_fim'] ?? '18:00:00',
                'almoco_ativo' => $horario['almoco_ativo'] ?? 0,
                'almoco_inicio' => $horario['almoco_inicio'] ?? null,
                'almoco_fim' => $horario['almoco_fim'] ?? null
            ];
        }

        if (!empty($dados)) {
            return $this->db->insert_batch($this->table, $dados);
        }

        return true;
    }

    /**
     * Verificar se estabelecimento está aberto em determinado dia/horário
     */
    public function verificar_disponibilidade($estabelecimento_id, $dia_semana, $hora) {
        $this->db->where('estabelecimento_id', $estabelecimento_id);
        $this->db->where('dia_semana', $dia_semana);
        $this->db->where('ativo', 1);
        $this->db->where('hora_inicio <=', $hora);
        $this->db->where('hora_fim >=', $hora);

        return $this->db->get($this->table)->num_rows() > 0;
    }

    /**
     * Verificar se horário está no intervalo de almoço
     * Verifica sobreposição: se qualquer parte do agendamento (início até fim) sobrepõe o horário de almoço
     *
     * @param int $estabelecimento_id
     * @param int $dia_semana
     * @param string $hora_inicio Hora de início do agendamento
     * @param string $hora_fim Hora de fim do agendamento (opcional, se não informado verifica apenas hora_inicio)
     * @return bool True se sobrepõe o horário de almoço (bloqueado), False se disponível
     */
    public function verificar_horario_almoco($estabelecimento_id, $dia_semana, $hora_inicio, $hora_fim = null) {
        $horario = $this->get_by_dia($estabelecimento_id, $dia_semana);

        if (!$horario || !$horario->almoco_ativo) {
            return false; // Não tem almoço configurado
        }

        // Se não informou hora_fim, verifica apenas hora_inicio (compatibilidade com código antigo)
        if ($hora_fim === null) {
            // Usa < no fim para permitir agendamento no horário de término (ex: almoço até 13h, 13h está disponível)
            $resultado = ($hora_inicio >= $horario->almoco_inicio && $hora_inicio < $horario->almoco_fim);
            log_message('debug', "Horario_estabelecimento_model::verificar_horario_almoco SEM hora_fim - hora_inicio=$hora_inicio, almoco={$horario->almoco_inicio}-{$horario->almoco_fim}, resultado=" . ($resultado ? 'BLOQUEADO' : 'DISPONIVEL'));
            return $resultado;
        }

        // Verificar sobreposição: agendamento sobrepõe almoço se:
        // - início do agendamento < fim do almoço E
        // - fim do agendamento > início do almoço
        // Mas permitir agendamento que inicia exatamente no fim do almoço (ex: almoço até 13h, agendar às 13h)
        $resultado = ($hora_inicio < $horario->almoco_fim && $hora_fim > $horario->almoco_inicio);
        log_message('debug', "Horario_estabelecimento_model::verificar_horario_almoco COM hora_fim - hora_inicio=$hora_inicio, hora_fim=$hora_fim, almoco={$horario->almoco_inicio}-{$horario->almoco_fim}, resultado=" . ($resultado ? 'BLOQUEADO' : 'DISPONIVEL'));
        return $resultado;
    }

    /**
     * Obter nomes dos dias da semana
     */
    public function get_dias_semana() {
        return [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado'
        ];
    }
}
