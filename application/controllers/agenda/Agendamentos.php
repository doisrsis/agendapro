<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Agendamentos da Agenda
 *
 * CRUD de agendamentos para profissionais
 *
 * @author Rafael Dias - doisr.com.br
 * @date 11/12/2024
 */
class Agendamentos extends Agenda_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Agendamento_model');
        $this->load->model('Cliente_model');
        $this->load->model('Servico_model');
    }

    /**
     * Criar novo agendamento
     */
    public function criar() {
        if ($this->input->method() === 'post') {
            log_message('debug', 'Agenda/Agendamentos/criar - POST recebido');

            $this->form_validation->set_rules('cliente_id', 'Cliente', 'required');
            $this->form_validation->set_rules('servico_id', 'Serviço', 'required');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');

            if ($this->form_validation->run()) {
                log_message('debug', 'Agenda/Agendamentos/criar - Validação OK');

                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id,
                    'profissional_id' => $this->profissional_id,
                    'cliente_id' => $this->input->post('cliente_id'),
                    'servico_id' => $this->input->post('servico_id'),
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => $this->input->post('status') ?: 'pendente',
                    'observacoes' => $this->input->post('observacoes')
                ];

                // Criar data_hora combinada
                $dados['data_hora'] = $dados['data'] . ' ' . $dados['hora_inicio'];

                log_message('debug', 'Agenda/Agendamentos/criar - Dados: ' . json_encode($dados));

                $agendamento_id = $this->Agendamento_model->create($dados);
                log_message('debug', 'Agenda/Agendamentos/criar - Resultado create: ' . ($agendamento_id ? $agendamento_id : 'false'));

                if ($agendamento_id) {
                    log_message('debug', 'Agenda/Agendamentos/criar - Agendamento criado com sucesso');

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

                        if ($pix_result && isset($pix_result['response']) && in_array($pix_result['status'], [200, 201])) {
                            $pix_data = $pix_result['response'];

                            // Gerar token único para acesso público
                            $token_pagamento = $this->Agendamento_model->gerar_token_pagamento();

                            // Salvar dados do PIX no agendamento
                            $this->Agendamento_model->update($agendamento_id, [
                                'pagamento_status' => 'pendente',
                                'pagamento_valor' => $valor,
                                'pagamento_pix_qrcode' => $pix_data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                                'pagamento_pix_copia_cola' => $pix_data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                                'pagamento_expira_em' => date('Y-m-d H:i:s', strtotime("+{$estabelecimento->agendamento_tempo_expiracao_pix} minutes")),
                                'pagamento_token' => $token_pagamento,
                                'pagamento_lembrete_enviado' => 0,
                                'status' => 'pendente'
                            ]);

                            // Criar registro de pagamento
                            $this->Pagamento_model->criar_agendamento([
                                'estabelecimento_id' => $estabelecimento->id,
                                'agendamento_id' => $agendamento_id,
                                'valor' => $valor,
                                'metodo' => 'pix',
                                'status' => 'pendente',
                                'mercadopago_id' => $pix_data['id'],
                                'mercadopago_status' => $pix_data['status']
                            ]);

                            // Redirecionar para página de pagamento
                            redirect('agenda/agendamentos/pagamento/' . $agendamento_id);
                            return;
                        } else {
                            log_message('error', 'Agenda/Agendamentos/criar - Erro ao gerar PIX');
                            $this->session->set_flashdata('erro', 'Erro ao gerar PIX. Agendamento criado, mas pagamento pendente.');
                        }
                    }

                    $this->session->set_flashdata('sucesso', 'Agendamento criado com sucesso!');
                    redirect('agenda/dashboard');
                } else {
                    log_message('error', 'Agenda/Agendamentos/criar - Falha ao criar agendamento');

                    // Verificar se há mensagem de erro específica
                    $erro_msg = $this->Agendamento_model->erro_disponibilidade ?? 'Erro ao criar agendamento.';
                    $this->session->set_flashdata('erro', $erro_msg);
                }
            } else {
                log_message('debug', 'Agenda/Agendamentos/criar - Validação falhou: ' . validation_errors());
            }
        }

        $data['titulo'] = 'Novo Agendamento';
        $data['menu_ativo'] = 'agenda';

        // Carregar clientes do estabelecimento
        $data['clientes'] = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);

        // Carregar serviços do profissional
        $data['servicos'] = $this->Profissional_model->get_servicos($this->profissional_id);

        // Calcular data máxima baseada no período de abertura
        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $dias_antecedencia = $estabelecimento->dias_antecedencia_agenda ?? 30;
        $this->load->model('Horario_estabelecimento_model');
        $data['data_maxima'] = $dias_antecedencia > 0
            ? $this->calcular_data_maxima_dias_uteis($this->estabelecimento_id, $dias_antecedencia)
            : null;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/agendamentos/form', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Editar agendamento
     */
    public function editar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        // Verificar se agendamento existe e pertence ao profissional
        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        if ($this->input->method() === 'post') {
            log_message('debug', 'Agenda/Agendamentos/editar - POST recebido para ID: ' . $id);

            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicio', 'Horário', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run()) {
                log_message('debug', 'Agenda/Agendamentos/editar - Validação OK');

                // Guardar data/hora anterior para verificar se houve reagendamento
                $data_anterior = $agendamento->data;
                $hora_anterior = $agendamento->hora_inicio;

                $dados = [
                    'data' => $this->input->post('data'),
                    'hora_inicio' => $this->input->post('hora_inicio'),
                    'status' => $this->input->post('status'),
                    'observacoes' => $this->input->post('observacoes')
                ];

                log_message('debug', 'Agenda/Agendamentos/editar - Dados: ' . json_encode($dados));

                $result = $this->Agendamento_model->update($id, $dados);
                log_message('debug', 'Agenda/Agendamentos/editar - Resultado update: ' . ($result ? 'true' : 'false'));

                if ($result) {
                    // Verificar se houve mudança de data/hora (reagendamento)
                    if ($dados['data'] != $data_anterior || $dados['hora_inicio'] != $hora_anterior) {
                        // Notificar cliente
                        $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'reagendamento', [
                            'data_anterior' => $data_anterior,
                            'hora_anterior' => $hora_anterior
                        ]);
                        // Notificar profissional/estabelecimento
                        $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'profissional_reagendamento', [
                            'data_anterior' => $data_anterior,
                            'hora_anterior' => $hora_anterior
                        ]);
                    }

                    log_message('debug', 'Agenda/Agendamentos/editar - Agendamento atualizado com sucesso');
                    $this->session->set_flashdata('sucesso', 'Agendamento atualizado com sucesso!');
                    redirect('agenda/dashboard');
                } else {
                    log_message('error', 'Agenda/Agendamentos/editar - Falha ao atualizar agendamento');

                    // Verificar se há mensagem de erro específica
                    $erro_msg = $this->Agendamento_model->erro_disponibilidade ?? 'Erro ao atualizar agendamento.';
                    $this->session->set_flashdata('erro', $erro_msg);
                }
            } else {
                log_message('debug', 'Agenda/Agendamentos/editar - Validação falhou: ' . validation_errors());
            }
        }

        $data['titulo'] = 'Editar Agendamento';
        $data['menu_ativo'] = 'agenda';
        $data['agendamento'] = $agendamento;

        // Calcular data máxima baseada no período de abertura
        $this->load->model('Estabelecimento_model');
        $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);
        $dias_antecedencia = $estabelecimento->dias_antecedencia_agenda ?? 30;
        $data['data_maxima'] = $dias_antecedencia > 0
            ? $this->calcular_data_maxima_dias_uteis($this->estabelecimento_id, $dias_antecedencia)
            : null;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/agendamentos/editar', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * API: Retorna horários disponíveis para agendamento
     * @param agendamento_id (opcional) - ID do agendamento sendo editado (para excluir da verificação)
     */
    public function get_horarios_disponiveis() {
        $servico_id = $this->input->get('servico_id');
        $data = $this->input->get('data');
        $agendamento_id = $this->input->get('agendamento_id'); // Para edição

        if (!$servico_id || !$data) {
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
                    $this->profissional_id,
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
     * Cancelar agendamento
     */
    public function cancelar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        // Verificar se agendamento existe e pertence ao profissional
        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        if ($this->Agendamento_model->cancelar($id, 'profissional')) {
            $this->session->set_flashdata('sucesso', 'Agendamento cancelado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cancelar agendamento.');
        }

        redirect('agenda/dashboard');
    }

    /**
     * Iniciar atendimento
     * Muda status para 'em_atendimento' e notifica cliente
     */
    public function iniciar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        // Verificar se pode iniciar (apenas confirmado ou pendente)
        if (!in_array($agendamento->status, ['confirmado', 'pendente'])) {
            $this->session->set_flashdata('erro', 'Este agendamento não pode ser iniciado.');
            redirect('agenda/dashboard');
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

        redirect('agenda/dashboard');
    }

    /**
     * Finalizar atendimento
     * Muda status para 'finalizado' e notifica cliente
     */
    public function finalizar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        // Verificar se pode finalizar (apenas em_atendimento)
        if ($agendamento->status !== 'em_atendimento') {
            $this->session->set_flashdata('erro', 'Este agendamento não está em atendimento.');
            redirect('agenda/dashboard');
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

        redirect('agenda/dashboard');
    }

    /**
     * Visualizar agendamento
     */
    public function visualizar($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            $this->session->set_flashdata('erro', 'Agendamento não encontrado.');
            redirect('agenda/dashboard');
        }

        $data['titulo'] = 'Visualizar Agendamento';
        $data['menu_ativo'] = 'agenda';
        $data['agendamento'] = $agendamento;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/agendamentos/visualizar', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Exibir página de pagamento PIX
     *
     * @param int $id ID do agendamento
     */
    public function pagamento($id) {
        $agendamento = $this->Agendamento_model->get_by_id($id);

        // Verificar permissão
        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            show_404();
        }

        // Verificar se tem pagamento pendente
        if ($agendamento->pagamento_status != 'pendente') {
            $this->session->set_flashdata('warning', 'Este agendamento não possui pagamento pendente.');
            redirect('agenda/dashboard');
            return;
        }

        $data['titulo'] = 'Pagamento do Agendamento';
        $data['menu_ativo'] = 'agenda';
        $data['agendamento'] = $agendamento;

        $this->load->view('agenda/layout/header', $data);
        $this->load->view('agenda/agendamentos/pagamento', $data);
        $this->load->view('agenda/layout/footer');
    }

    /**
     * Verificar status do pagamento (AJAX)
     *
     * @param int $id ID do agendamento
     */
    public function verificar_pagamento($id) {
        header('Content-Type: application/json');

        $agendamento = $this->Agendamento_model->get_by_id($id);

        // Verificar permissão
        if (!$agendamento || $agendamento->profissional_id != $this->profissional_id) {
            echo json_encode(['error' => 'Not found']);
            return;
        }

        // Verificar se expirou
        if ($agendamento->pagamento_expira_em && strtotime($agendamento->pagamento_expira_em) < time()) {
            if ($agendamento->pagamento_status == 'pendente') {
                $this->Agendamento_model->update($id, [
                    'pagamento_status' => 'expirado'
                ]);

                echo json_encode([
                    'status' => 'expirado',
                    'valor' => $agendamento->pagamento_valor
                ]);
                return;
            }
        }

        // Se ainda está pendente, verificar no Mercado Pago
        if ($agendamento->pagamento_status == 'pendente') {
            $this->load->model('Pagamento_model');
            $this->load->model('Estabelecimento_model');
            $this->load->library('mercadopago_lib');

            $pagamento = $this->Pagamento_model->get_by_agendamento($id);

            if ($pagamento && $pagamento->mercadopago_id) {
                $estabelecimento = $this->Estabelecimento_model->get($this->estabelecimento_id);

                $access_token = $estabelecimento->mp_sandbox
                    ? $estabelecimento->mp_access_token_test
                    : $estabelecimento->mp_access_token_prod;
                $public_key = $estabelecimento->mp_sandbox
                    ? $estabelecimento->mp_public_key_test
                    : $estabelecimento->mp_public_key_prod;

                $this->mercadopago_lib->set_credentials($access_token, $public_key);

                $mp_payment = $this->mercadopago_lib->get_pagamento($pagamento->mercadopago_id);

                if ($mp_payment && isset($mp_payment['response'])) {
                    $mp_status = $mp_payment['response']['status'];

                    if ($mp_status === 'approved') {
                        // Confirmar agendamento e enviar notificações
                        $this->Pagamento_model->confirmar_agendamento($id);

                        // Enviar notificações WhatsApp
                        $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'confirmacao');
                        $this->Agendamento_model->enviar_notificacao_whatsapp($id, 'profissional_novo');

                        echo json_encode([
                            'status' => 'pago',
                            'valor' => $agendamento->pagamento_valor,
                            'redirect' => base_url('agenda/dashboard')
                        ]);
                        return;
                    }

                    if (in_array($mp_status, ['cancelled', 'rejected'])) {
                        $this->Agendamento_model->update($id, [
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
     * Calcular data máxima considerando apenas dias úteis (dias ativos do estabelecimento)
     */
    private function calcular_data_maxima_dias_uteis($estabelecimento_id, $dias_necessarios) {
        // Carregar model se necessário
        if (!isset($this->Horario_estabelecimento_model)) {
            $this->load->model('Horario_estabelecimento_model');
        }

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
        }

        return $data_atual->format('Y-m-d');
    }
}
