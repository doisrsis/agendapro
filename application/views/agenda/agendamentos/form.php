<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('agenda/dashboard') ?>">Minha Agenda</a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plus me-2"></i>
                    Novo Agendamento
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body --->
<div class="page-body">
    <div class="container-xl">

        <form method="post">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Agendamento</h3>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label required">Cliente</label>
                                <select class="form-select" name="cliente_id" required>
                                    <option value="">Selecione o cliente...</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente->id ?>">
                                        <?= $cliente->nome ?> - <?= $cliente->whatsapp ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('cliente_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Serviço</label>
                                <select class="form-select" name="servico_id" id="servico_id" required>
                                    <option value="">Selecione o serviço...</option>
                                    <?php foreach ($servicos as $servico): ?>
                                    <option value="<?= $servico->id ?>" data-duracao="<?= $servico->duracao ?>">
                                        <?= $servico->nome ?> - <?= $servico->duracao ?> min - R$ <?= number_format($servico->preco, 2, ',', '.') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('servico_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data</label>
                                    <input type="date" class="form-control" name="data" id="data"
                                           value="<?= set_value('data', date('Y-m-d')) ?>"
                                           min="<?= date('Y-m-d') ?>"
                                           <?= isset($data_maxima) && $data_maxima ? 'max="' . $data_maxima . '"' : '' ?>
                                           required>
                                    <?= form_error('data', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Horário</label>
                                    <select class="form-select" name="hora_inicio" id="hora_inicio" required>
                                        <option value="">Selecione data e serviço primeiro</option>
                                    </select>
                                    <?= form_error('hora_inicio', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="observacoes" rows="3"><?= set_value('observacoes') ?></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ações</h3>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="ti ti-device-floppy me-2"></i>
                                Salvar Agendamento
                            </button>
                            <a href="<?= base_url('agenda/dashboard') ?>" class="btn btn-secondary w-100">
                                <i class="ti ti-x me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Informações</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-muted small">
                                <p><i class="ti ti-info-circle me-2"></i>O agendamento será criado com status <strong>Confirmado</strong>.</p>
                                <p><i class="ti ti-clock me-2"></i>Certifique-se de que o horário está disponível.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<?php $this->load->view('agenda/agendamentos/_horarios_script'); ?>
