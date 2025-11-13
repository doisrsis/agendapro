<?php
$is_edit = isset($extra);
$titulo_form = $is_edit ? 'Editar Extra' : 'Novo Extra';
$action = $is_edit ? base_url('admin/extras/editar/' . $extra->id) : base_url('admin/extras/criar');
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gerenciamento</div>
                <h2 class="page-title"><?= $titulo_form ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/extras') ?>" class="btn btn-secondary">
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
                            <h3 class="card-title">Informações do Extra</h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="mb-3">
                                <label class="form-label required">Nome</label>
                                <input type="text" name="nome" class="form-control" 
                                       value="<?= $is_edit ? $extra->nome : set_value('nome') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"><?= $is_edit ? $extra->descricao : set_value('descricao') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Tipo de Preço</label>
                                        <select name="tipo_preco" class="form-select" required>
                                            <option value="">Selecione...</option>
                                            <option value="fixo" <?= ($is_edit && $extra->tipo_preco === 'fixo') ? 'selected' : '' ?>>Fixo (R$)</option>
                                            <option value="percentual" <?= ($is_edit && $extra->tipo_preco === 'percentual') ? 'selected' : '' ?>>Percentual (%)</option>
                                            <option value="por_m2" <?= ($is_edit && $extra->tipo_preco === 'por_m2') ? 'selected' : '' ?>>Por m²</option>
                                        </select>
                                        <small class="form-hint">Como o valor será calculado</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Valor</label>
                                        <input type="number" name="valor" class="form-control" step="0.01" 
                                               value="<?= $is_edit ? $extra->valor : set_value('valor') ?>" required>
                                        <small class="form-hint">Valor em reais ou percentual</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Produtos Aplicáveis -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Produtos Aplicáveis</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Selecione os produtos onde este extra pode ser aplicado. Se nenhum for selecionado, será aplicável a todos.</p>
                            
                            <div class="row">
                                <?php foreach($produtos as $produto): ?>
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input type="checkbox" name="produtos_aplicaveis[]" class="form-check-input" 
                                                   value="<?= $produto->id ?>"
                                                   <?= ($is_edit && in_array($produto->id, $produtos_aplicaveis)) ? 'checked' : '' ?>>
                                            <span class="form-check-label"><?= $produto->nome ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configurações</h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="mb-3">
                                <label class="form-label">Ordem</label>
                                <input type="number" name="ordem" class="form-control" 
                                       value="<?= $is_edit ? $extra->ordem : '0' ?>">
                                <small class="form-hint">Ordem de exibição</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="ativo" <?= ($is_edit && $extra->status === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                                    <option value="inativo" <?= ($is_edit && $extra->status === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                                </select>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy"></i> Salvar Extra
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
