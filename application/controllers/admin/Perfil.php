<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Perfil
 * 
 * Permite que o usuário altere sua própria senha
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 15/11/2024
 */
class Perfil extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
    }

    /**
     * Página de perfil (alterar senha)
     */
    public function index() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Meu Perfil';
        $data['menu_ativo'] = 'perfil';
        
        $usuario_id = $this->session->userdata('usuario_id');
        $data['usuario'] = $this->Usuario_model->get($usuario_id);
        
        if ($this->input->method() === 'post') {
            $this->alterar_senha($usuario_id);
            return;
        }
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/perfil/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Alterar senha do usuário logado
     */
    private function alterar_senha($usuario_id) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('senha_atual', 'Senha Atual', 'required');
        $this->form_validation->set_rules('nova_senha', 'Nova Senha', 'required|min_length[6]');
        $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'required|matches[nova_senha]');
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('admin/perfil');
            return;
        }
        
        // Verificar senha atual
        $usuario = $this->Usuario_model->get($usuario_id);
        
        if (!password_verify($this->input->post('senha_atual'), $usuario->senha)) {
            $this->session->set_flashdata('erro', 'Senha atual incorreta.');
            redirect('admin/perfil');
            return;
        }
        
        // Atualizar senha (o model já faz o hash)
        if ($this->Usuario_model->update($usuario_id, ['senha' => $this->input->post('nova_senha')])) {
            // Registrar log
            $this->registrar_log('alterar_senha', 'usuarios', $usuario_id);
            
            $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao alterar senha.');
        }
        
        redirect('admin/perfil');
    }
}
