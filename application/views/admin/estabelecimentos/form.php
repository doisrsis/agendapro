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
                                    <label class="form-label">E-mail</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= set_value('email', $estabelecimento->email ?? '') ?>">
                                    <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control" name="telefone"
                                           value="<?= set_value('telefone', $estabelecimento->telefone ?? '') ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" name="whatsapp"
                                           value="<?= set_value('whatsapp', $estabelecimento->whatsapp ?? '') ?>">
                                </div>
                            </div>

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

                    <!-- Plano -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Plano e Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Plano</label>
                                <select class="form-select" name="plano" required>
                                    <option value="trimestral" <?= set_select('plano', 'trimestral', ($estabelecimento->plano ?? '') == 'trimestral') ?>>Trimestral</option>
                                    <option value="semestral" <?= set_select('plano', 'semestral', ($estabelecimento->plano ?? '') == 'semestral') ?>>Semestral</option>
                                    <option value="anual" <?= set_select('plano', 'anual', ($estabelecimento->plano ?? '') == 'anual') ?>>Anual</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vencimento do Plano</label>
                                <input type="date" class="form-control" name="plano_vencimento"
                                       value="<?= set_value('plano_vencimento', $estabelecimento->plano_vencimento ?? '') ?>">
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
