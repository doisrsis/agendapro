<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-calendar me-2"></i>
                    Minha Agenda
                </h2>
                <div class="text-muted mt-1">Olá, <?= $profissional->nome ?>! Aqui está sua agenda</div>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control" value="<?= $data_selecionada ?>"
                       onchange="window.location.href='<?= base_url('agenda/dashboard?data=') ?>' + this.value">
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <!-- Estatísticas -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Hoje</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_agendamentos_hoje ?></div>
                        <div class="d-flex mb-2">
                            <div>Agendamentos</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Confirmados</div>
                        </div>
                        <div class="h1 mb-3"><?= $agendamentos_confirmados ?></div>
                        <div class="d-flex mb-2">
                            <div>Para hoje</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Concluídos</div>
                        </div>
                        <div class="h1 mb-3"><?= $agendamentos_concluidos ?></div>
                        <div class="d-flex mb-2">
                            <div>Hoje</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Este Mês</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_agendamentos_mes ?></div>
                        <div class="d-flex mb-2">
                            <div>Agendamentos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Agenda do Dia -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Agenda de <?= date('d/m/Y', strtotime($data_selecionada)) ?></h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($agendamentos_dia)): ?>
                        <div class="list-group-item">
                            <div class="text-muted text-center py-4">
                                <i class="ti ti-calendar-off icon mb-2" style="font-size: 3rem;"></i>
                                <p class="mb-0">Nenhum agendamento para esta data</p>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($agendamentos_dia as $agendamento): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 0.75rem;">
                                        <?= date('H:i', strtotime($agendamento->hora_inicio)) ?>
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <span class="avatar avatar-md"><?= substr($agendamento->cliente_nome, 0, 2) ?></span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate">
                                        <strong><?= $agendamento->cliente_nome ?></strong>
                                    </div>
                                    <div class="text-muted"><?= $agendamento->servico_nome ?></div>
                                    <?php if ($agendamento->observacoes): ?>
                                    <div class="text-muted small">
                                        <i class="ti ti-note"></i> <?= $agendamento->observacoes ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto">
                                    <?php
                                    $badge_class = 'secondary';
                                    $status_text = $agendamento->status;

                                    switch ($agendamento->status) {
                                        case 'confirmado':
                                            $badge_class = 'success';
                                            $status_text = 'Confirmado';
                                            break;
                                        case 'concluido':
                                            $badge_class = 'primary';
                                            $status_text = 'Concluído';
                                            break;
                                        case 'cancelado':
                                            $badge_class = 'danger';
                                            $status_text = 'Cancelado';
                                            break;
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badge_class ?>"><?= $status_text ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Próximos Agendamentos -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Próximos 7 Dias</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($proximos_agendamentos)): ?>
                        <div class="list-group-item">
                            <div class="text-muted text-center py-3">
                                <i class="ti ti-calendar-event icon mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0 small">Nenhum agendamento próximo</p>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($proximos_agendamentos as $agendamento): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-truncate">
                                        <strong><?= $agendamento->cliente_nome ?></strong>
                                    </div>
                                    <div class="text-muted small"><?= $agendamento->servico_nome ?></div>
                                    <div class="text-muted small">
                                        <i class="ti ti-calendar"></i> <?= date('d/m', strtotime($agendamento->data)) ?>
                                        às <?= date('H:i', strtotime($agendamento->hora_inicio)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Meus Serviços -->
                <?php if (!empty($servicos)): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Meus Serviços</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($servicos as $servico): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-truncate">
                                        <strong><?= $servico->nome ?></strong>
                                    </div>
                                    <div class="text-muted small">
                                        <?= $servico->duracao ?> min - R$ <?= number_format($servico->preco, 2, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
