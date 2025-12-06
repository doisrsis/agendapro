<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Clientes
 *
 * Gerenciamento de clientes dos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Clientes extends Admin_Controller {

    protected $modulo_atual = 'clientes';

    public function __construct() {
        parent::__construct();
        $this->load->model('Cliente_model');
        $this->load->model('Estabelecimento_model');
        $this->load->library('upload');
    }

    /**
     * Listagem de clientes
     */
    public function index() {
        $data['titulo'] = 'Clientes';
        $data['menu_ativo'] = 'clientes';

        $filtros = [];

        if ($this->input->get('estabelecimento_id')) {
            $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
        }

        if ($this->input->get('tipo')) {
            $filtros['tipo'] = $this->input->get('tipo');
        }

        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }

        $data['clientes'] = $this->Cliente_model->get_all($filtros);
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        $data['filtros'] = $filtros;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Visualizar cliente
     */
    public function visualizar($id) {
        $cliente = $this->Cliente_model->get_by_id($id);

        if (!$cliente) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('admin/clientes');
        }

        $data['titulo'] = 'Detalhes do Cliente';
        $data['menu_ativo'] = 'clientes';
        $data['cliente'] = $cliente;
        $data['historico'] = $this->Cliente_model->get_historico_agendamentos($id, 20);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/visualizar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo cliente
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required|integer');
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'required|max_length[20]');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->input->post('estabelecimento_id'),
                    'nome' => $this->input->post('nome'),
                    'cpf' => $this->input->post('cpf'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                    'tipo' => $this->input->post('tipo') ?: 'novo',
                ];

                // Upload de foto
                if (!empty($_FILES['foto']['name'])) {
                    $foto = $this->fazer_upload_foto();
                    if ($foto) {
                        $dados['foto'] = $foto;
                    }
                }

                $id = $this->Cliente_model->create($dados);

                if ($id) {
                    $this->session->set_flashdata('sucesso', 'Cliente criado com sucesso!');
                    redirect('admin/clientes');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar cliente.');
                }
            }
        }

        $data['titulo'] = 'Novo Cliente';
        $data['menu_ativo'] = 'clientes';
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar cliente
     */
    public function editar($id) {
        $cliente = $this->Cliente_model->get_by_id($id);

        if (!$cliente) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('admin/clientes');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'required|max_length[20]');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'cpf' => $this->input->post('cpf'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                    'tipo' => $this->input->post('tipo'),
                ];

                // Upload de foto
                if (!empty($_FILES['foto']['name'])) {
                    $foto = $this->fazer_upload_foto();
                    if ($foto) {
                        if ($cliente->foto && file_exists('./uploads/clientes/' . $cliente->foto)) {
                            unlink('./uploads/clientes/' . $cliente->foto);
                        }
                        $dados['foto'] = $foto;
                    }
                }

                if ($this->Cliente_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Cliente atualizado com sucesso!');
                    redirect('admin/clientes');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar cliente.');
                }
            }
        }

        $data['titulo'] = 'Editar Cliente';
        $data['menu_ativo'] = 'clientes';
        $data['cliente'] = $cliente;
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Deletar cliente
     */
    public function deletar($id) {
        $cliente = $this->Cliente_model->get_by_id($id);

        if (!$cliente) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('admin/clientes');
        }

        if ($this->Cliente_model->delete($id)) {
            if ($cliente->foto && file_exists('./uploads/clientes/' . $cliente->foto)) {
                unlink('./uploads/clientes/' . $cliente->foto);
            }

            $this->session->set_flashdata('sucesso', 'Cliente deletado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar cliente.');
        }

        redirect('admin/clientes');
    }

    /**
     * Fazer upload de foto
     */
    private function fazer_upload_foto() {
        $config['upload_path'] = './uploads/clientes/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, TRUE);
        }

        $this->upload->initialize($config);

        if ($this->upload->do_upload('foto')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }

        return false;
    }
}
