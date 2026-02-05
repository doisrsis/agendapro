<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-settings me-2"></i>
                    Configurações
                </h2>
                <div class="text-muted mt-1">Gerencie as configurações do seu estabelecimento</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <!-- Abas -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="?aba=geral" class="nav-link <?= $aba_ativa == 'geral' ? 'active' : '' ?>">
                            <i class="ti ti-building me-2"></i>
                            Dados Gerais
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?aba=agendamento" class="nav-link <?= $aba_ativa == 'agendamento' ? 'active' : '' ?>">
                            <i class="ti ti-calendar me-2"></i>
                            Agendamento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?aba=whatsapp" class="nav-link <?= $aba_ativa == 'whatsapp' ? 'active' : '' ?>">
                            <i class="ti ti-brand-whatsapp me-2"></i>
                            WhatsApp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?aba=mercadopago" class="nav-link <?= $aba_ativa == 'mercadopago' ? 'active' : '' ?>">
                            <i class="ti ti-credit-card me-2"></i>
                            Mercado Pago
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?aba=notificacoes_profissional" class="nav-link <?= $aba_ativa == 'notificacoes_profissional' ? 'active' : '' ?>">
                            <i class="ti ti-bell me-2"></i>
                            Notificações Profissional
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    <!-- Aba Dados Gerais -->
                    <?php if ($aba_ativa == 'geral'): ?>
                    <div class="tab-pane active">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="aba" value="geral">

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
                                    // Hack visual: Se estiver no gestor, mostramos o domínio principal para ficar bonito
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

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- Aba Agendamento -->
                    <?php if ($aba_ativa == 'agendamento'): ?>
                    <?php
                    // Incluir a nova versão reorganizada com accordion
                    include(__DIR__ . '/agendamento_novo.php');
                    ?>
                    <?php endif; ?>

                    <!-- Aba Agendamento (VERSÃO ANTIGA - BACKUP) -->
                    <?php if (false && $aba_ativa == 'agendamento'): ?>
                    <div class="tab-pane active">
                        <form method="post">
                            <input type="hidden" name="aba" value="agendamento">

                            <h3 class="mb-3">Horários de Funcionamento</h3>
                            <p class="text-muted">Defina os horários de funcionamento do estabelecimento por dia da semana</p>

                            <div class="table-responsive mb-4">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Dia da Semana</th>
                                            <th class="text-center" width="80">Ativo</th>
                                            <th width="120">Abertura</th>
                                            <th width="120">Fechamento</th>
                                            <th class="text-center" width="80">Almoço</th>
                                            <th width="120">Início Almoço</th>
                                            <th width="120">Fim Almoço</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Converter array de horários para facilitar acesso
                                        $horarios_array = [];
                                        foreach ($horarios as $h) {
                                            $horarios_array[$h->dia_semana] = $h;
                                        }

                                        foreach ($dias_semana as $dia => $nome):
                                            $horario = $horarios_array[$dia] ?? null;
                                        ?>
                                        <tr>
                                            <td><strong><?= $nome ?></strong></td>
                                            <td class="text-center">
                                                <label class="form-check form-switch mb-0">
                                                    <input type="checkbox" class="form-check-input"
                                                           name="dia_<?= $dia ?>_ativo" value="1"
                                                           <?= ($horario && $horario->ativo) ? 'checked' : '' ?>>
                                                </label>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control"
                                                       name="dia_<?= $dia ?>_inicio"
                                                       value="<?= $horario ? substr($horario->hora_inicio, 0, 5) : '08:00' ?>">
                                            </td>
                                            <td>
                                                <input type="time" class="form-control"
                                                       name="dia_<?= $dia ?>_fim"
                                                       value="<?= $horario ? substr($horario->hora_fim, 0, 5) : '18:00' ?>">
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" name="dia_<?= $dia ?>_almoco_ativo" value="0">
                                                <label class="form-check form-switch mb-0">
                                                    <input type="checkbox" class="form-check-input"
                                                           name="dia_<?= $dia ?>_almoco_ativo" value="1"
                                                           <?= ($horario && $horario->almoco_ativo) ? 'checked' : '' ?>>
                                                </label>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control form-control-sm"
                                                       name="dia_<?= $dia ?>_almoco_inicio"
                                                       value="<?= $horario && $horario->almoco_inicio ? substr($horario->almoco_inicio, 0, 5) : '12:00' ?>">
                                            </td>
                                            <td>
                                                <input type="time" class="form-control form-control-sm"
                                                       name="dia_<?= $dia ?>_almoco_fim"
                                                       value="<?= $horario && $horario->almoco_fim ? substr($horario->almoco_fim, 0, 5) : '13:00' ?>">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Configurações de Agendamento</h3>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempo Mínimo para Agendamento</label>
                                    <select class="form-select" name="tempo_minimo_agendamento">
                                        <option value="0" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 0 ? 'selected' : '' ?>>Imediato</option>
                                        <option value="30" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 30 ? 'selected' : '' ?>>30 minutos</option>
                                        <option value="60" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 60 ? 'selected' : '' ?>>1 hora</option>
                                        <option value="120" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 120 ? 'selected' : '' ?>>2 horas</option>
                                        <option value="240" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 240 ? 'selected' : '' ?>>4 horas</option>
                                        <option value="1440" <?= ($estabelecimento->tempo_minimo_agendamento ?? 60) == 1440 ? 'selected' : '' ?>>1 dia</option>
                                    </select>
                                    <small class="text-muted">Antecedência mínima para cliente agendar</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Período de Abertura da Agenda</label>
                                    <select class="form-select" name="dias_antecedencia_agenda">
                                        <option value="0" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 0 ? 'selected' : '' ?>>
                                            Sem limite
                                        </option>
                                        <option value="7" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 7 ? 'selected' : '' ?>>
                                            1 semana (7 dias)
                                        </option>
                                        <option value="15" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 15 ? 'selected' : '' ?>>
                                            Quinzenal (15 dias)
                                        </option>
                                        <option value="30" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 30 ? 'selected' : '' ?>>
                                            Mensal (30 dias)
                                        </option>
                                        <option value="60" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 60 ? 'selected' : '' ?>>
                                            Bimestral (60 dias)
                                        </option>
                                        <option value="90" <?= ($estabelecimento->dias_antecedencia_agenda ?? 30) == 90 ? 'selected' : '' ?>>
                                            Trimestral (90 dias)
                                        </option>
                                    </select>
                                    <small class="text-muted">Quantos dias para frente o cliente pode agendar</small>
                                </div>
                            </div>

                            <!-- Intervalo de Horários -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <!-- Hidden field para garantir que sempre envie um valor -->
                                    <input type="hidden" name="usar_intervalo_fixo" value="0">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="usar_intervalo_fixo"
                                               id="usar_intervalo_fixo"
                                               value="1"
                                               <?= ($estabelecimento->usar_intervalo_fixo ?? 1) ? 'checked' : '' ?>>
                                        <span class="form-check-label">
                                            <strong>Usar Intervalo Fixo</strong>
                                        </span>
                                    </label>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ti ti-info-circle me-1"></i>
                                        <strong>Ativado:</strong> Todos os serviços usarão o mesmo intervalo configurado abaixo.<br>
                                        <i class="ti ti-info-circle me-1"></i>
                                        <strong>Desativado:</strong> O intervalo será calculado automaticamente baseado na duração de cada serviço.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3" id="campo-intervalo">
                                    <label class="form-label">Intervalo de Horários</label>
                                    <select class="form-select" name="intervalo_agendamento">
                                        <option value="5" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 5 ? 'selected' : '' ?>>
                                            5 minutos
                                        </option>
                                        <option value="10" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 10 ? 'selected' : '' ?>>
                                            10 minutos
                                        </option>
                                        <option value="15" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 15 ? 'selected' : '' ?>>
                                            15 minutos
                                        </option>
                                        <option value="30" <?= ($estabelecimento->intervalo_agendamento ?? 30) == 30 ? 'selected' : '' ?>>
                                            30 minutos (padrão)
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        Intervalo entre horários disponíveis para agendamento
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="confirmacao_automatica"
                                           <?= ($estabelecimento->confirmacao_automatica ?? 0) ? 'checked' : '' ?>>
                                    <span class="form-check-label">Confirmação Automática de Agendamentos</span>
                                </label>
                                <small class="text-muted d-block">Agendamentos serão confirmados automaticamente sem necessidade de aprovação manual</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="permite_reagendamento" id="permite_reagendamento" value="1"
                                           <?= ($estabelecimento->permite_reagendamento ?? 1) ? 'checked' : '' ?>>
                                    <span class="form-check-label">Permitir Reagendamento</span>
                                </label>
                                <small class="text-muted d-block">Clientes podem reagendar seus próprios agendamentos</small>
                            </div>

                            <div class="row" id="limite_reagendamentos_container" style="display: <?= ($estabelecimento->permite_reagendamento ?? 1) ? 'block' : 'none' ?>">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Limite de Reagendamentos</label>
                                    <input type="number" class="form-control" name="limite_reagendamentos"
                                           value="<?= $estabelecimento->limite_reagendamentos ?? 3 ?>" min="1" max="10">
                                    <small class="text-muted">Quantidade máxima de vezes que o cliente pode reagendar</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Pagamento de Agendamentos -->
                            <h3 class="mb-3">Pagamento de Agendamentos</h3>

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Importante:</strong> Configure suas credenciais do Mercado Pago na aba "Mercado Pago" antes de ativar o pagamento de agendamentos.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Exigir Pagamento para Confirmar Agendamento</label>
                                <select class="form-select" name="agendamento_requer_pagamento" id="agendamento_requer_pagamento">
                                    <option value="nao" <?= ($estabelecimento->agendamento_requer_pagamento ?? 'nao') == 'nao' ? 'selected' : '' ?>>
                                        Não exigir pagamento
                                    </option>
                                    <option value="valor_total" <?= ($estabelecimento->agendamento_requer_pagamento ?? 'nao') == 'valor_total' ? 'selected' : '' ?>>
                                        Valor total do serviço
                                    </option>
                                    <option value="taxa_fixa" <?= ($estabelecimento->agendamento_requer_pagamento ?? 'nao') == 'taxa_fixa' ? 'selected' : '' ?>>
                                        Taxa fixa de agendamento
                                    </option>
                                </select>
                                <small class="text-muted">
                                    Escolha se deseja cobrar via PIX para confirmar agendamentos
                                </small>
                            </div>

                            <div class="mb-3" id="taxa-fixa-container" style="display: none;">
                                <label class="form-label">Valor da Taxa de Agendamento</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="agendamento_taxa_fixa"
                                           value="<?= $estabelecimento->agendamento_taxa_fixa ?? '10.00' ?>"
                                           step="0.01" min="0" placeholder="10.00">
                                </div>
                                <small class="text-muted">
                                    Valor fixo que será cobrado para reservar o horário
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempo de Expiração do PIX</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="agendamento_tempo_expiracao_pix"
                                               value="<?= $estabelecimento->agendamento_tempo_expiracao_pix ?? 30 ?>"
                                               min="5" max="60">
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <small class="text-muted">
                                        Tempo inicial que o cliente tem para pagar o PIX
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempo Adicional após Expiração</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="agendamento_tempo_adicional_pix"
                                               value="<?= $estabelecimento->agendamento_tempo_adicional_pix ?? 5 ?>"
                                               min="0" max="30">
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <small class="text-muted">
                                        Tempo extra após enviar lembrete de pagamento (0 = desativado)
                                    </small>
                                </div>
                            </div>

                            <div class="alert alert-secondary mb-3">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Como funciona:</strong> Quando o tempo inicial expirar, o cliente receberá uma notificação no WhatsApp
                                com um link para pagar. Ele terá o tempo adicional configurado para concluir o pagamento antes do agendamento ser cancelado.
                            </div>

                            <script>
                            // Mostrar/ocultar campo de taxa fixa
                            document.getElementById('agendamento_requer_pagamento').addEventListener('change', function() {
                                document.getElementById('taxa-fixa-container').style.display =
                                    this.value === 'taxa_fixa' ? 'block' : 'none';
                            });

                            // Executar ao carregar a página
                            if (document.getElementById('agendamento_requer_pagamento').value === 'taxa_fixa') {
                                document.getElementById('taxa-fixa-container').style.display = 'block';
                            }
                            </script>

                            <hr class="my-4">

                            <!-- Confirmações e Lembretes -->
                            <h3 class="mb-3">📋 Confirmações e Lembretes</h3>

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Novo:</strong> Sistema automático de confirmação e lembretes via WhatsApp para agendamentos sem pagamento obrigatório.
                            </div>

                            <!-- Solicitação de Confirmação -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">✅ Solicitação de Confirmação</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   name="solicitar_confirmacao"
                                                   id="solicitar_confirmacao"
                                                   value="1"
                                                   <?= ($estabelecimento->solicitar_confirmacao ?? 1) ? 'checked' : '' ?>>
                                            <span class="form-check-label">
                                                <strong>Solicitar confirmação do cliente antes do agendamento</strong>
                                            </span>
                                        </label>
                                        <small class="text-muted d-block mt-1">
                                            Quando ativado, o cliente receberá uma mensagem para confirmar presença e poderá reagendar ou cancelar.
                                        </small>
                                    </div>

                                    <div id="confirmacao_opcoes" style="display: <?= ($estabelecimento->solicitar_confirmacao ?? 1) ? 'block' : 'none' ?>">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Solicitar quantas horas antes?</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="confirmacao_horas_antes"
                                                           value="<?= $estabelecimento->confirmacao_horas_antes ?? 24 ?>"
                                                           min="1" max="168">
                                                    <span class="input-group-text">horas</span>
                                                </div>
                                                <small class="text-muted">Exemplo: 24 = 1 dia antes do agendamento</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="confirmacao_dia_anterior"
                                                           id="confirmacao_dia_anterior"
                                                           value="1"
                                                           <?= ($estabelecimento->confirmacao_dia_anterior ?? 1) ? 'checked' : '' ?>>
                                                    <span class="form-check-label">
                                                        <strong>Enviar também no dia anterior</strong>
                                                    </span>
                                                </label>
                                                <small class="text-muted d-block mt-1">
                                                    Envia um pedido de confirmação no dia anterior em horário fixo
                                                </small>
                                            </div>
                                        </div>

                                        <div class="row" id="horario_dia_anterior_container" style="display: <?= ($estabelecimento->confirmacao_dia_anterior ?? 1) ? 'block' : 'none' ?>">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Horário para enviar no dia anterior</label>
                                                <input type="time" class="form-control" name="confirmacao_horario_dia_anterior"
                                                       value="<?= $estabelecimento->confirmacao_horario_dia_anterior ?? '18:00:00' ?>">
                                                <small class="text-muted">Horário fixo para enviar a confirmação</small>
                                            </div>
                                        </div>

                                        <hr class="my-4">
                                        <h5 class="mb-3">🔄 Tentativas Múltiplas de Confirmação</h5>
                                        <p class="text-muted small">Configure quantas vezes o sistema tentará confirmar com o cliente no dia anterior</p>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Máximo de tentativas</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="confirmacao_max_tentativas"
                                                           value="<?= $estabelecimento->confirmacao_max_tentativas ?? 3 ?>"
                                                           min="1" max="5">
                                                    <span class="input-group-text">vezes</span>
                                                </div>
                                                <small class="text-muted">Quantas vezes tentar antes de cancelar (padrão: 3)</small>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Intervalo entre tentativas</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="confirmacao_intervalo_tentativas_minutos"
                                                           value="<?= $estabelecimento->confirmacao_intervalo_tentativas_minutos ?? 30 ?>"
                                                           min="15" max="180">
                                                    <span class="input-group-text">minutos</span>
                                                </div>
                                                <small class="text-muted">Tempo de espera entre cada tentativa (padrão: 30min)</small>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Cancelar automaticamente?</label>
                                                <select class="form-select" name="confirmacao_cancelar_automatico">
                                                    <option value="sim" <?= ($estabelecimento->confirmacao_cancelar_automatico ?? 'sim') == 'sim' ? 'selected' : '' ?>>Sim</option>
                                                    <option value="nao" <?= ($estabelecimento->confirmacao_cancelar_automatico ?? 'sim') == 'nao' ? 'selected' : '' ?>>Não</option>
                                                </select>
                                                <small class="text-muted">Cancelar após todas as tentativas sem resposta</small>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="ti ti-info-circle me-2"></i>
                                            <strong>Exemplo de fluxo (intervalo de 30 minutos):</strong>
                                            <ul class="mb-0 mt-2">
                                                <li><strong>19:00</strong> - 1ª tentativa (mensagem padrão)</li>
                                                <li><strong>19:30</strong> - 2ª tentativa (mensagem urgente) <em>se não respondeu</em></li>
                                                <li><strong>20:00</strong> - 3ª tentativa (última chance - aviso de cancelamento) <em>se não respondeu</em></li>
                                                <li><strong>20:30</strong> - Cancelamento automático <em>se não respondeu</em></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lembrete Pré-Atendimento -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">⏰ Lembrete Pré-Atendimento</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   name="enviar_lembrete_pre_atendimento"
                                                   id="enviar_lembrete_pre_atendimento"
                                                   value="1"
                                                   <?= ($estabelecimento->enviar_lembrete_pre_atendimento ?? 1) ? 'checked' : '' ?>>
                                            <span class="form-check-label">
                                                <strong>Enviar lembrete minutos antes do atendimento</strong>
                                            </span>
                                        </label>
                                        <small class="text-muted d-block mt-1">
                                            Cliente receberá um lembrete próximo ao horário do agendamento
                                        </small>
                                    </div>

                                    <div id="lembrete_opcoes" style="display: <?= ($estabelecimento->enviar_lembrete_pre_atendimento ?? 1) ? 'block' : 'none' ?>">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Enviar quantos minutos antes?</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="lembrete_minutos_antes"
                                                           value="<?= $estabelecimento->lembrete_minutos_antes ?? 60 ?>"
                                                           min="5" max="1440">
                                                    <span class="input-group-text">minutos</span>
                                                </div>
                                                <small class="text-muted">Exemplo: 60 = 1 hora antes</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Pedir para chegar com antecedência de:</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="lembrete_antecedencia_chegada"
                                                           value="<?= $estabelecimento->lembrete_antecedencia_chegada ?? 10 ?>"
                                                           min="0" max="60">
                                                    <span class="input-group-text">minutos</span>
                                                </div>
                                                <small class="text-muted">Tempo de antecedência sugerido na mensagem</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancelamento Automático -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">🚫 Cancelamento Automático (Opcional)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   name="cancelar_nao_confirmados"
                                                   id="cancelar_nao_confirmados"
                                                   value="1"
                                                   <?= ($estabelecimento->cancelar_nao_confirmados ?? 0) ? 'checked' : '' ?>>
                                            <span class="form-check-label">
                                                <strong>Cancelar automaticamente agendamentos não confirmados</strong>
                                            </span>
                                        </label>
                                        <small class="text-muted d-block mt-1">
                                            Se o cliente não confirmar presença, o agendamento será cancelado automaticamente
                                        </small>
                                    </div>

                                    <div id="cancelamento_opcoes" style="display: <?= ($estabelecimento->cancelar_nao_confirmados ?? 0) ? 'block' : 'none' ?>">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Cancelar quantas horas antes do horário?</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="cancelar_nao_confirmados_horas"
                                                           value="<?= $estabelecimento->cancelar_nao_confirmados_horas ?? 2 ?>"
                                                           min="1" max="24">
                                                    <span class="input-group-text">horas</span>
                                                </div>
                                                <small class="text-muted">Se não confirmar até X horas antes, será cancelado</small>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="ti ti-alert-triangle me-2"></i>
                                            <strong>Atenção:</strong> O agendamento será cancelado automaticamente se o cliente não responder à confirmação no prazo configurado.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-secondary">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Como funciona:</strong>
                                <ol class="mb-0 mt-2">
                                    <li>Cliente agenda sem pagamento → Status: <span class="badge bg-yellow">Pendente</span></li>
                                    <li>Sistema envia pedido de confirmação → Cliente responde: Confirmar | Reagendar | Cancelar</li>
                                    <li>Se confirmar → Status: <span class="badge bg-green">Confirmado</span> → Receberá lembrete antes do horário</li>
                                    <li>Se não confirmar no prazo → Cancelamento automático (se ativado)</li>
                                </ol>
                            </div>

                            <script>
                            // Toggle confirmação
                            document.getElementById('solicitar_confirmacao').addEventListener('change', function() {
                                document.getElementById('confirmacao_opcoes').style.display = this.checked ? 'block' : 'none';
                            });

                            // Toggle dia anterior
                            document.getElementById('confirmacao_dia_anterior').addEventListener('change', function() {
                                document.getElementById('horario_dia_anterior_container').style.display = this.checked ? 'block' : 'none';
                            });

                            // Toggle lembrete
                            document.getElementById('enviar_lembrete_pre_atendimento').addEventListener('change', function() {
                                document.getElementById('lembrete_opcoes').style.display = this.checked ? 'block' : 'none';
                            });

                            // Toggle cancelamento
                            document.getElementById('cancelar_nao_confirmados').addEventListener('change', function() {
                                document.getElementById('cancelamento_opcoes').style.display = this.checked ? 'block' : 'none';
                            });
                            </script>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>

                    <script>
                        // Mostrar/ocultar limite de reagendamentos
                        document.getElementById('permite_reagendamento').addEventListener('change', function() {
                            document.getElementById('limite_reagendamentos_container').style.display = this.checked ? 'block' : 'none';
                        });
                    </script>
                    <?php endif; ?>

                    <!-- Aba WhatsApp -->
                    <?php if ($aba_ativa == 'whatsapp'): ?>
                    <div class="tab-pane active">
                        <?php
                        // Status da conexão WhatsApp do estabelecimento
                        $waha_status = $estabelecimento->waha_status ?? 'desconectado';
                        $waha_numero = $estabelecimento->waha_numero_conectado ?? '';
                        ?>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-brand-whatsapp me-2 text-success"></i>
                                    Conectar WhatsApp
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Status Conectado -->
                                <?php if ($waha_status == 'conectado' && $waha_numero): ?>
                                    <div class="text-center py-4">
                                        <div class="mb-4">
                                            <span class="avatar avatar-xl bg-success-lt">
                                                <i class="ti ti-check icon-lg text-success"></i>
                                            </span>
                                        </div>
                                        <h2 class="text-success mb-2">WhatsApp Conectado!</h2>
                                        <p class="text-muted mb-4">
                                            Número: <strong><?= $waha_numero ?></strong>
                                        </p>

                                        <div class="alert alert-success">
                                            <i class="ti ti-info-circle me-2"></i>
                                            Seu WhatsApp está conectado e pronto para enviar notificações aos seus clientes.
                                        </div>

                                        <div class="mt-4">
                                            <a href="<?= base_url('painel/configuracoes/waha_desconectar') ?>"
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('Deseja realmente desconectar o WhatsApp?')">
                                                <i class="ti ti-plug-off me-1"></i>
                                                Desconectar WhatsApp
                                            </a>
                                        </div>
                                    </div>

                                <!-- Status Conectando - Mostrar QR Code -->
                                <?php elseif ($waha_status == 'conectando'): ?>
                                    <div class="text-center py-4">
                                        <h3 class="mb-4">Escaneie o QR Code</h3>
                                        <p class="text-muted mb-4">
                                            Abra o WhatsApp no seu celular, vá em <strong>Configurações > Aparelhos conectados</strong> e escaneie o código abaixo.
                                        </p>

                                        <div id="qrcode-container" class="mb-4">
                                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                                            <p class="text-muted mt-3">Carregando QR Code...</p>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="ti ti-clock me-2"></i>
                                            O QR Code expira em alguns segundos. Se não conseguir escanear, clique em "Gerar Novo QR Code".
                                        </div>

                                        <div class="mt-3">
                                            <a href="<?= base_url('painel/configuracoes/waha_iniciar_sessao') ?>" class="btn btn-outline-primary">
                                                <i class="ti ti-refresh me-1"></i>
                                                Gerar Novo QR Code
                                            </a>
                                        </div>
                                    </div>

                                <!-- Status Desconectado - Botão para Conectar -->
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <div class="mb-4">
                                            <span class="avatar avatar-xl bg-secondary-lt">
                                                <i class="ti ti-brand-whatsapp icon-lg text-secondary"></i>
                                            </span>
                                        </div>
                                        <h2 class="mb-2">Conecte seu WhatsApp</h2>
                                        <p class="text-muted mb-4">
                                            Conecte o WhatsApp do seu estabelecimento para enviar notificações automáticas aos seus clientes sobre agendamentos.
                                        </p>

                                        <div class="row justify-content-center mb-4">
                                            <div class="col-md-8">
                                                <div class="list-group list-group-flush text-start">
                                                    <div class="list-group-item bg-transparent border-0">
                                                        <i class="ti ti-check text-success me-2"></i>
                                                        Confirmação automática de agendamentos
                                                    </div>
                                                    <div class="list-group-item bg-transparent border-0">
                                                        <i class="ti ti-check text-success me-2"></i>
                                                        Lembretes antes do horário marcado
                                                    </div>
                                                    <div class="list-group-item bg-transparent border-0">
                                                        <i class="ti ti-check text-success me-2"></i>
                                                        Notificações de cancelamento
                                                    </div>
                                                    <div class="list-group-item bg-transparent border-0">
                                                        <i class="ti ti-check text-success me-2"></i>
                                                        Bot de agendamento automático
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="<?= base_url('painel/configuracoes/waha_iniciar_sessao') ?>" class="btn btn-success btn-lg">
                                            <i class="ti ti-brand-whatsapp me-2"></i>
                                            Conectar WhatsApp
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Configurações do Bot WhatsApp -->
                        <?php if ($waha_status == 'conectado'): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-robot me-2"></i>
                                    Configurações do Bot de Agendamento
                                </h3>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <input type="hidden" name="aba" value="whatsapp">

                                    <!-- Ativar/Desativar Bot Geral -->
                                    <div class="mb-4">
                                        <div class="card bg-primary-lt">
                                            <div class="card-body">
                                                <label class="form-check form-switch form-switch-lg mb-0">
                                                    <input class="form-check-input" type="checkbox" name="waha_bot_ativo" value="1"
                                                        <?= ($estabelecimento->waha_bot_ativo ?? 0) ? 'checked' : '' ?>>
                                                    <span class="form-check-label">
                                                        <strong>Ativar Bot de Agendamento</strong>
                                                        <span class="form-check-description text-dark">
                                                            Quando ativado, o sistema responderá automaticamente conforme as regras abaixo.
                                                            Se desativado, nenhuma mensagem será enviada.
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filtro de Ativação (Privacidade) -->
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="ti ti-shield-lock me-1"></i>
                                            Filtro de Ativação (Opções de Privacidade)
                                        </label>

                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <!-- Modo Público -->
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="bot_modo_gatilho" value="sempre_ativo"
                                                       class="form-selectgroup-input"
                                                       <?= ($estabelecimento->bot_modo_gatilho ?? 'sempre_ativo') === 'sempre_ativo' ? 'checked' : '' ?>>
                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                                        <span class="avatar bg-success-lt me-3">
                                                            <i class="ti ti-world icon-lg"></i>
                                                        </span>
                                                        <div>
                                                            <strong>Bot Público (Responde a Todos)</strong>
                                                            <span class="d-block text-muted">O bot responderá automaticamente a qualquer mensagem recebida.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>

                                            <!-- Modo Privado -->
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="bot_modo_gatilho" value="palavra_chave"
                                                       class="form-selectgroup-input"
                                                       <?= ($estabelecimento->bot_modo_gatilho ?? 'sempre_ativo') === 'palavra_chave' ? 'checked' : '' ?>>
                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                                        <span class="avatar bg-secondary-lt me-3">
                                                            <i class="ti ti-lock icon-lg"></i>
                                                        </span>
                                                        <div>
                                                            <strong>Bot Privado (Requer Ativação)</strong>
                                                            <span class="d-block text-muted">O bot ficará "invisível" e só responderá se a mensagem contiver uma Palavra-Chave específica.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Configuração de Palavras-Chave -->
                                    <div id="palavras-chave-container" class="mb-4" style="display: none;">
                                        <div class="card bg-muted-lt">
                                            <div class="card-body">
                                                <label class="form-label required">
                                                    <i class="ti ti-key me-1"></i>
                                                    Palavras-Chave de Ativação
                                                </label>
                                                <textarea class="form-control mb-2" name="bot_palavras_chave" rows="3"
                                                          placeholder="Ex: agendar, marcar, horário, agenda"><?=
                                                    isset($estabelecimento->bot_palavras_chave) && !empty($estabelecimento->bot_palavras_chave)
                                                        ? implode(", ", json_decode($estabelecimento->bot_palavras_chave, true) ?: [])
                                                        : "agendar, agendamento, marcar, horário"
                                                ?></textarea>
                                                <small class="form-hint">
                                                    Separe as palavras por vírgula. Ex: <code>agendar, marcar, quero agendar</code>.
                                                    <br>O bot será ativado se a mensagem do cliente contiver <strong>qualquer uma</strong> dessas palavras.
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                    // Lógica para mostrar/ocultar palavras-chave
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const radios = document.querySelectorAll('input[name="bot_modo_gatilho"]');
                                        const container = document.getElementById('palavras-chave-container');

                                        function atualizarVisibilidade() {
                                            const selecionado = document.querySelector('input[name="bot_modo_gatilho"]:checked');
                                            container.style.display = (selecionado && selecionado.value === 'palavra_chave') ? 'block' : 'none';
                                        }

                                        radios.forEach(radio => radio.addEventListener('change', atualizarVisibilidade));
                                        atualizarVisibilidade(); // Executar ao carregar
                                    });
                                    </script>
                                        <input type="number" class="form-control" name="bot_timeout_minutos"
                                               value="<?= set_value('bot_timeout_minutos', $estabelecimento->bot_timeout_minutos ?? 30) ?>"
                                               min="5" max="120" step="5">
                                        <small class="form-hint">
                                            Tempo de inatividade antes de resetar a conversa do bot.
                                            <strong>Valores sugeridos:</strong> 15 min (rápido), 30 min (padrão), 60 min (longo)
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <strong>Como funciona:</strong> Se o cliente ficar inativo por mais tempo que o configurado,
                                        a conversa será resetada automaticamente e ele precisará digitar "oi" para iniciar novamente.
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i>
                                        Salvar Configurações
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Script para polling do QR Code -->
                    <?php if ($waha_status == 'conectando'): ?>
                    <script>
                    (function() {
                        let tentativas = 0;
                        const maxTentativas = 60; // 3 minutos (60 * 3s)

                        function atualizarQRCode() {
                            if (tentativas >= maxTentativas) {
                                document.getElementById('qrcode-container').innerHTML = `
                                    <div class="alert alert-danger">
                                        <i class="ti ti-alert-circle me-2"></i>
                                        Tempo esgotado. Clique em "Gerar Novo QR Code" para tentar novamente.
                                    </div>
                                `;
                                return;
                            }

                            fetch('<?= base_url('painel/configuracoes/waha_qrcode') ?>')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'connected' || data.status === 'working') {
                                        // Conectou! Recarregar página
                                        location.reload();
                                    } else if (data.qrcode) {
                                        document.getElementById('qrcode-container').innerHTML = `
                                            <div class="p-3 bg-white rounded shadow-sm d-inline-block">
                                                <img src="data:image/png;base64,${data.qrcode}" alt="QR Code" style="width: 280px; height: 280px;">
                                            </div>
                                            <p class="text-muted mt-3">Escaneie com seu WhatsApp</p>
                                        `;
                                    }
                                    tentativas++;
                                    setTimeout(atualizarQRCode, 3000);
                                })
                                .catch(err => {
                                    console.error('Erro:', err);
                                    tentativas++;
                                    setTimeout(atualizarQRCode, 3000);
                                });
                        }

                        // Iniciar polling
                        atualizarQRCode();
                    })();
                    </script>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Aba Mercado Pago -->
                    <?php if ($aba_ativa == 'mercadopago'): ?>
                    <div class="tab-pane active">
                        <form method="post">
                            <input type="hidden" name="aba" value="mercadopago">

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Pagamentos:</strong> Configure como deseja receber pagamentos dos agendamentos.
                            </div>

                            <!-- Escolha do Tipo de Pagamento -->
                            <div class="mb-4">
                                <label class="form-label required">Tipo de Pagamento</label>
                                <select class="form-select" name="pagamento_tipo" id="pagamento_tipo">
                                    <option value="mercadopago" <?= ($estabelecimento->pagamento_tipo ?? 'mercadopago') == 'mercadopago' ? 'selected' : '' ?>>
                                        Mercado Pago (Integração Automática)
                                    </option>
                                    <option value="pix_manual" <?= ($estabelecimento->pagamento_tipo ?? 'mercadopago') == 'pix_manual' ? 'selected' : '' ?>>
                                        PIX Manual (Confirmação Manual)
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    <strong>Mercado Pago:</strong> Pagamentos confirmados automaticamente via webhook.<br>
                                    <strong>PIX Manual:</strong> Você confirma manualmente após receber o pagamento.
                                </small>
                            </div>

                            <hr class="my-4">

                            <!-- Seção Mercado Pago -->
                            <div id="secao-mercadopago">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">
                                        <i class="ti ti-credit-card me-2"></i>
                                        Credenciais Mercado Pago
                                    </h4>
                                    <a href="<?= base_url('painel/configuracoes/tutorial_mercadopago') ?>"
                                       target="_blank"
                                       onclick="window.open(this.href, 'Tutorial', 'width=900,height=800,scrollbars=yes'); return false;"
                                       class="btn btn-primary d-none d-sm-inline-block">
                                        <i class="ti ti-help-circle me-1"></i>
                                        Como obter minhas credenciais?
                                    </a>
                                    <!-- Mobile Button -->
                                    <a href="<?= base_url('painel/configuracoes/tutorial_mercadopago') ?>" target="_blank" class="btn btn-primary btn-icon d-sm-none" aria-label="Ajuda">
                                        <i class="ti ti-help-circle"></i>
                                    </a>
                                </div>

                                <h5 class="mb-3">Credenciais de Teste (Sandbox)</h5>

                            <div class="mb-3">
                                <label class="form-label">Public Key (Teste)</label>
                                <input type="text" class="form-control" name="mp_public_key_test"
                                       value="<?= set_value('mp_public_key_test', $estabelecimento->mp_public_key_test ?? '') ?>"
                                       placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Access Token (Teste)</label>
                                <input type="text" class="form-control" name="mp_access_token_test"
                                       value="<?= set_value('mp_access_token_test', $estabelecimento->mp_access_token_test ?? '') ?>"
                                       placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>

                            <hr class="my-4">

                            <h4 class="mb-3">Credenciais de Produção</h4>

                            <div class="mb-3">
                                <label class="form-label">Public Key (Produção)</label>
                                <input type="text" class="form-control" name="mp_public_key_prod"
                                       value="<?= set_value('mp_public_key_prod', $estabelecimento->mp_public_key_prod ?? '') ?>"
                                       placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Access Token (Produção)</label>
                                <input type="text" class="form-control" name="mp_access_token_prod"
                                       value="<?= set_value('mp_access_token_prod', $estabelecimento->mp_access_token_prod ?? '') ?>"
                                       placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="mp_sandbox" value="1"
                                           <?= ($estabelecimento->mp_sandbox ?? 0) ? 'checked' : '' ?>>
                                    <span class="form-check-label">Modo Sandbox (Teste)</span>
                                </label>
                                <small class="text-muted d-block mt-1">
                                    Ative para usar as credenciais de teste. Desative para usar produção.
                                </small>
                            </div>
                            </div>
                            <!-- Fim Seção Mercado Pago -->

                            <!-- Seção PIX Manual -->
                            <div id="secao-pix-manual" style="display: none;">
                                <h4 class="mb-3">
                                    <i class="ti ti-qrcode me-2"></i>
                                    Configuração PIX Manual
                                </h4>

                                <div class="alert alert-warning">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    <strong>Atenção:</strong> Com PIX Manual, você precisará confirmar manualmente cada pagamento recebido no painel de agendamentos.
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label required">Chave PIX</label>
                                        <input type="text" class="form-control" name="pix_chave" id="pix_chave"
                                               value="<?= set_value('pix_chave', $estabelecimento->pix_chave ?? '') ?>"
                                               placeholder="Digite sua chave PIX">
                                        <small class="text-muted">CPF, CNPJ, e-mail, telefone ou chave aleatória</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Tipo da Chave</label>
                                        <select class="form-select" name="pix_tipo_chave" id="pix_tipo_chave">
                                            <option value="">Selecione...</option>
                                            <option value="cpf" <?= ($estabelecimento->pix_tipo_chave ?? '') == 'cpf' ? 'selected' : '' ?>>CPF</option>
                                            <option value="cnpj" <?= ($estabelecimento->pix_tipo_chave ?? '') == 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
                                            <option value="email" <?= ($estabelecimento->pix_tipo_chave ?? '') == 'email' ? 'selected' : '' ?>>E-mail</option>
                                            <option value="telefone" <?= ($estabelecimento->pix_tipo_chave ?? '') == 'telefone' ? 'selected' : '' ?>>Telefone</option>
                                            <option value="aleatoria" <?= ($estabelecimento->pix_tipo_chave ?? '') == 'aleatoria' ? 'selected' : '' ?>>Aleatória</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label required">Nome do Recebedor</label>
                                        <input type="text" class="form-control" name="pix_nome_recebedor"
                                               value="<?= set_value('pix_nome_recebedor', $estabelecimento->pix_nome_recebedor ?? '') ?>"
                                               placeholder="Nome que aparecerá no PIX">
                                        <small class="text-muted">Nome completo ou razão social</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Cidade</label>
                                        <input type="text" class="form-control" name="pix_cidade"
                                               value="<?= set_value('pix_cidade', $estabelecimento->pix_cidade ?? '') ?>"
                                               placeholder="Sua cidade">
                                        <small class="text-muted">Obrigatório pelo padrão PIX</small>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <strong>Como funciona:</strong>
                                    <ol class="mb-0 mt-2">
                                        <li>Cliente agenda e escolhe pagar via PIX</li>
                                        <li>Sistema gera QR Code com valor do serviço</li>
                                        <li>Cliente paga e envia comprovante pelo WhatsApp</li>
                                        <li>Você confirma o pagamento no painel de agendamentos</li>
                                    </ol>
                                </div>
                            </div>
                            <!-- Fim Seção PIX Manual -->

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>

                    <script>
                    // Alternar entre seções de Mercado Pago e PIX Manual
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectTipoPagamento = document.getElementById('pagamento_tipo');
                        const secaoMercadoPago = document.getElementById('secao-mercadopago');
                        const secaoPixManual = document.getElementById('secao-pix-manual');

                        function alternarSecao() {
                            if (selectTipoPagamento.value === 'pix_manual') {
                                secaoMercadoPago.style.display = 'none';
                                secaoPixManual.style.display = 'block';
                            } else {
                                secaoMercadoPago.style.display = 'block';
                                secaoPixManual.style.display = 'none';
                            }
                        }

                        selectTipoPagamento.addEventListener('change', alternarSecao);
                        alternarSecao(); // Executar ao carregar
                    });
                    </script>
                    <?php endif; ?>

                    <!-- Aba Notificações Profissional -->
                    <?php if ($aba_ativa == 'notificacoes_profissional'): ?>
                    <div class="tab-pane active">
                        <?php $this->load->view('painel/configuracoes/notificacoes_profissional'); ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Mostrar/ocultar campo de intervalo baseado no switch
document.addEventListener('DOMContentLoaded', function() {
    const switchIntervalo = document.getElementById('usar_intervalo_fixo');
    const campoIntervalo = document.getElementById('campo-intervalo');

    if (switchIntervalo && campoIntervalo) {
        function toggleCampoIntervalo() {
            if (switchIntervalo.checked) {
                campoIntervalo.style.display = 'block';
            } else {
                campoIntervalo.style.display = 'none';
            }
        }

        switchIntervalo.addEventListener('change', toggleCampoIntervalo);
        toggleCampoIntervalo(); // Executar ao carregar
    }
});
</script>
