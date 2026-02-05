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
            width: 30px; height: 30px;
            background: #007bff; color: white;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            font-weight: bold; margin-right: 15px;
        }
        .step-row { display: flex; align-items: flex-start; margin-bottom: 2rem; }
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
                        <p class="text-muted mb-4">Siga o passo a passo abaixo para criar sua aplicação e obter o <strong>Access Token</strong> necessário para integração.</p>

                        <!-- Passo 1 -->
                        <div class="step-row">
                            <div class="step-number">1</div>
                            <div>
                                <h4>Acesse o Painel de Desenvolvedores</h4>
                                <p>Faça login na sua conta do Mercado Pago e acesse o painel de desenvolvedores:</p>
                                <a href="https://www.mercadopago.com.br/developers/panel" target="_blank" class="btn btn-outline-primary btn-sm">
                                    Acessar Painel do Desenvolvedor <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5"></path><path d="M10 14l10 -10"></path><path d="M15 4h5v5"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Passo 2 -->
                        <div class="step-row">
                            <div class="step-number">2</div>
                            <div>
                                <h4>Crie uma nova Aplicação</h4>
                                <p>No painel, clique no botão azul <strong>"Criar aplicação"</strong> ou no ícone "+" se estiver no celular.</p>
                            </div>
                        </div>

                        <!-- Passo 3 -->
                        <div class="step-row">
                            <div class="step-number">3</div>
                            <div>
                                <h4>Escolha o Tipo de Integração</h4>
                                <p>Você verá duas opções. Escolha a opção:</p>
                                <div class="card card-sm border-primary">
                                    <div class="card-body">
                                        <h4 class="text-primary mb-1">Mercado Pago - Pagamentos On-line</h4>
                                        <div class="text-muted">Checkout Pro ou Checkout Transparente</div>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-2">
                                    <strong>⚠ Atenção:</strong> Não escolha "Pagamento Presencial" ou "Integração QR Code". O sistema utiliza a API de Pagamentos Online.
                                </div>
                            </div>
                        </div>

                        <!-- Passo 4 -->
                        <div class="step-row">
                            <div class="step-number">4</div>
                            <div>
                                <h4>Preencha os Dados</h4>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>Nome da aplicação:</strong> Coloque o nome da sua barbearia/loja (ex: Barbearia do Mestre).</li>
                                    <li class="mb-2"><strong>Qual produto você está integrando?:</strong> Selecione <code>Checkout Transparente</code>.</li>
                                    <li class="mb-2">Aceite os termos de uso e clique em <strong>Criar Aplicação</strong>.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Passo 5 -->
                        <div class="step-row">
                            <div class="step-number">5</div>
                            <div>
                                <h4>Copie suas Credenciais de Produção</h4>
                                <p>Após criar, entre na aplicação que você acabou de configurar.</p>
                                <ol>
                                    <li>Vá no menu lateral e clique em <strong>Credenciais de Produção</strong>.</li>
                                    <li>Copie o código que aparece no campo <strong>Access Token</strong>.</li>
                                    <li>Cole esse código no sistema AgendaPro no campo <em>Access Token (Produção)</em>.</li>
                                </ol>
                                <div class="alert alert-danger mt-3">
                                    <strong>Importante:</strong> Use sempre as "Credenciais de Produção" para receber pagamentos reais. As "Credenciais de Teste" servem apenas para simulação.
                                </div>
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
