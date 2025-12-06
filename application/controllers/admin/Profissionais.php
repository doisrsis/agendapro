<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Profissionais
 *
 * Gerenciamento de profissionais dos estabelecimentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Profissionais extends Admin_Controller {

    protected $modulo_atual = 'profissionais';

    public function __construct() {
        parent::__construct();
        $this->load->model('Profissional_model');
        $this->load->model('Estabelecimento_model');
        $this->load->model('Servico_model');
        $this->load->library('upload');
    }

    /**
     * Listagem de profissionais
     */
    public function index() {
        $data['titulo'] = 'Profissionais';
        $data['menu_ativo'] = 'profissionais';

        $filtros = [];

        if ($this->input->get('estabelecimento_id')) {
            $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
        }

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }

        $data['profissionais'] = $this->Profissional_model->get_all($filtros);
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        $data['filtros'] = $filtros;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/profissionais/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo profissional
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required|integer');
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->input->post('estabelecimento_id'),
                    'nome' => $this->input->post('nome'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                    'status' => $this->input->post('status') ?: 'ativo',
                ];

                // Upload de foto
                if (!empty($_FILES['foto']['name'])) {
                    $foto = $this->fazer_upload_foto();
                    if ($foto) {
                        $dados['foto'] = $foto;
                    }
                }

                $id = $this->Profissional_model->create($dados);

                if ($id) {
                    // Vincular serviços
                    $servicos_ids = $this->input->post('servicos') ?: [];
                    $this->Profissional_model->vincular_servicos($id, $servicos_ids);

                    $this->session->set_flashdata('sucesso', 'Profissional criado com sucesso!');
                    redirect('admin/profissionais');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar profissional.');
                }
            }
        }

        $data['titulo'] = 'Novo Profissional';
        $data['menu_ativo'] = 'profissionais';
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        $data['servicos'] = [];

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/profissionais/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar profissional
     */
    public function editar($id) {
        $profissional = $this->Profissional_model->get_by_id($id);

        if (!$profissional) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('admin/profissionais');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telefone' => $this->input->post('telefone'),
                    'email' => $this->input->post('email'),
                    'status' => $this->input->post('status'),
                ];

                // Upload de foto
                if (!empty($_FILES['foto']['name'])) {
                    $foto = $this->fazer_upload_foto();
                    if ($foto) {
                        if ($profissional->foto && file_exists('./uploads/profissionais/' . $profissional->foto)) {
                            unlink('./uploads/profissionais/' . $profissional->foto);
                        }
                        $dados['foto'] = $foto;
                    }
                }

                if ($this->Profissional_model->update($id, $dados)) {
                    // Vincular serviços
                    $servicos_ids = $this->input->post('servicos') ?: [];
                    $this->Profissional_model->vincular_servicos($id, $servicos_ids);

                    $this->session->set_flashdata('sucesso', 'Profissional atualizado com sucesso!');
                    redirect('admin/profissionais');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar profissional.');
                }
            }
        }

        $data['titulo'] = 'Editar Profissional';
        $data['menu_ativo'] = 'profissionais';
        $data['profissional'] = $profissional;
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $profissional->estabelecimento_id, 'status' => 'ativo']);
        $data['servicos_vinculados'] = array_column($this->Profissional_model->get_servicos($id), 'id');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/profissionais/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Deletar profissional
     */
    public function deletar($id) {
        $profissional = $this->Profissional_model->get_by_id($id);

        if (!$profissional) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('admin/profissionais');
        }

        if ($this->Profissional_model->delete($id)) {
            if ($profissional->foto && file_exists('./uploads/profissionais/' . $profissional->foto)) {
                unlink('./uploads/profissionais/' . $profissional->foto);
            }

            $this->session->set_flashdata('sucesso', 'Profissional deletado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar profissional.');
        }

        redirect('admin/profissionais');
    }

    /**
     * Buscar serviços do estabelecimento via AJAX
     */
    public function get_servicos($estabelecimento_id) {
        $servicos = $this->Servico_model->get_all([
            'estabelecimento_id' => $estabelecimento_id,
            'status' => 'ativo'
        ]);

        header('Content-Type: application/json');
        echo json_encode($servicos);
    }

    /**
     * Fazer upload de foto
     */
    private function fazer_upload_foto() {
        $config['upload_path'] = './uploads/profissionais/';
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
