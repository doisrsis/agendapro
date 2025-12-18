<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - AgendaPro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body class="d-flex flex-column bg-light">
    <div class="page page-center">
        <div class="container-xl py-5" style="padding-top: 30rem !important;">
            <div class="text-center mb-5 mt-4">
                <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark">
                    <h1 class="mb-0">AgendaPro</h1>
                </a>
            </div>

            <!-- Alert Principal -->
            <div class="card card-md mb-4">
                <div class="card-body text-center py-4">
                    <i class="ti ti-alert-circle icon mb-2 text-warning" style="font-size: 4rem;"></i>
                    <h1 class="mt-3">Assinatura Expirada</h1>
                    <p class="text-muted">
                        Sua assinatura expirou e o acesso ao sistema está temporariamente suspenso.
                    </p>

                    <?php if (isset($assinatura)): ?>
                        <?php
                        // Converter array para objeto se necessário
                        if (is_array($assinatura)) {
                            $assinatura = (object) $assinatura;
                        }
                        ?>
                    <div class="alert alert-warning mt-4 text-start">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-info-circle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Informações da Assinatura</h4>
                                <div class="text-muted">
                                    <strong>Plano:</strong> <?= ucfirst($assinatura->plano_nome ?? 'N/A') ?><br>
                                    <strong>Vencimento:</strong>
                                    <?php if (!empty($assinatura->data_fim)): ?>
                                        <?= date('d/m/Y', strtotime($assinatura->data_fim)) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                    <br>
                                    <strong>Status:</strong> <?= ucfirst($assinatura->status ?? 'N/A') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Seção de Planos -->
            <?php if (!empty($planos)): ?>
            <div class="mb-4">
                <h2 class="text-center mb-2">
                    <?php if (isset($is_trial) && $is_trial): ?>
                        Escolha um Plano para Começar
                    <?php else: ?>
                        Renove sua Assinatura
                    <?php endif; ?>
                </h2>
                <p class="text-center text-muted mb-4">
                    Escolha o plano ideal para o seu negócio e continue aproveitando todos os recursos
                </p>

                <div class="row g-4">
                    <?php foreach ($planos as $plano): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column p-4">
                                <div class="text-center mb-4">
                                    <h3 class="card-title mb-2"><?= $plano->nome ?></h3>

                                    <?php if (isset($assinatura) && $plano->id == $assinatura->plano_id): ?>
                                    <span class="badge bg-blue mb-3" style="color: #fff !important;">Seu Plano Atual</span>
                                    <?php endif; ?>

                                    <div class="display-4 fw-bold my-3">
                                        R$ <?= number_format($plano->valor_mensal, 2, ',', '.') ?>
                                    </div>
                                    <div class="text-muted">por mês</div>
                                </div>

                                <p class="text-muted text-center mb-4"><?= $plano->descricao ?></p>

                                <!-- Recursos -->
                                <ul class="list-unstyled mb-4 flex-grow-1">
                                    <li class="mb-3">
                                        <i class="ti ti-users text-primary me-2"></i>
                                        <strong><?= $plano->max_profissionais == -1 ? 'Ilimitados' : $plano->max_profissionais ?></strong> profissionais
                                    </li>
                                    <li class="mb-3">
                                        <i class="ti ti-calendar text-primary me-2"></i>
                                        <strong><?= $plano->max_agendamentos_mes == -1 ? 'Ilimitados' : number_format($plano->max_agendamentos_mes, 0, ',', '.') ?></strong> agendamentos/mês
                                    </li>
                                    <?php if ($plano->trial_dias > 0): ?>
                                    <li class="mb-3">
                                        <i class="ti ti-gift text-success me-2"></i>
                                        <strong><?= $plano->trial_dias ?> dias</strong> de teste grátis
                                    </li>
                                    <?php endif; ?>
                                </ul>

                                <a href="<?= base_url('painel/checkout/' . $plano->slug) ?>"
                                   class="btn btn-primary btn-lg w-100">
                                    <i class="ti ti-shopping-cart me-2"></i>
                                    Escolher Plano
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= base_url('planos/comparar') ?>" class="btn btn-link btn-lg">
                        <i class="ti ti-table me-2"></i>
                        Comparar Todos os Planos
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Informações Adicionais -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-3">O que fazer?</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="ti ti-check text-success me-2 fs-3"></i>
                                <div>
                                    <strong>Renove sua assinatura</strong>
                                    <p class="text-muted small mb-0">Continue usando o sistema</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="ti ti-check text-success me-2 fs-3"></i>
                                <div>
                                    <strong>Entre em contato</strong>
                                    <p class="text-muted small mb-0">Nosso suporte está disponível</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="ti ti-check text-success me-2 fs-3"></i>
                                <div>
                                    <strong>Seus dados estão seguros</strong>
                                    <p class="text-muted small mb-0">Mantidos por 30 dias</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="text-center mb-4">
                <a href="mailto:suporte@agendapro.com.br" class="btn btn-primary btn-lg me-2">
                    <i class="ti ti-mail me-2"></i>
                    Entrar em Contato
                </a>
                <a href="<?= base_url('login/logout') ?>" class="btn btn-outline-secondary btn-lg">
                    <i class="ti ti-logout me-2"></i>
                    Sair
                </a>
            </div>

            <div class="text-center text-muted">
                <small>© <?= date('Y') ?> AgendaPro. Todos os direitos reservados.</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>
