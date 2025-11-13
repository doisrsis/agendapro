<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Preços
 * 
 * Gerencia tabela de preços por dimensões dos produtos
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024 19:30
 */
class Preco_model extends CI_Model {

    protected $table = 'precos';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Listar preços com filtros
     */
    public function get_all($filtros = []) {
        $this->db->select('precos.*, produtos.nome as produto_nome, categorias.nome as categoria_nome');
        $this->db->from($this->table);
        $this->db->join('produtos', 'produtos.id = precos.produto_id', 'left');
        $this->db->join('categorias', 'categorias.id = produtos.categoria_id', 'left');
        
        // Filtro por produto
        if (isset($filtros['produto_id'])) {
            $this->db->where('precos.produto_id', $filtros['produto_id']);
        }
        
        // Busca
        if (isset($filtros['busca']) && !empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('produtos.nome', $filtros['busca']);
            $this->db->or_like('precos.observacoes', $filtros['busca']);
            $this->db->group_end();
        }
        
        $this->db->order_by('produtos.nome', 'ASC');
        $this->db->order_by('precos.largura_min', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Buscar preço por ID
     */
    public function get($id) {
        $this->db->select('precos.*, produtos.nome as produto_nome');
        $this->db->from($this->table);
        $this->db->join('produtos', 'produtos.id = precos.produto_id', 'left');
        $this->db->where('precos.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Inserir preço
     */
    public function insert($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Atualizar preço
     */
    public function update($id, $dados) {
        $dados['atualizado_em'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Deletar preço
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Buscar preços por produto
     */
    public function get_by_produto($produto_id) {
        $this->db->where('produto_id', $produto_id);
        $this->db->order_by('largura_min', 'ASC');
        $this->db->order_by('altura_min', 'ASC');
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Calcular preço para dimensões específicas
     */
    public function calcular_preco($produto_id, $largura, $altura) {
        $this->db->where('produto_id', $produto_id);
        $this->db->where('largura_min <=', $largura);
        $this->db->where('largura_max >=', $largura);
        $this->db->where('altura_min <=', $altura);
        $this->db->where('altura_max >=', $altura);
        $this->db->order_by('preco_m2', 'ASC');
        $this->db->limit(1);
        
        $preco = $this->db->get($this->table)->row();
        
        if (!$preco) {
            return null;
        }
        
        $area_m2 = $largura * $altura;
        
        // Calcular baseado no tipo de preço
        if ($preco->preco_fixo && $preco->preco_fixo > 0) {
            return $preco->preco_fixo;
        } elseif ($preco->preco_ml && $preco->preco_ml > 0) {
            return $largura * $preco->preco_ml;
        } else {
            return $area_m2 * $preco->preco_m2;
        }
    }

    /**
     * Contar preços
     */
    public function count_all($filtros = []) {
        $this->db->from($this->table);
        $this->db->join('produtos', 'produtos.id = precos.produto_id', 'left');
        
        if (isset($filtros['produto_id'])) {
            $this->db->where('precos.produto_id', $filtros['produto_id']);
        }
        
        if (isset($filtros['busca']) && !empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('produtos.nome', $filtros['busca']);
            $this->db->or_like('precos.observacoes', $filtros['busca']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

}
