<!-- VIEW RÁPIDA - Layout Cards (Opção 2 escolhida) -->
<!-- View Simplificada para Atendimento Rápido do Dia -->

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
                           value="<?= $filtros['busca'] ?? '' ?>"
                           autofocus>
                </div>

                <!-- Filtro de Status -->
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="filtro-status">
                        <option value="todos" <?= !isset($filtros['status']) || $filtros['status'] == '' ? 'selected' : '' ?>>Todos</option>
                        <option value="confirmado" <?= ($filtros['status'] ?? '') == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
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
                           id="filtro-data"
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

<!-- Cabeçalho da Lista -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">
        <i class="ti ti-calendar-check me-2"></i>
        Atendimentos de <?= date('d/m/Y', strtotime($filtros['data'] ?? date('Y-m-d'))) ?>
        <span class="badge bg-blue ms-2"><?= number_format($total, 0, ',', '.') ?></span>
    </h3>
</div>

<!-- Lista de Agendamentos - CARDS -->
<div class="row row-cards" id="lista-agendamentos">
    <?php if (empty($agendamentos)): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="ti ti-calendar-off fs-1 mb-3 d-block"></i>
                <h3>Nenhum agendamento encontrado</h3>
                <p class="text-muted">Tente ajustar os filtros acima</p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($agendamentos as $ag): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card card-sm">
            <div class="card-body">
                <!-- Header do Card: Cliente + Horário -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="card-title mb-1"><?= $ag->cliente_nome ?></h3>
                        <?php if (!empty($ag->cliente_whatsapp)): ?>
                        <div class="text-muted small">
                            <i class="ti ti-phone me-1"></i>
                            <?= $ag->cliente_whatsapp ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-blue-lt fs-3 px-3 py-2">
                            <i class="ti ti-clock me-1"></i>
                            <?= date('H:i', strtotime($ag->hora_inicio)) ?>
                        </div>
                    </div>
                </div>

                <!-- Informações do Agendamento -->
                <div class="mb-3">
                    <!-- Serviço -->
                    <div class="mb-2">
                        <i class="ti ti-scissors text-muted me-2"></i>
                        <strong><?= $ag->servico_nome ?></strong>
                        <span class="text-muted small">(<?= $ag->servico_duracao ?? 30 ?> min)</span>
                    </div>

                    <!-- Status -->
                    <div class="mb-2">
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
                    </div>

                    <!-- Pagamento -->
                    <div>
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
                    </div>
                </div>

                <!-- Ações -->
                <div class="d-grid gap-2">
                    <!-- Botão Finalizar (apenas para confirmado) -->
                    <?php if ($ag->status == 'confirmado'): ?>
                    <button type="button"
                            class="btn btn-success btn-finalizar"
                            data-agendamento-id="<?= $ag->id ?>"
                            data-cliente-nome="<?= $ag->cliente_nome ?>">
                        <i class="ti ti-circle-check me-2"></i>
                        Finalizar Atendimento
                    </button>
                    <?php endif; ?>

                    <!-- Botões de Ação Secundários -->
                    <div class="btn-group">
                        <!-- Botão Não Compareceu -->
                        <?php if (!in_array($ag->status, ['finalizado', 'cancelado', 'nao_compareceu'])): ?>
                        <button type="button"
                                class="btn btn-outline-dark btn-nao-compareceu"
                                data-agendamento-id="<?= $ag->id ?>"
                                data-cliente-nome="<?= $ag->cliente_nome ?>"
                                title="Marcar como Não Compareceu">
                            <i class="ti ti-user-x me-1"></i>
                            Não Compareceu
                        </button>
                        <?php endif; ?>

                        <!-- Botão Enviar/Reenviar Link de Pagamento PIX -->
                        <?php
                        // Condições para aparecer o botão:
                        // 1. Cliente tem WhatsApp
                        // 2. Status: pendente, confirmado ou nao_compareceu (não aparece para reagendado, cancelado ou finalizado)
                        // 3. Pagamento: pendente, cancelado, expirado OU presencial (para gerar PIX)
                        $mostrar_botao_pix = !empty($ag->cliente_whatsapp)
                            && in_array($ag->status, ['pendente', 'confirmado', 'nao_compareceu'])
                            && (in_array($ag->pagamento_status, ['pendente', 'cancelado', 'expirado'])
                                || $ag->forma_pagamento === 'presencial');

                        if ($mostrar_botao_pix):
                            // Texto dinâmico: se já tem token, é reenvio; se não, é geração
                            $tem_link = !empty($ag->pagamento_token);
                            $texto_botao = $tem_link ? 'Reenviar Link PIX' : 'Gerar Link PIX';
                            $icone = $tem_link ? 'ti-send' : 'ti-qrcode';
                        ?>
                        <button type="button"
                                class="btn btn-outline-info btn-reenviar-link"
                                data-agendamento-id="<?= $ag->id ?>"
                                data-cliente-nome="<?= $ag->cliente_nome ?>"
                                data-tem-link="<?= $tem_link ? '1' : '0' ?>"
                                title="<?= $texto_botao ?>">
                            <i class="ti <?= $icone ?> me-1"></i>
                            <?= $tem_link ? 'Reenviar Link' : 'Gerar PIX' ?>
                        </button>
                        <?php endif; ?>

                        <!-- Botão WhatsApp - FUNCIONAL -->
                        <?php if (!empty($ag->cliente_whatsapp)): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ag->cliente_whatsapp) ?>"
                           target="_blank"
                           class="btn btn-outline-success"
                           title="Enviar WhatsApp">
                            <i class="ti ti-brand-whatsapp"></i>
                        </a>
                        <?php endif; ?>

                        <!-- Botão Editar - FUNCIONAL -->
                        <a href="<?= base_url('painel/agendamentos/editar/' . $ag->id) ?>"
                           class="btn btn-outline-primary"
                           title="Editar/Reagendar">
                            <i class="ti ti-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script>
// Auto-submit ao mudar status ou data
document.getElementById('filtro-status')?.addEventListener('change', function() {
    document.getElementById('form-filtro-rapido').submit();
});

document.getElementById('filtro-data')?.addEventListener('change', function() {
    document.getElementById('form-filtro-rapido').submit();
});

// Busca ao pressionar Enter
document.getElementById('busca-cliente')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('form-filtro-rapido').submit();
    }
});

// Finalizar Atendimento
document.querySelectorAll('.btn-finalizar').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const agendamentoId = this.getAttribute('data-agendamento-id');
        const clienteNome = this.getAttribute('data-cliente-nome');

        Swal.fire({
            title: 'Finalizar Atendimento',
            html: `Confirma a finalização do atendimento de <strong>${clienteNome}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-circle-check me-1"></i> Sim, finalizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2fb344',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('<?= base_url('painel/agendamentos/finalizar_rapido/') ?>' + agendamentoId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Erro ao finalizar atendimento');
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Erro: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Sucesso: Remover card da lista com animação
                const card = document.querySelector(`[data-agendamento-id="${agendamentoId}"]`).closest('.col-md-6');

                // Animação de fade out
                card.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    card.remove();

                    // Verificar se ainda há cards na lista
                    const listaAgendamentos = document.getElementById('lista-agendamentos');
                    const cardsRestantes = listaAgendamentos.querySelectorAll('.col-md-6').length;

                    if (cardsRestantes === 0) {
                        // Mostrar mensagem de lista vazia
                        listaAgendamentos.innerHTML = `
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center text-muted py-5">
                                        <i class="ti ti-circle-check fs-1 mb-3 d-block text-success"></i>
                                        <h3>Todos os atendimentos foram finalizados!</h3>
                                        <p class="text-muted">Use os filtros acima para ver outros agendamentos</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    // Mostrar toast de sucesso
                    Swal.fire({
                        title: 'Finalizado!',
                        text: result.value.message,
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }, 300);
            }
        });
    });
});

// Marcar como Não Compareceu
document.querySelectorAll('.btn-nao-compareceu').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const agendamentoId = this.getAttribute('data-agendamento-id');
        const clienteNome = this.getAttribute('data-cliente-nome');

        Swal.fire({
            title: 'Cliente Não Compareceu',
            html: `<strong>${clienteNome}</strong> não compareceu ao atendimento?<br><br>` +
                  `<small class="text-muted">Uma notificação será enviada ao cliente oferecendo reagendamento.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-user-x me-1"></i> Sim, não compareceu',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d63939',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('<?= base_url('painel/agendamentos/marcar_nao_compareceu/') ?>' + agendamentoId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Erro ao marcar como não compareceu');
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Erro: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Sucesso: Remover card da lista com animação
                const card = document.querySelector(`[data-agendamento-id="${agendamentoId}"]`).closest('.col-md-6');

                // Animação de fade out
                card.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    card.remove();

                    // Verificar se ainda há cards na lista
                    const listaAgendamentos = document.getElementById('lista-agendamentos');
                    const cardsRestantes = listaAgendamentos.querySelectorAll('.col-md-6').length;

                    if (cardsRestantes === 0) {
                        // Mostrar mensagem de lista vazia
                        listaAgendamentos.innerHTML = `
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center text-muted py-5">
                                        <i class="ti ti-calendar-check fs-1 mb-3 d-block text-success"></i>
                                        <h3>Todos os agendamentos foram processados!</h3>
                                        <p class="text-muted">Use os filtros acima para ver outros agendamentos</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    // Mostrar toast de sucesso
                    Swal.fire({
                        title: 'Registrado!',
                        text: result.value.message,
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }, 300);
            }
        });
    });
});

// Enviar/Reenviar Link de Pagamento PIX (botão inteligente)
document.querySelectorAll('.btn-reenviar-link').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const agendamentoId = this.getAttribute('data-agendamento-id');
        const clienteNome = this.getAttribute('data-cliente-nome');
        const temLink = this.getAttribute('data-tem-link') === '1';

        // Textos dinâmicos baseados se já existe link ou não
        const titulo = temLink ? 'Reenviar Link de Pagamento' : 'Gerar Link de Pagamento PIX';
        const mensagem = temLink
            ? `Deseja reenviar o link de pagamento via WhatsApp para <strong>${clienteNome}</strong>?`
            : `Deseja gerar e enviar link de pagamento PIX via WhatsApp para <strong>${clienteNome}</strong>?<br><br><small class="text-muted">O link será gerado com o prazo de expiração configurado no sistema.</small>`;
        const icone = temLink ? 'ti-send' : 'ti-qrcode';
        const textoBotao = temLink ? 'Sim, reenviar' : 'Sim, gerar e enviar';

        Swal.fire({
            title: titulo,
            html: mensagem,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: `<i class="ti ${icone} me-1"></i> ${textoBotao}`,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0dcaf0',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('<?= base_url('painel/agendamentos/reenviar_link_pagamento/') ?>' + agendamentoId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Erro ao processar link de pagamento');
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Erro: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar toast de sucesso
                Swal.fire({
                    title: 'Enviado!',
                    text: result.value.message,
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
    });
});
</script>
