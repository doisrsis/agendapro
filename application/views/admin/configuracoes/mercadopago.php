<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Integrações</div>
                <h2 class="page-title">Configurações - Mercado Pago</h2>
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
                        <a class="nav-link active" href="<?= base_url('admin/configuracoes/mercadopago') ?>">
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
        <form method="post" action="<?= base_url('admin/configuracoes/mercadopago') ?>">
            
            <!-- Status e Ambiente -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Status da Integração</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Integração Ativa</label>
                                <div>
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="config[mercadopago_ativo]" value="1" 
                                            <?= get_config_value($configs, 'mercadopago_ativo') == '1' ? 'checked' : '' ?>>
                                        <span class="form-check-label">Ativar pagamentos online</span>
                                    </label>
                                </div>
                                <small class="form-hint">Desative para aceitar apenas pagamento via WhatsApp</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Ambiente</label>
                                <select class="form-select" name="config[mercadopago_ambiente]" required>
                                    <option value="teste" <?= get_config_value($configs, 'mercadopago_ambiente') == 'teste' ? 'selected' : '' ?>>Teste</option>
                                    <option value="producao" <?= get_config_value($configs, 'mercadopago_ambiente') == 'producao' ? 'selected' : '' ?>>Produção</option>
                                </select>
                                <small class="form-hint">Use "Teste" enquanto estiver configurando</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credenciais de Teste -->
            <div class="card mb-3">
                <div class="card-header bg-azure-lt">
                    <h3 class="card-title"><i class="ti ti-flask me-2"></i>Credenciais de Teste</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Como obter:</strong> Acesse <a href="https://www.mercadopago.com.br/developers/panel/credentials" target="_blank">Mercado Pago Developers</a> → Credenciais → Credenciais de teste
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Key (Teste)</label>
                        <input type="text" class="form-control font-monospace" name="config[mercadopago_public_key_teste]" 
                            value="<?= get_config_value($configs, 'mercadopago_public_key_teste') ?>" 
                            placeholder="TEST-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                        <small class="form-hint">Chave pública para o frontend</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Access Token (Teste)</label>
                        <input type="password" class="form-control font-monospace" name="config[mercadopago_access_token_teste]" 
                            value="<?= get_config_value($configs, 'mercadopago_access_token_teste') ?>" 
                            placeholder="TEST-xxxxxxxxxxxx-xxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxx">
                        <small class="form-hint">Token de acesso para o backend (mantenha em segredo)</small>
                    </div>
                </div>
            </div>

            <!-- Credenciais de Produção -->
            <div class="card mb-3">
                <div class="card-header bg-green-lt">
                    <h3 class="card-title"><i class="ti ti-rocket me-2"></i>Credenciais de Produção</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Atenção:</strong> Só use em produção após testar completamente. Certifique-se de ter SSL/HTTPS configurado.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Key (Produção)</label>
                        <input type="text" class="form-control font-monospace" name="config[mercadopago_public_key_prod]" 
                            value="<?= get_config_value($configs, 'mercadopago_public_key_prod') ?>" 
                            placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                        <small class="form-hint">Chave pública para o frontend</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Access Token (Produção)</label>
                        <input type="password" class="form-control font-monospace" name="config[mercadopago_access_token_prod]" 
                            value="<?= get_config_value($configs, 'mercadopago_access_token_prod') ?>" 
                            placeholder="APP_USR-xxxxxxxxxxxx-xxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxx">
                        <small class="form-hint">Token de acesso para o backend (mantenha em segredo)</small>
                    </div>
                </div>
            </div>

            <!-- Configurações de Pagamento -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Configurações de Pagamento</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Métodos de Pagamento</label>
                                <input type="text" class="form-control" name="config[mercadopago_metodos]" 
                                    value="<?= get_config_value($configs, 'mercadopago_metodos', 'credit_card,debit_card,pix') ?>" 
                                    placeholder="credit_card,debit_card,pix">
                                <small class="form-hint">Separe por vírgula. Ex: credit_card,debit_card,pix</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nome na Fatura</label>
                                <input type="text" class="form-control" name="config[mercadopago_statement_descriptor]" 
                                    value="<?= get_config_value($configs, 'mercadopago_statement_descriptor', 'LE CORTINE') ?>" 
                                    placeholder="LE CORTINE" maxlength="13">
                                <small class="form-hint">Máximo 13 caracteres</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Máximo de Parcelas</label>
                                <input type="number" class="form-control" name="config[mercadopago_max_parcelas]" 
                                    value="<?= get_config_value($configs, 'mercadopago_max_parcelas', '12') ?>" 
                                    min="1" max="12">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Valor Mínimo da Parcela (R$)</label>
                                <input type="number" class="form-control" name="config[mercadopago_parcela_minima]" 
                                    value="<?= get_config_value($configs, 'mercadopago_parcela_minima', '5') ?>" 
                                    min="1" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Taxa de Juros (% ao mês)</label>
                                <input type="number" class="form-control" name="config[mercadopago_juros]" 
                                    value="<?= get_config_value($configs, 'mercadopago_juros', '0') ?>" 
                                    min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="config[mercadopago_auto_return]" value="1" 
                                    <?= get_config_value($configs, 'mercadopago_auto_return') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Retorno Automático</span>
                            </label>
                            <small class="form-hint d-block">Redirecionar automaticamente após pagamento</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="config[mercadopago_binary_mode]" value="1" 
                                    <?= get_config_value($configs, 'mercadopago_binary_mode') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Modo Binário</span>
                            </label>
                            <small class="form-hint d-block">Apenas aprovado ou rejeitado (sem pendente)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- URLs de Retorno -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">URLs de Retorno e Webhook</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">URL de Sucesso</label>
                        <input type="url" class="form-control" name="config[mercadopago_url_sucesso]" 
                            value="<?= get_config_value($configs, 'mercadopago_url_sucesso', base_url('orcamento/agradecimento')) ?>" 
                            placeholder="<?= base_url('orcamento/agradecimento') ?>">
                        <small class="form-hint">Página para onde o cliente vai após pagamento aprovado</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL de Pendente</label>
                        <input type="url" class="form-control" name="config[mercadopago_url_pendente]" 
                            value="<?= get_config_value($configs, 'mercadopago_url_pendente', base_url('orcamento/pendente')) ?>" 
                            placeholder="<?= base_url('orcamento/pendente') ?>">
                        <small class="form-hint">Página para pagamento pendente (PIX, boleto)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL de Falha</label>
                        <input type="url" class="form-control" name="config[mercadopago_url_falha]" 
                            value="<?= get_config_value($configs, 'mercadopago_url_falha', base_url('orcamento/erro')) ?>" 
                            placeholder="<?= base_url('orcamento/erro') ?>">
                        <small class="form-hint">Página para pagamento rejeitado</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL do Webhook</label>
                        <input type="url" class="form-control" name="config[mercadopago_url_webhook]" 
                            value="<?= get_config_value($configs, 'mercadopago_url_webhook', base_url('webhook/mercadopago')) ?>" 
                            placeholder="<?= base_url('webhook/mercadopago') ?>">
                        <small class="form-hint">URL para receber notificações de pagamento (configure no painel do MP)</small>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-primary" onclick="testarMercadoPago()">
                            <i class="ti ti-plug me-2"></i>Testar Conexão
                        </button>
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
function testarMercadoPago() {
    // TODO: Implementar teste de conexão via AJAX
    alert('Funcionalidade de teste será implementada na integração do Mercado Pago');
}
</script>
