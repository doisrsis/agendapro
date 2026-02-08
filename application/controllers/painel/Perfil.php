<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Perfil do Estabelecimento
 *
 * Gerenciamento de dados do perfil do estabelecimento (Nome, Logo, Endereço, etc)
 * Refatorado de Configuracoes.php
 *
 * @author Rafael Dias - doisr.com.br
 * @date 08/02/2026
 */
class Perfil extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');
        $this->auth_check->check_tipo(['estabelecimento']);

        // Carregar models
        $this->load->model('Estabelecimento_model');

        // Obter dados do estabelecimento
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get_by_id($this->estabelecimento_id);
    }

    public function index() {
        if ($this->input->method() === 'post') {
            $this->salvar();
        }

        $data['titulo'] = 'Perfil do Estabelecimento';
        $data['menu_ativo'] = 'perfil'; // Novo menu ativo
        $data['estabelecimento'] = $this->estabelecimento;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/perfil/index', $data);
        $this->load->view('painel/layout/footer');
    }

    private function salvar() {
        $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
        $this->form_validation->set_rules('cnpj_cpf', 'CNPJ/CPF', 'max_length[18]');
        $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'max_length[20]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

        if ($this->form_validation->run()) {
            $dados = [
                'nome' => $this->input->post('nome'),
                'cnpj_cpf' => $this->input->post('cnpj_cpf'),
                'whatsapp' => $this->input->post('whatsapp'),
                'email' => $this->input->post('email'),
                'tema' => $this->input->post('tema'),
                'endereco' => $this->input->post('endereco'),
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'estado' => $this->input->post('estado'),
                'cep' => $this->input->post('cep'),
                // Slug logic maintained from Configuracoes.php
                'slug' => empty($this->estabelecimento->slug) ? url_title(convert_accented_characters($this->input->post('nome') . '-' . substr(md5(uniqid()), 0, 4)), '-', TRUE) : $this->estabelecimento->slug,
                'instagram' => $this->input->post('instagram'),
                'facebook' => $this->input->post('facebook'),
                'website' => $this->input->post('website')
            ];

            // Upload de Logo
            if (!empty($_FILES['logo']['name'])) {
                $config['upload_path'] = './assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('logo')) {
                    $upload_data = $this->upload->data();
                    $dados['logo'] = $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('erro', $this->upload->display_errors());
                    redirect('painel/perfil');
                    return;
                }
            }

            if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
                $this->session->set_flashdata('sucesso', 'Perfil atualizado com sucesso!');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao atualizar perfil.');
            }
        } else {
             $this->session->set_flashdata('erro', validation_errors());
        }

        redirect('painel/perfil');
    }
}
