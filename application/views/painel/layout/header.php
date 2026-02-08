<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= $titulo ?? 'Painel - ' . ($estabelecimento->nome_fantasia ?? 'AgendaPro') ?></title>

    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>

    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="<?= base_url('painel/dashboard') ?>">
                        <?= exibir_logo('navbar-brand-image', 'height: 32px;') ?>
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <?php if (!empty($estabelecimento->logo)): ?>
                                <span class="avatar avatar-sm" style="background-image: url(<?= base_url('assets/uploads/' . $estabelecimento->logo) ?>)"></span>
                            <?php else: ?>
                                <span class="avatar avatar-sm"><?= substr($this->session->userdata('usuario_nome'), 0, 2) ?></span>
                            <?php endif; ?>
                            <div class="d-none d-xl-block ps-2">
                                <div><?= $this->session->userdata('usuario_nome') ?></div>
                                <div class="mt-1 small text-muted">Estabelecimento</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="<?= base_url('painel/perfil') ?>" class="dropdown-item">
                                <i class="ti ti-user-circle me-2"></i>Perfil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('logout') ?>" class="dropdown-item">
                                <i class="ti ti-logout me-2"></i>Sair
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar navbar-light">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item <?= ($menu_ativo ?? '') == 'dashboard' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('painel/dashboard') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-home"></i>
                                    </span>
                                    <span class="nav-link-title">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item <?= ($menu_ativo ?? '') == 'clientes' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('painel/clientes') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-users"></i>
                                    </span>
                                    <span class="nav-link-title">Clientes</span>
                                </a>
                            </li>
                            <li class="nav-item <?= ($menu_ativo ?? '') == 'profissionais' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('painel/profissionais') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-user-check"></i>
                                    </span>
                                    <span class="nav-link-title">Profissionais</span>
                                </a>
                            </li>
                            <li class="nav-item <?= ($menu_ativo ?? '') == 'servicos' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('painel/servicos') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-briefcase"></i>
                                    </span>
                                    <span class="nav-link-title">Serviços</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown <?= in_array(($menu_ativo ?? ''), ['agendamentos', 'bloqueios', 'feriados']) ? 'active' : '' ?>">
                                <a class="nav-link dropdown-toggle" href="#navbar-agendamentos" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-calendar"></i>
                                    </span>
                                    <span class="nav-link-title">Agendamentos</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?= base_url('painel/agendamentos') ?>">
                                        <i class="ti ti-list me-2"></i>
                                        Ver Agendamentos
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('painel/bloqueios') ?>">
                                        <i class="ti ti-lock me-2"></i>
                                        Bloqueios
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('painel/feriados') ?>">
                                        <i class="ti ti-calendar-off me-2"></i>
                                        Feriados
                                    </a>
                                </div>
                            </li>
                             <li class="nav-item <?= ($menu_ativo ?? '') == 'relatorios' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('painel/relatorios') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-chart-bar"></i>
                                    </span>
                                    <span class="nav-link-title">Relatórios</span>
                                </a>
                            </li>
                            <li class="nav-item <?= ($menu_ativo ?? '') == 'configuracoes' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url('painel/configuracoes') ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-settings"></i>
                                    </span>
                                    <span class="nav-link-title">Configurações</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
