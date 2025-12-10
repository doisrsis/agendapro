<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Plano_model
 * Descrição: Gerenciamento de planos de assinatura
 *
 * @author Rafael Dias - doisr.com.br
 * @date 09/12/2024
 */
class Plano_model extends CI_Model {

    protected $table = 'planos';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Buscar plano por ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id) {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    /**
     * Alias para get() - manter compatibilidade
     */
    public function get_by_id($id) {
        return $this->get($id);
    }

    /**
     * Buscar plano por slug
     *
     * @param string $slug
     * @return object|null
     */
    public function get_by_slug($slug) {
        return $this->db
            ->where('slug', $slug)
            ->where('ativo', 1)
            ->get($this->table)
            ->row();
    }

    /**
     * Listar todos os planos ativos
     *
     * @param bool $apenas_ativos
     * @return array
     */
    public function get_all($apenas_ativos = true) {
        if ($apenas_ativos) {
            $this->db->where('ativo', 1);
        }

        return $this->db
            ->order_by('ordem', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * Listar planos para exibição pública (landing page)
     *
     * @return array
     */
    public function get_planos_publicos() {
        return $this->db
            ->select('id, nome, slug, descricao, valor_mensal, max_profissionais, max_agendamentos_mes, recursos, trial_dias')
            ->where('ativo', 1)
            ->order_by('ordem', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * Criar novo plano
     *
     * @param array $dados
     * @return int|false
     */
    public function criar($dados) {
        // Gerar slug se não fornecido
        if (!isset($dados['slug']) && isset($dados['nome'])) {
            $dados['slug'] = $this->gerar_slug($dados['nome']);
        }

        // Converter recursos para JSON se for array
        if (isset($dados['recursos']) && is_array($dados['recursos'])) {
            $dados['recursos'] = json_encode($dados['recursos']);
        }

        $dados['criado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar plano
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        // Converter recursos para JSON se for array
        if (isset($dados['recursos']) && is_array($dados['recursos'])) {
            $dados['recursos'] = json_encode($dados['recursos']);
        }

        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)->update($this->table, $dados);
    }

    /**
     * Ativar/Desativar plano
     *
     * @param int $id
     * @param bool $ativo
     * @return bool
     */
    public function toggle_ativo($id, $ativo = true) {
        return $this->db->where('id', $id)->update($this->table, ['ativo' => $ativo ? 1 : 0]);
    }

    /**
     * Excluir plano (apenas se não houver assinaturas)
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verificar se há assinaturas vinculadas
        $this->load->model('Assinatura_model');
        $tem_assinaturas = $this->Assinatura_model->count_by_plano($id) > 0;

        if ($tem_assinaturas) {
            return false; // Não pode excluir plano com assinaturas
        }

        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Verificar se estabelecimento pode criar mais profissionais
     *
     * @param int $plano_id
     * @param int $total_profissionais_atual
     * @return bool
     */
    public function pode_criar_profissional($plano_id, $total_profissionais_atual) {
        $plano = $this->get($plano_id);

        if (!$plano) {
            return false;
        }

        // 999 = ilimitado
        if ($plano->max_profissionais >= 999) {
            return true;
        }

        return $total_profissionais_atual < $plano->max_profissionais;
    }

    /**
     * Verificar se estabelecimento pode criar mais agendamentos
     *
     * @param int $plano_id
     * @param int $total_agendamentos_mes
     * @return bool
     */
    public function pode_criar_agendamento($plano_id, $total_agendamentos_mes) {
        $plano = $this->get($plano_id);

        if (!$plano) {
            return false;
        }

        // 999999 = ilimitado
        if ($plano->max_agendamentos_mes >= 999999) {
            return true;
        }

        return $total_agendamentos_mes < $plano->max_agendamentos_mes;
    }

    /**
     * Verificar se plano tem recurso específico
     *
     * @param int $plano_id
     * @param string $recurso
     * @return bool
     */
    public function tem_recurso($plano_id, $recurso) {
        $plano = $this->get($plano_id);

        if (!$plano || !$plano->recursos) {
            return false;
        }

        $recursos = json_decode($plano->recursos, true);

        return isset($recursos[$recurso]) && $recursos[$recurso] === true;
    }

    /**
     * Gerar slug único
     *
     * @param string $nome
     * @return string
     */
    private function gerar_slug($nome) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nome)));

        // Verificar se slug já existe
        $existe = $this->db->where('slug', $slug)->count_all_results($this->table) > 0;

        if ($existe) {
            $slug .= '-' . time();
        }

        return $slug;
    }

    /**
     * Comparar planos (para upgrade/downgrade)
     *
     * @param int $plano_atual_id
     * @param int $plano_novo_id
     * @return string 'upgrade', 'downgrade' ou 'igual'
     */
    public function comparar_planos($plano_atual_id, $plano_novo_id) {
        if ($plano_atual_id == $plano_novo_id) {
            return 'igual';
        }

        $plano_atual = $this->get($plano_atual_id);
        $plano_novo = $this->get($plano_novo_id);

        if (!$plano_atual || !$plano_novo) {
            return 'igual';
        }

        // Comparar por valor mensal
        if ($plano_novo->valor_mensal > $plano_atual->valor_mensal) {
            return 'upgrade';
        } else {
            return 'downgrade';
        }
    }

    // ========================================================================
    // MÉTODOS DE INTEGRAÇÃO COM MERCADO PAGO
    // ========================================================================

    /**
     * Criar plano local + Mercado Pago
     *
     * @param array $dados
     * @param bool $criar_no_mp
     * @return int|false
     */
    public function criar_com_mp($dados, $criar_no_mp = true) {
        // Criar plano localmente
        $plano_id = $this->criar($dados);

        if (!$plano_id) {
            return false;
        }

        // Criar no Mercado Pago se solicitado
        if ($criar_no_mp) {
            $this->load->library('mercadopago_lib');

            $mp_result = $this->mercadopago_lib->criar_plano([
                'nome' => $dados['nome'],
                'valor_mensal' => $dados['valor_mensal']
            ]);

            if ($mp_result['success']) {
                // Salvar ID do MP
                $this->atualizar($plano_id, [
                    'mercadopago_plan_id' => $mp_result['data']['id']
                ]);
            } else {
                log_message('error', 'Erro ao criar plano no MP: ' . json_encode($mp_result));
            }
        }

        return $plano_id;
    }

    /**
     * Atualizar plano local + Mercado Pago
     *
     * @param int $id
     * @param array $dados
     * @param bool $atualizar_no_mp
     * @return bool
     */
    public function atualizar_com_mp($id, $dados, $atualizar_no_mp = true) {
        $plano = $this->get($id);

        if (!$plano) {
            return false;
        }

        // Atualizar localmente
        $success = $this->atualizar($id, $dados);

        if (!$success) {
            return false;
        }

        // Atualizar no Mercado Pago se tiver ID
        if ($atualizar_no_mp && $plano->mercadopago_plan_id) {
            $this->load->library('mercadopago_lib');

            $mp_result = $this->mercadopago_lib->atualizar_plano(
                $plano->mercadopago_plan_id,
                ['nome' => $dados['nome']]
            );

            if (!$mp_result['success']) {
                log_message('error', 'Erro ao atualizar plano no MP: ' . json_encode($mp_result));
            }
        }

        return true;
    }

    /**
     * Desativar plano local + Mercado Pago
     *
     * @param int $id
     * @param bool $desativar_no_mp
     * @return bool
     */
    public function desativar_com_mp($id, $desativar_no_mp = true) {
        $plano = $this->get($id);

        if (!$plano) {
            return false;
        }

        // Desativar localmente
        $success = $this->toggle_ativo($id, false);

        if (!$success) {
            return false;
        }

        // Desativar no Mercado Pago se tiver ID
        if ($desativar_no_mp && $plano->mercadopago_plan_id) {
            $this->load->library('mercadopago_lib');

            $mp_result = $this->mercadopago_lib->desativar_plano($plano->mercadopago_plan_id);

            if (!$mp_result['success']) {
                log_message('error', 'Erro ao desativar plano no MP: ' . json_encode($mp_result));
            }
        }

        return true;
    }

    /**
     * Sincronizar plano com Mercado Pago
     *
     * @param int $id
     * @return bool
     */
    public function sincronizar_com_mp($id) {
        $plano = $this->get($id);

        if (!$plano) {
            return false;
        }

        $this->load->library('mercadopago_lib');

        // Se já tem ID do MP, buscar dados
        if ($plano->mercadopago_plan_id) {
            $mp_result = $this->mercadopago_lib->buscar_plano($plano->mercadopago_plan_id);

            if ($mp_result['success']) {
                return true;
            }
        }

        // Se não tem ID ou não encontrou, criar novo
        $mp_result = $this->mercadopago_lib->criar_plano([
            'nome' => $plano->nome,
            'valor_mensal' => $plano->valor_mensal
        ]);

        if ($mp_result['success']) {
            $this->atualizar($id, [
                'mercadopago_plan_id' => $mp_result['data']['id']
            ]);
            return true;
        }

        return false;
    }

    /**
     * Buscar plano por ID do Mercado Pago
     *
     * @param string $mp_plan_id
     * @return object|null
     */
    public function get_by_mp_id($mp_plan_id) {
        return $this->db
            ->where('mercadopago_plan_id', $mp_plan_id)
            ->get($this->table)
            ->row();
    }
}
