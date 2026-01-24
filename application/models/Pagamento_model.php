<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Pagamento_model
 *
 * Gerenciamento de pagamentos via Mercado Pago
 *
 * @author Rafael Dias - doisr.com.br
 * @date 18/12/2024
 */
class Pagamento_model extends CI_Model {

    protected $table = 'pagamentos';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Criar novo pagamento
     */
    public function criar($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Buscar pagamento por ID
     */
    public function get($id) {
        return $this->db
            ->select('p.*, pl.nome as plano_nome, e.nome as estabelecimento_nome')
            ->from($this->table . ' p')
            ->join('planos pl', 'p.plano_id = pl.id', 'left')
            ->join('estabelecimentos e', 'p.estabelecimento_id = e.id', 'left')
            ->where('p.id', $id)
            ->get()
            ->row();
    }

    /**
     * Buscar pagamento por ID do Mercado Pago
     */
    public function get_by_mercadopago_id($mercadopago_id) {
        return $this->db
            ->select('p.*, pl.nome as plano_nome')
            ->from($this->table . ' p')
            ->join('planos pl', 'p.plano_id = pl.id', 'left')
            ->where('p.mercadopago_id', $mercadopago_id)
            ->get()
            ->row();
    }

    /**
     * Listar pagamentos de um estabelecimento
     */
    public function get_by_estabelecimento($estabelecimento_id, $limit = 50, $offset = 0) {
        return $this->db
            ->select('p.*, pl.nome as plano_nome')
            ->from($this->table . ' p')
            ->join('planos pl', 'p.plano_id = pl.id', 'left')
            ->where('p.estabelecimento_id', $estabelecimento_id)
            ->order_by('p.criado_em', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    /**
     * Atualizar status do pagamento
     */
    public function atualizar_status($id, $status, $status_detail = null, $payment_data = null) {
        $dados = [
            'status' => $status,
            'atualizado_em' => date('Y-m-d H:i:s')
        ];

        if ($status_detail) {
            $dados['status_detail'] = $status_detail;
        }

        if ($payment_data) {
            $dados['payment_data'] = is_array($payment_data) ? json_encode($payment_data) : $payment_data;
        }

        return $this->db
            ->where('id', $id)
            ->update($this->table, $dados);
    }

    /**
     * Atualizar status por ID do Mercado Pago
     */
    public function atualizar_status_by_mp_id($mercadopago_id, $status, $status_detail = null, $payment_data = null) {
        $dados = [
            'status' => $status,
            'atualizado_em' => date('Y-m-d H:i:s')
        ];

        if ($status_detail) {
            $dados['status_detail'] = $status_detail;
        }

        if ($payment_data) {
            $dados['payment_data'] = is_array($payment_data) ? json_encode($payment_data) : $payment_data;
        }

        return $this->db
            ->where('mercadopago_id', $mercadopago_id)
            ->update($this->table, $dados);
    }

    /**
     * Vincular pagamento a uma assinatura
     */
    public function vincular_assinatura($id, $assinatura_id) {
        return $this->db
            ->where('id', $id)
            ->update($this->table, [
                'assinatura_id' => $assinatura_id,
                'atualizado_em' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Buscar pagamentos pendentes (para polling)
     */
    public function get_pendentes($estabelecimento_id = null, $minutos = 30) {
        $this->db
            ->select('p.*')
            ->from($this->table . ' p')
            ->where('p.status', 'pending')
            ->where('p.criado_em >', date('Y-m-d H:i:s', strtotime("-{$minutos} minutes")));

        if ($estabelecimento_id) {
            $this->db->where('p.estabelecimento_id', $estabelecimento_id);
        }

        return $this->db
            ->order_by('p.criado_em', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Contar pagamentos por status
     */
    public function contar_por_status($estabelecimento_id, $periodo_dias = 30) {
        return $this->db
            ->select('status, COUNT(*) as total')
            ->from($this->table)
            ->where('estabelecimento_id', $estabelecimento_id)
            ->where('criado_em >', date('Y-m-d', strtotime("-{$periodo_dias} days")))
            ->group_by('status')
            ->get()
            ->result();
    }

    /**
     * Buscar último pagamento aprovado de um estabelecimento
     */
    public function get_ultimo_aprovado($estabelecimento_id) {
        return $this->db
            ->select('p.*, pl.nome as plano_nome')
            ->from($this->table . ' p')
            ->join('planos pl', 'p.plano_id = pl.id', 'left')
            ->where('p.estabelecimento_id', $estabelecimento_id)
            ->where('p.status', 'approved')
            ->order_by('p.criado_em', 'DESC')
            ->limit(1)
            ->get()
            ->row();
    }

    /**
     * Excluir pagamento (apenas se pendente ou rejeitado)
     */
    public function excluir($id) {
        $pagamento = $this->get($id);

        if (!$pagamento) {
            return false;
        }

        // Só permite excluir se pendente ou rejeitado
        if (!in_array($pagamento->status, ['pending', 'rejected', 'cancelled'])) {
            return false;
        }

        return $this->db
            ->where('id', $id)
            ->delete($this->table);
    }

    /**
     * Criar pagamento de agendamento
     *
     * @param array $dados Dados do pagamento
     * @return int|false ID do pagamento criado ou false
     */
    public function criar_agendamento($dados) {
        $pagamento = [
            'estabelecimento_id' => $dados['estabelecimento_id'],
            'agendamento_id' => $dados['agendamento_id'],
            'plano_id' => 0, // Não é assinatura
            'tipo' => 'agendamento',
            'valor' => $dados['valor'],
            'mercadopago_id' => $dados['mercadopago_id'] ?? null,
            'status' => 'pending',
            'payment_data' => json_encode($dados['payment_data'] ?? [])
        ];

        return $this->criar($pagamento);
    }

    /**
     * Buscar pagamento por agendamento
     *
     * @param int $agendamento_id ID do agendamento
     * @return object|null Dados do pagamento
     */
    public function get_by_agendamento($agendamento_id) {
        return $this->db
            ->where('agendamento_id', $agendamento_id)
            ->where('tipo', 'agendamento')
            ->order_by('criado_em', 'DESC')
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    /**
     * Confirmar pagamento de agendamento
     *
     * @param int $agendamento_id ID do agendamento
     * @return bool Sucesso da operação
     */
    public function confirmar_agendamento($agendamento_id) {
        // Atualizar pagamento
        $this->db
            ->where('agendamento_id', $agendamento_id)
            ->where('tipo', 'agendamento')
            ->update($this->table, [
                'status' => 'approved',
                'atualizado_em' => date('Y-m-d H:i:s')
            ]);

        // Atualizar agendamento
        $this->db
            ->where('id', $agendamento_id)
            ->update('agendamentos', [
                'pagamento_status' => 'pago',
                'status' => 'confirmado',
                'forma_pagamento' => 'pix'
            ]);

        return true;
    }
}
