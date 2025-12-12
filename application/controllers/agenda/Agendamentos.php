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
}
