<!-- Similar ao criar.php mas com dados preenchidos -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><a href="<?= base_url('admin/usuarios') ?>">Usuários</a></div>
                <h2 class="page-title"><i class="ti ti-edit me-2"></i>Editar Usuário</h2>
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
        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible">
            <div class="d-flex"><div><i class="ti ti-alert-circle icon alert-icon"></i></div>
            <div><?= $this->session->flashdata('erro') ?></div></div>
            <a class="btn-close" data-bs-dismiss="alert"></a>
        </div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title">Dados do Usuário</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label required">Nome Completo</label>
                                    <input type="text" class="form-control" name="nome" value="<?= $usuario->nome ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control" name="telefone" value="<?= $usuario->telefone ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">E-mail</label>
                                <input type="email" class="form-control" name="email" value="<?= $usuario->email ?>" required>
                            </div>
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                Deixe os campos de senha em branco para manter a senha atual.
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" name="senha" minlength="6">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" name="confirmar_senha">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3" id="permissoesCard" style="display:<?= $usuario->nivel == 'usuario' ? 'block' : 'none' ?>;">
                        <div class="card-header"><h3 class="card-title">Permissões por Módulo</h3></div>
                        <div class="card-body">
                            <?php foreach ($modulos as $modulo_key => $modulo): ?>
                            <div class="mb-3">
                                <label class="form-label"><i class="ti <?= $modulo['icone'] ?> me-2"></i><?= $modulo['nome'] ?></label>
                                <div class="row">
                                    <?php foreach ($modulo['acoes'] as $acao): ?>
                                    <div class="col-auto">
                                        <label class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   name="permissoes[<?= $modulo_key ?>][<?= $acao ?>]" value="1"
                                                   <?= isset($permissoes[$modulo_key][$acao]) && $permissoes[$modulo_key][$acao] ? 'checked' : '' ?>>
                                            <span class="form-check-label"><?= ucfirst($acao) ?></span>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <hr>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title">Configurações</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Nível de Acesso</label>
                                <select name="nivel" id="nivel" class="form-select" required>
                                    <option value="usuario" <?= $usuario->nivel == 'usuario' ? 'selected' : '' ?>>Usuário</option>
                                    <option value="admin" <?= $usuario->nivel == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="ativo" <?= $usuario->status == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                    <option value="inativo" <?= $usuario->status == 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="ti ti-device-floppy me-2"></i>Salvar Alterações
                            </button>
                            <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary w-100">
                                <i class="ti ti-x me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('nivel').addEventListener('change', function() {
    document.getElementById('permissoesCard').style.display = this.value === 'usuario' ? 'block' : 'none';
});
</script>
