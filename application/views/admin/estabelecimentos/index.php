<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-building me-2"></i>
                    Estabelecimentos
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de estabelecimentos cadastrados
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/estabelecimentos/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Estabelecimento
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
                <form method="get" action="<?= base_url('admin/estabelecimentos') ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Busca</label>
                            <input type="text" class="form-control" name="busca"
                                   value="<?= $filtros['busca'] ?? '' ?>"
                                   placeholder="Nome, CNPJ/CPF, e-mail...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="ativo" <?= ($filtros['status'] ?? '') == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= ($filtros['status'] ?? '') == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                <option value="suspenso" <?= ($filtros['status'] ?? '') == 'suspenso' ? 'selected' : '' ?>>Suspenso</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plano</label>
                            <select class="form-select" name="plano">
                                <option value="">Todos</option>
                                <option value="trimestral" <?= ($filtros['plano'] ?? '') == 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                                <option value="semestral" <?= ($filtros['plano'] ?? '') == 'semestral' ? 'selected' : '' ?>>Semestral</option>
                                <option value="anual" <?= ($filtros['plano'] ?? '') == 'anual' ? 'selected' : '' ?>>Anual</option>
                            </select>
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

        <!-- Lista de Estabelecimentos -->
    <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Estabelecimentos Cadastrados: <span class="badge bg-blue ms-2"><?= $total ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nome</th>
                            <th>CNPJ/CPF</th>
                            <th>WhatsApp</th>
                            <th>Plano</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estabelecimentos)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum estabelecimento encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($estabelecimentos as $est): ?>
                        <tr>
                            <td>
                                <?php if ($est->logo): ?>
                                <img src="<?= base_url('uploads/logos/' . $est->logo) ?>"
                                     alt="<?= $est->nome ?>"
                                     class="avatar avatar-sm">
                                <?php else: ?>
                                <span class="avatar avatar-sm">
                                    <?= strtoupper(substr($est->nome, 0, 2)) ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= $est->nome ?></div>
                                <?php if ($est->email): ?>
                                <div class="text-muted small"><?= $est->email ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= $est->cnpj_cpf ?: '-' ?></td>
                            <td><?= $est->whatsapp ?: '-' ?></td>
                            <td>
                                <span class="badge bg-blue-lt">
                                    <?= ucfirst($est->plano) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($est->plano_vencimento): ?>
                                    <?= date('d/m/Y', strtotime($est->plano_vencimento)) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'bg-success';
                                if ($est->status == 'inativo') $badge_class = 'bg-secondary';
                                if ($est->status == 'suspenso') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($est->status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url('admin/estabelecimentos/editar/' . $est->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/estabelecimentos/deletar/' . $est->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       title="Deletar"
                                       onclick="return confirm('Tem certeza que deseja deletar este estabelecimento?')">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (isset($pagination) || isset($total)): ?>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Exibindo <span><?= count($estabelecimentos) ?></span> de <span><?= $total ?></span> registros</p>
                <?= $pagination ?? '' ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
