<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/clientes') ?>">Clientes</a> / 
                    <a href="<?= base_url('admin/clientes/visualizar/' . $cliente->id) ?>"><?= $cliente->nome ?></a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-edit me-2"></i>
                    Editar Cliente
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('admin/clientes/visualizar/' . $cliente->id) ?>" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- Mensagens -->
        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('erro') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form method="post" action="<?= base_url('admin/clientes/editar/' . $cliente->id) ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Dados Pessoais -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Dados Pessoais</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label required">Nome Completo</label>
                                        <input type="text" class="form-control" name="nome" 
                                               value="<?= $cliente->nome ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">CPF/CNPJ</label>
                                        <input type="text" class="form-control" name="cpf_cnpj" 
                                               value="<?= $cliente->cpf_cnpj ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">E-mail</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?= $cliente->email ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label required">Telefone</label>
                                        <input type="text" class="form-control" name="telefone" 
                                               value="<?= $cliente->telefone ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">WhatsApp</label>
                                        <input type="text" class="form-control" name="whatsapp" 
                                               value="<?= $cliente->whatsapp ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Endereço</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">CEP</label>
                                        <input type="text" class="form-control" name="cep" 
                                               value="<?= $cliente->cep ?>" 
                                               placeholder="00000-000">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <label class="form-label">Endereço Completo</label>
                                        <input type="text" class="form-control" name="endereco" 
                                               value="<?= $cliente->endereco ?>" 
                                               placeholder="Rua, Número, Bairro">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Cidade</label>
                                        <input type="text" class="form-control" name="cidade" 
                                               value="<?= $cliente->cidade ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Estado (UF)</label>
                                        <input type="text" class="form-control" name="estado" 
                                               value="<?= $cliente->estado ?>" 
                                               placeholder="BA" maxlength="2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Observações</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea class="form-control" name="observacoes" rows="4" 
                                          placeholder="Informações adicionais sobre o cliente..."><?= $cliente->observacoes ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Ações -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Ações</h3>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="ti ti-device-floppy me-2"></i>
                                Salvar Alterações
                            </button>
                            <a href="<?= base_url('admin/clientes/visualizar/' . $cliente->id) ?>" 
                               class="btn btn-outline-secondary w-100">
                                <i class="ti ti-x me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>

                    <!-- Informações -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informações</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Cadastrado em:</small><br>
                                <strong><?= date('d/m/Y H:i', strtotime($cliente->criado_em)) ?></strong>
                            </div>
                            <?php if ($cliente->atualizado_em): ?>
                            <div class="mb-0">
                                <small class="text-muted">Última atualização:</small><br>
                                <strong><?= date('d/m/Y H:i', strtotime($cliente->atualizado_em)) ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
