<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Serviços
 *
 * Gerencia os serviços oferecidos pelos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Servico_model extends CI_Model {

    protected $table = 'servicos';

    /**
     * Buscar todos os serviços
     */
    public function get_all($filtros = [], $limit = null, $offset = null) {
        $this->db->select('s.*, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' s');
        $this->db->join('estabelecimentos e', 'e.id = s.estabelecimento_id', 'left');

        if (!empty($limit)) {
            $this->db->limit($limit, $offset);
        }
        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('s.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['status'])) {
            $this->db->where('s.status', $filtros['status']);
        }

        if (!empty($filtros['busca'])) {
            $this->db->like('s.nome', $filtros['busca']);
        }

        $this->db->order_by('s.nome', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Contar total de serviços (para paginação)
     */
    public function count_all($filtros = []) {
        $this->db->from($this->table . ' s');

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('s.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['status'])) {
            $this->db->where('s.status', $filtros['status']);
        }

        if (!empty($filtros['busca'])) {
            $this->db->like('s.nome', $filtros['busca']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Buscar serviço por ID
     */
    public function get_by_id($id) {
        $this->db->select('s.*, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' s');
        $this->db->join('estabelecimentos e', 'e.id = s.estabelecimento_id', 'left');
        $this->db->where('s.id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Criar novo serviço
     */
    public function create($data) {
        $insert_data = [
            'estabelecimento_id' => $data['estabelecimento_id'],
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null,
            'duracao' => $data['duracao'],
            'preco' => $data['preco'],
            'status' => $data['status'] ?? 'ativo',
        ];

        if ($this->db->insert($this->table, $insert_data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar serviço
     */
    public function update($id, $data) {
        $update_data = [];

        if (isset($data['nome'])) $update_data['nome'] = $data['nome'];
        if (isset($data['descricao'])) $update_data['descricao'] = $data['descricao'];
        if (isset($data['duracao'])) $update_data['duracao'] = $data['duracao'];
        if (isset($data['preco'])) $update_data['preco'] = $data['preco'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Deletar serviço
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Buscar profissionais que realizam o serviço
     */
    public function get_profissionais($servico_id) {
        $this->db->select('p.*');
        $this->db->from('profissionais p');
        $this->db->join('profissional_servicos ps', 'ps.profissional_id = p.id');
        $this->db->where('ps.servico_id', $servico_id);
        $this->db->where('p.status', 'ativo');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Contar serviços por filtros
     */
    public function count($filtros = []) {
        $this->db->from($this->table);

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Buscar serviços ativos de um estabelecimento
     *
     * @param int $estabelecimento_id
     * @return array
     */
    public function get_by_estabelecimento($estabelecimento_id) {
        return $this->db
            ->select('s.*, s.duracao as duracao_minutos')
            ->from($this->table . ' s')
            ->where('s.estabelecimento_id', $estabelecimento_id)
            ->where('s.status', 'ativo')
            ->order_by('s.nome', 'ASC')
            ->get()
            ->result();
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
