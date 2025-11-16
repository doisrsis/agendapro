<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= $titulo ?? 'Dashboard Administrativo' ?></title>

    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet"/>

    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        /* Forçar tema claro */
        body {
            background-color: #f5f7fb !important;
        }
        .page {
            background-color: #f5f7fb !important;
        }
        .card {
            background-color: #ffffff !important;
            color: #1e293b !important;
        }
        .card-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e6e7e9 !important;
        }
        .text-secondary {
            color: #626976 !important;
        }
        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e6e7e9 !important;
        }
    </style>
</head>
<body data-bs-theme="light">
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="<?= base_url('admin') ?>" class="d-flex align-items-center">
                        <i class="ti ti-layout-dashboard me-2" style="font-size: 24px;"></i>
                        <span>Dashboard</span>
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <!-- Notificações -->
                    <div class="nav-item dropdown d-none d-md-flex me-3">
                        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Mostrar notificações">
                            <i class="ti ti-bell"></i>
                            <span class="badge bg-red"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Últimas notificações</h3>
                                </div>
                                <div class="list-group list-group-flush list-group-hoverable">
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span></div>
                                            <div class="col text-truncate">
                                                <a href="#" class="text-body d-block">Novo orçamento recebido</a>
                                                <div class="d-block text-secondary text-truncate mt-n1">
                                                    há 2 minutos
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Perfil do usuário -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menu do usuário">
                            <span class="avatar avatar-sm" style="background-image: url(<?= $usuario_logado->avatar ?? base_url('assets/img/avatar-default.png') ?>)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div><?= $usuario_logado->nome ?></div>
                                <div class="mt-1 small text-secondary"><?= ucfirst($usuario_logado->nivel) ?></div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="<?= base_url('admin/perfil') ?>" class="dropdown-item">
                                <i class="ti ti-user me-2"></i> Meu Perfil
                            </a>
                            <a href="<?= base_url('admin/configuracoes') ?>" class="dropdown-item">
                                <i class="ti ti-settings me-2"></i> Configurações
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('logout') ?>" class="dropdown-item">
                                <i class="ti ti-logout me-2"></i> Sair
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Menu Horizontal -->
        <header class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item <?= $menu_ativo == 'dashboard' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-home"></i>
                                    </span>
                                    <span class="nav-link-title">Dashboard</span>
                                </a>
                            </li>

                            <?php if ($usuario_logado->nivel == 'admin'): ?>
                            <li class="nav-item <?= $menu_ativo == 'usuarios' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('admin/usuarios') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-users"></i>
                                    </span>
                                    <span class="nav-link-title">Usuários</span>
                                </a>
                            </li>

                            <li class="nav-item <?= $menu_ativo == 'configuracoes' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('admin/configuracoes') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-settings"></i>
                                    </span>
                                    <span class="nav-link-title">Configurações</span>
                                </a>
                            </li>

                            <li class="nav-item <?= $menu_ativo == 'logs' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('admin/logs') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-history"></i>
                                    </span>
                                    <span class="nav-link-title">Logs</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
