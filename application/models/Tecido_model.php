<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Tecidos
 * 
 * Gerencia tecidos e suas cores
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024
 */
class Tecido_model extends CI_Model {

    protected $table = 'tecidos';
    protected $table_cores = 'cores';

    /**
     * Buscar todos os tecidos
     */
    public function get_all($filtros = []) {
        $this->db->select('tecidos.*, colecoes.nome as colecao_nome, COUNT(cores.id) as total_cores');
        $this->db->from($this->table);
        $this->db->join('colecoes', 'colecoes.id = tecidos.colecao_id', 'left');
        $this->db->join('cores', 'cores.tecido_id = tecidos.id', 'left');
        
        if (isset($filtros['colecao_id'])) {
            $this->db->where('tecidos.colecao_id', $filtros['colecao_id']);
        }
        
        if (isset($filtros['produto_id'])) {
            $this->db->where('tecidos.produto_id', $filtros['produto_id']);
        }
        
        if (isset($filtros['status'])) {
            $this->db->where('tecidos.status', $filtros['status']);
        }
        
        if (isset($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('tecidos.nome', $filtros['busca']);
            $this->db->or_like('tecidos.codigo', $filtros['busca']);
            $this->db->group_end();
        }
        
        $this->db->group_by('tecidos.id');
        $this->db->order_by('tecidos.nome', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Buscar tecido por ID
     */
    public function get($id) {
        $this->db->select('tecidos.*, colecoes.nome as colecao_nome');
        $this->db->from($this->table);
        $this->db->join('colecoes', 'colecoes.id = tecidos.colecao_id', 'left');
        $this->db->where('tecidos.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Inserir tecido
     */
    public function insert($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Atualizar tecido
     */
    public function update($id, $dados) {
        $dados['atualizado_em'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Deletar tecido
     */
    public function delete($id) {
        // Deletar cores associadas
        $this->db->where('tecido_id', $id);
        $this->db->delete($this->table_cores);
        
        // Deletar tecido
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Buscar cores de um tecido
     */
    public function get_cores($tecido_id) {
        $this->db->where('tecido_id', $tecido_id);
        $this->db->order_by('ordem', 'ASC');
        return $this->db->get($this->table_cores)->result();
    }

    /**
     * Buscar cor por ID
     */
    public function get_cor($id) {
        return $this->db->get_where($this->table_cores, ['id' => $id])->row();
    }

    /**
     * Adicionar cor ao tecido
     */
    public function add_cor($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        
        // Definir ordem
        if (!isset($dados['ordem'])) {
            $this->db->select_max('ordem');
            $this->db->where('tecido_id', $dados['tecido_id']);
            $max = $this->db->get($this->table_cores)->row();
            $dados['ordem'] = ($max->ordem ?? 0) + 1;
        }
        
        $this->db->insert($this->table_cores, $dados);
        return $this->db->insert_id();
    }

    /**
     * Atualizar cor
     */
    public function update_cor($id, $dados) {
        $this->db->where('id', $id);
        return $this->db->update($this->table_cores, $dados);
    }

    /**
     * Deletar cor
     */
    public function delete_cor($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_cores);
    }

    /**
     * Reordenar cores
     */
    public function reordenar_cores($ordem) {
        foreach ($ordem as $index => $id) {
            $this->db->where('id', $id);
            $this->db->update($this->table_cores, ['ordem' => $index + 1]);
        }
        return true;
    }

    /**
     * Contar tecidos
     */
    public function count($filtros = []) {
        if (isset($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        
        if (isset($filtros['colecao_id'])) {
            $this->db->where('colecao_id', $filtros['colecao_id']);
        }
        
        return $this->db->count_all_results($this->table);
    }

}
