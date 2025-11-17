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
                </div>
            </div>
        </div>
    </div>
</div>

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
