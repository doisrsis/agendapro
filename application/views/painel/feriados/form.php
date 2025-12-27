<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('painel/feriados') ?>">Feriados</a>
                </div>
                <h2 class="page-title">
                    <?= isset($feriado) ? 'Editar Feriado' : 'Novo Feriado' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <form method="post">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Feriado</h3>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label required">Nome do Feriado</label>
                                <input type="text" class="form-control" name="nome"
                                       value="<?= set_value('nome', $feriado->nome ?? '') ?>"
                                       placeholder="Ex: Aniversário da Cidade" required>
                                <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data</label>
                                    <input type="date" class="form-control" name="data"
                                           value="<?= set_value('data', $feriado->data ?? '') ?>" required>
                                    <?= form_error('data', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Tipo</label>
                                    <select class="form-select" name="tipo" required>
                                        <option value="">Selecione...</option>
                                        <option value="municipal"
                                                <?= set_select('tipo', 'municipal', ($feriado->tipo ?? '') == 'municipal') ?>>
                                            Municipal
                                        </option>
                                        <option value="personalizado"
                                                <?= set_select('tipo', 'personalizado', ($feriado->tipo ?? 'personalizado') == 'personalizado') ?>>
                                            Personalizado
                                        </option>
                                    </select>
                                    <?= form_error('tipo', '<div class="invalid-feedback d-block">', '</div>') ?>
                                    <small class="text-muted">
                                        Feriados nacionais são gerenciados pelo sistema
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="recorrente" value="1"
                                           <?= set_checkbox('recorrente', '1', ($feriado->recorrente ?? 1) == 1) ?>>
                                    <span class="form-check-label">
                                        Feriado recorrente (repete todo ano)
                                    </span>
                                </label>
                                <small class="text-muted d-block mt-1">
                                    Marque esta opção se o feriado se repete anualmente na mesma data
                                </small>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Ações -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ações</h3>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="ti ti-device-floppy me-2"></i>
                                Salvar Feriado
                            </button>
                            <a href="<?= base_url('painel/feriados') ?>" class="btn btn-secondary w-100">
                                <i class="ti ti-x me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>

                    <!-- Informações -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Informações</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                <i class="ti ti-info-circle me-2"></i>
                                Feriados bloqueiam automaticamente os agendamentos
                            </p>
                            <p class="text-muted small mb-2">
                                <i class="ti ti-calendar-off me-2"></i>
                                Feriados recorrentes se repetem todo ano
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="ti ti-toggle-right me-2"></i>
                                Você pode ativar/desativar feriados a qualquer momento
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
