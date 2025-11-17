<?php $saved_admin = $_SESSION['install_admin'] ?? []; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="ti ti-user-shield me-2"></i>Criar Administrador e Nome do Sistema</h3>
    </div>
    <form method="POST" action="?step=4" id="adminForm">
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="ti ti-info-circle me-2"></i>
                Crie o primeiro usuário administrador e defina o nome do seu sistema.
            </div>

            <h4 class="mb-3"><i class="ti ti-building me-2"></i>Informações do Sistema</h4>
            <div class="mb-4">
                <label class="form-label required">Nome do Sistema</label>
                <input type="text" class="form-control" name="sistema_nome"
                       value="<?= $saved_admin['sistema_nome'] ?? 'Dashboard Administrativo' ?>"
                       placeholder="Meu Sistema" required>
                <small class="form-hint">Nome que aparecerá no sistema (pode ser alterado depois)</small>
            </div>

            <hr class="my-4">

            <h4 class="mb-3"><i class="ti ti-user-shield me-2"></i>Dados do Administrador</h4>
            <div class="mb-3">
                <label class="form-label required">Nome Completo</label>
                <input type="text" class="form-control" name="nome"
                       value="<?= $saved_admin['nome'] ?? '' ?>"
                       placeholder="João da Silva" required>
                <small class="form-hint">Nome do administrador</small>
            </div>

            <div class="mb-3">
                <label class="form-label required">E-mail</label>
                <input type="email" class="form-control" name="email"
                       value="<?= $saved_admin['email'] ?? '' ?>"
                       placeholder="admin@seudominio.com.br" required>
                <small class="form-hint">E-mail para login (use um e-mail válido)</small>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">Senha</label>
                    <input type="password" class="form-control" name="senha"
                           id="senha" placeholder="Mínimo 6 caracteres"
                           minlength="6" required>
                    <small class="form-hint">Mínimo de 6 caracteres</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Confirmar Senha</label>
                    <input type="password" class="form-control" name="senha_confirm"
                           id="senha_confirm" placeholder="Digite a senha novamente"
                           minlength="6" required>
                    <small class="form-hint">Digite a mesma senha</small>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>Importante:</strong> Guarde essas credenciais em local seguro!
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="?step=3" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i>Voltar
            </a>
            <button type="submit" class="btn btn-primary btn-install">
                Finalizar Instalação<i class="ti ti-check ms-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('adminForm').addEventListener('submit', function(e) {
    var senha = document.getElementById('senha').value;
    var senha_confirm = document.getElementById('senha_confirm').value;

    if (senha !== senha_confirm) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        return false;
    }

    if (senha.length < 6) {
        e.preventDefault();
        alert('A senha deve ter no mínimo 6 caracteres!');
        return false;
    }
});
</script>
