<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Painel (Estabelecimento)
 *
 * Dashboard específico para usuários do tipo 'estabelecimento'
 * Exibe estatísticas e informações do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 10/12/2024
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');

        // Verificar tipo de usuário
        $this->auth_check->check_tipo(['estabelecimento']);

        // Carregar models
        $this->load->model('Estabelecimento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
        $this->load->model('Agendamento_model');
        $this->load->model('Assinatura_model');

        // Obter dados do estabelecimento
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get_by_id($this->estabelecimento_id);

        // Verificar se estabelecimento está ativo
        $this->auth_check->verificar_estabelecimento_ativo();
    }

    /**
     * Dashboard principal do estabelecimento
     */
    public function index() {
        $data['titulo'] = 'Painel do Estabelecimento';
        $data['menu_ativo'] = 'dashboard';

        // Dados do estabelecimento
        $data['estabelecimento'] = $this->estabelecimento;

        // Assinatura
        $data['assinatura'] = $this->Assinatura_model->get_ativa($this->estabelecimento_id);

        // Estatísticas gerais
        $data['total_clientes'] = $this->Cliente_model->count(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['total_profissionais'] = $this->Profissional_model->count_by_estabelecimento($this->estabelecimento_id);
        $data['total_servicos'] = $this->Servico_model->count(['estabelecimento_id' => $this->estabelecimento_id]);

        // Agendamentos
        $data['agendamentos_hoje'] = $this->Agendamento_model->count([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data' => date('Y-m-d'),
            'status' => 'confirmado'
        ]);

        $data['agendamentos_mes'] = $this->Agendamento_model->count_mes_atual($this->estabelecimento_id);

        // Faturamento
        $data['faturamento_dia'] = $this->Agendamento_model->get_faturamento_dia($this->estabelecimento_id);
        $data['faturamento_mes'] = $this->Agendamento_model->get_faturamento_mes($this->estabelecimento_id);

        // Novos clientes no mês
        // TODO: Implementar filtro por data de criação quando tabela tiver campo criado_em
        // Por enquanto, mostra total geral ou implementa lógica se campo existir
        $data['novos_clientes_mes'] = $this->Cliente_model->count([
            'estabelecimento_id' => $this->estabelecimento_id,
            // 'criado_em_mes' => date('m'), // Futura implementação
            // 'criado_em_ano' => date('Y')
        ]);

        // Gráficos
        $data['grafico_faturamento'] = $this->Agendamento_model->get_historico_faturamento($this->estabelecimento_id, 7);
        $data['grafico_status'] = $this->Agendamento_model->get_distribuicao_status($this->estabelecimento_id);

        // Próximos agendamentos (hoje)
        $data['proximos_agendamentos'] = $this->Agendamento_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data' => date('Y-m-d')
        ], 10);

        // Clientes recentes
        $data['clientes_recentes'] = $this->Cliente_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id
        ], 5);

        // Verificar limites do plano
        if ($data['assinatura'] && isset($data['assinatura']->plano_id)) {
            $this->load->model('Plano_model');
            $data['plano'] = $this->Plano_model->get_by_id($data['assinatura']->plano_id);

            // Calcular percentuais de uso
            if ($data['plano'] && $data['plano']->max_profissionais > 0) {
                $data['uso_profissionais'] = round(($data['total_profissionais'] / $data['plano']->max_profissionais) * 100);
            } else {
                $data['uso_profissionais'] = 0;
            }

            if ($data['plano'] && $data['plano']->max_agendamentos_mes > 0) {
                $data['uso_agendamentos'] = round(($data['agendamentos_mes'] / $data['plano']->max_agendamentos_mes) * 100);
            } else {
                $data['uso_agendamentos'] = 0;
            }
        }

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/dashboard/index', $data);
        $this->load->view('painel/layout/footer');
    }
}
