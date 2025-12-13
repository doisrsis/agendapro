<!-- Filtros -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-filter me-2"></i>
            Filtros
        </h3>
    </div>
    <div class="card-body">
        <form method="get" action="<?= base_url('agenda/dashboard') ?>">
            <input type="hidden" name="view" value="lista">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="pendente" <?= ($filtros['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="confirmado" <?= ($filtros['status'] ?? '') == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                        <option value="cancelado" <?= ($filtros['status'] ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        <option value="concluido" <?= ($filtros['status'] ?? '') == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data</label>
                    <input type="date" class="form-control" name="data"
                           value="<?= $filtros['data'] ?? '' ?>">
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

<!-- Lista de Agendamentos -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Agendamentos: <span class="badge bg-blue ms-2"><?= count($agendamentos ?? []) ?></span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th class="w-1">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($agendamentos)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="ti ti-calendar-off fs-1 mb-3 d-block"></i>
                        Nenhum agendamento encontrado
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($agendamentos as $ag): ?>
                <tr>
                    <td>
                        <div class="text-nowrap">
                            <i class="ti ti-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($ag->data)) ?>
                        </div>
                        <div class="text-muted small">
                            <i class="ti ti-clock me-1"></i>
                            <?= date('H:i', strtotime($ag->hora_inicio)) ?>
                        </div>
                    </td>
                    <td>
                        <div><?= $ag->cliente_nome ?></div>
                        <?php if (!empty($ag->cliente_whatsapp)): ?>
                        <div class="text-muted small"><?= $ag->cliente_whatsapp ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= $ag->servico_nome ?></td>
                    <td>R$ <?= number_format($ag->servico_preco, 2, ',', '.') ?></td>
                    <td>
                        <?php
                        $badge_class = 'bg-secondary';
                        switch ($ag->status) {
                            case 'pendente':
                                $badge_class = 'bg-warning';
                                break;
                            case 'confirmado':
                                $badge_class = 'bg-success';
                                break;
                            case 'cancelado':
                                $badge_class = 'bg-danger';
                                break;
                            case 'concluido':
                                $badge_class = 'bg-info';
                                break;
                        }
                        ?>
                        <span class="badge <?= $badge_class ?>">
                            <?= ucfirst($ag->status) ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= base_url('agenda/agendamentos/editar/' . $ag->id) ?>"
                               class="btn btn-sm btn-icon btn-ghost-primary"
                               title="Editar">
                                <i class="ti ti-edit"></i>
                            </a>

                            <?php if ($ag->status != 'cancelado' && $ag->status != 'concluido'): ?>
                            <button type="button"
                                    class="btn btn-sm btn-icon btn-ghost-danger"
                                    title="Cancelar"
                                    onclick="cancelarAgendamento(<?= $ag->id ?>)">
                                <i class="ti ti-x"></i>
                            </button>
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

<script>
function cancelarAgendamento(id) {
    Swal.fire({
        title: 'Cancelar Agendamento',
        text: 'Tem certeza que deseja cancelar este agendamento?',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Motivo do cancelamento',
        inputPlaceholder: 'Digite o motivo...',
        showCancelButton: true,
        confirmButtonText: 'Sim, cancelar',
        cancelButtonText: 'Não',
        confirmButtonColor: '#d63939',
        showLoaderOnConfirm: true,
        preConfirm: (motivo) => {
            return fetch('<?= base_url('agenda/agendamentos/cancelar/') ?>' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'motivo=' + encodeURIComponent(motivo)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao cancelar');
                }
                return response;
            })
            .catch(error => {
                Swal.showValidationMessage('Erro: ' + error);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Cancelado!',
                text: 'Agendamento cancelado com sucesso.',
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        }
    });
}
</script>
