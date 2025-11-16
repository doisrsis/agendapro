<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Configurações
 *
 * Gerencia configurações do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024
 */
class Configuracao_model extends CI_Model {

    protected $table = 'configuracoes';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Buscar configuração por chave
     */
    public function get($chave) {
        $config = $this->db->get_where($this->table, ['chave' => $chave])->row();
        return $config ? $config->valor : null;
    }

    /**
     * Buscar todas as configurações
     */
    public function get_all($grupo = null) {
        if ($grupo) {
            $this->db->where('grupo', $grupo);
        }

        $this->db->order_by('grupo', 'ASC');
        $this->db->order_by('chave', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar todas as configurações como array associativo
     */
    public function get_all_as_array($grupo = null) {
        $configs = $this->get_all($grupo);
        $array = [];

        foreach ($configs as $config) {
            $array[$config->chave] = $config->valor;
        }

        return $array;
    }

    /**
     * Buscar configurações por grupo
     */
    public function get_by_grupo($grupo) {
        return $this->get_all($grupo);
    }

    /**
     * Salvar ou atualizar configuração
     */
    public function set($chave, $valor, $tipo = 'texto', $grupo = 'geral', $descricao = null) {
        // Verificar se já existe
        $existe = $this->db->get_where($this->table, ['chave' => $chave])->row();

        if ($existe) {
            // Atualizar
            $data = [
                'valor' => $valor,
                'atualizado_em' => date('Y-m-d H:i:s')
            ];

            $this->db->where('chave', $chave);
            return $this->db->update($this->table, $data);
        } else {
            // Inserir
            $data = [
                'chave' => $chave,
                'valor' => $valor,
                'tipo' => $tipo,
                'grupo' => $grupo,
                'descricao' => $descricao,
                'criado_em' => date('Y-m-d H:i:s')
            ];

            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Salvar múltiplas configurações
     */
    public function set_multiple($configuracoes) {
        foreach ($configuracoes as $chave => $valor) {
            $this->set($chave, $valor);
        }
        return true;
    }

    /**
     * Deletar configuração
     */
    public function delete($chave) {
        $this->db->where('chave', $chave);
        return $this->db->delete($this->table);
    }

    /**
     * Listar grupos de configurações
     */
    public function get_grupos() {
        $this->db->select('grupo');
        $this->db->distinct();
        $this->db->order_by('grupo', 'ASC');

        $result = $this->db->get($this->table)->result();

        $grupos = [];
        foreach ($result as $row) {
            $grupos[] = $row->grupo;
        }

        return $grupos;
    }

    /**
     * Obter configurações da empresa
     */
    public function get_empresa() {
        return $this->get_all_as_array('empresa');
    }

    /**
     * Obter configurações do site
     */
    public function get_site() {
        return $this->get_all_as_array('site');
    }

    /**
     * Obter configurações de orçamento
     */
    public function get_orcamento() {
        return $this->get_all_as_array('orcamento');
    }

    /**
     * Obter configurações de e-mail
     */
    public function get_email() {
        return $this->get_all_as_array('email');
    }

    /**
     * Buscar configuração por chave (objeto completo)
     */
    public function get_by_chave($chave) {
        return $this->db->get_where($this->table, ['chave' => $chave])->row();
    }

    /**
     * Atualizar configuração por chave
     */
    public function update_by_chave($chave, $valor) {
        $data = [
            'valor' => $valor,
            'atualizado_em' => date('Y-m-d H:i:s')
        ];

        $this->db->where('chave', $chave);
        return $this->db->update($this->table, $data);
    }

    /**
     * Inserir nova configuração
     */
    public function insert($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Obter configurações dos Correios
     */
    public function get_correios() {
        return $this->get_all_as_array('correios');
    }

    /**
     * Obter configurações do Mercado Pago
     */
    public function get_mercadopago() {
        return $this->get_all_as_array('mercadopago');
    }

    /**
     * Obter configurações de notificações
     */
    public function get_notificacoes() {
        return $this->get_all_as_array('notificacoes');
    }

    /**
     * Obter valor de configuração diretamente (atalho)
     *
     * @param string $chave Chave da configuração
     * @param mixed $default Valor padrão se não encontrar
     * @return mixed
     */
    public function get_valor($chave, $default = '') {
        $config = $this->get_by_chave($chave);
        return $config ? $config->valor : $default;
    }

    /**
     * Obter configurações SMTP
     */
    public function get_smtp() {
        return $this->get_all_as_array('smtp');
    }
}
