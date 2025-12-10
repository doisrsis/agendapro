<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Planos de Assinatura</h2>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/planos/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Plano
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Valor Mensal</th>
                            <th>Limites</th>
                            <th>Mercado Pago</th>
                            <th>Status</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($planos)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Nenhum plano cadastrado</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($planos as $plano): ?>
                        <tr>
                            <td>
                                <strong><?= $plano->nome ?></strong>
                                <?php if ($plano->descricao): ?>
                                <br><small class="text-muted"><?= $plano->descricao ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong>R$ <?= number_format($plano->valor_mensal, 2, ',', '.') ?></strong>
                            </td>
                            <td>
                                <small>
                                    <i class="ti ti-users"></i> <?= $plano->max_profissionais >= 999 ? 'Ilimitado' : $plano->max_profissionais ?> profissionais<br>
                                    <i class="ti ti-calendar"></i> <?= $plano->max_agendamentos_mes >= 999999 ? 'Ilimitado' : number_format($plano->max_agendamentos_mes) ?> agendamentos/mês
                                </small>
                            </td>
                            <td>
                                <?php if ($plano->mercadopago_plan_id): ?>
                                <span class="badge bg-success">Sincronizado</span>
                                <br><small class="text-muted"><?= substr($plano->mercadopago_plan_id, 0, 20) ?>...</small>
                                <?php else: ?>
                                <span class="badge bg-warning">Não sincronizado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($plano->ativo): ?>
                                <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url('admin/planos/editar/' . $plano->id) ?>" class="btn btn-sm btn-icon" title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <?php if (!$plano->mercadopago_plan_id): ?>
                                    <a href="<?= base_url('admin/planos/sincronizar/' . $plano->id) ?>" class="btn btn-sm btn-icon btn-warning" title="Sincronizar com MP" onclick="return confirm('Sincronizar este plano com o Mercado Pago?')">
                                        <i class="ti ti-refresh"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($plano->ativo): ?>
                                    <a href="<?= base_url('admin/planos/desativar/' . $plano->id) ?>" class="btn btn-sm btn-icon btn-danger" title="Desativar" onclick="return confirm('Desativar este plano?')">
                                        <i class="ti ti-x"></i>
                                    </a>
                                    <?php else: ?>
                                    <a href="<?= base_url('admin/planos/ativar/' . $plano->id) ?>" class="btn btn-sm btn-icon btn-success" title="Ativar" onclick="return confirm('Ativar este plano?')">
                                        <i class="ti ti-check"></i>
                                    </a>
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
    </div>
</div>
