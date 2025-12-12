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
                'hora_fim' => $horario['hora_fim'] ?? '18:00:00'
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
