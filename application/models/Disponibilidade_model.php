<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Disponibilidade
 *
 * Gerencia os horários de disponibilidade dos profissionais
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Disponibilidade_model extends CI_Model {

    protected $table = 'disponibilidade';

    /**
     * Buscar todas as disponibilidades
     */
    public function get_all($filtros = []) {
        $this->db->select('d.*, p.nome as profissional_nome, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' d');
        $this->db->join('profissionais p', 'p.id = d.profissional_id');
        $this->db->join('estabelecimentos e', 'e.id = p.estabelecimento_id');

        if (!empty($filtros['profissional_id'])) {
            $this->db->where('d.profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['dia_semana'])) {
            $this->db->where('d.dia_semana', $filtros['dia_semana']);
        }

        $this->db->order_by('d.dia_semana', 'ASC');
        $this->db->order_by('d.hora_inicio', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Buscar disponibilidade por ID
     */
    public function get_by_id($id) {
        $this->db->select('d.*, p.nome as profissional_nome');
        $this->db->from($this->table . ' d');
        $this->db->join('profissionais p', 'p.id = d.profissional_id');
        $this->db->where('d.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Buscar disponibilidades de um profissional
     */
    public function get_by_profissional($profissional_id) {
        $this->db->where('profissional_id', $profissional_id);
        $this->db->order_by('dia_semana', 'ASC');
        $this->db->order_by('hora_inicio', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar disponibilidade de um profissional em um dia específico
     */
    public function get_by_dia($profissional_id, $dia_semana) {
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('dia_semana', $dia_semana);
        $this->db->order_by('hora_inicio', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Criar nova disponibilidade
     */
    public function create($dados) {
        // Validar se não há conflito de horários
        if (!$this->validar_conflito($dados['profissional_id'], $dados['dia_semana'], $dados['hora_inicio'], $dados['hora_fim'])) {
            return false;
        }

        $dados['criado_em'] = date('Y-m-d H:i:s');
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar disponibilidade
     */
    public function update($id, $dados) {
        $disponibilidade = $this->get_by_id($id);

        if (!$disponibilidade) {
            return false;
        }

        // Validar conflito (excluindo o próprio registro)
        if (!$this->validar_conflito(
            $dados['profissional_id'] ?? $disponibilidade->profissional_id,
            $dados['dia_semana'] ?? $disponibilidade->dia_semana,
            $dados['hora_inicio'] ?? $disponibilidade->hora_inicio,
            $dados['hora_fim'] ?? $disponibilidade->hora_fim,
            $id
        )) {
            return false;
        }

        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Deletar disponibilidade
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Validar conflito de horários
     */
    private function validar_conflito($profissional_id, $dia_semana, $hora_inicio, $hora_fim, $excluir_id = null) {
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('dia_semana', $dia_semana);

        // Verificar se há sobreposição de horários
        $this->db->group_start();
        $this->db->where('hora_inicio <', $hora_fim);
        $this->db->where('hora_fim >', $hora_inicio);
        $this->db->group_end();

        if ($excluir_id) {
            $this->db->where('id !=', $excluir_id);
        }

        $conflito = $this->db->get($this->table)->row();

        return !$conflito; // Retorna true se NÃO houver conflito
    }

    /**
     * Criar disponibilidade padrão para um profissional
     * Segunda a Sexta: 08:00 - 12:00 e 14:00 - 18:00
     * Sábado: 08:00 - 12:00
     */
    public function criar_disponibilidade_padrao($profissional_id) {
        $disponibilidades = [
            // Segunda a Sexta - Manhã
            ['profissional_id' => $profissional_id, 'dia_semana' => 1, 'hora_inicio' => '08:00:00', 'hora_fim' => '12:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 2, 'hora_inicio' => '08:00:00', 'hora_fim' => '12:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 3, 'hora_inicio' => '08:00:00', 'hora_fim' => '12:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 4, 'hora_inicio' => '08:00:00', 'hora_fim' => '12:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 5, 'hora_inicio' => '08:00:00', 'hora_fim' => '12:00:00'],

            // Segunda a Sexta - Tarde
            ['profissional_id' => $profissional_id, 'dia_semana' => 1, 'hora_inicio' => '14:00:00', 'hora_fim' => '18:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 2, 'hora_inicio' => '14:00:00', 'hora_fim' => '18:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 3, 'hora_inicio' => '14:00:00', 'hora_fim' => '18:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 4, 'hora_inicio' => '14:00:00', 'hora_fim' => '18:00:00'],
            ['profissional_id' => $profissional_id, 'dia_semana' => 5, 'hora_inicio' => '14:00:00', 'hora_fim' => '18:00:00'],

            // Sábado - Manhã
            ['profissional_id' => $profissional_id, 'dia_semana' => 6, 'hora_inicio' => '08:00:00', 'hora_fim' => '12:00:00'],
        ];

        foreach ($disponibilidades as $disp) {
            $this->create($disp);
        }

        return true;
    }

    /**
     * Verificar se profissional está disponível em um horário específico
     */
    public function esta_disponivel($profissional_id, $data, $hora_inicio, $hora_fim) {
        $dia_semana = date('N', strtotime($data)); // 1 = Segunda, 7 = Domingo

        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('dia_semana', $dia_semana);
        $this->db->where('hora_inicio <=', $hora_inicio);
        $this->db->where('hora_fim >=', $hora_fim);

        $disponibilidade = $this->db->get($this->table)->row();

        return $disponibilidade ? true : false;
    }

    /**
     * Contar disponibilidades
     */
    public function count($filtros = []) {
        if (!empty($filtros['profissional_id'])) {
            $this->db->where('profissional_id', $filtros['profissional_id']);
        }

        return $this->db->count_all_results($this->table);
    }

    // =========================================================================
    // ALIASES PARA COMPATIBILIDADE DE NOMENCLATURA
    // =========================================================================

    /**
     * Alias para get_by_id()
     */
    public function get($id) {
        return $this->get_by_id($id);
    }

    /**
     * Alias para create()
     */
    public function criar($dados) {
        return $this->create($dados);
    }

    /**
     * Alias para update()
     */
    public function atualizar($id, $dados) {
        return $this->update($id, $dados);
    }

    /**
     * Alias para delete()
     */
    public function excluir($id) {
        return $this->delete($id);
    }
}
