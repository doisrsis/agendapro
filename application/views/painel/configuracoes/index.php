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
                                            <th class="text-center" width="100">Ativo</th>
                                            <th width="150">Abertura</th>
                                            <th width="150">Fechamento</th>
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
                        <form method="post">
                            <input type="hidden" name="aba" value="whatsapp">

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Evolution API:</strong> Configure a integração com WhatsApp para enviar notificações automáticas aos clientes.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL da API</label>
                                <input type="url" class="form-control" name="whatsapp_api_url"
                                       value="<?= set_value('whatsapp_api_url', $estabelecimento->whatsapp_api_url ?? '') ?>"
                                       placeholder="https://api.evolution.com.br">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Token da API</label>
                                <input type="text" class="form-control" name="whatsapp_api_token"
                                       value="<?= set_value('whatsapp_api_token', $estabelecimento->whatsapp_api_token ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Número do WhatsApp</label>
                                <input type="text" class="form-control" name="whatsapp_numero"
                                       value="<?= set_value('whatsapp_numero', $estabelecimento->whatsapp_numero ?? '') ?>"
                                       placeholder="5511999999999">
                                <small class="text-muted">Número com código do país (55) + DDD + número</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="whatsapp_ativo"
                                           <?= ($estabelecimento->whatsapp_ativo ?? 0) ? 'checked' : '' ?>>
                                    <span class="form-check-label">Integração Ativa</span>
                                </label>
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

                    <!-- Aba Mercado Pago -->
                    <?php if ($aba_ativa == 'mercadopago'): ?>
                    <div class="tab-pane active">
                        <form method="post">
                            <input type="hidden" name="aba" value="mercadopago">

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Mercado Pago:</strong> Configure a integração para receber pagamentos online.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Public Key</label>
                                <input type="text" class="form-control" name="mp_public_key"
                                       value="<?= set_value('mp_public_key', $estabelecimento->mp_public_key ?? '') ?>"
                                       placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Access Token</label>
                                <input type="text" class="form-control" name="mp_access_token"
                                       value="<?= set_value('mp_access_token', $estabelecimento->mp_access_token ?? '') ?>"
                                       placeholder="APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="mp_ativo"
                                           <?= ($estabelecimento->mp_ativo ?? 0) ? 'checked' : '' ?>>
                                    <span class="form-check-label">Integração Ativa</span>
                                </label>
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
