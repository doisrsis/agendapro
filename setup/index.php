<?php
/**
 * Instalador do Projeto Base - Dashboard Administrativo
 *
 * @author Rafael Dias - doisr.com.br
 * @version 1.1.0
 * @date 16/11/2024
 */

// Iniciar sessão
session_start();

// Verificar se já está instalado
$config_file = '../application/config/database.php';
if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);
    if (strpos($config_content, "'database' => ''") === false &&
        strpos($config_content, "'hostname' => 'localhost'") !== false &&
        !isset($_GET['force'])) {
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistema Já Instalado</title>
            <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
            <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
            <style>
                body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            </style>
        </head>
        <body class="d-flex flex-column">
            <div class="page page-center">
                <div class="container-tight py-4">
                    <div class="text-center mb-4">
                        <h1 class="text-white mb-3">✅ Sistema Já Instalado!</h1>
                    </div>
                    <div class="card">
                        <div class="card-body text-center">
                            <p class="mb-3">O sistema já foi instalado anteriormente.</p>
                            <p class="text-muted mb-4">Se deseja reinstalar, delete o arquivo <code>application/config/database.php</code> e tente novamente.</p>
                            <a href="../" class="btn btn-primary">
                                <i class="ti ti-home me-2"></i>
                                Ir para o Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Definir passo atual
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 2:
            // Salvar dados do banco na sessão
            $_SESSION['install_db'] = [
                'hostname' => $_POST['hostname'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'database' => $_POST['database']
            ];

            // Testar conexão
            $mysqli = @new mysqli(
                $_POST['hostname'],
                $_POST['username'],
                $_POST['password']
            );

            if ($mysqli->connect_error) {
                $_SESSION['install_error'] = 'Erro ao conectar: ' . $mysqli->connect_error;
            } else {
                // Tentar criar banco se não existir
                $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$_POST['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $mysqli->close();

                // Próximo passo
                header('Location: ?step=3');
                exit;
            }
            break;

        case 3:
            // Detectar pasta do projeto
            $uri = $_SERVER['REQUEST_URI'];
            $project_folder = '';
            if (preg_match('#/([^/]+)/setup#', $uri, $matches)) {
                $project_folder = $matches[1];
            }

            // Salvar configurações do sistema
            $_SESSION['install_config'] = [
                'base_url' => rtrim($_POST['base_url'], '/') . '/',
                'encryption_key' => bin2hex(random_bytes(16)),
                'project_folder' => $project_folder
            ];
            header('Location: ?step=4');
            exit;

        case 4:
            // Salvar dados do admin
            $_SESSION['install_admin'] = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'senha' => $_POST['senha'],
                'sistema_nome' => $_POST['sistema_nome']
            ];
            header('Location: ?step=5');
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Projeto Base Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container-tight { max-width: 800px; }
        .card { box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); border: none; }
        .step-item { pointer-events: none; }
        .requirement-item {
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
        }
        .requirement-item.success { background: #d4edda; border-left: 4px solid #28a745; }
        .requirement-item.error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .requirement-item.warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .form-label.required::after { content: " *"; color: #dc3545; }
        .btn-install { padding: 0.75rem 2rem; font-weight: 600; }
        .install-success { text-align: center; padding: 2rem; }
        .install-success i { font-size: 4rem; color: #28a745; }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="text-white mb-2">🚀 Instalador</h1>
                <h2 class="h3 text-white">Projeto Base - Dashboard Administrativo</h2>
                <p class="text-white-50">Versão 1.1.0 | Rafael Dias - doisr.com.br</p>
            </div>

            <!-- Progress -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="steps steps-counter steps-lime">
                        <a href="#" class="step-item <?= $step >= 1 ? 'active' : '' ?>">
                            <span class="h4 mb-0">1</span>
                            <span class="text-muted">Requisitos</span>
                        </a>
                        <a href="#" class="step-item <?= $step >= 2 ? 'active' : '' ?>">
                            <span class="h4 mb-0">2</span>
                            <span class="text-muted">Banco de Dados</span>
                        </a>
                        <a href="#" class="step-item <?= $step >= 3 ? 'active' : '' ?>">
                            <span class="h4 mb-0">3</span>
                            <span class="text-muted">Sistema</span>
                        </a>
                        <a href="#" class="step-item <?= $step >= 4 ? 'active' : '' ?>">
                            <span class="h4 mb-0">4</span>
                            <span class="text-muted">Administrador</span>
                        </a>
                        <a href="#" class="step-item <?= $step >= 5 ? 'active' : '' ?>">
                            <span class="h4 mb-0">5</span>
                            <span class="text-muted">Concluir</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <?php
            switch ($step) {
                case 1:
                    include 'steps/requirements.php';
                    break;
                case 2:
                    include 'steps/database.php';
                    break;
                case 3:
                    include 'steps/config.php';
                    break;
                case 4:
                    include 'steps/admin.php';
                    break;
                case 5:
                    include 'steps/finish.php';
                    break;
            }
            ?>

            <!-- Footer -->
            <div class="text-center text-white-50 mt-3">
                <small>
                    Desenvolvido com ❤️ por <a href="https://doisr.com.br" target="_blank" class="text-white">Rafael Dias - doisr.com.br</a>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>
