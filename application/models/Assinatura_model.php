<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Assinatura_model
 * Descrição: Gerenciamento de assinaturas dos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 09/12/2024
 */
class Assinatura_model extends CI_Model {

    protected $table = 'assinaturas';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Buscar assinatura por ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id) {
        return $this->db
            ->select('a.*, p.nome as plano_nome, p.valor_mensal, e.nome as estabelecimento_nome')
            ->from($this->table . ' a')
            ->join('planos p', 'a.plano_id = p.id', 'left')
            ->join('estabelecimentos e', 'a.estabelecimento_id = e.id', 'left')
            ->where('a.id', $id)
            ->get()
            ->row();
    }

    /**
     * Buscar assinatura ativa de um estabelecimento
     *
     * @param int $estabelecimento_id
     * @return object|null
     */
    public function get_ativa($estabelecimento_id) {
        return $this->db
            ->select('a.*, p.nome as plano_nome, p.valor_mensal, p.max_profissionais, p.max_agendamentos_mes, p.recursos')
            ->from($this->table . ' a')
            ->join('planos p', 'a.plano_id = p.id', 'left')
            ->where('a.estabelecimento_id', $estabelecimento_id)
            ->where_in('a.status', ['ativa', 'trial'])
            ->order_by('a.id', 'DESC')
            ->limit(1)
            ->get()
            ->row();
    }

    /**
     * Alias para get_ativa() - manter consistência
     */
    public function get_ativa_por_estabelecimento($estabelecimento_id) {
        return $this->get_ativa($estabelecimento_id);
    }

    /**
     * Listar todas as assinaturas
     *
     * @param string $status Filtrar por status (opcional)
     * @return array
     */
    public function get_all($status = null) {
        $this->db
            ->select('a.*, p.nome as plano_nome, e.nome as estabelecimento_nome')
            ->from($this->table . ' a')
            ->join('planos p', 'a.plano_id = p.id', 'left')
            ->join('estabelecimentos e', 'a.estabelecimento_id = e.id', 'left');

        if ($status) {
            $this->db->where('a.status', $status);
        }

        return $this->db
            ->order_by('a.id', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Listar assinaturas de um estabelecimento
     *
     * @param int $estabelecimento_id
     * @return array
     */
    public function get_by_estabelecimento($estabelecimento_id) {
        return $this->db
            ->select('a.*, p.nome as plano_nome')
            ->from($this->table . ' a')
            ->join('planos p', 'a.plano_id = p.id', 'left')
            ->where('a.estabelecimento_id', $estabelecimento_id)
            ->order_by('a.id', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Criar nova assinatura
     *
     * @param array $dados
     * @return int|false
     */
    public function criar($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Criar assinatura trial
     *
     * @param int $estabelecimento_id
     * @param int $plano_id
     * @param int $dias_trial
     * @return int|false
     */
    public function criar_trial($estabelecimento_id, $plano_id, $dias_trial = 7) {
        $dados = [
            'estabelecimento_id' => $estabelecimento_id,
            'plano_id' => $plano_id,
            'data_inicio' => date('Y-m-d'),
            'data_fim' => date('Y-m-d', strtotime("+{$dias_trial} days")),
            'status' => 'trial',
            'valor_pago' => 0.00,
            'auto_renovar' => 1
        ];

        return $this->criar($dados);
    }

    /**
     * Atualizar assinatura
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $dados);
    }

    /**
     * Ativar assinatura (converter trial em ativa)
     *
     * @param int $id
     * @param string $mercadopago_subscription_id
     * @param float $valor_pago
     * @return bool
     */
    public function ativar($id, $mercadopago_subscription_id = null, $valor_pago = null) {
        $dados = [
            'status' => 'ativa',
            'data_fim' => date('Y-m-d', strtotime('+30 days'))
        ];

        if ($mercadopago_subscription_id) {
            $dados['mercadopago_subscription_id'] = $mercadopago_subscription_id;
        }

        if ($valor_pago) {
            $dados['valor_pago'] = $valor_pago;
        }

        return $this->atualizar($id, $dados);
    }

    /**
     * Renovar assinatura
     *
     * @param int $id
     * @param float $valor_pago
     * @return bool
     */
    public function renovar($id, $valor_pago = null) {
        $assinatura = $this->get($id);

        if (!$assinatura) {
            return false;
        }

        $dados = [
            'data_fim' => date('Y-m-d', strtotime($assinatura->data_fim . ' +30 days')),
            'status' => 'ativa'
        ];

        if ($valor_pago) {
            $dados['valor_pago'] = $valor_pago;
        }

        return $this->atualizar($id, $dados);
    }

    /**
     * Cancelar assinatura
     *
     * @param int $id
     * @param string $motivo
     * @return bool
     */
    public function cancelar($id, $motivo = null) {
        $dados = [
            'status' => 'cancelada',
            'cancelada_em' => date('Y-m-d H:i:s'),
            'auto_renovar' => 0
        ];

        if ($motivo) {
            $dados['motivo_cancelamento'] = $motivo;
        }

        return $this->atualizar($id, $dados);
    }

    /**
     * Suspender assinatura (por falta de pagamento)
     *
     * @param int $id
     * @return bool
     */
    public function suspender($id) {
        return $this->atualizar($id, ['status' => 'suspensa']);
    }

    /**
     * Verificar assinaturas vencidas e atualizar status
     *
     * @return int Número de assinaturas atualizadas
     */
    public function verificar_vencidas() {
        $assinaturas_vencidas = $this->db
            ->where('data_fim <', date('Y-m-d'))
            ->where_in('status', ['ativa', 'trial'])
            ->get($this->table)
            ->result();

        $total_atualizadas = 0;

        foreach ($assinaturas_vencidas as $assinatura) {
            if ($this->atualizar($assinatura->id, ['status' => 'vencida'])) {
                $total_atualizadas++;

                // Suspender estabelecimento
                $this->db->where('id', $assinatura->estabelecimento_id)
                    ->update('estabelecimentos', ['status' => 'suspenso']);
            }
        }

        return $total_atualizadas;
    }

    /**
     * Contar assinaturas por plano
     *
     * @param int $plano_id
     * @return int
     */
    public function count_by_plano($plano_id) {
        return $this->db
            ->where('plano_id', $plano_id)
            ->count_all_results($this->table);
    }

    /**
     * Verificar se assinatura está ativa
     *
     * @param int $estabelecimento_id
     * @return bool
     */
    public function esta_ativa($estabelecimento_id) {
        $assinatura = $this->get_ativa($estabelecimento_id);

        if (!$assinatura) {
            return false;
        }

        return in_array($assinatura->status, ['ativa', 'trial']) &&
               strtotime($assinatura->data_fim) >= strtotime(date('Y-m-d'));
    }

    /**
     * Dias restantes da assinatura
     *
     * @param int $estabelecimento_id
     * @return int|false
     */
    public function dias_restantes($estabelecimento_id) {
        $assinatura = $this->get_ativa($estabelecimento_id);

        if (!$assinatura) {
            return false;
        }

        $hoje = new DateTime();
        $data_fim = new DateTime($assinatura->data_fim);
        $diff = $hoje->diff($data_fim);

        return $diff->days;
    }

    /**
     * Fazer upgrade de plano
     *
     * @param int $estabelecimento_id
     * @param int $novo_plano_id
     * @param string $mercadopago_subscription_id
     * @return int|false ID da nova assinatura
     */
    public function fazer_upgrade($estabelecimento_id, $novo_plano_id, $mercadopago_subscription_id = null) {
        // Cancelar assinatura atual
        $assinatura_atual = $this->get_ativa($estabelecimento_id);

        if ($assinatura_atual) {
            $this->cancelar($assinatura_atual->id, 'Upgrade de plano');
        }

        // Criar nova assinatura
        $dados = [
            'estabelecimento_id' => $estabelecimento_id,
            'plano_id' => $novo_plano_id,
            'data_inicio' => date('Y-m-d'),
            'data_fim' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'ativa',
            'mercadopago_subscription_id' => $mercadopago_subscription_id,
            'auto_renovar' => 1
        ];

        return $this->criar($dados);
    }
}
