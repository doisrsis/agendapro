<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $agendamento->cliente_nome ?? 'Agendamento' ?>
                </h2>
                <div class="text-muted mt-1">
                    <i class="ti ti-calendar me-1"></i>
                    <?= date('d/m/Y', strtotime($agendamento->data)) ?> às <?= substr($agendamento->hora_inicio, 0, 5) ?>
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="<?= base_url('painel/agendamentos/editar/' . $agendamento->id) ?>" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i>
                        Editar
                    </a>
                    <a href="<?= base_url('painel/agendamentos') ?>" class="btn">
                        <i class="ti ti-arrow-left me-1"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detalhes do Agendamento</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Status</div>
                                <div class="datagrid-content">
                                    <?php
                                    $badges = [
                                        'pendente' => 'warning',
                                        'confirmado' => 'info',
                                        'em_atendimento' => 'primary',
                                        'concluido' => 'success',
                                        'cancelado' => 'danger',
                                        'nao_compareceu' => 'secondary'
                                    ];
                                    $status_texto = [
                                        'pendente' => 'Pendente',
                                        'confirmado' => 'Confirmado',
                                        'em_atendimento' => 'Em Atendimento',
                                        'concluido' => 'Concluído',
                                        'cancelado' => 'Cancelado',
                                        'nao_compareceu' => 'Não Compareceu'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $badges[$agendamento->status] ?? 'secondary' ?>">
                                        <?= $status_texto[$agendamento->status] ?? $agendamento->status ?>
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Cliente</div>
                                <div class="datagrid-content"><?= $agendamento->cliente_nome ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Telefone</div>
                                <div class="datagrid-content"><?= $agendamento->cliente_telefone ?? '-' ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Profissional</div>
                                <div class="datagrid-content"><?= $agendamento->profissional_nome ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Serviço</div>
                                <div class="datagrid-content"><?= $agendamento->servico_nome ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Duração</div>
                                <div class="datagrid-content"><?= $agendamento->servico_duracao ?? 30 ?> minutos</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Valor</div>
                                <div class="datagrid-content">
                                    <strong>R$ <?= number_format($agendamento->servico_valor ?? 0, 2, ',', '.') ?></strong>
                                </div>
                            </div>

                            <?php if (!empty($agendamento->forma_pagamento) && $agendamento->forma_pagamento != 'nao_definido'): ?>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Forma de Pagamento</div>
                                <div class="datagrid-content">
                                    <?php if ($agendamento->forma_pagamento == 'pix'): ?>
                                        <?php if ($agendamento->pagamento_status == 'pago'): ?>
                                            <span class="badge bg-success">
                                                <i class="ti ti-check me-1"></i>Pago via PIX
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="ti ti-clock me-1"></i>PIX Pendente
                                            </span>
                                        <?php endif; ?>
                                    <?php elseif ($agendamento->forma_pagamento == 'presencial'): ?>
                                        <span class="badge bg-info">
                                            <i class="ti ti-building-store me-1"></i>Pagamento Presencial
                                        </span>
                                    <?php elseif ($agendamento->forma_pagamento == 'cartao'): ?>
                                        <?php if ($agendamento->pagamento_status == 'pago'): ?>
                                            <span class="badge bg-success">
                                                <i class="ti ti-credit-card me-1"></i>Pago via Cartão
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">
                                                <i class="ti ti-credit-card me-1"></i>Cartão Pendente
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($agendamento->observacoes)): ?>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <div class="text-muted"><?= nl2br(htmlspecialchars($agendamento->observacoes)) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ações Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($agendamento->status == 'pendente'): ?>
                            <a href="<?= base_url('painel/agendamentos/confirmar/' . $agendamento->id) ?>" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>
                                Confirmar
                            </a>
                            <?php endif; ?>

                            <?php if (in_array($agendamento->status, ['pendente', 'confirmado'])): ?>
                            <a href="<?= base_url('painel/agendamentos/cancelar/' . $agendamento->id) ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente cancelar este agendamento?')">
                                <i class="ti ti-x me-1"></i>
                                Cancelar
                            </a>
                            <?php endif; ?>

                            <?php if ($agendamento->status == 'confirmado'): ?>
                            <a href="<?= base_url('painel/agendamentos/iniciar/' . $agendamento->id) ?>" class="btn btn-primary">
                                <i class="ti ti-play me-1"></i>
                                Iniciar Atendimento
                            </a>
                            <?php endif; ?>

                            <?php if ($agendamento->status == 'em_atendimento'): ?>
                            <a href="<?= base_url('painel/agendamentos/finalizar/' . $agendamento->id) ?>" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>
                                Finalizar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Card de Pagamento (se houver) -->
                <?php if (!empty($agendamento->pagamento_status) && $agendamento->pagamento_status != 'nao_requer'): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-credit-card me-2"></i>
                            Histórico de Pagamento
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <!-- Status do Pagamento -->
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <?php
                                        $pagamento_icons = [
                                            'pendente' => 'ti-clock text-warning',
                                            'pago' => 'ti-check text-success',
                                            'expirado' => 'ti-x text-danger',
                                            'cancelado' => 'ti-x text-danger'
                                        ];
                                        $pagamento_badges = [
                                            'pendente' => 'warning',
                                            'pago' => 'success',
                                            'expirado' => 'danger',
                                            'cancelado' => 'danger'
                                        ];
                                        $pagamento_textos = [
                                            'pendente' => 'Aguardando Pagamento',
                                            'pago' => 'Pago',
                                            'expirado' => 'Expirado',
                                            'cancelado' => 'Cancelado'
                                        ];
                                        ?>
                                        <span class="avatar bg-<?= $pagamento_badges[$agendamento->pagamento_status] ?? 'secondary' ?>-lt">
                                            <i class="ti <?= $pagamento_icons[$agendamento->pagamento_status] ?? 'ti-help' ?>"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Status do Pagamento</strong>
                                        </div>
                                        <div class="text-muted">
                                            <span class="badge bg-<?= $pagamento_badges[$agendamento->pagamento_status] ?? 'secondary' ?>">
                                                <?= $pagamento_textos[$agendamento->pagamento_status] ?? $agendamento->pagamento_status ?>
                                            </span>
                                            <?php if (!empty($agendamento->pagamento_valor)): ?>
                                            - R$ <?= number_format($agendamento->pagamento_valor, 2, ',', '.') ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expiração Original -->
                            <?php if (!empty($agendamento->pagamento_expira_em)): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-secondary-lt">
                                            <i class="ti ti-clock-hour-4 text-secondary"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>PIX Gerado</strong>
                                        </div>
                                        <div class="text-muted">
                                            Expiração: <?= date('d/m/Y H:i', strtotime($agendamento->pagamento_expira_em)) ?>
                                            <?php if (strtotime($agendamento->pagamento_expira_em) < time() && $agendamento->pagamento_status != 'pago'): ?>
                                            <span class="badge bg-danger-lt text-danger ms-1">Expirado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Lembrete Enviado -->
                            <?php if (!empty($agendamento->pagamento_lembrete_enviado) && $agendamento->pagamento_lembrete_enviado == 1): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-info-lt">
                                            <i class="ti ti-brand-whatsapp text-info"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Lembrete Enviado</strong>
                                        </div>
                                        <div class="text-muted">
                                            Cliente notificado via WhatsApp sobre pagamento pendente
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Tempo Adicional -->
                            <?php if (!empty($agendamento->pagamento_expira_adicional_em)): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-warning-lt">
                                            <i class="ti ti-clock-plus text-warning"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Prazo Adicional</strong>
                                        </div>
                                        <div class="text-muted">
                                            Expiração estendida até: <?= date('d/m/Y H:i', strtotime($agendamento->pagamento_expira_adicional_em)) ?>
                                            <?php if (strtotime($agendamento->pagamento_expira_adicional_em) < time() && $agendamento->pagamento_status != 'pago'): ?>
                                            <span class="badge bg-danger-lt text-danger ms-1">Expirado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Motivo do Cancelamento (se cancelado por falta de pagamento) -->
                            <?php if ($agendamento->status == 'cancelado' && !empty($agendamento->motivo_cancelamento)): ?>
                            <div class="list-group-item list-group-item-danger">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-danger-lt">
                                            <i class="ti ti-alert-circle text-danger"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Motivo do Cancelamento</strong>
                                        </div>
                                        <div class="text-muted">
                                            <?= htmlspecialchars($agendamento->motivo_cancelamento) ?>
                                            <?php if (!empty($agendamento->cancelado_por)): ?>
                                            <br><small>Cancelado por: <?= ucfirst($agendamento->cancelado_por) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Card de Cancelamento (se cancelado sem pagamento) -->
                <?php if ($agendamento->status == 'cancelado' && empty($agendamento->pagamento_status)): ?>
                <div class="card mt-3 border-danger">
                    <div class="card-header bg-danger-lt">
                        <h3 class="card-title text-danger">
                            <i class="ti ti-x me-2"></i>
                            Agendamento Cancelado
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($agendamento->motivo_cancelamento)): ?>
                        <p class="mb-1"><strong>Motivo:</strong> <?= htmlspecialchars($agendamento->motivo_cancelamento) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($agendamento->cancelado_por)): ?>
                        <p class="mb-0 text-muted"><small>Cancelado por: <?= ucfirst($agendamento->cancelado_por) ?></small></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Informações</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Criado em</div>
                                <div class="datagrid-content">
                                    <?= date('d/m/Y H:i', strtotime($agendamento->criado_em)) ?>
                                </div>
                            </div>
                            <?php if ($agendamento->atualizado_em): ?>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Atualizado em</div>
                                <div class="datagrid-content">
                                    <?= date('d/m/Y H:i', strtotime($agendamento->atualizado_em)) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
