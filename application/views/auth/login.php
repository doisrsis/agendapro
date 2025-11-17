<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= $titulo ?? 'Login - ' . get_nome_sistema() ?></title>

    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>

    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        .page {
            background: linear-gradient(135deg, #fff 0%, #a77843ff 100%);
        }
    </style>
</head>
<body class="d-flex flex-column">
    <script src="<?= base_url('assets/js/demo-theme.min.js') ?>"></script>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark">
                    <?= exibir_logo_login() ?>
                </a>
            </div>

            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Acesse sua conta</h2>

                    <?php if ($this->session->flashdata('erro')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-alert-circle"></i>
                            </div>
                            <div>
                                <?= $this->session->flashdata('erro') ?>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('sucesso')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-check"></i>
                            </div>
                            <div>
                                <?= $this->session->flashdata('sucesso') ?>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php endif; ?>

                    <?= form_open('login', ['autocomplete' => 'off', 'novalidate' => '']) ?>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
                                   placeholder="seu@email.com" value="<?= set_value('email', $email_lembrado ?? '') ?>"
                                   autocomplete="off" required>
                            <?php if (form_error('email')): ?>
                                <div class="invalid-feedback"><?= form_error('email') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">
                                Senha
                                <span class="form-label-description">
                                    <a href="<?= base_url('recuperar-senha') ?>">Esqueci minha senha</a>
                                </span>
                            </label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="senha" id="senha" class="form-control <?= form_error('senha') ? 'is-invalid' : '' ?>"
                                       placeholder="Sua senha" autocomplete="off" required>
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Mostrar senha" data-bs-toggle="tooltip" id="toggle-senha">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </span>
                                <?php if (form_error('senha')): ?>
                                    <div class="invalid-feedback"><?= form_error('senha') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" name="lembrar" class="form-check-input" value="1"/>
                                <span class="form-check-label">Lembrar-me neste dispositivo</span>
                            </label>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-login me-2"></i> Entrar
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

            <div class="text-center text-white mt-3">
                <a href="<?= base_url() ?>" class="text-white">
                    <i class="ti ti-arrow-left me-1"></i> Voltar ao site
                </a>
            </div>
        </div>
    </div>

    <!-- Libs JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Toggle mostrar/ocultar senha
        document.getElementById('toggle-senha').addEventListener('click', function(e) {
            e.preventDefault();
            const senha = document.getElementById('senha');
            const icon = this.querySelector('i');

            if (senha.type === 'password') {
                senha.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                senha.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        });
    </script>
</body>
</html>
