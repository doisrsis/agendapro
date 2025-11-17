<?php
$errors = [];
$success = false;

if (!isset($_SESSION['install_db']) || !isset($_SESSION['install_config']) || !isset($_SESSION['install_admin'])) {
    header('Location: ?step=1');
    exit;
}

$db = $_SESSION['install_db'];
$config = $_SESSION['install_config'];
$admin = $_SESSION['install_admin'];

try {
    // 1. Conectar ao banco
    $mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

    if ($mysqli->connect_error) {
        throw new Exception('Erro ao conectar: ' . $mysqli->connect_error);
    }

    $mysqli->set_charset('utf8mb4');

    // 2. LIMPAR BANCO ANTES DE IMPORTAR (evitar duplicação)
    $tables = ['usuarios', 'configuracoes', 'notificacoes', 'logs', 'usuario_permissoes'];
    foreach ($tables as $table) {
        $mysqli->query("DROP TABLE IF EXISTS `$table`");
    }

    // 3. Importar SQL
    $sql_file = '../docs/projeto_base_database.sql';
    if (!file_exists($sql_file)) {
        throw new Exception('Arquivo SQL não encontrado: ' . $sql_file);
    }

    $sql = file_get_contents($sql_file);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if (!$mysqli->query($statement)) {
                throw new Exception('Erro SQL: ' . $mysqli->error);
            }
        }
    }

    // 4. Criar admin (agora o banco está limpo, será ID 1)
    $senha_hash = password_hash($admin['senha'], PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO usuarios (nome, email, senha, nivel, status) VALUES (?, ?, ?, 'admin', 'ativo')");
    $stmt->bind_param('sss', $admin['nome'], $admin['email'], $senha_hash);
    $stmt->execute();

    // 5. Salvar nome do sistema
    $stmt = $mysqli->prepare("INSERT INTO configuracoes (chave, valor, grupo, tipo) VALUES ('sistema_nome', ?, 'geral', 'texto') ON DUPLICATE KEY UPDATE valor = ?");
    $stmt->bind_param('ss', $admin['sistema_nome'], $admin['sistema_nome']);
    $stmt->execute();

    $mysqli->close();

    // 6. Criar database.php
    $database_content = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\$active_group = 'default';
\$query_builder = TRUE;

\$db['default'] = array(
    'dsn'   => '',
    'hostname' => '{$db['hostname']}',
    'username' => '{$db['username']}',
    'password' => '{$db['password']}',
    'database' => '{$db['database']}',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
";

    file_put_contents('../application/config/database.php', $database_content);

    // 7. Atualizar config.php
    $config_file = '../application/config/config.php';
    $config_content = file_get_contents($config_file);

    $config_content = preg_replace(
        "/\\\$config\['base_url'\] = '.*?';/",
        "\$config['base_url'] = '{$config['base_url']}';",
        $config_content
    );

    $config_content = preg_replace(
        "/\\\$config\['encryption_key'\] = '.*?';/",
        "\$config['encryption_key'] = '{$config['encryption_key']}';",
        $config_content
    );

    file_put_contents($config_file, $config_content);

    // 8. ATUALIZAR .htaccess COM REWRITEBASE CORRETO
    $htaccess_file = '../.htaccess';
    if (file_exists($htaccess_file) && !empty($config['project_folder'])) {
        $htaccess_content = file_get_contents($htaccess_file);

        // Atualizar RewriteBase
        $htaccess_content = preg_replace(
            "/RewriteBase\s+\/.*?\n/",
            "RewriteBase /{$config['project_folder']}/\n",
            $htaccess_content
        );

        file_put_contents($htaccess_file, $htaccess_content);
    }

    $success = true;
    session_destroy();

} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
?>

<div class="card">
    <div class="card-body">
        <?php if ($success): ?>
        <div class="install-success">
            <i class="ti ti-circle-check"></i>
            <h2 class="text-success mt-3">✅ Instalação Concluída!</h2>
            <p class="text-muted">O sistema foi instalado e está pronto para uso.</p>
        </div>

        <div class="alert alert-success">
            <h4 class="alert-title"><i class="ti ti-check me-2"></i>Configurações Salvas</h4>
            <ul class="mb-0">
                <li>✅ Banco de dados limpo e configurado</li>
                <li>✅ Tabelas criadas</li>
                <li>✅ Administrador criado (ID: 1)</li>
                <li>✅ Sistema: <strong><?= htmlspecialchars($admin['sistema_nome']) ?></strong></li>
                <li>✅ Arquivos de configuração atualizados</li>
                <?php if (!empty($config['project_folder'])): ?>
                <li>✅ .htaccess atualizado (RewriteBase: /<?= htmlspecialchars($config['project_folder']) ?>/)</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card bg-light">
            <div class="card-body">
                <h4><i class="ti ti-key me-2"></i>Suas Credenciais de Acesso</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>E-mail:</strong><br>
                        <code><?= htmlspecialchars($admin['email']) ?></code>
                    </div>
                    <div class="col-md-6">
                        <strong>Senha:</strong><br>
                        <code>••••••••</code> (a que você definiu)
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning mt-3">
            <i class="ti ti-alert-triangle me-2"></i>
            <strong>Importante:</strong> A pasta <code>setup/</code> será removida automaticamente ao acessar o sistema.
        </div>

        <div class="mt-4 text-center">
            <a href="../" class="btn btn-primary btn-lg btn-install">
                <i class="ti ti-login me-2"></i>
                Acessar o Sistema
            </a>
        </div>

        <?php else: ?>
        <div class="alert alert-danger">
            <h4 class="alert-title"><i class="ti ti-alert-circle me-2"></i>Erro na Instalação</h4>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="mt-3 text-center">
            <a href="?step=1" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i>Tentar Novamente
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
