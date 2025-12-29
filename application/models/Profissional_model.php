<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Profissionais
 *
 * Gerencia os profissionais dos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Profissional_model extends CI_Model {

    protected $table = 'profissionais';

    /**
     * Buscar todos os profissionais
     */
    public function get_all($filtros = []) {
        $this->db->select('p.*, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' p');
        $this->db->join('estabelecimentos e', 'e.id = p.estabelecimento_id', 'left');

        // Filtros
        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('p.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['status'])) {
            $this->db->where('p.status', $filtros['status']);
        }

        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('p.nome', $filtros['busca']);
            $this->db->or_like('p.email', $filtros['busca']);
            $this->db->or_like('p.whatsapp', $filtros['busca']);
            $this->db->group_end();
        }

        $this->db->order_by('p.nome', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Buscar profissional por ID
     */
    public function get_by_id($id) {
        $this->db->select('p.*, e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' p');
        $this->db->join('estabelecimentos e', 'e.id = p.estabelecimento_id', 'left');
        $this->db->where('p.id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Criar novo profissional
     */
    public function create($data) {
        $insert_data = [
            'estabelecimento_id' => $data['estabelecimento_id'],
            'nome' => $data['nome'],
            'foto' => $data['foto'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => $data['status'] ?? 'ativo',
        ];

        if ($this->db->insert($this->table, $insert_data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar profissional
     */
    public function update($id, $data) {
        $update_data = [];

        if (isset($data['nome'])) $update_data['nome'] = $data['nome'];
        if (isset($data['foto'])) $update_data['foto'] = $data['foto'];
        if (isset($data['whatsapp'])) $update_data['whatsapp'] = $data['whatsapp'];
        if (isset($data['telefone'])) $update_data['telefone'] = $data['telefone'];
        if (isset($data['email'])) $update_data['email'] = $data['email'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Deletar profissional
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Vincular serviços ao profissional
     */
    public function vincular_servicos($profissional_id, $servicos_ids) {
        // Remover vínculos antigos
        $this->db->where('profissional_id', $profissional_id);
        $this->db->delete('profissional_servicos');

        // Criar novos vínculos
        if (!empty($servicos_ids)) {
            foreach ($servicos_ids as $servico_id) {
                $this->db->insert('profissional_servicos', [
                    'profissional_id' => $profissional_id,
                    'servico_id' => $servico_id
                ]);
            }
        }

        return true;
    }

    /**
     * Buscar serviços do profissional
     */
    public function get_servicos($profissional_id) {
        $this->db->select('s.*');
        $this->db->from('servicos s');
        $this->db->join('profissional_servicos ps', 'ps.servico_id = s.id');
        $this->db->where('ps.profissional_id', $profissional_id);
        $this->db->where('s.status', 'ativo');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Verificar se profissional pode realizar serviço
     */
    public function pode_realizar_servico($profissional_id, $servico_id) {
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('servico_id', $servico_id);
        $query = $this->db->get('profissional_servicos');

        return $query->num_rows() > 0;
    }

    /**
     * Contar profissionais por estabelecimento
     *
     * @param int $estabelecimento_id
     * @return int
     */
    public function count_by_estabelecimento($estabelecimento_id) {
        return $this->db
            ->where('estabelecimento_id', $estabelecimento_id)
            ->where('status', 'ativo')
            ->count_all_results($this->table);
    }

    /**
     * Buscar profissionais por estabelecimento
     *
     * @param int $estabelecimento_id
     * @return array
     */
    public function get_by_estabelecimento($estabelecimento_id) {
        return $this->get_all(['estabelecimento_id' => $estabelecimento_id]);
    }

    /**
     * Buscar profissionais que realizam um serviço específico
     *
     * @param int $servico_id
     * @param int $estabelecimento_id
     * @return array
     */
    public function get_by_servico($servico_id, $estabelecimento_id = null) {
        $this->db->select('p.*');
        $this->db->from($this->table . ' p');
        $this->db->join('profissional_servicos ps', 'ps.profissional_id = p.id');
        $this->db->where('ps.servico_id', $servico_id);
        $this->db->where('p.status', 'ativo');

        if ($estabelecimento_id) {
            $this->db->where('p.estabelecimento_id', $estabelecimento_id);
        }

        $this->db->order_by('p.nome', 'ASC');

        return $this->db->get()->result();
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
