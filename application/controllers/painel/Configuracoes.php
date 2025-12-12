<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Configurações do Estabelecimento
 *
 * Gerenciamento de configurações gerais do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Configuracoes extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');
        $this->auth_check->check_tipo(['estabelecimento']);

        // Carregar models
        $this->load->model('Estabelecimento_model');

        // Obter dados do estabelecimento
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get_by_id($this->estabelecimento_id);
    }

    /**
     * Página de configurações
     */
    public function index() {
        $aba = $this->input->get('aba') ?? 'geral';

        if ($this->input->method() === 'post') {
            $aba_post = $this->input->post('aba');

            switch ($aba_post) {
                case 'geral':
                    $this->salvar_dados_gerais();
                    break;
                case 'agendamento':
                    $this->salvar_configuracoes_agendamento();
                    break;
                case 'whatsapp':
                    $this->salvar_integracao_whatsapp();
                    break;
                case 'mercadopago':
                    $this->salvar_integracao_mercadopago();
                    break;
            }
        }

        $data['titulo'] = 'Configurações';
        $data['menu_ativo'] = 'configuracoes';
        $data['estabelecimento'] = $this->estabelecimento;
        $data['aba_ativa'] = $aba;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/configuracoes/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Salvar dados gerais
     */
    private function salvar_dados_gerais() {
        $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]');
        $this->form_validation->set_rules('cnpj_cpf', 'CNPJ/CPF', 'max_length[18]');
        $this->form_validation->set_rules('whatsapp', 'WhatsApp', 'max_length[20]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

        if ($this->form_validation->run()) {
            $dados = [
                'nome' => $this->input->post('nome'),
                'cnpj_cpf' => $this->input->post('cnpj_cpf'),
                'whatsapp' => $this->input->post('whatsapp'),
                'email' => $this->input->post('email'),
                'endereco' => $this->input->post('endereco'),
                'cidade' => $this->input->post('cidade'),
                'estado' => $this->input->post('estado'),
                'cep' => $this->input->post('cep')
            ];

            if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
                $this->session->set_flashdata('sucesso', 'Dados atualizados com sucesso!');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao atualizar dados.');
            }
        }

        redirect('painel/configuracoes?aba=geral');
    }

    /**
     * Salvar configurações de agendamento
     */
    private function salvar_configuracoes_agendamento() {
        $dados = [
            'tempo_minimo_agendamento' => $this->input->post('tempo_minimo_agendamento') ?? 60,
            'confirmacao_automatica' => $this->input->post('confirmacao_automatica') ? 1 : 0,
            'permite_reagendamento' => $this->input->post('permite_reagendamento') ? 1 : 0,
            'horario_abertura' => $this->input->post('horario_abertura'),
            'horario_fechamento' => $this->input->post('horario_fechamento')
        ];

        if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
            $this->session->set_flashdata('sucesso', 'Configurações de agendamento atualizadas!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao atualizar configurações.');
        }

        redirect('painel/configuracoes?aba=agendamento');
    }

    /**
     * Salvar integração WhatsApp
     */
    private function salvar_integracao_whatsapp() {
        $dados = [
            'whatsapp_api_url' => $this->input->post('whatsapp_api_url'),
            'whatsapp_api_token' => $this->input->post('whatsapp_api_token'),
            'whatsapp_numero' => $this->input->post('whatsapp_numero'),
            'whatsapp_ativo' => $this->input->post('whatsapp_ativo') ? 1 : 0
        ];

        if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
            $this->session->set_flashdata('sucesso', 'Integração WhatsApp atualizada!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao atualizar integração.');
        }

        redirect('painel/configuracoes?aba=whatsapp');
    }

    /**
     * Salvar integração Mercado Pago
     */
    private function salvar_integracao_mercadopago() {
        $dados = [
            'mp_public_key' => $this->input->post('mp_public_key'),
            'mp_access_token' => $this->input->post('mp_access_token'),
            'mp_ativo' => $this->input->post('mp_ativo') ? 1 : 0
        ];

        if ($this->Estabelecimento_model->update($this->estabelecimento_id, $dados)) {
            $this->session->set_flashdata('sucesso', 'Integração Mercado Pago atualizada!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao atualizar integração.');
        }

        redirect('painel/configuracoes?aba=mercadopago');
    }
}
