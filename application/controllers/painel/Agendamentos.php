<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Agendamentos (Painel)
 *
 * Gestão de agendamentos do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Agendamentos extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
    }

    /**
     * Listagem de agendamentos
     */
    public function index() {
        $data['titulo'] = 'Agendamentos';
        $data['menu_ativo'] = 'agendamentos';
        $data['view'] = $this->input->get('view') ?? 'lista';

        $filtros = ['estabelecimento_id' => $this->estabelecimento_id];

        if ($this->input->get('data')) {
            $filtros['data'] = $this->input->get('data');
        }

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('profissional_id')) {
            $filtros['profissional_id'] = $this->input->get('profissional_id');
        }

        $data['agendamentos'] = $this->Agendamento_model->get_all($filtros);
        $data['total'] = count($data['agendamentos']);
        $data['filtros'] = $filtros;
        $data['pagination'] = '';
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['estatisticas'] = $this->get_estatisticas();

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/agendamentos/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar agendamento
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('cliente_id', 'Cliente', 'required|integer');
            $this->form_validation->set_rules('profissional_id', 'Profissional', 'required|integer');
            $this->form_validation->set_rules('servico_id', 'Serviço', 'required|integer');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Hora', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'cliente_id' => $this->input->post('cliente_id'),
                    'profissional_id' => $this->input->post('profissional_id'),
                    'servico_id' => $this->input->post('servico_id'),
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => 'pendente',
                ];

                if ($this->Agendamento_model->criar($dados)) {
                    $this->session->set_flashdata('sucesso', 'Agendamento criado com sucesso!');
                    redirect('painel/agendamentos');
                } else {
                    // Verificar se há mensagem de erro específica
                    $erro_msg = $this->Agendamento_model->erro_disponibilidade ?? 'Erro ao criar agendamento.';
                    $this->session->set_flashdata('erro', $erro_msg);
                }
            }
        }

        $data['titulo'] = 'Novo Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/agendamentos/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar agendamento
     */
    public function editar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'status' => $this->input->post('status'),
                ];

                if ($this->Agendamento_model->atualizar($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Agendamento atualizado com sucesso!');
                    redirect('painel/agendamentos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar agendamento.');
                }
            }
        }

        $data['titulo'] = 'Editar Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['agendamento'] = $agendamento;
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/agendamentos/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Cancelar agendamento
     */
    public function cancelar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        if ($this->Agendamento_model->atualizar($id, ['status' => 'cancelado'])) {
            $this->session->set_flashdata('sucesso', 'Agendamento cancelado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cancelar agendamento.');
        }

        redirect('painel/agendamentos');
    }

    /**
     * Alias para cancelar
     */
    public function deletar($id) {
        $this->cancelar($id);
    }

    /**
     * Alias para cancelar
     */
    public function excluir($id) {
        $this->cancelar($id);
    }

    /**
     * Visualizar agendamento
     */
    public function visualizar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        $data['titulo'] = 'Visualizar Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['agendamento'] = $agendamento;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/agendamentos/visualizar', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * API JSON - Retorna agendamentos para o FullCalendar
     */
    public function get_agendamentos_json() {
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        $agendamentos = $this->Agendamento_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data_inicio' => $start,
            'data_fim' => $end
        ]);

        $eventos = [];
        foreach ($agendamentos as $ag) {
            $cor = $this->get_cor_status($ag->status);

            $data_hora_inicio = $ag->data . ' ' . $ag->hora_inicio;
            $data_hora_fim = $ag->data . ' ' . $ag->hora_fim;

            $eventos[] = [
                'id' => 'agendamento_' . $ag->id,
                'title' => $ag->cliente_nome . ' - ' . $ag->servico_nome . ' (' . $ag->profissional_nome . ')',
                'start' => $data_hora_inicio,
                'end' => $data_hora_fim,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'extendedProps' => [
                    'agendamento_id' => $ag->id,
                    'cliente_nome' => $ag->cliente_nome,
                    'profissional_nome' => $ag->profissional_nome,
                    'servico_nome' => $ag->servico_nome,
                    'status' => $ag->status
                ]
            ];
        }

        // Buscar bloqueios dos profissionais do estabelecimento
        $this->load->model('Bloqueio_model');

        // Buscar IDs dos profissionais do estabelecimento
        $profissionais = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $profissional_ids = array_column($profissionais, 'id');

        $bloqueios = [];
        foreach ($profissional_ids as $prof_id) {
            $bloqueios_prof = $this->Bloqueio_model->get_all([
                'profissional_id' => $prof_id,
                'data_inicio' => $start,
                'data_fim' => $end
            ]);
            $bloqueios = array_merge($bloqueios, $bloqueios_prof);
        }

        // Adicionar bloqueios ao calendário
        foreach ($bloqueios as $bloqueio) {
            // Definir título e cor baseado no tipo de bloqueio
            $cor = '#6c757d'; // Cinza padrão
            $titulo = '🚫 ';

            if (!empty($bloqueio->profissional_nome) && !empty($bloqueio->servico_nome)) {
                // Bloqueio específico (profissional + serviço)
                $titulo .= $bloqueio->profissional_nome . ' - ' . $bloqueio->servico_nome;
                $cor = '#dc3545'; // Vermelho
            } elseif (!empty($bloqueio->servico_nome)) {
                // Bloqueio de serviço (todos profissionais)
                $titulo .= $bloqueio->servico_nome . ' - Indisponível';
                $cor = '#fd7e14'; // Laranja
            } elseif (!empty($bloqueio->profissional_nome)) {
                // Bloqueio de profissional (todos serviços)
                $titulo .= $bloqueio->profissional_nome . ' - Bloqueado';
                $cor = '#6c757d'; // Cinza
            } else {
                $titulo .= 'Bloqueado';
            }

            if ($bloqueio->motivo) {
                $titulo .= ' (' . $bloqueio->motivo . ')';
            }

            // Definir data/hora baseado no tipo
            if ($bloqueio->tipo == 'horario') {
                // Bloqueio de horário específico
                $start_datetime = $bloqueio->data_inicio . ' ' . $bloqueio->hora_inicio;
                $end_datetime = $bloqueio->data_inicio . ' ' . $bloqueio->hora_fim;
            } else {
                // Bloqueio de dia ou período (dia inteiro)
                $start_datetime = $bloqueio->data_inicio;

                // Para bloqueio de dia específico (data_fim NULL), usar data_inicio + 1 dia
                if ($bloqueio->tipo == 'dia' || $bloqueio->data_fim === null) {
                    $end_datetime = date('Y-m-d', strtotime($bloqueio->data_inicio . ' +1 day'));
                } else {
                    // Para bloqueio de período, usar data_fim + 1 dia (FullCalendar exclusive end)
                    $end_datetime = date('Y-m-d', strtotime($bloqueio->data_fim . ' +1 day'));
                }
            }

            $eventos[] = [
                'id' => 'bloqueio_' . $bloqueio->id,
                'title' => $titulo,
                'start' => $start_datetime,
                'end' => $end_datetime,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'display' => 'background',
                'extendedProps' => [
                    'tipo' => 'bloqueio',
                    'bloqueio_tipo' => $bloqueio->tipo,
                    'profissional' => $bloqueio->profissional_nome ?? '',
                    'servico' => $bloqueio->servico_nome ?? '',
                    'motivo' => $bloqueio->motivo ?? ''
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($eventos);
    }

    /**
     * Obter estatísticas de agendamentos
     */
    public function get_estatisticas() {
        $hoje = date('Y-m-d');

        $stats = [
            'total_hoje' => 0,
            'confirmados' => 0,
            'pendentes' => 0,
            'cancelados' => 0
        ];

        $agendamentos_hoje = $this->Agendamento_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data' => $hoje
        ]);

        $stats['total_hoje'] = count($agendamentos_hoje);

        foreach ($agendamentos_hoje as $ag) {
            if ($ag->status == 'confirmado') $stats['confirmados']++;
            if ($ag->status == 'pendente') $stats['pendentes']++;
            if ($ag->status == 'cancelado') $stats['cancelados']++;
        }

        return $stats;
    }

    /**
     * Definir cor baseado no status
     */
    private function get_cor_status($status) {
        $cores = [
            'pendente' => '#ffc107',
            'confirmado' => '#28a745',
            'concluido' => '#17a2b8',
            'cancelado' => '#dc3545'
        ];
        return $cores[$status] ?? '#6c757d';
    }

    /**
     * API: Retorna horários disponíveis para agendamento
     */
    public function get_horarios_disponiveis() {
        $profissional_id = $this->input->get('profissional_id');
        $data = $this->input->get('data');
        $servico_id = $this->input->get('servico_id');

        if (!$profissional_id || !$data || !$servico_id) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Buscar serviço para pegar duração
        $servico = $this->Servico_model->get_by_id($servico_id);
        if (!$servico) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Buscar horário do estabelecimento para este dia
        $this->load->model('Horario_estabelecimento_model');
        $dia_semana = date('w', strtotime($data));
        $horario_estab = $this->Horario_estabelecimento_model->get_by_dia($this->estabelecimento_id, $dia_semana);

        if (!$horario_estab || !$horario_estab->ativo) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Buscar configuração de tempo mínimo e intervalo
        $this->load->model('Estabelecimento_model');
        $estabelecimento_config = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $tempo_minimo = $estabelecimento_config->tempo_minimo_agendamento ?? 60;
        $usar_intervalo_fixo = $estabelecimento_config->usar_intervalo_fixo ?? 1;

        // ✅ LÓGICA HÍBRIDA: Determinar intervalo
        if ($usar_intervalo_fixo) {
            // Modo 1: Intervalo Fixo Configurável
            $intervalo = $estabelecimento_config->intervalo_agendamento ?? 30;
        } else {
            // Modo 2: Intervalo Dinâmico (baseado na duração do serviço)
            $intervalo = $servico->duracao;
        }

        // Calcular horário mínimo permitido
        $agora = new DateTime();
        $horario_minimo = clone $agora;
        $horario_minimo->add(new DateInterval('PT' . $tempo_minimo . 'M'));

        // Verificar período de abertura da agenda
        $dias_antecedencia = $estabelecimento_config->dias_antecedencia_agenda ?? 30;
        if ($dias_antecedencia > 0) {
            $data_maxima = date('Y-m-d', strtotime("+{$dias_antecedencia} days"));

            if ($data > $data_maxima) {
                header('Content-Type: application/json');
                echo json_encode([]);
                return;
            }
        }

        // Gerar horários disponíveis
        $horarios = [];
        $hora_atual = new DateTime($horario_estab->hora_inicio);
        $hora_fim_estab = new DateTime($horario_estab->hora_fim);

        // Se a data selecionada for hoje, ajustar hora inicial
        if ($data == date('Y-m-d')) {
            if ($hora_atual < $horario_minimo) {
                $hora_atual = clone $horario_minimo;
                // Arredondar para próximo intervalo
                $minutos = (int)$hora_atual->format('i');
                $resto = $minutos % $intervalo;
                if ($resto > 0) {
                    $ajuste = $intervalo - $resto;
                    $hora_atual->add(new DateInterval('PT' . $ajuste . 'M'));
                }
            }
        }

        while ($hora_atual < $hora_fim_estab) {
            $hora_inicio_str = $hora_atual->format('H:i:s');
            $hora_fim_temp = clone $hora_atual;
            $hora_fim_temp->add(new DateInterval('PT' . $servico->duracao . 'M'));
            $hora_fim_str = $hora_fim_temp->format('H:i:s');

            // Verificar se horário está disponível
            if ($hora_fim_temp <= $hora_fim_estab) {
                if ($this->Agendamento_model->verificar_disponibilidade(
                    $profissional_id,
                    $data,
                    $hora_inicio_str,
                    $hora_fim_str
                )) {
                    $horarios[] = $hora_atual->format('H:i');
                }
            }

            // ✅ Avançar com intervalo (fixo ou dinâmico)
            $hora_atual->add(new DateInterval('PT' . $intervalo . 'M'));
        }

        header('Content-Type: application/json');
        echo json_encode($horarios);
    }
}
