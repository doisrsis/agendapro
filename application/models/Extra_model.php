<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Extras
 * 
 * Gerencia extras como Blackout, Motorização, Instalação, etc
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024 19:05
 */
class Extra_model extends CI_Model {

    protected $table = 'extras';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Listar extras com filtros
     */
    public function get_all($filtros = []) {
        $this->db->select('extras.*');
        $this->db->from($this->table);
        
        // Filtro por status
        if (isset($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        
        // Filtro por tipo de preço
        if (isset($filtros['tipo_preco'])) {
            $this->db->where('tipo_preco', $filtros['tipo_preco']);
        }
        
        // Busca por nome
        if (isset($filtros['busca']) && !empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('nome', $filtros['busca']);
            $this->db->or_like('descricao', $filtros['busca']);
            $this->db->group_end();
        }
        
        $this->db->order_by('ordem', 'ASC');
        $this->db->order_by('nome', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Buscar extra por ID
     */
    public function get($id) {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Inserir extra
     */
    public function insert($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Atualizar extra
     */
    public function update($id, $dados) {
        $dados['atualizado_em'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Deletar extra
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Alternar status
     */
    public function toggle_status($id) {
        $extra = $this->get($id);
        if (!$extra) return false;
        
        $novo_status = ($extra->status === 'ativo') ? 'inativo' : 'ativo';
        return $this->update($id, ['status' => $novo_status]);
    }

    /**
     * Atualizar ordem
     */
    public function atualizar_ordem($id, $ordem) {
        return $this->update($id, ['ordem' => $ordem]);
    }

    /**
     * Contar extras
     */
    public function count_all($filtros = []) {
        $this->db->from($this->table);
        
        if (isset($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        
        if (isset($filtros['tipo_preco'])) {
            $this->db->where('tipo_preco', $filtros['tipo_preco']);
        }
        
        if (isset($filtros['busca']) && !empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('nome', $filtros['busca']);
            $this->db->or_like('descricao', $filtros['busca']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Buscar extras por tipo de preço
     */
    public function get_by_tipo($tipo_preco, $apenas_ativos = true) {
        $this->db->where('tipo_preco', $tipo_preco);
        
        if ($apenas_ativos) {
            $this->db->where('status', 'ativo');
        }
        
        $this->db->order_by('ordem', 'ASC');
        $this->db->order_by('nome', 'ASC');
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar extras aplicáveis a um produto
     */
    public function get_aplicaveis_produto($produto_id) {
        $this->db->where('status', 'ativo');
        $this->db->order_by('ordem', 'ASC');
        
        $extras = $this->db->get($this->table)->result();
        
        // Filtrar extras aplicáveis ao produto
        $aplicaveis = [];
        foreach ($extras as $extra) {
            if (empty($extra->aplicavel_a)) {
                // Se não tem restrição, é aplicável a todos
                $aplicaveis[] = $extra;
            } else {
                $produtos_aplicaveis = json_decode($extra->aplicavel_a, true);
                if (is_array($produtos_aplicaveis) && in_array($produto_id, $produtos_aplicaveis)) {
                    $aplicaveis[] = $extra;
                }
            }
        }
        
        return $aplicaveis;
    }

}
