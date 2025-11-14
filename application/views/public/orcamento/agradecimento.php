<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-form">
                
                <!-- Mensagem de Sucesso -->
                <div class="text-center mb-4">
                    <div class="mb-4">
                        <i class="ti ti-circle-check" style="font-size: 100px; color: #28a745;"></i>
                    </div>
                    <h1 class="mb-3">Obrigado pela Contratação! 🎉</h1>
                    <p class="lead text-muted">
                        Sua consultoria foi confirmada com sucesso
                    </p>
                </div>

                <!-- Vídeo de Agradecimento -->
                <div class="card mb-4" style="border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden;">
                    <div class="ratio ratio-16x9">
                        <iframe 
                            src="https://www.youtube.com/embed/Bt79lJ7whcg" 
                            title="Le Cortine - Próximos Passos" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- Próximos Passos -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center" style="border: 2px solid #e0e0e0; border-radius: 15px;">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="ti ti-brand-whatsapp" style="font-size: 48px; color: #25D366;"></i>
                                </div>
                                <h5 class="mb-2">1. Contato via WhatsApp</h5>
                                <p class="text-muted small">
                                    Nossa equipe entrará em contato em até 24 horas úteis
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center" style="border: 2px solid #e0e0e0; border-radius: 15px;">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="ti ti-calendar-event" style="font-size: 48px; color: #8B4513;"></i>
                                </div>
                                <h5 class="mb-2">2. Agendamento</h5>
                                <p class="text-muted small">
                                    Vamos agendar a melhor data e horário para você
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center" style="border: 2px solid #e0e0e0; border-radius: 15px;">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="ti ti-file-invoice" style="font-size: 48px; color: #007bff;"></i>
                                </div>
                                <h5 class="mb-2">3. Orçamento</h5>
                                <p class="text-muted small">
                                    Você receberá um orçamento detalhado e personalizado
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações da Consultoria -->
                <div class="card mb-4" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05)); border: 2px solid #28a745; border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="ti ti-info-circle me-2"></i>Informações Importantes</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Confirmação enviada para seu e-mail</li>
                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Número do pedido: <strong>#<?= isset($numero) ? $numero : 'CONS-' . date('YmdHis') ?></strong></li>
                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Valor investido: <strong>R$ 150,00</strong></li>
                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Prazo de contato: <strong>24 horas úteis</strong></li>
                        </ul>
                    </div>
                </div>

                <!-- Dados do Cliente -->
                <?php if(isset($dados) && !empty($dados)): ?>
                <div class="card mb-4" style="border: 2px solid #e0e0e0; border-radius: 15px;">
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

                <!-- Dúvidas -->
                <div class="alert alert-info mb-4">
                    <h6 class="mb-2"><i class="ti ti-help-circle me-2"></i>Alguma dúvida?</h6>
                    <p class="mb-0 small">
                        Entre em contato conosco pelo WhatsApp: <strong>(75) 98889-0006</strong><br>
                        Ou envie um e-mail para: <strong>contato@lecortine.com.br</strong>
                    </p>
                </div>

                <!-- Botões de Ação -->
                <div class="d-grid gap-3">
                    <a href="https://api.whatsapp.com/send?phone=5575988890006&text=<?= urlencode("Olá! Contratei a consultoria e gostaria de mais informações.") ?>" 
                       class="btn btn-success btn-lg" target="_blank">
                        <i class="ti ti-brand-whatsapp me-2"></i> Falar no WhatsApp
                    </a>
                    
                    <a href="<?= base_url() ?>" class="btn btn-outline-primary btn-lg">
                        <i class="ti ti-home me-2"></i> Voltar ao Site
                    </a>
                </div>

                <!-- Rodapé -->
                <div class="text-center mt-5">
                    <p class="text-muted small mb-1">
                        <i class="ti ti-shield-check"></i> Transação 100% segura
                    </p>
                    <p class="text-muted small">
                        Le Cortine - Cortinas Sob Medida<br>
                        www.lecortine.com.br
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
