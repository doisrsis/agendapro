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
                    $this->session->set_flashdata('erro', 'Erro ao criar agendamento.');
                }
            }
        }

        $data['titulo'] = 'Novo Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/agendamentos/form', $data);
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

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/agendamentos/form', $data);
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
        $this->load->view('admin/agendamentos/visualizar', $data);
        $this->load->view('painel/layout/footer');
    }
}
