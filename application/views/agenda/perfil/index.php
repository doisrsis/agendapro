<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-user me-2"></i>
                    Meu Perfil
                </h2>
                <div class="text-muted mt-1">Gerencie suas informações pessoais</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-md-8">
                <!-- Dados Pessoais -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Dados Pessoais</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label required">Nome Completo</label>
                                <input type="text" class="form-control" name="nome"
                                       value="<?= set_value('nome', $profissional->nome) ?>" required>
                                <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" name="whatsapp"
                                           value="<?= set_value('whatsapp', $profissional->whatsapp) ?>"
                                           placeholder="(XX) XXXXX-XXXX">
                                    <?= form_error('whatsapp', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">E-mail</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= set_value('email', $profissional->email) ?>" required>
                                    <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alterar Senha -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Alterar Senha</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('agenda/perfil/alterar_senha') ?>">
                            <div class="mb-3">
                                <label class="form-label required">Senha Atual</label>
                                <input type="password" class="form-control" name="senha_atual" required>
                                <?= form_error('senha_atual', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nova Senha</label>
                                    <input type="password" class="form-control" name="senha_nova"
                                           required minlength="6">
                                    <?= form_error('senha_nova', '<div class="invalid-feedback d-block">', '</div>') ?>
                                    <small class="text-muted">Mínimo 6 caracteres</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" name="senha_confirmar" required>
                                    <?= form_error('senha_confirmar', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="ti ti-lock me-2"></i>
                                    Alterar Senha
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Informações -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informações</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                <span class="badge bg-<?= $profissional->status == 'ativo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($profissional->status) ?>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cadastrado em</label>
                            <div><?= date('d/m/Y', strtotime($profissional->criado_em)) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
