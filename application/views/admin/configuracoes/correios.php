<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Integrações</div>
                <h2 class="page-title">Configurações - Correios</h2>
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
                        <a class="nav-link active" href="<?= base_url('admin/configuracoes/correios') ?>">
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
        <form method="post" action="<?= base_url('admin/configuracoes/correios') ?>">
            
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
                                        <input class="form-check-input" type="checkbox" name="config[correios_ativo]" value="1" 
                                            <?= get_config_value($configs, 'correios_ativo') == '1' ? 'checked' : '' ?>>
                                        <span class="form-check-label">Ativar cálculo de frete pelos Correios</span>
                                    </label>
                                </div>
                                <small class="form-hint">Desative para usar apenas retirada no local</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Ambiente</label>
                                <select class="form-select" name="config[correios_ambiente]" required>
                                    <option value="teste" <?= get_config_value($configs, 'correios_ambiente') == 'teste' ? 'selected' : '' ?>>Teste</option>
                                    <option value="producao" <?= get_config_value($configs, 'correios_ambiente') == 'producao' ? 'selected' : '' ?>>Produção</option>
                                </select>
                                <small class="form-hint">Use "Teste" enquanto estiver configurando</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credenciais -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Credenciais de Acesso</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Como obter:</strong> Acesse <a href="https://www.correios.com.br/enviar/precisa-de-ajuda/contrato-nacional" target="_blank">Correios - Contrato Nacional</a> e solicite um contrato.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código Administrativo (Usuário)</label>
                                <input type="text" class="form-control" name="config[correios_usuario]" 
                                    value="<?= get_config_value($configs, 'correios_usuario') ?>" 
                                    placeholder="Ex: 12345678">
                                <small class="form-hint">Fornecido pelos Correios ao criar o contrato</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" class="form-control" name="config[correios_senha]" 
                                    value="<?= get_config_value($configs, 'correios_senha') ?>" 
                                    placeholder="••••••••">
                                <small class="form-hint">Senha de acesso ao webservice</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Número do Contrato</label>
                                <input type="text" class="form-control" name="config[correios_contrato]" 
                                    value="<?= get_config_value($configs, 'correios_contrato') ?>" 
                                    placeholder="Ex: 9912345678">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cartão de Postagem</label>
                                <input type="text" class="form-control" name="config[correios_cartao_postagem]" 
                                    value="<?= get_config_value($configs, 'correios_cartao_postagem') ?>" 
                                    placeholder="Ex: 0012345678">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Cálculo -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Configurações de Cálculo</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serviços Disponíveis</label>
                                <input type="text" class="form-control" name="config[correios_servicos]" 
                                    value="<?= get_config_value($configs, 'correios_servicos', 'PAC,SEDEX') ?>" 
                                    placeholder="PAC,SEDEX">
                                <small class="form-hint">Separe por vírgula. Ex: PAC,SEDEX</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prazo Adicional (dias)</label>
                                <input type="number" class="form-control" name="config[correios_prazo_adicional]" 
                                    value="<?= get_config_value($configs, 'correios_prazo_adicional', '0') ?>" 
                                    min="0" step="1">
                                <small class="form-hint">Dias extras para confecção/separação</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Valor Adicional (R$)</label>
                                <input type="number" class="form-control" name="config[correios_valor_adicional]" 
                                    value="<?= get_config_value($configs, 'correios_valor_adicional', '0') ?>" 
                                    min="0" step="0.01">
                                <small class="form-hint">Valor fixo a adicionar no frete</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Percentual Adicional (%)</label>
                                <input type="number" class="form-control" name="config[correios_percentual_adicional]" 
                                    value="<?= get_config_value($configs, 'correios_percentual_adicional', '0') ?>" 
                                    min="0" step="0.1">
                                <small class="form-hint">Percentual a adicionar no frete</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Pacote -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Dimensões do Pacote</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Peso Padrão (kg/m²)</label>
                                <input type="number" class="form-control" name="config[correios_peso_padrao]" 
                                    value="<?= get_config_value($configs, 'correios_peso_padrao', '1') ?>" 
                                    min="0" step="0.1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Comprimento (cm)</label>
                                <input type="number" class="form-control" name="config[correios_comprimento]" 
                                    value="<?= get_config_value($configs, 'correios_comprimento', '30') ?>" 
                                    min="16" max="105">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Largura (cm)</label>
                                <input type="number" class="form-control" name="config[correios_largura]" 
                                    value="<?= get_config_value($configs, 'correios_largura', '20') ?>" 
                                    min="11" max="105">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Altura (cm)</label>
                                <input type="number" class="form-control" name="config[correios_altura]" 
                                    value="<?= get_config_value($configs, 'correios_altura', '10') ?>" 
                                    min="2" max="105">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="config[correios_mao_propria]" value="1" 
                                    <?= get_config_value($configs, 'correios_mao_propria') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Mão Própria</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="config[correios_aviso_recebimento]" value="1" 
                                    <?= get_config_value($configs, 'correios_aviso_recebimento') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Aviso de Recebimento</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="config[correios_valor_declarado]" value="1" 
                                    <?= get_config_value($configs, 'correios_valor_declarado') == '1' ? 'checked' : '' ?>>
                                <span class="form-check-label">Declarar Valor</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-primary" onclick="testarCorreios()">
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
function testarCorreios() {
    // TODO: Implementar teste de conexão via AJAX
    alert('Funcionalidade de teste será implementada na integração dos Correios');
}
</script>
