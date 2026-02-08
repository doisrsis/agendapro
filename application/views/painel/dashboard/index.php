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
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('painel/agendamentos') ?>" class="btn btn-primary d-none d-sm-inline-block">
                        <i class="ti ti-calendar me-2"></i>
                        Ver Agenda Completa
                    </a>
                    <a href="<?= base_url('painel/agendamentos') ?>" class="btn btn-primary d-sm-none btn-icon">
                        <i class="ti ti-calendar"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body ---->
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

        <!-- Estatísticas Financeiras e Gerais -->
        <div class="row row-deck row-cards mb-3">
            <!-- Faturamento Hoje -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Faturamento Hoje</div>
                        </div>
                        <div class="h1 mb-3 text-success">R$ <?= number_format($faturamento_dia, 2, ',', '.') ?></div>
                        <div class="d-flex mb-2">
                            <div>Receita confirmada hoje</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faturamento do Mês -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Faturamento Mês</div>
                        </div>
                        <div class="h1 mb-3 text-primary">R$ <?= number_format($faturamento_mes, 2, ',', '.') ?></div>
                        <div class="d-flex mb-2">
                            <div>Receita acumulada no mês</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agendamentos Hoje -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Agendamentos Hoje</div>
                        </div>
                        <div class="h1 mb-3"><?= $agendamentos_hoje ?></div>
                        <div class="d-flex mb-2">
                            <div>Agendamentos confirmados</div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Novos Clientes -->
             <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Novos Clientes (Mês)</div>
                        </div>
                        <div class="h1 mb-3"><?= $novos_clientes_mes ?></div>
                        <div class="d-flex mb-2">
                            <div>Clientes cadastrados este mês</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Faturamento (Últimos 7 dias)</h3>
                        <div id="chart-faturamento" style="min-height: 240px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Status dos Agendamentos</h3>
                        <div id="chart-status" style="min-height: 240px;"></div>
                    </div>
                </div>
            </div>
        </div>

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
                                    <span class="badge bg-<?= $agendamento->status == 'em_atendimento' ? 'success' : 'primary' ?> me-2">
                                        <?= date('H:i', strtotime($agendamento->hora_inicio)) ?>
                                    </span>
                                    <!-- Badge de Status de Pagamento (se requer pagamento) -->
                                    <?php if($agendamento->pagamento_status == 'pago'): ?>
                                        <span class="badge bg-success" title="Pago"><i class="ti ti-currency-real"></i></span>
                                    <?php elseif($agendamento->pagamento_status == 'pendente'): ?>
                                        <span class="badge bg-warning" title="Pgto Pendente"><i class="ti ti-clock"></i></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group btn-group-sm">
                                        <?php if (in_array($agendamento->status, ['confirmado', 'pendente'])): ?>
                                        <a href="<?= base_url('painel/agendamentos/iniciar/' . $agendamento->id) ?>"
                                           class="btn btn-success btn-icon" title="Iniciar Atendimento">
                                            <i class="ti ti-player-play"></i>
                                        </a>
                                        <?php elseif ($agendamento->status == 'em_atendimento'): ?>
                                        <a href="<?= base_url('painel/agendamentos/finalizar/' . $agendamento->id) ?>"
                                           class="btn btn-warning btn-icon" title="Finalizar Atendimento">
                                            <i class="ti ti-player-stop"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('painel/agendamentos/visualizar/' . $agendamento->id) ?>"
                                           class="btn btn-outline-secondary btn-icon" title="Visualizar">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </div>
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

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Gráfico de Faturamento
    var optionsFaturamento = {
        series: [{
            name: 'Faturamento',
            data: [
                <?php
                if (!empty($grafico_faturamento)) {
                    foreach ($grafico_faturamento as $item) {
                        echo $item->total . ',';
                    }
                }
                ?>
            ]
        }],
        chart: {
            type: 'bar',
            height: 240,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '50%',
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: [
                <?php
                if (!empty($grafico_faturamento)) {
                    foreach ($grafico_faturamento as $item) {
                        echo "'" . date('d/m', strtotime($item->data)) . "',";
                    }
                }
                ?>
            ],
        },
        colors: ['#206bc4'],
        tooltip: {
            y: {
                formatter: function (val) {
                    return "R$ " + val.toFixed(2).replace('.', ',');
                }
            }
        }
    };
    var chartFaturamento = new ApexCharts(document.querySelector("#chart-faturamento"), optionsFaturamento);
    chartFaturamento.render();

    // Gráfico de Status
    var optionsStatus = {
        series: [
            <?php
            $labels = [];
            if (!empty($grafico_status)) {
                foreach ($grafico_status as $item) {
                    echo $item->total . ',';
                    $labels[] = ucfirst(str_replace('_', ' ', $item->status));
                }
            }
            ?>
        ],
        chart: {
            type: 'donut',
            height: 240,
        },
        labels: <?= json_encode($labels) ?>,
        colors: ['#206bc4', '#4299e1', '#b0c4de', '#f59f00', '#d63939'],
        legend: {
            position: 'bottom'
        },
         dataLabels: { enabled: false },
    };
    var chartStatus = new ApexCharts(document.querySelector("#chart-status"), optionsStatus);
    chartStatus.render();
});
</script>
