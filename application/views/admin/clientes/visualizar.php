<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/clientes') ?>">Clientes</a>
                </div>
                <h2 class="page-title">
                    <?= $cliente->nome ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/clientes/editar/' . $cliente->id) ?>" class="btn btn-primary">
                    <i class="ti ti-edit me-2"></i>
                    Editar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-md-4">
                <!-- Informações do Cliente -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <?php if ($cliente->foto): ?>
                        <img src="<?= base_url('uploads/clientes/' . $cliente->foto) ?>"
                             alt="<?= $cliente->nome ?>"
                             class="avatar avatar-xl mb-3">
                        <?php else: ?>
                        <span class="avatar avatar-xl mb-3">
                            <?= strtoupper(substr($cliente->nome, 0, 2)) ?>
                        </span>
                        <?php endif; ?>

                        <h3 class="mb-1"><?= $cliente->nome ?></h3>

                        <?php
                        $badge_class = 'bg-secondary';
                        switch ($cliente->tipo) {
                            case 'novo':
                                $badge_class = 'bg-info';
                                break;
                            case 'recorrente':
                                $badge_class = 'bg-success';
                                break;
                            case 'vip':
                                $badge_class = 'bg-yellow';
                                break;
                        }
                        ?>
                        <span class="badge <?= $badge_class ?> mb-3">
                            <?= ucfirst($cliente->tipo) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">WhatsApp:</small>
                            <div><?= $cliente->whatsapp ?></div>
                        </div>
                        <?php if ($cliente->telefone): ?>
                        <div class="mb-2">
                            <small class="text-muted">Telefone:</small>
                            <div><?= $cliente->telefone ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($cliente->email): ?>
                        <div class="mb-2">
                            <small class="text-muted">E-mail:</small>
                            <div><?= $cliente->email ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($cliente->cpf): ?>
                        <div class="mb-2">
                            <small class="text-muted">CPF:</small>
                            <div><?= $cliente->cpf ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Estatísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total de Agendamentos</span>
                                <strong><?= $cliente->total_agendamentos ?></strong>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Cliente desde:</small>
                            <div><?= date('d/m/Y', strtotime($cliente->criado_em)) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Histórico de Agendamentos -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-history me-2"></i>
                            Histórico de Agendamentos
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Serviço</th>
                                    <th>Profissional</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historico)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                                        Nenhum agendamento encontrado
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($historico as $ag): ?>
                                <tr>
                                    <td>
                                        <div class="text-nowrap">
                                            <?= date('d/m/Y', strtotime($ag->data)) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?= date('H:i', strtotime($ag->hora_inicio)) ?>
                                        </div>
                                    </td>
                                    <td><?= $ag->servico_nome ?></td>
                                    <td><?= $ag->profissional_nome ?></td>
                                    <td>
                                        <?php
                                        $badge_class = 'bg-secondary';
                                        switch ($ag->status) {
                                            case 'pendente':
                                                $badge_class = 'bg-warning';
                                                break;
                                            case 'confirmado':
                                                $badge_class = 'bg-info';
                                                break;
                                            case 'cancelado':
                                                $badge_class = 'bg-danger';
                                                break;
                                            case 'finalizado':
                                                $badge_class = 'bg-success';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= ucfirst($ag->status) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
