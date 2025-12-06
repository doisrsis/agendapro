<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-user-heart me-2"></i>
                    Clientes
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de clientes cadastrados
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/clientes/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Cliente
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
                <form method="get" action="<?= base_url('admin/clientes') ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
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
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo">
                                <option value="">Todos</option>
                                <option value="novo" <?= ($filtros['tipo'] ?? '') == 'novo' ? 'selected' : '' ?>>Novo</option>
                                <option value="recorrente" <?= ($filtros['tipo'] ?? '') == 'recorrente' ? 'selected' : '' ?>>Recorrente</option>
                                <option value="vip" <?= ($filtros['tipo'] ?? '') == 'vip' ? 'selected' : '' ?>>VIP</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Busca</label>
                            <input type="text" class="form-control" name="busca"
                                   value="<?= $filtros['busca'] ?? '' ?>"
                                   placeholder="Nome, CPF, WhatsApp...">
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

        <!-- Lista de Clientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Clientes Cadastrados: <span class="badge bg-blue ms-2"><?= count($clientes) ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Estabelecimento</th>
                            <th>WhatsApp</th>
                            <th>CPF</th>
                            <th>Agendamentos</th>
                            <th>Tipo</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum cliente encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($clientes as $cli): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($cli->foto): ?>
                                    <img src="<?= base_url('uploads/clientes/' . $cli->foto) ?>"
                                         alt="<?= $cli->nome ?>"
                                         class="avatar avatar-sm me-2">
                                    <?php else: ?>
                                    <span class="avatar avatar-sm me-2">
                                        <?= strtoupper(substr($cli->nome, 0, 2)) ?>
                                    </span>
                                    <?php endif; ?>
                                    <div>
                                        <div><?= $cli->nome ?></div>
                                        <?php if ($cli->email): ?>
                                        <div class="text-muted small"><?= $cli->email ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= $cli->estabelecimento_nome ?></td>
                            <td><?= $cli->whatsapp ?></td>
                            <td><?= $cli->cpf ?: '-' ?></td>
                            <td>
                                <span class="badge bg-blue-lt">
                                    <?= $cli->total_agendamentos ?> agendamento<?= $cli->total_agendamentos != 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'bg-secondary';
                                switch ($cli->tipo) {
                                    case 'novo':
                                        $badge_class = 'bg-info';
                                        break;
                                    case 'recorrente':
                                        $badge_class = 'bg-success';
                                        break;
                                    case 'vip':
                                        $badge_class = 'bg-yellow';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($cli->tipo) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url('admin/clientes/visualizar/' . $cli->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-info"
                                       title="Visualizar">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="<?= base_url('admin/clientes/editar/' . $cli->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/clientes/deletar/' . $cli->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       title="Deletar"
                                       onclick="return confirm('Tem certeza que deseja deletar este cliente?')">
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
        </div>

    </div>
</div>
