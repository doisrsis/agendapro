<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Bloqueios
 *
 * Gerencia bloqueios de horários dos profissionais (férias, folgas, etc)
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Bloqueio_model extends CI_Model {

    protected $table = 'bloqueios';

    /**
     * Buscar todos os bloqueios
     */
    public function get_all($filtros = [], $limit = null, $offset = null) {
        $this->db->select('b.*, p.nome as profissional_nome, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' b');
        $this->db->join('profissionais p', 'p.id = b.profissional_id');
        $this->db->join('estabelecimentos e', 'e.id = p.estabelecimento_id');

        if (!empty($filtros['profissional_id'])) {
            $this->db->where('b.profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('b.data_fim >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('b.data_inicio <=', $filtros['data_fim']);
        }

        if (!empty($filtros['tipo'])) {
            $this->db->where('b.tipo', $filtros['tipo']);
        }

        $this->db->order_by('b.data_inicio', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Buscar bloqueio por ID
     */
    public function get_by_id($id) {
        $this->db->select('b.*, p.nome as profissional_nome');
        $this->db->from($this->table . ' b');
        $this->db->join('profissionais p', 'p.id = b.profissional_id');
        $this->db->where('b.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Criar novo bloqueio
     */
    public function create($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar bloqueio
     */
    public function update($id, $dados) {
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Deletar bloqueio
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Verificar se há bloqueio em um período
     */
    public function tem_bloqueio($profissional_id, $data, $hora_inicio = null, $hora_fim = null) {
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data_inicio <=', $data);
        $this->db->where('data_fim >=', $data);

        // Se for bloqueio de horário específico
        if ($hora_inicio && $hora_fim) {
            $this->db->group_start();
            $this->db->where('hora_inicio IS NULL'); // Bloqueio de dia inteiro
            $this->db->or_group_start();
            $this->db->where('hora_inicio <=', $hora_fim);
            $this->db->where('hora_fim >=', $hora_inicio);
            $this->db->group_end();
            $this->db->group_end();
        }

        $bloqueio = $this->db->get($this->table)->row();

        return $bloqueio ? true : false;
    }

    /**
     * Buscar bloqueios ativos de um profissional
     */
    public function get_bloqueios_ativos($profissional_id) {
        $hoje = date('Y-m-d');

        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data_fim >=', $hoje);
        $this->db->order_by('data_inicio', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar bloqueios futuros
     */
    public function get_bloqueios_futuros($profissional_id, $dias = 30) {
        $hoje = date('Y-m-d');
        $data_limite = date('Y-m-d', strtotime("+{$dias} days"));

        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data_inicio >=', $hoje);
        $this->db->where('data_inicio <=', $data_limite);
        $this->db->order_by('data_inicio', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Contar bloqueios
     */
    public function count($filtros = []) {
        if (!empty($filtros['profissional_id'])) {
            $this->db->where('profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('data_fim >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('data_inicio <=', $filtros['data_fim']);
        }

        return $this->db->count_all_results($this->table);
    }
}
