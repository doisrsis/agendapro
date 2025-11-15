<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Usuários
 * 
 * Gerencia operações relacionadas aos usuários do sistema
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024
 */
class Usuario_model extends CI_Model {

    protected $table = 'usuarios';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Buscar usuário por ID
     */
    public function get($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    /**
     * Buscar usuário por email
     */
    public function get_by_email($email) {
        return $this->db->get_where($this->table, ['email' => $email])->row();
    }

    /**
     * Listar todos os usuários
     */
    public function get_all($busca = null, $nivel = null, $status = null) {
        // Busca
        if ($busca) {
            $this->db->group_start();
            $this->db->like('nome', $busca);
            $this->db->or_like('email', $busca);
            $this->db->group_end();
        }
        
        // Filtros
        if ($nivel) {
            $this->db->where('nivel', $nivel);
        }
        
        if ($status) {
            $this->db->where('status', $status);
        }
        
        $this->db->order_by('nome', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Inserir novo usuário
     */
    public function insert($data) {
        // Hash da senha
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        $data['criado_em'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar usuário
     */
    public function update($id, $data) {
        // Hash da senha se foi alterada
        if (isset($data['senha']) && !empty($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        } else {
            unset($data['senha']);
        }
        
        $data['atualizado_em'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Deletar usuário
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Verificar credenciais de login
     */
    public function verificar_login($email, $senha) {
        $usuario = $this->get_by_email($email);
        
        if (!$usuario) {
            return false;
        }
        
        if ($usuario->status !== 'ativo') {
            return false;
        }
        
        if (password_verify($senha, $usuario->senha)) {
            // Atualizar último acesso
            $this->db->where('id', $usuario->id);
            $this->db->update($this->table, ['ultimo_acesso' => date('Y-m-d H:i:s')]);
            
            return $usuario;
        }
        
        return false;
    }

    /**
     * Gerar token de recuperação de senha
     */
    public function gerar_token_recuperacao($email) {
        $usuario = $this->get_by_email($email);
        
        if (!$usuario) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->db->where('id', $usuario->id);
        $this->db->update($this->table, [
            'token_recuperacao' => $token,
            'token_expiracao' => $expiracao
        ]);
        
        return $token;
    }

    /**
     * Verificar token de recuperação
     */
    public function verificar_token($token) {
        $this->db->where('token_recuperacao', $token);
        $this->db->where('token_expiracao >', date('Y-m-d H:i:s'));
        $usuario = $this->db->get($this->table)->row();
        
        return $usuario ? $usuario : false;
    }

    /**
     * Resetar senha com token
     */
    public function resetar_senha($token, $nova_senha) {
        $usuario = $this->verificar_token($token);
        
        if (!$usuario) {
            return false;
        }
        
        $this->db->where('id', $usuario->id);
        return $this->db->update($this->table, [
            'senha' => password_hash($nova_senha, PASSWORD_DEFAULT),
            'token_recuperacao' => null,
            'token_expiracao' => null,
            'atualizado_em' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Contar usuários
     */
    public function count($filters = []) {
        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        return $this->db->count_all_results($this->table);
    }

    /**
     * Buscar permissões do usuário
     */
    public function get_permissoes($usuario_id) {
        $result = $this->db->get_where('usuario_permissoes', ['usuario_id' => $usuario_id])->result();
        
        $permissoes = [];
        foreach ($result as $row) {
            $permissoes[$row->modulo] = json_decode($row->permissoes, true);
        }
        
        return $permissoes;
    }

    /**
     * Inserir permissão
     */
    public function insert_permissao($usuario_id, $modulo, $permissoes_json) {
        return $this->db->insert('usuario_permissoes', [
            'usuario_id' => $usuario_id,
            'modulo' => $modulo,
            'permissoes' => $permissoes_json
        ]);
    }

    /**
     * Deletar todas as permissões do usuário
     */
    public function delete_permissoes($usuario_id) {
        $this->db->where('usuario_id', $usuario_id);
        return $this->db->delete('usuario_permissoes');
    }

    /**
     * Verificar se usuário tem permissão
     */
    public function tem_permissao($usuario_id, $modulo, $acao) {
        // Admin tem todas as permissões
        $usuario = $this->get($usuario_id);
        if ($usuario && $usuario->nivel == 'admin') {
            return true;
        }
        
        // Buscar permissão específica
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('modulo', $modulo);
        $permissao = $this->db->get('usuario_permissoes')->row();
        
        if (!$permissao) {
            return false;
        }
        
        $permissoes = json_decode($permissao->permissoes, true);
        return isset($permissoes[$acao]) && $permissoes[$acao] === true;
    }
}

