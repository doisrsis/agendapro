<?php
$is_edit = isset($preco);
$titulo_form = $is_edit ? 'Editar Preço' : 'Novo Preço';
$action = $is_edit ? base_url('admin/precos/editar/' . $preco->id) : base_url('admin/precos/criar');
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gerenciamento</div>
                <h2 class="page-title"><?= $titulo_form ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/precos') ?>" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <?php if(validation_errors()): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= validation_errors() ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= $action ?>">
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Faixa de Dimensões e Preços</h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="mb-3">
                                <label class="form-label required">Produto</label>
                                <select name="produto_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($produtos as $produto): ?>
                                        <option value="<?= $produto->id ?>" 
                                                <?= ($is_edit && $preco->produto_id == $produto->id) ? 'selected' : '' ?>>
                                            <?= $produto->nome ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h4 class="card-title">Largura (metros)</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label required">Mínima</label>
                                                    <input type="number" name="largura_min" class="form-control" step="0.01" 
                                                           value="<?= $is_edit ? $preco->largura_min : '' ?>" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label required">Máxima</label>
                                                    <input type="number" name="largura_max" class="form-control" step="0.01" 
                                                           value="<?= $is_edit ? $preco->largura_max : '' ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h4 class="card-title">Altura (metros)</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label required">Mínima</label>
                                                    <input type="number" name="altura_min" class="form-control" step="0.01" 
                                                           value="<?= $is_edit ? $preco->altura_min : '' ?>" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label required">Máxima</label>
                                                    <input type="number" name="altura_max" class="form-control" step="0.01" 
                                                           value="<?= $is_edit ? $preco->altura_max : '' ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="ti ti-info-circle"></i>
                                <strong>Importante:</strong> Preencha pelo menos um dos tipos de preço abaixo.
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Preço por m²</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" name="preco_m2" class="form-control" step="0.01" 
                                                   value="<?= $is_edit ? $preco->preco_m2 : '' ?>">
                                        </div>
                                        <small class="form-hint">Preço por metro quadrado</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Preço por ml</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" name="preco_ml" class="form-control" step="0.01" 
                                                   value="<?= $is_edit ? $preco->preco_ml : '' ?>">
                                        </div>
                                        <small class="form-hint">Preço por metro linear</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Preço Fixo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" name="preco_fixo" class="form-control" step="0.01" 
                                                   value="<?= $is_edit ? $preco->preco_fixo : '' ?>">
                                        </div>
                                        <small class="form-hint">Preço fixo (unidade)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observações</label>
                                <textarea name="observacoes" class="form-control" rows="3"><?= $is_edit ? $preco->observacoes : '' ?></textarea>
                                <small class="form-hint">Informações adicionais sobre esta faixa de preço</small>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i> Salvar Preço
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ajuda</h3>
                        </div>
                        <div class="card-body">
                            <h4>Como funciona?</h4>
                            <p class="text-muted">Defina faixas de dimensões (largura x altura) e seus respectivos preços.</p>
                            
                            <h4 class="mt-3">Tipos de Preço:</h4>
                            <ul class="text-muted">
                                <li><strong>Por m²:</strong> Multiplica área pela taxa</li>
                                <li><strong>Por ml:</strong> Multiplica largura pela taxa</li>
                                <li><strong>Fixo:</strong> Valor único independente do tamanho</li>
                            </ul>

                            <div class="alert alert-warning mt-3">
                                <i class="ti ti-alert-triangle"></i>
                                O sistema usará a primeira faixa que corresponder às dimensões solicitadas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
