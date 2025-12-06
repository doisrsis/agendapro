<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Disponibilidade
 *
 * Gerenciamento de disponibilidade de profissionais
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Disponibilidade extends Admin_Controller {

    protected $modulo_atual = 'disponibilidade';

    public function __construct() {
        parent::__construct();
        $this->load->model('Disponibilidade_model');
        $this->load->model('Profissional_model');
        $this->load->model('Estabelecimento_model');
    }

    /**
     * Gerenciar disponibilidade de um profissional
     */
    public function profissional($profissional_id) {
        $profissional = $this->Profissional_model->get_by_id($profissional_id);

        if (!$profissional) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('admin/profissionais');
        }

        $data['titulo'] = 'Disponibilidade - ' . $profissional->nome;
        $data['menu_ativo'] = 'profissionais';
        $data['profissional'] = $profissional;
        $data['disponibilidades'] = $this->Disponibilidade_model->get_by_profissional($profissional_id);

        // Agrupar por dia da semana
        $data['disponibilidades_por_dia'] = [];
        foreach ($data['disponibilidades'] as $disp) {
            $data['disponibilidades_por_dia'][$disp->dia_semana][] = $disp;
        }

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/disponibilidade/profissional', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar disponibilidade
     */
    public function criar($profissional_id) {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('dia_semana', 'Dia da Semana', 'required|integer|greater_than[0]|less_than[8]');
            $this->form_validation->set_rules('hora_inicio', 'Hora Início', 'required');
            $this->form_validation->set_rules('hora_fim', 'Hora Fim', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'profissional_id' => $profissional_id,
                    'dia_semana' => $this->input->post('dia_semana'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'hora_fim' => $this->input->post('hora_fim'),
                ];

                $id = $this->Disponibilidade_model->create($dados);

                if ($id) {
                    $this->session->set_flashdata('sucesso', 'Disponibilidade criada com sucesso!');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar disponibilidade. Verifique se não há conflito de horários.');
                }
            }
        }

        redirect('admin/disponibilidade/profissional/' . $profissional_id);
    }

    /**
     * Editar disponibilidade
     */
    public function editar($id) {
        $disponibilidade = $this->Disponibilidade_model->get_by_id($id);

        if (!$disponibilidade) {
            $this->session->set_flashdata('erro', 'Disponibilidade não encontrada.');
            redirect('admin/profissionais');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('hora_inicio', 'Hora Início', 'required');
            $this->form_validation->set_rules('hora_fim', 'Hora Fim', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'hora_fim' => $this->input->post('hora_fim'),
                ];

                if ($this->Disponibilidade_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Disponibilidade atualizada com sucesso!');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar disponibilidade.');
                }
            }
        }

        redirect('admin/disponibilidade/profissional/' . $disponibilidade->profissional_id);
    }

    /**
     * Deletar disponibilidade
     */
    public function deletar($id) {
        $disponibilidade = $this->Disponibilidade_model->get_by_id($id);

        if (!$disponibilidade) {
            $this->session->set_flashdata('erro', 'Disponibilidade não encontrada.');
            redirect('admin/profissionais');
        }

        $profissional_id = $disponibilidade->profissional_id;

        if ($this->Disponibilidade_model->delete($id)) {
            $this->session->set_flashdata('sucesso', 'Disponibilidade deletada com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar disponibilidade.');
        }

        redirect('admin/disponibilidade/profissional/' . $profissional_id);
    }

    /**
     * Criar disponibilidade padrão
     */
    public function criar_padrao($profissional_id) {
        if ($this->Disponibilidade_model->criar_disponibilidade_padrao($profissional_id)) {
            $this->session->set_flashdata('sucesso', 'Disponibilidade padrão criada com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao criar disponibilidade padrão.');
        }

        redirect('admin/disponibilidade/profissional/' . $profissional_id);
    }
}
