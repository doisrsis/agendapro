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
        $filtros = ['estabelecimento_id' => $this->estabelecimento_id];

        // Configuração da Paginação
        $this->load->library('pagination');
        $config['base_url'] = base_url('painel/profissionais/index');
        $config['total_rows'] = $this->Profissional_model->count_all($filtros);
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Estilização do Bootstrap 5 / Tabler
        $config['full_tag_open'] = '<ul class="pagination m-0 ms-auto">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '<i class="ti ti-chevrons-left"></i>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '<i class="ti ti-chevrons-right"></i>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '<i class="ti ti-chevron-right"></i>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="ti ti-chevron-left"></i>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $page = $this->input->get('page') ? (int)$this->input->get('page') : 0;
        $config['cur_page'] = $page;

        $this->pagination->initialize($config);

        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id, $config['per_page'], $page);
        $data['total'] = $config['total_rows'];
        $data['filtros'] = $filtros;
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/profissionais/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar profissional
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            log_message('debug', 'Painel/Profissionais/criar - POST recebido');

            $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');

            if ($this->form_validation->run()) {
                log_message('debug', 'Painel/Profissionais/criar - Validação OK');

                // Verificar se email já existe
                if ($this->Usuario_model->email_existe($this->input->post('email'))) {
                    log_message('debug', 'Painel/Profissionais/criar - Email já existe');
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

                log_message('debug', 'Painel/Profissionais/criar - Dados: ' . json_encode($dados_profissional));

                $profissional_id = $this->Profissional_model->criar($dados_profissional);
                log_message('debug', 'Painel/Profissionais/criar - ID retornado: ' . $profissional_id);

                if ($profissional_id) {
                    // Vincular serviços ao profissional
                    $servicos = $this->input->post('servicos');
                    log_message('debug', 'Painel/Profissionais/criar - Serviços: ' . json_encode($servicos));

                    if (!empty($servicos) && is_array($servicos)) {
                        $this->Profissional_model->vincular_servicos($profissional_id, $servicos);
                        log_message('debug', 'Painel/Profissionais/criar - Serviços vinculados');
                    }

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

                    $usuario_id = $this->Usuario_model->criar($dados_usuario);
                    log_message('debug', 'Painel/Profissionais/criar - Usuário criado: ' . $usuario_id);

                    $this->session->set_flashdata('sucesso', 'Profissional criado com sucesso!');
                    redirect('painel/profissionais');
                } else {
                    log_message('error', 'Painel/Profissionais/criar - Falha ao criar profissional');
                    $this->session->set_flashdata('erro', 'Erro ao criar profissional.');
                }
            } else {
                log_message('debug', 'Painel/Profissionais/criar - Validação falhou: ' . validation_errors());
            }
        }

        $data['titulo'] = 'Novo Profissional';
        $data['menu_ativo'] = 'profissionais';

        // Carregar serviços do estabelecimento
        $this->load->model('Servico_model');
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['servicos_vinculados'] = [];

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
                    'whatsapp' => $this->input->post('whatsapp'),
                    'email' => $this->input->post('email'),
                ];

                if ($this->Profissional_model->atualizar($id, $dados)) {
                    // Atualizar serviços vinculados
                    $servicos = $this->input->post('servicos');
                    if (is_array($servicos)) {
                        $this->Profissional_model->vincular_servicos($id, $servicos);
                    } else {
                        // Se nenhum serviço foi selecionado, remover todos
                        $this->Profissional_model->vincular_servicos($id, []);
                    }

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

        // Carregar serviços do estabelecimento
        $this->load->model('Servico_model');
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        // Carregar serviços vinculados ao profissional (extrair apenas IDs)
        $servicos_obj = $this->Profissional_model->get_servicos($id);
        $data['servicos_vinculados'] = array_column($servicos_obj, 'id');

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
