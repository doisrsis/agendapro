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
                        </div>

                        <?php if (!empty($agendamento->observacoes)): ?>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <div class="text-muted"><?= nl2br(esc($agendamento->observacoes)) ?></div>
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
                            <!---->
