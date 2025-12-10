<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/planos') ?>">Planos</a>
                </div>
                <h2 class="page-title">
                    <?= isset($plano) ? 'Editar Plano' : 'Novo Plano' ?>
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
                    <!-- Dados Básicos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Plano</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Nome do Plano</label>
                                <input type="text" class="form-control" name="nome"
                                       value="<?= set_value('nome', $plano->nome ?? '') ?>" required>
                                <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="descricao" rows="3"><?= set_value('descricao', $plano->descricao ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Valor Mensal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" name="valor_mensal" step="0.01"
                                               value="<?= set_value('valor_mensal', $plano->valor_mensal ?? '') ?>" required>
                                    </div>
                                    <?= form_error('valor_mensal', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Dias de Trial</label>
                                    <input type="number" class="form-control" name="trial_dias"
                                           value="<?= set_value('trial_dias', $plano->trial_dias ?? 7) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limites -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Limites do Plano</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Máx. Profissionais</label>
                                    <input type="number" class="form-control" name="max_profissionais"
                                           value="<?= set_value('max_profissionais', $plano->max_profissionais ?? 1) ?>" required>
                                    <small class="form-hint">Use 999 para ilimitado</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Máx. Agendamentos/Mês</label>
                                    <input type="number" class="form-control" name="max_agendamentos_mes"
                                           value="<?= set_value('max_agendamentos_mes', $plano->max_agendamentos_mes ?? 100) ?>" required>
                                    <small class="form-hint">Use 999999 para ilimitado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Integração Mercado Pago -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Mercado Pago</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($mp_sandbox): ?>
                            <div class="alert alert-warning mb-3">
                                <i class="ti ti-alert-triangle me-2"></i>
                                Modo Teste Ativo
                            </div>
                            <?php endif; ?>

                            <?php if (isset($plano) && $plano->mercadopago_plan_id): ?>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div>
                                    <span class="badge bg-success">Sincronizado</span>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    ID: <?= $plano->mercadopago_plan_id ?>
                                </small>
                            </div>

                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="atualizar_no_mp" value="1" checked>
                                <span class="form-check-label">Atualizar no MP</span>
                            </label>
                            <small class="form-hint">Apenas o nome será atualizado (MP não permite alterar valor)</small>
                            <?php else: ?>
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="criar_no_mp" value="1" checked>
                                <span class="form-check-label">Criar no Mercado Pago</span>
                            </label>
                            <small class="form-hint">Recomendado para ativar cobrança recorrente</small>
                            <?php endif; ?>

                            <hr class="my-3">

                            <div class="mb-3">
                                <label class="form-label">Status do Plano</label>
                                <select class="form-select" name="ativo">
                                    <option value="1" <?= set_select('ativo', '1', ($plano->ativo ?? 1) == 1) ?>>Ativo</option>
                                    <option value="0" <?= set_select('ativo', '0', ($plano->ativo ?? 0) == 0) ?>>Inativo</option>
                                </select>
                                <small class="form-hint">Planos inativos não aparecem para seleção</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url('admin/planos') ?>" class="btn btn-link">
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
