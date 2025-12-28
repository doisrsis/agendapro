<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Administração</div>
                <h2 class="page-title">
                    <i class="ti ti-settings me-2"></i>
                    Configurações
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <?php if ($this->session->flashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="ti ti-check icon alert-icon"></i>
                    </div>
                    <div>
                        <?= $this->session->flashdata('sucesso') ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="ti ti-alert-circle icon alert-icon"></i>
                    </div>
                    <div>
                        <?= $this->session->flashdata('erro') ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('aviso')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="ti ti-alert-triangle icon alert-icon"></i>
                    </div>
                    <div>
                        <?= $this->session->flashdata('aviso') ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Card com Abas -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tab-geral" class="nav-link <?= $aba_ativa == 'geral' ? 'active' : '' ?>" data-bs-toggle="tab" aria-selected="<?= $aba_ativa == 'geral' ? 'true' : 'false' ?>" role="tab">
                            <i class="ti ti-settings me-2"></i>
                            Geral
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab-smtp" class="nav-link <?= $aba_ativa == 'smtp' ? 'active' : '' ?>" data-bs-toggle="tab" aria-selected="<?= $aba_ativa == 'smtp' ? 'true' : 'false' ?>" role="tab">
                            <i class="ti ti-mail me-2"></i>
                            SMTP
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab-mercadopago" class="nav-link <?= $aba_ativa == 'mercadopago' ? 'active' : '' ?>" data-bs-toggle="tab" aria-selected="<?= $aba_ativa == 'mercadopago' ? 'true' : 'false' ?>" role="tab">
                            <i class="ti ti-credit-card me-2"></i>
                            Mercado Pago
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab-waha" class="nav-link <?= $aba_ativa == 'waha' ? 'active' : '' ?>" data-bs-toggle="tab" aria-selected="<?= $aba_ativa == 'waha' ? 'true' : 'false' ?>" role="tab">
                            <i class="ti ti-brand-whatsapp me-2"></i>
                            WhatsApp (WAHA)
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- ABA GERAL -->
                    <div class="tab-pane <?= $aba_ativa == 'geral' ? 'active show' : '' ?>" id="tab-geral" role="tabpanel">
                        <form method="post" action="<?= base_url('admin/configuracoes') ?>" enctype="multipart/form-data">
                            <input type="hidden" name="grupo" value="geral">

                            <h3 class="mb-3">Informações do Sistema</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nome do Sistema</label>
                                        <input type="text" class="form-control" name="config[sistema_nome]"
                                            value="<?= get_config_value($configs_geral, 'sistema_nome', 'Dashboard Administrativo') ?>"
                                            required>
                                        <small class="form-hint">Nome que aparecerá no sistema</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">E-mail Principal</label>
                                        <input type="email" class="form-control" name="config[sistema_email]"
                                            value="<?= get_config_value($configs_geral, 'sistema_email', 'contato@sistema.com.br') ?>"
                                            required>
                                        <small class="form-hint">E-mail principal do sistema</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" class="form-control" name="config[sistema_telefone]"
                                            value="<?= get_config_value($configs_geral, 'sistema_telefone') ?>"
                                            placeholder="(00) 0000-0000">
                                        <small class="form-hint">Telefone de contato</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Endereço</label>
                                        <input type="text" class="form-control" name="config[sistema_endereco]"
                                            value="<?= get_config_value($configs_geral, 'sistema_endereco') ?>"
                                            placeholder="Rua, Número - Bairro">
                                        <small class="form-hint">Endereço completo (opcional)</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Personalização</h3>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Logo do Sistema</label>

                                        <?php
                                        $logo_atual = get_config_value($configs_geral, 'sistema_logo');
                                        if ($logo_atual && file_exists('./assets/img/logo/' . $logo_atual)):
                                        ?>
                                        <div class="mb-2">
                                            <img src="<?= base_url('assets/img/logo/' . $logo_atual) ?>"
                                                 alt="Logo Atual"
                                                 style="max-height: 80px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: white;">
                                            <div class="mt-2">
                                                <small class="text-muted">Logo atual: <?= $logo_atual ?></small>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <input type="file" class="form-control" name="sistema_logo" accept="image/*">
                                        <small class="form-hint">
                                            Formatos aceitos: JPG, PNG, SVG. Tamanho recomendado: 200x50px.
                                            <?php if ($logo_atual): ?>
                                            Deixe em branco para manter a logo atual.
                                            <?php else: ?>
                                            Se não enviar logo, será usado o nome do sistema.
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <?php if ($logo_atual): ?>
                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="remover_logo" value="1">
                                    <span class="form-check-label">Remover logo atual</span>
                                </label>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ABA SMTP -->
                    <div class="tab-pane <?= $aba_ativa == 'smtp' ? 'active show' : '' ?>" id="tab-smtp" role="tabpanel">
                        <form method="post" action="<?= base_url('admin/configuracoes') ?>">
                            <input type="hidden" name="grupo" value="smtp">

                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-info-circle icon alert-icon"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Sobre as Configurações SMTP</h4>
                                        <div class="text-secondary">
                                            Configure aqui o servidor SMTP para envio de e-mails do sistema (recuperação de senha, notificações, etc).
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="config[smtp_ativo]" value="1"
                                            <?= (get_config_value($configs_smtp, 'smtp_ativo') == '1') ? 'checked' : '' ?>>
                                        <span class="form-check-label">
                                            <strong>Ativar SMTP</strong>
                                            <span class="form-check-description">Habilitar envio de e-mails via SMTP</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Servidor SMTP</h3>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label required">Host SMTP</label>
                                    <input type="text" class="form-control" name="config[smtp_host]"
                                        value="<?= get_config_value($configs_smtp, 'smtp_host') ?>"
                                        placeholder="smtp.gmail.com">
                                    <small class="form-hint">Endereço do servidor SMTP</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Porta</label>
                                    <input type="number" class="form-control" name="config[smtp_porta]"
                                        value="<?= get_config_value($configs_smtp, 'smtp_porta', '587') ?>"
                                        placeholder="587">
                                    <small class="form-hint">587 (TLS) ou 465 (SSL)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Usuário SMTP</label>
                                    <input type="text" class="form-control" name="config[smtp_usuario]"
                                        value="<?= get_config_value($configs_smtp, 'smtp_usuario') ?>"
                                        placeholder="seu-email@gmail.com">
                                    <small class="form-hint">Geralmente é o seu e-mail</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Senha SMTP</label>
                                    <input type="password" class="form-control" name="config[smtp_senha]"
                                        value="<?= get_config_value($configs_smtp, 'smtp_senha') ?>"
                                        placeholder="••••••••">
                                    <small class="form-hint">Senha do e-mail ou senha de app</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Tipo de Segurança</label>
                                    <select class="form-select" name="config[smtp_seguranca]">
                                        <option value="tls" <?= (get_config_value($configs_smtp, 'smtp_seguranca') == 'tls') ? 'selected' : '' ?>>TLS (porta 587)</option>
                                        <option value="ssl" <?= (get_config_value($configs_smtp, 'smtp_seguranca') == 'ssl') ? 'selected' : '' ?>>SSL (porta 465)</option>
                                    </select>
                                    <small class="form-hint">Tipo de criptografia</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Remetente</h3>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">E-mail do Remetente</label>
                                    <input type="email" class="form-control" name="config[smtp_remetente_email]"
                                        value="<?= get_config_value($configs_smtp, 'smtp_remetente_email') ?>"
                                        placeholder="noreply@seusite.com.br">
                                    <small class="form-hint">E-mail que aparecerá como remetente</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Nome do Remetente</label>
                                    <input type="text" class="form-control" name="config[smtp_remetente_nome]"
                                        value="<?= get_config_value($configs_smtp, 'smtp_remetente_nome') ?>"
                                        placeholder="Sistema - Seu Site">
                                    <small class="form-hint">Nome que aparecerá como remetente</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Exemplos de Configuração</h3>

                            <div class="accordion" id="accordionExemplos">
                                <!-- Gmail -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGmail">
                                            <i class="ti ti-brand-gmail me-2"></i> Gmail
                                        </button>
                                    </h2>
                                    <div id="collapseGmail" class="accordion-collapse collapse" data-bs-parent="#accordionExemplos">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled">
                                                <li><strong>Host:</strong> smtp.gmail.com</li>
                                                <li><strong>Porta:</strong> 587</li>
                                                <li><strong>Segurança:</strong> TLS</li>
                                                <li><strong>Usuário:</strong> seu-email@gmail.com</li>
                                                <li><strong>Senha:</strong> Use uma "Senha de app"</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Outlook -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOutlook">
                                            <i class="ti ti-brand-outlook me-2"></i> Outlook / Hotmail
                                        </button>
                                    </h2>
                                    <div id="collapseOutlook" class="accordion-collapse collapse" data-bs-parent="#accordionExemplos">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled">
                                                <li><strong>Host:</strong> smtp-mail.outlook.com</li>
                                                <li><strong>Porta:</strong> 587</li>
                                                <li><strong>Segurança:</strong> TLS</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Servidor Próprio -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseServidor">
                                            <i class="ti ti-server me-2"></i> Servidor Próprio (cPanel)
                                        </button>
                                    </h2>
                                    <div id="collapseServidor" class="accordion-collapse collapse" data-bs-parent="#accordionExemplos">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled">
                                                <li><strong>Host:</strong> mail.seudominio.com.br</li>
                                                <li><strong>Porta:</strong> 465</li>
                                                <li><strong>Segurança:</strong> SSL</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <div>
                                    <a href="<?= base_url('admin/configuracoes/testar_email') ?>" class="btn btn-info">
                                        <i class="ti ti-send me-2"></i>
                                        Testar E-mail
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-2"></i>
                                        Salvar Configurações
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ABA MERCADO PAGO -->
                    <div class="tab-pane <?= $aba_ativa == 'mercadopago' ? 'active show' : '' ?>" id="tab-mercadopago" role="tabpanel">
                        <form method="post" action="<?= base_url('admin/configuracoes') ?>">
                            <input type="hidden" name="grupo" value="mercadopago">

                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-info-circle icon alert-icon"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Sobre o Mercado Pago</h4>
                                        <div class="text-secondary">
                                            Configure suas credenciais do Mercado Pago para processar pagamentos PIX e cartão de crédito.
                                            Obtenha suas credenciais em:
                                            <a href="https://www.mercadopago.com.br/developers/panel/credentials" target="_blank" class="alert-link">
                                                Painel de Desenvolvedores
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="mb-3">Credenciais da API</h3>

                            <div class="alert alert-warning mb-3">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-alert-triangle icon alert-icon"></i>
                                    </div>
                                    <div>
                                        <strong>Importante:</strong> Configure ambas as credenciais (teste e produção).
                                        O sistema usará automaticamente as credenciais corretas baseado no modo selecionado abaixo.
                                    </div>
                                </div>
                            </div>

                            <!-- Credenciais de TESTE -->
                            <div class="card mb-3">
                                <div class="card-header bg-azure-lt">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-flask me-2"></i>
                                        Credenciais de Teste (Sandbox)
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Access Token de Teste</label>
                                        <input type="text" class="form-control font-monospace" name="config[mercadopago_access_token_test]"
                                            value="<?= get_config_value($configs_mercadopago, 'mercadopago_access_token_test') ?>"
                                            placeholder="TEST-...">
                                        <small class="form-hint">Token de teste para ambiente sandbox</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Public Key de Teste</label>
                                        <input type="text" class="form-control font-monospace" name="config[mercadopago_public_key_test]"
                                            value="<?= get_config_value($configs_mercadopago, 'mercadopago_public_key_test') ?>"
                                            placeholder="TEST-...">
                                        <small class="form-hint">Chave pública de teste</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Credenciais de PRODUÇÃO -->
                            <div class="card mb-3">
                                <div class="card-header bg-green-lt">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-shield-check me-2"></i>
                                        Credenciais de Produção
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Access Token de Produção</label>
                                        <input type="text" class="form-control font-monospace" name="config[mercadopago_access_token_prod]"
                                            value="<?= get_config_value($configs_mercadopago, 'mercadopago_access_token_prod') ?>"
                                            placeholder="APP_USR-...">
                                        <small class="form-hint">Token de produção para transações reais</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Public Key de Produção</label>
                                        <input type="text" class="form-control font-monospace" name="config[mercadopago_public_key_prod]"
                                            value="<?= get_config_value($configs_mercadopago, 'mercadopago_public_key_prod') ?>"
                                            placeholder="APP_USR-...">
                                        <small class="form-hint">Chave pública de produção</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Modo de Operação</h3>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" type="checkbox" name="config[mercadopago_sandbox]" value="1"
                                            <?= (get_config_value($configs_mercadopago, 'mercadopago_sandbox', '1') == '1') ? 'checked' : '' ?>>
                                        <span class="form-check-label">
                                            <strong>Modo Sandbox (Testes)</strong>
                                            <span class="form-check-description">
                                                <span class="badge bg-azure me-2">TESTE</span>
                                                Ativado: Usa credenciais de teste.
                                                <span class="badge bg-green ms-2">PRODUÇÃO</span>
                                                Desativado: Usa credenciais de produção.
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Status Atual:</strong>
                                <?php if (get_config_value($configs_mercadopago, 'mercadopago_sandbox', '1') == '1'): ?>
                                    <span class="badge bg-azure">Modo TESTE ativado</span> - Transações não são reais
                                <?php else: ?>
                                    <span class="badge bg-green">Modo PRODUÇÃO ativado</span> - Transações são reais e cobradas
                                <?php endif; ?>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">URLs de Webhook</h3>

                            <div class="alert alert-info mb-3">
                                <i class="ti ti-info-circle me-2"></i>
                                Configure URLs diferentes para teste e produção no
                                <a href="https://www.mercadopago.com.br/developers/panel/webhooks" target="_blank" class="alert-link">
                                    painel de webhooks do Mercado Pago
                                </a>
                            </div>

                            <!-- Webhook de TESTE -->
                            <div class="card mb-3">
                                <div class="card-header bg-azure-lt">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-webhook me-2"></i>
                                        Webhook de Teste
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">URL do Webhook de Teste</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control font-monospace"
                                                   name="config[mercadopago_webhook_url_test]"
                                                   value="<?= get_config_value($configs_mercadopago, 'mercadopago_webhook_url_test', base_url('webhook/mercadopago')) ?>"
                                                   id="webhook-url-test"
                                                   placeholder="<?= base_url('webhook/mercadopago') ?>">
                                            <button class="btn btn-icon" type="button"
                                                    onclick="navigator.clipboard.writeText(document.getElementById('webhook-url-test').value); alert('URL de teste copiada!')">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        <small class="form-hint">
                                            Configure esta URL para receber notificações de pagamentos de teste (pode ser localhost)
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Webhook de PRODUÇÃO -->
                            <div class="card mb-3">
                                <div class="card-header bg-green-lt">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-webhook me-2"></i>
                                        Webhook de Produção
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label required">URL do Webhook de Produção</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control font-monospace"
                                                   name="config[mercadopago_webhook_url_prod]"
                                                   value="<?= get_config_value($configs_mercadopago, 'mercadopago_webhook_url_prod', base_url('webhook/mercadopago')) ?>"
                                                   id="webhook-url-prod"
                                                   placeholder="https://seudominio.com/webhook/mercadopago"
                                                   required>
                                            <button class="btn btn-icon" type="button"
                                                    onclick="navigator.clipboard.writeText(document.getElementById('webhook-url-prod').value); alert('URL de produção copiada!')">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        <small class="form-hint">
                                            Configure esta URL para receber notificações de pagamentos reais
                                        </small>
                                    </div>

                                    <div class="alert alert-warning mb-0">
                                        <i class="ti ti-alert-triangle me-2"></i>
                                        <strong>Importante:</strong> Em produção, a URL deve ser acessível via HTTPS e não pode ser localhost.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Documentação</h3>

                            <div class="list-group">
                                <a href="https://www.mercadopago.com.br/developers/pt/docs/checkout-api-v2/overview"
                                   target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-book me-2"></i>
                                    Visão Geral da API
                                </a>
                                <a href="https://www.mercadopago.com.br/developers/pt/docs/checkout-api-v2/payment-integration/pix"
                                   target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-qrcode me-2"></i>
                                    Integração PIX
                                </a>
                                <a href="https://www.mercadopago.com.br/developers/pt/docs/checkout-api-v2/payment-integration/cards"
                                   target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-credit-card me-2"></i>
                                    Integração Cartões
                                </a>
                                <a href="https://www.mercadopago.com.br/developers/pt/docs/checkout-api-v2/notifications"
                                   target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-bell me-2"></i>
                                    Webhooks e Notificações
                                </a>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ABA WHATSAPP (WAHA) -->
                    <div class="tab-pane <?= $aba_ativa == 'waha' ? 'active show' : '' ?>" id="tab-waha" role="tabpanel">
                        <form method="post" action="<?= base_url('admin/configuracoes') ?>">
                            <input type="hidden" name="grupo" value="waha">

                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-info-circle icon alert-icon"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Sobre a Integração WAHA</h4>
                                        <div class="text-secondary">
                                            Configure a integração com <strong>WAHA - WhatsApp HTTP API</strong> para enviar notificações
                                            aos estabelecimentos (clientes do SaaS) e ter um bot de suporte.
                                            <br>
                                            <a href="https://waha.devlike.pro/docs/" target="_blank" class="alert-link">
                                                📚 Documentação WAHA
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status da Conexão -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-plug me-2"></i>
                                        Status da Conexão
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div id="waha-status-container">
                                        <?php
                                        $waha_status = get_config_value($configs_waha, 'waha_status', 'desconectado');
                                        $waha_numero = get_config_value($configs_waha, 'waha_numero_conectado', '');
                                        ?>

                                        <?php if ($waha_status == 'conectado' && $waha_numero): ?>
                                            <div class="alert alert-success">
                                                <div class="d-flex align-items-center">
                                                    <span class="status-indicator status-green status-indicator-animated me-3">
                                                        <span class="status-indicator-circle"></span>
                                                        <span class="status-indicator-circle"></span>
                                                        <span class="status-indicator-circle"></span>
                                                    </span>
                                                    <div>
                                                        <strong>Conectado</strong>
                                                        <div class="text-muted">Número: <?= $waha_numero ?></div>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <a href="<?= base_url('admin/configuracoes/waha_desconectar') ?>"
                                                           class="btn btn-outline-danger btn-sm"
                                                           onclick="return confirm('Deseja realmente desconectar o WhatsApp?')">
                                                            <i class="ti ti-plug-off me-1"></i>
                                                            Desconectar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif ($waha_status == 'conectando'): ?>
                                            <div class="alert alert-warning">
                                                <div class="d-flex align-items-center">
                                                    <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                                                    <div>
                                                        <strong>Aguardando conexão...</strong>
                                                        <div class="text-muted">Escaneie o QR Code abaixo com seu WhatsApp</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- QR Code Container -->
                                            <div class="text-center my-4" id="qrcode-container">
                                                <div class="spinner-border" role="status">
                                                    <span class="visually-hidden">Carregando QR Code...</span>
                                                </div>
                                                <p class="text-muted mt-2">Carregando QR Code...</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary">
                                                <div class="d-flex align-items-center">
                                                    <span class="status-indicator status-secondary me-3"></span>
                                                    <div>
                                                        <strong>Desconectado</strong>
                                                        <div class="text-muted">Configure as credenciais e inicie a sessão</div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($waha_status != 'conectado'): ?>
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="<?= base_url('admin/configuracoes/waha_iniciar_sessao') ?>" class="btn btn-success">
                                            <i class="ti ti-plug me-1"></i>
                                            Iniciar Sessão
                                        </a>
                                        <a href="<?= base_url('admin/configuracoes/testar_waha') ?>" class="btn btn-outline-primary">
                                            <i class="ti ti-test-pipe me-1"></i>
                                            Testar Conexão API
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Formulário de Teste de Envio (separado do form principal) -->
                            <?php if ($waha_status == 'conectado'): ?>
                            </form><!-- Fecha form principal temporariamente -->
                            <div class="card mb-4 border-success">
                                <div class="card-header bg-success-lt">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-send me-2"></i>
                                        Enviar Mensagem de Teste
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="<?= base_url('admin/configuracoes/waha_enviar_teste') ?>" class="row g-2 align-items-end">
                                        <div class="col-md-8">
                                            <label class="form-label">Número do WhatsApp</label>
                                            <input type="text" class="form-control" name="numero"
                                                   placeholder="5511999999999" required
                                                   pattern="[0-9]{10,15}"
                                                   title="Número com código do país (ex: 5511999999999)">
                                            <small class="text-muted">Código do país (55) + DDD + número</small>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="ti ti-send me-1"></i>
                                                Enviar Teste
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <form method="post" action="<?= base_url('admin/configuracoes') ?>"><!-- Reabre form principal -->
                            <input type="hidden" name="grupo" value="waha">
                            <?php endif; ?>

                            <h3 class="mb-3">Configurações da API</h3>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label required">URL da API WAHA</label>
                                    <input type="url" class="form-control" name="config[waha_api_url]"
                                        value="<?= get_config_value($configs_waha, 'waha_api_url') ?>"
                                        placeholder="http://localhost:3000">
                                    <small class="form-hint">URL onde a WAHA está rodando (ex: http://localhost:3000 ou https://waha.seudominio.com)</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">API Key</label>
                                    <input type="password" class="form-control" name="config[waha_api_key]"
                                        value="<?= get_config_value($configs_waha, 'waha_api_key') ?>"
                                        placeholder="sua-api-key">
                                    <small class="form-hint">Chave de autenticação (se configurada)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nome da Sessão</label>
                                    <input type="text" class="form-control" name="config[waha_session_name]"
                                        value="<?= get_config_value($configs_waha, 'waha_session_name', 'saas_admin') ?>"
                                        placeholder="saas_admin">
                                    <small class="form-hint">Identificador único da sessão (padrão: saas_admin)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">URL do Webhook</label>
                                    <div class="input-group">
                                        <input type="url" class="form-control" name="config[waha_webhook_url]"
                                            value="<?= get_config_value($configs_waha, 'waha_webhook_url', base_url('webhook/waha')) ?>"
                                            id="waha-webhook-url"
                                            placeholder="<?= base_url('webhook/waha') ?>">
                                        <button class="btn btn-icon" type="button"
                                                onclick="navigator.clipboard.writeText(document.getElementById('waha-webhook-url').value); alert('URL copiada!')">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                    </div>
                                    <small class="form-hint">URL para receber mensagens e eventos</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" type="checkbox" name="config[waha_ativo]" value="1"
                                            <?= (get_config_value($configs_waha, 'waha_ativo') == '1') ? 'checked' : '' ?>>
                                        <span class="form-check-label">
                                            <strong>Ativar Integração WAHA</strong>
                                            <span class="form-check-description">
                                                Habilitar envio de mensagens via WhatsApp usando WAHA
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Documentação e Ajuda</h3>

                            <div class="list-group mb-4">
                                <a href="https://waha.devlike.pro/docs/overview/quick-start/" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-rocket me-2"></i>
                                    Quick Start - Instalação Rápida
                                </a>
                                <a href="https://waha.devlike.pro/docs/how-to/sessions/" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-device-mobile me-2"></i>
                                    Gerenciamento de Sessões
                                </a>
                                <a href="https://waha.devlike.pro/docs/how-to/send-messages/" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-send me-2"></i>
                                    Envio de Mensagens
                                </a>
                                <a href="https://waha.devlike.pro/docs/how-to/receive-messages/" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="ti ti-message me-2"></i>
                                    Recebimento de Mensagens (Webhooks)
                                </a>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para atualizar QR Code e Status WAHA -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrcodeContainer = document.getElementById('qrcode-container');
    const statusContainer = document.getElementById('waha-status-container');

    // Se estiver em modo "conectando", buscar QR Code periodicamente
    <?php if ($waha_status == 'conectando'): ?>
    let qrInterval = setInterval(function() {
        fetch('<?= base_url('admin/configuracoes/waha_qrcode') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'connected' || data.status === 'working') {
                        // Conectado! Recarregar página
                        clearInterval(qrInterval);
                        location.reload();
                    } else if (data.qrcode) {
                        // Mostrar QR Code
                        qrcodeContainer.innerHTML = `
                            <img src="data:image/png;base64,${data.qrcode}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                            <p class="text-muted mt-2">Escaneie com seu WhatsApp</p>
                        `;
                    }
                }
            })
            .catch(err => console.error('Erro ao buscar QR:', err));
    }, 3000);
    <?php endif; ?>
});
</script>

<?php
// Helper para pegar valor da configuração
function get_config_value($configs, $chave, $default = '') {
    if (is_array($configs)) {
        foreach ($configs as $config) {
            if ($config->chave == $chave) {
                return $config->valor;
            }
        }
    }
    return $default;
}
?>
