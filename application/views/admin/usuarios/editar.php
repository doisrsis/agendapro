<!-- Cabeçalho -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><a href="<?= base_url('admin/usuarios') ?>">Usuários</a></div>
                <h2 class="page-title"><i class="ti ti-user-edit me-2"></i>Editar Usuário</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible">
            <div class="d-flex"><div><i class="ti ti-alert-circle icon alert-icon"></i></div>
            <div><?= $this->session->flashdata('erro') ?></div></div>
            <a class="btn-close" data-bs-dismiss="alert"></a>
        </div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados Básicos -->
                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title">Dados do Usuário</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label required">Nome Completo</label>
                                    <input type="text" class="form-control" name="nome" value="<?= $usuario->nome ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" name="whatsapp"
                                           value="<?= $usuario->whatsapp ?>"
                                           placeholder="(XX) XXXXX-XXXX">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">E-mail</label>
                                <input type="email" class="form-control" name="email" value="<?= $usuario->email ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" name="senha" minlength="6">
                                    <small class="form-hint">Deixe em branco para manter a senha atual</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" name="confirmar_senha">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Configurações -->
                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title">Configurações</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Tipo de Usuário</label>
                                <select name="tipo" id="tipo" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="super_admin" <?= $usuario->tipo == 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                    <option value="estabelecimento" <?= $usuario->tipo == 'estabelecimento' ? 'selected' : '' ?>>Estabelecimento</option>
                                    <option value="profissional" <?= $usuario->tipo == 'profissional' ? 'selected' : '' ?>>Profissional</option>
                                </select>
                            </div>

                            <!-- Estabelecimento (condicional) -->
                            <div class="mb-3" id="estabelecimento_field" style="display:<?= in_array($usuario->tipo, ['estabelecimento', 'profissional']) ? 'block' : 'none' ?>;">
                                <label class="form-label required">Estabelecimento</label>
                                <select name="estabelecimento_id" id="estabelecimento_id" class="form-select" <?= in_array($usuario->tipo, ['estabelecimento', 'profissional']) ? 'required' : '' ?>>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estabelecimentos as $est): ?>
                                    <option value="<?= $est->id ?>" <?= $usuario->estabelecimento_id == $est->id ? 'selected' : '' ?>><?= $est->nome_fantasia ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Profissional (condicional) -->
                            <div class="mb-3" id="profissional_field" style="display:<?= $usuario->tipo == 'profissional' ? 'block' : 'none' ?>;">
                                <label class="form-label required">Profissional</label>
                                <select name="profissional_id" id="profissional_id" class="form-select" <?= $usuario->tipo == 'profissional' ? 'required' : '' ?>>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($profissionais as $prof): ?>
                                    <option value="<?= $prof->id ?>" <?= $usuario->profissional_id == $prof->id ? 'selected' : '' ?>><?= $prof->nome ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="ativo" value="1" <?= $usuario->ativo ? 'checked' : '' ?>>
                                    <span class="form-check-label">Usuário Ativo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="ti ti-device-floppy me-2"></i>Salvar Alterações
                            </button>
                            <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary w-100">
                                <i class="ti ti-x me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Controlar exibição de campos baseado no tipo
document.getElementById('tipo').addEventListener('change', function() {
    const tipo = this.value;
    const estabelecimentoField = document.getElementById('estabelecimento_field');
    const profissionalField = document.getElementById('profissional_field');
    const estabelecimentoSelect = document.getElementById('estabelecimento_id');
    const profissionalSelect = document.getElementById('profissional_id');

    // Resetar
    estabelecimentoField.style.display = 'none';
    profissionalField.style.display = 'none';
    estabelecimentoSelect.removeAttribute('required');
    profissionalSelect.removeAttribute('required');

    if (tipo === 'estabelecimento') {
        estabelecimentoField.style.display = 'block';
        estabelecimentoSelect.setAttribute('required', 'required');
    } else if (tipo === 'profissional') {
        estabelecimentoField.style.display = 'block';
        profissionalField.style.display = 'block';
        estabelecimentoSelect.setAttribute('required', 'required');
        profissionalSelect.setAttribute('required', 'required');
    }
});

// Carregar profissionais via AJAX quando selecionar estabelecimento
document.getElementById('estabelecimento_id').addEventListener('change', function() {
    const estabelecimentoId = this.value;
    const profissionalSelect = document.getElementById('profissional_id');
    const currentProfissionalId = '<?= $usuario->profissional_id ?>';

    if (!estabelecimentoId) {
        profissionalSelect.innerHTML = '<option value="">Selecione um estabelecimento primeiro...</option>';
        return;
    }

    // Carregar profissionais
    fetch('<?= base_url('admin/profissionais/get_profissionais/') ?>' + estabelecimentoId)
        .then(response => response.json())
        .then(data => {
            profissionalSelect.innerHTML = '<option value="">Selecione...</option>';
            data.forEach(prof => {
                const selected = prof.id == currentProfissionalId ? 'selected' : '';
                profissionalSelect.innerHTML += `<option value="${prof.id}" ${selected}>${prof.nome}</option>`;
            });
        })
        .catch(error => {
            console.error('Erro ao carregar profissionais:', error);
            profissionalSelect.innerHTML = '<option value="">Erro ao carregar profissionais</option>';
        });
});
</script>
