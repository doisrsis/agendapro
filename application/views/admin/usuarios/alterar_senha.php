<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><a href="<?= base_url('admin/usuarios') ?>">Usuários</a></div>
                <h2 class="page-title"><i class="ti ti-key me-2"></i>Alterar Senha - <?= $usuario->nome ?></h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if ($this->session->flashdata('erro')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <div class="d-flex"><div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                    <div><?= $this->session->flashdata('erro') ?></div></div>
                    <a class="btn-close" data-bs-dismiss="alert"></a>
                </div>
                <?php endif; ?>

                <form method="post">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Nova Senha</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Nova Senha</label>
                                <input type="password" class="form-control" name="senha" required minlength="6" 
                                       placeholder="Mínimo 6 caracteres">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" name="confirmar_senha" required>
                            </div>
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle me-2"></i>
                                O usuário precisará usar a nova senha no próximo login.
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-link">Cancelar</a>
                                <button type="submit" class="btn btn-primary ms-auto">
                                    <i class="ti ti-key me-2"></i>Alterar Senha
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
