<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Erro' ?></title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .error-card {
            max-width: 450px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <div class="container">
        <div class="error-card">
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">

                    <div class="mb-4">
                        <span class="avatar avatar-xl bg-danger-lt">
                            <i class="ti ti-alert-circle icon-lg text-danger"></i>
                        </span>
                    </div>

                    <h2 class="mb-3"><?= $titulo ?? 'Erro' ?></h2>

                    <p class="text-muted mb-4">
                        <?= $mensagem ?? 'Ocorreu um erro ao processar sua solicitação.' ?>
                    </p>

                    <?php if (isset($agendamento)): ?>
                    <div class="alert alert-secondary text-start mb-4">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Serviço</small>
                                <div><?= $agendamento->servico_nome ?? '-' ?></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Data</small>
                                <div><?= isset($agendamento->data) ? date('d/m/Y', strtotime($agendamento->data)) : '-' ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        Entre em contato com o estabelecimento para mais informações.
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
