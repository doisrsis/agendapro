<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/servicos') ?>">Serviços</a>
                </div>
                <h2 class="page-title">
                    <?= isset($servico) ? 'Editar Serviço' : 'Novo Serviço' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form method="post">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados do Serviço -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Serviço</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Estabelecimento</label>
                                <select class="form-select" name="estabelecimento_id"
                                        <?= isset($servico) ? 'disabled' : '' ?> required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estabelecimentos as $est): ?>
                                    <option value="<?= $est->id ?>"
                                            <?= set_select('estabelecimento_id', $est->id, ($servico->estabelecimento_id ?? '') == $est->id) ?>>
                                        <?= $est->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('estabelecimento_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Nome do Serviço</label>
                                <input type="text" class="form-control" name="nome"
                                       value="<?= set_value('nome', $servico->nome ?? '') ?>"
                                       placeholder="Ex: Corte de Cabelo Masculino" required>
                                <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="descricao" rows="3"
                                          placeholder="Descrição detalhada do serviço..."><?= set_value('descricao', $servico->descricao ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Duração</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="duracao"
                                               value="<?= set_value('duracao', $servico->duracao ?? '') ?>"
                                               min="1" step="5" required>
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <?= form_error('duracao', '<div class="invalid-feedback d-block">', '</div>') ?>
                                    <small class="text-muted">Tempo estimado para realizar o serviço</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Preço</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" name="preco"
                                               value="<?= set_value('preco', $servico->preco ?? '') ?>"
                                               min="0" step="0.01" required>
                                    </div>
                                    <?= form_error('preco', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Status -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Status</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-select" name="status">
                                <option value="ativo" <?= set_select('status', 'ativo', ($servico->status ?? 'ativo') == 'ativo') ?>>Ativo</option>
                                <option value="inativo" <?= set_select('status', 'inativo', ($servico->status ?? '') == 'inativo') ?>>Inativo</option>
                            </select>
                            <small class="text-muted mt-2 d-block">Serviços inativos não aparecem para agendamento</small>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Resumo</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Duração:</small>
                                <div><strong id="resumo-duracao">-</strong></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Preço:</small>
                                <div><strong id="resumo-preco">R$ 0,00</strong></div>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <small class="text-muted">Valor por hora:</small>
                                <div><strong id="resumo-valor-hora">R$ 0,00</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url('admin/servicos') ?>" class="btn btn-link">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>
                        Salvar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const duracaoInput = document.querySelector('input[name="duracao"]');
    const precoInput = document.querySelector('input[name="preco"]');

    function atualizarResumo() {
        const duracao = parseInt(duracaoInput.value) || 0;
        const preco = parseFloat(precoInput.value) || 0;

        document.getElementById('resumo-duracao').textContent = duracao > 0 ? `${duracao} minutos` : '-';
        document.getElementById('resumo-preco').textContent = `R$ ${preco.toFixed(2).replace('.', ',')}`;

        if (duracao > 0 && preco > 0) {
            const valorHora = (preco / duracao) * 60;
            document.getElementById('resumo-valor-hora').textContent = `R$ ${valorHora.toFixed(2).replace('.', ',')}`;
        } else {
            document.getElementById('resumo-valor-hora').textContent = 'R$ 0,00';
        }
    }

    duracaoInput?.addEventListener('input', atualizarResumo);
    precoInput?.addEventListener('input', atualizarResumo);

    // Atualizar resumo inicial
    atualizarResumo();
});
</script>
