<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Usuários
 *
 * Gerencia o CRUD de usuários do sistema com controle de permissões
 *
 * @author Rafael Dias - doisr.com.br
 * @date 15/11/2024
 */
class Usuarios extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->model('Estabelecimento_model');
        $this->load->model('Profissional_model');
        $this->load->library('pagination');

        // Apenas super_admin pode acessar
        if (!$this->auth_check->is_super_admin()) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para acessar esta área.');
            redirect('admin/dashboard');
        }
    }

    /**
     * Listagem de usuários
     */
    public function index() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');

        $data['titulo'] = 'Usuários';
        $data['menu_ativo'] = 'usuarios';

        // Filtros
        $busca = $this->input->get('busca');
        $tipo = $this->input->get('tipo');
        $ativo = $this->input->get('ativo');

        // Buscar usuários
        $filtros = [];
        if ($tipo) $filtros['tipo'] = $tipo;
        if ($ativo !== null && $ativo !== '') $filtros['ativo'] = $ativo;

        $data['usuarios'] = $this->Usuario_model->get_all_with_filters($busca, $filtros);
        $data['total'] = count($data['usuarios']);
        $data['busca'] = $busca;
        $data['tipo'] = $tipo;
        $data['ativo'] = $ativo;

        // Estabelecimentos para filtro
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/usuarios/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo usuário
     */
    public function criar() {
        $data['titulo'] = 'Novo Usuário';
        $data['menu_ativo'] = 'usuarios';

        if ($this->input->method() === 'post') {
            $this->salvar_usuario();
            return;
        }

        // Estabelecimentos e profissionais para seleção
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        $data['profissionais'] = [];

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/usuarios/criar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar usuário
     */
    public function editar($id) {
        $data['titulo'] = 'Editar Usuário';
        $data['menu_ativo'] = 'usuarios';

        $data['usuario'] = $this->Usuario_model->get($id);

        if (!$data['usuario']) {
            $this->session->set_flashdata('erro', 'Usuário não encontrado.');
            redirect('admin/usuarios');
        }

        if ($this->input->method() === 'post') {
            $this->salvar_usuario($id);
            return;
        }

        // Estabelecimentos e profissionais
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);

        if ($data['usuario']->estabelecimento_id) {
            $data['profissionais'] = $this->Profissional_model->get_all([
                'estabelecimento_id' => $data['usuario']->estabelecimento_id,
                'status' => 'ativo'
            ]);
        } else {
            $data['profissionais'] = [];
        }

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/usuarios/editar', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Salvar usuário (criar ou editar)
     */
    private function salvar_usuario($id = null) {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|max_length[100]');
        $this->form_validation->set_rules('telefone', 'Telefone', 'max_length[20]');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required|in_list[super_admin,estabelecimento,profissional]');

        // Validações condicionais por tipo
        $tipo = $this->input->post('tipo');

        if ($tipo == 'estabelecimento') {
            $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required|integer');
        }

        if ($tipo == 'profissional') {
            $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required|integer');
            $this->form_validation->set_rules('profissional_id', 'Profissional', 'required|integer');
        }

        if (!$id) {
            // Ao criar, senha é obrigatória
            $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');
            $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'required|matches[senha]');
        } else {
            // Ao editar, senha é opcional
            if ($this->input->post('senha')) {
                $this->form_validation->set_rules('senha', 'Senha', 'min_length[6]');
                $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'matches[senha]');
            }
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            if ($id) {
                redirect('admin/usuarios/editar/' . $id);
            } else {
                redirect('admin/usuarios/criar');
            }
            return;
        }

        // Verificar se email já existe (exceto o próprio usuário)
        if ($this->Usuario_model->email_existe($this->input->post('email'), $id)) {
            $this->session->set_flashdata('erro', 'Este e-mail já está cadastrado.');
            if ($id) {
                redirect('admin/usuarios/editar/' . $id);
            } else {
                redirect('admin/usuarios/criar');
            }
            return;
        }

        $dados = [
            'nome' => $this->input->post('nome'),
            'email' => $this->input->post('email'),
            'telefone' => $this->input->post('telefone'),
            'tipo' => $tipo,
            'ativo' => $this->input->post('ativo') ? 1 : 0
        ];

        // Campos condicionais
        if ($tipo == 'estabelecimento' || $tipo == 'profissional') {
            $dados['estabelecimento_id'] = $this->input->post('estabelecimento_id');
        }

        if ($tipo == 'profissional') {
            $dados['profissional_id'] = $this->input->post('profissional_id');
        }

        // Senha (apenas se preenchida)
        if ($this->input->post('senha')) {
            $dados['senha'] = $this->input->post('senha');
        }

        if ($id) {
            // Buscar dados antigos
            $dados_antigos = (array) $this->Usuario_model->get($id);

            // Atualizar
            if ($this->Usuario_model->atualizar($id, $dados)) {
                // Registrar log
                $this->registrar_log('editar', 'usuarios', $id, $dados_antigos, $dados);

                $this->session->set_flashdata('sucesso', 'Usuário atualizado com sucesso!');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao atualizar usuário.');
            }
            redirect('admin/usuarios');
        } else {
            // Criar
            $novo_id = $this->Usuario_model->criar($dados);
            if ($novo_id) {
                // Registrar log
                $this->registrar_log('criar', 'usuarios', $novo_id, null, $dados);

                $this->session->set_flashdata('sucesso', 'Usuário criado com sucesso!');
                redirect('admin/usuarios');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao criar usuário.');
                redirect('admin/usuarios/criar');
            }
        }
    }

    /**
     * Salvar permissões do usuário
     */
    private function salvar_permissoes_usuario($usuario_id) {
        // Pegar permissões do POST
        $permissoes_post = $this->input->post('permissoes');

        if (!$permissoes_post) {
            // Se não enviou permissões, limpar todas
            $this->Usuario_model->delete_permissoes($usuario_id);
            return;
        }

        // Montar array de permissões
        $permissoes = [];
        foreach ($permissoes_post as $modulo => $acoes) {
            $permissoes[$modulo] = [
                'visualizar' => isset($acoes['visualizar']),
                'criar' => isset($acoes['criar']),
                'editar' => isset($acoes['editar']),
                'excluir' => isset($acoes['excluir'])
            ];
        }

        // Salvar permissões
        $this->Usuario_model->salvar_permissoes($usuario_id, $permissoes);
    }

    /**
     * Alterar senha
     */
    public function alterar_senha($id) {
        $data['titulo'] = 'Alterar Senha';
        $data['menu_ativo'] = 'usuarios';

        $data['usuario'] = $this->Usuario_model->get($id);

        if (!$data['usuario']) {
            $this->session->set_flashdata('erro', 'Usuário não encontrado.');
            redirect('admin/usuarios');
        }

        if ($this->input->method() === 'post') {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('senha', 'Nova Senha', 'required|min_length[6]');
            $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'required|matches[senha]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('erro', validation_errors());
            } else {
                // O model já faz o hash
                if ($this->Usuario_model->atualizar($id, ['senha' => $this->input->post('senha')])) {
                    // Registrar log
                    $this->registrar_log('alterar_senha', 'usuarios', $id);

                    $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
                    redirect('admin/usuarios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao alterar senha.');
                }
            }
        }

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/usuarios/alterar_senha', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Excluir usuário
     */
    public function excluir($id) {
        $usuario = $this->Usuario_model->get($id);

        if (!$usuario) {
            $this->session->set_flashdata('erro', 'Usuário não encontrado.');
            redirect('admin/usuarios');
        }

        // Não permitir excluir a si mesmo
        if ($id == $this->session->userdata('usuario_id')) {
            $this->session->set_flashdata('erro', 'Você não pode excluir seu próprio usuário.');
            redirect('admin/usuarios');
        }

        if ($this->Usuario_model->delete($id)) {
            // Registrar log
            $this->registrar_log('deletar', 'usuarios', $id, $usuario);

            $this->session->set_flashdata('sucesso', 'Usuário excluído com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao excluir usuário.');
        }

        redirect('admin/usuarios');
    }

    /**
     * Alternar status do usuário
     */
    public function alternar_status($id) {
        $usuario = $this->Usuario_model->get($id);

        if (!$usuario) {
            $this->session->set_flashdata('erro', 'Usuário não encontrado.');
            redirect('admin/usuarios');
        }

        // Não permitir desativar a si mesmo
        if ($id == $this->session->userdata('usuario_id')) {
            $this->session->set_flashdata('erro', 'Você não pode desativar seu próprio usuário.');
            redirect('admin/usuarios');
        }

        $novo_ativo = $usuario->ativo == 1 ? 0 : 1;

        if ($this->Usuario_model->atualizar($id, ['ativo' => $novo_ativo])) {
            // Registrar log
            $acao = $novo_ativo == 1 ? 'ativar' : 'desativar';
            $this->registrar_log($acao, 'usuarios', $id);

            $this->session->set_flashdata('sucesso', 'Status alterado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao alterar status.');
        }

        redirect('admin/usuarios');
    }

    /**
     * Módulos disponíveis no sistema (Projeto Base)
     */
    private function get_modulos_sistema() {
        return [
            'dashboard' => [
                'nome' => 'Dashboard',
                'icone' => 'ti-home',
                'acoes' => ['visualizar']
            ],
            'usuarios' => [
                'nome' => 'Usuários',
                'icone' => 'ti-users',
                'acoes' => ['visualizar', 'criar', 'editar', 'excluir']
            ],
            'configuracoes' => [
                'nome' => 'Configurações',
                'icone' => 'ti-settings',
                'acoes' => ['visualizar', 'editar']
            ],
            'logs' => [
                'nome' => 'Logs do Sistema',
                'icone' => 'ti-history',
                'acoes' => ['visualizar']
            ]
        ];
    }
}
