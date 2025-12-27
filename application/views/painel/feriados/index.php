<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Configurações
                </div>
                <h2 class="page-title">
                    <i class="ti ti-calendar-off me-2"></i>
                    Feriados
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="<?= base_url('painel/feriados/gerar_moveis?ano=' . (date('Y') + 1)) ?>"
                       class="btn btn-outline-primary">
                        <i class="ti ti-refresh me-2"></i>
                        Gerar Feriados Móveis <?= date('Y') + 1 ?>
                    </a>
                    <a href="<?= base_url('painel/feriados/criar') ?>" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Novo Feriado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Ano</label>
                        <select name="ano" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($anos as $a): ?>
                            <option value="<?= $a ?>" <?= $a == $ano_selecionado ? 'selected' : '' ?>>
                                <?= $a ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="nacional" <?= $tipo_selecionado == 'nacional' ? 'selected' : '' ?>>
                                Nacional
                            </option>
                            <option value="facultativo" <?= $tipo_selecionado == 'facultativo' ? 'selected' : '' ?>>
                                Facultativo
                            </option>
                            <option value="municipal" <?= $tipo_selecionado == 'municipal' ? 'selected' : '' ?>>
                                Municipal
                            </option>
                            <option value="personalizado" <?= $tipo_selecionado == 'personalizado' ? 'selected' : '' ?>>
                                Personalizado
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <a href="<?= base_url('painel/feriados') ?>" class="btn btn-secondary">
                            <i class="ti ti-x me-2"></i>
                            Limpar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Feriados -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Feriados de <?= $ano_selecionado ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th class="text-center">Recorrente</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feriados)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="ti ti-calendar-off" style="font-size: 48px;"></i>
                                <p class="mt-2">Nenhum feriado encontrado</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($feriados as $feriado): ?>
                            <tr>
                                <td>
                                    <strong><?= date('d/m', strtotime($feriado->data)) ?></strong>
                                    <small class="text-muted d-block">
                                        <?php
                                        $dias_semana = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                                        echo $dias_semana[date('w', strtotime($feriado->data))];
                                        ?>
                                    </small>
                                </td>
                                <td><?= $feriado->nome ?></td>
                                <td>
                                    <?php
                                    $badges = [
                                        'nacional' => 'bg-blue',
                                        'facultativo' => 'bg-cyan',
                                        'municipal' => 'bg-green',
                                        'personalizado' => 'bg-purple'
                                    ];
                                    $badge = $badges[$feriado->tipo] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badge ?>">
                                        <?= ucfirst($feriado->tipo) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($feriado->recorrente): ?>
                                        <i class="ti ti-check text-success" title="Repete todo ano"></i>
                                    <?php else: ?>
                                        <i class="ti ti-x text-muted" title="Data única"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($feriado->ativo): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-list justify-content-end">
                                        <!-- Toggle Ativo/Inativo -->
                                        <a href="<?= base_url('painel/feriados/toggle/' . $feriado->id) ?>"
                                           class="btn btn-sm btn-ghost-secondary"
                                           title="<?= $feriado->ativo ? 'Desativar' : 'Ativar' ?>">
                                            <i class="ti ti-toggle-<?= $feriado->ativo ? 'right' : 'left' ?>"></i>
                                        </a>

                                        <!-- Editar (apenas personalizados) -->
                                        <?php if ($feriado->estabelecimento_id): ?>
                                        <a href="<?= base_url('painel/feriados/editar/' . $feriado->id) ?>"
                                           class="btn btn-sm btn-ghost-primary"
                                           title="Editar">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <!-- Deletar (apenas personalizados) -->
                                        <a href="<?= base_url('painel/feriados/deletar/' . $feriado->id) ?>"
                                           class="btn btn-sm btn-ghost-danger"
                                           onclick="return confirm('Tem certeza que deseja deletar este feriado?')"
                                           title="Deletar">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted small">Nacional</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legenda -->
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Legenda</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">
                            <span class="badge bg-blue me-2">Nacional</span>
                            Feriados nacionais obrigatórios
                        </p>
                        <p class="mb-1">
                            <span class="badge bg-cyan me-2">Facultativo</span>
                            Pontos facultativos (Carnaval, Corpus Christi)
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">
                            <span class="badge bg-green me-2">Municipal</span>
                            Feriados municipais
                        </p>
                        <p class="mb-1">
                            <span class="badge bg-purple me-2">Personalizado</span>
                            Feriados personalizados do estabelecimento
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Configurar locale para português
moment.locale('pt-br');
</script>
