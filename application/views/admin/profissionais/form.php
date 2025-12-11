<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url(($base_controller ?? 'admin') . '/profissionais') ?>">Profissionais</a>
                </div>
                <h2 class="page-title">
                    <?= isset($profissional) ? 'Editar Profissional' : 'Novo Profissional' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form method="post" enctype="multipart/form-data" id="form-profissional">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados Básicos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados Básicos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($estabelecimentos) && !empty($estabelecimentos)): ?>
                            <div class="mb-3">
                                <label class="form-label required">Estabelecimento</label>
                                <select class="form-select" name="estabelecimento_id" id="estabelecimento_id"
                                        <?= isset($profissional) ? 'disabled' : '' ?> required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estabelecimentos as $est): ?>
                                    <option value="<?= $est->id ?>"
                                            <?= set_select('estabelecimento_id', $est->id, ($profissional->estabelecimento_id ?? '') == $est->id) ?>>
                                        <?= $est->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('estabelecimento_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label required">Nome</label>
                                <input type="text" class="form-control" name="nome"
                                       value="<?= set_value('nome', $profissional->nome ?? '') ?>" required>
                                <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" name="whatsapp"
                                           value="<?= set_value('whatsapp', $profissional->whatsapp ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control" name="telefone"
                                           value="<?= set_value('telefone', $profissional->telefone ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="email"
                                       value="<?= set_value('email', $profissional->email ?? '') ?>">
                                <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Serviços -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Serviços que Realiza</h3>
                        </div>
                        <div class="card-body">
                            <div id="servicos-list">
                                <?php if (empty($servicos)): ?>
                                <p class="text-muted">Selecione um estabelecimento para carregar os serviços</p>
                                <?php else: ?>
                                <?php foreach ($servicos as $servico): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="servicos[]"
                                           value="<?= $servico->id ?>" id="servico-<?= $servico->id ?>"
                                           <?= in_array($servico->id, $servicos_vinculados ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="servico-<?= $servico->id ?>">
                                        <?= $servico->nome ?>
                                        <span class="text-muted">(<?= $servico->duracao ?> min - R$ <?= number_format($servico->preco, 2, ',', '.') ?>)</span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Foto -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Foto</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if (isset($profissional) && $profissional->foto): ?>
                            <img src="<?= base_url('uploads/profissionais/' . $profissional->foto) ?>"
                                 alt="Foto" class="img-fluid mb-3" style="max-height: 200px;">
                            <?php endif; ?>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Status</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-select" name="status">
                                <option value="ativo" <?= set_select('status', 'ativo', ($profissional->status ?? 'ativo') == 'ativo') ?>>Ativo</option>
                                <option value="inativo" <?= set_select('status', 'inativo', ($profissional->status ?? '') == 'inativo') ?>>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url(($base_controller ?? 'admin') . '/profissionais') ?>" class="btn btn-link">
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
    const estabelecimentoSelect = document.getElementById('estabelecimento_id');
    const servicosList = document.getElementById('servicos-list');

    estabelecimentoSelect?.addEventListener('change', function() {
        const estabelecimentoId = this.value;

        if (estabelecimentoId) {
            fetch(`<?= base_url('admin/profissionais/get_servicos/') ?>${estabelecimentoId}`)
                .then(r => r.json())
                .then(servicos => {
                    if (servicos.length === 0) {
                        servicosList.innerHTML = '<p class="text-muted">Nenhum serviço cadastrado para este estabelecimento</p>';
                    } else {
                        servicosList.innerHTML = '';
                        servicos.forEach(s => {
                            servicosList.innerHTML += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="servicos[]"
                                           value="${s.id}" id="servico-${s.id}">
                                    <label class="form-check-label" for="servico-${s.id}">
                                        ${s.nome}
                                        <span class="text-muted">(${s.duracao} min - R$ ${parseFloat(s.preco).toFixed(2).replace('.', ',')})</span>
                                    </label>
                                </div>
                            `;
                        });
                    }
                });
        } else {
            servicosList.innerHTML = '<p class="text-muted">Selecione um estabelecimento para carregar os serviços</p>';
        }
    });
});
</script>
