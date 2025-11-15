<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Orçamentos
 * 
 * Gerencia operações relacionadas aos orçamentos
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024
 */
class Orcamento_model extends CI_Model {

    protected $table = 'orcamentos';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Buscar orçamento por ID
     */
    public function get($id) {
        $this->db->select('orcamentos.*, clientes.nome as cliente_nome, clientes.email as cliente_email, clientes.telefone as cliente_telefone, clientes.whatsapp as cliente_whatsapp');
        $this->db->from($this->table);
        $this->db->join('clientes', 'clientes.id = orcamentos.cliente_id', 'left');
        $this->db->where('orcamentos.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Buscar orçamento por número
     */
    public function get_by_numero($numero) {
        $this->db->select('orcamentos.*, clientes.nome as cliente_nome, clientes.email as cliente_email, clientes.telefone as cliente_telefone, clientes.whatsapp as cliente_whatsapp');
        $this->db->from($this->table);
        $this->db->join('clientes', 'clientes.id = orcamentos.cliente_id', 'left');
        $this->db->where('orcamentos.numero', $numero);
        
        return $this->db->get()->row();
    }

    /**
     * Listar todos os orçamentos
     */
    public function get_all($filters = [], $limit = null, $offset = 0) {
        $this->db->select('orcamentos.*, clientes.nome as cliente_nome, clientes.email as cliente_email, clientes.whatsapp as cliente_whatsapp');
        $this->db->from($this->table);
        $this->db->join('clientes', 'clientes.id = orcamentos.cliente_id', 'left');
        
        if (isset($filters['status'])) {
            $this->db->where('orcamentos.status', $filters['status']);
        }
        
        if (isset($filters['tipo_atendimento'])) {
            $this->db->where('orcamentos.tipo_atendimento', $filters['tipo_atendimento']);
        }
        
        if (isset($filters['cliente_id'])) {
            $this->db->where('orcamentos.cliente_id', $filters['cliente_id']);
        }
        
        if (isset($filters['data_inicio'])) {
            $this->db->where('DATE(orcamentos.criado_em) >=', $filters['data_inicio']);
        }
        
        if (isset($filters['data_fim'])) {
            $this->db->where('DATE(orcamentos.criado_em) <=', $filters['data_fim']);
        }
        
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('orcamentos.numero', $filters['search']);
            $this->db->or_like('clientes.nome', $filters['search']);
            $this->db->or_like('clientes.email', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('orcamentos.criado_em', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Inserir novo orçamento
     */
    public function insert($data) {
        // Gerar número único do orçamento
        $data['numero'] = $this->gerar_numero();
        $data['criado_em'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar orçamento
     */
    public function update($id, $data) {
        $data['atualizado_em'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Deletar orçamento
     */
    public function delete($id) {
        // Deletar itens do orçamento
        $this->db->where('orcamento_id', $id);
        $this->db->delete('orcamento_itens');
        
        // Deletar orçamento
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Gerar número único do orçamento
     */
    private function gerar_numero() {
        $ano = date('Y');
        $mes = date('m');
        
        // Buscar último número do mês
        $this->db->select('numero');
        $this->db->like('numero', "ORC-{$ano}{$mes}", 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $ultimo = $this->db->get($this->table)->row();
        
        if ($ultimo) {
            // Extrair sequencial do último número
            $partes = explode('-', $ultimo->numero);
            $sequencial = intval(end($partes)) + 1;
        } else {
            $sequencial = 1;
        }
        
        return sprintf('ORC-%s%s-%04d', $ano, $mes, $sequencial);
    }

    /**
     * Obter itens do orçamento
     */
    public function get_itens($orcamento_id) {
        $this->db->select('orcamento_itens.*, produtos.nome as produto_nome, produtos.imagem_principal as produto_imagem, tecidos.nome as tecido_nome, cores.nome as cor_nome');
        $this->db->from('orcamento_itens');
        $this->db->join('produtos', 'produtos.id = orcamento_itens.produto_id', 'left');
        $this->db->join('tecidos', 'tecidos.id = orcamento_itens.tecido_id', 'left');
        $this->db->join('cores', 'cores.id = orcamento_itens.cor_id', 'left');
        $this->db->where('orcamento_itens.orcamento_id', $orcamento_id);
        
        return $this->db->get()->result();
    }

    /**
     * Adicionar item ao orçamento
     */
    public function add_item($orcamento_id, $data) {
        $data['orcamento_id'] = $orcamento_id;
        $data['criado_em'] = date('Y-m-d H:i:s');
        
        $this->db->insert('orcamento_itens', $data);
        $item_id = $this->db->insert_id();
        
        // Recalcular valor total do orçamento
        $this->recalcular_total($orcamento_id);
        
        return $item_id;
    }

    /**
     * Remover item do orçamento
     */
    public function remove_item($item_id) {
        // Buscar orçamento_id antes de deletar
        $item = $this->db->get_where('orcamento_itens', ['id' => $item_id])->row();
        
        if (!$item) {
            return false;
        }
        
        // Deletar extras do item
        $this->db->where('orcamento_item_id', $item_id);
        $this->db->delete('orcamento_extras');
        
        // Deletar item
        $this->db->where('id', $item_id);
        $result = $this->db->delete('orcamento_itens');
        
        // Recalcular total
        if ($result) {
            $this->recalcular_total($item->orcamento_id);
        }
        
        return $result;
    }

    /**
     * Obter extras do item
     */
    public function get_extras_item($item_id) {
        $this->db->select('orcamento_extras.*, extras.nome as extra_nome');
        $this->db->from('orcamento_extras');
        $this->db->join('extras', 'extras.id = orcamento_extras.extra_id', 'left');
        $this->db->where('orcamento_extras.orcamento_item_id', $item_id);
        
        return $this->db->get()->result();
    }

    /**
     * Adicionar extra ao item
     */
    public function add_extra($item_id, $extra_id, $valor) {
        $data = [
            'orcamento_item_id' => $item_id,
            'extra_id' => $extra_id,
            'valor' => $valor,
            'criado_em' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('orcamento_extras', $data);
        
        // Buscar orçamento_id e recalcular
        $item = $this->db->get_where('orcamento_itens', ['id' => $item_id])->row();
        if ($item) {
            $this->recalcular_total($item->orcamento_id);
        }
        
        return $this->db->insert_id();
    }

    /**
     * Recalcular valor total do orçamento
     */
    public function recalcular_total($orcamento_id) {
        // Somar itens
        $this->db->select_sum('preco_total');
        $this->db->where('orcamento_id', $orcamento_id);
        $total_itens = $this->db->get('orcamento_itens')->row()->preco_total ?? 0;
        
        // Buscar desconto
        $orcamento = $this->get($orcamento_id);
        $desconto = $orcamento->desconto ?? 0;
        
        // Calcular valor final
        $valor_final = $total_itens - $desconto;
        
        // Atualizar orçamento
        $this->db->where('id', $orcamento_id);
        $this->db->update($this->table, [
            'valor_total' => $total_itens,
            'valor_final' => $valor_final,
            'atualizado_em' => date('Y-m-d H:i:s')
        ]);
        
        return $valor_final;
    }

    /**
     * Contar orçamentos
     */
    public function count($filters = []) {
        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (isset($filters['tipo_atendimento'])) {
            $this->db->where('tipo_atendimento', $filters['tipo_atendimento']);
        }
        
        if (isset($filters['data_inicio'])) {
            $this->db->where('DATE(criado_em) >=', $filters['data_inicio']);
        }
        
        if (isset($filters['data_fim'])) {
            $this->db->where('DATE(criado_em) <=', $filters['data_fim']);
        }
        
        return $this->db->count_all_results($this->table);
    }

    /**
     * Obter estatísticas de orçamentos
     */
    public function get_stats($periodo = 'mes') {
        $stats = [];
        
        // Total de orçamentos
        $stats['total'] = $this->db->count_all($this->table);
        
        // Orçamentos por status
        $this->db->select('status, COUNT(*) as total');
        $this->db->group_by('status');
        $stats['por_status'] = $this->db->get($this->table)->result();
        
        // Valor total de orçamentos
        $this->db->select_sum('valor_final');
        $stats['valor_total'] = $this->db->get($this->table)->row()->valor_final ?? 0;
        
        // Orçamentos do período
        if ($periodo == 'mes') {
            $this->db->where('MONTH(criado_em)', date('m'));
            $this->db->where('YEAR(criado_em)', date('Y'));
        } elseif ($periodo == 'semana') {
            $this->db->where('YEARWEEK(criado_em)', date('oW'));
        } elseif ($periodo == 'hoje') {
            $this->db->where('DATE(criado_em)', date('Y-m-d'));
        }
        
        $stats['periodo_total'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    /**
     * Marcar como enviado por WhatsApp
     */
    public function marcar_enviado_whatsapp($id) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'enviado_whatsapp' => 1,
            'data_envio_whatsapp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Marcar como enviado por email
     */
    public function marcar_enviado_email($id) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'enviado_email' => 1,
            'data_envio_email' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Listar orçamentos com dados do cliente (para admin)
     */
    public function get_all_with_cliente($limit = null, $offset = 0, $filtros = []) {
        $this->db->select('orcamentos.*, clientes.nome as cliente_nome, clientes.email as cliente_email, clientes.whatsapp as cliente_whatsapp');
        $this->db->from($this->table);
        $this->db->join('clientes', 'clientes.id = orcamentos.cliente_id', 'left');
        
        // Aplicar filtros
        if (isset($filtros['status']) && !empty($filtros['status'])) {
            $this->db->where('orcamentos.status', $filtros['status']);
        }
        
        if (isset($filtros['busca']) && !empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('orcamentos.numero', $filtros['busca']);
            $this->db->or_like('clientes.nome', $filtros['busca']);
            $this->db->or_like('clientes.email', $filtros['busca']);
            $this->db->group_end();
        }
        
        if (isset($filtros['data_inicio']) && !empty($filtros['data_inicio'])) {
            $this->db->where('DATE(orcamentos.criado_em) >=', $filtros['data_inicio']);
        }
        
        if (isset($filtros['data_fim']) && !empty($filtros['data_fim'])) {
            $this->db->where('DATE(orcamentos.criado_em) <=', $filtros['data_fim']);
        }
        
        $this->db->order_by('orcamentos.criado_em', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Contar todos os orçamentos
     */
    public function count_all() {
        return $this->db->count_all($this->table);
    }

    /**
     * Contar orçamentos por status
     */
    public function count_by_status($status) {
        $this->db->where('status', $status);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Somar valor total de todos os orçamentos
     */
    public function sum_valor_total() {
        $this->db->select_sum('valor_final');
        $result = $this->db->get($this->table)->row();
        return $result->valor_final ?? 0;
    }

    /**
     * Buscar orçamentos por cliente
     */
    public function get_by_cliente($cliente_id) {
        $this->db->select('orcamentos.*');
        $this->db->from($this->table);
        $this->db->where('cliente_id', $cliente_id);
        $this->db->order_by('criado_em', 'DESC');
        
        return $this->db->get()->result();
    }
}
