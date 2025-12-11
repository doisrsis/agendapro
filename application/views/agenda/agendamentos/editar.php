<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('agenda/dashboard') ?>">Minha Agenda</a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-edit me-2"></i>
                    Editar Agendamento
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
                                <label class="form-label">Cliente</label>
                                <input type="text" class="form-control" value="<?= $agendamento->cliente_nome ?>" disabled>
                                <small class="text-muted">Não é possível alterar o cliente</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Serviço</label>
                                <input type="text" class="form-control" value="<?= $agendamento->servico_nome ?>" disabled>
                                <small class="text-muted">Não é possível alterar o serviço</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data</label>
                                    <input type="date" class="form-control" name="data"
                                           value="<?= set_value('data', $agendamento->data) ?>" required>
                                    <?= form_error('data', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Horário</label>
                                    <input type="time" class="form-control" name="hora_inicio"
                                           value="<?= set_value('hora_inicio', $agendamento->hora_inicio) ?>" required>
                                    <?= form_error('hora_inicio', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="pendente" <?= $agendamento->status == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="confirmado" <?= $agendamento->status == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                    <option value="concluido" <?= $agendamento->status == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                    <option value="cancelado" <?= $agendamento->status == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                                <?= form_error('status', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="observacoes" rows="3"><?= set_value('observacoes', $agendamento->observacoes) ?></textarea>
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
                                Salvar Alterações
                            </button>
                            <a href="<?= base_url('agenda/dashboard') ?>" class="btn btn-secondary w-100 mb-2">
                                <i class="ti ti-x me-2"></i>
                                Cancelar
                            </a>
                            <a href="<?= base_url('agenda/agendamentos/cancelar/' . $agendamento->id) ?>"
                               class="btn btn-danger w-100"
                               onclick="return confirm('Tem certeza que deseja cancelar este agendamento?')">
                                <i class="ti ti-ban me-2"></i>
                                Cancelar Agendamento
                            </a>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Informações</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-muted small">
                                <p><strong>Cliente:</strong> <?= $agendamento->cliente_nome ?></p>
                                <?php if ($agendamento->cliente_whatsapp): ?>
                                <p><strong>WhatsApp:</strong> <?= $agendamento->cliente_whatsapp ?></p>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $agendamento->cliente_whatsapp) ?>"
                                   class="btn btn-success btn-sm w-100 mb-2" target="_blank">
                                    <i class="ti ti-brand-whatsapp me-2"></i>Enviar WhatsApp
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
