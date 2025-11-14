<div class="progress-container">
    <div class="step-progress">
        <div class="step-progress-bar"></div>
        <div class="step"><div class="step-circle">1</div><div class="step-label">Dados</div></div>
        <div class="step"><div class="step-circle">2</div><div class="step-label">Atendimento</div></div>
        <div class="step"><div class="step-circle">3</div><div class="step-label">Produto</div></div>
        <div class="step"><div class="step-circle">4</div><div class="step-label">Tecido</div></div>
        <div class="step"><div class="step-circle">5</div><div class="step-label">Largura</div></div>
        <div class="step"><div class="step-circle">6</div><div class="step-label">Altura</div></div>
        <div class="step"><div class="step-circle">7</div><div class="step-label">Extras</div></div>
        <div class="step"><div class="step-circle">8</div><div class="step-label">Endereço</div></div>
    </div>

    <div class="card-form">
        <h2><i class="ti ti-truck-delivery"></i> Forma de Entrega</h2>
        <p class="text-muted mb-4">Escolha como deseja receber seu pedido</p>

        <?php if(validation_errors()): ?>
            <div class="alert alert-danger"><?= validation_errors() ?></div>
        <?php endif; ?>

        <form method="post" id="formEndereco">
            
            <!-- Opção de Entrega -->
            <div class="mb-4">
                <label class="form-label fw-bold">Tipo de Entrega</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipo_entrega" value="entrega" class="form-selectgroup-input" 
                                <?= !isset($dados['tipo_entrega']) || $dados['tipo_entrega'] == 'entrega' ? 'checked' : '' ?>>
                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                <span class="me-3">
                                    <i class="ti ti-truck-delivery" style="font-size: 32px; color: var(--primary-color);"></i>
                                </span>
                                <span class="form-selectgroup-label-content">
                                    <span class="form-selectgroup-title strong mb-1">Entrega no Endereço</span>
                                    <span class="d-block text-muted">Calcularemos o frete</span>
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipo_entrega" value="retirada" class="form-selectgroup-input"
                                <?= isset($dados['tipo_entrega']) && $dados['tipo_entrega'] == 'retirada' ? 'checked' : '' ?>>
                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                <span class="me-3">
                                    <i class="ti ti-building-store" style="font-size: 32px; color: var(--primary-color);"></i>
                                </span>
                                <span class="form-selectgroup-label-content">
                                    <span class="form-selectgroup-title strong mb-1">Retirar no Local</span>
                                    <span class="d-block text-muted">Sem custo de frete</span>
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Endereço para Entrega (condicional) -->
            <div id="camposEndereco">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">CEP *</label>
                    <input type="text" name="cep" id="cep" class="form-control mask-cep" 
                           value="<?= isset($dados['cep']) ? $dados['cep'] : set_value('cep') ?>" 
                           placeholder="00000-000" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Endereço *</label>
                    <input type="text" name="endereco" id="endereco" class="form-control" 
                           value="<?= isset($dados['endereco']) ? $dados['endereco'] : set_value('endereco') ?>" 
                           placeholder="Rua, Avenida..." required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Número *</label>
                    <input type="text" name="numero" class="form-control" 
                           value="<?= isset($dados['numero']) ? $dados['numero'] : set_value('numero') ?>" 
                           placeholder="123" required>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Complemento</label>
                    <input type="text" name="complemento" class="form-control" 
                           value="<?= isset($dados['complemento']) ? $dados['complemento'] : set_value('complemento') ?>" 
                           placeholder="Apto, Bloco...">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Bairro *</label>
                    <input type="text" name="bairro" id="bairro" class="form-control" 
                           value="<?= isset($dados['bairro']) ? $dados['bairro'] : set_value('bairro') ?>" 
                           placeholder="Bairro" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9 mb-3">
                    <label class="form-label">Cidade *</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" 
                           value="<?= isset($dados['cidade']) ? $dados['cidade'] : set_value('cidade') ?>" 
                           placeholder="Cidade" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Estado *</label>
                    <select name="estado" id="estado" class="form-select" required>
                        <option value="">UF</option>
                        <option value="SP" <?= (isset($dados['estado']) && $dados['estado'] == 'SP') ? 'selected' : '' ?>>SP</option>
                        <option value="RJ" <?= (isset($dados['estado']) && $dados['estado'] == 'RJ') ? 'selected' : '' ?>>RJ</option>
                        <option value="MG" <?= (isset($dados['estado']) && $dados['estado'] == 'MG') ? 'selected' : '' ?>>MG</option>
                        <option value="ES" <?= (isset($dados['estado']) && $dados['estado'] == 'ES') ? 'selected' : '' ?>>ES</option>
                        <option value="PR" <?= (isset($dados['estado']) && $dados['estado'] == 'PR') ? 'selected' : '' ?>>PR</option>
                        <option value="SC" <?= (isset($dados['estado']) && $dados['estado'] == 'SC') ? 'selected' : '' ?>>SC</option>
                        <option value="RS" <?= (isset($dados['estado']) && $dados['estado'] == 'RS') ? 'selected' : '' ?>>RS</option>
                    </select>
                </div>
            </div>

            </div><!-- fim camposEndereco -->

            <!-- Informação de Retirada Local -->
            <div id="infoRetirada" style="display: none;">
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="ti ti-building-store me-2"></i>Retirada no Local</h5>
                    <p class="mb-2">Você optou por retirar seu pedido em nossa loja. Sem custo de frete!</p>
                    <hr>
                    <p class="mb-1"><strong>Endereço para retirada:</strong></p>
                    <p class="mb-0">Será informado pela nossa equipe via WhatsApp após confirmação do pedido.</p>
                </div>
            </div>

            <div class="alert alert-info" id="alertFrete">
                <i class="ti ti-info-circle"></i>
                <strong>Importante:</strong> O valor do frete será calculado e informado por nossa equipe via WhatsApp.
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('orcamento/etapa7') ?>" class="btn btn-secondary btn-lg">
                    <i class="ti ti-arrow-left me-2"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    Ver Resumo <i class="ti ti-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const camposEndereco = document.getElementById('camposEndereco');
    const infoRetirada = document.getElementById('infoRetirada');
    const alertFrete = document.getElementById('alertFrete');
    const radios = document.querySelectorAll('input[name="tipo_entrega"]');
    
    // Função para alternar entre entrega e retirada
    function toggleTipoEntrega() {
        const tipoSelecionado = document.querySelector('input[name="tipo_entrega"]:checked').value;
        
        if (tipoSelecionado === 'retirada') {
            // Retirada no local
            camposEndereco.style.display = 'none';
            infoRetirada.style.display = 'block';
            alertFrete.style.display = 'none';
            
            // Remover required dos campos de endereço
            document.querySelectorAll('#camposEndereco input[required], #camposEndereco select[required]').forEach(input => {
                input.removeAttribute('required');
            });
        } else {
            // Entrega no endereço
            camposEndereco.style.display = 'block';
            infoRetirada.style.display = 'none';
            alertFrete.style.display = 'block';
            
            // Adicionar required nos campos obrigatórios
            document.getElementById('cep').setAttribute('required', 'required');
            document.getElementById('endereco').setAttribute('required', 'required');
            document.querySelector('input[name="numero"]').setAttribute('required', 'required');
            document.getElementById('bairro').setAttribute('required', 'required');
            document.getElementById('cidade').setAttribute('required', 'required');
            document.getElementById('estado').setAttribute('required', 'required');
        }
    }
    
    // Event listeners para os radios
    radios.forEach(radio => {
        radio.addEventListener('change', toggleTipoEntrega);
    });
    
    // Executar ao carregar
    toggleTipoEntrega();
    
    // Máscara CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });
        
        // Buscar CEP
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro || '';
                            document.getElementById('bairro').value = data.bairro || '';
                            document.getElementById('cidade').value = data.localidade || '';
                            document.getElementById('estado').value = data.uf || '';
                            document.querySelector('input[name="numero"]').focus();
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar CEP:', error);
                    });
            }
        });
    }
});
</script>
