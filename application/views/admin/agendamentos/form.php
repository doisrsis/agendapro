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
                            <div class="mb-3">
                                <label class="form-label required">Estabelecimento</label>
                                <select class="form-select" name="estabelecimento_id" id="estabelecimento_id"
                                        <?= isset($agendamento) ? 'disabled' : '' ?> required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estabelecimentos as $est): ?>
                                    <option value="<?= $est->id ?>"
                                            <?= set_select('estabelecimento_id', $est->id, ($agendamento->estabelecimento_id ?? '') == $est->id) ?>>
                                        <?= $est->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('estabelecimento_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Cliente</label>
                                    <select class="form-select" name="cliente_id" id="cliente_id"
                                            <?= isset($agendamento) ? 'disabled' : '' ?> required>
                                        <option value="">Selecione o estabelecimento primeiro</option>
                                        <?php if (isset($agendamento)): ?>
                                        <option value="<?= $agendamento->cliente_id ?>" selected>
                                            <?= $agendamento->cliente_nome ?>
                                        </option>
                                        <?php endif; ?>
                                    </select>
                                    <?= form_error('cliente_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Serviço</label>
                                    <select class="form-select" name="servico_id" id="servico_id"
                                            <?= isset($agendamento) ? 'disabled' : '' ?> required>
                                        <option value="">Selecione o estabelecimento primeiro</option>
                                        <?php if (isset($agendamento)): ?>
                                        <option value="<?= $agendamento->servico_id ?>" selected>
                                            <?= $agendamento->servico_nome ?>
                                        </option>
                                        <?php endif; ?>
                                    </select>
                                    <?= form_error('servico_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Profissional</label>
                                <select class="form-select" name="profissional_id" id="profissional_id"
                                        <?= isset($agendamento) ? 'disabled' : '' ?> required>
                                    <option value="">Selecione o estabelecimento primeiro</option>
                                    <?php if (isset($agendamento)): ?>
                                    <option value="<?= $agendamento->profissional_id ?>" selected>
                                        <?= $agendamento->profissional_nome ?>
                                    </option>
                                    <?php endif; ?>
                                </select>
                                <?= form_error('profissional_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data</label>
                                    <input type="date" class="form-control" name="data" id="data"
                                           value="<?= set_value('data', $agendamento->data ?? '') ?>"
                                           min="<?= date('Y-m-d') ?>" required>
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
                    <!-- Status -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Status</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-select" name="status">
                                <option value="pendente" <?= set_select('status', 'pendente', ($agendamento->status ?? '') == 'pendente') ?>>Pendente</option>
                                <option value="confirmado" <?= set_select('status', 'confirmado', ($agendamento->status ?? 'confirmado') == 'confirmado') ?>>Confirmado</option>
                                <option value="cancelado" <?= set_select('status', 'cancelado', ($agendamento->status ?? '') == 'cancelado') ?>>Cancelado</option>
                                <option value="finalizado" <?= set_select('status', 'finalizado', ($agendamento->status ?? '') == 'finalizado') ?>>Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Resumo</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Cliente:</small>
                                <div id="resumo-cliente">-</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Serviço:</small>
                                <div id="resumo-servico">-</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Profissional:</small>
                                <div id="resumo-profissional">-</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Data/Hora:</small>
                                <div id="resumo-data-hora">-</div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Valor:</strong>
                                <strong id="resumo-valor">R$ 0,00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url(($base_controller ?? 'admin') . '/agendamentos') ?>" class="btn btn-link">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>
                        Salvar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const estabelecimentoSelect = document.getElementById('estabelecimento_id');
    const clienteSelect = document.getElementById('cliente_id');
    const servicoSelect = document.getElementById('servico_id');
    const profissionalSelect = document.getElementById('profissional_id');
    const dataInput = document.getElementById('data');
    const horaSelect = document.getElementById('hora_inicio');

    // Carregar dados ao mudar estabelecimento
    estabelecimentoSelect?.addEventListener('change', function() {
        const estabelecimentoId = this.value;

        if (estabelecimentoId) {
            // Carregar clientes
            fetch(`<?= base_url('admin/agendamentos/get_clientes/') ?>${estabelecimentoId}`)
                .then(r => r.json())
                .then(data => {
                    clienteSelect.innerHTML = '<option value="">Selecione...</option>';
                    data.forEach(c => {
                        clienteSelect.innerHTML += `<option value="${c.id}">${c.nome}</option>`;
                    });
                });

            // Carregar serviços
            fetch(`<?= base_url('admin/agendamentos/get_servicos/') ?>${estabelecimentoId}`)
                .then(r => r.json())
                .then(data => {
                    servicoSelect.innerHTML = '<option value="">Selecione...</option>';
                    data.forEach(s => {
                        servicoSelect.innerHTML += `<option value="${s.id}" data-preco="${s.preco}">${s.nome} - R$ ${parseFloat(s.preco).toFixed(2).replace('.', ',')}</option>`;
                    });
                });

            // Carregar profissionais
            fetch(`<?= base_url('admin/agendamentos/get_profissionais/') ?>${estabelecimentoId}`)
                .then(r => r.json())
                .then(data => {
                    profissionalSelect.innerHTML = '<option value="">Selecione...</option>';
                    data.forEach(p => {
                        profissionalSelect.innerHTML += `<option value="${p.id}">${p.nome}</option>`;
                    });
                });
        }
    });

    // Carregar horários disponíveis
    function carregarHorarios() {
        const profissionalId = profissionalSelect.value;
        const data = dataInput.value;
        const servicoId = servicoSelect.value;

        if (profissionalId && data && servicoId) {
            fetch(`<?= base_url('admin/agendamentos/get_horarios_disponiveis') ?>?profissional_id=${profissionalId}&data=${data}&servico_id=${servicoId}`)
                .then(r => r.json())
                .then(horarios => {
                    horaSelect.innerHTML = '<option value="">Selecione...</option>';
                    horarios.forEach(h => {
                        horaSelect.innerHTML += `<option value="${h}">${h}</option>`;
                    });
                });
        }
    }

    profissionalSelect?.addEventListener('change', carregarHorarios);
    dataInput?.addEventListener('change', carregarHorarios);
    servicoSelect?.addEventListener('change', carregarHorarios);

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
