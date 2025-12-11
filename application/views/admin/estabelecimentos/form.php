<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/estabelecimentos') ?>">Estabelecimentos</a>
                </div>
                <h2 class="page-title">
                    <?= isset($estabelecimento) ? 'Editar Estabelecimento' : 'Novo Estabelecimento' ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados Básicos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados Básicos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label required">Nome</label>
                                    <input type="text" class="form-control" name="nome"
                                           value="<?= set_value('nome', $estabelecimento->nome ?? '') ?>" required>
                                    <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CNPJ/CPF</label>
                                    <input type="text" class="form-control" name="cnpj_cpf"
                                           value="<?= set_value('cnpj_cpf', $estabelecimento->cnpj_cpf ?? '') ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">E-mail</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= set_value('email', $estabelecimento->email ?? '') ?>" required>
                                    <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                                    <?php if (!isset($estabelecimento)): ?>
                                    <small class="form-hint">Será usado para login do usuário</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" name="whatsapp"
                                           value="<?= set_value('whatsapp', $estabelecimento->whatsapp ?? '') ?>"
                                           placeholder="(XX) XXXXX-XXXX">
                                </div>
                            </div>

                            <?php if (!isset($estabelecimento)): ?>
                            <!-- Campos de senha apenas ao criar -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Senha</label>
                                    <input type="password" class="form-control" name="senha" required minlength="6">
                                    <?= form_error('senha', '<div class="invalid-feedback d-block">', '</div>') ?>
                                    <small class="form-hint">Mínimo 6 caracteres</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Confirmar Senha</label>
                                    <input type="password" class="form-control" name="confirmar_senha" required>
                                    <?= form_error('confirmar_senha', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <!-- Campos de senha ao editar (opcional) -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" name="senha" minlength="6">
                                    <?= form_error('senha', '<div class="invalid-feedback d-block">', '</div>') ?>
                                    <small class="form-hint">Deixe em branco para manter a senha atual</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" name="confirmar_senha">
                                    <?= form_error('confirmar_senha', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Endereço</label>
                                <input type="text" class="form-control" name="endereco"
                                       value="<?= set_value('endereco', $estabelecimento->endereco ?? '') ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CEP</label>
                                    <input type="text" class="form-control" name="cep"
                                           value="<?= set_value('cep', $estabelecimento->cep ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" class="form-control" name="cidade"
                                           value="<?= set_value('cidade', $estabelecimento->cidade ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" name="estado"
                                           value="<?= set_value('estado', $estabelecimento->estado ?? '') ?>"
                                           maxlength="2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Logo -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Logo</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if (isset($estabelecimento) && $estabelecimento->logo): ?>
                            <img src="<?= base_url('uploads/logos/' . $estabelecimento->logo) ?>"
                                 alt="Logo" class="img-fluid mb-3" style="max-height: 200px;">
                            <?php endif; ?>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
                        </div>
                    </div>

                    <!-- Plano e Assinatura -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Plano de Assinatura</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Plano</label>
                                <select class="form-select" name="plano_id" required>
                                    <option value="">Selecione...</option>
                                    <?php
                                    $plano_selecionado = isset($assinatura_atual) && $assinatura_atual ? $assinatura_atual->plano_id : null;
                                    foreach ($planos as $p):
                                    ?>
                                    <option value="<?= $p->id ?>" <?= ($plano_selecionado == $p->id) ? 'selected' : '' ?>>
                                        <?= $p->nome ?> - R$ <?= number_format($p->valor_mensal, 2, ',', '.') ?>/mês
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($estabelecimento)): ?>
                                <small class="form-hint">Alterar o plano criará uma nova assinatura</small>
                                <?php else: ?>
                                <small class="form-hint">Uma assinatura será criada automaticamente</small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="ativo" <?= set_select('status', 'ativo', ($estabelecimento->status ?? 'ativo') == 'ativo') ?>>Ativo</option>
                                    <option value="inativo" <?= set_select('status', 'inativo', ($estabelecimento->status ?? '') == 'inativo') ?>>Inativo</option>
                                    <option value="suspenso" <?= set_select('status', 'suspenso', ($estabelecimento->status ?? '') == 'suspenso') ?>>Suspenso</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tempo Mínimo para Agendamento</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="tempo_minimo_agendamento"
                                           value="<?= set_value('tempo_minimo_agendamento', $estabelecimento->tempo_minimo_agendamento ?? 60) ?>"
                                           min="0">
                                    <span class="input-group-text">minutos</span>
                                </div>
                                <small class="text-muted">Tempo mínimo antes do serviço para permitir agendamento</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="card">
                <div class="card-footer text-end">
                    <a href="<?= base_url('admin/estabelecimentos') ?>" class="btn btn-link">
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
