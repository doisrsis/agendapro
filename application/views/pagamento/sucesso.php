<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Confirmado</title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .success-card {
            max-width: 450px;
            margin: 0 auto;
        }
        .checkmark {
            animation: scale-in 0.5s ease-out;
        }
        @keyframes scale-in {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <div class="container">
        <div class="success-card">
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">

                    <div class="mb-4 checkmark">
                        <span class="avatar avatar-xl bg-success-lt">
                            <i class="ti ti-check icon-lg text-success"></i>
                        </span>
                    </div>

                    <h2 class="text-success mb-3">Pagamento Confirmado!</h2>

                    <p class="text-muted mb-4">
                        Seu agendamento foi confirmado com sucesso.
                    </p>

                    <div class="alert alert-success text-start mb-4">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Estabelecimento</small>
                                <div class="fw-bold"><?= $agendamento->estabelecimento_nome ?></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Serviço</small>
                                <div class="fw-bold"><?= $agendamento->servico_nome ?></div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Data</small>
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($agendamento->data)) ?></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Horário</small>
                                <div class="fw-bold"><?= date('H:i', strtotime($agendamento->hora_inicio)) ?></div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Profissional</small>
                                <div class="fw-bold"><?= $agendamento->profissional_nome ?></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Valor Pago</small>
                                <div class="fw-bold text-success">R$ <?= number_format($agendamento->pagamento_valor, 2, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="text-muted">
                        <i class="ti ti-brand-whatsapp me-1 text-success"></i>
                        Você receberá uma confirmação no WhatsApp.
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
