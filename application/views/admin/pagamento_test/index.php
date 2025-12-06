<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/mercadopago-test') ?>">Testes Mercado Pago</a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-credit-card me-2"></i>
                    Teste de Pagamentos
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <!-- Modo Atual -->
        <div class="alert <?= $is_sandbox ? 'alert-info' : 'alert-warning' ?> mb-3">
            <i class="ti ti-info-circle me-2"></i>
            <strong>Modo Atual:</strong>
            <?php if ($is_sandbox): ?>
                <span class="badge bg-azure ms-2">TESTE (Sandbox)</span>
                Use os cartões e dados de teste da documentação do Mercado Pago
            <?php else: ?>
                <span class="badge bg-orange ms-2">PRODUÇÃO</span>
                ⚠️ Atenção! Pagamentos serão reais e cobrados
            <?php endif; ?>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-pix" role="tab">
                    <i class="ti ti-qrcode me-2"></i>
                    Teste PIX
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-cartao" role="tab">
                    <i class="ti ti-credit-card me-2"></i>
                    Teste Cartão
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-docs" role="tab">
                    <i class="ti ti-book me-2"></i>
                    Documentação
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab PIX -->
            <div class="tab-pane active" id="tab-pix" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Criar Pagamento PIX de Teste</h3>
                            </div>
                            <div class="card-body">
                                <form id="form-pix">
                                    <div class="mb-3">
                                        <label class="form-label required">Valor (R$)</label>
                                        <input type="number" class="form-control" name="valor" value="10.00" step="0.01" min="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">E-mail</label>
                                        <input type="email" class="form-control" name="email" value="test@test.com" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Nome</label>
                                        <input type="text" class="form-control" name="nome" value="Test User" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-qrcode me-2"></i>
                                        Gerar PIX
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Resultado</h3>
                            </div>
                            <div class="card-body">
                                <div id="resultado-pix">
                                    <div class="text-muted text-center py-4">
                                        <i class="ti ti-qrcode fs-1 mb-2 d-block"></i>
                                        Preencha o formulário e clique em "Gerar PIX"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Cartão -->
            <div class="tab-pane" id="tab-cartao" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Teste com Cartão</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Cartões de Teste:</strong> Use os cartões da
                            <a href="https://www.mercadopago.com.br/developers/pt/docs/checkout-api-v2/integration-test/cards" target="_blank">
                                documentação do Mercado Pago
                            </a>
                        </div>

                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <strong>Atenção:</strong> Este formulário cria o token do cartão manualmente.
                            Para produção, use o SDK oficial do Mercado Pago.
                        </div>

                        <form id="form-cartao-simples">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Número do Cartão</label>
                                    <input type="text" class="form-control" id="card-number" placeholder="5031 4332 1540 6351" maxlength="19" required>
                                    <small class="form-hint">Mastercard aprovado: 5031 4332 1540 6351</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nome no Cartão</label>
                                    <input type="text" class="form-control" id="card-holder" placeholder="APRO" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label required">Mês</label>
                                    <input type="text" class="form-control" id="card-exp-month" placeholder="12" maxlength="2" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label required">Ano</label>
                                    <input type="text" class="form-control" id="card-exp-year" placeholder="2026" maxlength="4" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label required">CVV</label>
                                    <input type="text" class="form-control" id="card-cvv" placeholder="123" maxlength="4" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label required">Valor (R$)</label>
                                    <input type="number" class="form-control" id="card-amount" value="100.00" step="0.01" min="0.01" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">CPF</label>
                                    <input type="text" class="form-control" id="card-cpf" placeholder="12345678909" value="12345678909" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">E-mail</label>
                                    <input type="email" class="form-control" id="card-email" value="test@test.com" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-credit-card me-2"></i>
                                Processar Pagamento
                            </button>

                            <div id="resultado-cartao-simples" class="mt-3" style="display: none;"></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Documentação -->
            <div class="tab-pane" id="tab-docs" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cartões de Teste</h3>
                    </div>
                    <div class="card-body">
                        <h4>Cartões que Aprovam</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Bandeira</th>
                                        <th>Número</th>
                                        <th>CVV</th>
                                        <th>Validade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Mastercard</strong></td>
                                        <td><code>5031 4332 1540 6351</code></td>
                                        <td>123</td>
                                        <td>12/2026</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Visa</strong></td>
                                        <td><code>4235 6477 2802 5682</code></td>
                                        <td>123</td>
                                        <td>12/2026</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h4 class="mt-4">Cartões que Recusam</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Bandeira</th>
                                        <th>Número</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Mastercard</strong></td>
                                        <td><code>5031 7557 3453 0604</code></td>
                                        <td>Fundos insuficientes</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Visa</strong></td>
                                        <td><code>4509 9535 6623 3704</code></td>
                                        <td>Recusado</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Nome no cartão:</strong> Use "APRO" para aprovar ou "OTHE" para outros cenários<br>
                            <strong>Validade:</strong> Use 12/2026 ou qualquer data futura<br>
                            <strong>Documentação completa:</strong>
                            <a href="https://www.mercadopago.com.br/developers/pt/docs/checkout-api-v2/integration-test/cards" target="_blank">
                                Cartões de teste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Mercado Pago SDK -->
<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
// Inicializar Mercado Pago
const mp = new MercadoPago('<?= $public_key ?>', {
    locale: 'pt-BR'
});

// Formatação automática do número do cartão
document.getElementById('card-number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Teste Cartão Simplificado
document.getElementById('form-cartao-simples').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    const resultado = document.getElementById('resultado-cartao-simples');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
    resultado.style.display = 'none';

    try {
        // Obter dados do formulário
        const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
        const cardholderName = document.getElementById('card-holder').value;
        const cardExpirationMonth = document.getElementById('card-exp-month').value;
        const cardExpirationYear = document.getElementById('card-exp-year').value;
        const securityCode = document.getElementById('card-cvv').value;
        const identificationType = 'CPF';
        const identificationNumber = document.getElementById('card-cpf').value;

        // Criar token do cartão
        const cardToken = await mp.createCardToken({
            cardNumber,
            cardholderName,
            cardExpirationMonth,
            cardExpirationYear,
            securityCode,
            identificationType,
            identificationNumber
        });

        if (!cardToken || !cardToken.id) {
            throw new Error('Erro ao criar token do cartão');
        }

        // Preparar dados para envio
        const formData = new FormData();
        formData.append('valor', document.getElementById('card-amount').value);
        formData.append('token', cardToken.id);
        formData.append('email', document.getElementById('card-email').value);
        formData.append('parcelas', '1');
        formData.append('numero_documento', identificationNumber);
        formData.append('tipo_documento', identificationType);

        // Enviar pagamento
        const response = await fetch('<?= base_url('admin/pagamento_test/criar_cartao') ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        resultado.style.display = 'block';

        if (data.sucesso) {
            resultado.innerHTML = `
                <div class="alert alert-success">
                    <h4 class="alert-title"><i class="ti ti-check me-2"></i>${data.mensagem}</h4>
                    <div class="mt-2">
                        <strong>ID:</strong> <code>${data.dados.id}</code><br>
                        <strong>Status:</strong> <span class="badge bg-${data.dados.status === 'approved' ? 'success' : 'danger'}">${data.dados.status}</span><br>
                        <strong>Detalhe:</strong> ${data.dados.status_detail}<br>
                        <strong>Valor:</strong> R$ ${data.dados.transaction_amount}
                    </div>
                </div>
            `;
        } else {
            resultado.innerHTML = `
                <div class="alert alert-danger">
                    <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                    <div>${data.mensagem}</div>
                    <pre class="mt-2">${JSON.stringify(data.dados, null, 2)}</pre>
                </div>
            `;
        }
    } catch (error) {
        resultado.style.display = 'block';
        resultado.innerHTML = `
            <div class="alert alert-danger">
                <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                <div>${error.message}</div>
            </div>
        `;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-credit-card me-2"></i>Processar Pagamento';
    }
});

// Teste PIX
document.getElementById('form-pix').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    const resultado = document.getElementById('resultado-pix');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando...';

    const formData = new FormData(this);

    fetch('<?= base_url('admin/pagamento_test/criar_pix') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            resultado.innerHTML = `
                <div class="alert alert-success">
                    <h4 class="alert-title"><i class="ti ti-check me-2"></i>${data.mensagem}</h4>
                </div>
                <div class="text-center">
                    <img src="data:image/png;base64,${data.dados.qr_code_base64}" alt="QR Code PIX" class="img-fluid mb-3" style="max-width: 300px;">
                    <div class="mb-3">
                        <strong>ID do Pagamento:</strong> <code>${data.dados.id}</code>
                        <button class="btn btn-sm btn-icon" onclick="navigator.clipboard.writeText('${data.dados.id}')">
                            <i class="ti ti-copy"></i>
                        </button>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> <span class="badge bg-warning">${data.dados.status}</span>
                    </div>
                    <button class="btn btn-primary" onclick="consultarPagamento('${data.dados.id}')">
                        <i class="ti ti-refresh me-2"></i>
                        Consultar Status
                    </button>
                </div>
            `;
        } else {
            resultado.innerHTML = `
                <div class="alert alert-danger">
                    <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                    <div>${data.mensagem}</div>
                    <pre class="mt-2">${JSON.stringify(data.dados, null, 2)}</pre>
                </div>
            `;
        }
    })
    .catch(error => {
        resultado.innerHTML = `
            <div class="alert alert-danger">
                <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                <div>${error.message}</div>
            </div>
        `;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-qrcode me-2"></i>Gerar PIX';
    });
});

function consultarPagamento(paymentId) {
    fetch('<?= base_url('admin/pagamento_test/consultar/') ?>' + paymentId)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert('Status: ' + data.dados.status + '\nStatus Detail: ' + (data.dados.status_detail || 'N/A'));
            } else {
                alert('Erro ao consultar pagamento');
            }
        });
}
</script>
