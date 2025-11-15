<div class="progress-container">
    <div class="step-progress">
        <div class="step-progress-bar" style="width: 100%;"></div>
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
        <div class="text-center mb-4">
            <i class="ti ti-clipboard-check" style="font-size: 64px; color: var(--primary-color);"></i>
            <h2 class="mt-3">Resumo do Orçamento</h2>
            <p class="text-muted">Confira todos os dados antes de enviar</p>
        </div>

        <!-- Dados do Cliente -->
        <div class="card mb-3" style="border: 2px solid #e0e0e0; border-radius: 10px;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <h5 class="mb-0"><i class="ti ti-user"></i> Seus Dados</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nome:</strong> <?= $dados['nome'] ?></p>
                        <p><strong>E-mail:</strong> <?= $dados['email'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Telefone:</strong> <?= $dados['telefone'] ?></p>
                        <p><strong>WhatsApp:</strong> <?= $dados['whatsapp'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produto -->
        <div class="card mb-3" style="border: 2px solid #e0e0e0; border-radius: 10px;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <h5 class="mb-0"><i class="ti ti-package"></i> Produto</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <?php if($produto->imagem_principal): ?>
                            <img src="<?= base_url('uploads/produtos/' . $produto->imagem_principal) ?>" 
                                 class="img-fluid rounded" alt="<?= $produto->nome ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <h4><?= $produto->nome ?></h4>
                        <?php if($produto->descricao): ?>
                            <p class="text-muted"><?= $produto->descricao ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tecido e Cor -->
        <div class="card mb-3" style="border: 2px solid #e0e0e0; border-radius: 10px;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <h5 class="mb-0"><i class="ti ti-palette"></i> Tecido e Cor</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tecido:</strong> <?= $tecido->nome ?></p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <strong>Cor:</strong> <?= $cor->nome ?>
                            <span class="ms-2" style="display: inline-block; width: 30px; height: 30px; background: <?= $cor->codigo_hex ?>; border: 2px solid #ddd; border-radius: 50%; vertical-align: middle;"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dimensões -->
        <div class="card mb-3" style="border: 2px solid #e0e0e0; border-radius: 10px;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <h5 class="mb-0"><i class="ti ti-ruler"></i> Dimensões</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Largura:</strong> <?= number_format($dados['largura'], 2, ',', '.') ?>m</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Altura:</strong> <?= number_format($dados['altura'], 2, ',', '.') ?>m</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Extras -->
        <?php if(!empty($extras)): ?>
        <div class="card mb-3 extras-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-plus"></i> Extras Selecionados</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach($extras as $extra): ?>
                        <div class="col-12 col-md-6">
                            <div class="extra-item">
                                <h6 class="mb-1"><?= $extra->nome ?></h6>
                                <?php if(!empty($extra->descricao)): ?>
                                    <p class="small text-muted mb-2"><?= $extra->descricao ?></p>
                                <?php endif; ?>
                                <span class="badge bg-brown-soft">
                                    <?= $extra->tipo_preco === 'fixo' ? 'Valor fixo' : 'Valor por m²' ?>
                                </span>
                                <?php if(isset($extra->valor)): ?>
                                    <div class="extra-price">+ R$ <?= number_format((float)$extra->valor, 2, ',', '.') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Endereço / Retirada -->
        <div class="card mb-3" style="border: 2px solid #e0e0e0; border-radius: 10px;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <h5 class="mb-0">
                    <?php if(isset($dados['tipo_entrega']) && $dados['tipo_entrega'] == 'retirada'): ?>
                        <i class="ti ti-building-store"></i> Retirada no Local
                    <?php else: ?>
                        <i class="ti ti-map-pin"></i> Endereço de Entrega
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if(isset($dados['tipo_entrega']) && $dados['tipo_entrega'] == 'retirada'): ?>
                    <div class="alert alert-success mb-0">
                        <i class="ti ti-check-circle me-2"></i>
                        <strong>Você optou por retirar no local</strong><br>
                        <small class="text-muted">O endereço para retirada será informado pela nossa equipe via WhatsApp.</small>
                    </div>
                <?php else: ?>
                    <p>
                        <?= $dados['endereco'] ?>, <?= $dados['numero'] ?>
                        <?= isset($dados['complemento']) && $dados['complemento'] ? ' - ' . $dados['complemento'] : '' ?><br>
                        <?= $dados['bairro'] ?> - <?= $dados['cidade'] ?>/<?= $dados['estado'] ?><br>
                        CEP: <?= $dados['cep'] ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Valor Total -->
        <div class="card mb-4" style="border: 3px solid var(--primary-color); border-radius: 15px; background: linear-gradient(135deg, rgba(139, 69, 19, 0.05), rgba(210, 105, 30, 0.05));">
            <div class="card-body text-center p-4">
                <h3 class="mb-3">Valor Estimado</h3>
                <h1 class="display-3 mb-0" style="color: var(--primary-color);">
                    R$ <?= number_format($valor_calculado, 2, ',', '.') ?>
                </h1>
                <p class="text-muted mt-2">*Frete não incluso - será calculado pela nossa equipe</p>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="ti ti-info-circle"></i>
            <strong>Próximo Passo:</strong> Ao confirmar, você será redirecionado para o WhatsApp onde nossa equipe finalizará seu atendimento com informações sobre frete e prazo de entrega.
        </div>

        <form method="post">
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('orcamento/etapa8') ?>" class="btn btn-secondary btn-lg">
                    <i class="ti ti-arrow-left me-2"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="ti ti-brand-whatsapp me-2"></i> Enviar para WhatsApp
                </button>
            </div>
        </form>
    </div>
</div>
