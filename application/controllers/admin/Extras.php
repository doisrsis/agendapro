<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Extras
 * 
 * Gerencia extras como Blackout, Motorização, Instalação, etc
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 13/11/2024 19:10
 */
class Extras extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Extra_model');
        $this->load->model('Produto_model');
    }

    /**
     * Listar extras
     */
    public function index() {
        $data['titulo'] = 'Extras - Le Cortine';
        $data['menu_ativo'] = 'extras';
        
        // Filtros
        $filtros = [];
        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }
        if ($this->input->get('tipo_preco')) {
            $filtros['tipo_preco'] = $this->input->get('tipo_preco');
        }
        if ($this->input->get('busca')) {
            $filtros['busca'] = $this->input->get('busca');
        }
        
        $data['extras'] = $this->Extra_model->get_all($filtros);
        $data['total'] = $this->Extra_model->count_all($filtros);
        $data['filtros'] = $filtros;
        
        // Carregar views
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/extras/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Criar extra
     */
    public function criar() {
        $data['titulo'] = 'Novo Extra - Le Cortine';
        $data['menu_ativo'] = 'extras';
        $data['produtos'] = $this->Produto_model->get_all(['status' => 'ativo']);
        
        // Processar formulário
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('tipo_preco', 'Tipo de Preço', 'required|in_list[fixo,percentual,por_m2]');
            $this->form_validation->set_rules('valor', 'Valor', 'required|numeric');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[ativo,inativo]');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'tipo_preco' => $this->input->post('tipo_preco'),
                    'valor' => $this->input->post('valor'),
                    'ordem' => $this->input->post('ordem') ?: 0,
                    'status' => $this->input->post('status')
                ];

                // Produtos aplicáveis (JSON)
                $produtos_aplicaveis = $this->input->post('produtos_aplicaveis');
                if (!empty($produtos_aplicaveis) && is_array($produtos_aplicaveis)) {
                    $dados['aplicavel_a'] = json_encode($produtos_aplicaveis);
                } else {
                    $dados['aplicavel_a'] = null; // Aplicável a todos
                }

                $id = $this->Extra_model->insert($dados);

                if ($id) {
                    $this->registrar_log('criar', 'extras', $id, null, $dados);
                    $this->session->set_flashdata('sucesso', 'Extra criado com sucesso!');
                    redirect('admin/extras');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar extra.');
                }
            }
        }
        
        // Carregar views
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/extras/form', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Editar extra
     */
    public function editar($id) {
        $extra = $this->Extra_model->get($id);
        
        if (!$extra) {
            show_404();
        }
        
        $data['titulo'] = 'Editar Extra - Le Cortine';
        $data['menu_ativo'] = 'extras';
        $data['extra'] = $extra;
        $data['produtos'] = $this->Produto_model->get_all(['status' => 'ativo']);
        
        // Decodificar produtos aplicáveis
        $data['produtos_aplicaveis'] = [];
        if (!empty($extra->aplicavel_a)) {
            $data['produtos_aplicaveis'] = json_decode($extra->aplicavel_a, true) ?: [];
        }
        
        // Processar formulário
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('tipo_preco', 'Tipo de Preço', 'required|in_list[fixo,percentual,por_m2]');
            $this->form_validation->set_rules('valor', 'Valor', 'required|numeric');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[ativo,inativo]');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'descricao' => $this->input->post('descricao'),
                    'tipo_preco' => $this->input->post('tipo_preco'),
                    'valor' => $this->input->post('valor'),
                    'ordem' => $this->input->post('ordem') ?: 0,
                    'status' => $this->input->post('status')
                ];

                // Produtos aplicáveis (JSON)
                $produtos_aplicaveis = $this->input->post('produtos_aplicaveis');
                if (!empty($produtos_aplicaveis) && is_array($produtos_aplicaveis)) {
                    $dados['aplicavel_a'] = json_encode($produtos_aplicaveis);
                } else {
                    $dados['aplicavel_a'] = null; // Aplicável a todos
                }

                if ($this->Extra_model->update($id, $dados)) {
                    $this->registrar_log('editar', 'extras', $id, $extra, $dados);
                    $this->session->set_flashdata('sucesso', 'Extra atualizado com sucesso!');
                    redirect('admin/extras');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar extra.');
                }
            }
        }
        
        // Carregar views
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/extras/form', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Deletar extra
     */
    public function deletar($id) {
        if ($this->input->method() !== 'post') {
            redirect('admin/extras');
        }

        $extra = $this->Extra_model->get($id);
        
        if (!$extra) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Extra não encontrado.']);
            return;
        }

        if ($this->Extra_model->delete($id)) {
            $this->registrar_log('deletar', 'extras', $id, $extra, null);
            
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => true, 'message' => 'Extra deletado com sucesso!']);
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar extra.']);
        }
    }

    /**
     * Toggle status via AJAX
     */
    public function toggle_status($id) {
        if ($this->input->method() !== 'post') {
            redirect('admin/extras');
        }

        $extra = $this->Extra_model->get($id);
        
        if (!$extra) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Extra não encontrado.']);
            return;
        }

        if ($this->Extra_model->toggle_status($id)) {
            $novo_status = ($extra->status === 'ativo') ? 'inativo' : 'ativo';
            $this->registrar_log('toggle_status', 'extras', $id, ['status' => $extra->status], ['status' => $novo_status]);
            
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => true,
                'status' => $novo_status,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status.']);
        }
    }

    /**
     * Reordenar extras via AJAX
     */
    public function reordenar() {
        if ($this->input->method() !== 'post') {
            redirect('admin/extras');
        }

        $ordem = $this->input->post('ordem');
        
        if (!is_array($ordem)) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
            return;
        }

        $sucesso = true;
        foreach ($ordem as $index => $id) {
            if (!$this->Extra_model->atualizar_ordem($id, $index + 1)) {
                $sucesso = false;
            }
        }

        if ($sucesso) {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => true, 'message' => 'Ordem atualizada com sucesso!']);
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar ordem.']);
        }
    }

}
