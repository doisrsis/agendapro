<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-calendar me-2"></i>
                    Minha Agenda
                </h2>
                <div class="text-muted mt-1">Olá, <?= $profissional->nome ?>! Aqui está sua agenda</div>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('agenda/agendamentos/criar') ?>" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>
                    Novo Agendamento
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <!-- Estatísticas --->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Hoje</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_agendamentos_hoje ?></div>
                        <div class="d-flex mb-2">
                            <div>Agendamentos</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Confirmados</div>
                        </div>
                        <div class="h1 mb-3"><?= $agendamentos_confirmados ?></div>
                        <div class="d-flex mb-2">
                            <div>Para hoje</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Concluídos</div>
                        </div>
                        <div class="h1 mb-3"><?= $agendamentos_concluidos ?></div>
                        <div class="d-flex mb-2">
                            <div>Hoje</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Este Mês</div>
                        </div>
                        <div class="h1 mb-3"><?= $total_agendamentos_mes ?></div>
                        <div class="d-flex mb-2">
                            <div>Agendamentos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendário -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Calendário de Agendamentos</h3>
                        <div class="card-actions">
                            <div class="d-flex gap-2">
                                <span class="badge bg-success">Confirmado</span>
                                <span class="badge bg-warning">Pendente</span>
                                <span class="badge bg-danger">Cancelado</span>
                                <span class="badge bg-primary">Concluído</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal de Detalhes do Agendamento -->
<div class="modal modal-blur fade" id="modalAgendamento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Cliente</label>
                    <div id="modal-cliente-nome"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Serviço</label>
                    <div id="modal-servico-nome"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Data</label>
                        <div id="modal-data"></div>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Horário</label>
                        <div id="modal-horario"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <div id="modal-status"></div>
                </div>
                <div class="mb-3" id="modal-observacoes-container" style="display:none;">
                    <label class="form-label fw-bold">Observações</label>
                    <div id="modal-observacoes"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" id="modal-whatsapp-btn" class="btn btn-success" target="_blank">
                    <i class="ti ti-brand-whatsapp me-2"></i>WhatsApp
                </a>
                <a href="#" id="modal-editar-btn" class="btn btn-primary">
                    <i class="ti ti-edit me-2"></i>Editar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        editable: false,
        selectable: false,
        height: 'auto',
        events: '<?= base_url('agenda/dashboard/get_agendamentos_json') ?>',
        eventClick: function(info) {
            // Preencher modal com dados do evento
            var event = info.event;
            var props = event.extendedProps;

            document.getElementById('modal-cliente-nome').textContent = props.cliente_nome;
            document.getElementById('modal-servico-nome').textContent = props.servico_nome;

            var dataInicio = new Date(event.start);
            document.getElementById('modal-data').textContent = dataInicio.toLocaleDateString('pt-BR');
            document.getElementById('modal-horario').textContent = dataInicio.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});

            // Status com badge
            var statusBadge = '';
            switch(props.status) {
                case 'confirmado':
                    statusBadge = '<span class="badge bg-success">Confirmado</span>';
                    break;
                case 'pendente':
                    statusBadge = '<span class="badge bg-warning">Pendente</span>';
                    break;
                case 'cancelado':
                    statusBadge = '<span class="badge bg-danger">Cancelado</span>';
                    break;
                case 'concluido':
                    statusBadge = '<span class="badge bg-primary">Concluído</span>';
                    break;
            }
            document.getElementById('modal-status').innerHTML = statusBadge;

            // Observações
            if (props.observacoes) {
                document.getElementById('modal-observacoes').textContent = props.observacoes;
                document.getElementById('modal-observacoes-container').style.display = 'block';
            } else {
                document.getElementById('modal-observacoes-container').style.display = 'none';
            }

            // WhatsApp
            if (props.cliente_whatsapp) {
                var whatsappUrl = 'https://wa.me/55' + props.cliente_whatsapp.replace(/\D/g, '');
                document.getElementById('modal-whatsapp-btn').href = whatsappUrl;
                document.getElementById('modal-whatsapp-btn').style.display = 'inline-block';
            } else {
                document.getElementById('modal-whatsapp-btn').style.display = 'none';
            }

            // Botão editar
            document.getElementById('modal-editar-btn').href = '<?= base_url('agenda/agendamentos/editar/') ?>' + event.id;

            // Abrir modal
            var modal = new bootstrap.Modal(document.getElementById('modalAgendamento'));
            modal.show();
        }
    });

    calendar.render();
});
</script>
