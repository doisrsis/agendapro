<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Planos
 *
 * CRUD de planos integrado ao Mercado Pago
 *
 * @author Rafael Dias - doisr.com.br
 * @date 10/12/2024
 */
class Planos extends Admin_Controller {

    protected $modulo_atual = 'planos';

    public function __construct() {
        parent::__construct();
        $this->load->model('Plano_model');
        $this->load->library('mercadopago_lib');
    }

    /**
     * Listagem de planos
     */
    public function index() {
        $data['titulo'] = 'Planos de Assinatura';
        $data['menu_ativo'] = 'planos';
        $data['planos'] = $this->Plano_model->get_all(false); // Todos, incluindo inativos

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/planos/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo plano
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('valor_mensal', 'Valor Mensal', 'required|numeric');
            $this->form_validation->set_rules('max_profissionais', 'Máx. Profissionais', 'required|integer');
            $this->form_validation->set_rules('max_agendamentos_mes', 'Máx. Agendamentos/Mês', 'required|integer');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'valor_mensal' => $this->input->post('valor_mensal'),
                    'max_profissionais' => $this->input->post('max_profissionais'),
                    'max_agendamentos_mes' => $this->input->post('max_agendamentos_mes'),
                    'trial_dias' => $this->input->post('trial_dias') ?: 7,
                    'ativo' => $this->input->post('ativo') ?: 1
                ];

                $criar_no_mp = $this->input->post('criar_no_mp') == '1';
                $id = $this->Plano_model->criar_com_mp($dados, $criar_no_mp);

                if ($id) {
                    $this->session->set_flashdata('sucesso', 'Plano criado com sucesso!');
                    redirect('admin/planos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar plano.');
                }
            }
        }

        $data['titulo'] = 'Novo Plano';
        $data['menu_ativo'] = 'planos';
        $data['mp_sandbox'] = $this->mercadopago_lib->is_sandbox();

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/planos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar plano
     */
    public function editar($id) {
        $plano = $this->Plano_model->get($id);

        if (!$plano) {
            $this->session->set_flashdata('erro', 'Plano não encontrado.');
            redirect('admin/planos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('valor_mensal', 'Valor Mensal', 'required|numeric');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'valor_mensal' => $this->input->post('valor_mensal'),
                    'max_profissionais' => $this->input->post('max_profissionais'),
                    'max_agendamentos_mes' => $this->input->post('max_agendamentos_mes'),
                    'trial_dias' => $this->input->post('trial_dias'),
                    'ativo' => $this->input->post('ativo')
                ];

                $atualizar_no_mp = $this->input->post('atualizar_no_mp') == '1';

                if ($this->Plano_model->atualizar_com_mp($id, $dados, $atualizar_no_mp)) {
                    $this->session->set_flashdata('sucesso', 'Plano atualizado com sucesso!');
                    redirect('admin/planos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar plano.');
                }
            }
        }

        $data['titulo'] = 'Editar Plano';
        $data['menu_ativo'] = 'planos';
        $data['plano'] = $plano;
        $data['mp_sandbox'] = $this->mercadopago_lib->is_sandbox();

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/planos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Desativar plano
     */
    public function desativar($id) {
        if ($this->Plano_model->desativar_com_mp($id)) {
            $this->session->set_flashdata('sucesso', 'Plano desativado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao desativar plano.');
        }

        redirect('admin/planos');
    }

    /**
     * Ativar plano
     */
    public function ativar($id) {
        if ($this->Plano_model->toggle_ativo($id, true)) {
            $this->session->set_flashdata('sucesso', 'Plano ativado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao ativar plano.');
        }

        redirect('admin/planos');
    }

    /**
     * Sincronizar plano com MP
     */
    public function sincronizar($id) {
        if ($this->Plano_model->sincronizar_com_mp($id)) {
            $this->session->set_flashdata('sucesso', 'Plano sincronizado com Mercado Pago!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao sincronizar plano.');
        }

        redirect('admin/planos');
    }
}
