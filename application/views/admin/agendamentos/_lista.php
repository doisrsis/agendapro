<!-- Filtros -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-filter me-2"></i>
            Filtros
        </h3>
    </div>
    <div class="card-body">
        <form method="get" action="<?= base_url('painel/agendamentos') ?>">
            <input type="hidden" name="view" value="lista">
            <div class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="pendente" <?= ($filtros['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="confirmado" <?= ($filtros['status'] ?? '') == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                        <option value="em_atendimento" <?= ($filtros['status'] ?? '') == 'em_atendimento' ? 'selected' : '' ?>>Em Atendimento</option>
                        <option value="finalizado" <?= ($filtros['status'] ?? '') == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        <option value="cancelado" <?= ($filtros['status'] ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
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
            Agendamentos: <span class="badge bg-blue ms-2"><?= number_format($total, 0, ',', '.') ?></span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Profissional</th>
                    <th>Serviço</th>
                    <th>Pagamento</th>
                    <th>Status</th>
                    <th class="w-1">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($agendamentos)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
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
                    <td><?= $ag->profissional_nome ?></td>
                    <td><?= $ag->servico_nome ?></td>
                    <td>
                        <?php if (!empty($ag->forma_pagamento) && $ag->forma_pagamento != 'nao_definido'): ?>
                            <?php if ($ag->forma_pagamento == 'pix'): ?>
                                <?php if ($ag->pagamento_status == 'pago'): ?>
                                    <span class="badge bg-success">
                                        <i class="ti ti-check me-1"></i>PIX Pago
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="ti ti-clock me-1"></i>PIX Pendente
                                    </span>
                                <?php endif; ?>
                            <?php elseif ($ag->forma_pagamento == 'presencial'): ?>
                                <span class="badge bg-info">
                                    <i class="ti ti-building-store me-1"></i>Presencial
                                </span>
                            <?php elseif ($ag->forma_pagamento == 'cartao'): ?>
                                <?php if ($ag->pagamento_status == 'pago'): ?>
                                    <span class="badge bg-success">
                                        <i class="ti ti-credit-card me-1"></i>Cartão Pago
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary">
                                        <i class="ti ti-credit-card me-1"></i>Cartão Pendente
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="ti ti-minus me-1"></i>Não Definido
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $badge_class = 'bg-secondary';
                        $status_texto = $ag->status;
                        switch ($ag->status) {
                            case 'pendente':
                                $badge_class = 'bg-warning';
                                $status_texto = 'Pendente';
                                break;
                            case 'confirmado':
                                $badge_class = 'bg-success';
                                $status_texto = 'Confirmado';
                                break;
                            case 'em_atendimento':
                                $badge_class = 'bg-primary';
                                $status_texto = 'Em Atendimento';
                                break;
                            case 'cancelado':
                                $badge_class = 'bg-danger';
                                $status_texto = 'Cancelado';
                                break;
                            case 'concluido':
                            case 'finalizado':
                                $badge_class = 'bg-info';
                                $status_texto = 'Finalizado';
                                break;
                        }
                        ?>
                        <span class="badge <?= $badge_class ?>">
                            <?= $status_texto ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <?php if (in_array($ag->status, ['confirmado', 'pendente'])): ?>
                            <a href="<?= base_url('painel/agendamentos/iniciar/' . $ag->id) ?>"
                               class="btn btn-success btn-icon" title="Iniciar Atendimento">
                                <i class="ti ti-player-play"></i>
                            </a>
                            <?php elseif ($ag->status == 'em_atendimento'): ?>
                            <a href="<?= base_url('painel/agendamentos/finalizar/' . $ag->id) ?>"
                               class="btn btn-warning btn-icon" title="Finalizar Atendimento">
                                <i class="ti ti-player-stop"></i>
                            </a>
                            <?php endif; ?>

                            <a href="<?= base_url('painel/agendamentos/visualizar/' . $ag->id) ?>"
                               class="btn btn-outline-secondary btn-icon" title="Visualizar">
                                <i class="ti ti-eye"></i>
                            </a>

                            <a href="<?= base_url('painel/agendamentos/editar/' . $ag->id) ?>"
                               class="btn btn-outline-primary btn-icon" title="Editar">
                                <i class="ti ti-pencil"></i>
                            </a>

                            <?php if (!in_array($ag->status, ['cancelado', 'concluido', 'finalizado'])): ?>
                            <button type="button"
                                    class="btn btn-outline-danger btn-icon"
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
    <?php if (isset($pagination)): ?>
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted">Exibindo <span><?= count($agendamentos) ?></span> de <span><?= $total ?></span> registros</p>
        <?= $pagination ?>
    </div>
    <?php endif; ?>
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
            return fetch('<?= base_url('painel/agendamentos/cancelar/') ?>' + id, {
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
