<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-calendar-off me-2"></i>
                    Meus Bloqueios
                </h2>
                <div class="text-muted mt-1">Gerencie folgas, férias e compromissos</div>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('agenda/bloqueios/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Bloqueio
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <?php if ($this->session->flashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible">
            <i class="ti ti-check me-2"></i>
            <?= $this->session->flashdata('sucesso') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="ti ti-alert-circle me-2"></i>
            <?= $this->session->flashdata('erro') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bloqueios Cadastrados</h3>
            </div>

            <?php if (empty($bloqueios)): ?>
            <div class="card-body">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-calendar-off"></i>
                    </div>
                    <p class="empty-title">Nenhum bloqueio cadastrado</p>
                    <p class="empty-subtitle text-muted">
                        Crie bloqueios para dias de folga, férias ou compromissos pessoais
                    </p>
                    <div class="empty-action">
                        <a href="<?= base_url('agenda/bloqueios/criar') ?>" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>
                            Criar Primeiro Bloqueio
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Período</th>
                            <th>Horário</th>
                            <th>Motivo</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bloqueios as $bloqueio): ?>
                        <tr>
                            <td>
                                <?php if ($bloqueio->tipo == 'dia'): ?>
                                    <span class="badge bg-blue">Dia</span>
                                <?php elseif ($bloqueio->tipo == 'periodo'): ?>
                                    <span class="badge bg-purple">Período</span>
                                <?php else: ?>
                                    <span class="badge bg-orange">Horário</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($bloqueio->tipo == 'dia'): ?>
                                    <?= date('d/m/Y', strtotime($bloqueio->data_inicio)) ?>
                                <?php else: ?>
                                    <?= date('d/m/Y', strtotime($bloqueio->data_inicio)) ?>
                                    <?php if ($bloqueio->data_fim): ?>
                                        até <?= date('d/m/Y', strtotime($bloqueio->data_fim)) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($bloqueio->tipo == 'horario' && $bloqueio->hora_inicio): ?>
                                    <?= substr($bloqueio->hora_inicio, 0, 5) ?> - <?= substr($bloqueio->hora_fim, 0, 5) ?>
                                <?php else: ?>
                                    <span class="text-muted">Dia inteiro</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $bloqueio->motivo ?: '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?= base_url('agenda/bloqueios/editar/' . $bloqueio->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-primary">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="<?= base_url('agenda/bloqueios/excluir/' . $bloqueio->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       onclick="return confirm('Deseja realmente excluir este bloqueio?')">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
