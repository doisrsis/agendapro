<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-chart-bar me-2"></i>
                    Relatórios
                </h2>
                <div class="text-muted mt-1">Análise detalhada do seu negócio</div>
            </div>
            <!-- Botão Imprimir -->
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
                    <i class="ti ti-printer me-2"></i>
                    Imprimir Relatório
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <!-- Filtros -->
        <div class="card mb-3 d-print-none">
            <div class="card-body">
                <form action="<?= base_url('painel/relatorios') ?>" method="get">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Data Inicial</label>
                            <input type="date" name="data_inicio" class="form-control" value="<?= $filtros['data_inicio'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data Final</label>
                            <input type="date" name="data_fim" class="form-control" value="<?= $filtros['data_fim'] ?>">
                        </div>
                         <div class="col-md-3">
                            <label class="form-label">Profissional</label>
                            <select name="profissional_id" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($profissionais as $prof): ?>
                                    <option value="<?= $prof->id ?>" <?= $filtros['profissional_id'] == $prof->id ? 'selected' : '' ?>>
                                        <?= $prof->nome ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter me-2"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resumo -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Faturamento Total</div>
                        </div>
                        <div class="h1 mb-3 text-success">R$ <?= number_format($receita_total, 2, ',', '.') ?></div>
                        <div class="text-muted">No período selecionado</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total de Agendamentos</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_agendamentos ?></div>
                        <div class="text-muted">Confirmados / Concluídos</div>
                    </div>
                </div>
            </div>
             <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Ticket Médio</div>
                        </div>
                        <div class="h1 mb-3">R$ <?= number_format($ticket_medio, 2, ',', '.') ?></div>
                        <div class="text-muted">Por agendamento</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <!-- Ranking Serviços -->
            <div class="col-md-6">
                <div class="card">
                     <div class="card-header">
                        <h3 class="card-title">Serviços Mais Realizados</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ranking_servicos)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">Sem dados</td></tr>
                                <?php else: ?>
                                    <?php foreach ($ranking_servicos as $servico): ?>
                                    <tr>
                                        <td><?= $servico['nome'] ?></td>
                                        <td class="text-center"><?= $servico['qtd'] ?></td>
                                        <td class="text-end">R$ <?= number_format($servico['receita'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ranking Profissionais -->
            <div class="col-md-6">
                 <div class="card">
                     <div class="card-header">
                        <h3 class="card-title">Desempenho por Profissional</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Profissional</th>
                                    <th class="text-center">Atendimentos</th>
                                    <th class="text-end">Receita Gerada</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php if (empty($ranking_profissionais)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">Sem dados</td></tr>
                                <?php else: ?>
                                    <?php foreach ($ranking_profissionais as $prof): ?>
                                    <tr>
                                        <td><?= $prof['nome'] ?></td>
                                        <td class="text-center"><?= $prof['qtd'] ?></td>
                                        <td class="text-end">R$ <?= number_format($prof['receita'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
