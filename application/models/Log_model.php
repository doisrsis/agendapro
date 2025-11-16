<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Logs
 *
 * Gerencia os logs de ações do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 15/11/2024
 */
class Log_model extends CI_Model {

    protected $table = 'logs';

    /**
     * Buscar log por ID
     */
    public function get($id) {
        $this->db->select('logs.*, usuarios.nome as usuario_nome, usuarios.email as usuario_email');
        $this->db->from($this->table);
        $this->db->join('usuarios', 'usuarios.id = logs.usuario_id', 'left');
        $this->db->where('logs.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Alias para get() - compatibilidade
     */
    public function get_by_id($id) {
        return $this->get($id);
    }

    /**
     * Buscar todos os logs com filtros
     */
    public function get_all($filtros = [], $limit = null, $offset = 0) {
        $this->db->select('logs.*, usuarios.nome as usuario_nome, usuarios.email as usuario_email');
        $this->db->from($this->table);
        $this->db->join('usuarios', 'usuarios.id = logs.usuario_id', 'left');

        // Aplicar filtros
        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('usuarios.nome', $filtros['busca']);
            $this->db->or_like('logs.acao', $filtros['busca']);
            $this->db->or_like('logs.tabela', $filtros['busca']);
            $this->db->or_like('logs.ip', $filtros['busca']);
            $this->db->group_end();
        }

        if (!empty($filtros['acao'])) {
            $this->db->where('logs.acao', $filtros['acao']);
        }

        if (!empty($filtros['tabela'])) {
            $this->db->where('logs.tabela', $filtros['tabela']);
        }

        if (!empty($filtros['usuario_id'])) {
            $this->db->where('logs.usuario_id', $filtros['usuario_id']);
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('DATE(logs.criado_em) >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('DATE(logs.criado_em) <=', $filtros['data_fim']);
        }

        $this->db->order_by('logs.criado_em', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Contar logs com filtros (alias)
     */
    public function count($filtros = []) {
        return $this->count_all($filtros);
    }

    /**
     * Contar logs com filtros
     */
    public function count_all($filtros = []) {
        $this->db->from($this->table);
        $this->db->join('usuarios', 'usuarios.id = logs.usuario_id', 'left');

        // Aplicar filtros
        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('usuarios.nome', $filtros['busca']);
            $this->db->or_like('logs.acao', $filtros['busca']);
            $this->db->or_like('logs.tabela', $filtros['busca']);
            $this->db->or_like('logs.ip', $filtros['busca']);
            $this->db->group_end();
        }

        if (!empty($filtros['acao'])) {
            $this->db->where('logs.acao', $filtros['acao']);
        }

        if (!empty($filtros['tabela'])) {
            $this->db->where('logs.tabela', $filtros['tabela']);
        }

        if (!empty($filtros['usuario_id'])) {
            $this->db->where('logs.usuario_id', $filtros['usuario_id']);
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('DATE(logs.criado_em) >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('DATE(logs.criado_em) <=', $filtros['data_fim']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Inserir log
     */
    public function insert($data) {
        $data['criado_em'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Limpar logs antigos
     */
    public function limpar_antigos($dias = 30) {
        $data_limite = date('Y-m-d', strtotime("-{$dias} days"));

        $this->db->where('DATE(criado_em) <', $data_limite);
        return $this->db->delete($this->table);
    }

    /**
     * Obter ações distintas
     */
    public function get_acoes_distintas() {
        $this->db->select('acao');
        $this->db->from($this->table);
        $this->db->distinct();
        $this->db->order_by('acao', 'ASC');

        $result = $this->db->get()->result();
        return array_column($result, 'acao');
    }

    /**
     * Obter tabelas distintas
     */
    public function get_tabelas_distintas() {
        $this->db->select('tabela');
        $this->db->from($this->table);
        $this->db->distinct();
        $this->db->order_by('tabela', 'ASC');

        $result = $this->db->get()->result();
        return array_column($result, 'tabela');
    }

    /**
     * Obter usuários distintos
     */
    public function get_usuarios_distintos() {
        $this->db->select('usuarios.id, usuarios.nome');
        $this->db->from($this->table);
        $this->db->join('usuarios', 'usuarios.id = logs.usuario_id', 'left');
        $this->db->where('logs.usuario_id IS NOT NULL');
        $this->db->distinct();
        $this->db->order_by('usuarios.nome', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Estatísticas de logs
     */
    public function get_estatisticas() {
        // Total de logs
        $total = $this->db->count_all($this->table);

        // Logs hoje
        $this->db->where('DATE(criado_em)', date('Y-m-d'));
        $hoje = $this->db->count_all_results($this->table);

        // Logs esta semana
        $this->db->where('YEARWEEK(criado_em, 1)', 'YEARWEEK(CURDATE(), 1)');
        $semana = $this->db->count_all_results($this->table);

        // Logs este mês
        $this->db->where('YEAR(criado_em)', date('Y'));
        $this->db->where('MONTH(criado_em)', date('m'));
        $mes = $this->db->count_all_results($this->table);

        return [
            'total' => $total,
            'hoje' => $hoje,
            'semana' => $semana,
            'mes' => $mes
        ];
    }
}
