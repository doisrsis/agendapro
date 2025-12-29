<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .payment-card {
            max-width: 500px;
            margin: 0 auto;
        }
        .qr-code-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
        }
        .countdown {
            font-size: 2rem;
            font-weight: bold;
        }
        .countdown.warning {
            color: #f59f00;
        }
        .countdown.danger {
            color: #d63939;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <div class="container">
        <div class="payment-card">

            <!-- Header com logo do estabelecimento -->
            <div class="text-center mb-4">
                <?php if (!empty($agendamento->estabelecimento_logo)): ?>
                    <img src="<?= base_url('uploads/logos/' . $agendamento->estabelecimento_logo) ?>"
                         alt="<?= $agendamento->estabelecimento_nome ?>"
                         class="mb-3" style="max-height: 80px;">
                <?php endif; ?>
                <h2 class="text-white"><?= $agendamento->estabelecimento_nome ?></h2>
            </div>

            <!-- Card de Pagamento -->
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="card-title mb-0">
                        <i class="ti ti-brand-pix me-2"></i>
                        Pague com PIX
                    </h3>
                </div>

                <div class="card-body text-center">

                    <!-- Informações do Agendamento -->
                    <div class="alert alert-info mb-4">
                        <div class="row text-start">
                            <div class="col-6">
                                <small class="text-muted">Serviço</small>
                                <div class="fw-bold"><?= $agendamento->servico_nome ?></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Profissional</small>
                                <div class="fw-bold"><?= $agendamento->profissional_nome ?></div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row text-start">
                            <div class="col-6">
                                <small class="text-muted">Data</small>
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($agendamento->data)) ?></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Horário</small>
                                <div class="fw-bold"><?= date('H:i', strtotime($agendamento->hora_inicio)) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <?php if ($agendamento->pagamento_pix_qrcode): ?>
                    <div class="mb-4">
                        <p class="text-muted mb-3">Escaneie o QR Code com o app do seu banco</p>
                        <div class="qr-code-container">
                            <img src="data:image/png;base64,<?= $agendamento->pagamento_pix_qrcode ?>"
                                 alt="QR Code PIX"
                                 class="img-fluid"
                                 style="max-width: 250px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Divisor -->
                    <div class="my-4">
                        <div class="text-muted">
                            <hr class="d-inline-block" style="width: 35%;">
                            <span class="px-2">OU</span>
                            <hr class="d-inline-block" style="width: 35%;">
                        </div>
                    </div>

                    <!-- Copia e Cola -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">PIX Copia e Cola</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control font-monospace text-truncate"
                                   id="pix-copia-cola"
                                   readonly
                                   value="<?= $agendamento->pagamento_pix_copia_cola ?>">
                            <button class="btn btn-primary" onclick="copiarPix()">
                                <i class="ti ti-copy"></i> Copiar
                            </button>
                        </div>
                        <small class="text-muted">Cole no app do seu banco para pagar</small>
                    </div>

                    <!-- Valor -->
                    <div class="alert alert-success mb-3">
                        <div class="h2 mb-0">
                            R$ <?= number_format($agendamento->pagamento_valor, 2, ',', '.') ?>
                        </div>
                    </div>

                    <!-- Tempo Restante -->
                    <div class="alert alert-warning mb-3">
                        <i class="ti ti-clock me-2"></i>
                        <strong>Tempo restante:</strong>
                        <div class="countdown" id="countdown"></div>
                    </div>

                    <!-- Status do Pagamento -->
                    <div id="status-pagamento" class="alert alert-secondary">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Aguardando pagamento...
                    </div>

                </div>
            </div>

            <!-- Instruções -->
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title text-center">
                        <i class="ti ti-help me-2"></i>
                        Como pagar
                    </h4>
                    <ol class="mb-0">
                        <li>Abra o app do seu banco</li>
                        <li>Escolha a opção PIX</li>
                        <li>Escaneie o QR Code OU cole o código</li>
                        <li>Confirme o pagamento</li>
                        <li>Aguarde a confirmação automática</li>
                    </ol>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4 text-white-50">
                <small>Pagamento seguro via Mercado Pago</small>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Copiar PIX
    function copiarPix() {
        const input = document.getElementById('pix-copia-cola');
        input.select();
        input.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'PIX Copiado!',
                text: 'Cole no app do seu banco para pagar',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (err) {
            // Fallback para navegadores modernos
            navigator.clipboard.writeText(input.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'PIX Copiado!',
                    text: 'Cole no app do seu banco para pagar',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    }

    // Polling para verificar pagamento
    const token = '<?= $token ?>';
    const intervaloPagamento = setInterval(() => {
        fetch('<?= base_url("pagamento/verificar/") ?>' + token)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'pago') {
                    clearInterval(intervaloPagamento);
                    clearInterval(intervaloTempo);

                    document.getElementById('status-pagamento').innerHTML =
                        '<i class="ti ti-check fs-1 text-success"></i><br><strong>Pagamento confirmado!</strong>';
                    document.getElementById('status-pagamento').className = 'alert alert-success';

                    Swal.fire({
                        icon: 'success',
                        title: 'Pagamento Confirmado!',
                        text: 'Seu agendamento foi confirmado com sucesso. Você receberá uma confirmação no WhatsApp.',
                        confirmButtonText: 'OK'
                    });
                } else if (data.status === 'expirado') {
                    clearInterval(intervaloPagamento);
                    clearInterval(intervaloTempo);

                    document.getElementById('status-pagamento').innerHTML =
                        '<i class="ti ti-x fs-1"></i><br>Tempo esgotado';
                    document.getElementById('status-pagamento').className = 'alert alert-danger';
                }
            })
            .catch(err => console.error('Erro ao verificar:', err));
    }, 3000);

    // Contador regressivo
    const expiraEm = new Date('<?= $expira_em ?>');
    const countdownEl = document.getElementById('countdown');

    const intervaloTempo = setInterval(() => {
        const agora = new Date();
        const diff = expiraEm - agora;

        if (diff <= 0) {
            clearInterval(intervaloTempo);
            clearInterval(intervaloPagamento);

            countdownEl.textContent = 'EXPIRADO';
            countdownEl.className = 'countdown danger';

            document.getElementById('status-pagamento').innerHTML =
                '<i class="ti ti-x fs-1"></i><br>Tempo esgotado. Entre em contato com o estabelecimento.';
            document.getElementById('status-pagamento').className = 'alert alert-danger';
        } else {
            const minutos = Math.floor(diff / 60000);
            const segundos = Math.floor((diff % 60000) / 1000);

            countdownEl.textContent = `${minutos}:${segundos.toString().padStart(2, '0')}`;

            // Mudar cor conforme tempo restante
            if (minutos < 1) {
                countdownEl.className = 'countdown danger';
            } else if (minutos < 3) {
                countdownEl.className = 'countdown warning';
            } else {
                countdownEl.className = 'countdown';
            }
        }
    }, 1000);

    // Limpar intervalos ao sair
    window.addEventListener('beforeunload', () => {
        clearInterval(intervaloPagamento);
        clearInterval(intervaloTempo);
    });
    </script>
</body>
</html>
