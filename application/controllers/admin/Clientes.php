<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Clientes
 * 
 * Gerencia o CRUD de clientes do sistema
 * 
 * @author Rafael Dias - doisr.com.br
 * @date 15/11/2024
 */
class Clientes extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Cliente_model');
        $this->load->model('Orcamento_model');
        $this->load->library('pagination');
    }

    /**
     * Listagem de clientes
     */
    public function index() {
        // Evitar cache
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $data['titulo'] = 'Clientes';
        $data['menu_ativo'] = 'clientes';
        
        // Busca e filtros
        $busca = $this->input->get('busca');
        $status = $this->input->get('status');
        $ordem = $this->input->get('ordem') ?? 'recente';
        
        // Configuração de paginação
        $config['base_url'] = base_url('admin/clientes/index');
        $config['total_rows'] = $this->Cliente_model->count_all($busca, $status);
        $config['per_page'] = 20;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        
        // Estilo da paginação
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');
        
        $this->pagination->initialize($config);
        
        $page = $this->input->get('page') ?? 1;
        $offset = ($page - 1) * $config['per_page'];
        
        // Buscar clientes
        $data['clientes'] = $this->Cliente_model->get_all($config['per_page'], $offset, $busca, $status, $ordem);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];
        $data['busca'] = $busca;
        $data['status'] = $status;
        $data['ordem'] = $ordem;
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Visualizar cliente
     */
    public function visualizar($id) {
        $data['titulo'] = 'Detalhes do Cliente';
        $data['menu_ativo'] = 'clientes';
        
        $data['cliente'] = $this->Cliente_model->get($id);
        
        if (!$data['cliente']) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('admin/clientes');
        }
        
        // Buscar orçamentos do cliente
        $data['orcamentos'] = $this->Orcamento_model->get_by_cliente($id);
        
        // Estatísticas
        $data['total_orcamentos'] = count($data['orcamentos']);
        $data['valor_total'] = array_sum(array_column($data['orcamentos'], 'valor_total'));
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/visualizar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar cliente
     */
    public function editar($id) {
        $data['titulo'] = 'Editar Cliente';
        $data['menu_ativo'] = 'clientes';
        
        $data['cliente'] = $this->Cliente_model->get($id);
        
        if (!$data['cliente']) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado.');
            redirect('admin/clientes');
        }
        
        if ($this->input->method() === 'post') {
            $this->salvar_cliente($id);
            return;
        }
        
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/clientes/editar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Salvar cliente (criar ou editar)
     */
    private function salvar_cliente($id = null) {
        // Validação
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|max_length[100]');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required|max_length[20]');
        $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'max_length[20]');
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('admin/clientes/editar/' . $id);
            return;
        }
        
        $dados = [
            'nome' => $this->input->post('nome'),
            'email' => $this->input->post('email'),
            'telefone' => $this->input->post('telefone'),
            'whatsapp' => $this->input->post('whatsapp'),
            'cpf_cnpj' => $this->input->post('cpf_cnpj'),
            'endereco' => $this->input->post('endereco'),
            'cidade' => $this->input->post('cidade'),
            'estado' => $this->input->post('estado'),
            'cep' => $this->input->post('cep'),
            'observacoes' => $this->input->post('observacoes')
        ];
        
        if ($id) {
            // Buscar dados antigos
            $dados_antigos = $this->Cliente_model->get($id);
            
            // Atualizar
            if ($this->Cliente_model->update($id, $dados)) {
                // Registrar log
                $this->registrar_log('editar', 'clientes', $id, $dados_antigos, $dados);
                
                $this->session->set_flashdata('sucesso', 'Cliente atualizado com sucesso!');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao atualizar cliente.');
            }
            redirect('admin/clientes/visualizar/' . $id);
        } else {
            // Criar (não usado por enquanto, clientes são criados pelo orçamento)
            $novo_id = $this->Cliente_model->insert($dados);
            if ($novo_id) {
                $this->session->set_flashdata('sucesso', 'Cliente criado com sucesso!');
                redirect('admin/clientes/visualizar/' . $novo_id);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao criar cliente.');
                redirect('admin/clientes');
            }
        }
    }

    /**
     * Excluir cliente
     */
    public function excluir($id) {
        // Verificar se cliente tem orçamentos
        $orcamentos = $this->Orcamento_model->get_by_cliente($id);
        
        if (count($orcamentos) > 0) {
            $this->session->set_flashdata('erro', 'Não é possível excluir este cliente pois ele possui orçamentos cadastrados.');
            redirect('admin/clientes/visualizar/' . $id);
            return;
        }
        
        // Buscar dados antes de excluir
        $dados_antigos = $this->Cliente_model->get($id);
        
        if ($this->Cliente_model->delete($id)) {
            // Registrar log
            $this->registrar_log('deletar', 'clientes', $id, $dados_antigos);
            
            $this->session->set_flashdata('sucesso', 'Cliente excluído com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao excluir cliente.');
        }
        
        redirect('admin/clientes');
    }

    /**
     * Exportar clientes para CSV
     */
    public function exportar() {
        $busca = $this->input->get('busca');
        $status = $this->input->get('status');
        
        $clientes = $this->Cliente_model->get_all(null, null, $busca, $status);
        
        // Configurar headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=clientes_' . date('Y-m-d_His') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho
        fputcsv($output, ['ID', 'Nome', 'E-mail', 'Telefone', 'WhatsApp', 'Cidade', 'Estado', 'Cadastrado em'], ';');
        
        // Dados
        foreach ($clientes as $cliente) {
            fputcsv($output, [
                $cliente->id,
                $cliente->nome,
                $cliente->email,
                $cliente->telefone,
                $cliente->whatsapp,
                $cliente->cidade,
                $cliente->estado,
                date('d/m/Y H:i', strtotime($cliente->criado_em))
            ], ';');
        }
        
        fclose($output);
        exit;
    }
}
