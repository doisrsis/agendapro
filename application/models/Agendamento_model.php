<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Agendamentos
 *
 * Gerencia os agendamentos do sistema com validação de disponibilidade
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Agendamento_model extends CI_Model {

    protected $table = 'agendamentos';

    public function __construct() {
        parent::__construct();
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
        $this->load->model('Cliente_model');
    }

    /**
     * Buscar todos os agendamentos
     */
    public function get_all($filtros = [], $limit = null, $offset = 0) {
        $this->db->select('a.*,
                          c.nome as cliente_nome, c.whatsapp as cliente_whatsapp,
                          p.nome as profissional_nome,
                          s.nome as servico_nome, s.preco as servico_preco,
                          e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' a');
        $this->db->join('clientes c', 'c.id = a.cliente_id', 'left');
        $this->db->join('profissionais p', 'p.id = a.profissional_id', 'left');
        $this->db->join('servicos s', 's.id = a.servico_id', 'left');
        $this->db->join('estabelecimentos e', 'e.id = a.estabelecimento_id', 'left');

        // Filtros
        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('a.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['profissional_id'])) {
            $this->db->where('a.profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['cliente_id'])) {
            $this->db->where('a.cliente_id', $filtros['cliente_id']);
        }

        if (!empty($filtros['status'])) {
            $this->db->where('a.status', $filtros['status']);
        }

        if (!empty($filtros['data'])) {
            $this->db->where('a.data', $filtros['data']);
        }

        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $this->db->where('a.data >=', $filtros['data_inicio']);
            $this->db->where('a.data <=', $filtros['data_fim']);
        }

        $this->db->order_by('a.data', 'DESC');
        $this->db->order_by('a.hora_inicio', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Buscar agendamento por ID
     */
    public function get_by_id($id) {
        $this->db->select('a.*,
                          c.nome as cliente_nome, c.whatsapp as cliente_whatsapp, c.email as cliente_email,
                          p.nome as profissional_nome, p.whatsapp as profissional_whatsapp,
                          s.nome as servico_nome, s.preco as servico_preco, s.duracao as servico_duracao,
                          e.nome as estabelecimento_nome');
        $this->db->from($this->table . ' a');
        $this->db->join('clientes c', 'c.id = a.cliente_id', 'left');
        $this->db->join('profissionais p', 'p.id = a.profissional_id', 'left');
        $this->db->join('servicos s', 's.id = a.servico_id', 'left');
        $this->db->join('estabelecimentos e', 'e.id = a.estabelecimento_id', 'left');
        $this->db->where('a.id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Criar novo agendamento
     */
    public function create($data) {
        // Validar dados obrigatórios
        if (empty($data['estabelecimento_id']) || empty($data['cliente_id']) ||
            empty($data['profissional_id']) || empty($data['servico_id']) ||
            empty($data['data']) || empty($data['hora_inicio'])) {
            return false;
        }

        // Buscar serviço para calcular hora_fim
        $servico = $this->Servico_model->get_by_id($data['servico_id']);
        if (!$servico) {
            return false;
        }

        // Calcular hora_fim baseado na duração do serviço
        $hora_inicio = new DateTime($data['hora_inicio']);
        $hora_fim = clone $hora_inicio;
        $hora_fim->add(new DateInterval('PT' . $servico->duracao . 'M'));

        // Verificar disponibilidade
        if (!$this->verificar_disponibilidade(
            $data['profissional_id'],
            $data['data'],
            $hora_inicio->format('H:i:s'),
            $hora_fim->format('H:i:s')
        )) {
            return false;
        }

        $insert_data = [
            'estabelecimento_id' => $data['estabelecimento_id'],
            'cliente_id' => $data['cliente_id'],
            'profissional_id' => $data['profissional_id'],
            'servico_id' => $data['servico_id'],
            'data' => $data['data'],
            'hora_inicio' => $hora_inicio->format('H:i:s'),
            'hora_fim' => $hora_fim->format('H:i:s'),
            'status' => $data['status'] ?? 'pendente',
            'observacoes' => $data['observacoes'] ?? null,
        ];

        if ($this->db->insert($this->table, $insert_data)) {
            $agendamento_id = $this->db->insert_id();

            // Incrementar contador de agendamentos do cliente
            $this->Cliente_model->incrementar_agendamentos($data['cliente_id']);

            return $agendamento_id;
        }

        return false;
    }

    /**
     * Atualizar agendamento
     */
    public function update($id, $data) {
        $update_data = [];

        // Se estiver alterando data/hora/profissional, validar disponibilidade
        if (isset($data['data']) || isset($data['hora_inicio']) || isset($data['profissional_id'])) {
            $agendamento_atual = $this->get_by_id($id);

            $profissional_id = $data['profissional_id'] ?? $agendamento_atual->profissional_id;
            $data_agendamento = $data['data'] ?? $agendamento_atual->data;
            $hora_inicio = $data['hora_inicio'] ?? $agendamento_atual->hora_inicio;

            // Calcular hora_fim
            if (isset($data['servico_id'])) {
                $servico = $this->Servico_model->get_by_id($data['servico_id']);
            } else {
                $servico = $this->Servico_model->get_by_id($agendamento_atual->servico_id);
            }

            $hora_inicio_dt = new DateTime($hora_inicio);
            $hora_fim_dt = clone $hora_inicio_dt;
            $hora_fim_dt->add(new DateInterval('PT' . $servico->duracao . 'M'));

            // Verificar disponibilidade (excluindo o próprio agendamento)
            if (!$this->verificar_disponibilidade(
                $profissional_id,
                $data_agendamento,
                $hora_inicio_dt->format('H:i:s'),
                $hora_fim_dt->format('H:i:s'),
                $id
            )) {
                return false;
            }

            $update_data['hora_fim'] = $hora_fim_dt->format('H:i:s');
        }

        if (isset($data['data'])) $update_data['data'] = $data['data'];
        if (isset($data['hora_inicio'])) $update_data['hora_inicio'] = $data['hora_inicio'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];
        if (isset($data['observacoes'])) $update_data['observacoes'] = $data['observacoes'];
        if (isset($data['cancelado_por'])) $update_data['cancelado_por'] = $data['cancelado_por'];
        if (isset($data['motivo_cancelamento'])) $update_data['motivo_cancelamento'] = $data['motivo_cancelamento'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Cancelar agendamento
     */
    public function cancelar($id, $cancelado_por, $motivo = null) {
        return $this->update($id, [
            'status' => 'cancelado',
            'cancelado_por' => $cancelado_por,
            'motivo_cancelamento' => $motivo
        ]);
    }

    /**
     * Finalizar agendamento
     */
    public function finalizar($id) {
        return $this->update($id, ['status' => 'finalizado']);
    }

    /**
     * Verificar disponibilidade do profissional
     */
    public function verificar_disponibilidade($profissional_id, $data, $hora_inicio, $hora_fim, $excluir_agendamento_id = null) {
        // 1. Verificar se há conflito com outros agendamentos
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data', $data);
        $this->db->where('status !=', 'cancelado');

        if ($excluir_agendamento_id) {
            $this->db->where('id !=', $excluir_agendamento_id);
        }

        // Verificar sobreposição de horários
        $this->db->group_start();
        $this->db->where('hora_inicio <', $hora_fim);
        $this->db->where('hora_fim >', $hora_inicio);
        $this->db->group_end();

        $conflitos = $this->db->get($this->table)->num_rows();

        if ($conflitos > 0) {
            return false;
        }

        // 2. Verificar bloqueios
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data', $data);
        $this->db->group_start();
        $this->db->where('dia_todo', 1);
        $this->db->or_group_start();
        $this->db->where('hora_inicio <', $hora_fim);
        $this->db->where('hora_fim >', $hora_inicio);
        $this->db->group_end();
        $this->db->group_end();

        $bloqueios = $this->db->get('bloqueios')->num_rows();

        if ($bloqueios > 0) {
            return false;
        }

        // 3. Verificar disponibilidade configurada
        $dia_semana = date('w', strtotime($data)); // 0=Domingo, 6=Sábado

        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('dia_semana', $dia_semana);
        $this->db->where('hora_inicio <=', $hora_inicio);
        $this->db->where('hora_fim >=', $hora_fim);

        $disponibilidade = $this->db->get('disponibilidade')->num_rows();

        if ($disponibilidade == 0) {
            return false;
        }

        return true;
    }

    /**
     * Buscar horários disponíveis para agendamento
     */
    public function get_horarios_disponiveis($profissional_id, $data, $duracao_minutos) {
        $dia_semana = date('w', strtotime($data));

        // Buscar disponibilidade do profissional no dia
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('dia_semana', $dia_semana);
        $disponibilidades = $this->db->get('disponibilidade')->result();

        if (empty($disponibilidades)) {
            return [];
        }

        $horarios_disponiveis = [];

        foreach ($disponibilidades as $disp) {
            $hora_atual = new DateTime($disp->hora_inicio);
            $hora_fim = new DateTime($disp->hora_fim);

            while ($hora_atual < $hora_fim) {
                $hora_inicio_str = $hora_atual->format('H:i:s');

                $hora_fim_agendamento = clone $hora_atual;
                $hora_fim_agendamento->add(new DateInterval('PT' . $duracao_minutos . 'M'));

                // Verificar se cabe no período de disponibilidade
                if ($hora_fim_agendamento <= $hora_fim) {
                    // Verificar se está disponível
                    if ($this->verificar_disponibilidade(
                        $profissional_id,
                        $data,
                        $hora_inicio_str,
                        $hora_fim_agendamento->format('H:i:s')
                    )) {
                        $horarios_disponiveis[] = $hora_atual->format('H:i');
                    }
                }

                // Avançar 30 minutos
                $hora_atual->add(new DateInterval('PT30M'));
            }
        }

        return $horarios_disponiveis;
    }

    /**
     * Contar agendamentos
     */
    public function count($filtros = []) {
        $this->db->from($this->table);

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('estabelecimento_id', $filtros['estabelecimento_id']);
        }

        if (!empty($filtros['profissional_id'])) {
            $this->db->where('profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }

        if (!empty($filtros['data'])) {
            $this->db->where('data', $filtros['data']);
        }

        return $this->db->count_all_results();
    }
}
