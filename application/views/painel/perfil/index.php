<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-user-circle me-2"></i>
                    Perfil do Estabelecimento
                </h2>
                <div class="text-muted mt-1">Gerencie as informações públicas do seu negócio</div>
            </div>
             <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('painel/dashboard') ?>" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label required">Nome do Estabelecimento</label>
                            <input type="text" class="form-control" name="nome"
                                   value="<?= set_value('nome', $estabelecimento->nome) ?>" required>
                            <?= form_error('nome', '<div class="invalid-feedback d-block">', '</div>') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CNPJ/CPF</label>
                            <input type="text" class="form-control" name="cnpj_cpf"
                                   value="<?= set_value('cnpj_cpf', $estabelecimento->cnpj_cpf ?? '') ?>"
                                   placeholder="00.000.000/0000-00">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" class="form-control" name="whatsapp"
                                   value="<?= set_value('whatsapp', $estabelecimento->whatsapp ?? '') ?>"
                                   placeholder="(XX) XXXXX-XXXX">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">E-mail</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= set_value('email', $estabelecimento->email) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo do Estabelecimento</label>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <?php if (!empty($estabelecimento->logo)): ?>
                                    <span class="avatar avatar-xl" style="background-image: url(<?= base_url('assets/uploads/' . $estabelecimento->logo) ?>)"></span>
                                <?php else: ?>
                                    <span class="avatar avatar-xl"><?= substr($estabelecimento->nome, 0, 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="col">
                                <input type="file" class="form-control" name="logo">
                                <div class="form-text">Formatos: JPG, PNG. Máx: 2MB.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tema Padrão do Linktree</label>
                        <div class="form-selectgroup">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="tema" value="light" class="form-selectgroup-input"
                                    <?= ($estabelecimento->tema ?? 'light') == 'light' ? 'checked' : '' ?>>
                                <span class="form-selectgroup-label">
                                    <i class="ti ti-sun me-1"></i> Claro
                                </span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="tema" value="dark" class="form-selectgroup-input"
                                    <?= ($estabelecimento->tema ?? 'light') == 'dark' ? 'checked' : '' ?>>
                                <span class="form-selectgroup-label">
                                    <i class="ti ti-moon me-1"></i> Escuro
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Endereço (Rua e Número)</label>
                            <input type="text" class="form-control" name="endereco"
                                   value="<?= set_value('endereco', $estabelecimento->endereco ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" class="form-control" name="bairro"
                                   value="<?= set_value('bairro', $estabelecimento->bairro ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" class="form-control" name="cidade"
                                   value="<?= set_value('cidade', $estabelecimento->cidade ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" name="estado"
                                   value="<?= set_value('estado', $estabelecimento->estado ?? '') ?>"
                                   maxlength="2" placeholder="SP">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CEP</label>
                            <input type="text" class="form-control" name="cep"
                                   value="<?= set_value('cep', $estabelecimento->cep ?? '') ?>"
                                   placeholder="00000-000">
                        </div>
                    </div>

                    <div class="hr-text">Página de Links (Linktree)</div>

                    <div class="mb-3">
                        <label class="form-label">Seu Link Personalizado</label>
                        <div class="input-group">
                            <?php
                            // Hack visual do Configurações.php mantido
                            $link_base = base_url('links/');
                            $link_visual = str_replace('gestor.', '', $link_base);
                            ?>
                            <span class="input-group-text"><?= $link_visual ?></span>
                            <input type="text" class="form-control"
                                   value="<?= $estabelecimento->slug ?? 'Será gerado ao salvar' ?>"
                                   readonly id="linkSlug">
                            <button class="btn btn-outline-secondary" type="button" onclick="copiarLink()">
                                <i class="ti ti-copy"></i> Copiar
                            </button>
                        </div>
                        <small class="form-hint">O link é gerado automaticamente pelo sistema.</small>
                    </div>

                    <script>
                    function copiarLink() {
                        var base = "<?= $link_visual ?>";
                        var copyText = base + document.getElementById("linkSlug").value;
                        if(document.getElementById("linkSlug").value === 'Será gerado ao salvar') {
                            alert('Salve as configurações primeiro para gerar o link!');
                            return;
                        }
                        navigator.clipboard.writeText(copyText).then(function() {
                            alert("Link copiado: " + copyText);
                        }, function(err) {
                            console.error('Erro ao copiar: ', err);
                        });
                    }
                    </script>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-instagram"></i></span>
                                <input type="text" class="form-control" name="instagram"
                                       value="<?= set_value('instagram', $estabelecimento->instagram ?? '') ?>"
                                       placeholder="@usuario">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-facebook"></i></span>
                                <input type="text" class="form-control" name="facebook"
                                       value="<?= set_value('facebook', $estabelecimento->facebook ?? '') ?>"
                                       placeholder="Link do perfil">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Website</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-world"></i></span>
                                <input type="text" class="form-control" name="website"
                                       value="<?= set_value('website', $estabelecimento->website ?? '') ?>"
                                       placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-2"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
