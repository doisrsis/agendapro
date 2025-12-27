<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Feriados (Painel)
 *
 * Gestão de feriados do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 27/12/2024
 */
class Feriados extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Feriado_model');
    }

    /**
     * Listagem de feriados
     */
    public function index() {
        $data['titulo'] = 'Feriados';
        $data['menu_ativo'] = 'configuracoes';

        // Filtros
        $filtros = [];

        $tipo = $this->input->get('tipo');
        if ($tipo) {
            $filtros['tipo'] = $tipo;
        }

        $ano = $this->input->get('ano') ?? date('Y');
        $filtros['ano'] = $ano;

        // Buscar feriados (nacionais + personalizados do estabelecimento)
        $filtros['estabelecimento_id'] = $this->estabelecimento_id;
        $data['feriados'] = $this->Feriado_model->get_all($filtros);

        // Anos disponíveis para filtro
        $data['anos'] = range(date('Y'), date('Y') + 2);
        $data['ano_selecionado'] = $ano;
        $data['tipo_selecionado'] = $tipo;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/feriados/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar feriado
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'nome' => $this->input->post('nome'),
                    'data' => $this->input->post('data'),
                    'tipo' => $this->input->post('tipo'),
                    'recorrente' => $this->input->post('recorrente') ? 1 : 0,
                    'ativo' => 1
                ];

                if ($this->Feriado_model->create($dados)) {
                    $this->session->set_flashdata('sucesso', 'Feriado criado com sucesso!');
                    redirect('painel/feriados');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao criar feriado.');
                }
            }
        }

        $data['titulo'] = 'Novo Feriado';
        $data['menu_ativo'] = 'configuracoes';

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/feriados/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar feriado
     */
    public function editar($id) {
        $feriado = $this->Feriado_model->get($id);

        // Verificar se feriado existe e pertence ao estabelecimento
        if (!$feriado || ($feriado->estabelecimento_id && $feriado->estabelecimento_id != $this->estabelecimento_id)) {
            $this->session->set_flashdata('erro', 'Feriado não encontrado.');
            redirect('painel/feriados');
        }

        // Não permitir editar feriados nacionais
        if (!$feriado->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Feriados nacionais não podem ser editados.');
            redirect('painel/feriados');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required');
            $this->form_validation->set_rules('data', 'Data', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'data' => $this->input->post('data'),
                    'tipo' => $this->input->post('tipo'),
                    'recorrente' => $this->input->post('recorrente') ? 1 : 0
                ];

                if ($this->Feriado_model->update($id, $dados)) {
                    $this->session->set_flashdata('sucesso', 'Feriado atualizado com sucesso!');
                    redirect('painel/feriados');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar feriado.');
                }
            }
        }

        $data['titulo'] = 'Editar Feriado';
        $data['menu_ativo'] = 'configuracoes';
        $data['feriado'] = $feriado;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/feriados/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Deletar feriado
     */
    public function deletar($id) {
        $feriado = $this->Feriado_model->get($id);

        // Verificar se feriado existe e pertence ao estabelecimento
        if (!$feriado || ($feriado->estabelecimento_id && $feriado->estabelecimento_id != $this->estabelecimento_id)) {
            $this->session->set_flashdata('erro', 'Feriado não encontrado.');
            redirect('painel/feriados');
        }

        // Não permitir deletar feriados nacionais
        if (!$feriado->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Feriados nacionais não podem ser deletados.');
            redirect('painel/feriados');
        }

        if ($this->Feriado_model->delete($id)) {
            $this->session->set_flashdata('sucesso', 'Feriado deletado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao deletar feriado.');
        }

        redirect('painel/feriados');
    }

    /**
     * Ativar/Desativar feriado
     */
    public function toggle($id) {
        $feriado = $this->Feriado_model->get($id);

        if (!$feriado) {
            $this->session->set_flashdata('erro', 'Feriado não encontrado.');
            redirect('painel/feriados');
        }

        if ($this->Feriado_model->toggle_ativo($id)) {
            $status = $feriado->ativo ? 'desativado' : 'ativado';
            $this->session->set_flashdata('sucesso', "Feriado {$status} com sucesso!");
        } else {
            $this->session->set_flashdata('erro', 'Erro ao alterar status do feriado.');
        }

        redirect('painel/feriados');
    }

    /**
     * Gerar feriados móveis do próximo ano
     */
    public function gerar_moveis() {
        $ano = $this->input->get('ano') ?? (date('Y') + 1);

        $inseridos = $this->Feriado_model->gerar_feriados_moveis($ano);

        if ($inseridos > 0) {
            $this->session->set_flashdata('sucesso', "{$inseridos} feriado(s) móvel(is) gerado(s) para {$ano}!");
        } else {
            $this->session->set_flashdata('info', "Feriados móveis de {$ano} já existem.");
        }

        redirect('painel/feriados?ano=' . $ano);
    }
}
