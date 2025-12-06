<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/bloqueios') ?>">Bloqueios</a>
                </div>
                <h2 class="page-title">
                    <?= isset($bloqueio) ? 'Editar Bloqueio' : 'Novo Bloqueio' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form method="post">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados do Bloqueio -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Bloqueio</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Profissional</label>
                                <select class="form-select" name="profissional_id"
                                        <?= isset($bloqueio) ? 'disabled' : '' ?> required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($profissionais as $prof): ?>
                                    <option value="<?= $prof->id ?>"
                                            <?= set_select('profissional_id', $prof->id, ($bloqueio->profissional_id ?? '') == $prof->id) ?>>
                                        <?= $prof->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('profissional_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data Início</label>
                                    <input type="date" class="form-control" name="data_inicio"
                                           value="<?= set_value('data_inicio', $bloqueio->data_inicio ?? '') ?>" required>
                                    <?= form_error('data_inicio', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data Fim</label>
                                    <input type="date" class="form-control" name="data_fim"
                                           value="<?= set_value('data_fim', $bloqueio->data_fim ?? '') ?>" required>
                                    <?= form_error('data_fim', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora Início (opcional)</label>
                                    <input type="time" class="form-control" name="hora_inicio"
                                           value="<?= set_value('hora_inicio', $bloqueio->hora_inicio ?? '') ?>">
                                    <small class="text-muted">Deixe em branco para bloquear o dia inteiro</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora Fim (opcional)</label>
                                    <input type="time" class="form-control" name="hora_fim"
                                           value="<?= set_value('hora_fim', $bloqueio->hora_fim ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Motivo</label>
                                <textarea class="form-control" name="motivo" rows="3"><?= set_value('motivo', $bloqueio->motivo ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Tipo -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tipo de Bloqueio</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-select" name="tipo" required>
                                <option value="ferias" <?= set_select('tipo', 'ferias', ($bloqueio->tipo ?? '') == 'ferias') ?>>Férias</option>
                                <option value="folga" <?= set_select('tipo', 'folga', ($bloqueio->tipo ?? '') == 'folga') ?>>Folga</option>
                                <option value="almoco" <?= set_select('tipo', 'almoco', ($bloqueio->tipo ?? '') == 'almoco') ?>>Almoço</option>
                                <option value="outro" <?= set_select('tipo', 'outro', ($bloqueio->tipo ?? '') == 'outro') ?>>Outro</option>
                            </select>
                            <?= form_error('tipo', '<div class="invalid-feedback d-block">', '</div>') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url('admin/bloqueios') ?>" class="btn btn-link">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>
                        Salvar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
