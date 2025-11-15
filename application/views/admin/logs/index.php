<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-history me-2"></i>
                    Logs do Sistema
                </h2>
                <div class="text-muted mt-1">
                    Histórico de ações realizadas no sistema
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('admin/logs/exportar?' . http_build_query($filtros)) ?>" class="btn btn-success">
                        <i class="ti ti-download me-2"></i>
                        Exportar CSV
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-limpar">
                        <i class="ti ti-trash me-2"></i>
                        Limpar Logs Antigos
                    </button>
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
                <form method="get" action="<?= base_url('admin/logs') ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Busca Geral</label>
                            <input type="text" class="form-control" name="busca" 
                                   value="<?= $filtros['busca'] ?? '' ?>" 
                                   placeholder="Usuário, ação, tabela, IP...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Ação</label>
                            <select class="form-select" name="acao">
                                <option value="">Todas</option>
                                <?php foreach ($acoes as $acao): ?>
                                <option value="<?= $acao ?>" <?= ($filtros['acao'] ?? '') == $acao ? 'selected' : '' ?>>
                                    <?= ucfirst($acao) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tabela</label>
                            <select class="form-select" name="tabela">
                                <option value="">Todas</option>
                                <?php foreach ($tabelas as $tabela): ?>
                                <option value="<?= $tabela ?>" <?= ($filtros['tabela'] ?? '') == $tabela ? 'selected' : '' ?>>
                                    <?= ucfirst($tabela) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Usuário</label>
                            <select class="form-select" name="usuario_id">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario->id ?>" <?= ($filtros['usuario_id'] ?? '') == $usuario->id ? 'selected' : '' ?>>
                                    <?= $usuario->nome ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="data_inicio" 
                                       value="<?= $filtros['data_inicio'] ?? '' ?>">
                                <span class="input-group-text">até</span>
                                <input type="date" class="form-control" name="data_fim" 
                                       value="<?= $filtros['data_fim'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-2"></i>
                            Filtrar
                        </button>
                        <a href="<?= base_url('admin/logs') ?>" class="btn btn-link">
                            Limpar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Logs -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Logs Encontrados: <span class="badge bg-blue ms-2"><?= number_format($total_logs, 0, ',', '.') ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Tabela</th>
                            <th>Registro</th>
                            <th>IP</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum log encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <div class="text-nowrap">
                                    <?= date('d/m/Y', strtotime($log->criado_em)) ?>
                                </div>
                                <div class="text-muted small">
                                    <?= date('H:i:s', strtotime($log->criado_em)) ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($log->usuario_nome): ?>
                                <div><?= $log->usuario_nome ?></div>
                                <div class="text-muted small"><?= $log->usuario_email ?></div>
                                <?php else: ?>
                                <span class="text-muted">Sistema</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'bg-blue';
                                switch ($log->acao) {
                                    case 'criar':
                                    case 'insert':
                                        $badge_class = 'bg-green';
                                        break;
                                    case 'editar':
                                    case 'update':
                                        $badge_class = 'bg-yellow';
                                        break;
                                    case 'deletar':
                                    case 'delete':
                                        $badge_class = 'bg-red';
                                        break;
                                    case 'login':
                                        $badge_class = 'bg-cyan';
                                        break;
                                    case 'logout':
                                        $badge_class = 'bg-gray';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($log->acao) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-blue-lt">
                                    <?= ucfirst($log->tabela) ?>
                                </span>
                            </td>
                            <td>
                                <?= $log->registro_id ? '#' . $log->registro_id : '-' ?>
                            </td>
                            <td>
                                <code class="small"><?= $log->ip ?></code>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/logs/visualizar/' . $log->id) ?>" 
                                   class="btn btn-sm btn-icon btn-ghost-primary" 
                                   title="Ver Detalhes">
                                    <i class="ti ti-eye"></i>
                                </a>
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
                    Mostrando <span><?= count($logs) ?></span> de <span><?= number_format($total_logs, 0, ',', '.') ?></span> logs
                </p>
                <div class="ms-auto">
                    <?= $pagination ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Modal Limpar Logs -->
<div class="modal fade" id="modal-limpar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= base_url('admin/logs/limpar') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-trash me-2"></i>
                        Limpar Logs Antigos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remover logs com mais de:</label>
                        <select class="form-select" name="dias" required>
                            <option value="7">7 dias</option>
                            <option value="15">15 dias</option>
                            <option value="30" selected>30 dias</option>
                            <option value="60">60 dias</option>
                            <option value="90">90 dias</option>
                            <option value="180">180 dias</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-2"></i>
                        Limpar Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
