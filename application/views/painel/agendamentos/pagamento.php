<?php
/**
 * View: Pagamento de Agendamento via PIX
 *
 * Exibe QR Code e Copia e Cola para pagamento
 * Verifica status automaticamente via polling
 *
 * @author Rafael Dias - doisr.com.br
 * @date 27/12/2024
 */
$this->load->view('painel/layout/header');
?>

<div class="page-header">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-qrcode me-2"></i>
                    Pagamento do Agendamento
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="ti ti-brand-pix me-2"></i>
                            Pague com PIX
                        </h3>
                    </div>
                    <div class="card-body text-center">

                        <!-- Informações do Agendamento -->
                        <div class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <strong>Data:</strong><br>
                                    <?= date('d/m/Y', strtotime($agendamento->data)) ?>
                                </div>
                                <div class="col-6 text-end">
                                    <strong>Horário:</strong><br>
                                    <?= date('H:i', strtotime($agendamento->hora_inicio)) ?>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <?php if ($agendamento->pagamento_pix_qrcode): ?>
                        <div class="mb-4">
                            <p class="text-muted mb-3">Escaneie o QR Code com o app do seu banco</p>
                            <div class="p-3 bg-light rounded d-inline-block">
                                <img src="data:image/png;base64,<?= $agendamento->pagamento_pix_qrcode ?>"
                                     alt="QR Code PIX"
                                     class="img-fluid"
                                     style="max-width: 280px;">
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Divisor -->
                        <div class="my-4">
                            <div class="text-muted">
                                <hr class="d-inline-block" style="width: 40%;">
                                <span class="px-2">OU</span>
                                <hr class="d-inline-block" style="width: 40%;">
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
                            <div class="h3 mb-0">
                                <i class="ti ti-currency-real me-1"></i>
                                R$ <?= number_format($agendamento->pagamento_valor, 2, ',', '.') ?>
                            </div>
                        </div>

                        <!-- Tempo Restante -->
                        <div class="alert alert-warning mb-3">
                            <i class="ti ti-clock me-2"></i>
                            <strong>Expira em:</strong>
                            <span id="tempo-restante" class="fw-bold"></span>
                        </div>

                        <!-- Status do Pagamento -->
                        <div id="status-pagamento" class="alert alert-secondary">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Aguardando pagamento...
                        </div>

                        <!-- Botão Voltar -->
                        <div class="mt-4">
                            <a href="<?= base_url('painel/agendamentos') ?>" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-2"></i>
                                Voltar para Agendamentos
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Instruções -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card-title">
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
            </div>
        </div>
    </div>
</div>

<script>
// Copiar PIX
function copiarPix() {
    const input = document.getElementById('pix-copia-cola');
    input.select();
    input.setSelectionRange(0, 99999); // Mobile

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
        alert('PIX copiado para área de transferência!');
    }
}

// Polling para verificar pagamento (a cada 3 segundos)
const intervaloPagamento = setInterval(() => {
    fetch('<?= base_url("painel/agendamentos/verificar_pagamento/{$agendamento->id}") ?>')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'pago') {
                clearInterval(intervaloPagamento);
                clearInterval(intervaloTempo);

                document.getElementById('status-pagamento').innerHTML =
                    '<i class="ti ti-check fs-1"></i><br>Pagamento confirmado! Redirecionando...';
                document.getElementById('status-pagamento').className = 'alert alert-success';

                Swal.fire({
                    icon: 'success',
                    title: 'Pagamento Confirmado!',
                    text: 'Seu agendamento foi confirmado com sucesso',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '<?= base_url("painel/agendamentos") ?>';
                });
            } else if (data.status === 'expirado') {
                clearInterval(intervaloPagamento);
                clearInterval(intervaloTempo);

                document.getElementById('status-pagamento').innerHTML =
                    '<i class="ti ti-x fs-1"></i><br>PIX expirado';
                document.getElementById('status-pagamento').className = 'alert alert-danger';
            }
        })
        .catch(err => console.error('Erro ao verificar pagamento:', err));
}, 3000);

// Contador regressivo
const expiraEm = new Date('<?= $agendamento->pagamento_expira_em ?>');
const intervaloTempo = setInterval(() => {
    const agora = new Date();
    const diff = expiraEm - agora;

    if (diff <= 0) {
        clearInterval(intervaloTempo);
        clearInterval(intervaloPagamento);

        document.getElementById('tempo-restante').textContent = 'EXPIRADO';
        document.getElementById('tempo-restante').className = 'text-danger fw-bold';
        document.getElementById('status-pagamento').innerHTML =
            '<i class="ti ti-x fs-1"></i><br>PIX expirado. Crie um novo agendamento.';
        document.getElementById('status-pagamento').className = 'alert alert-danger';
    } else {
        const minutos = Math.floor(diff / 60000);
        const segundos = Math.floor((diff % 60000) / 1000);
        document.getElementById('tempo-restante').textContent =
            `${minutos}:${segundos.toString().padStart(2, '0')}`;
    }
}, 1000);

// Limpar intervalos ao sair da página
window.addEventListener('beforeunload', () => {
    clearInterval(intervaloPagamento);
    clearInterval(intervaloTempo);
});
</script>

<?php $this->load->view('painel/layout/footer'); ?>
