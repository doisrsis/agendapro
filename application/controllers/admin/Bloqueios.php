<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Bloqueios
 *
 * Gerenciamento de bloqueios de horários
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Bloqueios extends Admin_Controller {

    protected $modulo_atual = 'bloqueios';

    public function __construct() {
        parent::__construct();
        $this->load->model('Bloqueio_model');
        $this->load->model('Profissional_model');
        $this->load->model('Estabelecimento_model');
    }

    /**
     * Listagem de bloqueios
     */
    public function index() {
        $data['titulo'] = 'Bloqueios de Horários';
        $data['menu_ativo'] = 'bloqueios';

        $filtros = [];

        if ($this->input->get('profissional_id')) {
            $filtros['profissional_id'] = $this->input->get('profissional_id');
        }

        if ($this->input->get('tipo')) {
            $filtros['tipo'] = $this->input->get('tipo');
        }

        if ($this->input->get('data_inicio')) {
            $filtros['data_inicio'] = $this->input->get('data_inicio');
        }

        if ($this->input->get('data_fim')) {
            $filtros['data_fim'] = $this->input->get('data_fim');
        }

        $data['bloqueios'] = $this->Bloqueio_model->get_all($filtros);
        $data['profissionais'] = $this->Profissional_model->get_all(['status' => 'ativo']);
        $data['filtros'] = $filtros;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/bloqueios/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar bloqueio
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('profissional_id', 'Profissional', 'required|integer');
            $this->form_validation->set_rules('data_inicio', 'Data Início', 'required');
            $this->form_validation->set_rules('data_fim', 'Data Fim', 'required');
            $this->form_validation->set_rules('tipo', 'Tipo', 'required|in_list[ferias,folga,almoco,outro]');

            if ($this->form_validation->run()) {
                $dados = [
                    'profissional_id' => $this->input->post('profissional_id'),
                    'data_inicio' => $this->input->post('data_inicio'),
                    'data_fim' => $this->input->post('data_fim'),
                    'hora_inicio' => $this->input->post('hora_inicio') ?: null,
                    'hora_fim' => $this->input->post('hora_fim') ?: null,
                    'tipo' => $this->input->post('tipo'),
                    'motivo' => $this->input->post('motivo'),
                ];

                $id = $this->Bloqueio_model->create($dados);

                if ($id) {
                    $this->session->set_flashdata('sucesso', 'Bloqueio criado com sucesso!');
                    redirect('admin/bloqueios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar bloqueio.');
                }
            }
        }

        $data['titulo'] = 'Novo Bloqueio';
        $data['menu_ativo'] = 'bloqueios';
        $data['profissionais'] = $this->Profissional_model->get_all(['status' => 'ativo']);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/bloqueios/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar bloqueio
     */
    public function editar($id) {
        $bloqueio = $this->Bloqueio_model->get_by_id($id);

        if (!$bloqueio) {
            $this->session->set_flashdata('erro', 'Bloqueio não encontrado.');
            redirect('admin/bloqueios');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('data_inicio', 'Data Início', 'required');
            $this->form_validation->set_rules('data_fim', 'Data Fim', 'required');
            $this->form_validation->set_rules('tipo', 'Tipo', 'required|in_list[ferias,folga,almoco,outro]');

            if ($this->form_validation->run()) {
                $dados = [
                    'data_inicio' => $this->input->post('data_inicio'),
                    'data_fim' => $this->input->post('data_fim'),
                    'hora_inicio' => $this->input->post('hora_inicio') ?: null,
                    'hora_fim' => $this->input->post('hora_fim') ?: null,
                    'tipo' => $this->input->post('tipo'),
                    'motivo' => $this->input->post('motivo'),
                ];

                if ($this->Bloqueio_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Bloqueio atualizado com sucesso!');
                    redirect('admin/bloqueios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar bloqueio.');
                }
            }
        }

        $data['titulo'] = 'Editar Bloqueio';
        $data['menu_ativo'] = 'bloqueios';
        $data['bloqueio'] = $bloqueio;
        $data['profissionais'] = $this->Profissional_model->get_all(['status' => 'ativo']);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/bloqueios/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Deletar bloqueio
     */
    public function deletar($id) {
        $bloqueio = $this->Bloqueio_model->get_by_id($id);

        if (!$bloqueio) {
            $this->session->set_flashdata('erro', 'Bloqueio não encontrado.');
            redirect('admin/bloqueios');
        }

        if ($this->Bloqueio_model->delete($id)) {
            $this->session->set_flashdata('sucesso', 'Bloqueio deletado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar bloqueio.');
        }

        redirect('admin/bloqueios');
    }
}
