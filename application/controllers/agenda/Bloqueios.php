<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Bloqueios do Profissional
 *
 * Gerenciamento de bloqueios de agenda (férias, folgas, compromissos)
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Bloqueios extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');
        $this->auth_check->check_tipo(['profissional']);

        // Carregar models
        $this->load->model('Bloqueio_model');
        $this->load->model('Profissional_model');

        // Obter dados do profissional
        $this->profissional_id = $this->auth_check->get_profissional_id();
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->profissional = $this->Profissional_model->get_by_id($this->profissional_id);
    }

    /**
     * Listar bloqueios
     */
    public function index() {
        $bloqueios = $this->Bloqueio_model->get_all(['profissional_id' => $this->profissional_id]);

        $data['titulo'] = 'Meus Bloqueios';
        $data['menu_ativo'] = 'bloqueios';
        $data['bloqueios'] = $bloqueios;
        $data['profissional'] = $this->profissional;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/bloqueios/index', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Criar bloqueio
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');
            $this->form_validation->set_rules('data_inicio', 'Data Início', 'required');
            $this->form_validation->set_rules('motivo', 'Motivo', 'max_length[200]');

            if ($this->form_validation->run()) {
                $tipo = $this->input->post('tipo');

                $dados = [
                    'profissional_id' => $this->profissional_id,
                    'tipo' => $tipo,
                    'data_inicio' => $this->input->post('data_inicio'),
                    'motivo' => $this->input->post('motivo'),
                    'criado_por' => 'profissional'
                ];

                // Campos específicos por tipo
                if ($tipo == 'periodo') {
                    $dados['data_fim'] = $this->input->post('data_fim');
                } elseif ($tipo == 'horario') {
                    $dados['data_fim'] = $this->input->post('data_inicio'); // Mesmo dia
                    $dados['hora_inicio'] = $this->input->post('hora_inicio');
                    $dados['hora_fim'] = $this->input->post('hora_fim');
                }

                if ($this->Bloqueio_model->create($dados)) {
                    $this->session->set_flashdata('sucesso', 'Bloqueio criado com sucesso!');
                    redirect('agenda/bloqueios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar bloqueio.');
                }
            }
        }

        $data['titulo'] = 'Novo Bloqueio';
        $data['menu_ativo'] = 'bloqueios';
        $data['profissional'] = $this->profissional;
        $data['tipos'] = [
            'dia' => 'Dia Específico',
            'periodo' => 'Período (vários dias)',
            'horario' => 'Horário Específico'
        ];

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/bloqueios/form', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Editar bloqueio
     */
    public function editar($id) {
        $bloqueio = $this->Bloqueio_model->get_by_id($id);

        // Verificar se bloqueio existe e pertence ao profissional
        if (!$bloqueio || $bloqueio->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Bloqueio não encontrado.');
            redirect('agenda/bloqueios');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');
            $this->form_validation->set_rules('data_inicio', 'Data Início', 'required');

            if ($this->form_validation->run()) {
                $tipo = $this->input->post('tipo');

                $dados = [
                    'tipo' => $tipo,
                    'data_inicio' => $this->input->post('data_inicio'),
                    'motivo' => $this->input->post('motivo')
                ];

                // Campos específicos por tipo
                if ($tipo == 'periodo') {
                    $dados['data_fim'] = $this->input->post('data_fim');
                    $dados['hora_inicio'] = null;
                    $dados['hora_fim'] = null;
                } elseif ($tipo == 'horario') {
                    $dados['data_fim'] = $this->input->post('data_inicio');
                    $dados['hora_inicio'] = $this->input->post('hora_inicio');
                    $dados['hora_fim'] = $this->input->post('hora_fim');
                } else {
                    $dados['data_fim'] = null;
                    $dados['hora_inicio'] = null;
                    $dados['hora_fim'] = null;
                }

                if ($this->Bloqueio_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Bloqueio atualizado com sucesso!');
                    redirect('agenda/bloqueios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar bloqueio.');
                }
            }
        }

        $data['titulo'] = 'Editar Bloqueio';
        $data['menu_ativo'] = 'bloqueios';
        $data['bloqueio'] = $bloqueio;
        $data['profissional'] = $this->profissional;
        $data['tipos'] = [
            'dia' => 'Dia Específico',
            'periodo' => 'Período (vários dias)',
            'horario' => 'Horário Específico'
        ];

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/bloqueios/form', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Excluir bloqueio
     */
    public function excluir($id) {
        $bloqueio = $this->Bloqueio_model->get_by_id($id);

        // Verificar se bloqueio existe e pertence ao profissional
        if (!$bloqueio || $bloqueio->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Bloqueio não encontrado.');
            redirect('agenda/bloqueios');
        }

        if ($this->Bloqueio_model->delete($id)) {
            $this->session->set_flashdata('sucesso', 'Bloqueio excluído com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao excluir bloqueio.');
        }

        redirect('agenda/bloqueios');
    }
}
