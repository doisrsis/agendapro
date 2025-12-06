<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/profissionais') ?>">Profissionais</a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-clock me-2"></i>
                    Disponibilidade - <?= $profissional->nome ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/disponibilidade/criar_padrao/' . $profissional->id) ?>"
                   class="btn btn-secondary"
                   onclick="return confirm('Criar disponibilidade padrão (Seg-Sex 8h-12h e 14h-18h, Sáb 8h-12h)?')">
                    <i class="ti ti-wand me-2"></i>
                    Criar Padrão
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- Mensagens -->
        <?php if ($this->session->flashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('sucesso') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('erro') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <div class="row">
            <?php
            $dias_semana = [
                1 => 'Segunda-feira',
                2 => 'Terça-feira',
                3 => 'Quarta-feira',
                4 => 'Quinta-feira',
                5 => 'Sexta-feira',
                6 => 'Sábado',
                7 => 'Domingo'
            ];

            foreach ($dias_semana as $dia_num => $dia_nome):
            ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= $dia_nome ?></h3>
                        <div class="card-actions">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-adicionar-<?= $dia_num ?>">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($disponibilidades_por_dia[$dia_num]) && !empty($disponibilidades_por_dia[$dia_num])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($disponibilidades_por_dia[$dia_num] as $disp): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="ti ti-clock me-2"></i>
                                    <?= date('H:i', strtotime($disp->hora_inicio)) ?> - <?= date('H:i', strtotime($disp->hora_fim)) ?>
                                </div>
                                <div class="btn-list">
                                    <a href="<?= base_url('admin/disponibilidade/deletar/' . $disp->id) ?>"
                                       class="btn btn-sm btn-icon btn-ghost-danger"
                                       onclick="return confirm('Tem certeza que deseja deletar este horário?')">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center py-3">Nenhum horário configurado</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal Adicionar Horário -->
            <div class="modal fade" id="modal-adicionar-<?= $dia_num ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" action="<?= base_url('admin/disponibilidade/criar/' . $profissional->id) ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Adicionar Horário - <?= $dia_nome ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="dia_semana" value="<?= $dia_num ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Hora Início</label>
                                        <input type="time" class="form-control" name="hora_inicio" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Hora Fim</label>
                                        <input type="time" class="form-control" name="hora_fim" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
