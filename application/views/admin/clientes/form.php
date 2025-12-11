<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url(($base_controller ?? 'admin') . '/clientes') ?>">Clientes</a>
                </div>
                <h2 class="page-title">
                    <?= isset($cliente) ? 'Editar Cliente' : 'Novo Cliente' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados Básicos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados Básicos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($estabelecimentos) && !empty($estabelecimentos)): ?>
                            <div class="mb-3">
                                <label class="form-label required">Estabelecimento</label>
                                <select class="form-select" name="estabelecimento_id"
                                        <?= isset($cliente) ? 'disabled' : '' ?> required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estabelecimentos as $est): ?>
                                    <option value="<?= $est->id ?>"
                                            <?= set_select('estabelecimento_id', $est->id, ($cliente->estabelecimento_id ?? '') == $est->id) ?>>
                                        <?= $est->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('estabelecimento_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label required">Nome</label>
                                <input type="text" class="form-control" name="nome"
                                       value="<?= set_value('nome', $cliente->nome ?? '') ?>" required>
                                <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CPF</label>
                                    <input type="text" class="form-control" name="cpf"
                                           value="<?= set_value('cpf', $cliente->cpf ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">E-mail</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= set_value('email', $cliente->email ?? '') ?>">
                                    <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">WhatsApp</label>
                                    <input type="text" class="form-control" name="whatsapp"
                                           value="<?= set_value('whatsapp', $cliente->whatsapp ?? '') ?>" required>
                                    <?= form_error('whatsapp', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control" name="telefone"
                                           value="<?= set_value('telefone', $cliente->telefone ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Foto -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Foto</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if (isset($cliente) && $cliente->foto): ?>
                            <img src="<?= base_url('uploads/clientes/' . $cliente->foto) ?>"
                                 alt="Foto" class="img-fluid mb-3" style="max-height: 200px;">
                            <?php endif; ?>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
                        </div>
                    </div>

                    <!-- Tipo -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Classificação</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-select" name="tipo">
                                <option value="novo" <?= set_select('tipo', 'novo', ($cliente->tipo ?? 'novo') == 'novo') ?>>Novo</option>
                                <option value="recorrente" <?= set_select('tipo', 'recorrente', ($cliente->tipo ?? '') == 'recorrente') ?>>Recorrente</option>
                                <option value="vip" <?= set_select('tipo', 'vip', ($cliente->tipo ?? '') == 'vip') ?>>VIP</option>
                            </select>
                            <small class="text-muted mt-2 d-block">
                                A classificação é atualizada automaticamente baseada no histórico de agendamentos
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url(($base_controller ?? 'admin') . '/clientes') ?>" class="btn btn-link">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>
                        Salvar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
