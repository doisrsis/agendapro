<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gerenciar</div>
                <h2 class="page-title">
                    <i class="ti ti-user-shield me-2"></i>
                    Usuários do Sistema
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('admin/usuarios/criar') ?>" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Novo Usuário
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
            <div class="card-body">
                <form method="get" action="<?= base_url('admin/usuarios') ?>" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="busca"
                                   value="<?= $busca ?>"
                                   placeholder="Nome ou e-mail...">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nível</label>
                        <select name="nivel" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="admin" <?= $nivel == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="usuario" <?= $nivel == 'usuario' ? 'selected' : '' ?>>Usuário</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="ativo" <?= $status == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= $status == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <?php if ($busca || $nivel || $status): ?>
                        <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-x me-2"></i>Limpar
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
                            <div class="subheader">Total de usuários</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-primary"><?= $total ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Usuários</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Nível</th>
                            <th>Status</th>
                            <th>Último Acesso</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="ti ti-user-shield icon mb-2" style="font-size: 3rem;"></i>
                                <p class="mb-0">Nenhum usuário encontrado.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 <?= $usuario->status == 'ativo' ? 'bg-success' : 'bg-secondary' ?>-lt">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <strong><?= $usuario->nome ?></strong>
                                        <div class="small text-muted"><?= $usuario->email ?></div>
                                        <?php if ($usuario->telefone): ?>
                                        <div class="small text-muted"><i class="ti ti-phone"></i> <?= $usuario->telefone ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($usuario->nivel == 'admin'): ?>
                                <span class="badge bg-red">
                                    <i class="ti ti-shield-check me-1"></i>Admin
                                </span>
                                <?php else: ?>
                                <span class="badge bg-blue">
                                    <i class="ti ti-user me-1"></i>Usuário
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario->status == 'ativo'): ?>
                                <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario->ultimo_acesso): ?>
                                <span class="text-muted" title="<?= date('d/m/Y H:i', strtotime($usuario->ultimo_acesso)) ?>">
                                    <?= date('d/m/Y', strtotime($usuario->ultimo_acesso)) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">Nunca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url('admin/usuarios/editar/' . $usuario->id) ?>"
                                       class="btn btn-sm btn-icon btn-warning"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/usuarios/alterar_senha/' . $usuario->id) ?>"
                                       class="btn btn-sm btn-icon btn-info"
                                       title="Alterar Senha">
                                        <i class="ti ti-key"></i>
                                    </a>
                                    <?php if ($usuario->id != $this->session->userdata('usuario_id')): ?>
                                    <a href="<?= base_url('admin/usuarios/alternar_status/' . $usuario->id) ?>"
                                       class="btn btn-sm btn-icon btn-<?= $usuario->status == 'ativo' ? 'secondary' : 'success' ?>"
                                       title="<?= $usuario->status == 'ativo' ? 'Desativar' : 'Ativar' ?>"
                                       onclick="return confirm('Tem certeza que deseja <?= $usuario->status == 'ativo' ? 'desativar' : 'ativar' ?> este usuário?')">
                                        <i class="ti ti-<?= $usuario->status == 'ativo' ? 'ban' : 'check' ?>"></i>
                                    </a>
                                    <a href="<?= base_url('admin/usuarios/excluir/' . $usuario->id) ?>"
                                       class="btn btn-sm btn-icon btn-danger"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')">
                                        <i class="ti ti-trash"></i>
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
