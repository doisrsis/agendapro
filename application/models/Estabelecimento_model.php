<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Estabelecimentos
 *
 * Gerencia os estabelecimentos (multi-tenant) do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Estabelecimento_model extends CI_Model {

    protected $table = 'estabelecimentos';

    /**
     * Buscar todos os estabelecimentos
     */
    public function get_all($filtros = []) {
        $this->db->select('*');
        $this->db->from($this->table);

        // Filtros
        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }

        if (!empty($filtros['plano'])) {
            $this->db->where('plano', $filtros['plano']);
        }

        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('nome', $filtros['busca']);
            $this->db->or_like('cnpj_cpf', $filtros['busca']);
            $this->db->or_like('email', $filtros['busca']);
            $this->db->group_end();
        }

        $this->db->order_by('nome', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Buscar estabelecimento por ID
     */
    public function get_by_id($id) {
        $query = $this->db->get_where($this->table, ['id' => $id]);
        return $query->row();
    }

    /**
     * Criar novo estabelecimento
     */
    public function create($data) {
        // Validar dados obrigatórios
        if (empty($data['nome'])) {
            return false;
        }

        // Preparar dados
        $insert_data = [
            'nome' => $data['nome'],
            'cnpj_cpf' => $data['cnpj_cpf'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'cep' => $data['cep'] ?? null,
            'cidade' => $data['cidade'] ?? null,
            'estado' => $data['estado'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'email' => $data['email'] ?? null,
            'logo' => $data['logo'] ?? null,
            'plano' => $data['plano'] ?? 'trimestral',
            'plano_vencimento' => $data['plano_vencimento'] ?? null,
            'status' => $data['status'] ?? 'ativo',
            'tempo_minimo_agendamento' => $data['tempo_minimo_agendamento'] ?? 60,
        ];

        if ($this->db->insert($this->table, $insert_data)) {
            $estabelecimento_id = $this->db->insert_id();

            // Criar templates padrão de notificações
            $this->criar_templates_notificacoes($estabelecimento_id);

            return $estabelecimento_id;
        }

        return false;
    }

    /**
     * Atualizar estabelecimento
     */
    public function update($id, $data) {
        // Preparar dados
        $update_data = [];

        if (isset($data['nome'])) $update_data['nome'] = $data['nome'];
        if (isset($data['cnpj_cpf'])) $update_data['cnpj_cpf'] = $data['cnpj_cpf'];
        if (isset($data['endereco'])) $update_data['endereco'] = $data['endereco'];
        if (isset($data['cep'])) $update_data['cep'] = $data['cep'];
        if (isset($data['cidade'])) $update_data['cidade'] = $data['cidade'];
        if (isset($data['estado'])) $update_data['estado'] = $data['estado'];
        if (isset($data['telefone'])) $update_data['telefone'] = $data['telefone'];
        if (isset($data['whatsapp'])) $update_data['whatsapp'] = $data['whatsapp'];
        if (isset($data['email'])) $update_data['email'] = $data['email'];
        if (isset($data['logo'])) $update_data['logo'] = $data['logo'];
        if (isset($data['plano'])) $update_data['plano'] = $data['plano'];
        if (isset($data['plano_vencimento'])) $update_data['plano_vencimento'] = $data['plano_vencimento'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];
        if (isset($data['tempo_minimo_agendamento'])) $update_data['tempo_minimo_agendamento'] = $data['tempo_minimo_agendamento'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Deletar estabelecimento
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Contar estabelecimentos
     */
    public function count($filtros = []) {
        $this->db->from($this->table);

        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }

        if (!empty($filtros['plano'])) {
            $this->db->where('plano', $filtros['plano']);
        }

        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('nome', $filtros['busca']);
            $this->db->or_like('cnpj_cpf', $filtros['busca']);
            $this->db->or_like('email', $filtros['busca']);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Criar templates padrão de notificações para novo estabelecimento
     */
    private function criar_templates_notificacoes($estabelecimento_id) {
        $templates = [
            [
                'tipo' => 'confirmacao',
                'template' => 'Olá {cliente}! ✅ Seu agendamento foi confirmado!\n\n📅 Data: {data}\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}\n\nNos vemos em breve!'
            ],
            [
                'tipo' => 'cancelamento',
                'template' => 'Olá {cliente}. ❌ Seu agendamento foi cancelado.\n\n📅 Data: {data}\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n\nQualquer dúvida, entre em contato!'
            ],
            [
                'tipo' => 'reagendamento',
                'template' => 'Olá {cliente}! 🔄 Seu agendamento foi reagendado.\n\n📅 Nova Data: {data}\n🕐 Novo Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}'
            ],
            [
                'tipo' => 'lembrete_1dia',
                'template' => 'Olá {cliente}! 🔔 Lembrete: você tem um agendamento amanhã!\n\n📅 Data: {data}\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}\n\nTe esperamos!'
            ],
            [
                'tipo' => 'lembrete_1hora',
                'template' => 'Olá {cliente}! ⏰ Seu agendamento é daqui a 1 hora!\n\n🕐 Horário: {hora}\n💇 Serviço: {servico}\n👤 Profissional: {profissional}\n\nEstamos te esperando!'
            ],
            [
                'tipo' => 'pagamento',
                'template' => 'Olá {cliente}! 💰 Pagamento confirmado!\n\n✅ Valor: R$ {valor}\n📅 Agendamento: {data} às {hora}\n\nObrigado pela preferência!'
            ],
            [
                'tipo' => 'feedback',
                'template' => 'Olá {cliente}! 🌟 Como foi sua experiência?\n\nGostaríamos de saber sua opinião sobre o atendimento de {profissional}.\n\nAvalie aqui: {link}'
            ]
        ];

        foreach ($templates as $template) {
            $this->db->insert('notificacoes_config', [
                'estabelecimento_id' => $estabelecimento_id,
                'tipo' => $template['tipo'],
                'template' => $template['template'],
                'ativo' => 1
            ]);
        }
    }

    /**
     * Verificar se plano está vencido
     */
    public function verificar_plano_vencido($id) {
        $estabelecimento = $this->get_by_id($id);

        if (!$estabelecimento || !$estabelecimento->plano_vencimento) {
            return false;
        }

        $hoje = date('Y-m-d');
        return $estabelecimento->plano_vencimento < $hoje;
    }

    /**
     * Suspender estabelecimento por falta de pagamento
     */
    public function suspender($id) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['status' => 'suspenso']);
    }
}
