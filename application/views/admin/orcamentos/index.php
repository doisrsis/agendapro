<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-file-invoice me-2"></i>
                    Orçamentos
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <!-- Estatísticas -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total de Orçamentos</div>
                        </div>
                        <div class="h1 mb-0"><?= $total ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pendentes</div>
                        </div>
                        <div class="h1 mb-0 text-warning"><?= $pendentes ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Aprovados</div>
                        </div>
                        <div class="h1 mb-0 text-success"><?= $aprovados ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Valor Total</div>
                        </div>
                        <div class="h1 mb-0 text-primary">R$ <?= number_format($valor_total, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="busca" class="form-control" placeholder="Número, cliente..." value="<?= isset($filtros['busca']) ? $filtros['busca'] : '' ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendente" <?= (isset($filtros['status']) && $filtros['status'] == 'pendente') ? 'selected' : '' ?>>Pendente</option>
                            <option value="em_analise" <?= (isset($filtros['status']) && $filtros['status'] == 'em_analise') ? 'selected' : '' ?>>Em Análise</option>
                            <option value="aprovado" <?= (isset($filtros['status']) && $filtros['status'] == 'aprovado') ? 'selected' : '' ?>>Aprovado</option>
                            <option value="recusado" <?= (isset($filtros['status']) && $filtros['status'] == 'recusado') ? 'selected' : '' ?>>Recusado</option>
                            <option value="cancelado" <?= (isset($filtros['status']) && $filtros['status'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                            <option value="em_preparacao" <?= (isset($filtros['status']) && $filtros['status'] == 'em_preparacao') ? 'selected' : '' ?>>Em Preparação</option>
                            <option value="pedido_postado" <?= (isset($filtros['status']) && $filtros['status'] == 'pedido_postado') ? 'selected' : '' ?>>Pedido Postado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Data Início</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?= isset($filtros['data_inicio']) ? $filtros['data_inicio'] : '' ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Data Fim</label>
                        <input type="date" name="data_fim" class="form-control" value="<?= isset($filtros['data_fim']) ? $filtros['data_fim'] : '' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> Filtrar
                            </button>
                            <a href="<?= base_url('admin/orcamentos') ?>" class="btn btn-secondary">
                                <i class="ti ti-x me-1"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mensagens -->
        <?php if($this->session->flashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-2"></i>
                <?= $this->session->flashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>
                <?= $this->session->flashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabela -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="w-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orcamentos)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-inbox" style="font-size: 48px;"></i>
                                    <p class="mt-2">Nenhum orçamento encontrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($orcamentos as $orc): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= $orc->numero ?></strong>
                                    </td>
                                    <td>
                                        <div><?= $orc->cliente_nome ?></div>
                                        <small class="text-muted"><?= $orc->cliente_email ?></small>
                                    </td>
                                    <td>
                                        <?php if($orc->tipo_atendimento == 'orcamento'): ?>
                                            <span class="badge bg-blue">Orçamento</span>
                                        <?php else: ?>
                                            <span class="badge bg-purple">Consultoria</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong>R$ <?= number_format($orc->valor_final, 2, ',', '.') ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = 'secondary';
                                        switch($orc->status) {
                                            case 'pendente': $badge_class = 'warning'; break;
                                            case 'em_analise': $badge_class = 'info'; break;
                                            case 'aprovado': $badge_class = 'success'; break;
                                            case 'recusado': $badge_class = 'danger'; break;
                                            case 'cancelado': $badge_class = 'dark'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?= ucfirst(str_replace('_', ' ', $orc->status)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($orc->criado_em)) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/orcamentos/visualizar/' . $orc->id) ?>" class="btn btn-sm btn-primary" title="Visualizar">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/orcamentos/imprimir/' . $orc->id) ?>" class="btn btn-sm btn-secondary" target="_blank" title="Imprimir">
                                                <i class="ti ti-printer"></i>
                                            </a>
                                            <a href="<?= base_url('admin/orcamentos/enviar_whatsapp/' . $orc->id) ?>" class="btn btn-sm btn-success" title="WhatsApp">
                                                <i class="ti ti-brand-whatsapp"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmarExclusao(<?= $orc->id ?>)" title="Excluir">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($pagination): ?>
                <div class="card-footer d-flex align-items-center">
                    <div class="ms-auto">
                        <?= $pagination ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este orçamento? Esta ação não pode ser desfeita.')) {
        window.location.href = '<?= base_url('admin/orcamentos/excluir/') ?>' + id;
    }
}
</script>
