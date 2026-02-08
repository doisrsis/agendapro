<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-users me-2"></i>
                    Profissionais
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de profissionais dos estabelecimentos
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url(($base_controller ?? 'admin') . '/profissionais/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Profissional
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
                <form method="get" action="<?= base_url(($base_controller ?? 'admin') . '/profissionais') ?>">
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
                            <label class="form-label">Busca</label>
                            <input type="text" class="form-control" name="busca"
                                   value="<?= $filtros['busca'] ?? '' ?>"
                                   placeholder="Nome, e-mail, WhatsApp...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="ativo" <?= ($filtros['status'] ?? '') == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= ($filtros['status'] ?? '') == 'inativo' ? 'selected' : '' ?>>Inativo</option>
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

        <!-- Lista de Profissionais -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Profissionais Cadastrados: <span class="badge bg-blue ms-2"><?= $total ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Estabelecimento</th>
                            <th>WhatsApp</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($profissionais)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum profissional encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($profissionais as $prof): ?>
                        <tr>
                            <td>
                                <?php if ($prof->foto): ?>
                                <img src="<?= base_url('uploads/profissionais/' . $prof->foto) ?>"
                                     alt="<?= $prof->nome ?>"
                                     class="avatar avatar-sm">
                                <?php else: ?>
                                <span class="avatar avatar-sm">
                                    <?= strtoupper(substr($prof->nome, 0, 2)) ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= $prof->nome ?></div>
                            </td>
                            <td><?= $prof->estabelecimento_nome ?></td>
                            <td><?= $prof->whatsapp ?: '-' ?></td>
                            <td><?= $prof->email ?: '-' ?></td>
                            <td>
                                <span class="badge <?= $prof->status == 'ativo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($prof->status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url(($base_controller ?? 'admin') . '/profissionais/editar/' . $prof->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url(($base_controller ?? 'admin') . '/profissionais/deletar/' . $prof->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       title="Deletar"
                                       onclick="return confirm('Tem certeza que deseja deletar este profissional?')">
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
                <p class="m-0 text-muted">Exibindo <span><?= count($profissionais) ?></span> de <span><?= $total ?></span> registros</p>
                <?= $pagination ?? '' ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
