<!-- OPÇÃO 1: LAYOUT TABELA TRADICIONAL -->
<!-- View Simplificada para Atendimento Rápido -->

<!-- Filtros Simplificados -->
<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="<?= base_url('painel/agendamentos') ?>" id="form-filtro-rapido">
            <input type="hidden" name="view" value="rapida">
            <div class="row g-3 align-items-end">
                <!-- Busca por Nome/Telefone -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ti ti-search me-1"></i>
                        Buscar Cliente
                    </label>
                    <input type="text"
                           class="form-control"
                           name="busca"
                           id="busca-cliente"
                           placeholder="Digite nome ou telefone..."
                           value="<?= $filtros['busca'] ?? '' ?>">
                </div>

                <!-- Filtro de Status -->
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="filtro-status">
                        <option value="">Todos</option>
                        <option value="confirmado" <?= ($filtros['status'] ?? 'confirmado') == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                        <option value="pendente" <?= ($filtros['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="finalizado" <?= ($filtros['status'] ?? '') == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        <option value="cancelado" <?= ($filtros['status'] ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        <option value="nao_compareceu" <?= ($filtros['status'] ?? '') == 'nao_compareceu' ? 'selected' : '' ?>>Não Compareceu</option>
                    </select>
                </div>

                <!-- Filtro de Data -->
                <div class="col-md-3">
                    <label class="form-label">Data</label>
                    <input type="date"
                           class="form-control"
                           name="data"
                           value="<?= $filtros['data'] ?? date('Y-m-d') ?>">
                </div>

                <!-- Botão Filtrar -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-2"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Agendamentos - TABELA -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-calendar-check me-2"></i>
            Atendimentos de Hoje
            <span class="badge bg-blue ms-2"><?= number_format($total, 0, ',', '.') ?></span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Status</th>
                    <th>Pagamento</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($agendamentos)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="ti ti-calendar-off fs-1 mb-3 d-block"></i>
                        Nenhum agendamento encontrado
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($agendamentos as $ag): ?>
                <tr>
                    <!-- Cliente -->
                    <td>
                        <div class="d-flex flex-column">
                            <div class="fw-bold"><?= $ag->cliente_nome ?></div>
                            <?php if (!empty($ag->cliente_whatsapp)): ?>
                            <div class="text-muted small">
                                <i class="ti ti-phone me-1"></i>
                                <?= $ag->cliente_whatsapp ?>
                            </div>
                            <?php endif; ?>
                            <div class="text-muted small">
                                <i class="ti ti-clock me-1"></i>
                                <?= date('H:i', strtotime($ag->hora_inicio)) ?>
                            </div>
                        </div>
                    </td>

                    <!-- Serviço -->
                    <td>
                        <div><?= $ag->servico_nome ?></div>
                        <div class="text-muted small"><?= $ag->servico_duracao ?? 30 ?> min</div>
                    </td>

                    <!-- Status -->
                    <td>
                        <?php
                        $badge_class = 'bg-secondary';
                        $status_texto = $ag->status;
                        $status_icon = 'ti-info-circle';

                        switch ($ag->status) {
                            case 'pendente':
                                $badge_class = 'bg-warning';
                                $status_texto = 'Pendente';
                                $status_icon = 'ti-clock';
                                break;
                            case 'confirmado':
                                $badge_class = 'bg-success';
                                $status_texto = 'Confirmado';
                                $status_icon = 'ti-check';
                                break;
                            case 'finalizado':
                                $badge_class = 'bg-info';
                                $status_texto = 'Finalizado';
                                $status_icon = 'ti-circle-check';
                                break;
                            case 'cancelado':
                                $badge_class = 'bg-danger';
                                $status_texto = 'Cancelado';
                                $status_icon = 'ti-x';
                                break;
                            case 'nao_compareceu':
                                $badge_class = 'bg-dark';
                                $status_texto = 'Não Compareceu';
                                $status_icon = 'ti-user-x';
                                break;
                        }
                        ?>
                        <span class="badge <?= $badge_class ?>">
                            <i class="ti <?= $status_icon ?> me-1"></i>
                            <?= $status_texto ?>
                        </span>
                    </td>

                    <!-- Pagamento -->
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
                                <span class="badge bg-primary">
                                    <i class="ti ti-credit-card me-1"></i>Cartão
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="ti ti-minus me-1"></i>N/D
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Ações -->
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <!-- Botão Finalizar (apenas para confirmado) -->
                            <?php if ($ag->status == 'confirmado'): ?>
                            <button type="button"
                                    class="btn btn-success"
                                    title="Finalizar Atendimento"
                                    onclick="finalizarAtendimento(<?= $ag->id ?>)">
                                <i class="ti ti-circle-check"></i>
                                Finalizar
                            </button>
                            <?php endif; ?>

                            <!-- Botão Não Compareceu (para todos) -->
                            <?php if (!in_array($ag->status, ['finalizado', 'cancelado', 'nao_compareceu'])): ?>
                            <button type="button"
                                    class="btn btn-outline-dark"
                                    title="Marcar como Não Compareceu"
                                    onclick="marcarNaoCompareceu(<?= $ag->id ?>)">
                                <i class="ti ti-user-x"></i>
                            </button>
                            <?php endif; ?>

                            <!-- Botão WhatsApp -->
                            <?php if (!empty($ag->cliente_whatsapp)): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ag->cliente_whatsapp) ?>"
                               target="_blank"
                               class="btn btn-outline-success"
                               title="Enviar WhatsApp">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                            <?php endif; ?>

                            <!-- Botão Editar -->
                            <a href="<?= base_url('painel/agendamentos/editar/' . $ag->id) ?>"
                               class="btn btn-outline-primary"
                               title="Editar/Reagendar">
                                <i class="ti ti-pencil"></i>
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

<!-- Scripts (placeholder - será implementado depois) -->
<script>
// Busca em tempo real (será implementado)
document.getElementById('busca-cliente')?.addEventListener('input', function(e) {
    // TODO: Implementar busca em tempo real
    console.log('Busca:', e.target.value);
});

// Finalizar atendimento
function finalizarAtendimento(id) {
    console.log('Finalizar:', id);
    // TODO: Implementar
}

// Marcar como não compareceu
function marcarNaoCompareceu(id) {
    console.log('Não compareceu:', id);
    // TODO: Implementar
}
</script>
