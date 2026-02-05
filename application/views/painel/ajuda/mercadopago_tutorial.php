<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Como Criar App Mercado Pago - AgendaPro</title>
    <!-- Tabler Core -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
        body { background: #f4f6fa; font-family: 'Inter', sans-serif; }
        .step-number {
            width: 30px; height: 30px; min-width: 30px;
            background: #007bff; color: white;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            font-weight: bold; margin-right: 15px;
        }
        .step-row { display: flex; align-items: flex-start; margin-bottom: 3rem; }
        .tutorial-img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 15px;
            border: 1px solid #e6e7e9;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="page-wrapper">
        <div class="page-body">
            <div class="container-xl" style="max-width: 800px;">

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-mercadopago" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M17 10c0 -2.573 -1.423 -5.658 -4.5 -7c-2.433 1.066 -5.5 3.738 -5.5 8c0 2.9 .72 5.093 1.5 6.487c-.636 1.135 -1.94 1.513 -4.5 1.513"></path>
                                <path d="M17 10c2.516 .64 3.5 2.5 3.5 5c0 2.5 -1.5 4.545 -4.5 4.5c-2.457 0 -3.882 -.603 -4.5 -1.636"></path>
                            </svg>
                            Como obter suas Credenciais do Mercado Pago
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="icon ti ti-info-circle me-2"></i>
                            Siga este guia visual para configurar sua integração corretamente.
                        </div>

                        <!-- Passo 1 -->
                        <div class="step-row">
                            <div class="step-number">1</div>
                            <div>
                                <h4>Acesse o Painel e Crie uma Aplicação</h4>
                                <p>Acesse <a href="https://www.mercadopago.com.br/developers/panel" target="_blank">mercadopago.com.br/developers/panel</a>.</p>
                                <p>Clique em <strong>"Criar aplicação"</strong>. Dê um nome para sua aplicação (ex: Nome da sua Barbearia) e clique em Continuar.</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_1_nome.png') ?>" class="tutorial-img" alt="Passo 1">
                            </div>
                        </div>

                        <!-- Passo 2 -->
                        <div class="step-row">
                            <div class="step-number">2</div>
                            <div>
                                <h4>Escolha o Tipo de Pagamento</h4>
                                <p>Selecione a opção <strong>"Pagamentos online"</strong>.</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_2_online.png') ?>" class="tutorial-img" alt="Passo 2">
                            </div>
                        </div>

                        <!-- Passo 3 -->
                        <div class="step-row">
                            <div class="step-number">3</div>
                            <div>
                                <h4>Escolha a Integração</h4>
                                <p>Selecione a opção <strong>"Checkout API"</strong> (Esta é a opção mais flexível que permite nossa integração transparente).</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_3_checkout_api.png') ?>" class="tutorial-img" alt="Passo 3">
                            </div>
                        </div>

                        <!-- Passo 4 -->
                        <div class="step-row">
                            <div class="step-number">4</div>
                            <div>
                                <h4>Selecione o Modelo de Integração</h4>
                                <p>Escolha <strong>"API de Pagamentos"</strong>.</p>
                                <div class="text-muted small">Não escolha "API de Orders".</div>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_4_api_pagamentos.png') ?>" class="tutorial-img" alt="Passo 4">
                            </div>
                        </div>

                        <!-- Passo 5 -->
                        <div class="step-row">
                            <div class="step-number">5</div>
                            <div>
                                <h4>Revise e Confirme</h4>
                                <p>Confira se as opções estão como na imagem abaixo:</p>
                                <ul>
                                    <li>Tipo de pagamento: <strong>Pagamentos online</strong></li>
                                    <li>Solução: <strong>Checkout API</strong></li>
                                    <li>Qual tipo...: <strong>API de Pagamentos</strong></li>
                                </ul>
                                <p>Marque a caixa "Eu autorizo..." e clique em <strong>Confirmar</strong>.</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_5_confirmacao.png') ?>" class="tutorial-img" alt="Passo 5">
                            </div>
                        </div>

                        <!-- Passo 6 -->
                        <div class="step-row">
                            <div class="step-number">6</div>
                            <div>
                                <h4>Acesse as Credenciais de Produção</h4>
                                <p>No menu lateral esquerdo, clique em <strong>"Credenciais de produção"</strong>.</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_6_menu_producao.png') ?>" class="tutorial-img" alt="Passo 6">
                            </div>
                        </div>

                        <!-- Passo 7 -->
                        <div class="step-row">
                            <div class="step-number">7</div>
                            <div>
                                <h4>Ative as Credenciais</h4>
                                <p>Se for solicitado preencher dados do negócio:</p>
                                <ul>
                                    <li><strong>Setor:</strong> Beleza, estética e saúde</li>
                                    <li><strong>Site:</strong> https://gestor.zappagenda.com.br</li>
                                </ul>
                                <p>Marque "Eu autorizo..." e clique em <strong>Ativar credenciais de produção</strong>.</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_7_ativar_producao.png') ?>" class="tutorial-img" alt="Passo 7">
                            </div>
                        </div>

                        <!-- Passo 8 -->
                        <div class="step-row">
                            <div class="step-number">8</div>
                            <div>
                                <h4>Copie as Credenciais</h4>
                                <p>Você precisará de dois códigos:</p>
                                <ol class="mb-2">
                                    <li><strong>Public Key</strong>: Código menor.</li>
                                    <li><strong>Access Token</strong>: Código longo que começa com <code>APP_USR-...</code></li>
                                </ol>
                                <p>Clique no ícone de copiar ao lado de cada um deles.</p>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_8_copiar_token.png') ?>" class="tutorial-img" alt="Passo 8">
                            </div>
                        </div>

                        <!-- Passo 9 (Final) -->
                        <div class="step-row">
                            <div class="step-number">9</div>
                            <div>
                                <h4>Finalize no Sistema</h4>
                                <p>Volte para a tela de configurações do AgendaPro ("Configurações > Mercado Pago") e cole os códigos nos respectivos campos:</p>
                                <ul class="mb-3">
                                    <li>Cole a <strong>Public Key</strong> no campo <em>Public Key (Produção)</em></li>
                                    <li>Cole o <strong>Access Token</strong> no campo <em>Access Token (Produção)</em></li>
                                </ul>
                                <img src="<?= base_url('assets/images/tutorial_mp/step_9_final_config.png') ?>" class="tutorial-img" alt="Passo 9">
                                <p class="mt-3">Depois clique em <strong>Salvar Configurações</strong>.</p>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-end">
                        <button onclick="window.close()" class="btn btn-secondary">Fechar Janela</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
