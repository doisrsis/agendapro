<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Visão Geral</div>
                <h2 class="page-title">
                    Dashboard
                    <span class="badge bg-blue ms-2">Projeto Base</span>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= base_url('admin/usuarios/criar') ?>" class="btn btn-primary d-none d-sm-inline-block">
                        <i class="ti ti-plus"></i> Novo Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <!-- Cards de Estatísticas -->
        <div class="row row-deck row-cards">
            <!-- Usuários Ativos -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Usuários Ativos</div>
                            <div class="ms-auto lh-1">
                                <i class="ti ti-users text-primary"></i>
                            </div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['usuarios_total'] ?? 0 ?></div>
                        <div class="d-flex mb-2">
                            <div>Total de usuários no sistema</div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: 100%" role="progressbar">
                                <span class="visually-hidden"><?= $stats['usuarios_total'] ?? 0 ?> usuários</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Administradores -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Administradores</div>
                            <div class="ms-auto lh-1">
                                <i class="ti ti-shield-check text-success"></i>
                            </div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['usuarios_admin'] ?? 0 ?></div>
                        <div class="d-flex mb-2">
                            <div>Usuários com nível admin</div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: <?= $stats['usuarios_total'] > 0 ? round(($stats['usuarios_admin'] / $stats['usuarios_total']) * 100) : 0 ?>%" role="progressbar">
                                <span class="visually-hidden"><?= $stats['usuarios_admin'] ?? 0 ?> admins</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuários Este Mês -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Novos Este Mês</div>
                            <div class="ms-auto lh-1">
                                <i class="ti ti-user-plus text-info"></i>
                            </div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['usuarios_mes'] ?? 0 ?></div>
                        <div class="d-flex mb-2">
                            <div>Cadastrados em <?= date('F') ?></div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" style="width: <?= $stats['usuarios_mes'] > 0 ? '100' : '0' ?>%" role="progressbar">
                                <span class="visually-hidden"><?= $stats['usuarios_mes'] ?? 0 ?> novos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notificações -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Notificações</div>
                            <div class="ms-auto lh-1">
                                <i class="ti ti-bell text-warning"></i>
                            </div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['notificacoes_nao_lidas'] ?? 0 ?></div>
                        <div class="d-flex mb-2">
                            <div>Não lidas</div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: <?= $stats['notificacoes_nao_lidas'] > 0 ? '100' : '0' ?>%" role="progressbar">
                                <span class="visually-hidden"><?= $stats['notificacoes_nao_lidas'] ?? 0 ?> não lidas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações e Atividades -->
        <div class="row row-deck row-cards mt-3">
            <!-- Bem-vindo -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-rocket me-2"></i>
                            Bem-vindo ao Projeto Base!
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-info-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">Sistema Pronto para Uso!</h4>
                                    <div class="text-secondary">
                                        Este é um projeto base com todas as funcionalidades essenciais já configuradas.
                                        Você pode começar a desenvolver suas funcionalidades específicas imediatamente!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-3">✅ Funcionalidades Incluídas:</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled space-y-1">
                                    <li><i class="ti ti-check text-success me-2"></i> Sistema de autenticação</li>
                                    <li><i class="ti ti-check text-success me-2"></i> Recuperação de senha</li>
                                    <li><i class="ti ti-check text-success me-2"></i> Gerenciamento de usuários</li>
                                    <li><i class="ti ti-check text-success me-2"></i> Sistema de notificações</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled space-y-1">
                                    <li><i class="ti ti-check text-success me-2"></i> Configurações SMTP dinâmicas</li>
                                    <li><i class="ti ti-check text-success me-2"></i> Sistema de logs</li>
                                    <li><i class="ti ti-check text-success me-2"></i> Interface Tabler Dashboard</li>
                                    <li><i class="ti ti-check text-success me-2"></i> Totalmente responsivo</li>
                                </ul>
                            </div>
                        </div>

                        <hr class="my-3">

                        <h4 class="mb-3">🚀 Próximos Passos:</h4>
                        <div class="steps steps-vertical">
                            <div class="step-item">
                                <div class="h4 m-0">1. Configure o SMTP</div>
                                <div class="text-secondary">Vá em <a href="<?= base_url('admin/configuracoes/smtp') ?>">Configurações > SMTP</a> para ativar o envio de e-mails</div>
                            </div>
                            <div class="step-item">
                                <div class="h4 m-0">2. Personalize o Sistema</div>
                                <div class="text-secondary">Acesse <a href="<?= base_url('admin/configuracoes/geral') ?>">Configurações > Geral</a> para personalizar nome, logo, etc</div>
                            </div>
                            <div class="step-item">
                                <div class="h4 m-0">3. Crie Novos Módulos</div>
                                <div class="text-secondary">Comece a desenvolver suas funcionalidades específicas!</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="<?= base_url('docs/README_PROJETO_BASE.md') ?>" class="btn btn-primary" target="_blank">
                                <i class="ti ti-book me-2"></i>
                                Ver Documentação Completa
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atividades Recentes -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-activity me-2"></i>
                            Atividades Recentes
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($atividades_recentes)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($atividades_recentes as $log): ?>
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <?php
                                            $icon_map = [
                                                'login' => 'ti-login text-success',
                                                'logout' => 'ti-logout text-secondary',
                                                'criar' => 'ti-plus text-primary',
                                                'editar' => 'ti-edit text-info',
                                                'excluir' => 'ti-trash text-danger'
                                            ];
                                            $icon = $icon_map[$log->acao] ?? 'ti-point';
                                            ?>
                                            <span class="avatar avatar-sm">
                                                <i class="ti <?= $icon ?>"></i>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="text-reset d-block text-truncate">
                                                <?= ucfirst($log->acao) ?>
                                                <?php if ($log->tabela): ?>
                                                    em <?= $log->tabela ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-secondary text-truncate mt-n1">
                                                <?= date('d/m/Y H:i', strtotime($log->criado_em)) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty p-4">
                                <div class="empty-icon">
                                    <i class="ti ti-activity"></i>
                                </div>
                                <p class="empty-title">Nenhuma atividade</p>
                                <p class="empty-subtitle text-secondary">
                                    As atividades do sistema aparecerão aqui
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($atividades_recentes)): ?>
                    <div class="card-footer">
                        <a href="<?= base_url('admin/logs') ?>" class="btn btn-link">
                            Ver todos os logs
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Estatísticas Adicionais -->
        <div class="row row-deck row-cards mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-chart-bar me-2"></i>
                            Estatísticas do Sistema
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="text-secondary">Usuários Hoje</div>
                                    <div class="h2 mb-0"><?= $stats['usuarios_hoje'] ?? 0 ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="text-secondary">Logs Hoje</div>
                                    <div class="h2 mb-0"><?= $stats['logs_hoje'] ?? 0 ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="text-secondary">Último Acesso</div>
                                    <div class="h2 mb-0">
                                        <?php if (!empty($stats['ultimo_acesso'])): ?>
                                            <?= date('H:i', strtotime($stats['ultimo_acesso'])) ?>
                                        <?php else: ?>
                                            --:--
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="text-secondary">Status do Sistema</div>
                                    <div class="h2 mb-0">
                                        <span class="badge bg-success">Online</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
