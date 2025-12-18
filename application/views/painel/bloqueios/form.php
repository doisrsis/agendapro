<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('painel/bloqueios') ?>">← Voltar</a>
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
                                <label class="form-label">O que deseja bloquear?</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="tipo_bloqueio" value="profissional" class="form-selectgroup-input" checked>
                                        <span class="form-selectgroup-label">
                                            <i class="ti ti-user me-1"></i>
                                            Profissional
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="tipo_bloqueio" value="servico" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">
                                            <i class="ti ti-briefcase me-1"></i>
                                            Serviço
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="tipo_bloqueio" value="ambos" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">
                                            <i class="ti ti-users me-1"></i>
                                            Profissional + Serviço
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted">
                                    • Profissional: Bloqueia todos os serviços do profissional<br>
                                    • Serviço: Bloqueia o serviço para todos os profissionais<br>
                                    • Ambos: Bloqueia serviço específico para profissional específico
                                </small>
                            </div>

                            <div class="mb-3" id="campo-profissional">
                                <label class="form-label required">Profissional</label>
                                <select class="form-select" name="profissional_id" id="profissional_id">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($profissionais as $prof): ?>
                                    <option value="<?= $prof->id ?>"
                                            <?= (isset($bloqueio) && $bloqueio->profissional_id == $prof->id) ? 'selected' : '' ?>>
                                        <?= $prof->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3" id="campo-servico" style="display:none;">
                                <label class="form-label required">Serviço</label>
                                <select class="form-select" name="servico_id" id="servico_id">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($servicos as $serv): ?>
                                    <option value="<?= $serv->id ?>"
                                            <?= (isset($bloqueio) && $bloqueio->servico_id == $serv->id) ? 'selected' : '' ?>>
                                        <?= $serv->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

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
                                           value="<?= isset($bloqueio) ? $bloqueio->data_inicio : '' ?>" required>
                                </div>

                                <div class="col-md-6 mb-3" id="campo-data-fim" style="display:none;">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" name="data_fim"
                                           value="<?= isset($bloqueio) ? $bloqueio->data_fim : '' ?>">
                                </div>
                            </div>

                            <div class="row" id="campo-horarios" style="display:none;">
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
                                <textarea class="form-control" name="motivo" rows="3"
                                          placeholder="Ex: Férias, Compromisso pessoal, etc."><?= isset($bloqueio) ? $bloqueio->motivo : '' ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-2"></i>
                                    Salvar
                                </button>
                                <a href="<?= base_url('painel/bloqueios') ?>" class="btn">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-info-circle me-2"></i>
                            Ajuda
                        </h3>
                    </div>
                    <div class="card-body">
                        <h4>Tipos de Bloqueio</h4>
                        <p class="text-muted small">
                            <strong>Profissional:</strong> Bloqueia todos os serviços do profissional selecionado.
                            Útil para férias ou folgas.
                        </p>
                        <p class="text-muted small">
                            <strong>Serviço:</strong> Bloqueia um serviço específico para todos os profissionais.
                            Útil quando um serviço está temporariamente indisponível.
                        </p>
                        <p class="text-muted small">
                            <strong>Profissional + Serviço:</strong> Bloqueia um serviço específico apenas para um profissional.
                            Útil quando um profissional não pode realizar determinado serviço temporariamente.
                        </p>

                        <hr>

                        <h4>Exemplos</h4>
                        <p class="text-muted small">
                            <strong>Férias:</strong> Profissional + Período<br>
                            <strong>Serviço indisponível:</strong> Serviço + Dia<br>
                            <strong>Equipamento em manutenção:</strong> Profissional + Serviço + Horário
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Controlar exibição de campos baseado no tipo de bloqueio selecionado
document.addEventListener('DOMContentLoaded', function() {
    const tipoBloqueioRadios = document.querySelectorAll('input[name="tipo_bloqueio"]');
    const campoProfissional = document.getElementById('campo-profissional');
    const campoServico = document.getElementById('campo-servico');
    const profissionalSelect = document.getElementById('profissional_id');
    const servicoSelect = document.getElementById('servico_id');

    tipoBloqueioRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'profissional') {
                campoProfissional.style.display = 'block';
                campoServico.style.display = 'none';
                profissionalSelect.required = true;
                servicoSelect.required = false;
                servicoSelect.value = '';
            } else if (this.value === 'servico') {
                campoProfissional.style.display = 'none';
                campoServico.style.display = 'block';
                profissionalSelect.required = false;
                servicoSelect.required = true;
                profissionalSelect.value = '';
            } else if (this.value === 'ambos') {
                campoProfissional.style.display = 'block';
                campoServico.style.display = 'block';
                profissionalSelect.required = true;
                servicoSelect.required = true;
            }
        });
    });

    // Controlar exibição de campos baseado no tipo de período
    const tipoSelect = document.getElementById('tipo_bloqueio');
    const campoDataFim = document.getElementById('campo-data-fim');
    const campoHorarios = document.getElementById('campo-horarios');

    tipoSelect.addEventListener('change', function() {
        if (this.value === 'periodo') {
            campoDataFim.style.display = 'block';
            campoHorarios.style.display = 'none';
        } else if (this.value === 'horario') {
            campoDataFim.style.display = 'none';
            campoHorarios.style.display = 'block';
        } else {
            campoDataFim.style.display = 'none';
            campoHorarios.style.display = 'none';
        }
    });

    // Inicializar estado correto ao carregar (para edição)
    <?php if (isset($bloqueio)): ?>
    if (<?= !empty($bloqueio->profissional_id) ? 'true' : 'false' ?> && <?= !empty($bloqueio->servico_id) ? 'true' : 'false' ?>) {
        document.querySelector('input[name="tipo_bloqueio"][value="ambos"]').checked = true;
        campoProfissional.style.display = 'block';
        campoServico.style.display = 'block';
    } else if (<?= !empty($bloqueio->servico_id) ? 'true' : 'false' ?>) {
        document.querySelector('input[name="tipo_bloqueio"][value="servico"]').checked = true;
        campoProfissional.style.display = 'none';
        campoServico.style.display = 'block';
    }

    if ('<?= $bloqueio->tipo ?>' === 'periodo') {
        campoDataFim.style.display = 'block';
    } else if ('<?= $bloqueio->tipo ?>' === 'horario') {
        campoHorarios.style.display = 'block';
    }
    <?php endif; ?>
});
</script>
