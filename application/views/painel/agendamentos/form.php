<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url(($base_controller ?? 'admin') . '/agendamentos') ?>">Agendamentos</a>
                </div>
                <h2 class="page-title">
                    <?= isset($agendamento) ? 'Editar Agendamento' : 'Novo Agendamento' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form method="post" id="form-agendamento">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados do Agendamento -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Agendamento</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Cliente</label>
                                    <select class="form-select" name="cliente_id" id="cliente_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente->id ?>"
                                                <?= set_select('cliente_id', $cliente->id, ($agendamento->cliente_id ?? '') == $cliente->id) ?>>
                                            <?= $cliente->nome ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?= form_error('cliente_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Profissional</label>
                                    <select class="form-select" name="profissional_id" id="profissional_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($profissionais as $prof): ?>
                                        <option value="<?= $prof->id ?>"
                                                <?= set_select('profissional_id', $prof->id, ($agendamento->profissional_id ?? '') == $prof->id) ?>>
                                            <?= $prof->nome ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?= form_error('profissional_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Serviço</label>
                                <select class="form-select" name="servico_id" id="servico_id" required>
                                    <option value="">Selecione o profissional primeiro...</option>
                                    <?php foreach ($servicos as $servico):
                                        // Em modo edição, mostrar o serviço selecionado
                                        $isSelected = ($agendamento->servico_id ?? '') == $servico->id;
                                        $displayStyle = $isSelected ? '' : 'display:none;';
                                    ?>
                                    <option value="<?= $servico->id ?>"
                                            data-preco="<?= $servico->preco ?>"
                                            data-profissionais="<?= isset($servico->profissionais) ? $servico->profissionais : '' ?>"
                                            style="<?= $displayStyle ?>"
                                            <?= $isSelected ? 'selected' : '' ?>>
                                        <?= $servico->nome ?> - R$ <?= number_format($servico->preco, 2, ',', '.') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('servico_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data</label>
                                    <input type="date" class="form-control" name="data" id="data"
                                           value="<?= set_value('data', $agendamento->data ?? '') ?>"
                                           min="<?= date('Y-m-d') ?>"
                                           <?= isset($data_maxima) && $data_maxima ? 'max="' . $data_maxima . '"' : '' ?>
                                           required>
                                    <?= form_error('data', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Horário</label>
                                    <select class="form-select" name="hora_inicio" id="hora_inicio" required>
                                        <option value="">Selecione data e serviço primeiro</option>
                                        <?php if (isset($agendamento)): ?>
                                        <option value="<?= date('H:i', strtotime($agendamento->hora_inicio)) ?>" selected>
                                            <?= date('H:i', strtotime($agendamento->hora_inicio)) ?>
                                        </option>
                                        <?php endif; ?>
                                    </select>
                                    <?= form_error('hora_inicio', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="observacoes" rows="3"><?= set_value('observacoes', $agendamento->observacoes ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Ações -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ações</h3>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="ti ti-device-floppy me-2"></i>
                                <?= isset($agendamento) ? 'Salvar Alterações' : 'Salvar Agendamento' ?>
                            </button>
                            <a href="<?= base_url('painel/agendamentos') ?>" class="btn btn-secondary w-100 mb-2">
                                <i class="ti ti-x me-2"></i>
                                Cancelar
                            </a>
                            <?php if (isset($agendamento)): ?>
                            <a href="<?= base_url('painel/agendamentos/cancelar/' . $agendamento->id) ?>"
                               class="btn btn-danger w-100"
                               onclick="return confirm('Tem certeza que deseja cancelar este agendamento?')">
                                <i class="ti ti-ban me-2"></i>
                                Cancelar Agendamento
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Informações -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Informações</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($agendamento)): ?>
                            <div class="text-muted small">
                                <p><strong>Cliente:</strong> <?= $agendamento->cliente_nome ?? '-' ?></p>
                                <?php if (!empty($agendamento->cliente_whatsapp)): ?>
                                <p><strong>WhatsApp:</strong> <?= $agendamento->cliente_whatsapp ?></p>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $agendamento->cliente_whatsapp) ?>"
                                   class="btn btn-success btn-sm w-100 mb-2" target="_blank">
                                    <i class="ti ti-brand-whatsapp me-2"></i>Enviar WhatsApp
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-muted small">
                                <p><i class="ti ti-info-circle me-2"></i>Preencha todos os campos obrigatórios.</p>
                                <p><i class="ti ti-clock me-2"></i>Certifique-se de que o horário está disponível.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Status</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-select" name="status">
                                <option value="pendente" <?= ($agendamento->status ?? 'pendente') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="confirmado" <?= ($agendamento->status ?? '') == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                <option value="cancelado" <?= ($agendamento->status ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                <option value="finalizado" <?= ($agendamento->status ?? '') == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pagamento -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Pagamento</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Forma de Pagamento</label>
                                <select class="form-select" name="forma_pagamento">
                                    <option value="nao_definido" <?= ($agendamento->forma_pagamento ?? 'nao_definido') == 'nao_definido' ? 'selected' : '' ?>>Não Definido</option>
                                    <option value="pix" <?= ($agendamento->forma_pagamento ?? '') == 'pix' ? 'selected' : '' ?>>PIX (Mercado Pago)</option>
                                    <option value="pix_manual" <?= ($agendamento->forma_pagamento ?? '') == 'pix_manual' ? 'selected' : '' ?>>PIX Manual</option>
                                    <option value="presencial" <?= ($agendamento->forma_pagamento ?? '') == 'presencial' ? 'selected' : '' ?>>Presencial</option>
                                    <option value="cartao" <?= ($agendamento->forma_pagamento ?? '') == 'cartao' ? 'selected' : '' ?>>Cartão</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Status do Pagamento</label>
                                <select class="form-select" name="pagamento_status">
                                    <option value="nao_requerido" <?= ($agendamento->pagamento_status ?? 'nao_requerido') == 'nao_requerido' ? 'selected' : '' ?>>Não Requerido</option>
                                    <option value="pendente" <?= ($agendamento->pagamento_status ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="pago" <?= ($agendamento->pagamento_status ?? '') == 'pago' ? 'selected' : '' ?>>Pago</option>
                                    <option value="presencial" <?= ($agendamento->pagamento_status ?? '') == 'presencial' ? 'selected' : '' ?>>Presencial</option>
                                    <option value="expirado" <?= ($agendamento->pagamento_status ?? '') == 'expirado' ? 'selected' : '' ?>>Expirado</option>
                                    <option value="cancelado" <?= ($agendamento->pagamento_status ?? '') == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clienteSelect = document.getElementById('cliente_id');
    const servicoSelect = document.getElementById('servico_id');
    const profissionalSelect = document.getElementById('profissional_id');
    const dataInput = document.getElementById('data');
    const horaSelect = document.getElementById('hora_inicio');

    // ID do agendamento sendo editado (para excluir da verificação de conflitos)
    const agendamentoId = '<?= isset($agendamento) ? $agendamento->id : '' ?>';
    const horarioAtual = '<?= isset($agendamento) ? date('H:i', strtotime($agendamento->hora_inicio)) : '' ?>';
    const isEdicao = agendamentoId !== '';

    // Carregar serviços quando profissional for selecionado
    profissionalSelect.addEventListener('change', function() {
        const profissionalId = this.value;

        if (!profissionalId) {
            servicoSelect.innerHTML = '<option value="">Selecione o profissional primeiro...</option>';
            return;
        }

        // Carregar serviços do profissional
        servicoSelect.innerHTML = '<option value="">🔄 Carregando serviços...</option>';

        fetch('<?= base_url('painel/agendamentos/get_servicos_profissional/') ?>' + profissionalId)
            .then(r => r.json())
            .then(servicos => {
                servicoSelect.innerHTML = '<option value="">Selecione...</option>';

                if (servicos && servicos.length > 0) {
                    servicos.forEach(servico => {
                        const option = document.createElement('option');
                        option.value = servico.id;
                        option.dataset.preco = servico.preco;
                        option.textContent = `${servico.nome} - R$ ${parseFloat(servico.preco).toFixed(2).replace('.', ',')}`;
                        servicoSelect.appendChild(option);
                    });
                } else {
                    servicoSelect.innerHTML = '<option value="">Nenhum serviço ativo para este profissional</option>';
                }

                // Limpar horários
                horaSelect.innerHTML = '<option value="">Selecione data e serviço primeiro</option>';
            })
            .catch(error => {
                console.error('Erro ao carregar serviços:', error);
                servicoSelect.innerHTML = '<option value="">Erro ao carregar serviços</option>';
            });
    });

    // Carregar horários disponíveis
    function carregarHorarios() {
        const profissionalId = profissionalSelect.value;
        const data = dataInput.value;
        const servicoId = servicoSelect.value;

        if (profissionalId && data && servicoId) {
            // Mostrar loading
            horaSelect.innerHTML = '<option value="">🔄 Carregando horários...</option>';
            horaSelect.disabled = true;

            // Incluir agendamento_id na URL para excluir da verificação (modo edição)
            let url = `<?= base_url('painel/agendamentos/get_horarios_disponiveis') ?>?profissional_id=${profissionalId}&data=${data}&servico_id=${servicoId}`;
            if (agendamentoId) {
                url += `&agendamento_id=${agendamentoId}`;
            }

            fetch(url)
                .then(r => r.json())
                .then(horarios => {
                    horaSelect.disabled = false;

                    if (horarios.length > 0) {
                        horaSelect.innerHTML = '';

                        // Verificar se horário atual está na lista
                        let horarioAtualNaLista = horarios.includes(horarioAtual);

                        horarios.forEach(h => {
                            const isAtual = (h === horarioAtual);
                            const selected = isAtual ? 'selected' : '';
                            const label = isAtual ? `${h} (atual)` : h;
                            horaSelect.innerHTML += `<option value="${h}" ${selected}>${label}</option>`;
                        });

                        // Se horário atual não estava na lista (modo edição), adicionar no topo
                        if (isEdicao && !horarioAtualNaLista && horarioAtual) {
                            const option = document.createElement('option');
                            option.value = horarioAtual;
                            option.text = horarioAtual + ' (atual)';
                            option.selected = true;
                            horaSelect.insertBefore(option, horaSelect.firstChild);
                        }
                    } else {
                        // Manter horário atual mesmo sem outros disponíveis (modo edição)
                        if (isEdicao && horarioAtual) {
                            horaSelect.innerHTML = `<option value="${horarioAtual}" selected>${horarioAtual} (atual)</option>`;
                        } else {
                            horaSelect.innerHTML = '<option value="">❌ Nenhum horário disponível</option>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar horários:', error);
                    horaSelect.disabled = false;
                    if (isEdicao && horarioAtual) {
                        horaSelect.innerHTML = `<option value="${horarioAtual}" selected>${horarioAtual} (atual)</option>`;
                    } else {
                        horaSelect.innerHTML = '<option value="">⚠️ Erro ao carregar horários</option>';
                    }
                });
        }
    }

    profissionalSelect?.addEventListener('change', carregarHorarios);
    dataInput?.addEventListener('change', carregarHorarios);
    servicoSelect?.addEventListener('change', carregarHorarios);

    // Carregar horários automaticamente ao abrir página em modo edição
    if (isEdicao && profissionalSelect.value && dataInput.value && servicoSelect.value) {
        carregarHorarios();
    }

    // Atualizar resumo
    function atualizarResumo() {
        document.getElementById('resumo-cliente').textContent = clienteSelect.options[clienteSelect.selectedIndex]?.text || '-';
        document.getElementById('resumo-servico').textContent = servicoSelect.options[servicoSelect.selectedIndex]?.text || '-';
        document.getElementById('resumo-profissional').textContent = profissionalSelect.options[profissionalSelect.selectedIndex]?.text || '-';
        document.getElementById('resumo-data-hora').textContent = dataInput.value && horaSelect.value ?
            `${dataInput.value.split('-').reverse().join('/')} às ${horaSelect.value}` : '-';

        const preco = servicoSelect.options[servicoSelect.selectedIndex]?.dataset.preco || 0;
        document.getElementById('resumo-valor').textContent = `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;
    }

    clienteSelect?.addEventListener('change', atualizarResumo);
    servicoSelect?.addEventListener('change', atualizarResumo);
    profissionalSelect?.addEventListener('change', atualizarResumo);
    dataInput?.addEventListener('change', atualizarResumo);
    horaSelect?.addEventListener('change', atualizarResumo);
});
</script>
