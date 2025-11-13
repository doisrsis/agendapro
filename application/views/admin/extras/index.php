<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gerenciamento</div>
                <h2 class="page-title">Extras</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/extras/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Novo Extra
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <?php if($this->session->flashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check"></i> <?= $this->session->flashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle"></i> <?= $this->session->flashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="<?= base_url('admin/extras') ?>">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." 
                                   value="<?= isset($filtros['busca']) ? $filtros['busca'] : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="tipo_preco" class="form-select">
                                <option value="">Todos os tipos</option>
                                <option value="fixo" <?= (isset($filtros['tipo_preco']) && $filtros['tipo_preco'] === 'fixo') ? 'selected' : '' ?>>Fixo</option>
                                <option value="percentual" <?= (isset($filtros['tipo_preco']) && $filtros['tipo_preco'] === 'percentual') ? 'selected' : '' ?>>Percentual</option>
                                <option value="por_m2" <?= (isset($filtros['tipo_preco']) && $filtros['tipo_preco'] === 'por_m2') ? 'selected' : '' ?>>Por m²</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="ativo" <?= (isset($filtros['status']) && $filtros['status'] === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= (isset($filtros['status']) && $filtros['status'] === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Extras -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $total ?> <?= $total === 1 ? 'extra encontrado' : 'extras encontrados' ?>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped" id="tabela-extras">
                    <thead>
                        <tr>
                            <th width="50">Ordem</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th width="120">Tipo</th>
                            <th width="120">Valor</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="150" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-extras">
                        <?php if(empty($extras)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="ti ti-inbox" style="font-size: 48px;"></i>
                                    <p class="mt-2">Nenhum extra encontrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($extras as $extra): ?>
                                <tr data-id="<?= $extra->id ?>">
                                    <td>
                                        <i class="ti ti-grip-vertical handle" style="cursor: move;"></i>
                                        <?= $extra->ordem ?>
                                    </td>
                                    <td>
                                        <strong><?= $extra->nome ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= character_limiter($extra->descricao, 80) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $tipo_badges = [
                                            'fixo' => 'bg-blue',
                                            'percentual' => 'bg-green',
                                            'por_m2' => 'bg-orange'
                                        ];
                                        $tipo_labels = [
                                            'fixo' => 'Fixo',
                                            'percentual' => 'Percentual',
                                            'por_m2' => 'Por m²'
                                        ];
                                        ?>
                                        <span class="badge <?= $tipo_badges[$extra->tipo_preco] ?>">
                                            <?= $tipo_labels[$extra->tipo_preco] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($extra->tipo_preco === 'percentual'): ?>
                                            <?= number_format($extra->valor, 2, ',', '.') ?>%
                                        <?php else: ?>
                                            R$ <?= number_format($extra->valor, 2, ',', '.') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <label class="form-check form-switch">
                                            <input class="form-check-input toggle-status" type="checkbox" 
                                                   data-id="<?= $extra->id ?>" 
                                                   <?= $extra->status === 'ativo' ? 'checked' : '' ?>>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/extras/editar/' . $extra->id) ?>" 
                                           class="btn btn-sm btn-icon btn-primary" title="Editar">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-danger btn-deletar" 
                                                data-id="<?= $extra->id ?>" 
                                                data-nome="<?= $extra->nome ?>" 
                                                title="Deletar">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    
    // Drag & Drop para reordenar
    <?php if(!empty($extras)): ?>
    const sortable = new Sortable(document.getElementById('sortable-extras'), {
        handle: '.handle',
        animation: 150,
        onEnd: function(evt) {
            const ordem = [];
            $('#sortable-extras tr[data-id]').each(function() {
                ordem.push($(this).data('id'));
            });
            
            $.ajax({
                url: '<?= base_url('admin/extras/reordenar') ?>',
                method: 'POST',
                data: { ordem: ordem },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }
            });
        }
    });
    <?php endif; ?>
    
    // Toggle Status
    $('.toggle-status').on('change', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        
        $.ajax({
            url: '<?= base_url('admin/extras/toggle_status/') ?>' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message
                    });
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao atualizar status.'
                });
            }
        });
    });
    
    // Deletar
    $('.btn-deletar').on('click', function() {
        const id = $(this).data('id');
        const nome = $(this).data('nome');
        
        Swal.fire({
            title: 'Tem certeza?',
            html: `Deseja realmente deletar o extra <strong>${nome}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/extras/deletar/') ?>' + id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deletado!',
                                text: response.message,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    });
    
});
</script>
