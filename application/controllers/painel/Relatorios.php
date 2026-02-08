<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Relatorios (Estabelecimento)
 *
 * Exibe relatórios detalhados de faturamento, agendamentos e desempenho
 *
 * @author Rafael Dias - doisr.com.br
 * @date 08/02/2/2025
 */
class Relatorios extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Verificar autenticação
        $this->load->library('auth_check');
        $this->auth_check->check_tipo(['estabelecimento']);

        // Carregar models
        $this->load->model('Agendamento_model');
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
        $this->load->model('Estabelecimento_model');

        // Dados do estabelecimento
        $this->estabelecimento_id = $this->auth_check->get_estabelecimento_id();
        $this->estabelecimento = $this->Estabelecimento_model->get_by_id($this->estabelecimento_id);
    }

    public function index() {
        $data['titulo'] = 'Relatórios';
        $data['menu_ativo'] = 'relatorios';
        $data['estabelecimento'] = $this->estabelecimento;

        // Filtros (Padrão: Mês Atual)
        $data_inicio = $this->input->get('data_inicio') ?? date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?? date('Y-m-t');
        $profissional_id = $this->input->get('profissional_id');

        $data['filtros'] = [
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'profissional_id' => $profissional_id
        ];

        // Buscar Profissionais para o filtro
        $data['profissionais'] = $this->Profissional_model->get_all($this->estabelecimento_id);

        // Métricas Gerais do Período
        $agendamentos = $this->Agendamento_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'profissional_id' => $profissional_id, // Add support in model if needed or handle logic
            'status_in' => ['confirmado', 'concluido', 'em_atendimento']
        ]);

        // Se o model suportar filtro por profissional, ótimo.
        // Caso contrário, filtramos aqui (menos performático, mas funcional para MVP)
        // O Agendamento_model::get_all já tem filtro 'profissional_id'

        $receita_total = 0;
        $qtd_agendamentos = count($agendamentos);

        foreach ($agendamentos as $ag) {
            $receita_total += $ag->servico_preco;
        }

        $data['receita_total'] = $receita_total;
        $data['total_agendamentos'] = $qtd_agendamentos;
        $data['ticket_medio'] = $qtd_agendamentos > 0 ? ($receita_total / $qtd_agendamentos) : 0;

        // Ranking de Serviços
        $servicos_stats = [];
        foreach ($agendamentos as $ag) {
            if (!isset($servicos_stats[$ag->servico_id])) {
                $servicos_stats[$ag->servico_id] = [
                    'nome' => $ag->servico_nome,
                    'qtd' => 0,
                    'receita' => 0
                ];
            }
            $servicos_stats[$ag->servico_id]['qtd']++;
            $servicos_stats[$ag->servico_id]['receita'] += $ag->servico_preco;
        }
        // Ordenar por qtd
        usort($servicos_stats, function($a, $b) {
            return $b['qtd'] - $a['qtd'];
        });
        $data['ranking_servicos'] = array_slice($servicos_stats, 0, 5);

        // Ranking de Profissionais (Receita)
        $profissionais_stats = [];
        foreach ($agendamentos as $ag) {
             if (!isset($profissionais_stats[$ag->profissional_id])) {
                $profissionais_stats[$ag->profissional_id] = [
                    'nome' => $ag->profissional_nome,
                    'qtd' => 0,
                    'receita' => 0
                ];
            }
            $profissionais_stats[$ag->profissional_id]['qtd']++;
            $profissionais_stats[$ag->profissional_id]['receita'] += $ag->servico_preco;
        }
         usort($profissionais_stats, function($a, $b) {
            return $b['receita'] - $a['receita'];
        });
        $data['ranking_profissionais'] = $profissionais_stats;


        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/relatorios/index', $data);
        $this->load->view('painel/layout/footer');
    }
}
