<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-form">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-4">
                    <i class="ti ti-sparkles" style="font-size: 60px; color: var(--primary-color);"></i>
                    <h1 class="mt-3 mb-2">Consultoria Personalizada</h1>
                    <p class="lead text-muted">
                        Atendimento especializado para projetos únicos
                    </p>
                </div>

                <!-- Vídeo de Apresentação -->
                <div class="card mb-4" style="border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden;">
                    <div class="ratio ratio-16x9">
                        <iframe 
                            src="https://www.youtube.com/embed/Bt79lJ7whcg" 
                            title="Le Cortine - Consultoria" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- Benefícios -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border: 2px solid #e0e0e0; border-radius: 15px;">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="ti ti-check-circle text-success me-2"></i>O que você recebe:</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Análise completa do seu espaço</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Recomendações personalizadas</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Orçamento detalhado</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Suporte técnico especializado</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Acompanhamento pós-venda</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border: 2px solid #e0e0e0; border-radius: 15px;">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="ti ti-info-circle text-info me-2"></i>Ideal para:</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Dimensões especiais (>5m ou >2,80m)</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Projetos complexos</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Toldos e cortinas motorizadas</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Múltiplos ambientes</li>
                                    <li class="mb-2"><i class="ti ti-point-filled text-primary"></i> Soluções corporativas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seus Dados -->
                <?php if(isset($dados) && !empty($dados)): ?>
                <div class="card mb-4" style="background: linear-gradient(135deg, rgba(139, 69, 19, 0.05), rgba(210, 105, 30, 0.05)); border: none; border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="ti ti-user me-2"></i>Seus Dados</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Nome:</strong> <?= $dados['nome'] ?></p>
                                <p class="mb-2"><strong>E-mail:</strong> <?= $dados['email'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Telefone:</strong> <?= $dados['telefone'] ?></p>
                                <p class="mb-2"><strong>WhatsApp:</strong> <?= $dados['whatsapp'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Investimento e CTA -->
                <div class="card mb-4" style="background: linear-gradient(135deg, #8B4513, #D2691E); border: none; border-radius: 15px; color: white;">
                    <div class="card-body text-center p-5">
                        <h3 class="mb-2">Investimento</h3>
                        <div class="display-4 mb-3">R$ 150,00</div>
                        <p class="mb-0 opacity-75">Consultoria completa com especialista</p>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="d-grid gap-3">
                    <?php if(isset($dados) && !empty($dados)): ?>
                        <!-- Botão WhatsApp -->
                        <a href="https://api.whatsapp.com/send?phone=5575988890006&text=<?= urlencode("🎯 *CONSULTORIA PERSONALIZADA*\n\n👤 Nome: {$dados['nome']}\n📧 Email: {$dados['email']}\n📱 Telefone: {$dados['telefone']}\n\nGostaria de contratar a consultoria personalizada!") ?>" 
                           class="btn btn-success btn-lg" target="_blank">
                            <i class="ti ti-brand-whatsapp me-2"></i> Contratar via WhatsApp
                        </a>
                        
                        <!-- Botão Mercado Pago (futuro) -->
                        <button class="btn btn-primary btn-lg" disabled>
                            <i class="ti ti-credit-card me-2"></i> Pagar com Cartão (Em breve)
                        </button>
                    <?php else: ?>
                        <a href="<?= base_url('orcamento') ?>" class="btn btn-primary btn-lg">
                            <i class="ti ti-arrow-right me-2"></i> Iniciar Orçamento
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('orcamento') ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="ti ti-arrow-left me-2"></i> Voltar ao Orçamento
                    </a>
                </div>

                <!-- Informações Adicionais -->
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">
                        <i class="ti ti-clock"></i> Horário de atendimento: Segunda a Sexta, 9h às 18h
                    </p>
                    <p class="text-muted small">
                        <i class="ti ti-shield-check"></i> Pagamento 100% seguro
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
