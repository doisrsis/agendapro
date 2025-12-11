<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-calendar me-2"></i>
                    Agendamentos
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de agendamentos do sistema
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url(($base_controller ?? 'admin') . '/agendamentos/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Agendamento
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- Mensagens -->
        <?php if ($this->session->flashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('sucesso') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('erro') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-filter me-2"></i>
                    Filtros
                </h3>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url(($base_controller ?? 'admin') . '/agendamentos') ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Estabelecimento</label>
                            <select class="form-select" name="estabelecimento_id">
                                <option value="">Todos</option>
                                <?php foreach ($estabelecimentos as $est): ?>
                                <option value="<?= $est->id ?>" <?= ($filtros['estabelecimento_id'] ?? '') == $est->id ? 'selected' : '' ?>>
                                    <?= $est->nome ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Profissional</label>
                            <select class="form-select" name="profissional_id">
                                <option value="">Todos</option>
                                <?php foreach ($profissionais as $prof): ?>
                                <option value="<?= $prof->id ?>" <?= ($filtros['profissional_id'] ?? '') == $prof->id ? 'selected' : '' ?>>
                                    <?= $prof->nome ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="pendente" <?= ($filtros['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="confirmado" <?= ($filtros['status'] ?? '') == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                <option value="cancelado" <?= ($filtros['status'] ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                <option value="finalizado" <?= ($filtros['status'] ?? '') == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" name="data"
                                   value="<?= $filtros['data'] ?? '' ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-2"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Agendamentos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Agendamentos: <span class="badge bg-blue ms-2"><?= number_format($total, 0, ',', '.') ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Cliente</th>
                            <th>Profissional</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agendamentos)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum agendamento encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($agendamentos as $ag): ?>
                        <tr>
                            <td>
                                <div class="text-nowrap">
                                    <i class="ti ti-calendar me-1"></i>
                                    <?= date('d/m/Y', strtotime($ag->data)) ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="ti ti-clock me-1"></i>
                                    <?= date('H:i', strtotime($ag->hora_inicio)) ?>
                                </div>
                            </td>
                            <td>
                                <div><?= $ag->cliente_nome ?></div>
                                <div class="text-muted small"><?= $ag->cliente_whatsapp ?></div>
                            </td>
                            <td><?= $ag->profissional_nome ?></td>
                            <td><?= $ag->servico_nome ?></td>
                            <td>R$ <?= number_format($ag->servico_preco, 2, ',', '.') ?></td>
                            <td>
                                <?php
                                $badge_class = 'bg-secondary';
                                switch ($ag->status) {
                                    case 'pendente':
                                        $badge_class = 'bg-warning';
                                        break;
                                    case 'confirmado':
                                        $badge_class = 'bg-info';
                                        break;
                                    case 'cancelado':
                                        $badge_class = 'bg-danger';
                                        break;
                                    case 'finalizado':
                                        $badge_class = 'bg-success';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($ag->status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if ($ag->status == 'confirmado'): ?>
                                    <a href="<?= base_url(($base_controller ?? 'admin') . '/agendamentos/finalizar/' . $ag->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-success"
                                       title="Finalizar"
                                       onclick="return confirm('Confirmar finalização do atendimento?')">
                                        <i class="ti ti-check"></i>
                                    </a>
                                    <?php endif; ?>

                                    <a href="<?= base_url(($base_controller ?? 'admin') . '/agendamentos/editar/' . $ag->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>

                                    <?php if ($ag->status != 'cancelado' && $ag->status != 'finalizado'): ?>
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-ghost-danger"
                                            title="Cancelar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-cancelar-<?= $ag->id ?>">
                                        <i class="ti ti-x"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Modal Cancelar -->
                                <div class="modal fade" id="modal-cancelar-<?= $ag->id ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="post" action="<?= base_url(($base_controller ?? 'admin') . '/agendamentos/cancelar/' . $ag->id) ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cancelar Agendamento</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="ti ti-alert-triangle me-2"></i>
                                                        Tem certeza que deseja cancelar este agendamento?
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Motivo do Cancelamento</label>
                                                        <textarea class="form-control" name="motivo" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn" data-bs-dismiss="modal">Fechar</button>
                                                    <button type="submit" class="btn btn-danger">Cancelar Agendamento</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($pagination)): ?>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">
                    Mostrando <span><?= count($agendamentos) ?></span> de <span><?= number_format($total, 0, ',', '.') ?></span> agendamentos
                </p>
                <div class="ms-auto">
                    <?= $pagination ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
