<!--
    Aba Agendamento - Versão Reorganizada com Accordion
    Autor: Rafael Dias - doisr.com.br
    Data: 16/01/2026
-->

<?php
// Calcular resumos para os badges
$tempo_min = $estabelecimento->tempo_minimo_agendamento ?? 60;
$tempo_min_texto = $tempo_min == 0 ? 'Imediato' : ($tempo_min >= 60 ? ($tempo_min / 60) . 'h' : $tempo_min . 'min');

$confirmacao_ativa = $estabelecimento->solicitar_confirmacao ?? 1;
$confirmacao_horas = $estabelecimento->confirmacao_horas_antes ?? 2;
$confirmacao_tentativas = $estabelecimento->confirmacao_max_tentativas ?? 3;
$confirmacao_intervalo = $estabelecimento->confirmacao_intervalo_tentativas_minutos ?? 20;

$lembrete_ativo = $estabelecimento->enviar_lembrete_pre_atendimento ?? 1;
$lembrete_minutos = $estabelecimento->lembrete_minutos_antes ?? 30;

$cancelamento_ativo = $estabelecimento->cancelar_nao_confirmados ?? 1;
$cancelamento_horas = $estabelecimento->cancelar_nao_confirmados_horas ?? 1;

$pagamento_ativo = ($estabelecimento->agendamento_requer_pagamento ?? 'nao') != 'nao';
?>

<div class="tab-pane active">
    <form method="post" id="form-agendamento">
        <input type="hidden" name="aba" value="agendamento">

        <!-- Campo de Busca -->
        <div class="mb-4">
            <div class="input-group input-group-lg">
                <span class="input-group-text">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text"
                       class="form-control"
                       id="busca-config"
                       placeholder="Buscar configuração... (Ex: confirmação, horário, pagamento)"
                       autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" id="limpar-busca" style="display: none;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <small class="text-muted">
                <i class="ti ti-info-circle me-1"></i>
                Digite para filtrar as configurações
            </small>
        </div>

        <!-- Accordion -->
        <div class="accordion" id="accordionConfigAgendamento">

            <!-- 1. Configurações Básicas -->
            <div class="accordion-item" data-keywords="tempo minimo agendamento periodo abertura agenda intervalo horario fixo confirmacao automatica reagendamento limite">
                <h2 class="accordion-header" id="headingBasico">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasico" aria-expanded="true">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <i class="ti ti-settings text-primary me-2"></i>
                                <strong>Configurações Básicas</strong>
                                <span class="badge bg-primary-lt ms-2">ESSENCIAL</span>
                            </div>
                            <small class="text-muted">
                                Tempo mín: <?= $tempo_min_texto ?> • Intervalo: <?= $estabelecimento->intervalo_agendamento ?? 30 ?>min
                            </small>
                        </div>
                    </button>
                </h2>
                <div id="collapseBasico" class="accordion-collapse collapse show" data-bs-parent="#accordionConfigAgendamento">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tempo Mínimo para Agendamento
                                    <i class="ti ti-help-circle text-muted ms-1"
                                       data-bs-toggle="tooltip"
                                       title="Antecedência mínima que o cliente precisa ter para fazer um agendamento"></i>
                                </label>
                                <select class="form-select" name="tempo_minimo_agendamento">
                                    <option value="0" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 0 ? 'selected' : '' ?>>Imediato</option>
                                    <option value="30" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 30 ? 'selected' : '' ?>>30 minutos</option>
                                    <option value="60" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 60 ? 'selected' : '' ?>>1 hora</option>
                                    <option value="120" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 120 ? 'selected' : '' ?>>2 horas</option>
                                    <option value="240" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 240 ? 'selected' : '' ?>>4 horas</option>
                                    <option value="1440" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 1440 ? 'selected' : '' ?>>1 dia</option>
                                </select>
                                <small class="text-muted">Cliente só vê horários com esta antecedência</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Período de Abertura da Agenda
                                    <i class="ti ti-help-circle text-muted ms-1"
                                       data-bs-toggle="tooltip"
                                       title="Quantos dias para frente o cliente pode visualizar e agendar"></i>
                                </label>
                                <select class="form-select" name="dias_antecedencia_agenda">
                                    <option value="0" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 0 ? 'selected' : '' ?>>Sem limite</option>
                                    <option value="7" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 7 ? 'selected' : '' ?>>1 semana (7 dias)</option>
                                    <option value="15" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 15 ? 'selected' : '' ?>>Quinzenal (15 dias)</option>
                                    <option value="30" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 30 ? 'selected' : '' ?>>Mensal (30 dias)</option>
                                    <option value="60" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 60 ? 'selected' : '' ?>>Bimestral (60 dias)</option>
                                    <option value="90" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 90 ? 'selected' : '' ?>>Trimestral (90 dias)</option>
                                </select>
                                <small class="text-muted">Limite de dias futuros visíveis</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <input type="hidden" name="usar_intervalo_fixo" value="0">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           name="usar_intervalo_fixo"
                                           id="usar_intervalo_fixo"
                                           value="1"
                                           <?= ($estabelecimento->usar_intervalo_fixo ?? 1) ? 'checked' : '' ?>>
                                    <span class="form-check-label">
                                        <strong>Usar Intervalo Fixo</strong>
                                    </span>
                                </label>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti ti-info-circle me-1"></i>
                                    <strong>Ativado:</strong> Todos os serviços usarão o mesmo intervalo configurado abaixo.<br>
                                    <i class="ti ti-info-circle me-1"></i>
                                    <strong>Desativado:</strong> O intervalo será calculado automaticamente baseado na duração de cada serviço.
                                </small>
                            </div>

                            <div class="col-md-6 mb-3" id="campo-intervalo">
                                <label class="form-label">Intervalo de Horários</label>
                                <select class="form-select" name="intervalo_agendamento">
                                    <option value="5" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 5 ? 'selected' : '' ?>>5 minutos</option>
                                    <option value="10" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 10 ? 'selected' : '' ?>>10 minutos</option>
                                    <option value="15" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 15 ? 'selected' : '' ?>>15 minutos</option>
                                    <option value="30" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 30 ? 'selected' : '' ?>>30 minutos (padrão)</option>
                                </select>
                                <small class="text-muted">Intervalo entre horários disponíveis</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="confirmacao_automatica" value="1"
                                       <?= ($estabelecimento->confirmacao_automatica ?? 0) ? 'checked' : '' ?>>
                                <span class="form-check-label">Confirmação Automática de Agendamentos</span>
                            </label>
                            <small class="text-muted d-block">Agendamentos serão confirmados automaticamente sem necessidade de aprovação manual</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="permite_reagendamento" id="permite_reagendamento" value="1"
                                       <?= ($estabelecimento->permite_reagendamento ?? 1) ? 'checked' : '' ?>>
                                <span class="form-check-label">Permitir Reagendamento</span>
                            </label>
                            <small class="text-muted d-block">Clientes podem reagendar seus próprios agendamentos</small>
                        </div>

                        <div class="row" id="limite_reagendamentos_container" style="display: <?= ($estabelecimento->permite_reagendamento ?? 1) ? 'block' : 'none' ?>">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Limite de Reagendamentos</label>
                                <input type="number" class="form-control" name="limite_reagendamentos"
                                       value="<?= $estabelecimento->limite_reagendamentos ?? 3 ?>" min="1" max="10">
                                <small class="text-muted">Quantidade máxima de vezes que o cliente pode reagendar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Horários de Funcionamento -->
            <div class="accordion-item" data-keywords="horarios funcionamento abertura fechamento almoco dias semana segunda terca quarta quinta sexta sabado domingo">
                <h2 class="accordion-header" id="headingHorarios">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHorarios">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <i class="ti ti-clock text-info me-2"></i>
                                <strong>Horários de Funcionamento</strong>
                                <span class="badge bg-info-lt ms-2">CONFIGURADO</span>
                            </div>
                            <small class="text-muted">
                                Defina os horários por dia da semana
                            </small>
                        </div>
                    </button>
                </h2>
                <div id="collapseHorarios" class="accordion-collapse collapse" data-bs-parent="#accordionConfigAgendamento">
                    <div class="accordion-body">
                        <p class="text-muted mb-3">Defina os horários de funcionamento do estabelecimento por dia da semana</p>

                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Dia da Semana</th>
                                        <th class="text-center" width="80">Ativo</th>
                                        <th width="120">Abertura</th>
                                        <th width="120">Fechamento</th>
                                        <th class="text-center" width="80">Almoço</th>
                                        <th width="120">Início Almoço</th>
                                        <th width="120">Fim Almoço</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $horarios_array = [];
                                    foreach ($horarios as $h) {
                                        $horarios_array[$h->dia_semana] = $h;
                                    }

                                    foreach ($dias_semana as $dia => $nome):
                                        $horario = $horarios_array[$dia] ?? null;
                                    ?>
                                    <tr>
                                        <td><strong><?= $nome ?></strong></td>
                                        <td class="text-center">
                                            <label class="form-check form-switch mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="dia_<?= $dia ?>_ativo" value="1"
                                                       <?= ($horario && $horario->ativo) ? 'checked' : '' ?>>
                                            </label>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control"
                                                   name="dia_<?= $dia ?>_inicio"
                                                   value="<?= $horario ? substr($horario->hora_inicio, 0, 5) : '08:00' ?>">
                                        </td>
                                        <td>
                                            <input type="time" class="form-control"
                                                   name="dia_<?= $dia ?>_fim"
                                                   value="<?= $horario ? substr($horario->hora_fim, 0, 5) : '18:00' ?>">
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="dia_<?= $dia ?>_almoco_ativo" value="0">
                                            <label class="form-check form-switch mb-0">
                                                <input type="checkbox" class="form-check-input"
                                                       name="dia_<?= $dia ?>_almoco_ativo" value="1"
                                                       <?= ($horario && $horario->almoco_ativo) ? 'checked' : '' ?>>
                                            </label>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm"
                                                   name="dia_<?= $dia ?>_almoco_inicio"
                                                   value="<?= $horario && $horario->almoco_inicio ? substr($horario->almoco_inicio, 0, 5) : '12:00' ?>">
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm"
                                                   name="dia_<?= $dia ?>_almoco_fim"
                                                   value="<?= $horario && $horario->almoco_fim ? substr($horario->almoco_fim, 0, 5) : '13:00' ?>">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Pagamento de Agendamentos -->
            <div class="accordion-item" data-keywords="pagamento pix mercado pago taxa fixa valor total exigir cobranca expiracao">
                <h2 class="accordion-header" id="headingPagamento">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagamento">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <i class="ti ti-credit-card text-success me-2"></i>
                                <strong>Pagamento de Agendamentos</strong>
                                <?php if ($pagamento_ativo): ?>
                                    <span class="badge bg-success ms-2">ATIVO</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2">INATIVO</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= $pagamento_ativo ? 'Pagamento obrigatório' : 'Sem pagamento' ?>
                            </small>
                        </div>
                    </button>
                </h2>
                <div id="collapsePagamento" class="accordion-collapse collapse" data-bs-parent="#accordionConfigAgendamento">
                    <div class="accordion-body">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Importante:</strong> Configure suas credenciais do Mercado Pago na aba "Mercado Pago" antes de ativar o pagamento de agendamentos.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Exigir Pagamento para Confirmar Agendamento</label>
                            <select class="form-select" name="agendamento_requer_pagamento" id="agendamento_requer_pagamento">
                                <option value="nao" <?= ($estabelecimento->agendamento_requer_pagamento ?? 'nao') == 'nao' ? 'selected' : '' ?>>Não exigir pagamento</option>
                                <option value="valor_total" <?= ($estabelecimento->agendamento_requer_pagamento ?? 'nao') == 'valor_total' ? 'selected' : '' ?>>Valor total do serviço</option>
                                <option value="taxa_fixa" <?= ($estabelecimento->agendamento_requer_pagamento ?? 'nao') == 'taxa_fixa' ? 'selected' : '' ?>>Taxa fixa de agendamento</option>
                            </select>
                            <small class="text-muted">Escolha se deseja cobrar via PIX para confirmar agendamentos</small>
                        </div>

                        <div class="mb-3" id="taxa-fixa-container" style="display: none;">
                            <label class="form-label">Valor da Taxa de Agendamento</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" name="agendamento_taxa_fixa"
                                       value="<?= $estabelecimento->agendamento_taxa_fixa ?? '10.00' ?>"
                                       step="0.01" min="0" placeholder="10.00">
                            </div>
                            <small class="text-muted">Valor fixo que será cobrado para reservar o horário</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempo de Expiração do PIX</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="agendamento_tempo_expiracao_pix"
                                           value="<?= $estabelecimento->agendamento_tempo_expiracao_pix ?? 30 ?>"
                                           min="5" max="60">
                                    <span class="input-group-text">minutos</span>
                                </div>
                                <small class="text-muted">Tempo inicial que o cliente tem para pagar o PIX</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempo Adicional após Expiração</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="agendamento_tempo_adicional_pix"
                                           value="<?= $estabelecimento->agendamento_tempo_adicional_pix ?? 5 ?>"
                                           min="0" max="30">
                                    <span class="input-group-text">minutos</span>
                                </div>
                                <small class="text-muted">Tempo extra após enviar lembrete de pagamento (0 = desativado)</small>
                            </div>
                        </div>

                        <div class="alert alert-secondary">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Como funciona:</strong> Quando o tempo inicial expirar, o cliente receberá uma notificação no WhatsApp
                            com um link para pagar. Ele terá o tempo adicional configurado para concluir o pagamento antes do agendamento ser cancelado.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Confirmações -->
            <div class="accordion-item" data-keywords="confirmacao solicitar horas antes dia anterior tentativas intervalo cancelar automatico">
                <h2 class="accordion-header" id="headingConfirmacoes">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfirmacoes">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <i class="ti ti-check-circle text-success me-2"></i>
                                <strong>Confirmações</strong>
                                <?php if ($confirmacao_ativa): ?>
                                    <span class="badge bg-success ms-2">ATIVO</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2">INATIVO</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= $confirmacao_ativa ? "{$confirmacao_horas}h antes • {$confirmacao_tentativas}x • {$confirmacao_intervalo}min" : 'Desativado' ?>
                            </small>
                        </div>
                    </button>
                </h2>
                <div id="collapseConfirmacoes" class="accordion-collapse collapse" data-bs-parent="#accordionConfigAgendamento">
                    <div class="accordion-body">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Novo:</strong> Sistema automático de confirmação via WhatsApp para agendamentos sem pagamento obrigatório.
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="solicitar_confirmacao"
                                       id="solicitar_confirmacao"
                                       value="1"
                                       <?= ($estabelecimento->solicitar_confirmacao ?? 1) ? 'checked' : '' ?>>
                                <span class="form-check-label">
                                    <strong>Solicitar confirmação do cliente antes do agendamento</strong>
                                </span>
                            </label>
                            <small class="text-muted d-block mt-1">
                                Quando ativado, o cliente receberá uma mensagem para confirmar presença e poderá reagendar ou cancelar.
                            </small>
                        </div>

                        <div id="confirmacao_opcoes" style="display: <?= ($estabelecimento->solicitar_confirmacao ?? 1) ? 'block' : 'none' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Solicitar quantas horas antes?</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="confirmacao_horas_antes"
                                               value="<?= $estabelecimento->confirmacao_horas_antes ?? 2 ?>"
                                               min="1" max="168">
                                        <span class="input-group-text">horas</span>
                                    </div>
                                    <small class="text-muted">Exemplo: 2 = 2 horas antes do agendamento</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="confirmacao_dia_anterior"
                                               id="confirmacao_dia_anterior"
                                               value="1"
                                               <?= ($estabelecimento->confirmacao_dia_anterior ?? 1) ? 'checked' : '' ?>>
                                        <span class="form-check-label">
                                            <strong>Enviar também no dia anterior</strong>
                                        </span>
                                    </label>
                                    <small class="text-muted d-block mt-1">
                                        Envia um pedido de confirmação no dia anterior em horário fixo
                                    </small>
                                </div>
                            </div>

                            <div class="row" id="horario_dia_anterior_container" style="display: <?= ($estabelecimento->confirmacao_dia_anterior ?? 1) ? 'block' : 'none' ?>">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Horário para enviar no dia anterior</label>
                                    <input type="time" class="form-control" name="confirmacao_horario_dia_anterior"
                                           value="<?= $estabelecimento->confirmacao_horario_dia_anterior ?? '18:00:00' ?>">
                                    <small class="text-muted">Horário fixo para enviar a confirmação</small>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">🔄 Tentativas Múltiplas de Confirmação</h5>
                            <p class="text-muted small">Configure quantas vezes o sistema tentará confirmar com o cliente</p>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Máximo de tentativas</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="confirmacao_max_tentativas"
                                               value="<?= $estabelecimento->confirmacao_max_tentativas ?? 3 ?>"
                                               min="1" max="5">
                                        <span class="input-group-text">vezes</span>
                                    </div>
                                    <small class="text-muted">Quantas vezes tentar antes de cancelar (padrão: 3)</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Intervalo entre tentativas</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="confirmacao_intervalo_tentativas_minutos"
                                               value="<?= $estabelecimento->confirmacao_intervalo_tentativas_minutos ?? 20 ?>"
                                               min="15" max="180">
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <small class="text-muted">Tempo de espera entre cada tentativa (padrão: 20min)</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cancelar automaticamente?</label>
                                    <select class="form-select" name="confirmacao_cancelar_automatico">
                                        <option value="sim" <?= ($estabelecimento->confirmacao_cancelar_automatico ?? 'sim') == 'sim' ? 'selected' : '' ?>>Sim</option>
                                        <option value="nao" <?= ($estabelecimento->confirmacao_cancelar_automatico ?? 'sim') == 'nao' ? 'selected' : '' ?>>Não</option>
                                    </select>
                                    <small class="text-muted">Cancelar após todas as tentativas sem resposta</small>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Exemplo de fluxo (intervalo de 20 minutos):</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>15:00</strong> - 1ª tentativa (mensagem padrão)</li>
                                    <li><strong>15:20</strong> - 2ª tentativa (mensagem urgente) <em>se não respondeu</em></li>
                                    <li><strong>15:40</strong> - 3ª tentativa (última chance - aviso de cancelamento) <em>se não respondeu</em></li>
                                    <li><strong>16:00</strong> - Cancelamento automático <em>se não respondeu</em></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Lembretes Pré-Atendimento -->
            <div class="accordion-item" data-keywords="lembrete pre atendimento minutos antes antecedencia chegada">
                <h2 class="accordion-header" id="headingLembretes">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLembretes">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <i class="ti ti-bell text-warning me-2"></i>
                                <strong>Lembretes Pré-Atendimento</strong>
                                <?php if ($lembrete_ativo): ?>
                                    <span class="badge bg-warning ms-2">ATIVO</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2">INATIVO</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= $lembrete_ativo ? "{$lembrete_minutos}min antes" : 'Desativado' ?>
                            </small>
                        </div>
                    </button>
                </h2>
                <div id="collapseLembretes" class="accordion-collapse collapse" data-bs-parent="#accordionConfigAgendamento">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="enviar_lembrete_pre_atendimento"
                                       id="enviar_lembrete_pre_atendimento"
                                       value="1"
                                       <?= ($estabelecimento->enviar_lembrete_pre_atendimento ?? 1) ? 'checked' : '' ?>>
                                <span class="form-check-label">
                                    <strong>Enviar lembrete minutos antes do atendimento</strong>
                                </span>
                            </label>
                            <small class="text-muted d-block mt-1">
                                Cliente receberá um lembrete próximo ao horário do agendamento
                            </small>
                        </div>

                        <div id="lembrete_opcoes" style="display: <?= ($estabelecimento->enviar_lembrete_pre_atendimento ?? 1) ? 'block' : 'none' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Enviar quantos minutos antes?</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="lembrete_minutos_antes"
                                               value="<?= $estabelecimento->lembrete_minutos_antes ?? 30 ?>"
                                               min="5" max="1440">
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <small class="text-muted">Exemplo: 30 = 30 minutos antes</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pedir para chegar com antecedência de:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="lembrete_antecedencia_chegada"
                                               value="<?= $estabelecimento->lembrete_antecedencia_chegada ?? 15 ?>"
                                               min="0" max="60">
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <small class="text-muted">Tempo de antecedência sugerido na mensagem</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Cancelamento Automático -->
            <div class="accordion-item" data-keywords="cancelamento automatico nao confirmados horas antes cancelar">
                <h2 class="accordion-header" id="headingCancelamento">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCancelamento">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <div>
                                <i class="ti ti-x-circle text-danger me-2"></i>
                                <strong>Cancelamento Automático</strong>
                                <?php if ($cancelamento_ativo): ?>
                                    <span class="badge bg-danger ms-2">ATIVO</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2">INATIVO</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= $cancelamento_ativo ? "Cancela {$cancelamento_horas}h antes" : 'Desativado' ?>
                            </small>
                        </div>
                    </button>
                </h2>
                <div id="collapseCancelamento" class="accordion-collapse collapse" data-bs-parent="#accordionConfigAgendamento">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="cancelar_nao_confirmados"
                                       id="cancelar_nao_confirmados"
                                       value="1"
                                       <?= ($estabelecimento->cancelar_nao_confirmados ?? 1) ? 'checked' : '' ?>>
                                <span class="form-check-label">
                                    <strong>Cancelar automaticamente agendamentos não confirmados</strong>
                                </span>
                            </label>
                            <small class="text-muted d-block mt-1">
                                Se o cliente não confirmar presença, o agendamento será cancelado automaticamente
                            </small>
                        </div>

                        <div id="cancelamento_opcoes" style="display: <?= ($estabelecimento->cancelar_nao_confirmados ?? 1) ? 'block' : 'none' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cancelar quantas horas antes do horário?</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="cancelar_nao_confirmados_horas"
                                               value="<?= $estabelecimento->cancelar_nao_confirmados_horas ?? 1 ?>"
                                               min="1" max="24">
                                        <span class="input-group-text">horas</span>
                                    </div>
                                    <small class="text-muted">Se não confirmar até X horas antes, será cancelado</small>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Atenção:</strong> O agendamento será cancelado automaticamente se o cliente não responder à confirmação no prazo configurado. Isso libera o horário para outro cliente agendar.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Informação de Fluxo -->
        <div class="alert alert-secondary mt-4">
            <i class="ti ti-info-circle me-2"></i>
            <strong>Como funciona o fluxo completo:</strong>
            <ol class="mb-0 mt-2">
                <li>Cliente agenda sem pagamento → Status: <span class="badge bg-yellow">Pendente</span></li>
                <li>Sistema envia pedido de confirmação → Cliente responde: Confirmar | Reagendar | Cancelar</li>
                <li>Se confirmar → Status: <span class="badge bg-green">Confirmado</span> → Receberá lembrete antes do horário</li>
                <li>Se não confirmar no prazo → Cancelamento automático (se ativado) → Horário liberado</li>
            </ol>
        </div>

        <!-- Botão Salvar -->
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="ti ti-device-floppy me-2"></i>
                Salvar Todas as Configurações
            </button>
        </div>
    </form>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Busca inteligente
    const buscaInput = document.getElementById('busca-config');
    const limparBtn = document.getElementById('limpar-busca');
    const accordionItems = document.querySelectorAll('.accordion-item');

    buscaInput.addEventListener('input', function() {
        const termo = this.value.toLowerCase().trim();

        if (termo === '') {
            limparBtn.style.display = 'none';
            // Mostrar todos os itens
            accordionItems.forEach(item => {
                item.style.display = 'block';
            });
            return;
        }

        limparBtn.style.display = 'block';
        let encontrouAlgum = false;

        accordionItems.forEach(item => {
            const keywords = item.getAttribute('data-keywords') || '';
            const titulo = item.querySelector('.accordion-button strong').textContent.toLowerCase();

            if (keywords.includes(termo) || titulo.includes(termo)) {
                item.style.display = 'block';
                // Expandir automaticamente
                const collapse = item.querySelector('.accordion-collapse');
                if (collapse && !collapse.classList.contains('show')) {
                    const button = item.querySelector('.accordion-button');
                    button.click();
                }
                encontrouAlgum = true;
            } else {
                item.style.display = 'none';
            }
        });

        // Se não encontrou nada, mostrar mensagem
        if (!encontrouAlgum) {
            console.log('Nenhuma configuração encontrada para: ' + termo);
        }
    });

    limparBtn.addEventListener('click', function() {
        buscaInput.value = '';
        buscaInput.dispatchEvent(new Event('input'));
        buscaInput.focus();
    });

    // Toggle de campos condicionais
    document.getElementById('permite_reagendamento').addEventListener('change', function() {
        document.getElementById('limite_reagendamentos_container').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('agendamento_requer_pagamento').addEventListener('change', function() {
        document.getElementById('taxa-fixa-container').style.display = this.value === 'taxa_fixa' ? 'block' : 'none';
    });

    if (document.getElementById('agendamento_requer_pagamento').value === 'taxa_fixa') {
        document.getElementById('taxa-fixa-container').style.display = 'block';
    }

    document.getElementById('solicitar_confirmacao').addEventListener('change', function() {
        document.getElementById('confirmacao_opcoes').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('confirmacao_dia_anterior').addEventListener('change', function() {
        document.getElementById('horario_dia_anterior_container').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('enviar_lembrete_pre_atendimento').addEventListener('change', function() {
        document.getElementById('lembrete_opcoes').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('cancelar_nao_confirmados').addEventListener('change', function() {
        document.getElementById('cancelamento_opcoes').style.display = this.checked ? 'block' : 'none';
    });

    // Toggle Intervalo Fixo
    const toggleIntervalo = document.getElementById('usar_intervalo_fixo');
    const campoIntervalo = document.getElementById('campo-intervalo');

    function atualizaIntervalo() {
        campoIntervalo.style.display = toggleIntervalo.checked ? 'block' : 'none';
    }

    toggleIntervalo.addEventListener('change', atualizaIntervalo);
    atualizaIntervalo(); // Executar ao carregar
});
</script>

<style>
/* Estilos personalizados */
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #1e293b;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}

.accordion-item {
    transition: all 0.3s ease;
}

.accordion-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

#busca-config {
    font-size: 1.1rem;
}

#busca-config:focus {
    border-color: #206bc4;
    box-shadow: 0 0 0 0.25rem rgba(32, 107, 196, 0.1);
}

.badge {
    font-weight: 600;
    padding: 0.35em 0.65em;
}
</style>
