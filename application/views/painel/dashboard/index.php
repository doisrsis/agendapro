<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-dashboard me-2"></i>
                    Dashboard
                </h2>
                <div class="text-muted mt-1">Bem-vindo ao painel do seu estabelecimento</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body --->
<div class="page-body">
    <div class="container-xl">

        <!-- Alertas de Assinatura -->
        <?php if (isset($assinatura)): ?>
            <?php if ($assinatura->status == 'trial'): ?>
            <div class="alert alert-info mb-3">
                <i class="ti ti-info-circle me-2"></i>
                Você está no período de teste. Expira em: <strong><?= date('d/m/Y', strtotime($assinatura->data_fim)) ?></strong>
            </div>
            <?php endif; ?>

            <?php
            $dias_restantes = (strtotime($assinatura->data_fim) - time()) / 86400;
            if ($assinatura->status == 'ativa' && $dias_restantes <= 7):
            ?>
            <div class="alert alert-warning mb-3">
                <i class="ti ti-alert-triangle me-2"></i>
                Sua assinatura vence em <?= ceil($dias_restantes) ?> dias. Renove para continuar usando o sistema.
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Clientes</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_clientes ?></div>
                        <div class="d-flex mb-2">
                            <div>Total de clientes cadastrados</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Profissionais</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_profissionais ?></div>
                        <?php if (isset($plano) && $plano->max_profissionais > 0): ?>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: <?= $uso_profissionais ?>%"></div>
                        </div>
                        <div class="text-muted mt-1"><?= $uso_profissionais ?>% do limite (<?= $plano->max_profissionais ?>)</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Serviços</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_servicos ?></div>
                        <div class="d-flex mb-2">
                            <div>Serviços cadastrados</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Agendamentos Hoje</div>
                        </div>
                        <div class="h1 mb-3"><?= $agendamentos_hoje ?></div>
                        <div class="d-flex mb-2">
                            <div>Confirmados para hoje</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agendamentos do Mês -->
        <?php if (isset($plano) && $plano->max_agendamentos_mes > 0): ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Agendamentos deste Mês</h3>
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="h1 mb-0"><?= $agendamentos_mes ?> / <?= $plano->max_agendamentos_mes ?></div>
                                <div class="text-muted">agendamentos realizados</div>
                            </div>
                            <div class="col-auto">
                                <div class="progress progress-sm" style="width: 200px;">
                                    <div class="progress-bar <?= $uso_agendamentos >= 90 ? 'bg-danger' : ($uso_agendamentos >= 70 ? 'bg-warning' : 'bg-primary') ?>"
                                         style="width: <?= $uso_agendamentos ?>%"></div>
                                </div>
                                <div class="text-muted mt-1"><?= $uso_agendamentos ?>% do limite</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Próximos Agendamentos -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Agendamentos de Hoje</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($proximos_agendamentos)): ?>
                        <div class="list-group-item">
                            <div class="text-muted text-center py-3">
                                <i class="ti ti-calendar-off icon mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">Nenhum agendamento para hoje</p>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($proximos_agendamentos as $agendamento): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar"><?= substr($agendamento->cliente_nome, 0, 2) ?></span>
                                </div>
                                <div class="col text-truncate">
                                    <strong><?= $agendamento->cliente_nome ?></strong>
                                    <div class="text-muted"><?= $agendamento->servico_nome ?></div>
                                    <div class="text-muted small">
                                        <i class="ti ti-user"></i> <?= $agendamento->profissional_nome ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-primary"><?= date('H:i', strtotime($agendamento->hora_inicio)) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($proximos_agendamentos)): ?>
                    <div class="card-footer">
                        <a href="<?= base_url('painel/agendamentos') ?>" class="btn btn-link">Ver todos os agendamentos</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Clientes Recentes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Clientes Recentes</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($clientes_recentes)): ?>
                        <div class="list-group-item">
                            <div class="text-muted text-center py-3">
                                <i class="ti ti-users-off icon mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">Nenhum cliente cadastrado</p>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($clientes_recentes as $cliente): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar"><?= substr($cliente->nome, 0, 2) ?></span>
                                </div>
                                <div class="col text-truncate">
                                    <strong><?= $cliente->nome ?></strong>
                                    <div class="text-muted"><?= $cliente->whatsapp ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($clientes_recentes)): ?>
                    <div class="card-footer">
                        <a href="<?= base_url('painel/clientes') ?>" class="btn btn-link">Ver todos os clientes</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
