<?php
$saved_db = $_SESSION['install_db'] ?? [];
$error = $_SESSION['install_error'] ?? null;
unset($_SESSION['install_error']);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="ti ti-database me-2"></i>Configuração do Banco de Dados</h3>
    </div>
    <form method="POST" action="?step=2">
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="ti ti-info-circle me-2"></i>
                Configure a conexão com o banco de dados MySQL. O banco será criado automaticamente se não existir.
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="ti ti-alert-circle me-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">Host do Banco</label>
                    <input type="text" class="form-control" name="hostname"
                           value="<?= $saved_db['hostname'] ?? 'localhost' ?>" required>
                    <small class="form-hint">Geralmente é "localhost"</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Nome do Banco</label>
                    <input type="text" class="form-control" name="database"
                           value="<?= $saved_db['database'] ?? 'projeto_base' ?>" required>
                    <small class="form-hint">Será criado se não existir</small>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">Usuário</label>
                    <input type="text" class="form-control" name="username"
                           value="<?= $saved_db['username'] ?? 'root' ?>" required>
                    <small class="form-hint">Usuário do MySQL</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Senha</label>
                    <input type="password" class="form-control" name="password"
                           value="<?= $saved_db['password'] ?? '' ?>">
                    <small class="form-hint">Deixe em branco se não tiver senha</small>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>Atenção:</strong> Se o banco já existir, todas as tabelas serão removidas e recriadas.
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="?step=1" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i>Voltar
            </a>
            <button type="submit" class="btn btn-primary btn-install">
                Testar e Continuar<i class="ti ti-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>
