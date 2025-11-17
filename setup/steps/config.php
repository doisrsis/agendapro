<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = preg_replace('/\/setup.*$/', '', $_SERVER['REQUEST_URI']);
$auto_base_url = $protocol . '://' . $host . rtrim($uri, '/') . '/';
$saved_config = $_SESSION['install_config'] ?? [];
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="ti ti-settings me-2"></i>Configuração do Sistema</h3>
    </div>
    <form method="POST" action="?step=3">
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="ti ti-info-circle me-2"></i>
                Configure as informações básicas do sistema.
            </div>

            <div class="mb-3">
                <label class="form-label required">URL Base do Sistema</label>
                <input type="url" class="form-control" name="base_url"
                       value="<?= $saved_config['base_url'] ?? $auto_base_url ?>" required>
                <small class="form-hint">URL completa onde o sistema está instalado (com / no final)</small>
            </div>

            <div class="alert alert-success">
                <i class="ti ti-check me-2"></i>
                <strong>Detectado automaticamente:</strong> <code><?= $auto_base_url ?></code>
            </div>

            <div class="alert alert-warning">
                <i class="ti ti-lock me-2"></i>
                <strong>Segurança:</strong> Uma chave de criptografia será gerada automaticamente.
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="?step=2" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i>Voltar
            </a>
            <button type="submit" class="btn btn-primary btn-install">
                Próximo: Criar Administrador<i class="ti ti-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>
