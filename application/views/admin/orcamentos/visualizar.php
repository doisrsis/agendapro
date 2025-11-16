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
                                    <th class="text-end">Valores</th>
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
                                                <?php
                                                    $extras_total_item = 0;
                                                    if (!empty($item->extras)) {
                                                        foreach ($item->extras as $extra_item) {
                                                            $extras_total_item += (float) ($extra_item->valor ?? 0);
                                                        }
                                                    }
                                                    $valor_base_item = (float) $item->preco_total - $extras_total_item;
                                                    if ($valor_base_item < 0) {
                                                        $valor_base_item = 0;
                                                    }
                                                ?>
                                                <div class="d-flex flex-column align-items-end gap-1">
                                                    <span class="small text-muted">Valor base</span>
                                                    <strong>R$ <?= number_format($valor_base_item, 2, ',', '.') ?></strong>
                                                    <span class="small text-muted mt-2"><strong>Extras</strong></span>
                                                <!--strong>R$ <//?= number_format($extras_total_item, 2, ',', '.') ?></strong-->
                                                    <?php if(!empty($item->extras)): ?>
                                                        <div class="mt-2 text-start w-100">
                                                            <?php foreach($item->extras as $extra_item): ?>
                                                                <div class="d-flex justify-content-between gap-2 small">
                                                                    <span><?= $extra_item->extra_nome ?></span>
                                                                    <span>R$ <?= number_format((float)($extra_item->valor ?? 0), 2, ',', '.') ?></span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <!--hr class="my-2 w-100">
                                                    <span class="text-muted small">Subtotal</span>
                                                    <h4 class="mb-0 text-primary">R$ <//?= number_format($item->preco_total, 2, ',', '.') ?></h4-->
                                                </div>
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
                                    <option value="em_preparacao" <?= $orcamento->status == 'em_preparacao' ? 'selected' : '' ?>>Em Preparação</option>
                                    <option value="pedido_postado" <?= $orcamento->status == 'pedido_postado' ? 'selected' : '' ?>>Pedido Postado</option>
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
                <!--div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Informações Adicionais</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Enviado WhatsApp:</small>
                            <//?php if($orcamento->enviado_whatsapp): ?>
                                <span class="badge bg-success">Sim</span>
                                <//?php if(//$orcamento->data_envio_whatsapp): ?>
                                    <div><small><//?= date('d/m/Y H:i', strtotime($orcamento->data_envio_whatsapp)) ?></small></div>
                                <//?php endif; ?>
                            <//?php else: ?>
                                <span class="badge bg-secondary">Não</span>
                            <//?php endif; ?>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Enviado Email:</small>
                            <//?php if($orcamento->enviado_email): ?>
                                <span class="badge bg-success">Sim</span>
                                <//?php if(//$orcamento->data_envio_email): ?>
                                    <div><small><//?= date('d/m/Y H:i', strtotime($orcamento->data_envio_email)) ?></small></div>
                                <//?php endif; ?>
                            <//?php else: ?>
                                <span class="badge bg-secondary">Não</span>
                            <//?php endif; ?>
                        </div>

                        <?php if($orcamento->ip_cliente): ?>
                            <div class="mb-2">
                                <small class="text-muted">IP:</small> <?= $orcamento->ip_cliente ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div-->

                <!-- Histórico de E-mails -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0">Histórico de E-mails</h3>
                        <?php if(!empty($email_logs)): ?>
                            <span class="badge bg-primary" aria-label="Total de e-mails"><?= count($email_logs) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if(empty($email_logs)): ?>
                            <div class="text-center text-muted py-3">
                                <i class="ti ti-mail-off fs-1 mb-2 d-block"></i>
                                <p class="mb-0">Nenhum e-mail registrado para este orçamento.</p>
                                <small>Envie o orçamento ou altere o status para gerar registros.</small>
                            </div>
                        <?php else: ?>
                            <?php
                                $evento_labels = [
                                    'novo_orcamento_cliente' => 'Novo orçamento - Cliente',
                                    'novo_orcamento_empresa' => 'Novo orçamento - Empresa',
                                    'pendente' => 'Pendente',
                                    'em_analise' => 'Em análise',
                                    'aprovado' => 'Aprovado',
                                    'recusado' => 'Recusado',
                                    'cancelado' => 'Cancelado',
                                    'em_preparacao' => 'Em preparação',
                                    'pedido_postado' => 'Pedido postado'
                                ];
                            ?>
                            <div class="list-group email-log-list" role="list">
                                <?php foreach($email_logs as $log): ?>
                                    <?php
                                        $status_badge = $log->status === 'sucesso' ? 'success' : 'danger';
                                        $status_icon = $log->status === 'sucesso' ? 'ti ti-circle-check' : 'ti ti-alert-triangle';
                                        $tipo_legivel = $log->tipo;

                                        if (strpos($log->tipo, 'status_') === 0) {
                                            $tipo_chave = substr($log->tipo, strlen('status_'));
                                            $tipo_legivel = $evento_labels[$tipo_chave] ?? ucwords(str_replace('_', ' ', $tipo_chave));
                                        } else {
                                            $tipo_legivel = $evento_labels[$log->tipo] ?? ucwords(str_replace('_', ' ', $log->tipo));
                                        }
                                    ?>
                                    <div class="list-group-item list-group-item-action mb-2 rounded" role="listitem">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="text-muted"><i class="ti ti-mail"></i></span>
                                            <span class="fw-semibold">Para:</span>
                                            <span><?= html_escape($log->destinatario) ?></span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div>
                                                <span class="fw-semibold">Status do envio:</span>
                                                <span class="badge bg-<?= $status_badge ?> ms-1">
                                                    <i class="<?= $status_icon ?> me-1"></i>
                                                    <?= ucfirst($log->status) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">Data e hora:</span>
                                                <small class="text-muted ms-1"><?= date('d/m/Y H:i', strtotime($log->criado_em)) ?></small>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="fw-semibold">E-mail enviado:</span>
                                            <div class="badge bg-<?= $status_badge ?> ms-1"><i class="ti ti-mail"></i> <?= html_escape($tipo_legivel) ?></div>
                                            <?php if($log->erro): ?>
                                                <div class="alert alert-danger mt-2 mb-0" role="alert">
                                                    <small><strong>Erro:</strong> <?= html_escape($log->erro) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
