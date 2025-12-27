<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Agendamentos da Agenda
 *
 * CRUD de agendamentos para profissionais
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Agendamentos extends Agenda_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Servico_model');
    }

    /**
     * Criar novo agendamento
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            log_message('debug', 'Agenda/Agendamentos/criar - POST recebido');

            $this->form_validation->set_rules('cliente_id', 'Cliente', 'required');
            $this->form_validation->set_rules('servico_id', 'Serviço', 'required');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');

            if ($this->form_validation->run()) {
                log_message('debug', 'Agenda/Agendamentos/criar - Validação OK');

                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'profissional_id' => $this->profissional_id,
                    'cliente_id' => $this->input->post('cliente_id'),
                    'servico_id' => $this->input->post('servico_id'),
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => 'confirmado',
                    'observacoes' => $this->input->post('observacoes')
                ];

                // Criar data_hora combinada
                $dados['data_hora'] = $dados['data'] . ' ' . $dados['hora_inicio'];

                log_message('debug', 'Agenda/Agendamentos/criar - Dados: ' . json_encode($dados));

                $result = $this->Agendamento_model->create($dados);
                log_message('debug', 'Agenda/Agendamentos/criar - Resultado create: ' . ($result ? 'true' : 'false'));

                if ($result) {
                    log_message('debug', 'Agenda/Agendamentos/criar - Agendamento criado com sucesso');
                    $this->session->set_flashdata('sucesso', 'Agendamento criado com sucesso!');
                    redirect('agenda/dashboard');
                } else {
                    log_message('error', 'Agenda/Agendamentos/criar - Falha ao criar agendamento');

                    // Verificar se há mensagem de erro específica
                    $erro_msg = $this->Agendamento_model->erro_disponibilidade ?? 'Erro ao criar agendamento.';
                    $this->session->set_flashdata('erro', $erro_msg);
                }
            } else {
                log_message('debug', 'Agenda/Agendamentos/criar - Validação falhou: ' . validation_errors());
            }
        }

        $data['titulo'] = 'Novo Agendamento';
        $data['menu_ativo'] = 'agenda';

        // Carregar clientes do estabelecimento
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        // Carregar serviços do profissional
        $data['servicos'] = $this->Profissional_model->get_servicos($this->profissional_id);

        // Calcular data máxima baseada no período de abertura
        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $dias_antecedencia = $estabelecimento->dias_antecedencia_agenda ?? 30;
        $this->load->model('Horario_estabelecimento_model');
        $data['data_maxima'] = $dias_antecedencia > 0
            ? $this->calcular_data_maxima_dias_uteis($this->estabelecimento_id, $dias_antecedencia)
            : null;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/agendamentos/form', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Editar agendamento
     */
    public function editar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        // Verificar se agendamento existe e pertence ao profissional
        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        if ($this->input->method() === 'post') {
            log_message('debug', 'Agenda/Agendamentos/editar - POST recebido para ID: ' . $id);

            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run()) {
                log_message('debug', 'Agenda/Agendamentos/editar - Validação OK');

                $dados = [
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => $this->input->post('status'),
                    'observacoes' => $this->input->post('observacoes')
                ];

                log_message('debug', 'Agenda/Agendamentos/editar - Dados: ' . json_encode($dados));

                $result = $this->Agendamento_model->update($id, $dados);
                log_message('debug', 'Agenda/Agendamentos/editar - Resultado update: ' . ($result ? 'true' : 'false'));

                if ($result) {
                    log_message('debug', 'Agenda/Agendamentos/editar - Agendamento atualizado com sucesso');
                    $this->session->set_flashdata('sucesso', 'Agendamento atualizado com sucesso!');
                    redirect('agenda/dashboard');
                } else {
                    log_message('error', 'Agenda/Agendamentos/editar - Falha ao atualizar agendamento');

                    // Verificar se há mensagem de erro específica
                    $erro_msg = $this->Agendamento_model->erro_disponibilidade ?? 'Erro ao atualizar agendamento.';
                    $this->session->set_flashdata('erro', $erro_msg);
                }
            } else {
                log_message('debug', 'Agenda/Agendamentos/editar - Validação falhou: ' . validation_errors());
            }
        }

        $data['titulo'] = 'Editar Agendamento';
        $data['menu_ativo'] = 'agenda';
        $data['agendamento'] = $agendamento;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/agendamentos/editar', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * API: Retorna horários disponíveis para agendamento
     */
    public function get_horarios_disponiveis() {
        $servico_id = $this->input->get('servico_id');
        $data = $this->input->get('data');

        if (!$servico_id || !$data) {
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
                    $this->profissional_id,
                    $data,
                    $hora_inicio_str,
                    $hora_fim_str,
                    $servico_id
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

    /**
     * Cancelar agendamento
     */
    public function cancelar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        // Verificar se agendamento existe e pertence ao profissional
        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        if ($this->Agendamento_model->update($id, ['status' => 'cancelado'])) {
            $this->session->set_flashdata('sucesso', 'Agendamento cancelado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cancelar agendamento.');
        }

        redirect('agenda/dashboard');
    }

    /**
     * Calcular data máxima considerando apenas dias úteis (dias ativos do estabelecimento)
     */
    private function calcular_data_maxima_dias_uteis($estabelecimento_id, $dias_necessarios) {
        // Buscar horários do estabelecimento
        $horarios = $this->Horario_estabelecimento_model->get_by_estabelecimento($estabelecimento_id);

        // Criar array de dias ativos (0=domingo, 1=segunda, ..., 6=sábado)
        $dias_ativos = [];
        foreach ($horarios as $horario) {
            if ($horario->ativo) {
                $dias_ativos[] = (int)$horario->dia_semana;
            }
        }

        // Se não há dias ativos, retornar null
        if (empty($dias_ativos)) {
            return null;
        }

        // Carregar model de feriados
        $this->load->model('Feriado_model');

        // Calcular data máxima pulando dias inativos
        $data_atual = new DateTime();
        $dias_contados = 0;

        while ($dias_contados < $dias_necessarios) {
            $data_atual->add(new DateInterval('P1D')); // Adicionar 1 dia
            $dia_semana = (int)$data_atual->format('w'); // 0=domingo, 6=sábado
            $data_str = $data_atual->format('Y-m-d');

            // Contar apenas se o dia está ativo
            if (in_array($dia_semana, $dias_ativos) &&
    !$this->Feriado_model->is_feriado($data_str, $estabelecimento_id)) {
        $dias_contados++;
    }
        }

        return $data_atual->format('Y-m-d');
    }
}
