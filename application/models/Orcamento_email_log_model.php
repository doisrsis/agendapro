<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Orcamento_email_log_model
 * Registro de histórico dos e-mails emitidos para um orçamento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 15/11/2025 20:30
 */
class Orcamento_email_log_model extends CI_Model {

    protected $table = 'orcamento_email_logs';

    /**
     * Registrar novo log de e-mail
     *
     * @param array $data
     * @return int|false
     */
    public function registrar(array $data) {
        if (empty($data['orcamento_id']) || empty($data['destinatario']) || empty($data['assunto'])) {
            log_message('error', 'Dados obrigatórios ausentes ao registrar e-mail do orçamento.');
            return false;
        }

        $payload = [
            'orcamento_id' => (int) $data['orcamento_id'],
            'tipo' => $data['tipo'] ?? 'generico',
            'destinatario' => $data['destinatario'],
            'assunto' => $data['assunto'],
            'status' => in_array($data['status'] ?? 'sucesso', ['sucesso', 'erro'], true) ? $data['status'] : 'sucesso',
            'preview' => $this->limitar_texto($data['preview'] ?? null, 500),
            'corpo' => $data['corpo'] ?? null,
            'erro' => $this->limitar_texto($data['erro'] ?? null, 2000),
            'criado_em' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $payload);
        return $this->db->insert_id();
    }

    /**
     * Listar logs de e-mail de um orçamento
     *
     * @param int $orcamento_id
     * @return array
     */
    public function listar_por_orcamento($orcamento_id) {
        $this->db->where('orcamento_id', (int) $orcamento_id);
        $this->db->order_by('criado_em', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Remover logs antigos de um orçamento (opcional)
     *
     * @param int $orcamento_id
     * @param int $dias
     * @return bool
     */
    public function limpar_antigos($orcamento_id, $dias = 180) {
        $limite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        $this->db->where('orcamento_id', (int) $orcamento_id);
        $this->db->where('criado_em <', $limite);
        return $this->db->delete($this->table);
    }

    /**
     * Limitar tamanho de texto mantendo caracteres multibyte
     */
    private function limitar_texto($texto, $limite) {
        if ($texto === null) {
            return null;
        }

        $texto = trim(strip_tags($texto));
        if ($texto === '') {
            return null;
        }

        if (function_exists('mb_substr')) {
            return mb_substr($texto, 0, $limite);
        }

        return substr($texto, 0, $limite);
    }
}
