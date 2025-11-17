<?php
$requirements = [];

// PHP Version
$php_version = phpversion();
$requirements[] = [
    'name' => 'PHP Version',
    'required' => '>= 7.4',
    'current' => $php_version,
    'status' => version_compare($php_version, '7.4.0', '>=') ? 'success' : 'error'
];

// Extensions
$extensions = ['mysqli' => 'MySQLi', 'mbstring' => 'MBString', 'json' => 'JSON', 'openssl' => 'OpenSSL'];
foreach ($extensions as $ext => $name) {
    $requirements[] = [
        'name' => $name . ' Extension',
        'required' => 'Habilitada',
        'current' => extension_loaded($ext) ? 'Habilitada' : 'Desabilitada',
        'status' => extension_loaded($ext) ? 'success' : ($ext == 'openssl' ? 'warning' : 'error')
    ];
}

// Writable dirs
$dirs = ['../application/cache' => 'Cache', '../application/logs' => 'Logs', '../application/config' => 'Config', '../assets/img/logo' => 'Logo'];
foreach ($dirs as $dir => $name) {
    $requirements[] = [
        'name' => $name . ' Directory',
        'required' => 'Escrita permitida',
        'current' => is_writable($dir) ? 'Escrita permitida' : 'Sem permissão',
        'status' => is_writable($dir) ? 'success' : 'error'
    ];
}

$can_proceed = true;
foreach ($requirements as $req) {
    if ($req['status'] === 'error') {
        $can_proceed = false;
        break;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="ti ti-checklist me-2"></i>Verificação de Requisitos</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-3">
            <i class="ti ti-info-circle me-2"></i>
            Verificando se o servidor atende aos requisitos mínimos.
        </div>

        <?php foreach ($requirements as $req): ?>
        <div class="requirement-item <?= $req['status'] ?>">
            <div class="row align-items-center">
                <div class="col">
                    <strong><?= $req['name'] ?></strong>
                    <div class="text-muted small">
                        Requerido: <?= $req['required'] ?> | Atual: <?= $req['current'] ?>
                    </div>
                </div>
                <div class="col-auto">
                    <?php if ($req['status'] === 'success'): ?>
                        <i class="ti ti-check text-success" style="font-size: 1.5rem;"></i>
                    <?php elseif ($req['status'] === 'warning'): ?>
                        <i class="ti ti-alert-triangle text-warning" style="font-size: 1.5rem;"></i>
                    <?php else: ?>
                        <i class="ti ti-x text-danger" style="font-size: 1.5rem;"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (!$can_proceed): ?>
        <div class="alert alert-danger mt-3">
            <i class="ti ti-alert-circle me-2"></i>
            <strong>Atenção!</strong> Alguns requisitos não foram atendidos.
        </div>
        <?php endif; ?>
    </div>
    <div class="card-footer text-end">
        <a href="?step=2" class="btn btn-primary btn-install <?= !$can_proceed ? 'disabled' : '' ?>">
            Próximo: Banco de Dados
            <i class="ti ti-arrow-right ms-2"></i>
        </a>
    </div>
</div>
