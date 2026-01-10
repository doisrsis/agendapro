<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Agendamentos
 *
 * Gerencia os agendamentos do sistema com validação de disponibilidade
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12//2024
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
    public function create($data, $enviar_notificacao = true) {
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
            $hora_fim->format('H:i:s'),
            $data['servico_id']
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

            // Verificar se estabelecimento requer pagamento para agendamento
            $this->load->model('Estabelecimento_model');
            $estabelecimento = $this->Estabelecimento_model->get_by_id($data['estabelecimento_id']);
            $requer_pagamento = ($estabelecimento->agendamento_requer_pagamento ?? 'nao') != 'nao';

            // Só enviar notificações se NÃO requer pagamento E se enviar_notificacao = true
            // Se requer pagamento, as notificações serão enviadas após confirmação do pagamento
            // Se enviar_notificacao = false (bot), o bot enviará sua própria mensagem
            if (!$requer_pagamento && $enviar_notificacao) {
                // Enviar notificação WhatsApp de confirmação para cliente
                $this->enviar_notificacao_whatsapp($agendamento_id, 'confirmacao');

                // Enviar notificação WhatsApp para profissional/estabelecimento
                $this->enviar_notificacao_whatsapp($agendamento_id, 'profissional_novo');
            }

            return $agendamento_id;
        }

        return false;
    }

    /**
     * Atualizar agendamento
     */
    public function update($id, $data) {
        $update_data = [];

        // Se estiver alterando data/hora/profissional, calcular hora_fim
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

            $servico_id = $data['servico_id'] ?? $agendamento_atual->servico_id;

            // Verificar disponibilidade
            if (!$this->verificar_disponibilidade(
                $profissional_id,
                $data_agendamento,
                $hora_inicio_dt->format('H:i:s'),
                $hora_fim_dt->format('H:i:s'),
                $servico_id,
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
        if (isset($data['qtd_reagendamentos'])) $update_data['qtd_reagendamentos'] = $data['qtd_reagendamentos'];

        // Campos de pagamento
        if (isset($data['pagamento_status'])) $update_data['pagamento_status'] = $data['pagamento_status'];
        if (isset($data['pagamento_valor'])) $update_data['pagamento_valor'] = $data['pagamento_valor'];
        if (isset($data['pagamento_pix_qrcode'])) $update_data['pagamento_pix_qrcode'] = $data['pagamento_pix_qrcode'];
        if (isset($data['pagamento_pix_copia_cola'])) $update_data['pagamento_pix_copia_cola'] = $data['pagamento_pix_copia_cola'];
        if (isset($data['pagamento_expira_em'])) $update_data['pagamento_expira_em'] = $data['pagamento_expira_em'];
        if (isset($data['pagamento_lembrete_enviado'])) $update_data['pagamento_lembrete_enviado'] = $data['pagamento_lembrete_enviado'];
        if (isset($data['pagamento_token'])) $update_data['pagamento_token'] = $data['pagamento_token'];
        if (isset($data['pagamento_expira_adicional_em'])) $update_data['pagamento_expira_adicional_em'] = $data['pagamento_expira_adicional_em'];

        // Campos de Notificação / Controle
        if (isset($data['confirmacao_enviada'])) $update_data['confirmacao_enviada'] = $data['confirmacao_enviada'];
        if (isset($data['confirmacao_enviada_em'])) $update_data['confirmacao_enviada_em'] = $data['confirmacao_enviada_em'];
        if (isset($data['confirmacao_tentativas'])) $update_data['confirmacao_tentativas'] = $data['confirmacao_tentativas'];
        if (isset($data['confirmacao_ultima_tentativa'])) $update_data['confirmacao_ultima_tentativa'] = $data['confirmacao_ultima_tentativa'];
        if (isset($data['lembrete_enviado'])) $update_data['lembrete_enviado'] = $data['lembrete_enviado'];
        if (isset($data['lembrete_enviado_em'])) $update_data['lembrete_enviado_em'] = $data['lembrete_enviado_em'];
        if (isset($data['confirmado_em'])) $update_data['confirmado_em'] = $data['confirmado_em'];

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
        $resultado = $this->update($id, [
            'status' => 'cancelado',
            'cancelado_por' => $cancelado_por,
            'motivo_cancelamento' => $motivo
        ]);

        if ($resultado) {
            // Enviar notificação WhatsApp de cancelamento para cliente
            $this->enviar_notificacao_whatsapp($id, 'cancelamento', ['motivo' => $motivo]);

            // Enviar notificação WhatsApp para profissional/estabelecimento
            $this->enviar_notificacao_whatsapp($id, 'profissional_cancelamento', ['motivo' => $motivo]);
        }

        return $resultado;
    }

    /**
     * Finalizar agendamento
     */
    public function finalizar($id) {
        $resultado = $this->update($id, ['status' => 'finalizado']);

        if ($resultado) {
            // Enviar notificação WhatsApp de finalização
            $this->enviar_notificacao_whatsapp($id, 'finalizacao');
        }

        return $resultado;
    }

    /**
     * Enviar notificação WhatsApp para o cliente
     *
     * @param int $agendamento_id ID do agendamento
     * @param string $tipo Tipo: confirmacao, lembrete, cancelamento, reagendamento, finalizacao
     * @param array $dados_extras Dados adicionais (motivo, data_anterior, etc)
     * @return bool
     */
    public function enviar_notificacao_whatsapp($agendamento_id, $tipo, $dados_extras = []) {
        try {
            $CI =& get_instance();
            $CI->load->library('notificacao_whatsapp_lib');

            $agendamento = $this->get_by_id($agendamento_id);
            if (!$agendamento) {
                log_message('error', 'Notificacao WhatsApp: Agendamento #' . $agendamento_id . ' não encontrado');
                return false;
            }

            switch ($tipo) {
                case 'confirmacao':
                    $resultado = $CI->notificacao_whatsapp_lib->enviar_confirmacao($agendamento);
                    break;

                case 'lembrete':
                    $horas = $dados_extras['horas_antes'] ?? 24;
                    $resultado = $CI->notificacao_whatsapp_lib->enviar_lembrete($agendamento, $horas);
                    break;

                case 'cancelamento':
                    $motivo = $dados_extras['motivo'] ?? null;
                    $resultado = $CI->notificacao_whatsapp_lib->enviar_cancelamento($agendamento, $motivo);
                    break;

                case 'reagendamento':
                    $data_anterior = $dados_extras['data_anterior'] ?? '';
                    $hora_anterior = $dados_extras['hora_anterior'] ?? '';
                    $resultado = $CI->notificacao_whatsapp_lib->enviar_reagendamento($agendamento, $data_anterior, $hora_anterior);
                    break;

                case 'inicio':
                    $resultado = $CI->notificacao_whatsapp_lib->enviar_inicio($agendamento);
                    break;

                case 'finalizacao':
                    $resultado = $CI->notificacao_whatsapp_lib->enviar_finalizacao($agendamento);
                    break;

                // Notificações para profissional/estabelecimento
                case 'profissional_novo':
                    $resultado = $CI->notificacao_whatsapp_lib->notificar_profissional_novo_agendamento($agendamento);
                    break;

                case 'profissional_cancelamento':
                    $motivo = $dados_extras['motivo'] ?? null;
                    $resultado = $CI->notificacao_whatsapp_lib->notificar_profissional_cancelamento($agendamento, $motivo);
                    break;

                case 'profissional_reagendamento':
                    $data_anterior = $dados_extras['data_anterior'] ?? '';
                    $hora_anterior = $dados_extras['hora_anterior'] ?? '';
                    $resultado = $CI->notificacao_whatsapp_lib->notificar_profissional_reagendamento($agendamento, $data_anterior, $hora_anterior);
                    break;

                default:
                    log_message('error', 'Notificacao WhatsApp: Tipo desconhecido - ' . $tipo);
                    return false;
            }

            return $resultado['success'] ?? false;

        } catch (Exception $e) {
            log_message('error', 'Notificacao WhatsApp: Erro - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar disponibilidade do profissional
     */
    public function verificar_disponibilidade($profissional_id, $data, $hora_inicio, $hora_fim, $servico_id = null, $excluir_agendamento_id = null) {
        // 1. Verificar horário do estabelecimento
        $dia_semana = date('w', strtotime($data));

        // Buscar estabelecimento do profissional
        $this->db->select('estabelecimento_id');
        $this->db->where('id', $profissional_id);
        $profissional = $this->db->get('profissionais')->row();

        if (!$profissional) {
            $this->erro_disponibilidade = 'Profissional não encontrado.';
            return false;
        }

        // Verificar se estabelecimento está aberto neste dia/horário
        $this->load->model('Horario_estabelecimento_model');
        $horario_estab = $this->Horario_estabelecimento_model->get_by_dia($profissional->estabelecimento_id, $dia_semana);

        if (!$horario_estab || !$horario_estab->ativo) {
            $dias = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
            $this->erro_disponibilidade = 'Estabelecimento fechado em ' . $dias[$dia_semana] . '.';
            return false;
        }

        // Verificar se horário está dentro do expediente
        if ($hora_inicio < $horario_estab->hora_inicio || $hora_fim > $horario_estab->hora_fim) {
            $this->erro_disponibilidade = 'Horário fora do expediente. Funcionamento: ' .
                substr($horario_estab->hora_inicio, 0, 5) . ' às ' .
                substr($horario_estab->hora_fim, 0, 5) . '.';
            return false;
        }

        // 1.5. Verificar intervalo de almoço
        if ($this->Horario_estabelecimento_model->verificar_horario_almoco(
            $profissional->estabelecimento_id,
            $dia_semana,
            $hora_inicio
        )) {
            $this->erro_disponibilidade = 'Horário de almoço. Estabelecimento fechado.';
            return false;
        }

        /// 1.6. Verificar se a data é feriado
        $this->load->model('Feriado_model');
        if ($this->Feriado_model->is_feriado($data, $profissional->estabelecimento_id)) {
            $feriado = $this->Feriado_model->get_by_data($data, $profissional->estabelecimento_id);
            $this->erro_disponibilidade = 'Feriado: ' . $feriado->nome . '. Estabelecimento fechado.';
            return false;
        }

        // 2. Verificar tempo mínimo para agendamento
        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get($profissional->estabelecimento_id);

        if ($estabelecimento && $estabelecimento->tempo_minimo_agendamento > 0) {
            // Calcular data/hora do agendamento
            $data_hora_agendamento = new DateTime($data . ' ' . $hora_inicio);
            $agora = new DateTime();

            // Calcular diferença em minutos
            $diferenca_minutos = ($data_hora_agendamento->getTimestamp() - $agora->getTimestamp()) / 60;

            // Verificar se respeita o tempo mínimo
            if ($diferenca_minutos < $estabelecimento->tempo_minimo_agendamento) {
                $horas = floor($estabelecimento->tempo_minimo_agendamento / 60);
                $minutos = $estabelecimento->tempo_minimo_agendamento % 60;

                $tempo_texto = '';
                if ($horas > 0) {
                    $tempo_texto = $horas . ' hora' . ($horas > 1 ? 's' : '');
                    if ($minutos > 0) {
                        $tempo_texto .= ' e ' . $minutos . ' minutos';
                    }
                } else {
                    $tempo_texto = $minutos . ' minutos';
                }

                $this->erro_disponibilidade = 'Agendamento deve ser feito com antecedência mínima de ' . $tempo_texto . '.';
                return false;
            }
        }

        // 3. Verificar conflito com outros agendamentos
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
            $this->erro_disponibilidade = 'Já existe um agendamento neste horário.';
            return false;
        }

        // 3. Verificar bloqueios
        $this->load->model('Bloqueio_model');

        // 3.1. Bloqueio de profissional (geral ou específico do serviço)
        // Este método já verifica:
        // - Bloqueios gerais do profissional (servico_id NULL)
        // - Bloqueios específicos do profissional para este serviço
        $bloqueio_prof = $this->Bloqueio_model->verificar_bloqueio($profissional_id, $data, $hora_inicio, $hora_fim, $servico_id);
        if ($bloqueio_prof) {
            $this->erro_disponibilidade = 'Horário bloqueado para este serviço.';
            return false;
        }

        // 3.2. Bloqueio geral de serviço (sem profissional específico)
        // Verifica se o serviço está bloqueado para TODOS os profissionais
        if ($servico_id) {
            $bloqueio_servico = $this->Bloqueio_model->tem_bloqueio_servico($servico_id, $data, $hora_inicio, $hora_fim);
            if ($bloqueio_servico) {
                $this->erro_disponibilidade = 'Serviço indisponível neste horário.';
                return false;
            }
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

    /**
     * Contar agendamentos do mês atual por estabelecimento
     *
     * @param int $estabelecimento_id
     * @return int
     */
    public function count_mes_atual($estabelecimento_id) {
        $primeiro_dia = date('Y-m-01');
        $ultimo_dia = date('Y-m-t');

        return $this->db
            ->where('estabelecimento_id', $estabelecimento_id)
            ->where('data >=', $primeiro_dia)
            ->where('data <=', $ultimo_dia)
            ->count_all_results($this->table);
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

    // =========================================================================
    // MÉTODOS PARA CRON DE PAGAMENTOS
    // =========================================================================

    /**
     * Buscar agendamentos com pagamento pendente que expiraram
     * Usado pelo cron para enviar lembretes e cancelar
     *
     * @return array Lista de agendamentos pendentes expirados
     */
    public function get_pagamentos_pendentes_expirados() {
        return $this->db
            ->select('a.*, e.nome as estabelecimento_nome, e.agendamento_tempo_adicional_pix,
                      c.nome as cliente_nome, c.whatsapp as cliente_whatsapp, c.email as cliente_email,
                      s.nome as servico_nome, p.nome as profissional_nome')
            ->from($this->table . ' a')
            ->join('estabelecimentos e', 'a.estabelecimento_id = e.id')
            ->join('clientes c', 'a.cliente_id = c.id')
            ->join('servicos s', 'a.servico_id = s.id')
            ->join('profissionais p', 'a.profissional_id = p.id')
            ->where('a.pagamento_status', 'pendente')
            ->where('a.pagamento_expira_em IS NOT NULL')
            ->where('a.pagamento_expira_em <', date('Y-m-d H:i:s'))
            ->where('a.pagamento_lembrete_enviado', 0)
            ->get()
            ->result();
    }

    /**
     * Buscar agendamentos com tempo adicional expirado
     * Usado pelo cron para cancelar definitivamente
     *
     * @return array Lista de agendamentos para cancelar
     */
    public function get_pagamentos_tempo_adicional_expirado() {
        return $this->db
            ->select('a.*, e.nome as estabelecimento_nome,
                      c.nome as cliente_nome, c.whatsapp as cliente_whatsapp')
            ->from($this->table . ' a')
            ->join('estabelecimentos e', 'a.estabelecimento_id = e.id')
            ->join('clientes c', 'a.cliente_id = c.id')
            ->where('a.pagamento_status', 'pendente')
            ->where('a.pagamento_lembrete_enviado', 1)
            ->where('a.pagamento_expira_adicional_em IS NOT NULL')
            ->where('a.pagamento_expira_adicional_em <', date('Y-m-d H:i:s'))
            ->get()
            ->result();
    }

    /**
     * Buscar agendamento por token de pagamento
     *
     * @param string $token Token único do pagamento
     * @return object|null Agendamento encontrado
     */
    public function get_by_pagamento_token($token) {
        if (empty($token)) {
            return null;
        }

        return $this->db
            ->select('a.*, e.nome as estabelecimento_nome, e.logo as estabelecimento_logo,
                      c.nome as cliente_nome, c.whatsapp as cliente_whatsapp,
                      s.nome as servico_nome, s.preco as servico_preco,
                      p.nome as profissional_nome')
            ->from($this->table . ' a')
            ->join('estabelecimentos e', 'a.estabelecimento_id = e.id')
            ->join('clientes c', 'a.cliente_id = c.id')
            ->join('servicos s', 'a.servico_id = s.id')
            ->join('profissionais p', 'a.profissional_id = p.id')
            ->where('a.pagamento_token', $token)
            ->get()
            ->row();
    }

    /**
     * Gerar token único para pagamento
     *
     * @return string Token de 32 caracteres
     */
    public function gerar_token_pagamento() {
        return bin2hex(random_bytes(16));
    }

    /**
     * Buscar próximos agendamentos de um cliente
     *
     * @param int $cliente_id
     * @param int $limite
     * @return array
     */
    public function get_proximos_by_cliente($cliente_id, $limite = 5) {
        return $this->db
            ->select('a.*, s.nome as servico_nome, s.duracao as duracao_minutos, s.preco, p.nome as profissional_nome')
            ->from($this->table . ' a')
            ->join('servicos s', 'a.servico_id = s.id')
            ->join('profissionais p', 'a.profissional_id = p.id')
            ->where('a.cliente_id', $cliente_id)
            ->where('a.data >=', date('Y-m-d'))
            ->where_in('a.status', ['pendente', 'confirmado'])
            ->order_by('a.data', 'ASC')
            ->order_by('a.hora_inicio', 'ASC')
            ->limit($limite)
            ->get()
            ->result();
    }

    /**
     * Buscar agendamentos de um profissional em uma data específica
     *
     * @param int $profissional_id
     * @param string $data
     * @return array
     */
    public function get_by_profissional_data($profissional_id, $data) {
        return $this->db
            ->select('a.*')
            ->from($this->table . ' a')
            ->where('a.profissional_id', $profissional_id)
            ->where('a.data', $data)
            ->where_in('a.status', ['pendente', 'confirmado', 'em_atendimento'])
            ->order_by('a.hora_inicio', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Reagendar um agendamento
     * Valida limite de reagendamentos e disponibilidade
     *
     * @param int $agendamento_id
     * @param string $nova_data
     * @param string $nova_hora_inicio
     * @return array ['success' => bool, 'message' => string, 'agendamento_id' => int]
     */
    public function reagendar($agendamento_id, $nova_data, $nova_hora_inicio) {
        // Buscar agendamento
        $agendamento = $this->get_by_id($agendamento_id);

        if (!$agendamento) {
            return ['success' => false, 'message' => 'Agendamento não encontrado'];
        }

        // Verificar se agendamento pode ser reagendado
        if (!in_array($agendamento->status, ['pendente', 'confirmado'])) {
            return ['success' => false, 'message' => 'Agendamento não pode ser reagendado (status: ' . $agendamento->status . ')'];
        }

        // Buscar estabelecimento
        $CI =& get_instance();
        $CI->load->model('Estabelecimento_model');
        $estabelecimento = $CI->Estabelecimento_model->get_by_id($agendamento->estabelecimento_id);

        // Verificar se estabelecimento permite reagendamento
        if (!$estabelecimento->permite_reagendamento) {
            return ['success' => false, 'message' => 'Estabelecimento não permite reagendamento'];
        }

        // Verificar limite de reagendamentos
        $qtd_atual = $agendamento->qtd_reagendamentos ?? 0;
        $limite = $estabelecimento->limite_reagendamentos ?? 3;

        if ($qtd_atual >= $limite) {
            return ['success' => false, 'message' => "Limite de reagendamentos atingido ({$limite}x)"];
        }

        // Calcular nova hora de término
        $duracao = $agendamento->servico_duracao ?? 30;
        $nova_hora_fim = date('H:i:s', strtotime($nova_hora_inicio) + ($duracao * 60));

        // Verificar disponibilidade
        $disponivel = $this->verificar_disponibilidade(
            $agendamento->profissional_id,
            $nova_data,
            $nova_hora_inicio,
            $nova_hora_fim,
            $agendamento->servico_id,
            $agendamento_id // Excluir o próprio agendamento da verificação
        );

        if (!$disponivel) {
            return ['success' => false, 'message' => $this->erro_disponibilidade ?? 'Horário não disponível'];
        }

        // Guardar data/hora anterior para notificação
        $data_anterior = $agendamento->data;
        $hora_anterior = $agendamento->hora_inicio;

        // Atualizar agendamento
        $dados_update = [
            'data' => $nova_data,
            'hora_inicio' => $nova_hora_inicio,
            'hora_fim' => $nova_hora_fim,
            'qtd_reagendamentos' => $qtd_atual + 1
        ];

        $atualizado = $this->update($agendamento_id, $dados_update);

        if (!$atualizado) {
            return ['success' => false, 'message' => 'Erro ao atualizar agendamento'];
        }

        // Enviar notificações
        $this->enviar_notificacao_whatsapp($agendamento_id, 'reagendamento', [
            'data_anterior' => $data_anterior,
            'hora_anterior' => $hora_anterior
        ]);

        $this->enviar_notificacao_whatsapp($agendamento_id, 'profissional_reagendamento', [
            'data_anterior' => $data_anterior,
            'hora_anterior' => $hora_anterior
        ]);

        return [
            'success' => true,
            'message' => 'Agendamento reagendado com sucesso',
            'agendamento_id' => $agendamento_id,
            'qtd_reagendamentos' => $qtd_atual + 1,
            'limite_reagendamentos' => $limite
        ];
    }

    /**
     * Verificar se agendamento pode ser reagendado
     *
     * @param int $agendamento_id
     * @return array ['pode_reagendar' => bool, 'motivo' => string, 'qtd_atual' => int, 'limite' => int]
     */
    public function pode_reagendar($agendamento_id) {
        $agendamento = $this->get_by_id($agendamento_id);

        if (!$agendamento) {
            return ['pode_reagendar' => false, 'motivo' => 'Agendamento não encontrado'];
        }

        // Verificar status
        if (!in_array($agendamento->status, ['pendente', 'confirmado'])) {
            return ['pode_reagendar' => false, 'motivo' => 'Status não permite reagendamento'];
        }

        // Buscar estabelecimento
        $CI =& get_instance();
        $CI->load->model('Estabelecimento_model');
        $estabelecimento = $CI->Estabelecimento_model->get_by_id($agendamento->estabelecimento_id);

        $qtd_atual = $agendamento->qtd_reagendamentos ?? 0;
        $limite = $estabelecimento->limite_reagendamentos ?? 3;

        // Verificar se estabelecimento permite
        if (!$estabelecimento->permite_reagendamento) {
            return [
                'pode_reagendar' => false,
                'motivo' => 'Este estabelecimento não aceita reagendamentos automáticos.',
                'qtd_atual' => $qtd_atual,
                'limite' => $limite
            ];
        }

        // Verificar limite
        if ($qtd_atual >= $limite) {
            return [
                'pode_reagendar' => false,
                'motivo' => "Limite de reagendamentos atingido ({$qtd_atual}/{$limite})",
                'qtd_atual' => $qtd_atual,
                'limite' => $limite
            ];
        }

        return [
            'pode_reagendar' => true,
            'motivo' => 'Pode reagendar',
            'qtd_atual' => $qtd_atual,
            'limite' => $limite
        ];
    }

    /**
     * Buscar agendamentos pendentes que precisam de confirmação
     *
     * @return array Lista de agendamentos
     */
    public function get_pendentes_confirmacao() {
        $sql = "
            SELECT
                a.*,
                e.nome as estabelecimento_nome,
                e.endereco as estabelecimento_endereco,
                e.solicitar_confirmacao,
                e.confirmacao_dia_anterior,
                e.confirmacao_horario_dia_anterior,
                e.confirmacao_max_tentativas,
                e.confirmacao_intervalo_tentativas_minutos,
                e.confirmacao_cancelar_automatico,
                c.nome as cliente_nome,
                c.whatsapp as cliente_whatsapp,
                s.nome as servico_nome,
                s.duracao as servico_duracao,
                s.preco as servico_preco,
                p.nome as profissional_nome
            FROM agendamentos a
            JOIN estabelecimentos e ON a.estabelecimento_id = e.id
            JOIN clientes c ON a.cliente_id = c.id
            JOIN servicos s ON a.servico_id = s.id
            JOIN profissionais p ON a.profissional_id = p.id
            WHERE a.status = 'pendente'
              AND e.agendamento_requer_pagamento = 'nao'
              AND e.solicitar_confirmacao = 1
              AND e.confirmacao_dia_anterior = 1
              -- IMPORTANTE: Apenas agendamentos para AMANHÃ (dia anterior)
              AND a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
              AND (
                  -- Primeira tentativa: horário configurado passou E ainda não enviou
                  (a.confirmacao_tentativas = 0
                   AND TIME(NOW()) >= e.confirmacao_horario_dia_anterior)
                  OR
                  -- Tentativas subsequentes: já passou o intervalo desde a última tentativa
                  (a.confirmacao_tentativas > 0
                   AND a.confirmacao_tentativas < COALESCE(e.confirmacao_max_tentativas, 3)
                   AND TIMESTAMPDIFF(MINUTE, a.confirmacao_ultima_tentativa, NOW()) >= COALESCE(e.confirmacao_intervalo_tentativas_minutos, 30))
              )
            ORDER BY a.data, a.hora_inicio
        ";

        $result = $this->db->query($sql)->result();

        // Log detalhado para debug
        log_message('info', 'CRON: get_pendentes_confirmacao - Total encontrado: ' . count($result));
        foreach ($result as $ag) {
            log_message('info', "CRON: Agendamento #{$ag->id} - Data: {$ag->data}, Tentativas: {$ag->confirmacao_tentativas}/{$ag->confirmacao_max_tentativas}");
        }

        return $result;
    }

    /**
     * Buscar agendamentos confirmados que precisam de lembrete
     *
     * @return array Lista de agendamentos
     */
    public function get_para_lembrete() {
        $sql = "
            SELECT
                a.*,
                e.nome as estabelecimento_nome,
                e.endereco as estabelecimento_endereco,
                e.lembrete_minutos_antes,
                e.lembrete_antecedencia_chegada,
                c.nome as cliente_nome,
                c.whatsapp as cliente_whatsapp,
                s.nome as servico_nome,
                s.duracao as servico_duracao,
                p.nome as profissional_nome
            FROM agendamentos a
            JOIN estabelecimentos e ON a.estabelecimento_id = e.id
            JOIN clientes c ON a.cliente_id = c.id
            JOIN servicos s ON a.servico_id = s.id
            JOIN profissionais p ON a.profissional_id = p.id
            WHERE a.status = 'confirmado'
              AND a.data = CURDATE()
              AND e.enviar_lembrete_pre_atendimento = 1

              -- Cooldown e envio único
              AND (
                  a.lembrete_enviado = 0
                  OR (a.lembrete_enviado_em IS NOT NULL AND TIMESTAMPDIFF(MINUTE, a.lembrete_enviado_em, NOW()) >= 14)
              )

              -- Janela de envio (dentro dos minutos configurados)
              AND TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) <= e.lembrete_minutos_antes
              AND TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) > 0

              -- Otimização: Tentar enviar o mais próximo possível do tempo configurado (margem de 15 min)
              AND ABS(TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) - e.lembrete_minutos_antes) <= 15
            ORDER BY a.hora_inicio
        ";

        return $this->db->query($sql)->result();
    }

    /**
     * Buscar agendamentos não confirmados que expiraram
     *
     * @return array Lista de agendamentos
     */
    public function get_nao_confirmados_expirados() {
        $sql = "
            SELECT
                a.*,
                e.nome as estabelecimento_nome,
                e.confirmacao_max_tentativas,
                e.confirmacao_intervalo_tentativas_minutos,
                e.confirmacao_cancelar_automatico,
                e.cancelar_nao_confirmados,
                e.cancelar_nao_confirmados_horas,
                c.nome as cliente_nome,
                c.whatsapp as cliente_whatsapp,
                s.nome as servico_nome,
                p.nome as profissional_nome
            FROM agendamentos a
            JOIN estabelecimentos e ON a.estabelecimento_id = e.id
            JOIN clientes c ON a.cliente_id = c.id
            JOIN servicos s ON a.servico_id = s.id
            JOIN profissionais p ON a.profissional_id = p.id
            WHERE a.status = 'pendente'
              AND a.data >= CURDATE()
              AND (
                  -- OPÇÃO 1: Sistema de tentativas múltiplas (NOVO)
                  -- Cancela após X tentativas no dia anterior + intervalo
                  (e.confirmacao_dia_anterior = 1
                   AND e.confirmacao_cancelar_automatico = 'sim'
                   AND a.confirmacao_tentativas >= COALESCE(e.confirmacao_max_tentativas, 3)
                   AND TIMESTAMPDIFF(MINUTE, a.confirmacao_ultima_tentativa, NOW()) >= COALESCE(e.confirmacao_intervalo_tentativas_minutos, 30))
                  OR
                  -- OPÇÃO 2: Sistema antigo por horas antes do agendamento
                  -- Cancela X horas antes do horário se não confirmou
                  (e.cancelar_nao_confirmados = 1
                   AND a.confirmacao_enviada = 1
                   AND TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) <= COALESCE(e.cancelar_nao_confirmados_horas, 2))
              )
            ORDER BY a.data, a.hora_inicio
        ";

        return $this->db->query($sql)->result();
    }
}
