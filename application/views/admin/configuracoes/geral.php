<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Administração</div>
                <h2 class="page-title">Configurações Gerais</h2>
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

        <!-- Navegação de Abas -->
        <div class="card mb-3">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('admin/configuracoes/geral') ?>">
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
                        <a class="nav-link" href="<?= base_url('admin/configuracoes/notificacoes') ?>">
                            <i class="ti ti-bell me-2"></i>Notificações
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Formulário -->
        <form method="post" action="<?= base_url('admin/configuracoes/geral') ?>">
            
            <!-- Dados da Empresa -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Dados da Empresa</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nome da Empresa</label>
                                <input type="text" class="form-control" name="config[empresa_nome]" 
                                    value="<?= get_config_value($configs, 'empresa_nome', 'Le Cortine') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">CNPJ</label>
                                <input type="text" class="form-control" name="config[empresa_cnpj]" 
                                    value="<?= get_config_value($configs, 'empresa_cnpj') ?>" 
                                    placeholder="00.000.000/0000-00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" class="form-control" name="config[empresa_telefone]" 
                                    value="<?= get_config_value($configs, 'empresa_telefone', '(75) 98889-0006') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" name="config[empresa_whatsapp]" 
                                    value="<?= get_config_value($configs, 'empresa_whatsapp', '5575988890006') ?>" 
                                    placeholder="5575988890006">
                                <small class="form-hint">Com DDI e DDD, sem espaços</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="config[empresa_email]" 
                                    value="<?= get_config_value($configs, 'empresa_email', 'contato@lecortine.com.br') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Endereço (para cálculo de frete) -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Endereço da Empresa</h3>
                    <p class="text-muted small mb-0">Usado como origem para cálculo de frete</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">CEP</label>
                                <input type="text" class="form-control" name="config[empresa_cep]" 
                                    value="<?= get_config_value($configs, 'empresa_cep') ?>" 
                                    placeholder="00000-000">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label class="form-label">Endereço Completo</label>
                                <input type="text" class="form-control" name="config[empresa_endereco]" 
                                    value="<?= get_config_value($configs, 'empresa_endereco') ?>" 
                                    placeholder="Rua, Número, Bairro">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cidade</label>
                                <input type="text" class="form-control" name="config[empresa_cidade]" 
                                    value="<?= get_config_value($configs, 'empresa_cidade') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado (UF)</label>
                                <input type="text" class="form-control" name="config[empresa_estado]" 
                                    value="<?= get_config_value($configs, 'empresa_estado') ?>" 
                                    placeholder="BA" maxlength="2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opções de Entrega -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Opções de Entrega</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Retirada no Local</label>
                        <div>
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="config[retirada_local_ativa]" value="1" 
                                    <?= get_config_value($configs, 'retirada_local_ativa', '1') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Permitir que clientes retirem no local</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Endereço para Retirada</label>
                        <input type="text" class="form-control" name="config[retirada_local_endereco]" 
                            value="<?= get_config_value($configs, 'retirada_local_endereco') ?>" 
                            placeholder="Endereço completo para retirada">
                        <small class="form-hint">Será exibido para o cliente quando escolher retirada no local</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Frete Grátis Acima de (R$)</label>
                        <input type="number" class="form-control" name="config[frete_gratis_acima]" 
                            value="<?= get_config_value($configs, 'frete_gratis_acima', '0') ?>" 
                            min="0" step="0.01">
                        <small class="form-hint">Digite 0 para desabilitar frete grátis</small>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
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
