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
     * Reseta automaticamente se a última interação ultrapassou o timeout configurado
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
            // Criar nova conversa
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
        } else {
            // Buscar timeout configurado do estabelecimento (padrão: 30 minutos)
            $this->load->model('Estabelecimento_model');
            $estabelecimento = $this->Estabelecimento_model->get_by_id($estabelecimento_id);
            $timeout_minutos = $estabelecimento->bot_timeout_minutos ?? 30;

            // Verificar timeout de sessão
            $ultima_interacao = strtotime($conversa->ultima_interacao);
            $agora = time();
            $diferenca_minutos = ($agora - $ultima_interacao) / 60;

            if ($diferenca_minutos > $timeout_minutos) {
                // Sessão expirada - resetar para menu
                log_message('debug', "Bot: Sessão expirada para {$numero} (última interação há " . round($diferenca_minutos, 1) . " minutos, timeout: {$timeout_minutos} min)");
                $this->resetar($conversa->id);

                // Recarregar conversa resetada
                $conversa = $this->db
                    ->where('id', $conversa->id)
                    ->get($this->table)
                    ->row();
            } else {
                // Atualizar última interação
                $this->db
                    ->where('id', $conversa->id)
                    ->update($this->table, ['ultima_interacao' => date('Y-m-d H:i:s')]);

                $conversa->ultima_interacao = date('Y-m-d H:i:s');
            }
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
     * Encerra a sessão do bot
     * Marca a conversa como encerrada e limpa dados temporários
     *
     * @param int $conversa_id
     * @return bool
     */
    public function encerrar($conversa_id)
    {
        log_message('debug', "Bot: Encerrando sessão {$conversa_id}");

        return $this->db
            ->where('id', $conversa_id)
            ->update($this->table, [
                'estado' => 'encerrada',
                'dados_temporarios' => json_encode([]),
                'encerrada' => 1,
                'data_encerramento' => date('Y-m-d H:i:s'),
                'ultima_interacao' => date('Y-m-d H:i:s')
            ]);
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
     * Remove conversas encerradas ou inativas
     *
     * @return int Número de registros removidos
     */
    public function limpar_antigas()
    {
        // Remover conversas encerradas há mais de 7 dias
        $this->db
            ->where('encerrada', 1)
            ->where('data_encerramento <', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->delete($this->table);

        $encerradas = $this->db->affected_rows();

        // Remover conversas inativas (não encerradas) há mais de 24 horas
        $this->db
            ->where('encerrada', 0)
            ->where('ultima_interacao <', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->delete($this->table);

        $inativas = $this->db->affected_rows();

        log_message('debug', "Bot: Limpeza - {$encerradas} encerradas, {$inativas} inativas removidas");

        return $encerradas + $inativas;
    }

    /**
     * Limpar conversa do usuário (Delete)
     *
     * @param string $numero
     * @param int $estabelecimento_id
     * @return bool
     */
    public function limpar($numero, $estabelecimento_id) {
        return $this->db
            ->where('numero_whatsapp', $numero)
            ->where('estabelecimento_id', $estabelecimento_id)
            ->delete($this->table);
    }

    /**
     * Criar ou atualizar conversa
     *
     * @param string $numero
     * @param int $estabelecimento_id
     * @param string $estado
     * @param string|array $dados
     * @return bool
     */
    public function criar_ou_atualizar($numero, $estabelecimento_id, $estado, $dados) {
        $conversa = $this->get_ou_criar($estabelecimento_id, $numero);

        $dados_array = is_string($dados) ? (json_decode($dados, true) ?: []) : $dados;

        return $this->atualizar_estado($conversa->id, $estado, $dados_array);
    }
}
