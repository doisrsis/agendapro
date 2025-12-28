<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Clientes
 *
 * Gerencia os clientes dos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Cliente_model extends CI_Model {

    protected $table = 'clientes';

    /**
     * Buscar todos os clientes
     */
    public function get_all($filtros = []) {
        $this->db->select('c.*, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' c');
        $this->db->join('estabelecimentos e', 'e.id = c.estabelecimento_id', 'left');

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('c.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['tipo'])) {
            $this->db->where('c.tipo', $filtros['tipo']);
        }

        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('c.nome', $filtros['busca']);
            $this->db->or_like('c.cpf', $filtros['busca']);
            $this->db->or_like('c.whatsapp', $filtros['busca']);
            $this->db->or_like('c.email', $filtros['busca']);
            $this->db->group_end();
        }

        $this->db->order_by('c.nome', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Buscar cliente por ID
     */
    public function get_by_id($id) {
        $this->db->select('c.*, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' c');
        $this->db->join('estabelecimentos e', 'e.id = c.estabelecimento_id', 'left');
        $this->db->where('c.id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Buscar cliente por WhatsApp
     */
    public function get_by_whatsapp($whatsapp, $estabelecimento_id) {
        $this->db->where('whatsapp', $whatsapp);
        $this->db->where('estabelecimento_id', $estabelecimento_id);

        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Criar novo cliente
     */
    public function create($data) {
        // CPF vazio deve ser NULL para evitar erro de duplicidade
        $cpf = !empty($data['cpf']) ? $data['cpf'] : null;

        $insert_data = [
            'estabelecimento_id' => $data['estabelecimento_id'],
            'nome' => $data['nome'],
            'cpf' => $cpf,
            'whatsapp' => $data['whatsapp'],
            'email' => !empty($data['email']) ? $data['email'] : null,
            'foto' => $data['foto'] ?? null,
            'tipo' => $data['tipo'] ?? 'novo',
            'total_agendamentos' => 0,
        ];

        if ($this->db->insert($this->table, $insert_data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar cliente
     */
    public function update($id, $data) {
        $update_data = [];

        if (isset($data['nome'])) $update_data['nome'] = $data['nome'];
        if (isset($data['cpf'])) $update_data['cpf'] = $data['cpf'];
        if (isset($data['whatsapp'])) $update_data['whatsapp'] = $data['whatsapp'];
        if (isset($data['email'])) $update_data['email'] = $data['email'];
        if (isset($data['foto'])) $update_data['foto'] = $data['foto'];
        if (isset($data['tipo'])) $update_data['tipo'] = $data['tipo'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Deletar cliente
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Incrementar contador de agendamentos
     */
    public function incrementar_agendamentos($id) {
        $this->db->set('total_agendamentos', 'total_agendamentos + 1', FALSE);
        $this->db->where('id', $id);
        $this->db->update($this->table);

        // Atualizar tipo do cliente baseado no total de agendamentos
        $cliente = $this->get_by_id($id);
        if ($cliente) {
            $novo_tipo = $this->calcular_tipo_cliente($cliente->total_agendamentos + 1);
            if ($novo_tipo != $cliente->tipo) {
                $this->update($id, ['tipo' => $novo_tipo]);
            }
        }
    }

    /**
     * Calcular tipo do cliente baseado no histórico
     */
    private function calcular_tipo_cliente($total_agendamentos) {
        if ($total_agendamentos >= 10) {
            return 'vip';
        } elseif ($total_agendamentos >= 3) {
            return 'recorrente';
        }
        return 'novo';
    }

    /**
     * Buscar histórico de agendamentos do cliente
     */
    public function get_historico_agendamentos($cliente_id, $limit = 10) {
        $this->db->select('a.*, s.nome as servico_nome, p.nome as profissional_nome');
        $this->db->from('agendamentos a');
        $this->db->join('servicos s', 's.id = a.servico_id', 'left');
        $this->db->join('profissionais p', 'p.id = a.profissional_id', 'left');
        $this->db->where('a.cliente_id', $cliente_id);
        $this->db->order_by('a.data', 'DESC');
        $this->db->order_by('a.hora_inicio', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Contar clientes por filtros
     */
    public function count($filtros = []) {
        $this->db->from($this->table);

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['tipo'])) {
            $this->db->where('tipo', $filtros['tipo']);
        }

        return $this->db->count_all_results();
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
