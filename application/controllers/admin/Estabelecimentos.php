<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Estabelecimentos
 *
 * Gerenciamento de estabelecimentos do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Estabelecimentos extends Admin_Controller {

    protected $modulo_atual = 'estabelecimentos';

    public function __construct() {
        parent::__construct();
        $this->load->model('Estabelecimento_model');
        $this->load->library('upload');
    }

    /**
     * Listagem de estabelecimentos
     */
    public function index() {
        $data['titulo'] = 'Estabelecimentos';
        $data['menu_ativo'] = 'estabelecimentos';

        // Filtros
        $filtros = [];

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('plano')) {
            $filtros['plano'] = $this->input->get('plano');
        }

        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }

        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all($filtros);
        $data['filtros'] = $filtros;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/estabelecimentos/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo estabelecimento
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[200]');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');
            $this->form_validation->set_rules('plano', 'Plano', 'required|in_list[trimestral,semestral,anual]');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'cnpj_cpf' => $this->input->post('cnpj_cpf'),
                    'endereco' => $this->input->post('endereco'),
                    'cep' => $this->input->post('cep'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'telefone' => $this->input->post('telefone'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'email' => $this->input->post('email'),
                    'plano' => $this->input->post('plano'),
                    'plano_vencimento' => $this->input->post('plano_vencimento'),
                    'tempo_minimo_agendamento' => $this->input->post('tempo_minimo_agendamento') ?: 60,
                    'status' => $this->input->post('status') ?: 'ativo',
                ];

                // Upload de logo
                if (!empty($_FILES['logo']['name'])) {
                    $logo = $this->fazer_upload_logo();
                    if ($logo) {
                        $dados['logo'] = $logo;
                    }
                }

                $id = $this->Estabelecimento_model->create($dados);

                if ($id) {
                    $this->session->set_flashdata('sucesso', 'Estabelecimento criado com sucesso!');
                    redirect('admin/estabelecimentos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar estabelecimento.');
                }
            }
        }

        $data['titulo'] = 'Novo Estabelecimento';
        $data['menu_ativo'] = 'estabelecimentos';

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/estabelecimentos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar estabelecimento
     */
    public function editar($id) {
        $estabelecimento = $this->Estabelecimento_model->get_by_id($id);

        if (!$estabelecimento) {
            $this->session->set_flashdata('erro', 'Estabelecimento não encontrado.');
            redirect('admin/estabelecimentos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[200]');
            $this->form_validation->set_rules('email', 'E-mail', 'valid_email');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'cnpj_cpf' => $this->input->post('cnpj_cpf'),
                    'endereco' => $this->input->post('endereco'),
                    'cep' => $this->input->post('cep'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'telefone' => $this->input->post('telefone'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'email' => $this->input->post('email'),
                    'plano' => $this->input->post('plano'),
                    'plano_vencimento' => $this->input->post('plano_vencimento'),
                    'tempo_minimo_agendamento' => $this->input->post('tempo_minimo_agendamento'),
                    'status' => $this->input->post('status'),
                ];

                // Upload de logo
                if (!empty($_FILES['logo']['name'])) {
                    $logo = $this->fazer_upload_logo();
                    if ($logo) {
                        // Deletar logo antiga
                        if ($estabelecimento->logo && file_exists('./uploads/logos/' . $estabelecimento->logo)) {
                            unlink('./uploads/logos/' . $estabelecimento->logo);
                        }
                        $dados['logo'] = $logo;
                    }
                }

                if ($this->Estabelecimento_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Estabelecimento atualizado com sucesso!');
                    redirect('admin/estabelecimentos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar estabelecimento.');
                }
            }
        }

        $data['titulo'] = 'Editar Estabelecimento';
        $data['menu_ativo'] = 'estabelecimentos';
        $data['estabelecimento'] = $estabelecimento;

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/estabelecimentos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Deletar estabelecimento
     */
    public function deletar($id) {
        $estabelecimento = $this->Estabelecimento_model->get_by_id($id);

        if (!$estabelecimento) {
            $this->session->set_flashdata('erro', 'Estabelecimento não encontrado.');
            redirect('admin/estabelecimentos');
        }

        if ($this->Estabelecimento_model->delete($id)) {
            // Deletar logo
            if ($estabelecimento->logo && file_exists('./uploads/logos/' . $estabelecimento->logo)) {
                unlink('./uploads/logos/' . $estabelecimento->logo);
            }

            $this->session->set_flashdata('sucesso', 'Estabelecimento deletado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar estabelecimento.');
        }

        redirect('admin/estabelecimentos');
    }

    /**
     * Fazer upload de logo
     */
    private function fazer_upload_logo() {
        $config['upload_path'] = './uploads/logos/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;

        // Criar diretório se não existir
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, TRUE);
        }

        $this->upload->initialize($config);

        if ($this->upload->do_upload('logo')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }

        return false;
    }
}
