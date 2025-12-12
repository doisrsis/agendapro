<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Agenda (Profissional)
 *
 * Dashboard específico para usuários do tipo 'profissional'
 * Exibe agenda e informações do profissional
 *
 * @author Rafael Dias - doisr.com.br
 * @date 10/12/2024
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');

        // Verificar tipo de usuário
        $this->auth_check->check_tipo(['profissional']);

        // Carregar models
        $this->load->model('Profissional_model');
        $this->load->model('Agendamento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Servico_model');

        // Obter dados do profissional
        $this->profissional_id = $this->auth_check->get_profissional_id();
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->profissional = $this->Profissional_model->get_by_id($this->profissional_id);

        // Verificar se estabelecimento está ativo
        $this->auth_check->verificar_estabelecimento_ativo();
    }

    /**
     * Dashboard principal do profissional (agenda)
     */
    public function index() {
        $data['titulo'] = 'Minha Agenda';
        $data['menu_ativo'] = 'agenda';

        // Dados do profissional
        $data['profissional'] = $this->profissional;

        // Data selecionada (padrão: hoje)
        $data_selecionada = $this->input->get('data') ?: date('Y-m-d');
        $data['data_selecionada'] = $data_selecionada;

        // Agendamentos do dia
        $data['agendamentos_dia'] = $this->Agendamento_model->get_all([
            'profissional_id' => $this->profissional_id,
            'data' => $data_selecionada
        ]);

        // Estatísticas do profissional
        $data['total_agendamentos_hoje'] = $this->Agendamento_model->count([
            'profissional_id' => $this->profissional_id,
            'data' => date('Y-m-d')
        ]);

        $data['total_agendamentos_mes'] = $this->Agendamento_model->count([
            'profissional_id' => $this->profissional_id,
            'data_inicio' => date('Y-m-01'),
            'data_fim' => date('Y-m-t')
        ]);

        $data['agendamentos_confirmados'] = $this->Agendamento_model->count([
            'profissional_id' => $this->profissional_id,
            'data' => date('Y-m-d'),
            'status' => 'confirmado'
        ]);

        $data['agendamentos_concluidos'] = $this->Agendamento_model->count([
            'profissional_id' => $this->profissional_id,
            'data' => date('Y-m-d'),
            'status' => 'concluido'
        ]);

        // Próximos agendamentos (próximos 7 dias)
        $data['proximos_agendamentos'] = $this->Agendamento_model->get_all([
            'profissional_id' => $this->profissional_id,
            'data_inicio' => date('Y-m-d'),
            'data_fim' => date('Y-m-d', strtotime('+7 days'))
        ], 10);

        // Serviços do profissional
        $data['servicos'] = $this->Profissional_model->get_servicos($this->profissional_id);

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/dashboard/index', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * API JSON - Retorna agendamentos e bloqueios para o FullCalendar
     */
    public function get_agendamentos_json() {
        // Pegar parâmetros de data do FullCalendar
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        // Buscar agendamentos do profissional no período
        $agendamentos = $this->Agendamento_model->get_all([
            'profissional_id' => $this->profissional_id,
            'data_inicio' => $start,
            'data_fim' => $end
        ]);

        // Buscar bloqueios do profissional no período
        $this->load->model('Bloqueio_model');
        $bloqueios = $this->Bloqueio_model->get_all([
            'profissional_id' => $this->profissional_id,
            'data_inicio' => $start,
            'data_fim' => $end
        ]);

        // Converter agendamentos para formato FullCalendar
        $eventos = [];
        foreach ($agendamentos as $ag) {
            // Definir cor baseado no status
            $cor = $this->get_cor_status($ag->status);

            // Combinar data e hora
            $data_hora_inicio = $ag->data . ' ' . $ag->hora_inicio;
            $data_hora_fim = $ag->data . ' ' . $ag->hora_fim;

            $eventos[] = [
                'id' => 'agendamento_' . $ag->id,
                'title' => $ag->cliente_nome . ' - ' . $ag->servico_nome,
                'start' => $data_hora_inicio,
                'end' => $data_hora_fim,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'extendedProps' => [
                    'tipo' => 'agendamento',
                    'cliente_id' => $ag->cliente_id,
                    'cliente_nome' => $ag->cliente_nome,
                    'cliente_whatsapp' => $ag->cliente_whatsapp ?? '',
                    'servico_id' => $ag->servico_id,
                    'servico_nome' => $ag->servico_nome,
                    'status' => $ag->status,
                    'observacoes' => $ag->observacoes ?? ''
                ]
            ];
        }

        // Adicionar bloqueios ao calendário
        foreach ($bloqueios as $bloqueio) {
            $titulo = '🚫 Bloqueado';
            if ($bloqueio->motivo) {
                $titulo .= ': ' . $bloqueio->motivo;
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

            // Log temporário para debug
            log_message('debug', 'Bloqueio ID ' . $bloqueio->id . ': tipo=' . $bloqueio->tipo .
                ', data_inicio=' . $bloqueio->data_inicio .
                ', data_fim=' . ($bloqueio->data_fim ?? 'NULL') .
                ', start=' . $start_datetime .
                ', end=' . $end_datetime);

            $eventos[] = [
                'id' => 'bloqueio_' . $bloqueio->id,
                'title' => $titulo,
                'start' => $start_datetime,
                'end' => $end_datetime,
                'backgroundColor' => '#6c757d',
                'borderColor' => '#6c757d',
                'display' => 'background',
                'extendedProps' => [
                    'tipo' => 'bloqueio',
                    'bloqueio_tipo' => $bloqueio->tipo,
                    'motivo' => $bloqueio->motivo ?? ''
                ]
            ];
        }

        // Retornar JSON
        header('Content-Type: application/json');
        echo json_encode($eventos);
    }

    /**
     * Retorna cor baseado no status
     */
    private function get_cor_status($status) {
        $cores = [
            'confirmado' => '#28a745',  // Verde
            'pendente' => '#ffc107',    // Amarelo
            'cancelado' => '#dc3545',   // Vermelho
            'concluido' => '#007bff'    // Azul
        ];

        return $cores[$status] ?? '#6c757d'; // Cinza padrão
    }
}
