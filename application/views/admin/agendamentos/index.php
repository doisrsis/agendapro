<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-calendar me-2"></i>
                    Agendamentos
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <!-- Toggle Visualização -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary <?= $view == 'calendario' ? 'active' : '' ?>" id="btn-calendario">
                            <i class="ti ti-calendar"></i> Calendário
                        </button>
                        <button type="button" class="btn btn-outline-primary <?= $view == 'lista' ? 'active' : '' ?>" id="btn-lista">
                            <i class="ti ti-list"></i> Lista
                        </button>
                    </div>

                    <a href="<?= base_url('painel/agendamentos/criar') ?>" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Novo Agendamento
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- Mensagens -->
        <?php if ($this->session->flashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('sucesso') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                <div><?= $this->session->flashdata('erro') ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Hoje</div>
                        </div>
                        <div class="h1 mb-0"><?= $estatisticas['total_hoje'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Confirmados</div>
                        </div>
                        <div class="h1 mb-0 text-success"><?= $estatisticas['confirmados'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pendentes</div>
                        </div>
                        <div class="h1 mb-0 text-warning"><?= $estatisticas['pendentes'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Cancelados</div>
                        </div>
                        <div class="h1 mb-0 text-danger"><?= $estatisticas['cancelados'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visualização Calendário -->
        <div id="view-calendario" style="display: <?= $view == 'calendario' ? 'block' : 'none' ?>;">
            <div class="card">
                <div class="card-body">
                    <?php $this->load->view('admin/agendamentos/_calendario'); ?>
                </div>
            </div>
        </div>

        <!-- Visualização Lista -->
        <div id="view-lista" style="display: <?= $view == 'lista' ? 'block' : 'none' ?>;">
            <?php $this->load->view('admin/agendamentos/_lista'); ?>
        </div>

    </div>
</div>

<script>
// Toggle entre visualizações
document.getElementById('btn-calendario').addEventListener('click', function() {
    document.getElementById('view-calendario').style.display = 'block';
    document.getElementById('view-lista').style.display = 'none';
    this.classList.add('active');
    document.getElementById('btn-lista').classList.remove('active');
    localStorage.setItem('agendamentos_view', 'calendario');
});

document.getElementById('btn-lista').addEventListener('click', function() {
    document.getElementById('view-calendario').style.display = 'none';
    document.getElementById('view-lista').style.display = 'block';
    this.classList.add('active');
    document.getElementById('btn-calendario').classList.remove('active');
    localStorage.setItem('agendamentos_view', 'lista');
});

// Restaurar visualização salva
window.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('agendamentos_view');
    if (savedView === 'calendario') {
        document.getElementById('btn-calendario').click();
    }
});
</script>
