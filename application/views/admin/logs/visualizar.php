<!-- Cabeçalho da Página -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="<?= base_url('admin/logs') ?>" class="text-muted">
                        <i class="ti ti-arrow-left me-1"></i>
                        Voltar para Logs
                    </a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-file-text me-2"></i>
                    Detalhes do Log #<?= $log->id ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <!-- Informações Principais -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-info-circle me-2"></i>
                            Informações Principais
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">ID</div>
                                <div class="datagrid-content">#<?= $log->id ?></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Data/Hora</div>
                                <div class="datagrid-content">
                                    <?= date('d/m/Y H:i:s', strtotime($log->criado_em)) ?>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Usuário</div>
                                <div class="datagrid-content">
                                    <?php if ($log->usuario_nome): ?>
                                    <div><?= $log->usuario_nome ?></div>
                                    <div class="text-muted small"><?= $log->usuario_email ?></div>
                                    <?php else: ?>
                                    <span class="text-muted">Sistema</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Ação</div>
                                <div class="datagrid-content">
                                    <?php
                                    $badge_class = 'bg-blue';
                                    switch ($log->acao) {
                                        case 'criar':
                                        case 'insert':
                                            $badge_class = 'bg-green';
                                            break;
                                        case 'editar':
                                        case 'update':
                                            $badge_class = 'bg-yellow';
                                            break;
                                        case 'deletar':
                                        case 'delete':
                                            $badge_class = 'bg-red';
                                            break;
                                        case 'login':
                                            $badge_class = 'bg-cyan';
                                            break;
                                        case 'logout':
                                            $badge_class = 'bg-gray';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= ucfirst($log->acao) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Tabela</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-blue-lt">
                                        <?= ucfirst($log->tabela) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Registro ID</div>
                                <div class="datagrid-content">
                                    <?= $log->registro_id ? '#' . $log->registro_id : '-' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Técnicas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-device-desktop me-2"></i>
                            Informações Técnicas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Endereço IP</div>
                                <div class="datagrid-content">
                                    <code><?= $log->ip ?></code>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">User Agent</div>
                                <div class="datagrid-content">
                                    <small class="text-muted" style="word-break: break-all;">
                                        <?= $log->user_agent ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados Antigos -->
        <?php if ($log->dados_antigos): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-database me-2"></i>
                    Dados Antigos
                </h3>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code><?= json_encode(json_decode($log->dados_antigos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></code></pre>
            </div>
        </div>
        <?php endif; ?>

        <!-- Dados Novos -->
        <?php if ($log->dados_novos): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-database-plus me-2"></i>
                    Dados Novos
                </h3>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code><?= json_encode(json_decode($log->dados_novos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></code></pre>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comparação (se houver dados antigos e novos) -->
        <?php if ($log->dados_antigos && $log->dados_novos): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-git-compare me-2"></i>
                    Alterações Realizadas
                </h3>
            </div>
            <div class="card-body">
                <?php
                $antigos = json_decode($log->dados_antigos, true);
                $novos = json_decode($log->dados_novos, true);
                $diferencas = array_diff_assoc($novos, $antigos);
                ?>
                
                <?php if (empty($diferencas)): ?>
                <div class="text-muted">Nenhuma alteração detectada</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th class="bg-red-lt">Valor Anterior</th>
                                <th class="bg-green-lt">Valor Novo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($diferencas as $campo => $valor_novo): ?>
                            <tr>
                                <td><strong><?= $campo ?></strong></td>
                                <td class="bg-red-lt">
                                    <code><?= $antigos[$campo] ?? '-' ?></code>
                                </td>
                                <td class="bg-green-lt">
                                    <code><?= $valor_novo ?></code>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
