<!-- Notificações para Profissionais -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-bell"></i>
            Notificações para Profissionais
        </h3>
    </div>
    <div class="card-body">
        <form id="form-notificacoes-profissional" method="post" action="<?= base_url('painel/configuracoes/salvar_notificacoes_profissional') ?>">

            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i>
                Configure quais notificações o profissional/estabelecimento receberá via WhatsApp sobre os agendamentos.
            </div>

            <!-- Novo Agendamento -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notif_prof_novo_agendamento"
                           name="notif_prof_novo_agendamento" value="1"
                           <?= !empty($estabelecimento->notif_prof_novo_agendamento) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="notif_prof_novo_agendamento">
                        <strong>Novo Agendamento</strong>
                    </label>
                </div>
                <small class="text-muted ms-4">
                    Notificar quando um novo agendamento for criado e confirmado
                </small>
            </div>

            <!-- Cancelamento -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notif_prof_cancelamento"
                           name="notif_prof_cancelamento" value="1"
                           <?= !empty($estabelecimento->notif_prof_cancelamento) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="notif_prof_cancelamento">
                        <strong>Cancelamento</strong>
                    </label>
                </div>
                <small class="text-muted ms-4">
                    Notificar quando cliente cancelar um agendamento
                </small>
            </div>

            <!-- Reagendamento -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notif_prof_reagendamento"
                           name="notif_prof_reagendamento" value="1"
                           <?= !empty($estabelecimento->notif_prof_reagendamento) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="notif_prof_reagendamento">
                        <strong>Reagendamento</strong>
                    </label>
                </div>
                <small class="text-muted ms-4">
                    Notificar quando cliente reagendar via bot
                </small>
            </div>

            <hr class="my-4">

            <!-- Resumo Diário -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notif_prof_resumo_diario"
                           name="notif_prof_resumo_diario" value="1"
                           <?= !empty($estabelecimento->notif_prof_resumo_diario) ? 'checked' : '' ?>
                           onchange="toggleResumoHorarios(this.checked)">
                    <label class="form-check-label" for="notif_prof_resumo_diario">
                        <strong>Resumo Diário da Agenda</strong>
                    </label>
                </div>
                <small class="text-muted ms-4">
                    Enviar resumo da agenda 2 vezes ao dia com os próximos agendamentos
                </small>
            </div>

            <!-- Horários do Resumo Diário -->
            <div id="resumo-horarios" style="<?= empty($estabelecimento->notif_prof_resumo_diario) ? 'display: none;' : '' ?>">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ti ti-clock"></i>
                            Horários de Envio
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="notif_prof_resumo_manha" class="form-label">
                                    <i class="ti ti-sunrise"></i>
                                    Resumo da Manhã
                                </label>
                                <input type="time" class="form-control" id="notif_prof_resumo_manha"
                                       name="notif_prof_resumo_manha"
                                       value="<?= !empty($estabelecimento->notif_prof_resumo_manha) ? substr($estabelecimento->notif_prof_resumo_manha, 0, 5) : '08:00' ?>">
                                <small class="text-muted">
                                    Enviar resumo dos agendamentos a partir deste horário
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="notif_prof_resumo_tarde" class="form-label">
                                    <i class="ti ti-sun"></i>
                                    Resumo da Tarde
                                </label>
                                <input type="time" class="form-control" id="notif_prof_resumo_tarde"
                                       name="notif_prof_resumo_tarde"
                                       value="<?= !empty($estabelecimento->notif_prof_resumo_tarde) ? substr($estabelecimento->notif_prof_resumo_tarde, 0, 5) : '13:00' ?>">
                                <small class="text-muted">
                                    Enviar resumo dos agendamentos a partir deste horário
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="ti ti-alert-circle"></i>
                            <strong>Importante:</strong> O resumo só será enviado se houver agendamentos confirmados ou pendentes para o período.
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy"></i>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleResumoHorarios(checked) {
    const resumoHorarios = document.getElementById('resumo-horarios');
    if (checked) {
        resumoHorarios.style.display = 'block';
    } else {
        resumoHorarios.style.display = 'none';
    }
}

// Formulário de notificações profissional
document.getElementById('form-notificacoes-profissional').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Adicionar valores 0 para checkboxes desmarcados
    const checkboxes = ['notif_prof_novo_agendamento', 'notif_prof_cancelamento', 'notif_prof_reagendamento', 'notif_prof_resumo_diario'];
    checkboxes.forEach(name => {
        if (!formData.has(name)) {
            formData.append(name, '0');
        }
    });

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: data.message || 'Configurações salvas com sucesso!',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao salvar configurações'
            });
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao salvar configurações'
        });
    });
});
</script>
