<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gerenciar</div>
                <h2 class="page-title">
                    <i class="ti ti-users me-2"></i>
                    Clientes
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('admin/clientes/exportar?' . http_build_query(['busca' => $busca, 'status' => $status])) ?>" class="btn btn-outline-success">
                        <i class="ti ti-download me-2"></i>
                        Exportar CSV
                    </a>
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
                <div>
                    <?= $this->session->flashdata('sucesso') ?>
                </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                <div>
                    <?= $this->session->flashdata('erro') ?>
                </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <!-- Card de Filtros e Busca -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="<?= base_url('admin/clientes') ?>" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="busca" 
                                   value="<?= $busca ?>" 
                                   placeholder="Nome, e-mail, telefone ou cidade...">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ordenar por</label>
                        <select name="ordem" class="form-select" onchange="this.form.submit()">
                            <option value="recente" <?= $ordem == 'recente' ? 'selected' : '' ?>>Mais recentes</option>
                            <option value="antigo" <?= $ordem == 'antigo' ? 'selected' : '' ?>>Mais antigos</option>
                            <option value="nome" <?= $ordem == 'nome' ? 'selected' : '' ?>>Nome (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <?php if ($busca || $status): ?>
                        <a href="<?= base_url('admin/clientes') ?>" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-2"></i>Limpar Filtros
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total de clientes</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-primary"><?= $total ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Clientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Clientes</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Contato</th>
                            <th>Localização</th>
                            <th>Cadastrado em</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="ti ti-users icon mb-2" style="font-size: 3rem;"></i>
                                <p class="mb-0">Nenhum cliente encontrado.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td>
                                <span class="text-muted">#<?= $cliente->id ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2" style="background-color: <?= sprintf('#%06X', mt_rand(0, 0xFFFFFF)) ?>;">
                                        <?= strtoupper(substr($cliente->nome, 0, 2)) ?>
                                    </div>
                                    <div>
                                        <strong><?= $cliente->nome ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div><i class="ti ti-mail me-1"></i><?= $cliente->email ?></div>
                                    <div><i class="ti ti-phone me-1"></i><?= $cliente->telefone ?></div>
                                    <?php if ($cliente->whatsapp): ?>
                                    <div><i class="ti ti-brand-whatsapp me-1"></i><?= $cliente->whatsapp ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($cliente->cidade && $cliente->estado): ?>
                                <span class="badge bg-azure-lt">
                                    <i class="ti ti-map-pin me-1"></i>
                                    <?= $cliente->cidade ?>/<?= $cliente->estado ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-muted" title="<?= date('d/m/Y H:i', strtotime($cliente->criado_em)) ?>">
                                    <?= date('d/m/Y', strtotime($cliente->criado_em)) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url('admin/clientes/visualizar/' . $cliente->id) ?>" 
                                       class="btn btn-sm btn-icon btn-primary" 
                                       title="Visualizar">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="<?= base_url('admin/clientes/editar/' . $cliente->id) ?>" 
                                       class="btn btn-sm btn-icon btn-warning" 
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pagination): ?>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">
                    Mostrando <?= count($clientes) ?> de <?= $total ?> clientes
                </p>
                <div class="ms-auto">
                    <?= $pagination ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
