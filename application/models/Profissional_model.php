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
            'whatsapp' => $this->normalizar_telefone($data['whatsapp'] ?? null),
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

        // Normalizar e validar WhatsApp (adicionar nono dígito se necessário)
        if (array_key_exists('whatsapp', $data)) {
            $update_data['whatsapp'] = $this->normalizar_telefone($data['whatsapp']);
        }

        if (array_key_exists('email', $data)) {
            $update_data['email'] = $data['email'];
        }

        if (isset($data['status'])) $update_data['status'] = $data['status'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Normalizar telefone - adicionar nono dígito se necessário
     *
     * @param string $telefone
     * @return string|null
     */
    private function normalizar_telefone($telefone) {
        if (empty($telefone)) {
            return null;
        }

        // Remover caracteres não numéricos
        $numero_limpo = preg_replace('/[^0-9]/', '', $telefone);

        // Se estiver vazio após limpeza, retornar null
        if (empty($numero_limpo)) {
            return null;
        }

        // Se tiver 10 dígitos (sem nono dígito) e for celular (DDD + 8 ou 9)
        // Adicionar o 9 no início do número
        if (strlen($numero_limpo) == 10) {
            $ddd = substr($numero_limpo, 0, 2);
            $numero = substr($numero_limpo, 2);

            // Se o número começa com 6, 7, 8 ou 9 (celular antigo sem nono dígito)
            if (in_array($numero[0], ['6', '7', '8', '9'])) {
                $numero_limpo = $ddd . '9' . $numero;
            }
        }

        // Se tiver 13 dígitos (com código do país 55), remover o 55
        if (strlen($numero_limpo) == 13 && substr($numero_limpo, 0, 2) == '55') {
            $numero_limpo = substr($numero_limpo, 2);
        }

        // Formatar: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
        if (strlen($numero_limpo) == 11) {
            return '(' . substr($numero_limpo, 0, 2) . ') ' . substr($numero_limpo, 2, 5) . '-' . substr($numero_limpo, 7);
        } elseif (strlen($numero_limpo) == 10) {
            return '(' . substr($numero_limpo, 0, 2) . ') ' . substr($numero_limpo, 2, 4) . '-' . substr($numero_limpo, 6);
        }

        // Retornar como está se não conseguir formatar
        return $telefone;
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
