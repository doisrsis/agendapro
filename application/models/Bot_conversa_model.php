<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model para gerenciar estados das conversas do bot WhatsApp
 *
 * @author Rafael Dias - doisr.com.br
 * @date 29/12/2024
 */
class Bot_conversa_model extends CI_Model
{
    protected $table = 'bot_conversas';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtém ou cria uma conversa para o número
     *
     * @param int $estabelecimento_id
     * @param string $numero
     * @return object
     */
    public function get_ou_criar($estabelecimento_id, $numero)
    {
        $conversa = $this->db
            ->where('estabelecimento_id', $estabelecimento_id)
            ->where('numero_whatsapp', $numero)
            ->get($this->table)
            ->row();

        if (!$conversa) {
            $this->db->insert($this->table, [
                'estabelecimento_id' => $estabelecimento_id,
                'numero_whatsapp' => $numero,
                'estado' => 'menu',
                'dados_temporarios' => json_encode([]),
                'ultima_interacao' => date('Y-m-d H:i:s')
            ]);

            $conversa = $this->db
                ->where('id', $this->db->insert_id())
                ->get($this->table)
                ->row();
        }

        // Decodificar dados temporários
        if ($conversa && $conversa->dados_temporarios) {
            $conversa->dados = json_decode($conversa->dados_temporarios, true) ?: [];
        } else {
            $conversa->dados = [];
        }

        return $conversa;
    }

    /**
     * Atualiza o estado da conversa
     *
     * @param int $conversa_id
     * @param string $estado
     * @param array $dados Dados temporários adicionais
     * @return bool
     */
    public function atualizar_estado($conversa_id, $estado, $dados = null)
    {
        $update = [
            'estado' => $estado,
            'ultima_interacao' => date('Y-m-d H:i:s')
        ];

        if ($dados !== null) {
            $update['dados_temporarios'] = json_encode($dados);
        }

        return $this->db
            ->where('id', $conversa_id)
            ->update($this->table, $update);
    }

    /**
     * Adiciona dados temporários à conversa
     *
     * @param int $conversa_id
     * @param array $novos_dados
     * @return bool
     */
    public function adicionar_dados($conversa_id, $novos_dados)
    {
        $conversa = $this->db
            ->where('id', $conversa_id)
            ->get($this->table)
            ->row();

        if (!$conversa) {
            return false;
        }

        $dados_atuais = json_decode($conversa->dados_temporarios, true) ?: [];
        $dados_merged = array_merge($dados_atuais, $novos_dados);

        return $this->db
            ->where('id', $conversa_id)
            ->update($this->table, [
                'dados_temporarios' => json_encode($dados_merged),
                'ultima_interacao' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Reseta a conversa para o menu inicial
     *
     * @param int $conversa_id
     * @return bool
     */
    public function resetar($conversa_id)
    {
        return $this->atualizar_estado($conversa_id, 'menu', []);
    }

    /**
     * Define o cliente da conversa
     *
     * @param int $conversa_id
     * @param int $cliente_id
     * @return bool
     */
    public function set_cliente($conversa_id, $cliente_id)
    {
        return $this->db
            ->where('id', $conversa_id)
            ->update($this->table, [
                'cliente_id' => $cliente_id,
                'ultima_interacao' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Limpa conversas antigas (mais de 24 horas)
     *
     * @return int Número de registros removidos
     */
    public function limpar_antigas()
    {
        $this->db
            ->where('ultima_interacao <', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->delete($this->table);

        return $this->db->affected_rows();
    }
}
