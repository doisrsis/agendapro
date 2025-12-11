<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-scissors me-2"></i>
                    Serviços
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de serviços oferecidos
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url(($base_controller ?? 'admin') . '/servicos/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Serviço
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
                <form method="get" action="<?= base_url(($base_controller ?? 'admin') . '/servicos') ?>">
                    <div class="row g-3">
                        <div class="col-md-5">
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
                        <div class="col-md-4">
                            <label class="form-label">Busca</label>
                            <input type="text" class="form-control" name="busca"
                                   value="<?= $filtros['busca'] ?? '' ?>"
                                   placeholder="Nome do serviço...">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-2"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Serviços -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Serviços Cadastrados: <span class="badge bg-blue ms-2"><?= count($servicos) ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Estabelecimento</th>
                            <th>Duração</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($servicos)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum serviço encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($servicos as $serv): ?>
                        <tr>
                            <td>
                                <div><?= $serv->nome ?></div>
                                <?php if ($serv->descricao): ?>
                                <div class="text-muted small"><?= substr($serv->descricao, 0, 50) ?><?= strlen($serv->descricao) > 50 ? '...' : '' ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= $serv->estabelecimento_nome ?></td>
                            <td>
                                <span class="badge bg-azure-lt">
                                    <i class="ti ti-clock me-1"></i>
                                    <?= $serv->duracao ?> min
                                </span>
                            </td>
                            <td>
                                <strong>R$ <?= number_format($serv->preco, 2, ',', '.') ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $serv->status == 'ativo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($serv->status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url(($base_controller ?? 'admin') . '/servicos/editar/' . $serv->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url(($base_controller ?? 'admin') . '/servicos/deletar/' . $serv->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       title="Deletar"
                                       onclick="return confirm('Tem certeza que deseja deletar este serviço?')">
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
