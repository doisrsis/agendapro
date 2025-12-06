<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-ban me-2"></i>
                    Bloqueios de Horários
                </h2>
                <div class="text-muted mt-1">
                    Gerenciamento de férias, folgas e bloqueios
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/bloqueios/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Bloqueio
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
                <form method="get" action="<?= base_url('admin/bloqueios') ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo">
                                <option value="">Todos</option>
                                <option value="ferias" <?= ($filtros['tipo'] ?? '') == 'ferias' ? 'selected' : '' ?>>Férias</option>
                                <option value="folga" <?= ($filtros['tipo'] ?? '') == 'folga' ? 'selected' : '' ?>>Folga</option>
                                <option value="almoco" <?= ($filtros['tipo'] ?? '') == 'almoco' ? 'selected' : '' ?>>Almoço</option>
                                <option value="outro" <?= ($filtros['tipo'] ?? '') == 'outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <input type="date" class="form-control" name="data_inicio"
                                   value="<?= $filtros['data_inicio'] ?? '' ?>">
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

        <!-- Lista de Bloqueios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Bloqueios Cadastrados: <span class="badge bg-blue ms-2"><?= count($bloqueios) ?></span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Profissional</th>
                            <th>Período</th>
                            <th>Horário</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bloqueios)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                Nenhum bloqueio encontrado
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($bloqueios as $bloq): ?>
                        <tr>
                            <td><?= $bloq->profissional_nome ?></td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($bloq->data_inicio)) ?></div>
                                <?php if ($bloq->data_inicio != $bloq->data_fim): ?>
                                <div class="text-muted small">até <?= date('d/m/Y', strtotime($bloq->data_fim)) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($bloq->hora_inicio && $bloq->hora_fim): ?>
                                <?= date('H:i', strtotime($bloq->hora_inicio)) ?> - <?= date('H:i', strtotime($bloq->hora_fim)) ?>
                                <?php else: ?>
                                <span class="text-muted">Dia inteiro</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'bg-secondary';
                                switch ($bloq->tipo) {
                                    case 'ferias':
                                        $badge_class = 'bg-blue';
                                        break;
                                    case 'folga':
                                        $badge_class = 'bg-green';
                                        break;
                                    case 'almoco':
                                        $badge_class = 'bg-orange';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($bloq->tipo) ?>
                                </span>
                            </td>
                            <td><?= $bloq->motivo ?: '-' ?></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url('admin/bloqueios/editar/' . $bloq->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary"
                                       title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/bloqueios/deletar/' . $bloq->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       title="Deletar"
                                       onclick="return confirm('Tem certeza que deseja deletar este bloqueio?')">
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
