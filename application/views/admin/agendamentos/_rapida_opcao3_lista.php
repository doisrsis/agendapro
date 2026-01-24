<!-- OPÇÃO 3: LAYOUT LISTA COMPACTA (Estilo Inbox/Timeline) -->
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

<!-- Lista de Agendamentos - LISTA COMPACTA -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-calendar-check me-2"></i>
            Atendimentos de Hoje
            <span class="badge bg-blue ms-2"><?= number_format($total, 0, ',', '.') ?></span>
        </h3>
    </div>
    <div class="list-group list-group-flush">
        <?php if (empty($agendamentos)): ?>
        <div class="list-group-item text-center text-muted py-5">
            <i class="ti ti-calendar-off fs-1 mb-3 d-block"></i>
            <h3>Nenhum agendamento encontrado</h3>
        </div>
        <?php else: ?>
        <?php foreach ($agendamentos as $ag): ?>
        <div class="list-group-item">
            <div class="row align-items-center">
                <!-- Coluna 1: Horário (fixo à esquerda) -->
                <div class="col-auto">
                    <div class="text-center">
                        <div class="badge bg-blue-lt fs-3 px-3 py-2">
                            <?= date('H:i', strtotime($ag->hora_inicio)) ?>
                        </div>
                    </div>
                </div>

                <!-- Coluna 2: Informações do Cliente e Serviço -->
                <div class="col">
                    <div class="d-flex flex-column">
                        <!-- Nome do Cliente -->
                        <div class="fw-bold fs-4 mb-1"><?= $ag->cliente_nome ?></div>

                        <!-- Telefone -->
                        <?php if (!empty($ag->cliente_whatsapp)): ?>
                        <div class="text-muted small mb-1">
                            <i class="ti ti-phone me-1"></i>
                            <?= $ag->cliente_whatsapp ?>
                        </div>
                        <?php endif; ?>

                        <!-- Serviço -->
                        <div class="text-muted">
                            <i class="ti ti-scissors me-1"></i>
                            <strong><?= $ag->servico_nome ?></strong>
                            <span class="small">(<?= $ag->servico_duracao ?? 30 ?> min)</span>
                        </div>
                    </div>
                </div>

                <!-- Coluna 3: Status e Pagamento (Badges) -->
                <div class="col-auto">
                    <div class="d-flex flex-column gap-2">
                        <!-- Status -->
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
                        <span class="badge <?= $badge_class ?> text-start">
                            <i class="ti <?= $status_icon ?> me-1"></i>
                            <?= $status_texto ?>
                        </span>

                        <!-- Pagamento -->
                        <?php if (!empty($ag->forma_pagamento) && $ag->forma_pagamento != 'nao_definido'): ?>
                            <?php if ($ag->forma_pagamento == 'pix'): ?>
                                <?php if ($ag->pagamento_status == 'pago'): ?>
                                    <span class="badge bg-success text-start">
                                        <i class="ti ti-check me-1"></i>PIX Pago
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-start">
                                        <i class="ti ti-clock me-1"></i>PIX Pendente
                                    </span>
                                <?php endif; ?>
                            <?php elseif ($ag->forma_pagamento == 'presencial'): ?>
                                <span class="badge bg-info text-start">
                                    <i class="ti ti-building-store me-1"></i>Presencial
                                </span>
                            <?php elseif ($ag->forma_pagamento == 'cartao'): ?>
                                <span class="badge bg-primary text-start">
                                    <i class="ti ti-credit-card me-1"></i>Cartão
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-secondary text-start">
                                <i class="ti ti-minus me-1"></i>N/D
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Coluna 4: Ações (Botões) -->
                <div class="col-auto">
                    <div class="btn-group-vertical btn-group-sm">
                        <!-- Botão Finalizar (apenas para confirmado) -->
                        <?php if ($ag->status == 'confirmado'): ?>
                        <button type="button"
                                class="btn btn-success"
                                title="Finalizar Atendimento"
                                onclick="finalizarAtendimento(<?= $ag->id ?>)">
                            <i class="ti ti-circle-check me-1"></i>
                            Finalizar
                        </button>
                        <?php endif; ?>

                        <!-- Botão Não Compareceu -->
                        <?php if (!in_array($ag->status, ['finalizado', 'cancelado', 'nao_compareceu'])): ?>
                        <button type="button"
                                class="btn btn-outline-dark"
                                title="Marcar como Não Compareceu"
                                onclick="marcarNaoCompareceu(<?= $ag->id ?>)">
                            <i class="ti ti-user-x me-1"></i>
                            Não Compareceu
                        </button>
                        <?php endif; ?>

                        <!-- Botão WhatsApp -->
                        <?php if (!empty($ag->cliente_whatsapp)): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ag->cliente_whatsapp) ?>"
                           target="_blank"
                           class="btn btn-outline-success"
                           title="Enviar WhatsApp">
                            <i class="ti ti-brand-whatsapp me-1"></i>
                            WhatsApp
                        </a>
                        <?php endif; ?>

                        <!-- Botão Editar -->
                        <a href="<?= base_url('painel/agendamentos/editar/' . $ag->id) ?>"
                           class="btn btn-outline-primary"
                           title="Editar/Reagendar">
                            <i class="ti ti-pencil me-1"></i>
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
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
