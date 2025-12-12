<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('agenda/bloqueios') ?>">← Voltar</a>
                </div>
                <h2 class="page-title">
                    <?= isset($bloqueio) ? 'Editar Bloqueio' : 'Novo Bloqueio' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dados do Bloqueio</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label required">Tipo de Bloqueio</label>
                                <select class="form-select" name="tipo" id="tipo_bloqueio" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($tipos as $valor => $label): ?>
                                    <option value="<?= $valor ?>"
                                            <?= (isset($bloqueio) && $bloqueio->tipo == $valor) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    • Dia: Bloqueia um dia específico<br>
                                    • Período: Bloqueia vários dias (férias)<br>
                                    • Horário: Bloqueia apenas um horário específico
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Data Início</label>
                                    <input type="date" class="form-control" name="data_inicio"
                                           value="<?= isset($bloqueio) ? $bloqueio->data_inicio : '' ?>"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3" id="campo_data_fim" style="display: none;">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" name="data_fim"
                                           value="<?= isset($bloqueio) ? $bloqueio->data_fim : '' ?>">
                                </div>
                            </div>

                            <div class="row" id="campos_horario" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora Início</label>
                                    <input type="time" class="form-control" name="hora_inicio"
                                           value="<?= isset($bloqueio) && $bloqueio->hora_inicio ? substr($bloqueio->hora_inicio, 0, 5) : '' ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora Fim</label>
                                    <input type="time" class="form-control" name="hora_fim"
                                           value="<?= isset($bloqueio) && $bloqueio->hora_fim ? substr($bloqueio->hora_fim, 0, 5) : '' ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Motivo</label>
                                <input type="text" class="form-control" name="motivo"
                                       value="<?= isset($bloqueio) ? $bloqueio->motivo : '' ?>"
                                       placeholder="Ex: Férias, Compromisso médico, etc">
                            </div>

                            <div class="text-end">
                                <a href="<?= base_url('agenda/bloqueios') ?>" class="btn btn-link">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Bloqueio
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ajuda</h3>
                    </div>
                    <div class="card-body">
                        <h4>Tipos de Bloqueio</h4>

                        <div class="mb-3">
                            <strong>Dia Específico</strong>
                            <p class="text-muted small">Bloqueia um dia inteiro. Use para folgas pontuais.</p>
                        </div>

                        <div class="mb-3">
                            <strong>Período</strong>
                            <p class="text-muted small">Bloqueia vários dias consecutivos. Use para férias ou viagens.</p>
                        </div>

                        <div class="mb-3">
                            <strong>Horário Específico</strong>
                            <p class="text-muted small">Bloqueia apenas um horário em um dia. Use para compromissos pessoais.</p>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Bloqueios impedem que clientes agendem nos períodos definidos.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Mostrar/ocultar campos baseado no tipo
document.getElementById('tipo_bloqueio').addEventListener('change', function() {
    const tipo = this.value;
    const campoDataFim = document.getElementById('campo_data_fim');
    const camposHorario = document.getElementById('campos_horario');

    // Resetar
    campoDataFim.style.display = 'none';
    camposHorario.style.display = 'none';

    if (tipo === 'periodo') {
        campoDataFim.style.display = 'block';
    } else if (tipo === 'horario') {
        camposHorario.style.display = 'block';
    }
});

// Trigger no carregamento se estiver editando
<?php if (isset($bloqueio)): ?>
document.getElementById('tipo_bloqueio').dispatchEvent(new Event('change'));
<?php endif; ?>
</script>
