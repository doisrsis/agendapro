<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/configuracoes?aba=mercadopago') ?>">Configurações</a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-test-pipe me-2"></i>
                    Teste - Mercado Pago
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <?php if (!$configurado): ?>
        <div class="alert alert-warning">
            <i class="ti ti-alert-triangle me-2"></i>
            <strong>Atenção:</strong> Configure suas credenciais do Mercado Pago em
            <a href="<?= base_url('admin/configuracoes?aba=mercadopago') ?>" class="alert-link">
                Admin > Configurações > Mercado Pago
            </a>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Teste de Conexão -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-plug me-2"></i>
                            Teste de Conexão
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Verifica se as credenciais estão corretas e se a API do Mercado Pago está acessível.
                        </p>

                        <button type="button" class="btn btn-primary w-100" onclick="testarConexao()" id="btn-testar-conexao">
                            <i class="ti ti-plug me-2"></i>
                            Testar Conexão
                        </button>

                        <div id="resultado-conexao" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- Simular Webhook -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-webhook me-2"></i>
                            Simular Webhook
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Simula o recebimento de uma notificação de webhook do Mercado Pago.
                        </p>

                        <button type="button" class="btn btn-secondary w-100" onclick="simularWebhook()" id="btn-simular-webhook">
                            <i class="ti ti-webhook me-2"></i>
                            Simular Webhook
                        </button>

                        <div id="resultado-webhook" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testes de Pagamento -->
        <div class="card mt-3 bg-primary-lt">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-1">
                            <i class="ti ti-credit-card me-2"></i>
                            Testes de Pagamento Real
                        </h3>
                        <p class="text-muted mb-0">
                            Teste pagamentos PIX e Cartão com dados de teste do Mercado Pago
                        </p>
                    </div>
                    <div class="col-auto">
                        <a href="<?= base_url('admin/pagamento-test') ?>" class="btn btn-primary">
                            <i class="ti ti-arrow-right me-2"></i>
                            Acessar Testes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-info-circle me-2"></i>
                    Informações do Sistema
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-5">URL do Webhook:</dt>
                            <dd class="col-7">
                                <code><?= base_url('webhook/mercadopago') ?></code>
                                <button class="btn btn-sm btn-icon" onclick="copiarTexto('<?= base_url('webhook/mercadopago') ?>')">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </dd>
                            <dt class="col-5">Ambiente:</dt>
                            <dd class="col-7"><?= ENVIRONMENT ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-5">PHP Version:</dt>
                            <dd class="col-7"><?= phpversion() ?></dd>
                            <dt class="col-5">CodeIgniter:</dt>
                            <dd class="col-7"><?= CI_VERSION ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Recentes do Mercado Pago -->
        <div class="card mt-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="ti ti-file-text me-2"></i>
                        Logs Recentes do Mercado Pago
                    </h3>
                    <button class="btn btn-sm btn-primary" onclick="carregarLogs()">
                        <i class="ti ti-refresh me-2"></i>
                        Atualizar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="logs-container">
                    <div class="text-center text-muted py-3">
                        <i class="ti ti-loader fs-1 mb-2 d-block"></i>
                        Clique em "Atualizar" para carregar os logs
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function testarConexao() {
    const btn = document.getElementById('btn-testar-conexao');
    const resultado = document.getElementById('resultado-conexao');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testando...';

    fetch('<?= base_url('admin/mercadopago_test/testar_conexao') ?>')
        .then(response => response.json())
        .then(data => {
            resultado.style.display = 'block';

            if (data.sucesso) {
                resultado.innerHTML = `
                    <div class="alert alert-success">
                        <h4 class="alert-title"><i class="ti ti-check me-2"></i>${data.mensagem}</h4>
                        <div class="text-secondary">
                            <strong>Modo:</strong> ${data.detalhes.modo}<br>
                            <strong>Access Token:</strong> ${data.detalhes.access_token}<br>
                            <strong>Public Key:</strong> ${data.detalhes.public_key}<br>
                            <strong>Métodos Disponíveis:</strong> ${data.detalhes.metodos_disponiveis}
                        </div>
                    </div>
                `;
            } else {
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                        <div class="text-secondary">${data.mensagem}</div>
                        ${data.detalhes.erro ? '<pre class="mt-2">' + JSON.stringify(data.detalhes.erro, null, 2) + '</pre>' : ''}
                    </div>
                `;
            }
        })
        .catch(error => {
            resultado.style.display = 'block';
            resultado.innerHTML = `
                <div class="alert alert-danger">
                    <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                    <div class="text-secondary">Erro ao fazer requisição: ${error.message}</div>
                </div>
            `;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-plug me-2"></i>Testar Conexão';
        });
}

function simularWebhook() {
    const btn = document.getElementById('btn-simular-webhook');
    const resultado = document.getElementById('resultado-webhook');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Simulando...';

    fetch('<?= base_url('admin/mercadopago_test/simular_webhook') ?>')
        .then(response => response.json())
        .then(data => {
            resultado.style.display = 'block';

            if (data.sucesso) {
                resultado.innerHTML = `
                    <div class="alert alert-success">
                        <h4 class="alert-title"><i class="ti ti-check me-2"></i>${data.mensagem}</h4>
                        <div class="text-secondary">
                            <pre>${JSON.stringify(data.detalhes, null, 2)}</pre>
                        </div>
                    </div>
                `;
            } else {
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                        <div class="text-secondary">${data.mensagem}</div>
                    </div>
                `;
            }

            // Atualizar logs após simular
            setTimeout(carregarLogs, 1000);
        })
        .catch(error => {
            resultado.style.display = 'block';
            resultado.innerHTML = `
                <div class="alert alert-danger">
                    <h4 class="alert-title"><i class="ti ti-x me-2"></i>Erro</h4>
                    <div class="text-secondary">Erro ao fazer requisição: ${error.message}</div>
                </div>
            `;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-webhook me-2"></i>Simular Webhook';
        });
}

function carregarLogs() {
    const container = document.getElementById('logs-container');
    container.innerHTML = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm me-2"></span>Carregando logs...</div>';

    fetch('<?= base_url('admin/mercadopago_test/logs') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso && data.logs.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm table-vcenter"><thead><tr><th>Nível</th><th>Data/Hora</th><th>Mensagem</th></tr></thead><tbody>';

                data.logs.forEach(log => {
                    let badgeClass = 'bg-secondary';
                    let icon = 'ti-info-circle';

                    if (log.nivel === 'ERROR') {
                        badgeClass = 'bg-danger';
                        icon = 'ti-alert-circle';
                    } else if (log.nivel === 'WARNING') {
                        badgeClass = 'bg-warning';
                        icon = 'ti-alert-triangle';
                    } else if (log.nivel === 'INFO') {
                        badgeClass = 'bg-info';
                        icon = 'ti-info-circle';
                    }

                    html += `
                        <tr>
                            <td><span class="badge ${badgeClass}"><i class="ti ${icon} me-1"></i>${log.nivel}</span></td>
                            <td><small>${log.data}</small></td>
                            <td><code class="text-wrap">${log.mensagem}</code></td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                html += `<div class="text-muted small mt-2">Total: ${data.total} logs encontrados</div>`;
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        Nenhum log do Mercado Pago encontrado hoje. Faça um teste para gerar logs.
                    </div>
                `;
            }
        })
        .catch(error => {
            container.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="ti ti-x me-2"></i>
                    Erro ao carregar logs: ${error.message}
                </div>
            `;
        });
}

function copiarTexto(texto) {
    navigator.clipboard.writeText(texto);
    alert('Texto copiado!');
}
</script>
