<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - AgendaPro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark">
                    <h1>AgendaPro</h1>
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body text-center py-4 p-sm-5">
                    <i class="ti ti-alert-circle icon mb-2 text-warning" style="font-size: 5rem;"></i>
                    <h1 class="mt-3">Assinatura Expirada</h1>
                    <p class="text-muted">
                        Sua assinatura expirou e o acesso ao sistema está temporariamente suspenso.
                    </p>

                    <?php if (isset($assinatura)): ?>
                    <div class="alert alert-warning mt-4">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-info-circle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Informações da Assinatura</h4>
                                <div class="text-muted">
                                    <strong>Plano:</strong> <?= ucfirst($assinatura->plano_nome ?? 'N/A') ?><br>
                                    <strong>Vencimento:</strong> <?= date('d/m/Y', strtotime($assinatura->data_fim)) ?><br>
                                    <strong>Status:</strong> <?= ucfirst($assinatura->status) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <h3>O que fazer?</h3>
                        <div class="text-start mt-3">
                            <div class="mb-3">
                                <i class="ti ti-check text-success me-2"></i>
                                <strong>Renove sua assinatura</strong> para continuar usando o sistema
                            </div>
                            <div class="mb-3">
                                <i class="ti ti-check text-success me-2"></i>
                                <strong>Entre em contato</strong> com nosso suporte para mais informações
                            </div>
                            <div class="mb-3">
                                <i class="ti ti-check text-success me-2"></i>
                                <strong>Seus dados estão seguros</strong> e serão mantidos por 30 dias
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="mailto:suporte@agendapro.com.br" class="btn btn-primary">
                            <i class="ti ti-mail me-2"></i>
                            Entrar em Contato
                        </a>
                        <a href="<?= base_url('login/sair') ?>" class="btn btn-outline-secondary">
                            <i class="ti ti-logout me-2"></i>
                            Sair
                        </a>
                    </div>
                </div>
            </div>
            <div class="text-center text-muted mt-3">
                <small>© <?= date('Y') ?> AgendaPro. Todos os direitos reservados.</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>
