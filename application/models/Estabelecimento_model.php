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
            // Converter string vazia para NULL para evitar erro de duplicidade em unique key
            'cnpj_cpf' => !empty($data['cnpj_cpf']) ? $data['cnpj_cpf'] : null,
            'endereco' => $data['endereco'] ?? null,
            'cep' => $data['cep'] ?? null,
            'cidade' => $data['cidade'] ?? null,
            'estado' => $data['estado'] ?? null,

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
        if (isset($data['cnpj_cpf'])) $update_data['cnpj_cpf'] = !empty($data['cnpj_cpf']) ? $data['cnpj_cpf'] : null;
        if (isset($data['endereco'])) $update_data['endereco'] = $data['endereco'];
        if (isset($data['cep'])) $update_data['cep'] = $data['cep'];
        if (isset($data['cidade'])) $update_data['cidade'] = $data['cidade'];
        if (isset($data['estado'])) $update_data['estado'] = $data['estado'];

        if (isset($data['whatsapp'])) $update_data['whatsapp'] = $data['whatsapp'];
        if (isset($data['email'])) $update_data['email'] = $data['email'];
        if (isset($data['logo'])) $update_data['logo'] = $data['logo'];
        if (isset($data['plano'])) $update_data['plano'] = $data['plano'];
        if (isset($data['plano_vencimento'])) $update_data['plano_vencimento'] = $data['plano_vencimento'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];
        if (isset($data['tempo_minimo_agendamento'])) $update_data['tempo_minimo_agendamento'] = $data['tempo_minimo_agendamento'];
        if (isset($data['usar_intervalo_fixo'])) $update_data['usar_intervalo_fixo'] = $data['usar_intervalo_fixo'];
        if (isset($data['intervalo_agendamento'])) $update_data['intervalo_agendamento'] = $data['intervalo_agendamento'];
        if (isset($data['dias_antecedencia_agenda'])) $update_data['dias_antecedencia_agenda'] = $data['dias_antecedencia_agenda'];

        // Campos de pagamento de agendamentos
        if (isset($data['agendamento_requer_pagamento'])) $update_data['agendamento_requer_pagamento'] = $data['agendamento_requer_pagamento'];
        if (isset($data['agendamento_taxa_fixa'])) $update_data['agendamento_taxa_fixa'] = $data['agendamento_taxa_fixa'];
        if (isset($data['agendamento_tempo_expiracao_pix'])) $update_data['agendamento_tempo_expiracao_pix'] = $data['agendamento_tempo_expiracao_pix'];
        if (isset($data['agendamento_tempo_adicional_pix'])) $update_data['agendamento_tempo_adicional_pix'] = $data['agendamento_tempo_adicional_pix'];

        // Campos de Mercado Pago
        if (isset($data['mp_access_token_test'])) $update_data['mp_access_token_test'] = $data['mp_access_token_test'];
        if (isset($data['mp_public_key_test'])) $update_data['mp_public_key_test'] = $data['mp_public_key_test'];
        if (isset($data['mp_access_token_prod'])) $update_data['mp_access_token_prod'] = $data['mp_access_token_prod'];
        if (isset($data['mp_public_key_prod'])) $update_data['mp_public_key_prod'] = $data['mp_public_key_prod'];
        if (isset($data['mp_sandbox'])) $update_data['mp_sandbox'] = $data['mp_sandbox'];

        // Campos de PIX Manual
        if (isset($data['pagamento_tipo'])) $update_data['pagamento_tipo'] = $data['pagamento_tipo'];
        if (isset($data['pix_chave'])) $update_data['pix_chave'] = $data['pix_chave'];
        if (isset($data['pix_tipo_chave'])) $update_data['pix_tipo_chave'] = $data['pix_tipo_chave'];
        if (isset($data['pix_nome_recebedor'])) $update_data['pix_nome_recebedor'] = $data['pix_nome_recebedor'];
        if (isset($data['pix_cidade'])) $update_data['pix_cidade'] = $data['pix_cidade'];

        // Campos de WhatsApp - Tipo de API
        if (isset($data['whatsapp_api_tipo'])) $update_data['whatsapp_api_tipo'] = $data['whatsapp_api_tipo'];

        // Campos de WhatsApp - Evolution API
        if (isset($data['whatsapp_api_url'])) $update_data['whatsapp_api_url'] = $data['whatsapp_api_url'];
        if (isset($data['whatsapp_api_token'])) $update_data['whatsapp_api_token'] = $data['whatsapp_api_token'];
        if (isset($data['whatsapp_numero'])) $update_data['whatsapp_numero'] = $data['whatsapp_numero'];
        if (isset($data['whatsapp_ativo'])) $update_data['whatsapp_ativo'] = $data['whatsapp_ativo'];

        // Campos de WhatsApp - WAHA API
        if (isset($data['waha_api_url'])) $update_data['waha_api_url'] = $data['waha_api_url'];
        if (isset($data['waha_api_key'])) $update_data['waha_api_key'] = $data['waha_api_key'];
        if (isset($data['waha_session_name'])) $update_data['waha_session_name'] = $data['waha_session_name'];
        if (isset($data['waha_webhook_url'])) $update_data['waha_webhook_url'] = $data['waha_webhook_url'];
        if (isset($data['waha_status'])) $update_data['waha_status'] = $data['waha_status'];
        if (isset($data['waha_numero_conectado'])) $update_data['waha_numero_conectado'] = $data['waha_numero_conectado'];
        if (isset($data['waha_ativo'])) $update_data['waha_ativo'] = $data['waha_ativo'];
        if (isset($data['waha_bot_ativo'])) $update_data['waha_bot_ativo'] = $data['waha_bot_ativo'];
        if (isset($data['bot_timeout_minutos'])) $update_data['bot_timeout_minutos'] = $data['bot_timeout_minutos'];
        if (isset($data['bot_modo_gatilho'])) $update_data['bot_modo_gatilho'] = $data['bot_modo_gatilho'];
        if (isset($data['bot_palavras_chave'])) $update_data['bot_palavras_chave'] = $data['bot_palavras_chave'];

        // Campos de reagendamento
        if (isset($data['permite_reagendamento'])) $update_data['permite_reagendamento'] = $data['permite_reagendamento'];
        if (isset($data['limite_reagendamentos'])) $update_data['limite_reagendamentos'] = $data['limite_reagendamentos'];

        // Campos de confirmação e lembretes
        if (isset($data['solicitar_confirmacao'])) $update_data['solicitar_confirmacao'] = $data['solicitar_confirmacao'];
        if (isset($data['confirmacao_horas_antes'])) $update_data['confirmacao_horas_antes'] = $data['confirmacao_horas_antes'];
        if (isset($data['confirmacao_dia_anterior'])) $update_data['confirmacao_dia_anterior'] = $data['confirmacao_dia_anterior'];
        if (isset($data['confirmacao_horario_dia_anterior'])) $update_data['confirmacao_horario_dia_anterior'] = $data['confirmacao_horario_dia_anterior'];
        // Tentativas múltiplas de confirmação
        if (isset($data['confirmacao_max_tentativas'])) $update_data['confirmacao_max_tentativas'] = $data['confirmacao_max_tentativas'];
        if (isset($data['confirmacao_intervalo_tentativas_minutos'])) $update_data['confirmacao_intervalo_tentativas_minutos'] = $data['confirmacao_intervalo_tentativas_minutos'];
        if (isset($data['confirmacao_cancelar_automatico'])) $update_data['confirmacao_cancelar_automatico'] = $data['confirmacao_cancelar_automatico'];
        // Lembretes
        if (isset($data['enviar_lembrete_pre_atendimento'])) $update_data['enviar_lembrete_pre_atendimento'] = $data['enviar_lembrete_pre_atendimento'];
        if (isset($data['lembrete_minutos_antes'])) $update_data['lembrete_minutos_antes'] = $data['lembrete_minutos_antes'];
        if (isset($data['lembrete_antecedencia_chegada'])) $update_data['lembrete_antecedencia_chegada'] = $data['lembrete_antecedencia_chegada'];
        // Cancelamento automático (sistema antigo)
        if (isset($data['cancelar_nao_confirmados'])) $update_data['cancelar_nao_confirmados'] = $data['cancelar_nao_confirmados'];
        if (isset($data['cancelar_nao_confirmados_horas'])) $update_data['cancelar_nao_confirmados_horas'] = $data['cancelar_nao_confirmados_horas'];

        // Campos de notificações para profissionais
        if (isset($data['notif_prof_novo_agendamento'])) $update_data['notif_prof_novo_agendamento'] = $data['notif_prof_novo_agendamento'];
        if (isset($data['notif_prof_cancelamento'])) $update_data['notif_prof_cancelamento'] = $data['notif_prof_cancelamento'];
        if (isset($data['notif_prof_reagendamento'])) $update_data['notif_prof_reagendamento'] = $data['notif_prof_reagendamento'];
        if (isset($data['notif_prof_resumo_diario'])) $update_data['notif_prof_resumo_diario'] = $data['notif_prof_resumo_diario'];
        if (array_key_exists('notif_prof_resumo_manha', $data)) $update_data['notif_prof_resumo_manha'] = $data['notif_prof_resumo_manha'];
        if (array_key_exists('notif_prof_resumo_tarde', $data)) $update_data['notif_prof_resumo_tarde'] = $data['notif_prof_resumo_tarde'];

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

    // =========================================================================
    // ALIASES PARA COMPATIBILIDADE DE NOMENCLATURA
    // =========================================================================

    /**
     * Alias para get_by_id()
     */
    public function get($id) {
        return $this->get_by_id($id);
    }

    /**
     * Alias para create()
     */
    public function criar($dados) {
        return $this->create($dados);
    }

    /**
     * Alias para update()
     */
    public function atualizar($id, $dados) {
        return $this->update($id, $dados);
    }

    /**
     * Alias para delete()
     */
    public function excluir($id) {
        return $this->delete($id);
    }
}
