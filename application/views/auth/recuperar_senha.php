<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= $titulo ?? 'Recuperar Senha - ' . get_nome_sistema() ?></title>

    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>

    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        .page {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark">
                    <?= exibir_logo_login() ?>
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Recuperar Senha</h2>
                    <p class="text-muted text-center mb-4">
                        Digite seu e-mail e enviaremos instruções para recuperar sua senha.
                    </p>

                    <?php if ($this->session->flashdata('erro')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                            <div><?= $this->session->flashdata('erro') ?></div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('auth/recuperar_senha') ?>" method="post" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" name="email" placeholder="seu@email.com" required autofocus>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-mail me-2"></i>
                                Enviar Instruções
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-white mt-3">
                <a href="<?= base_url('login') ?>" class="text-white">
                    <i class="ti ti-arrow-left me-1"></i>
                    Voltar para o login
                </a>
            </div>
        </div>
    </div>
    <!-- Tabler Core -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>
