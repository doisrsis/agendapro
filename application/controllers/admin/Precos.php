<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Preços
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024 19:35
 */
class Precos extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Preco_model');
        $this->load->model('Produto_model');
    }

    /**
     * Listar preços
     */
    public function index() {
        $data['titulo'] = 'Tabela de Preços - Le Cortine';
        $data['menu_ativo'] = 'precos';
        
        // Filtros
        $filtros = [];
        if ($this->input->get('produto_id')) {
            $filtros['produto_id'] = $this->input->get('produto_id');
        }
        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }
        
        $data['precos'] = $this->Preco_model->get_all($filtros);
        $data['total'] = $this->Preco_model->count_all($filtros);
        $data['produtos'] = $this->Produto_model->get_all(['status' => 'ativo']);
        $data['filtros'] = $filtros;
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/precos/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Criar preço
     */
    public function criar() {
        $data['titulo'] = 'Novo Preço - Le Cortine';
        $data['menu_ativo'] = 'precos';
        $data['produtos'] = $this->Produto_model->get_all(['status' => 'ativo']);
        
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('produto_id', 'Produto', 'required|integer');
            $this->form_validation->set_rules('largura_min', 'Largura Mínima', 'required|numeric');
            $this->form_validation->set_rules('largura_max', 'Largura Máxima', 'required|numeric');
            $this->form_validation->set_rules('altura_min', 'Altura Mínima', 'required|numeric');
            $this->form_validation->set_rules('altura_max', 'Altura Máxima', 'required|numeric');

            if ($this->form_validation->run()) {
                $dados = [
                    'produto_id' => $this->input->post('produto_id'),
                    'largura_min' => $this->input->post('largura_min'),
                    'largura_max' => $this->input->post('largura_max'),
                    'altura_min' => $this->input->post('altura_min'),
                    'altura_max' => $this->input->post('altura_max'),
                    'preco_m2' => $this->input->post('preco_m2') ?: null,
                    'preco_ml' => $this->input->post('preco_ml') ?: null,
                    'preco_fixo' => $this->input->post('preco_fixo') ?: null,
                    'observacoes' => $this->input->post('observacoes')
                ];

                $id = $this->Preco_model->insert($dados);

                if ($id) {
                    $this->registrar_log('criar', 'precos', $id, null, $dados);
                    $this->session->set_flashdata('sucesso', 'Preço criado com sucesso!');
                    redirect('admin/precos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar preço.');
                }
            }
        }
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/precos/form', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Editar preço
     */
    public function editar($id) {
        $preco = $this->Preco_model->get($id);
        
        if (!$preco) {
            show_404();
        }
        
        $data['titulo'] = 'Editar Preço - Le Cortine';
        $data['menu_ativo'] = 'precos';
        $data['preco'] = $preco;
        $data['produtos'] = $this->Produto_model->get_all(['status' => 'ativo']);
        
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('produto_id', 'Produto', 'required|integer');
            $this->form_validation->set_rules('largura_min', 'Largura Mínima', 'required|numeric');
            $this->form_validation->set_rules('largura_max', 'Largura Máxima', 'required|numeric');
            $this->form_validation->set_rules('altura_min', 'Altura Mínima', 'required|numeric');
            $this->form_validation->set_rules('altura_max', 'Altura Máxima', 'required|numeric');

            if ($this->form_validation->run()) {
                $dados = [
                    'produto_id' => $this->input->post('produto_id'),
                    'largura_min' => $this->input->post('largura_min'),
                    'largura_max' => $this->input->post('largura_max'),
                    'altura_min' => $this->input->post('altura_min'),
                    'altura_max' => $this->input->post('altura_max'),
                    'preco_m2' => $this->input->post('preco_m2') ?: null,
                    'preco_ml' => $this->input->post('preco_ml') ?: null,
                    'preco_fixo' => $this->input->post('preco_fixo') ?: null,
                    'observacoes' => $this->input->post('observacoes')
                ];

                if ($this->Preco_model->update($id, $dados)) {
                    $this->registrar_log('editar', 'precos', $id, $preco, $dados);
                    $this->session->set_flashdata('sucesso', 'Preço atualizado com sucesso!');
                    redirect('admin/precos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar preço.');
                }
            }
        }
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/precos/form', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Deletar preço
     */
    public function deletar($id) {
        if ($this->input->method() !== 'post') {
            redirect('admin/precos');
        }

        $preco = $this->Preco_model->get($id);
        
        if (!$preco) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Preço não encontrado.']);
            return;
        }

        if ($this->Preco_model->delete($id)) {
            $this->registrar_log('deletar', 'precos', $id, $preco, null);
            
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => true, 'message' => 'Preço deletado com sucesso!']);
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar preço.']);
        }
    }

}
