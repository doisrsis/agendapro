<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-credit-card me-2"></i>
                    Pagamentos
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de pagamentos via Mercado Pago
                </div>
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
                <form method="get" action="<?= base_url('admin/pagamentos') ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="pending" <?= ($filtros['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pendente</option>
                                <option value="approved" <?= ($filtros['status'] ?? '') == 'approved' ? 'selected' : '' ?>>Aprovado</option>
                                <option value="in_process" <?= ($filtros['status'] ?? '') == 'in_process' ? 'selected' : '' ?>>Em Processamento</option>
                                <option value="rejected" <?= ($filtros['status'] ?? '') == 'rejected' ? 'selected' : '' ?>>Rejeitado</option>
                                <option value="cancelled" <?= ($filtros['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                <option value="refunded" <?= ($filtros['status'] ?? '') == 'refunded' ? 'selected' : '' ?>>Reembolsado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Método</label>
                            <select class="form-select" name="metodo">
                                <option value="">Todos</option>
                                <option value="pix" <?= ($filtros['metodo'] ?? '') == 'pix' ? 'selected' : '' ?>>PIX</option>
                                <option value="credit_card" <?= ($filtros['metodo'] ?? '') == 'credit_card' ? 'selected' : '' ?>>Cartão de Crédito</option>
                                <option value="debit_card" <?= ($filtros['metodo'] ?? '') == 'debit_card' ? 'selected' : '' ?>>Cartão de Débito</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-2"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Pagamentos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Pagamentos: <span class="badge bg-blue ms-2"><?= count($pagamentos) ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Agendamento</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pagamentos)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum pagamento encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($pagamentos as $pag): ?>
                        <tr>
                            <td><code>#<?= $pag->mercadopago_id ?></code></td>
                            <td><?= $pag->cliente_nome ?></td>
                            <td>
                                <div><?= $pag->servico_nome ?></div>
                                <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($pag->data . ' ' . $pag->hora_inicio)) ?></div>
                            </td>
                            <td><strong>R$ <?= number_format($pag->valor, 2, ',', '.') ?></strong></td>
                            <td>
                                <?php
                                $metodo_icon = 'ti-credit-card';
                                $metodo_nome = ucfirst($pag->metodo_pagamento);
                                if ($pag->metodo_pagamento == 'pix') {
                                    $metodo_icon = 'ti-qrcode';
                                    $metodo_nome = 'PIX';
                                }
                                ?>
                                <i class="ti <?= $metodo_icon ?> me-1"></i>
                                <?= $metodo_nome ?>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'bg-secondary';
                                $status_texto = ucfirst($pag->status);
                                switch ($pag->status) {
                                    case 'pending':
                                        $badge_class = 'bg-warning';
                                        $status_texto = 'Pendente';
                                        break;
                                    case 'approved':
                                        $badge_class = 'bg-success';
                                        $status_texto = 'Aprovado';
                                        break;
                                    case 'in_process':
                                        $badge_class = 'bg-info';
                                        $status_texto = 'Processando';
                                        break;
                                    case 'rejected':
                                        $badge_class = 'bg-danger';
                                        $status_texto = 'Rejeitado';
                                        break;
                                    case 'cancelled':
                                        $badge_class = 'bg-dark';
                                        $status_texto = 'Cancelado';
                                        break;
                                    case 'refunded':
                                        $badge_class = 'bg-purple';
                                        $status_texto = 'Reembolsado';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= $status_texto ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($pag->criado_em)) ?></td>
                            <td>
                                <?php if ($pag->status == 'approved'): ?>
                                <a href="<?= base_url('admin/pagamentos/reembolsar/' . $pag->id) ?>"
                                   class="btn btn-sm btn-icon btn-ghost-warning"
                                   title="Reembolsar"
                                   onclick="return confirm('Tem certeza que deseja reembolsar este pagamento?')">
                                    <i class="ti ti-arrow-back-up"></i>
                                </a>
                                <?php endif; ?>
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
