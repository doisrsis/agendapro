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

        // Configuração da Paginação
        $this->load->library('pagination');
        $config['base_url'] = base_url('admin/estabelecimentos/index');
        $config['total_rows'] = $this->Estabelecimento_model->count_all($filtros);
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

        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all($filtros, $config['per_page'], $page);
        $data['total'] = $config['total_rows'];
        $data['filtros'] = $filtros;
        $data['pagination'] = $this->pagination->create_links();

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
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');
            $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'required|matches[senha]');
            $this->form_validation->set_rules('plano_id', 'Plano', 'required|integer');

            if ($this->form_validation->run()) {
                // Verificar se email já existe
                $this->load->model('Usuario_model');
                if ($this->Usuario_model->email_existe($this->input->post('email'))) {
                    $this->session->set_flashdata('erro', 'Este e-mail já está cadastrado.');
                    redirect('admin/estabelecimentos/criar');
                    return;
                }

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
                    // Criar usuário automaticamente
                    $dados_usuario = [
                        'nome' => $this->input->post('nome'),
                        'email' => $this->input->post('email'),
                        'telefone' => $this->input->post('telefone'),
                        'senha' => $this->input->post('senha'),
                        'tipo' => 'estabelecimento',
                        'estabelecimento_id' => $id,
                        'ativo' => 1
                    ];

                    $usuario_id = $this->Usuario_model->criar($dados_usuario);

                    // Criar assinatura automaticamente
                    $this->load->model('Plano_model');
                    $this->load->model('Assinatura_model');

                    $plano_id = $this->input->post('plano_id');
                    $plano = $this->Plano_model->get($plano_id);

                    if ($plano) {
                        $dados_assinatura = [
                            'estabelecimento_id' => $id,
                            'plano_id' => $plano_id,
                            'data_inicio' => date('Y-m-d'),
                            'data_fim' => date('Y-m-d', strtotime('+' . $plano->trial_dias . ' days')),
                            'status' => 'trial',
                            'valor_pago' => 0.00,
                            'auto_renovar' => 1
                        ];

                        $this->Assinatura_model->criar($dados_assinatura);
                    }

                    if ($usuario_id) {
                        $this->session->set_flashdata('sucesso', 'Estabelecimento, usuário e assinatura criados! Credenciais: ' . $this->input->post('email'));
                    } else {
                        $this->session->set_flashdata('sucesso', 'Estabelecimento criado, mas houve erro ao criar usuário.');
                    }

                    redirect('admin/estabelecimentos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar estabelecimento.');
                }
            }
        }

        $data['titulo'] = 'Novo Estabelecimento';
        $data['menu_ativo'] = 'estabelecimentos';

        // Carregar planos ativos
        $this->load->model('Plano_model');
        $data['planos'] = $this->Plano_model->get_all(true);

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
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('plano_id', 'Plano', 'required|integer');

            // Validar senha apenas se preenchida
            if ($this->input->post('senha')) {
                $this->form_validation->set_rules('senha', 'Senha', 'min_length[6]');
                $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'matches[senha]');
            }

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
                    // Atualizar usuário vinculado se existir
                    $this->load->model('Usuario_model');
                    $usuarios = $this->Usuario_model->get_all('estabelecimento');
                    $usuario = null;
                    foreach ($usuarios as $u) {
                        if ($u->estabelecimento_id == $id) {
                            $usuario = $u;
                            break;
                        }
                    }

                    if ($usuario) {
                        $dados_usuario = [
                            'nome' => $this->input->post('nome'),
                            'email' => $this->input->post('email'),
                            'telefone' => $this->input->post('telefone')
                        ];

                        // Atualizar senha se fornecida
                        if ($this->input->post('senha')) {
                            $dados_usuario['senha'] = $this->input->post('senha');
                        }

                        $this->Usuario_model->atualizar($usuario->id, $dados_usuario);
                    }
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar estabelecimento.');
                    redirect('admin/estabelecimentos'); // Redirect on error
                }

                // Verificar se o plano foi alterado
                $this->load->model('Assinatura_model');
                $this->load->model('Plano_model');

                $plano_id_novo = $this->input->post('plano_id');
                $assinatura_atual = $this->Assinatura_model->get_ativa_por_estabelecimento($id);

                if ($assinatura_atual && $assinatura_atual->plano_id != $plano_id_novo) {
                    // Plano foi alterado - criar nova assinatura
                    $plano = $this->Plano_model->get($plano_id_novo);

                    if ($plano) {
                        // Cancelar assinatura antiga
                        $this->Assinatura_model->atualizar($assinatura_atual->id, ['status' => 'cancelada']);

                        // Criar nova assinatura
                        $dados_assinatura = [
                            'estabelecimento_id' => $id,
                            'plano_id' => $plano_id_novo,
                            'data_inicio' => date('Y-m-d'),
                            'data_fim' => date('Y-m-d', strtotime('+30 days')),
                            'status' => 'ativa',
                            'valor_pago' => 0.00,
                            'auto_renovar' => 1
                        ];

                        $this->Assinatura_model->criar($dados_assinatura);
                        $this->session->set_flashdata('sucesso', 'Estabelecimento atualizado e plano alterado com sucesso!');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao alterar plano: Plano não encontrado.');
                    }
                } else {
                    $this->session->set_flashdata('sucesso', 'Estabelecimento atualizado com sucesso!');
                }

                redirect('admin/estabelecimentos');
            }
        }

        $data['titulo'] = 'Editar Estabelecimento';
        $data['menu_ativo'] = 'estabelecimentos';
        $data['estabelecimento'] = $estabelecimento;

        // Carregar planos e assinatura atual
        $this->load->model('Plano_model');
        $this->load->model('Assinatura_model');
        $data['planos'] = $this->Plano_model->get_all(true);
        $data['assinatura_atual'] = $this->Assinatura_model->get_ativa_por_estabelecimento($id);

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
