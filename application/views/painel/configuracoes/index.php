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
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    <!-- Aba Dados Gerais -->
                    <?php if ($aba_ativa == 'geral'): ?>
                    <div class="tab-pane active">
                        <form method="post">
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
                                <label class="form-label">Endereço</label>
                                <input type="text" class="form-control" name="endereco"
                                       value="<?= set_value('endereco', $estabelecimento->endereco ?? '') ?>">
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
                                    <input type="checkbox" class="form-check-input" name="permite_reagendamento" id="permite_reagendamento"
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

                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="ti ti-clock me-1"></i>
                                            Timeout da Sessão (minutos)
                                        </label>
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
                                <strong>Mercado Pago:</strong> Configure a integração para receber pagamentos online.
                            </div>

                            <h4 class="mb-3">Credenciais de Teste (Sandbox)</h4>

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

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Salvar Integração
                                </button>
                            </div>
                        </form>
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
