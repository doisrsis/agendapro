<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/orcamentos') ?>">
                        <i class="ti ti-arrow-left me-1"></i> Voltar
                    </a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-file-invoice me-2"></i>
                    Orçamento #<?= $orcamento->numero ?>
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="<?= base_url('admin/orcamentos/imprimir/' . $orcamento->id) ?>" class="btn btn-secondary" target="_blank">
                        <i class="ti ti-printer me-1"></i> Imprimir
                    </a>
                    <a href="<?= base_url('admin/orcamentos/enviar_whatsapp/' . $orcamento->id) ?>" class="btn btn-success">
                        <i class="ti ti-brand-whatsapp me-1"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
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

        <div class="row">
            <!-- Coluna Principal -->
            <div class="col-lg-8">
                
                <!-- Informações do Orçamento -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Informações do Orçamento</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Número:</strong> #<?= $orcamento->numero ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($orcamento->criado_em)) ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tipo:</strong>
                                <?php if($orcamento->tipo_atendimento == 'orcamento'): ?>
                                    <span class="badge bg-blue">Orçamento</span>
                                <?php else: ?>
                                    <span class="badge bg-purple">Consultoria</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <?php
                                $badge_class = 'secondary';
                                switch($orcamento->status) {
                                    case 'pendente': $badge_class = 'warning'; break;
                                    case 'em_analise': $badge_class = 'info'; break;
                                    case 'aprovado': $badge_class = 'success'; break;
                                    case 'recusado': $badge_class = 'danger'; break;
                                    case 'cancelado': $badge_class = 'dark'; break;
                                }
                                ?>
                                <span class="badge bg-<?= $badge_class ?>">
                                    <?= ucfirst(str_replace('_', ' ', $orcamento->status)) ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if($orcamento->observacoes_cliente): ?>
                            <div class="mb-3">
                                <strong>Observações do Cliente:</strong>
                                <p class="text-muted mb-0"><?= nl2br($orcamento->observacoes_cliente) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($orcamento->observacoes_internas): ?>
                            <div>
                                <strong>Observações Internas:</strong>
                                <p class="text-muted mb-0"><?= nl2br($orcamento->observacoes_internas) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Itens do Orçamento -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Itens do Orçamento</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Detalhes</th>
                                    <th>Dimensões</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($itens)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="ti ti-inbox fs-1 mb-2 d-block"></i>
                                            <strong>Nenhum item encontrado neste orçamento</strong>
                                            <br>
                                            <small>Os itens podem não ter sido salvos corretamente no banco de dados.</small>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($itens as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= $item->produto->nome ?></strong>
                                            </td>
                                            <td>
                                                <?php if(isset($item->tecido)): ?>
                                                    <div><small class="text-muted">Tecido:</small> <?= $item->tecido->nome ?></div>
                                                <?php endif; ?>
                                                <?php if(isset($item->cor)): ?>
                                                    <div><small class="text-muted">Cor:</small> <?= $item->cor->nome ?></div>
                                                <?php endif; ?>
                                                <?php if(!empty($item->extras)): ?>
                                                    <div><small class="text-muted">Extras:</small>
                                                        <?php foreach($item->extras as $extra): ?>
                                                            <span class="badge bg-secondary"><?= $extra->extra_nome ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $item->largura ?>m × <?= $item->altura ?>m
                                                <?php if($item->quantidade > 1): ?>
                                                    <br><small class="text-muted">Qtd: <?= $item->quantidade ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <strong>R$ <?= number_format($item->preco_total, 2, ',', '.') ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end">
                                        <h3 class="text-primary mb-0">R$ <?= number_format($orcamento->valor_final, 2, ',', '.') ?></h3>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Coluna Lateral -->
            <div class="col-lg-4">
                
                <!-- Dados do Cliente -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Dados do Cliente</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong><i class="ti ti-user me-2"></i>Nome:</strong>
                            <div><?= $cliente->nome ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="ti ti-mail me-2"></i>Email:</strong>
                            <div><a href="mailto:<?= $cliente->email ?>"><?= $cliente->email ?></a></div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="ti ti-phone me-2"></i>Telefone:</strong>
                            <div><?= $cliente->telefone ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="ti ti-brand-whatsapp me-2"></i>WhatsApp:</strong>
                            <div><?= $cliente->whatsapp ?></div>
                        </div>
                        
                        <?php if($cliente->endereco): ?>
                            <div class="mb-3">
                                <strong><i class="ti ti-map-pin me-2"></i>Endereço:</strong>
                                <div><?= $cliente->endereco ?></div>
                                <div><?= $cliente->cidade ?> - <?= $cliente->estado ?></div>
                                <?php if($cliente->cep): ?>
                                    <div>CEP: <?= $cliente->cep ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alterar Status -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Alterar Status</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('admin/orcamentos/alterar_status/' . $orcamento->id) ?>">
                            <div class="mb-3">
                                <label class="form-label">Novo Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pendente" <?= $orcamento->status == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="em_analise" <?= $orcamento->status == 'em_analise' ? 'selected' : '' ?>>Em Análise</option>
                                    <option value="aprovado" <?= $orcamento->status == 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                                    <option value="recusado" <?= $orcamento->status == 'recusado' ? 'selected' : '' ?>>Recusado</option>
                                    <option value="cancelado" <?= $orcamento->status == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Observações Internas</label>
                                <textarea name="observacoes_internas" class="form-control" rows="3" placeholder="Adicione observações..."><?= $orcamento->observacoes_internas ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-check me-1"></i> Salvar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informações Adicionais</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Enviado WhatsApp:</small>
                            <?php if($orcamento->enviado_whatsapp): ?>
                                <span class="badge bg-success">Sim</span>
                                <?php if($orcamento->data_envio_whatsapp): ?>
                                    <div><small><?= date('d/m/Y H:i', strtotime($orcamento->data_envio_whatsapp)) ?></small></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Não</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">Enviado Email:</small>
                            <?php if($orcamento->enviado_email): ?>
                                <span class="badge bg-success">Sim</span>
                                <?php if($orcamento->data_envio_email): ?>
                                    <div><small><?= date('d/m/Y H:i', strtotime($orcamento->data_envio_email)) ?></small></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Não</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($orcamento->ip_cliente): ?>
                            <div class="mb-2">
                                <small class="text-muted">IP:</small> <?= $orcamento->ip_cliente ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
