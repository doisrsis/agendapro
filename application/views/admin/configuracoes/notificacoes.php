<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Integrações</div>
                <h2 class="page-title">Configurações - Notificações</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/configuracoes/geral') ?>" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <!-- Mensagens -->
        <?php if ($this->session->flashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('sucesso') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('erro') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <!-- Navegação de Abas -->
        <div class="card mb-3">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/configuracoes/geral') ?>">
                            <i class="ti ti-settings me-2"></i>Geral
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/configuracoes/correios') ?>">
                            <i class="ti ti-truck-delivery me-2"></i>Correios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/configuracoes/mercadopago') ?>">
                            <i class="ti ti-credit-card me-2"></i>Mercado Pago
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('admin/configuracoes/notificacoes') ?>">
                            <i class="ti ti-bell me-2"></i>Notificações
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Formulário -->
        <form method="post" action="<?= base_url('admin/configuracoes/notificacoes') ?>">
            
            <!-- Notificações por E-mail -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-mail me-2"></i>Notificações por E-mail</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Importante:</strong> Configure o SMTP em <code>application/config/email.php</code> para enviar e-mails.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ativar Notificações por E-mail</label>
                        <div>
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="config[email_notificacoes_ativo]" value="1" 
                                    <?= get_config_value($configs, 'email_notificacoes_ativo') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Enviar notificações por e-mail</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail para Receber Notificações</label>
                        <input type="email" class="form-control" name="config[email_destinatario]" 
                            value="<?= get_config_value($configs, 'email_destinatario', 'contato@lecortine.com.br') ?>" 
                            placeholder="contato@lecortine.com.br">
                        <small class="form-hint">E-mail que receberá as notificações do sistema</small>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-bold mb-3">Eventos para Notificar:</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="config[email_novo_orcamento]" value="1" 
                                        <?= get_config_value($configs, 'email_novo_orcamento') == '1' ? 'checked' : '' ?>>
                                    <span class="form-check-label">
                                        <strong>Novo Orçamento</strong>
                                        <span class="d-block text-muted small">Quando um cliente solicitar um orçamento</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="config[email_pagamento_aprovado]" value="1" 
                                        <?= get_config_value($configs, 'email_pagamento_aprovado') == '1' ? 'checked' : '' ?>>
                                    <span class="form-check-label">
                                        <strong>Pagamento Aprovado</strong>
                                        <span class="d-block text-muted small">Quando um pagamento for confirmado</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Configuração SMTP necessária:</strong> Edite o arquivo <code>application/config/email.php</code> com os dados do seu servidor de e-mail.
                    </div>
                </div>
            </div>

            <!-- Notificações por WhatsApp -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-brand-whatsapp me-2"></i>Notificações por WhatsApp</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Como funciona:</strong> As notificações abrem o WhatsApp Web com mensagem pré-formatada para você enviar manualmente.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ativar Notificações por WhatsApp</label>
                        <div>
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="config[whatsapp_notificacoes_ativo]" value="1" 
                                    <?= get_config_value($configs, 'whatsapp_notificacoes_ativo') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Enviar notificações via WhatsApp</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Número para Receber Notificações</label>
                        <input type="text" class="form-control" name="config[whatsapp_numero_notificacao]" 
                            value="<?= get_config_value($configs, 'whatsapp_numero_notificacao', '5575988890006') ?>" 
                            placeholder="5575988890006">
                        <small class="form-hint">Formato: DDI + DDD + Número (sem espaços ou caracteres especiais)</small>
                    </div>

                    <div class="alert alert-success">
                        <i class="ti ti-check me-2"></i>
                        <strong>Exemplo de uso:</strong> Quando um novo orçamento chegar, o sistema abrirá o WhatsApp com uma mensagem pronta para você enviar ao cliente.
                    </div>
                </div>
            </div>

            <!-- Notificações no Sistema -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-bell-ringing me-2"></i>Notificações no Sistema</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Notificações internas:</strong> Aparecem no painel admin quando você está logado.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="config[notif_sistema_novo_orcamento]" value="1" 
                                        <?= get_config_value($configs, 'notif_sistema_novo_orcamento', '1') == '1' ? 'checked' : '' ?>>
                                    <span class="form-check-label">
                                        <strong>Novo Orçamento</strong>
                                        <span class="d-block text-muted small">Exibir badge no menu</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="config[notif_sistema_pagamento]" value="1" 
                                        <?= get_config_value($configs, 'notif_sistema_pagamento', '1') == '1' ? 'checked' : '' ?>>
                                    <span class="form-check-label">
                                        <strong>Novo Pagamento</strong>
                                        <span class="d-block text-muted small">Exibir badge no menu</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Som de Notificação</label>
                        <div>
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="config[notif_sistema_som]" value="1" 
                                    <?= get_config_value($configs, 'notif_sistema_som', '1') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Tocar som quando receber notificação</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo Visual -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-list-check me-2"></i>Resumo das Notificações</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th class="text-center">E-mail</th>
                                    <th class="text-center">WhatsApp</th>
                                    <th class="text-center">Sistema</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Novo Orçamento</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-success" id="status-email-orcamento">Ativo</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success" id="status-whats-orcamento">Ativo</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success" id="status-sistema-orcamento">Ativo</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Pagamento Aprovado</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-success" id="status-email-pagamento">Ativo</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-muted">N/A</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success" id="status-sistema-pagamento">Ativo</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('admin/configuracoes/testar_email') ?>" class="btn btn-outline-primary">
                            <i class="ti ti-send me-2"></i>Enviar E-mail de Teste
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-2"></i>Salvar Configurações
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<?php
// Helper para pegar valor da configuração
function get_config_value($configs, $chave, $default = '') {
    foreach ($configs as $config) {
        if ($config->chave == $chave) {
            return $config->valor;
        }
    }
    return $default;
}
?>

<script>
// Atualizar resumo visual em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', atualizarResumo);
    });
    
    function atualizarResumo() {
        // E-mail - Novo Orçamento
        const emailOrcamento = document.querySelector('input[name="config[email_novo_orcamento]"]');
        const emailAtivo = document.querySelector('input[name="config[email_notificacoes_ativo]"]');
        updateBadge('status-email-orcamento', emailAtivo?.checked && emailOrcamento?.checked);
        
        // E-mail - Pagamento
        const emailPagamento = document.querySelector('input[name="config[email_pagamento_aprovado]"]');
        updateBadge('status-email-pagamento', emailAtivo?.checked && emailPagamento?.checked);
        
        // WhatsApp - Novo Orçamento
        const whatsAtivo = document.querySelector('input[name="config[whatsapp_notificacoes_ativo]"]');
        updateBadge('status-whats-orcamento', whatsAtivo?.checked);
        
        // Sistema - Novo Orçamento
        const sistemaOrcamento = document.querySelector('input[name="config[notif_sistema_novo_orcamento]"]');
        updateBadge('status-sistema-orcamento', sistemaOrcamento?.checked);
        
        // Sistema - Pagamento
        const sistemaPagamento = document.querySelector('input[name="config[notif_sistema_pagamento]"]');
        updateBadge('status-sistema-pagamento', sistemaPagamento?.checked);
    }
    
    function updateBadge(id, isActive) {
        const badge = document.getElementById(id);
        if (badge) {
            if (isActive) {
                badge.className = 'badge bg-success';
                badge.textContent = 'Ativo';
            } else {
                badge.className = 'badge bg-secondary';
                badge.textContent = 'Inativo';
            }
        }
    }
    
    // Executar ao carregar
    atualizarResumo();
});
</script>
