<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/clientes') ?>">Clientes</a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-user me-2"></i>
                    <?= $cliente->nome ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('admin/clientes') ?>" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Voltar
                    </a>
                    <a href="<?= base_url('admin/clientes/editar/' . $cliente->id) ?>" class="btn btn-primary">
                        <i class="ti ti-edit me-2"></i>Editar
                    </a>
                </div>
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
            <!-- Informações do Cliente -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Informações do Cliente</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <div class="avatar avatar-xl mx-auto mb-3" style="background-color: <?= sprintf('#%06X', mt_rand(0, 0xFFFFFF)) ?>; font-size: 2rem;">
                                <?= strtoupper(substr($cliente->nome, 0, 2)) ?>
                            </div>
                            <h3 class="mb-1"><?= $cliente->nome ?></h3>
                            <div class="text-muted">Cliente desde <?= date('d/m/Y', strtotime($cliente->criado_em)) ?></div>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-mail text-muted"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">E-mail</small>
                                        <a href="mailto:<?= $cliente->email ?>"><?= $cliente->email ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-phone text-muted"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">Telefone</small>
                                        <a href="tel:<?= $cliente->telefone ?>"><?= $cliente->telefone ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php if ($cliente->whatsapp): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-brand-whatsapp text-success"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">WhatsApp</small>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $cliente->whatsapp) ?>" target="_blank">
                                            <?= $cliente->whatsapp ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($cliente->cpf_cnpj): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-id text-muted"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">CPF/CNPJ</small>
                                        <?= $cliente->cpf_cnpj ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <?php if ($cliente->endereco || $cliente->cidade): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Endereço</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($cliente->endereco): ?>
                        <p class="mb-2"><?= $cliente->endereco ?></p>
                        <?php endif; ?>
                        <?php if ($cliente->cidade && $cliente->estado): ?>
                        <p class="mb-2"><?= $cliente->cidade ?> - <?= $cliente->estado ?></p>
                        <?php endif; ?>
                        <?php if ($cliente->cep): ?>
                        <p class="mb-0">CEP: <?= $cliente->cep ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Observações -->
                <?php if ($cliente->observacoes): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Observações</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br($cliente->observacoes) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Ações -->
                <div class="card">
                    <div class="card-body">
                        <a href="<?= base_url('admin/clientes/editar/' . $cliente->id) ?>" class="btn btn-primary w-100 mb-2">
                            <i class="ti ti-edit me-2"></i>Editar Cliente
                        </a>
                        <?php if ($total_orcamentos == 0): ?>
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                            <i class="ti ti-trash me-2"></i>Excluir Cliente
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Histórico de Orçamentos -->
            <div class="col-md-8">
                <!-- Estatísticas -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total de Orçamentos</div>
                                    <div class="ms-auto lh-1">
                                        <span class="badge bg-primary fs-3"><?= $total_orcamentos ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Valor Total</div>
                                    <div class="ms-auto lh-1">
                                        <span class="badge bg-success fs-3">R$ <?= number_format($valor_total, 2, ',', '.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Orçamentos -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Histórico de Orçamentos</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th class="w-1">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orcamentos)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="ti ti-file-invoice icon mb-2" style="font-size: 3rem;"></i>
                                        <p class="mb-0">Nenhum orçamento encontrado.</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($orcamentos as $orcamento): ?>
                                <tr>
                                    <td>
                                        <strong><?= $orcamento->numero ?></strong>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($orcamento->criado_em)) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'pendente' => 'warning',
                                            'aprovado' => 'success',
                                            'rejeitado' => 'danger',
                                            'finalizado' => 'info'
                                        ];
                                        $class = $status_class[$orcamento->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $class ?>">
                                            <?= ucfirst($orcamento->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>R$ <?= number_format($orcamento->valor_total, 2, ',', '.') ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/orcamentos/visualizar/' . $orcamento->id) ?>" 
                                           class="btn btn-sm btn-icon btn-primary" 
                                           title="Visualizar">
                                            <i class="ti ti-eye"></i>
                                        </a>
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

<!-- Modal de Exclusão -->
<div class="modal modal-blur fade" id="modalExcluir" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Tem certeza?</div>
                <div>Deseja realmente excluir este cliente? Esta ação não pode ser desfeita.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancelar</button>
                <a href="<?= base_url('admin/clientes/excluir/' . $cliente->id) ?>" class="btn btn-danger">Sim, excluir</a>
            </div>
        </div>
    </div>
</div>
