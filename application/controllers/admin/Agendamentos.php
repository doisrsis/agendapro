<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Agendamentos
 *
 * Gerenciamento de agendamentos do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 05/12/2024
 */
class Agendamentos extends Admin_Controller {

    protected $modulo_atual = 'agendamentos';

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Estabelecimento_model');
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
        $this->load->model('Cliente_model');
    }

    /**
     * Listagem de agendamentos
     */
    public function index() {
        $data['titulo'] = 'Agendamentos';
        $data['menu_ativo'] = 'agendamentos';

        $filtros = [];

        // Multi-tenant: filtrar por estabelecimento
        if ($this->estabelecimento_id) {
            $filtros['estabelecimento_id'] = $this->estabelecimento_id;
        } elseif ($this->input->get('estabelecimento_id')) {
            $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
        }

        if ($this->input->get('profissional_id')) {
            $filtros['profissional_id'] = $this->input->get('profissional_id');
        }

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('data')) {
            $filtros['data'] = $this->input->get('data');
        }

        if ($this->input->get('data_inicio') && $this->input->get('data_fim')) {
            $filtros['data_inicio'] = $this->input->get('data_inicio');
            $filtros['data_fim'] = $this->input->get('data_fim');
        }

        // Paginação
        $config['base_url'] = base_url('admin/agendamentos');
        $config['total_rows'] = $this->Agendamento_model->count($filtros);
        $config['per_page'] = 50;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        $data['agendamentos'] = $this->Agendamento_model->get_all($filtros, $config['per_page'], $offset);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];
        $data['filtros'] = $filtros;

        if ($this->auth_check->is_super_admin()) {
            $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
            $data['profissionais'] = $this->Profissional_model->get_all(['status' => 'ativo']);
        } else {
            $data['profissionais'] = $this->Profissional_model->get_all([
                'estabelecimento_id' => $this->estabelecimento_id,
                'status' => 'ativo'
            ]);
        }

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/agendamentos/index', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Criar novo agendamento
     */
    public function criar() {
        // Verificar limite do plano
        if ($this->estabelecimento_id && !$this->pode_criar_agendamento()) {
            $this->session->set_flashdata('erro', 'Limite de agendamentos do mês atingido. Faça upgrade do seu plano.');
            redirect('admin/agendamentos');
        }

        if ($this->input->method() === 'post') {
            if (!$this->estabelecimento_id) {
                $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required|integer');
            }

            $this->form_validation->set_rules('cliente_id', 'Cliente', 'required|integer');
            $this->form_validation->set_rules('profissional_id', 'Profissional', 'required|integer');
            $this->form_validation->set_rules('servico_id', 'Serviço', 'required|integer');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id ?: $this->input->post('estabelecimento_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'profissional_id' => $this->input->post('profissional_id'),
                    'servico_id' => $this->input->post('servico_id'),
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => $this->input->post('status') ?: 'confirmado',
                    'observacoes' => $this->input->post('observacoes'),
                ];

                $id = $this->Agendamento_model->create($dados);

                if ($id) {
                    $this->registrar_log('criar', 'agendamentos', $id, null, $dados);
                    $this->session->set_flashdata('sucesso', 'Agendamento criado com sucesso!');
                    redirect('admin/agendamentos');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar agendamento. Verifique a disponibilidade.');
                }
            }
        }

        $data['titulo'] = 'Novo Agendamento';
        $data['menu_ativo'] = 'agendamentos';

        if ($this->auth_check->is_super_admin()) {
            $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        }

        $data['clientes'] = [];
        $data['profissionais'] = [];
        $data['servicos'] = [];

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/agendamentos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Editar agendamento
     */
    public function editar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        if (!$agendamento) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('admin/agendamentos');
        }

        // Verificar permissão
        if ($this->estabelecimento_id && $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Sem permissão.');
            redirect('admin/agendamentos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');

            if ($this->form_validation->run()) {
                $dados_antigos = (array) $agendamento;

                // Guardar data/hora anterior para verificar se houve reagendamento
                $data_anterior = $agendamento->data;
                $hora_anterior = $agendamento->hora_inicio;

                $nova_data = $this->input->post('data');
                $nova_hora_inicio = $this->input->post('hora_inicio');
                $novo_status = $this->input->post('status');
                $novas_observacoes = $this->input->post('observacoes');

                // Verificar se houve mudança de data/hora (reagendamento)
                $houve_reagendamento = ($nova_data != $data_anterior || $nova_hora_inicio != $hora_anterior);

                if ($houve_reagendamento) {
                    // Usar método reagendar_criar_novo para manter histórico completo
                    // Calcular hora_fim baseado na duração do serviço
                    $servico = $this->Servico_model->get_by_id($agendamento->servico_id);
                    $duracao = $servico ? $servico->duracao : 30;
                    $nova_hora_fim = date('H:i:s', strtotime($nova_hora_inicio) + ($duracao * 60));

                    $resultado = $this->Agendamento_model->reagendar_criar_novo(
                        $id,
                        $nova_data,
                        $nova_hora_inicio,
                        $nova_hora_fim
                    );

                    if ($resultado['success']) {
                        // Atualizar observações e status no novo agendamento se necessário
                        $novo_id = $resultado['novo_agendamento_id'];
                        $dados_extras = [];

                        if ($novas_observacoes && $novas_observacoes != $agendamento->observacoes) {
                            $dados_extras['observacoes'] = $novas_observacoes;
                        }

                        // Só atualizar status se for diferente de 'pendente'
                        if ($novo_status && $novo_status != 'pendente') {
                            $dados_extras['status'] = $novo_status;
                        }

                        if (!empty($dados_extras)) {
                            $this->Agendamento_model->update($novo_id, $dados_extras);
                        }

                        $this->registrar_log('reagendar', 'agendamentos', $id, $dados_antigos, [
                            'novo_agendamento_id' => $novo_id,
                            'data' => $nova_data,
                            'hora_inicio' => $nova_hora_inicio
                        ]);

                        $this->session->set_flashdata('sucesso', 'Agendamento reagendado com sucesso! Novo ID: ' . $novo_id);
                        redirect('admin/agendamentos');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao reagendar: ' . $resultado['message']);
                    }
                } else {
                    // Apenas atualização de outros campos (status, observações)
                    $dados = [
                        'status' => $novo_status,
                        'observacoes' => $novas_observacoes,
                    ];

                    if ($this->Agendamento_model->update($id, $dados)) {
                        $this->registrar_log('atualizar', 'agendamentos', $id, $dados_antigos, $dados);
                        $this->session->set_flashdata('sucesso', 'Agendamento atualizado com sucesso!');
                        redirect('admin/agendamentos');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao atualizar agendamento.');
                    }
                }
            }
        }

        $data['titulo'] = 'Editar Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['agendamento'] = $agendamento;

        if ($this->auth_check->is_super_admin()) {
            $data['estabelecimentos'] = $this->Estabelecimento_model->get_all(['status' => 'ativo']);
        }

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/agendamentos/form', $data);
        $this->load->view('admin/layout/footer');
    }

    /**
     * Cancelar agendamento
     */
    public function cancelar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        if (!$agendamento) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('admin/agendamentos');
        }

        // Verificar permissão
        if ($this->estabelecimento_id && $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Sem permissão.');
            redirect('admin/agendamentos');
        }

        $motivo = $this->input->post('motivo');

        if ($this->Agendamento_model->cancelar($id, 'admin', $motivo)) {
            $this->registrar_log('cancelar', 'agendamentos', $id, (array) $agendamento, ['status' => 'cancelado', 'motivo' => $motivo]);
            $this->session->set_flashdata('sucesso', 'Agendamento cancelado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cancelar agendamento.');
        }

        redirect('admin/agendamentos');
    }

    /**
     * Finalizar agendamento
     */
    public function finalizar($id) {
        if ($this->Agendamento_model->finalizar($id)) {
            $this->session->set_flashdata('sucesso', 'Agendamento finalizado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao finalizar agendamento.');
        }

        redirect('admin/agendamentos');
    }

    /**
     * Buscar horários disponíveis via AJAX
     */
    public function get_horarios_disponiveis() {
        $profissional_id = $this->input->get('profissional_id');
        $data = $this->input->get('data');
        $servico_id = $this->input->get('servico_id');

        if (!$profissional_id || !$data || !$servico_id) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Parâmetros inválidos']);
            return;
        }

        $servico = $this->Servico_model->get_by_id($servico_id);

        if (!$servico) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Serviço não encontrado']);
            return;
        }

        $horarios = $this->Agendamento_model->get_horarios_disponiveis(
            $profissional_id,
            $data,
            $servico->duracao
        );

        header('Content-Type: application/json');
        echo json_encode($horarios);
    }

    /**
     * Buscar clientes do estabelecimento via AJAX
     */
    public function get_clientes($estabelecimento_id) {
        $clientes = $this->Cliente_model->get_all([
            'estabelecimento_id' => $estabelecimento_id
        ]);

        header('Content-Type: application/json');
        echo json_encode($clientes);
    }

    /**
     * Buscar profissionais do estabelecimento via AJAX
     */
    public function get_profissionais($estabelecimento_id) {
        $profissionais = $this->Profissional_model->get_all([
            'estabelecimento_id' => $estabelecimento_id,
            'status' => 'ativo'
        ]);

        header('Content-Type: application/json');
        echo json_encode($profissionais);
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
}
