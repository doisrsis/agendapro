<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Cron Jobs - AgendaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-box {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .info { color: #9cdcfe; }
        .warning { color: #dcdcaa; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">🤖 Teste de Cron Jobs</h1>
                <p class="text-muted">Teste os cron jobs de confirmação e lembretes do sistema</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">📧 Confirmações</h5>
                        <p class="card-text small">Envia pedidos de confirmação para agendamentos pendentes</p>
                        <button class="btn btn-primary w-100" onclick="testarCron('confirmacoes')">
                            Testar Confirmações
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">⏰ Lembretes</h5>
                        <p class="card-text small">Envia lembretes para agendamentos confirmados</p>
                        <button class="btn btn-success w-100" onclick="testarCron('lembretes')">
                            Testar Lembretes
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">🚫 Cancelamentos</h5>
                        <p class="card-text small">Cancela agendamentos não confirmados</p>
                        <button class="btn btn-warning w-100" onclick="testarCron('cancelamentos')">
                            Testar Cancelamentos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Log de Execução</h5>
                        <button class="btn btn-sm btn-secondary" onclick="limparLog()">Limpar</button>
                    </div>
                    <div class="card-body">
                        <div id="logBox" class="log-box">
                            <span class="info">Aguardando execução...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">🔍 Debug - Agendamentos</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info" onclick="verificarAgendamentos()">
                            Verificar Agendamentos Pendentes
                        </button>
                        <button class="btn btn-info ms-2" onclick="verificarConfirmados()">
                            Verificar Agendamentos Confirmados
                        </button>
                        <div id="debugBox" class="log-box mt-3" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = '<?= $token ?>';
        const baseUrl = '<?= base_url() ?>';

        function addLog(message, type = 'info') {
            const logBox = document.getElementById('logBox');
            const timestamp = new Date().toLocaleTimeString('pt-BR');
            const className = type;
            logBox.innerHTML += `\n<span class="${className}">[${timestamp}] ${message}</span>`;
            logBox.scrollTop = logBox.scrollHeight;
        }

        function limparLog() {
            document.getElementById('logBox').innerHTML = '<span class="info">Log limpo. Aguardando execução...</span>';
        }

        async function testarCron(tipo) {
            const urls = {
                'confirmacoes': `${baseUrl}cron/enviar_confirmacoes?token=${token}`,
                'lembretes': `${baseUrl}cron/enviar_lembretes?token=${token}`,
                'cancelamentos': `${baseUrl}cron/cancelar_nao_confirmados?token=${token}`
            };

            const nomes = {
                'confirmacoes': 'Confirmações',
                'lembretes': 'Lembretes',
                'cancelamentos': 'Cancelamentos'
            };

            addLog(`🚀 Iniciando teste: ${nomes[tipo]}`, 'info');
            addLog(`📡 URL: ${urls[tipo]}`, 'info');

            try {
                const response = await fetch(urls[tipo]);
                const data = await response.json();

                if (data.success) {
                    addLog(`✅ Sucesso! Timestamp: ${data.timestamp}`, 'success');
                    addLog(`📊 Resultado: ${JSON.stringify(data.resultado, null, 2)}`, 'success');

                    if (tipo === 'confirmacoes' && data.resultado.confirmacoes_enviadas > 0) {
                        addLog(`📧 ${data.resultado.confirmacoes_enviadas} confirmação(ões) enviada(s)`, 'success');
                    } else if (tipo === 'lembretes' && data.resultado.lembretes_enviados > 0) {
                        addLog(`⏰ ${data.resultado.lembretes_enviados} lembrete(s) enviado(s)`, 'success');
                    } else if (tipo === 'cancelamentos' && data.resultado.cancelados > 0) {
                        addLog(`🚫 ${data.resultado.cancelados} agendamento(s) cancelado(s)`, 'warning');
                    } else {
                        addLog(`ℹ️ Nenhum registro processado (normal se não houver agendamentos elegíveis)`, 'info');
                    }

                    if (data.resultado.erros && data.resultado.erros.length > 0) {
                        addLog(`⚠️ Erros encontrados:`, 'error');
                        data.resultado.erros.forEach(erro => {
                            addLog(`  - ${erro}`, 'error');
                        });
                    }
                } else {
                    addLog(`❌ Falha na execução`, 'error');
                    addLog(`Resposta: ${JSON.stringify(data)}`, 'error');
                }
            } catch (error) {
                addLog(`❌ Erro na requisição: ${error.message}`, 'error');
            }
        }

        async function verificarAgendamentos() {
            const debugBox = document.getElementById('debugBox');
            debugBox.style.display = 'block';
            debugBox.innerHTML = '<span class="info">Carregando...</span>';

            try {
                const response = await fetch(`${baseUrl}cron/debug_agendamentos_pendentes?token=${token}`);
                const data = await response.json();

                debugBox.innerHTML = `<span class="success">Total: ${data.total} agendamento(s) pendente(s)</span>\n`;
                debugBox.innerHTML += JSON.stringify(data.agendamentos, null, 2);
            } catch (error) {
                debugBox.innerHTML = `<span class="error">Erro: ${error.message}</span>`;
            }
        }

        async function verificarConfirmados() {
            const debugBox = document.getElementById('debugBox');
            debugBox.style.display = 'block';
            debugBox.innerHTML = '<span class="info">Carregando...</span>';

            try {
                const response = await fetch(`${baseUrl}cron/debug_agendamentos_confirmados?token=${token}`);
                const data = await response.json();

                debugBox.innerHTML = `<span class="success">Total: ${data.total} agendamento(s) confirmado(s)</span>\n`;
                debugBox.innerHTML += JSON.stringify(data.agendamentos, null, 2);
            } catch (error) {
                debugBox.innerHTML = `<span class="error">Erro: ${error.message}</span>`;
            }
        }
    </script>
</body>
</html>
