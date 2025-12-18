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
        <div class="container-xl py-5">
            <div class="text-center mb-5 mt-4">
                <a href="<?= base_url() ?>" class="navbar-brand navbar-brand-autodark">
                    <h1 class="mb-0">AgendaPro</h1>
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Resumo do Plano -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-shopping-cart me-2"></i>
                                Resumo do Pedido
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h2 class="mb-2"><?= $plano->nome ?></h2>
                                    <p class="text-muted"><?= $plano->descricao ?></p>

                                    <ul class="list-unstyled mt-4">
                                        <li class="mb-2">
                                            <i class="ti ti-users text-primary me-2"></i>
                                            <strong><?= $plano->max_profissionais == -1 ? 'Ilimitados' : $plano->max_profissionais ?></strong> profissionais
                                        </li>
                                        <li class="mb-2">
                                            <i class="ti ti-calendar text-primary me-2"></i>
                                            <strong><?= $plano->max_agendamentos_mes == -1 ? 'Ilimitados' : number_format($plano->max_agendamentos_mes, 0, ',', '.') ?></strong> agendamentos/mês
                                        </li>
                                        <?php if ($plano->trial_dias > 0): ?>
                                        <li class="mb-2">
                                            <i class="ti ti-gift text-success me-2"></i>
                                            <strong><?= $plano->trial_dias ?> dias</strong> de teste grátis
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="display-4 fw-bold text-primary">
                                        R$ <?= number_format($plano->valor_mensal, 2, ',', '.') ?>
                                    </div>
                                    <div class="text-muted">por mês</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('painel/assinatura_expirada') ?>" class="btn btn-link">
                                    <i class="ti ti-arrow-left me-2"></i>
                                    Voltar
                                </a>

                                <button type="button" onclick="abrirModalPix()" class="btn btn-primary btn-lg" data-plano-id="<?= $plano->id ?>">
                                    <i class="ti ti-qrcode me-2"></i>
                                    Pagar com PIX
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Garantia -->
                    <div class="text-center mt-4">
                        <div class="text-muted small">
                            <i class="ti ti-lock me-1"></i>
                            Pagamento 100% seguro via Mercado Pago
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center text-muted mt-5">
                <small>© <?= date('Y') ?> AgendaPro. Todos os direitos reservados.</small>
            </div>
        </div>
    </div>

    <!-- Modal PIX -->
    <div class="modal modal-blur fade" id="modal-pix" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pagamento via PIX</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="pix-content">
                    <!-- Loading -->
                    <div id="pix-loading">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Gerando QR Code PIX...</p>
                    </div>

                    <!-- QR Code -->
                    <div id="pix-qrcode" style="display: none;">
                        <div class="alert alert-success">
                            <i class="ti ti-check-circle me-2"></i>
                            QR Code gerado com sucesso!
                        </div>

                        <div class="mb-4">
                            <img id="qr-code-image" src="" alt="QR Code PIX" class="img-fluid" style="max-width: 300px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Código PIX (Copiar e Colar)</label>
                            <div class="input-group">
                                <input type="text" id="pix-code" class="form-control font-monospace" readonly>
                                <button class="btn btn-primary" type="button" onclick="copiarPix()">
                                    <i class="ti ti-copy me-2"></i>
                                    Copiar
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div><i class="ti ti-info-circle icon alert-icon"></i></div>
                                <div class="text-start">
                                    <h4 class="alert-title">Aguardando pagamento...</h4>
                                    <p class="mb-0">Escaneie o QR Code ou copie o código PIX. Assim que o pagamento for confirmado, sua assinatura será ativada automaticamente.</p>
                                </div>
                            </div>
                        </div>

                        <div id="status-pagamento" class="mt-3">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span class="text-muted">Verificando pagamento...</span>
                        </div>
                    </div>

                    <!-- Erro -->
                    <div id="pix-erro" style="display: none;">
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-circle me-2"></i>
                            <span id="erro-mensagem"></span>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    console.log('Script carregado!');
    console.log('Bootstrap disponível:', typeof bootstrap !== 'undefined');

    let pollingInterval;
    let paymentId;
    const planoId = <?= $plano->id ?>;

    function abrirModalPix() {
        console.log('Função abrirModalPix chamada! Plano ID:', planoId);

        const modal = new bootstrap.Modal(document.getElementById('modal-pix'));

        // Resetar modal
        document.getElementById('pix-loading').style.display = 'block';
        document.getElementById('pix-qrcode').style.display = 'none';
        document.getElementById('pix-erro').style.display = 'none';

        modal.show();

        console.log('Gerando PIX...');

        // Gerar PIX
        fetch('<?= base_url('painel/checkout/gerar-pix') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'plano_id=' + planoId
        })
        .then(response => {
            console.log('Resposta recebida:', response);
            return response.json();
        })
        .then(data => {
            console.log('Dados:', data);
            document.getElementById('pix-loading').style.display = 'none';

            if (data.success) {
                // Exibir QR Code
                document.getElementById('qr-code-image').src = 'data:image/png;base64,' + data.qr_code_base64;
                document.getElementById('pix-code').value = data.qr_code;
                document.getElementById('pix-qrcode').style.display = 'block';

                paymentId = data.payment_id;

                // Iniciar polling
                startPolling(paymentId);
            } else {
                // Exibir erro
                console.error('Erro:', data.error);
                document.getElementById('erro-mensagem').textContent = data.error || 'Erro ao gerar PIX';
                document.getElementById('pix-erro').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erro de conexão:', error);
            document.getElementById('pix-loading').style.display = 'none';
            document.getElementById('erro-mensagem').textContent = 'Erro de conexão. Tente novamente.';
            document.getElementById('pix-erro').style.display = 'block';
        });
    }

    function copiarPix() {
        const pixCode = document.getElementById('pix-code');
        pixCode.select();
        document.execCommand('copy');

        Swal.fire({
            icon: 'success',
            title: 'Código copiado!',
            text: 'Cole no seu app de banco para pagar',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function startPolling(paymentId) {
        pollingInterval = setInterval(function() {
            fetch('<?= base_url('painel/checkout/status/') ?>' + paymentId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'approved') {
                        clearInterval(pollingInterval);

                        Swal.fire({
                            icon: 'success',
                            title: 'Pagamento Aprovado!',
                            text: 'Sua assinatura foi ativada com sucesso!',
                            confirmButtonText: 'Ir para Dashboard'
                        }).then(() => {
                            window.location.href = '<?= base_url('painel/dashboard') ?>';
                        });
                    } else if (data.status === 'rejected' || data.status === 'cancelled') {
                        clearInterval(pollingInterval);

                        Swal.fire({
                            icon: 'error',
                            title: 'Pagamento Não Aprovado',
                            text: 'O pagamento foi ' + (data.status === 'rejected' ? 'rejeitado' : 'cancelado'),
                            confirmButtonText: 'Tentar Novamente'
                        }).then(() => {
                            location.reload();
                        });
                    }
                }
            });
        }, 3000);
    }
    </script>
</body>
</html>
