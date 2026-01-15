<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Agendamentos (Painel)
 *
 * Gestão de agendamentos do estabelecimento
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Agendamentos extends Painel_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Profissional_model');
        $this->load->model('Servico_model');
    }

    /**
     * Listagem de agendamentos
     */
    public function index() {
        $data['titulo'] = 'Agendamentos';
        $data['menu_ativo'] = 'agendamentos';
        $data['view'] = $this->input->get('view') ?? 'lista';

        $filtros = ['estabelecimento_id' => $this->estabelecimento_id];

        if ($this->input->get('data')) {
            $filtros['data'] = $this->input->get('data');
        }

        if ($this->input->get('status')) {
            $filtros['status'] = $this->input->get('status');
        }

        if ($this->input->get('profissional_id')) {
            $filtros['profissional_id'] = $this->input->get('profissional_id');
        }

        $data['agendamentos'] = $this->Agendamento_model->get_all($filtros);
        $data['total'] = count($data['agendamentos']);
        $data['filtros'] = $filtros;
        $data['pagination'] = '';
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['estatisticas'] = $this->get_estatisticas();

        $this->load->view('painel/layout/header', $data);
        $this->load->view('admin/agendamentos/index', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Criar agendamento
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('cliente_id', 'Cliente', 'required|integer');
            $this->form_validation->set_rules('profissional_id', 'Profissional', 'required|integer');
            $this->form_validation->set_rules('servico_id', 'Serviço', 'required|integer');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Hora', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'cliente_id' => $this->input->post('cliente_id'),
                    'profissional_id' => $this->input->post('profissional_id'),
                    'servico_id' => $this->input->post('servico_id'),
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => $this->input->post('status') ?: 'pendente',
                    'observacoes' => $this->input->post('observacoes')
                ];

                $agendamento_id = $this->Agendamento_model->criar($dados);

                if ($agendamento_id) {
                    // Verificar se requer pagamento
                    $this->load->model('Estabelecimento_model');
                    $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);

                    if ($estabelecimento->agendamento_requer_pagamento != 'nao') {
                        // Calcular valor
                        $valor = 0;
                        if ($estabelecimento->agendamento_requer_pagamento == 'valor_total') {
                            $servico = $this->Servico_model->get($dados['servico_id']);
                            $valor = $servico->preco;
                        } else if ($estabelecimento->agendamento_requer_pagamento == 'taxa_fixa') {
                            $valor = $estabelecimento->agendamento_taxa_fixa;
                        }

                        // Gerar PIX
                        $this->load->library('mercadopago_lib');
                        $this->load->model('Pagamento_model');

                        // Usar credenciais do estabelecimento
                        $access_token = $estabelecimento->mp_sandbox
                            ? $estabelecimento->mp_access_token_test
                            : $estabelecimento->mp_access_token_prod;
                        $public_key = $estabelecimento->mp_sandbox
                            ? $estabelecimento->mp_public_key_test
                            : $estabelecimento->mp_public_key_prod;

                        $this->mercadopago_lib->set_credentials($access_token, $public_key);

                        $cliente = $this->Cliente_model->get($dados['cliente_id']);

                        log_message('error', 'DEBUG: Cliente = ' . ($cliente ? $cliente->nome : 'NULL'));

                        log_message('error', 'DEBUG: Chamando criar_pix_agendamento...');

                        $pix_result = $this->mercadopago_lib->criar_pix_agendamento(
                            $agendamento_id,
                            $valor,
                            [
                                'nome' => $cliente->nome,
                                'email' => $cliente->email ?: 'cliente@agendapro.com',
                                'cpf' => $cliente->cpf ?: ''
                            ],
                            $estabelecimento->id
                        );

                        log_message('error', 'DEBUG: PIX retornou - Status: ' . ($pix_result['status'] ?? 'NULL'));
                        log_message('error', 'DEBUG: PIX retornou - Response existe: ' . (isset($pix_result['response']) ? 'SIM' : 'NÃO'));

                        if ($pix_result && isset($pix_result['response']) && in_array($pix_result['status'], [200, 201])) {
                            log_message('error', 'DEBUG: Entrou no IF de sucesso!');

                            $pix_data = $pix_result['response'];

                            log_message('error', 'DEBUG: PIX ID = ' . $pix_data['id']);

                            // Gerar token único para acesso público
                            $token_pagamento = $this->Agendamento_model->gerar_token_pagamento();

                            // Salvar dados do PIX no agendamento
                            $this->Agendamento_model->atualizar($agendamento_id, [
                                'pagamento_status' => 'pendente',
                                'pagamento_valor' => $valor,
                                'pagamento_pix_qrcode' => $pix_data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                                'pagamento_pix_copia_cola' => $pix_data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                                'pagamento_expira_em' => date('Y-m-d H:i:s', strtotime("+{$estabelecimento->agendamento_tempo_expiracao_pix} minutes")),
                                'pagamento_token' => $token_pagamento,
                                'pagamento_lembrete_enviado' => 0
                            ]);

                            // Criar registro de pagamento
                            $this->Pagamento_model->criar_agendamento([
                                'estabelecimento_id' => $estabelecimento->id,
                                'agendamento_id' => $agendamento_id,
                                'valor' => $valor,
                                'mercadopago_id' => $pix_data['id'],
                                'payment_data' => $pix_data
                            ]);

                            // Redirecionar para página de pagamento
                            $this->session->set_flashdata('sucesso', 'Agendamento criado! Complete o pagamento para confirmar.');
                            redirect('painel/agendamentos/pagamento/' . $agendamento_id);
                            return;
                        } else {
                            // Erro ao gerar PIX
                            log_message('error', 'Erro ao gerar PIX: ' . json_encode($pix_result));
                            $this->session->set_flashdata('erro', 'Erro ao gerar PIX. Verifique as configurações do Mercado Pago.');
                        }
                    } else {
                        // NÃO requer pagamento - confirmar automaticamente
                        $this->Agendamento_model->atualizar($agendamento_id, [
                            'status' => 'confirmado'
                        ]);

                        $this->session->set_flashdata('sucesso', 'Agendamento criado com sucesso!');
                        redirect('painel/agendamentos');
                        return;
                    }

                    // Agendamento sem pagamento
                    $this->session->set_flashdata('sucesso', 'Agendamento criado com sucesso!');
                    redirect('painel/agendamentos');
                } else {
                    // Verificar se há mensagem de erro específica
                    $erro_msg = $this->Agendamento_model->erro_disponibilidade ?? 'Erro ao criar agendamento.';
                    $this->session->set_flashdata('erro', $erro_msg);
                }
            }
        }

        $data['titulo'] = 'Novo Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['servicos'] = $this->Servico_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'ativo' => 1  // Apenas serviços ativos
        ]);

        // Calcular data máxima baseada no período de abertura
        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $dias_antecedencia = $estabelecimento->dias_antecedencia_agenda ?? 30;
        $this->load->model('Horario_estabelecimento_model');
        $data['data_maxima'] = $dias_antecedencia > 0
            ? $this->calcular_data_maxima_dias_uteis($this->estabelecimento_id, $dias_antecedencia)
            : null;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/agendamentos/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Editar agendamento
     */
    public function editar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run()) {
                // Guardar data/hora anterior para verificar se houve reagendamento
                $data_anterior = $agendamento->data;
                $hora_anterior = $agendamento->hora_inicio;

                $nova_data = $this->input->post('data');
                $nova_hora = $this->input->post('hora_inicio');
                $status = $this->input->post('status');
                $observacoes = $this->input->post('observacoes');

                // Verificar se houve mudança de data/hora (reagendamento)
                if ($nova_data != $data_anterior || $nova_hora != $hora_anterior) {
                    // Usar método reagendar que valida limite
                    $resultado = $this->Agendamento_model->reagendar($id, $nova_data, $nova_hora);

                    if ($resultado['success']) {
                        // Atualizar status e observações separadamente
                        $this->Agendamento_model->update($id, [
                            'status' => $status,
                            'observacoes' => $observacoes
                        ]);

                        $this->session->set_flashdata('sucesso', $resultado['message']);
                        redirect('painel/agendamentos');
                    } else {
                        $this->session->set_flashdata('erro', $resultado['message']);
                    }
                } else {
                    // Apenas atualizar status e observações
                    $dados = [
                        'status' => $status,
                        'observacoes' => $observacoes
                    ];

                    if ($this->Agendamento_model->update($id, $dados)) {
                        $this->session->set_flashdata('sucesso', 'Agendamento atualizado com sucesso!');
                        redirect('painel/agendamentos');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao atualizar agendamento.');
                    }
                }
            }
        }

        $data['titulo'] = 'Editar Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['agendamento'] = $agendamento;
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
        $data['profissionais'] = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $data['servicos'] = $this->Servico_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        // Calcular data máxima baseada no período de abertura
        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $dias_antecedencia = $estabelecimento->dias_antecedencia_agenda ?? 30;
        $this->load->model('Horario_estabelecimento_model');
        $data['data_maxima'] = $dias_antecedencia > 0
            ? $this->calcular_data_maxima_dias_uteis($this->estabelecimento_id, $dias_antecedencia)
            : null;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/agendamentos/form', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * Cancelar agendamento
     */
    public function cancelar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        if ($this->Agendamento_model->cancelar($id, 'estabelecimento')) {
            $this->session->set_flashdata('sucesso', 'Agendamento cancelado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cancelar agendamento.');
        }

        redirect('painel/agendamentos');
    }

    /**
     * Alias para cancelar
     */
    public function deletar($id) {
        $this->cancelar($id);
    }

    /**
     * Alias para cancelar
     */
    public function excluir($id) {
        $this->cancelar($id);
    }

    /**
     * Iniciar atendimento
     * Muda status para 'em_atendimento' e notifica cliente
     */
    public function iniciar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        // Verificar se pode iniciar (apenas confirmado ou pendente)
        if (!in_array($agendamento->status, ['confirmado', 'pendente'])) {
            $this->session->set_flashdata('erro', 'Este agendamento não pode ser iniciado.');
            redirect('painel/agendamentos');
        }

        $dados = [
            'status' => 'em_atendimento',
            'hora_inicio_real' => date('H:i:s')
        ];

        if ($this->Agendamento_model->atualizar($id, $dados)) {
            // Enviar notificação WhatsApp
            $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'inicio');
            $this->session->set_flashdata('sucesso', 'Atendimento iniciado!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao iniciar atendimento.');
        }

        redirect('painel/agendamentos');
    }

    /**
     * Finalizar atendimento
     * Muda status para 'finalizado' e notifica cliente
     */
    public function finalizar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        // Verificar se pode finalizar (apenas em_atendimento)
        if ($agendamento->status !== 'em_atendimento') {
            $this->session->set_flashdata('erro', 'Este agendamento não está em atendimento.');
            redirect('painel/agendamentos');
        }

        $dados = [
            'status' => 'finalizado',
            'hora_fim_real' => date('H:i:s')
        ];

        if ($this->Agendamento_model->atualizar($id, $dados)) {
            // Enviar notificação WhatsApp
            $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'finalizacao');
            $this->session->set_flashdata('sucesso', 'Atendimento finalizado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao finalizar atendimento.');
        }

        redirect('painel/agendamentos');
    }

    /**
     * Visualizar agendamento
     */
    public function visualizar($id) {
        $agendamento = $this->Agendamento_model->get($id);

        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('painel/agendamentos');
        }

        $data['titulo'] = 'Visualizar Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['agendamento'] = $agendamento;

        $this->load->view('painel/layout/header', $data);
        $this->load->view('painel/agendamentos/visualizar', $data);
        $this->load->view('painel/layout/footer');
    }

    /**
     * API JSON - Retorna agendamentos para o FullCalendar
     */
    public function get_agendamentos_json() {
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        $agendamentos = $this->Agendamento_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data_inicio' => $start,
            'data_fim' => $end
        ]);

        $eventos = [];
        foreach ($agendamentos as $ag) {
            $cor = $this->get_cor_status($ag->status);

            $data_hora_inicio = $ag->data . ' ' . $ag->hora_inicio;
            $data_hora_fim = $ag->data . ' ' . $ag->hora_fim;

            $eventos[] = [
                'id' => 'agendamento_' . $ag->id,
                'title' => $ag->cliente_nome . ' - ' . $ag->servico_nome . ' (' . $ag->profissional_nome . ')',
                'start' => $data_hora_inicio,
                'end' => $data_hora_fim,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'extendedProps' => [
                    'agendamento_id' => $ag->id,
                    'cliente_nome' => $ag->cliente_nome,
                    'profissional_nome' => $ag->profissional_nome,
                    'servico_nome' => $ag->servico_nome,
                    'status' => $ag->status
                ]
            ];
        }

        // Buscar bloqueios dos profissionais do estabelecimento
        $this->load->model('Bloqueio_model');

        // Buscar IDs dos profissionais do estabelecimento
        $profissionais = $this->Profissional_model->get_by_estabelecimento($this->estabelecimento_id);
        $profissional_ids = array_column($profissionais, 'id');

        $bloqueios = [];
        foreach ($profissional_ids as $prof_id) {
            $bloqueios_prof = $this->Bloqueio_model->get_all([
                'profissional_id' => $prof_id,
                'data_inicio' => $start,
                'data_fim' => $end
            ]);
            $bloqueios = array_merge($bloqueios, $bloqueios_prof);
        }

        // Adicionar bloqueios ao calendário
        foreach ($bloqueios as $bloqueio) {
            // Definir título e cor baseado no tipo de bloqueio
            $cor = '#6c757d'; // Cinza padrão
            $titulo = '🚫 ';

            if (!empty($bloqueio->profissional_nome) && !empty($bloqueio->servico_nome)) {
                // Bloqueio específico (profissional + serviço)
                $titulo .= $bloqueio->profissional_nome . ' - ' . $bloqueio->servico_nome;
                $cor = '#dc3545'; // Vermelho
            } elseif (!empty($bloqueio->servico_nome)) {
                // Bloqueio de serviço (todos profissionais)
                $titulo .= $bloqueio->servico_nome . ' - Indisponível';
                $cor = '#fd7e14'; // Laranja
            } elseif (!empty($bloqueio->profissional_nome)) {
                // Bloqueio de profissional (todos serviços)
                $titulo .= $bloqueio->profissional_nome . ' - Bloqueado';
                $cor = '#6c757d'; // Cinza
            } else {
                $titulo .= 'Bloqueado';
            }

            if ($bloqueio->motivo) {
                $titulo .= ' (' . $bloqueio->motivo . ')';
            }

            // Definir data/hora baseado no tipo
            if ($bloqueio->tipo == 'horario') {
                // Bloqueio de horário específico
                $start_datetime = $bloqueio->data_inicio . ' ' . $bloqueio->hora_inicio;
                $end_datetime = $bloqueio->data_inicio . ' ' . $bloqueio->hora_fim;
            } else {
                // Bloqueio de dia ou período (dia inteiro)
                $start_datetime = $bloqueio->data_inicio;

                // Para bloqueio de dia específico (data_fim NULL), usar data_inicio + 1 dia
                if ($bloqueio->tipo == 'dia' || $bloqueio->data_fim === null) {
                    $end_datetime = date('Y-m-d', strtotime($bloqueio->data_inicio . ' +1 day'));
                } else {
                    // Para bloqueio de período, usar data_fim + 1 dia (FullCalendar exclusive end)
                    $end_datetime = date('Y-m-d', strtotime($bloqueio->data_fim . ' +1 day'));
                }
            }

            $eventos[] = [
                'id' => 'bloqueio_' . $bloqueio->id,
                'title' => $titulo,
                'start' => $start_datetime,
                'end' => $end_datetime,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'display' => 'background',
                'extendedProps' => [
                    'tipo' => 'bloqueio',
                    'bloqueio_tipo' => $bloqueio->tipo,
                    'profissional' => $bloqueio->profissional_nome ?? '',
                    'servico' => $bloqueio->servico_nome ?? '',
                    'motivo' => $bloqueio->motivo ?? ''
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($eventos);
    }

    /**
     * Obter estatísticas de agendamentos
     */
    public function get_estatisticas() {
        $hoje = date('Y-m-d');

        $stats = [
            'total_hoje' => 0,
            'confirmados' => 0,
            'pendentes' => 0,
            'cancelados' => 0
        ];

        $agendamentos_hoje = $this->Agendamento_model->get_all([
            'estabelecimento_id' => $this->estabelecimento_id,
            'data' => $hoje
        ]);

        $stats['total_hoje'] = count($agendamentos_hoje);

        foreach ($agendamentos_hoje as $ag) {
            if ($ag->status == 'confirmado') $stats['confirmados']++;
            if ($ag->status == 'pendente') $stats['pendentes']++;
            if ($ag->status == 'cancelado') $stats['cancelados']++;
        }

        return $stats;
    }

    /**
     * Definir cor baseado no status
     */
    private function get_cor_status($status) {
        $cores = [
            'pendente' => '#ffc107',
            'confirmado' => '#28a745',
            'concluido' => '#17a2b8',
            'cancelado' => '#dc3545'
        ];
        return $cores[$status] ?? '#6c757d';
    }

    /**
     * API: Retorna horários disponíveis para agendamento
     * @param agendamento_id (opcional) - ID do agendamento sendo editado (para excluir da verificação)
     */
    public function get_horarios_disponiveis() {
        $profissional_id = $this->input->get('profissional_id');
        $data = $this->input->get('data');
        $servico_id = $this->input->get('servico_id');
        $agendamento_id = $this->input->get('agendamento_id'); // Para edição

        if (!$profissional_id || !$data || !$servico_id) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Buscar serviço para pegar duração
        $servico = $this->Servico_model->get_by_id($servico_id);
        if (!$servico) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Buscar horário do estabelecimento para este dia
        $this->load->model('Horario_estabelecimento_model');
        $dia_semana = date('w', strtotime($data));
        $horario_estab = $this->Horario_estabelecimento_model->get_by_dia($this->estabelecimento_id, $dia_semana);

        if (!$horario_estab || !$horario_estab->ativo) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Buscar configuração de tempo mínimo e intervalo
        $this->load->model('Estabelecimento_model');
        $estabelecimento_config = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $tempo_minimo = $estabelecimento_config->tempo_minimo_agendamento ?? 60;
        $usar_intervalo_fixo = $estabelecimento_config->usar_intervalo_fixo ?? 1;

        // ✅ LÓGICA HÍBRIDA: Determinar intervalo
        if ($usar_intervalo_fixo) {
            // Modo 1: Intervalo Fixo Configurável
            $intervalo = $estabelecimento_config->intervalo_agendamento ?? 30;
        } else {
            // Modo 2: Intervalo Dinâmico (baseado na duração do serviço)
            $intervalo = $servico->duracao;
        }

        // Calcular horário mínimo permitido
        $agora = new DateTime();
        $horario_minimo = clone $agora;
        $horario_minimo->add(new DateInterval('PT' . $tempo_minimo . 'M'));

        // Verificar período de abertura da agenda
        $dias_antecedencia = $estabelecimento_config->dias_antecedencia_agenda ?? 30;
        if ($dias_antecedencia > 0) {
            $data_maxima = date('Y-m-d', strtotime("+{$dias_antecedencia} days"));

            if ($data > $data_maxima) {
                header('Content-Type: application/json');
                echo json_encode([]);
                return;
            }
        }

        // Gerar horários disponíveis
        $horarios = [];
        $hora_atual = new DateTime($horario_estab->hora_inicio);
        $hora_fim_estab = new DateTime($horario_estab->hora_fim);

        // Se a data selecionada for hoje, ajustar hora inicial
        if ($data == date('Y-m-d')) {
            if ($hora_atual < $horario_minimo) {
                $hora_atual = clone $horario_minimo;
                // Arredondar para próximo intervalo
                $minutos = (int)$hora_atual->format('i');
                $resto = $minutos % $intervalo;
                if ($resto > 0) {
                    $ajuste = $intervalo - $resto;
                    $hora_atual->add(new DateInterval('PT' . $ajuste . 'M'));
                }
            }
        }

        while ($hora_atual < $hora_fim_estab) {
            $hora_inicio_str = $hora_atual->format('H:i:s');
            $hora_fim_temp = clone $hora_atual;
            $hora_fim_temp->add(new DateInterval('PT' . $servico->duracao . 'M'));
            $hora_fim_str = $hora_fim_temp->format('H:i:s');

            // Verificar se horário está disponível
            if ($hora_fim_temp <= $hora_fim_estab) {
                if ($this->Agendamento_model->verificar_disponibilidade(
                    $profissional_id,
                    $data,
                    $hora_inicio_str,
                    $hora_fim_str,
                    $servico_id,
                    $agendamento_id // Excluir este agendamento da verificação (para edição)
                )) {
                    $horarios[] = $hora_atual->format('H:i');
                }
            }

            // ✅ Avançar com intervalo (fixo ou dinâmico)
            $hora_atual->add(new DateInterval('PT' . $intervalo . 'M'));
        }

        header('Content-Type: application/json');
        echo json_encode($horarios);
    }

    /**
     * Calcular data máxima considerando apenas dias úteis (dias ativos do estabelecimento)
     */
    private function calcular_data_maxima_dias_uteis($estabelecimento_id, $dias_necessarios) {
        // Buscar horários do estabelecimento
        $horarios = $this->Horario_estabelecimento_model->get_by_estabelecimento($estabelecimento_id);

        // Criar array de dias ativos (0=domingo, 1=segunda, ..., 6=sábado)
        $dias_ativos = [];
        foreach ($horarios as $horario) {
            if ($horario->ativo) {
                $dias_ativos[] = (int)$horario->dia_semana;
            }
        }

        // Se não há dias ativos, retornar null
        if (empty($dias_ativos)) {
            return null;
        }

        // Carregar model de feriados
        $this->load->model('Feriado_model');

        // Calcular data máxima pulando dias inativos
        $data_atual = new DateTime();
        $dias_contados = 0;

        while ($dias_contados < $dias_necessarios) {
            $data_atual->add(new DateInterval('P1D')); // Adicionar 1 dia
            $dia_semana = (int)$data_atual->format('w'); // 0=domingo, 6=sábado
            $data_str = $data_atual->format('Y-m-d');

            // Contar apenas se o dia está ativo
            if (in_array($dia_semana, $dias_ativos) &&
            !$this->Feriado_model->is_feriado($data_str, $estabelecimento_id)) {
        $dias_contados++;
            }
        }  // ← Fechar o while
        return $data_atual->format('Y-m-d');
    }

    /**
     * Exibir página de pagamento PIX
     *
     * @param int $id ID do agendamento
     */
    public function pagamento($id) {
        $agendamento = $this->Agendamento_model->get($id);

        // Verificar permissão
        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            show_404();
        }

        // Verificar se tem pagamento pendente
        if ($agendamento->pagamento_status != 'pendente') {
            $this->session->set_flashdata('warning', 'Este agendamento não possui pagamento pendente.');
            redirect('painel/agendamentos');
            return;
        }

        $data['titulo'] = 'Pagamento do Agendamento';
        $data['menu_ativo'] = 'agendamentos';
        $data['agendamento'] = $agendamento;

        $this->load->view('painel/agendamentos/pagamento', $data);
    }

    /**
     * Verificar status do pagamento (AJAX)
     *
     * @param int $id ID do agendamento
     */
    public function verificar_pagamento($id) {
        header('Content-Type: application/json');

        $agendamento = $this->Agendamento_model->get($id);

        // Verificar permissão
        if (!$agendamento || $agendamento->estabelecimento_id != $this->estabelecimento_id) {
            echo json_encode(['error' => 'Not found']);
            return;
        }

        // Verificar se expirou
        if ($agendamento->pagamento_expira_em && strtotime($agendamento->pagamento_expira_em) < time()) {
            // Marcar como expirado se ainda estiver pendente
            if ($agendamento->pagamento_status == 'pendente') {
                $this->Agendamento_model->atualizar($id, [
                    'pagamento_status' => 'expirado'
                ]);

                echo json_encode([
                    'status' => 'expirado',
                    'valor' => $agendamento->pagamento_valor
                ]);
                return;
            }
        }

        // /Se ainda está pendente, verificar no Mercado Pago
        if ($agendamento->pagamento_status == 'pendente') {
            log_message('error', '=== POLLING: Agendamento #' . $id . ' está pendente, consultando MP...');

            $this->load->model('Pagamento_model');
            $this->load->model('Estabelecimento_model');
            $this->load->library('mercadopago_lib');

            // Buscar pagamento
            $pagamento = $this->Pagamento_model->get_by_agendamento($id);

            log_message('error', '=== POLLING: Pagamento encontrado? ' . ($pagamento ? 'SIM (MP ID: ' . $pagamento->mercadopago_id . ')' : 'NÃO'));

            if ($pagamento && $pagamento->mercadopago_id) {
                // Buscar estabelecimento para credenciais
                $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);

                // Configurar credenciais
                $access_token = $estabelecimento->mp_sandbox
                    ? $estabelecimento->mp_access_token_test
                    : $estabelecimento->mp_access_token_prod;
                $public_key = $estabelecimento->mp_sandbox
                    ? $estabelecimento->mp_public_key_test
                    : $estabelecimento->mp_public_key_prod;

                $this->mercadopago_lib->set_credentials($access_token, $public_key);

                log_message('error', '=== POLLING: Consultando MP Payment ID: ' . $pagamento->mercadopago_id);

                // Consultar status no MP
                $mp_payment = $this->mercadopago_lib->get_pagamento($pagamento->mercadopago_id);

                log_message('error', '=== POLLING: Resposta MP: ' . json_encode($mp_payment));

                // CORREÇÃO: A biblioteca retorna em 'response', não em 'data'
                if ($mp_payment && isset($mp_payment['response'])) {
                    $mp_status = $mp_payment['response']['status'];

                    log_message('error', '=== POLLING: Status MP = ' . $mp_status);

                    // Se foi aprovado, confirmar
                    if ($mp_status === 'approved') {
                        log_message('error', '=== POLLING: APROVADO! Confirmando...');

                        $this->Pagamento_model->confirmar_agendamento($id);

                        // Enviar notificações WhatsApp
                        $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'confirmacao');
                        $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'profissional_novo');

                        echo json_encode([
                            'status' => 'pago',
                            'valor' => $agendamento->pagamento_valor,
                            'redirect' => base_url('painel/agendamentos')
                        ]);
                        return;
                    }

                    // Se foi cancelado/rejeitado
                    if (in_array($mp_status, ['cancelled', 'rejected'])) {
                        $this->Agendamento_model->atualizar($id, [
                            'pagamento_status' => 'cancelado'
                        ]);

                        echo json_encode([
                            'status' => 'cancelado',
                            'valor' => $agendamento->pagamento_valor
                        ]);
                        return;
                    }
                }
            }
        }

        echo json_encode([
            'status' => $agendamento->pagamento_status,
            'valor' => $agendamento->pagamento_valor
        ]);
    }

    /**
     * Buscar serviços de um profissional (AJAX)
     *
     * @param int $profissional_id ID do profissional
     */
    public function get_servicos_profissional($profissional_id) {
        header('Content-Type: application/json');

        $this->load->model('Profissional_model');
        $servicos = $this->Profissional_model->get_servicos($profissional_id);

        echo json_encode($servicos);
    }
}
