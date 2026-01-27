<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Usuario_model
 * Descrição: Gerenciamento de usuários multi-tenant
 *
 * Tipos de usuários:
 * - super_admin: Acesso total ao sistema
 * - estabelecimento: Dono do estabelecimento
 * - profissional: Profissional autônomo ou vinculado
 *
 * @author Rafael Dias - doisr.com.br
 * @date 09/12/2024
 */
class Usuario_model extends CI_Model {

    protected $table = 'usuarios';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Criar novo usuário
     *
     * @param array $dados
     * @return int|false ID do usuário criado ou false
     */
    public function criar($dados) {
        // Hash da senha
        if (isset($dados['senha'])) {
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        }

        // Remover campos inexistentes no banco
        // FIX: Usar array_key_exists pois isset retorna false para NULL
        if (array_key_exists('telefone', $dados)) {
            unset($dados['telefone']);
        }

        // Definir valores padrão
        $dados['ativo'] = $dados['ativo'] ?? 1;
        $dados['primeiro_acesso'] = 1;
        $dados['criado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Autenticar usuário
     *
     * @param string $email
     * @param string $senha
     * @return object|false Dados do usuário ou false
     */
    public function autenticar($email, $senha) {
        $usuario = $this->db
            ->where('email', $email)
            ->where('ativo', 1)
            ->get($this->table)
            ->row();

        if ($usuario && password_verify($senha, $usuario->senha)) {
            // Atualizar último acesso
            $this->db->where('id', $usuario->id)
                ->update($this->table, ['ultimo_acesso' => date('Y-m-d H:i:s')]);

            // Remover senha do objeto retornado
            unset($usuario->senha);

            return $usuario;
        }

        return false;
    }

    /**
     * Buscar usuário por ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id) {
        $usuario = $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();

        if ($usuario) {
            unset($usuario->senha);
        }

        return $usuario;
    }

    /**
     * Buscar usuário por e-mail
     *
     * @param string $email
     * @return object|null
     */
    public function get_by_email($email) {
        $usuario = $this->db
            ->where('email', $email)
            ->get($this->table)
            ->row();

        if ($usuario) {
            unset($usuario->senha);
        }

        return $usuario;
    }

    /**
     * Listar todos os usuários
     *
     * @param string $tipo Filtrar por tipo (opcional)
     * @return array
     */
    public function get_all($tipo = null) {
        if ($tipo) {
            $this->db->where('tipo', $tipo);
        }

        $usuarios = $this->db
            ->select('id, email, tipo, nome, ativo, estabelecimento_id, profissional_id, ultimo_acesso')
            ->get($this->table)
            ->result();

        return $usuarios;
    }

     /**
     * Listar usuários com filtros avançados
     *
     * @param string $busca Busca por nome ou email
     * @param array $filtros Filtros adicionais (tipo, ativo, estabelecimento_id)
     * @return array
     */
    public function get_all_with_filters($busca = null, $filtros = []) {
        $this->db->select('u.*, e.nome as estabelecimento_nome, p.nome as profissional_nome')
            ->from($this->table . ' u')
            ->join('estabelecimentos e', 'u.estabelecimento_id = e.id', 'left')
            ->join('profissionais p', 'u.profissional_id = p.id', 'left');

        // Busca por nome ou email
        if ($busca) {
            $this->db->group_start()
                ->like('u.nome', $busca)
                ->or_like('u.email', $busca)
                ->group_end();
        }

        // Filtros
        if (isset($filtros['tipo'])) {
            $this->db->where('u.tipo', $filtros['tipo']);
        }

        if (isset($filtros['ativo'])) {
            $this->db->where('u.ativo', $filtros['ativo']);
        }

        if (isset($filtros['estabelecimento_id'])) {
            $this->db->where('u.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        $this->db->order_by('u.nome', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Atualizar usuário
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        // Se estiver atualizando senha, fazer hash
        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        } else {
            unset($dados['senha']);
        }

        // Remover campos inexistentes no banco
        if (array_key_exists('telefone', $dados)) {
            unset($dados['telefone']);
        }

        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $dados);
    }

    /**
     * Atualizar senha
     *
     * @param int $id
     * @param string $nova_senha
     * @return bool
     */
    public function atualizar_senha($id, $nova_senha) {
        $dados = [
            'senha' => password_hash($nova_senha, PASSWORD_BCRYPT),
            'atualizado_em' => date('Y-m-d H:i:s')
        ];

        return $this->db->where('id', $id)->update($this->table, $dados);
    }

    /**
     * Gerar token para reset de senha
     *
     * @param string $email
     * @return string|false Token gerado ou false
     */
    public function gerar_token_reset($email) {
        $usuario = $this->get_by_email($email);

        if (!$usuario) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $dados = [
            'token_reset_senha' => $token,
            'token_expiracao' => $expiracao
        ];

        if ($this->db->where('id', $usuario->id)->update($this->table, $dados)) {
            return $token;
        }

        return false;
    }

    /**
     * Validar token de reset
     *
     * @param string $token
     * @return object|false Dados do usuário ou false
     */
    public function validar_token_reset($token) {
        $usuario = $this->db
            ->where('token_reset_senha', $token)
            ->where('token_expiracao >=', date('Y-m-d H:i:s'))
            ->get($this->table)
            ->row();

        if ($usuario) {
            unset($usuario->senha);
            return $usuario;
        }

        return false;
    }

    /**
     * Limpar token de reset após uso
     *
     * @param int $id
     * @return bool
     */
    public function limpar_token_reset($id) {
        $dados = [
            'token_reset_senha' => null,
            'token_expiracao' => null
        ];

        return $this->db->where('id', $id)->update($this->table, $dados);
    }

    /**
     * Marcar primeiro acesso como concluído
     *
     * @param int $id
     * @return bool
     */
    public function marcar_primeiro_acesso($id) {
        return $this->db->where('id', $id)->update($this->table, ['primeiro_acesso' => 0]);
    }

    /**
     * Ativar/Desativar usuário
     *
     * @param int $id
     * @param bool $ativo
     * @return bool
     */
    public function toggle_ativo($id, $ativo = true) {
        return $this->db->where('id', $id)->update($this->table, ['ativo' => $ativo ? 1 : 0]);
    }

    /**
     * Excluir usuário
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Verificar se e-mail já existe
     *
     * @param string $email
     * @param int $excluir_id ID para excluir da verificação (útil em edições)
     * @return bool
     */
    public function email_existe($email, $excluir_id = null) {
        $this->db->where('email', $email);

        if ($excluir_id) {
            $this->db->where('id !=', $excluir_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Buscar usuários por estabelecimento
     *
     * @param int $estabelecimento_id
     * @return array
     */
    public function get_by_estabelecimento($estabelecimento_id) {
        return $this->db
            ->select('id, email, tipo, nome, ativo, ultimo_acesso')
            ->where('estabelecimento_id', $estabelecimento_id)
            ->get($this->table)
            ->result();
    }

    /**
     * Criar usuário para estabelecimento
     *
     * @param int $estabelecimento_id
     * @param string $email
     * @param string $senha
     * @param string $nome
     * @return int|false
     */
    public function criar_usuario_estabelecimento($estabelecimento_id, $email, $senha, $nome) {
        $dados = [
            'email' => $email,
            'senha' => $senha,
            'tipo' => 'estabelecimento',
            'estabelecimento_id' => $estabelecimento_id,
            'nome' => $nome,
            'ativo' => 1,
            'primeiro_acesso' => 1
        ];

        return $this->criar($dados);
    }

    /**
     * Criar usuário para profissional
     *
     * @param int $profissional_id
     * @param int $estabelecimento_id
     * @param string $email
     * @param string $senha
     * @param string $nome
     * @return int|false
     */
    public function criar_usuario_profissional($profissional_id, $estabelecimento_id, $email, $senha, $nome) {
        $dados = [
            'email' => $email,
            'senha' => $senha,
            'tipo' => 'profissional',
            'profissional_id' => $profissional_id,
            'estabelecimento_id' => $estabelecimento_id,
            'nome' => $nome,
            'ativo' => 1,
            'primeiro_acesso' => 1
        ];

        return $this->criar($dados);
    }

    /**
     * Contar usuários
     *
     * @param array $filtros Filtros opcionais (tipo, estabelecimento_id, ativo)
     * @return int
     */
    public function count($filtros = []) {
        $this->db->from($this->table);

        if (!empty($filtros['tipo'])) {
            $this->db->where('tipo', $filtros['tipo']);
        }

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (isset($filtros['ativo'])) {
            $this->db->where('ativo', $filtros['ativo']);
        }

        return $this->db->count_all_results();
    }
}
