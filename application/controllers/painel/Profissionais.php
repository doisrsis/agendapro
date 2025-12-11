<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Profissionais (Painel)
 *
 * Gestão de profissionais do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Profissionais extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Profissional_model');
        $this->load->model('Usuario_model');
    }

    /**
     * Listagem de profissionais
     */
    public function index() {
        $data['titulo'] = 'Profissionais';
        $data['menu_ativo'] = 'profissionais';
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['total'] = count($data['profissionais']);
        $data['filtros'] = ['estabelecimento_id' => $this->estabelecimento_id];
        $data['pagination'] = '';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/profissionais/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar profissional
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');

            if ($this->form_validation->run()) {
                // Verificar se email já existe
                if ($this->Usuario_model->email_existe($this->input->post('email'))) {
                    $this->session->set_flashdata('erro', 'Este e-mail já está cadastrado.');
                    redirect('painel/profissionais/criar');
                    return;
                }

                $dados_profissional = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'nome' => $this->input->post('nome'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                    'status' => 'ativo',
                ];

                $profissional_id = $this->Profissional_model->criar($dados_profissional);

                if ($profissional_id) {
                    // Criar usuário automaticamente
                    $dados_usuario = [
                        'nome' => $this->input->post('nome'),
                        'email' => $this->input->post('email'),
                        'telefone' => $this->input->post('telefone'),
                        'senha' => $this->input->post('senha'),
                        'tipo' => 'profissional',
                        'estabelecimento_id' => $this->estabelecimento_id,
                        'profissional_id' => $profissional_id,
                        'ativo' => 1
                    ];

                    $this->Usuario_model->criar($dados_usuario);

                    $this->session->set_flashdata('sucesso', 'Profissional criado com sucesso!');
                    redirect('painel/profissionais');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar profissional.');
                }
            }
        }

        $data['titulo'] = 'Novo Profissional';
        $data['menu_ativo'] = 'profissionais';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/profissionais/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar profissional
     */
    public function editar($id) {
        $profissional = $this->Profissional_model->get($id);

        if (!$profissional || $profissional->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('painel/profissionais');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                ];

                if ($this->Profissional_model->atualizar($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Profissional atualizado com sucesso!');
                    redirect('painel/profissionais');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar profissional.');
                }
            }
        }

        $data['titulo'] = 'Editar Profissional';
        $data['menu_ativo'] = 'profissionais';
        $data['profissional'] = $profissional;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/profissionais/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Desativar profissional
     */
    public function desativar($id) {
        $profissional = $this->Profissional_model->get($id);

        if (!$profissional || $profissional->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('painel/profissionais');
        }

        if ($this->Profissional_model->atualizar($id, ['status' => 'inativo'])) {
            $this->session->set_flashdata('sucesso', 'Profissional desativado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao desativar profissional.');
        }

        redirect('painel/profissionais');
    }

    /**
     * Alias para desativar
     */
    public function deletar($id) {
        $this->desativar($id);
    }

    /**
     * Alias para desativar
     */
    public function excluir($id) {
        $this->desativar($id);
    }

    /**
     * Visualizar profissional
     */
    public function visualizar($id) {
        $profissional = $this->Profissional_model->get($id);

        if (!$profissional || $profissional->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('painel/profissionais');
        }

        $data['titulo'] = 'Visualizar Profissional';
        $data['menu_ativo'] = 'profissionais';
        $data['profissional'] = $profissional;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/profissionais/visualizar', $data);
        $this->load->view('painel/layout/footer');
    }
}
