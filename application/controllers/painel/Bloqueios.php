<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller de Bloqueios do Estabelecimento
 *
 * Gerenciamento de bloqueios de profissionais e serviços
 *
 * @author Rafael Dias - doisr.com.br
 * @date 13/12/2024
 */
class Bloqueios extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Bloqueio_model');
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
    }

    /**
     * Listar bloqueios
     */
    public function index() {
        $filtros = ['estabelecimento_id' => $this->estabelecimento_id];

        // Aplicar filtros
        if ($this->input->get('profissional_id')) {
            $filtros['profissional_id'] = $this->input->get('profissional_id');
        }

        if ($this->input->get('servico_id')) {
            $filtros['servico_id'] = $this->input->get('servico_id');
        }

        if ($this->input->get('tipo')) {
            $filtros['tipo'] = $this->input->get('tipo');
        }

        $bloqueios = $this->Bloqueio_model->get_all($filtros);

        // Carregar profissionais e serviços para filtros
        $profissionais = $this->Profissional_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $servicos = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        $data['titulo'] = 'Bloqueios';
        $data['menu_ativo'] = 'bloqueios';
        $data['bloqueios'] = $bloqueios;
        $data['profissionais'] = $profissionais;
        $data['servicos'] = $servicos;
        $data['filtros'] = $filtros;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/bloqueios/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar bloqueio
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');
            $this->form_validation->set_rules('data_inicio', 'Data Início', 'required');
            $this->form_validation->set_rules('motivo', 'Motivo', 'max_length[200]');

            // Validar que pelo menos profissional ou serviço foi selecionado
            $profissional_id = $this->input->post('profissional_id');
            $servico_id = $this->input->post('servico_id');

            if (empty($profissional_id) && empty($servico_id)) {
                $this->session->set_flashdata('erro', 'Selecione pelo menos um profissional ou serviço para bloquear.');
                redirect('painel/bloqueios/criar');
                return;
            }

            if ($this->form_validation->run()) {
                $tipo = $this->input->post('tipo');

                $dados = [
                    'profissional_id' => $profissional_id ?: null,
                    'servico_id' => $servico_id ?: null,
                    'tipo' => $tipo,
                    'data_inicio' => $this->input->post('data_inicio'),
                    'motivo' => $this->input->post('motivo'),
                    'criado_por_tipo' => 'estabelecimento',
                    'criado_por_id' => $this->session->userdata('usuario_id')
                ];

                // Validar que profissional pertence ao estabelecimento
                if ($profissional_id) {
                    $prof = $this->Profissional_model->get_by_id($profissional_id);
                    if (!$prof || $prof->estabelecimento_id != $this->estabelecimento_id) {
                        $this->session->set_flashdata('erro', 'Profissional não pertence ao estabelecimento.');
                        redirect('painel/bloqueios/criar');
                        return;
                    }
                }

                // Validar que serviço pertence ao estabelecimento
                if ($servico_id) {
                    $serv = $this->Servico_model->get_by_id($servico_id);
                    if (!$serv || $serv->estabelecimento_id != $this->estabelecimento_id) {
                        $this->session->set_flashdata('erro', 'Serviço não pertence ao estabelecimento.');
                        redirect('painel/bloqueios/criar');
                        return;
                    }
                }

                // Campos específicos por tipo
                if ($tipo == 'periodo') {
                    $dados['data_fim'] = $this->input->post('data_fim');
                } elseif ($tipo == 'horario') {
                    $dados['hora_inicio'] = $this->input->post('hora_inicio');
                    $dados['hora_fim'] = $this->input->post('hora_fim');
                }

                // Campos de Recorrência
                $recorrencia = $this->input->post('recorrencia');
                $dados['recorrencia'] = $recorrencia ?: 'nao';

                if ($recorrencia && $recorrencia != 'nao') {
                    $dados['data_limite'] = $this->input->post('data_limite') ?: null;

                    if ($recorrencia == 'semanal') {
                        // Calcula dia da semana baseado na data de início
                        $dados['dia_semana'] = date('w', strtotime($dados['data_inicio']));
                    }
                }

                if ($this->Bloqueio_model->create($dados)) {
                    $this->session->set_flashdata('sucesso', 'Bloqueio criado com sucesso!');
                    redirect('painel/bloqueios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar bloqueio.');
                }
            }
        }

        // Carregar profissionais e serviços
        $profissionais = $this->Profissional_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $servicos = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        $data['titulo'] = 'Novo Bloqueio';
        $data['menu_ativo'] = 'bloqueios';
        $data['profissionais'] = $profissionais;
        $data['servicos'] = $servicos;
        $data['tipos'] = [
            'dia' => 'Dia Específico',
            'periodo' => 'Período (vários dias)',
            'horario' => 'Horário Específico'
        ];

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/bloqueios/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar bloqueio
     */
    public function editar($id) {
        $bloqueio = $this->Bloqueio_model->get_by_id($id);

        if (!$bloqueio) {
            $this->session->set_flashdata('erro', 'Bloqueio não encontrado.');
            redirect('painel/bloqueios');
            return;
        }

        // Verificar se bloqueio pertence ao estabelecimento
        if ($bloqueio->profissional_id) {
            $prof = $this->Profissional_model->get_by_id($bloqueio->profissional_id);
            if (!$prof || $prof->estabelecimento_id != $this->estabelecimento_id) {
                $this->session->set_flashdata('erro', 'Bloqueio não pertence ao estabelecimento.');
                redirect('painel/bloqueios');
                return;
            }
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');
            $this->form_validation->set_rules('data_inicio', 'Data Início', 'required');
            $this->form_validation->set_rules('motivo', 'Motivo', 'max_length[200]');

            if ($this->form_validation->run()) {
                $tipo = $this->input->post('tipo');

                $dados = [
                    'profissional_id' => $this->input->post('profissional_id') ?: null,
                    'servico_id' => $this->input->post('servico_id') ?: null,
                    'tipo' => $tipo,
                    'data_inicio' => $this->input->post('data_inicio'),
                    'motivo' => $this->input->post('motivo')
                ];

                if ($tipo == 'periodo') {
                    $dados['data_fim'] = $this->input->post('data_fim');
                } elseif ($tipo == 'horario') {
                    $dados['hora_inicio'] = $this->input->post('hora_inicio');
                    $dados['hora_fim'] = $this->input->post('hora_fim');
                } else {
                    $dados['data_fim'] = null;
                    $dados['hora_inicio'] = null;
                    $dados['hora_fim'] = null;
                }

                // Campos de Recorrência
                $recorrencia = $this->input->post('recorrencia');
                $dados['recorrencia'] = $recorrencia ?: 'nao';

                if ($recorrencia && $recorrencia != 'nao') {
                    $dados['data_limite'] = $this->input->post('data_limite') ?: null;

                    if ($recorrencia == 'semanal') {
                        $dados['dia_semana'] = date('w', strtotime($dados['data_inicio']));
                    } else {
                        $dados['dia_semana'] = null;
                    }
                } else {
                    $dados['recorrencia'] = 'nao';
                    $dados['dia_semana'] = null;
                    $dados['data_limite'] = null;
                }

                if ($this->Bloqueio_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Bloqueio atualizado com sucesso!');
                    redirect('painel/bloqueios');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar bloqueio.');
                }
            }
        }

        // Carregar profissionais e serviços
        $profissionais = $this->Profissional_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $servicos = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        $data['titulo'] = 'Editar Bloqueio';
        $data['menu_ativo'] = 'bloqueios';
        $data['bloqueio'] = $bloqueio;
        $data['profissionais'] = $profissionais;
        $data['servicos'] = $servicos;
        $data['tipos'] = [
            'dia' => 'Dia Específico',
            'periodo' => 'Período (vários dias)',
            'horario' => 'Horário Específico'
        ];

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/bloqueios/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Excluir bloqueio
     */
    public function excluir($id) {
        $bloqueio = $this->Bloqueio_model->get_by_id($id);

        if (!$bloqueio) {
            $this->session->set_flashdata('erro', 'Bloqueio não encontrado.');
            redirect('painel/bloqueios');
            return;
        }

        // Verificar se bloqueio pertence ao estabelecimento
        if ($bloqueio->profissional_id) {
            $prof = $this->Profissional_model->get_by_id($bloqueio->profissional_id);
            if (!$prof || $prof->estabelecimento_id != $this->estabelecimento_id) {
                $this->session->set_flashdata('erro', 'Bloqueio não pertence ao estabelecimento.');
                redirect('painel/bloqueios');
                return;
            }
        }

        if ($this->Bloqueio_model->delete($id)) {
            $this->session->set_flashdata('sucesso', 'Bloqueio excluído com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao excluir bloqueio.');
        }

        redirect('painel/bloqueios');
    }
}
