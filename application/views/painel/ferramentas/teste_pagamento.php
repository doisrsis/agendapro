<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Ferramentas
            <small>Teste de Pagamento Real</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('painel/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Teste de Pagamento</li>
        </ol>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Gerar PIX de Teste</h3>
                    </div>
                    <form id="form-teste" onsubmit="return gerarPix(event)">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Estabelecimento</label>
                                <select class="form-control" name="estabelecimento_id" id="estabelecimento_id" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estabelecimentos as $est): ?>
                                        <option value="<?= $est->id; ?>"><?= $est->nome; ?> (ID: <?= $est->id; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Valor (R$)</label>
                                <input type="number" class="form-control" name="valor" id="valor" value="1.00" step="0.01" min="0.01" required>
                                <p class="help-block">Valor baixo para teste real (mínimo R$ 0,01).</p>
                            </div>

                            <div id="resultado" style="display:none; text-align: center; margin-top: 20px; padding: 20px; border: 1px solid #ddd; background: #f9f9f9;">
                                <h4>Pagamento Gerado!</h4>
                                <p><strong>ID Agendamento Fake:</strong> <span id="res-id"></span></p>

                                <img id="res-img" src="" style="width: 200px; height: 200px; margin: 10px auto; display: block;">

                                <div class="form-group">
                                    <label>PIX Copia e Cola:</label>
                                    <textarea class="form-control" id="res-copia" rows="3" readonly></textarea>
                                </div>

                                <div class="alert alert-info">
                                    <i class="icon fa fa-info"></i>
                                    <strong>Agora pague no seu banco!</strong><br>
                                    Assim que pagar, verifique no seu WhatsApp (ou no log do sistema) se a notificação chegou.<br>
                                    O sistema deve processar o Webhook automaticamente.
                                </div>
                            </div>

                            <div id="erro" class="alert alert-danger" style="display:none; margin-top: 10px;"></div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary" id="btn-gerar">
                                <i class="fa fa-qrcode"></i> Gerar PIX
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-question-circle"></i>
                        <h3 class="box-title">Como funciona?</h3>
                    </div>
                    <div class="box-body">
                        <ol>
                            <li>Selecione o estabelecimento que deseja testar (ele deve ter o Mercado Pago configurado).</li>
                            <li>Escolha um valor simbólico (ex: R$ 1,00).</li>
                            <li>Clique em <strong>Gerar PIX</strong>.</li>
                            <li>O sistema criará um <em>Agendamento de Teste</em> (status pendente) e gerará uma cobrança real no Mercado Pago.</li>
                            <li>Use o App do seu banco para pagar o QR Code gerado.</li>
                            <li><strong>Monitoramento:</strong> O Mercado Pago avisará o sistema via Webhook. O sistema deve:
                                <ul>
                                    <li>Confirmar o pagamento.</li>
                                    <li>Enviar a notificação de confirmação no WhatsApp.</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<script>
function gerarPix(e) {
    e.preventDefault();

    var btn = $('#btn-gerar');
    var originalText = btn.html();

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gerando...');
    $('#resultado').hide();
    $('#erro').hide();

    $.ajax({
        url: '<?= base_url("painel/ferramentas/Teste_pagamento/gerar"); ?>',
        method: 'POST',
        data: {
            estabelecimento_id: $('#estabelecimento_id').val(),
            valor: $('#valor').val()
        },
        dataType: 'json',
        success: function(response) {
            btn.prop('disabled', false).html(originalText);

            if (response.error) {
                $('#erro').text(response.error).show();
            } else {
                $('#res-id').text('#' + response.agendamento_id);
                $('#res-img').attr('src', 'data:image/png;base64,' + response.qrcode_base64);
                $('#res-copia').val(response.copia_cola);
                $('#resultado').slideDown();
            }
        },
        error: function() {
            btn.prop('disabled', false).html(originalText);
            $('#erro').text('Erro ao comunicar com o servidor.').show();
        }
    });

    return false;
}
</script>
