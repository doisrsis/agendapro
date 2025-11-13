<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gerenciamento</div>
                <h2 class="page-title">Tabela de Preços</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('admin/precos/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Novo Preço
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <?php if($this->session->flashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-check"></i> <?= $this->session->flashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="get">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="busca" class="form-control" placeholder="Buscar..." 
                                   value="<?= isset($filtros['busca']) ? $filtros['busca'] : '' ?>">
                        </div>
                        <div class="col-md-5">
                            <select name="produto_id" class="form-select">
                                <option value="">Todos os produtos</option>
                                <?php foreach($produtos as $produto): ?>
                                    <option value="<?= $produto->id ?>" 
                                            <?= (isset($filtros['produto_id']) && $filtros['produto_id'] == $produto->id) ? 'selected' : '' ?>>
                                        <?= $produto->nome ?>
                                    </option>
                                <?php endforeach; ?>
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

        <!-- Tabela -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= $total ?> <?= $total === 1 ? 'preço' : 'preços' ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th width="150">Largura (m)</th>
                            <th width="150">Altura (m)</th>
                            <th width="120">Preço m²</th>
                            <th width="120">Preço ml</th>
                            <th width="120">Preço Fixo</th>
                            <th width="120" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($precos)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="ti ti-inbox" style="font-size: 48px;"></i>
                                    <p class="mt-2">Nenhum preço cadastrado</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($precos as $preco): ?>
                                <tr>
                                    <td>
                                        <strong><?= $preco->produto_nome ?></strong>
                                        <?php if($preco->observacoes): ?>
                                            <br><small class="text-muted"><?= character_limiter($preco->observacoes, 50) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format($preco->largura_min, 2, ',', '.') ?> - <?= number_format($preco->largura_max, 2, ',', '.') ?></td>
                                    <td><?= number_format($preco->altura_min, 2, ',', '.') ?> - <?= number_format($preco->altura_max, 2, ',', '.') ?></td>
                                    <td><?= $preco->preco_m2 ? 'R$ ' . number_format($preco->preco_m2, 2, ',', '.') : '-' ?></td>
                                    <td><?= $preco->preco_ml ? 'R$ ' . number_format($preco->preco_ml, 2, ',', '.') : '-' ?></td>
                                    <td><?= $preco->preco_fixo ? 'R$ ' . number_format($preco->preco_fixo, 2, ',', '.') : '-' ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/precos/editar/' . $preco->id) ?>" 
                                           class="btn btn-sm btn-icon btn-primary" title="Editar">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-danger btn-deletar" 
                                                data-id="<?= $preco->id ?>" title="Deletar">
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
    $('.btn-deletar').on('click', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Tem certeza?',
            text: 'Deseja realmente deletar este preço?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/precos/deletar/') ?>' + id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deletado!',
                                text: response.message,
                                timer: 2000
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
