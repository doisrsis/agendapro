<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Perfil do Profissional
 *
 * Gerenciamento do perfil e configurações do profissional
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Perfil extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');
        $this->auth_check->check_tipo(['profissional']);

        // Carregar models
        $this->load->model('Profissional_model');
        $this->load->model('Usuario_model');

        // Obter dados do profissional
        $this->profissional_id = $this->auth_check->get_profissional_id();
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->profissional = $this->Profissional_model->get_by_id($this->profissional_id);
    }

    /**
     * Página de perfil
     */
    public function index() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'max_length[20]');
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

            if ($this->form_validation->run()) {
                $dados_profissional = [
                    'nome' => $this->input->post('nome'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'email' => $this->input->post('email')
                ];

                // Atualizar profissional
                if ($this->Profissional_model->update($this->profissional_id, $dados_profissional)) {
                    // Atualizar usuário também
                    $dados_usuario = [
                        'nome' => $this->input->post('nome'),
                        'email' => $this->input->post('email')
                    ];

                    $this->Usuario_model->update($this->session->userdata('usuario_id'), $dados_usuario);

                    $this->session->set_flashdata('sucesso', 'Perfil atualizado com sucesso!');
                    redirect('agenda/perfil');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar perfil.');
                }
            }
        }

        $data['titulo'] = 'Meu Perfil';
        $data['menu_ativo'] = 'perfil';
        $data['profissional'] = $this->profissional;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/perfil/index', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Alterar senha
     */
    public function alterar_senha() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('senha_atual', 'Senha Atual', 'required');
            $this->form_validation->set_rules('senha_nova', 'Nova Senha', 'required|min_length[6]');
            $this->form_validation->set_rules('senha_confirmar', 'Confirmar Senha', 'required|matches[senha_nova]');

            if ($this->form_validation->run()) {
                $usuario_id = $this->session->userdata('usuario_id');
                $usuario = $this->Usuario_model->get_by_id($usuario_id);

                // Verificar senha atual
                if (password_verify($this->input->post('senha_atual'), $usuario->senha)) {
                    $nova_senha = password_hash($this->input->post('senha_nova'), PASSWORD_DEFAULT);

                    if ($this->Usuario_model->update($usuario_id, ['senha' => $nova_senha])) {
                        $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
                        redirect('agenda/perfil');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao alterar senha.');
                    }
                } else {
                    $this->session->set_flashdata('erro', 'Senha atual incorreta.');
                }
            }
        }

        redirect('agenda/perfil');
    }
}
