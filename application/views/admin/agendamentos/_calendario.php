<style>
#calendar {
    height: 700px;
}
</style>

<div id="calendar"></div>

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
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        editable: false,
        selectable: false,
        height: 'auto',
        events: '<?= base_url('painel/agendamentos/get_agendamentos_json') ?>',
        eventClick: function(info) {
            var agendamento_id = info.event.extendedProps.agendamento_id;
            window.location.href = '<?= base_url('painel/agendamentos/visualizar/') ?>' + agendamento_id;
        }
    });

    calendar.render();
});
</script>
