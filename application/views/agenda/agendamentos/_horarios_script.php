<script>
document.addEventListener('DOMContentLoaded', function() {
    const servicoSelect = document.getElementById('servico_id');
    const dataInput = document.getElementById('data');
    const horaSelect = document.getElementById('hora_inicio');

    // Carregar horários disponíveis
    function carregarHorarios() {
        const servicoId = servicoSelect.value;
        const data = dataInput.value;

        if (servicoId && data) {
            // Mostrar loading
            horaSelect.innerHTML = '<option value="">🔄 Carregando horários...</option>';
            horaSelect.disabled = true;

            fetch(`<?= base_url('agenda/agendamentos/get_horarios_disponiveis') ?>?servico_id=${servicoId}&data=${data}`)
                .then(r => r.json())
                .then(horarios => {
                    horaSelect.disabled = false;

                    if (horarios.length > 0) {
                        horaSelect.innerHTML = '<option value="">Selecione...</option>';
                        horarios.forEach(h => {
                            horaSelect.innerHTML += `<option value="${h}">${h}</option>`;
                        });
                    } else {
                        horaSelect.innerHTML = '<option value="">❌ Nenhum horário disponível</option>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar horários:', error);
                    horaSelect.disabled = false;
                    horaSelect.innerHTML = '<option value="">⚠️ Erro ao carregar horários</option>';
                });
        }
    }

    servicoSelect?.addEventListener('change', carregarHorarios);
    dataInput?.addEventListener('change', carregarHorarios);
});
</script>
