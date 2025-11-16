<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Notificações
 *
 * Gerencia as notificações do sistema para os usuários
 *
 * @author Rafael Dias - doisr.com.br
 * @date 16/11/2024
 */
class Notificacao_model extends CI_Model {

    protected $tabela = 'notificacoes';

    /**
     * Criar nova notificação
     *
     * @param array $dados Dados da notificação
     * @return int|bool ID da notificação criada ou false
     */
    public function criar($dados) {
        $dados_notificacao = [
            'usuario_id' => $dados['usuario_id'] ?? null,
            'tipo' => $dados['tipo'] ?? 'info',
            'titulo' => $dados['titulo'],
            'mensagem' => $dados['mensagem'],
            'link' => $dados['link'] ?? null,
            'criado_em' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert($this->tabela, $dados_notificacao)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Buscar notificações de um usuário
     *
     * @param int $usuario_id ID do usuário
     * @param bool $apenas_nao_lidas Retornar apenas não lidas
     * @param int $limite Limite de resultados
     * @return array
     */
    public function get_por_usuario($usuario_id, $apenas_nao_lidas = false, $limite = 50) {
        $this->db->where('usuario_id', $usuario_id);

        if ($apenas_nao_lidas) {
            $this->db->where('lida', 0);
        }

        $this->db->order_by('criado_em', 'DESC');
        $this->db->limit($limite);

        return $this->db->get($this->tabela)->result();
    }

    /**
     * Contar notificações não lidas
     *
     * @param int $usuario_id ID do usuário
     * @return int
     */
    public function contar_nao_lidas($usuario_id) {
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('lida', 0);

        return $this->db->count_all_results($this->tabela);
    }

    /**
     * Marcar notificação como lida
     *
     * @param int $id ID da notificação
     * @param int $usuario_id ID do usuário (para segurança)
     * @return bool
     */
    public function marcar_como_lida($id, $usuario_id) {
        $this->db->where('id', $id);
        $this->db->where('usuario_id', $usuario_id);

        return $this->db->update($this->tabela, [
            'lida' => 1,
            'data_leitura' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Marcar todas como lidas
     *
     * @param int $usuario_id ID do usuário
     * @return bool
     */
    public function marcar_todas_como_lidas($usuario_id) {
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('lida', 0);

        return $this->db->update($this->tabela, [
            'lida' => 1,
            'data_leitura' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Excluir notificação
     *
     * @param int $id ID da notificação
     * @param int $usuario_id ID do usuário (para segurança)
     * @return bool
     */
    public function excluir($id, $usuario_id) {
        $this->db->where('id', $id);
        $this->db->where('usuario_id', $usuario_id);

        return $this->db->delete($this->tabela);
    }

    /**
     * Excluir notificações antigas
     *
     * @param int $dias Dias para considerar antiga
     * @return bool
     */
    public function limpar_antigas($dias = 30) {
        $data_limite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));

        $this->db->where('criado_em <', $data_limite);
        $this->db->where('lida', 1);

        return $this->db->delete($this->tabela);
    }

    /**
     * Buscar notificação por ID
     *
     * @param int $id ID da notificação
     * @return object|null
     */
    public function get_por_id($id) {
        return $this->db->get_where($this->tabela, ['id' => $id])->row();
    }

    /**
     * Criar notificação para todos os usuários
     *
     * @param array $dados Dados da notificação
     * @return bool
     */
    public function criar_para_todos($dados) {
        // Buscar todos os usuários ativos
        $usuarios = $this->db->get_where('usuarios', ['status' => 'ativo'])->result();

        $sucesso = true;
        foreach ($usuarios as $usuario) {
            $dados['usuario_id'] = $usuario->id;
            if (!$this->criar($dados)) {
                $sucesso = false;
            }
        }

        return $sucesso;
    }

    /**
     * Criar notificação para administradores
     *
     * @param array $dados Dados da notificação
     * @return bool
     */
    public function criar_para_admins($dados) {
        // Buscar todos os administradores ativos
        $admins = $this->db->get_where('usuarios', [
            'status' => 'ativo',
            'nivel' => 'admin'
        ])->result();

        $sucesso = true;
        foreach ($admins as $admin) {
            $dados['usuario_id'] = $admin->id;
            if (!$this->criar($dados)) {
                $sucesso = false;
            }
        }

        return $sucesso;
    }
}
