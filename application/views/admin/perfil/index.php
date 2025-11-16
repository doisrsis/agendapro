<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-user-circle me-2"></i>
                    Meu Perfil
                </h2>
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
            <!-- Informações do Usuário -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informações</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar avatar-xl mx-auto mb-3" style="background-color: #667eea; font-size: 2rem;">
                                <?= strtoupper(substr($usuario->nome, 0, 2)) ?>
                            </div>
                            <h3 class="mb-1"><?= $usuario->nome ?></h3>
                            <div class="text-muted mb-2"><?= $usuario->email ?></div>
                            <?php if ($usuario->nivel == 'admin'): ?>
                            <span class="badge bg-red">
                                <i class="ti ti-shield-check me-1"></i>Administrador
                            </span>
                            <?php else: ?>
                            <span class="badge bg-blue">
                                <i class="ti ti-user me-1"></i>Usuário
                            </span>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <div class="list-group list-group-flush">
                            <?php if ($usuario->telefone): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-phone text-muted"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">Telefone</small>
                                        <?= $usuario->telefone ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-calendar text-muted"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">Membro desde</small>
                                        <?= date('d/m/Y', strtotime($usuario->criado_em)) ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($usuario->ultimo_acesso): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="ti ti-clock text-muted"></i>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">Último acesso</small>
                                        <?= date('d/m/Y H:i', strtotime($usuario->ultimo_acesso)) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alterar Senha -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-key me-2"></i>
                            Alterar Senha
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('admin/perfil') ?>">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Dica de segurança:</strong> Use uma senha forte com pelo menos 6 caracteres,
                                combinando letras, números e símbolos.
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Senha Atual</label>
                                <input type="password" class="form-control" name="senha_atual"
                                       required autocomplete="current-password">
                                <small class="form-hint">Digite sua senha atual para confirmar a alteração</small>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nova Senha</label>
                                        <input type="password" class="form-control" name="nova_senha"
                                               required minlength="6" autocomplete="new-password">
                                        <small class="form-hint">Mínimo 6 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" name="confirmar_senha"
                                               required autocomplete="new-password">
                                        <small class="form-hint">Digite a mesma senha novamente</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent mt-3">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary ms-auto">
                                        <i class="ti ti-device-floppy me-2"></i>
                                        Alterar Senha
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informações de Segurança -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-shield-lock me-2"></i>
                            Segurança da Conta
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status da Conta</label>
                                    <div>
                                        <?php if ($usuario->status == 'ativo'): ?>
                                        <span class="badge bg-success-lt fs-4">
                                            <i class="ti ti-check me-1"></i>Ativa
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-danger-lt fs-4">
                                            <i class="ti ti-x me-1"></i>Inativa
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nível de Acesso</label>
                                    <div>
                                        <?php if ($usuario->nivel == 'admin'): ?>
                                        <span class="badge bg-red-lt fs-4">
                                            <i class="ti ti-shield-check me-1"></i>Administrador
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-blue-lt fs-4">
                                            <i class="ti ti-user me-1"></i>Usuário
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <strong>Importante:</strong> Nunca compartilhe sua senha com outras pessoas.
                            Em caso de suspeita de acesso não autorizado, altere sua senha imediatamente.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
