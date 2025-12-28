<!--
 * Script de horários para edição de agendamento
 * Carrega horários disponíveis considerando o horário atual do agendamento
 * @author Rafael Dias - doisr.com.br (28/12/2024)
-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const servicoInput = document.getElementById('servico_id');
    const dataInput = document.getElementById('data');
    const horaSelect = document.getElementById('hora_inicio');

    // Horário atual do agendamento
    const horarioAtual = '<?= $agendamento->hora_inicio ?>';

    // Carregar horários disponíveis
    function carregarHorarios() {
        const servicoId = servicoInput.value;
        const data = dataInput.value;

        if (servicoId && data) {
            // Mostrar loading
            horaSelect.innerHTML = '<option value="">🔄 Carregando horários...</option>';
            horaSelect.disabled = true;

            fetch(`<?= base_url('agenda/agendamentos/get_horarios_disponiveis') ?>?servico_id=${servicoId}&data=${data}&agendamento_id=<?= $agendamento->id ?>`)
                .then(r => r.json())
                .then(horarios => {
                    horaSelect.disabled = false;

                    if (horarios.length > 0) {
                        horaSelect.innerHTML = '';

                        // Adicionar horário atual se não estiver na lista
                        let horarioAtualNaLista = horarios.includes(horarioAtual.substring(0, 5));

                        horarios.forEach(h => {
                            const isAtual = (h === horarioAtual.substring(0, 5));
                            const selected = isAtual ? 'selected' : '';
                            const label = isAtual ? `${h} (atual)` : h;
                            horaSelect.innerHTML += `<option value="${h}" ${selected}>${label}</option>`;
                        });

                        // Se horário atual não estava na lista, adicionar no topo
                        if (!horarioAtualNaLista) {
                            const option = document.createElement('option');
                            option.value = horarioAtual;
                            option.text = horarioAtual.substring(0, 5) + ' (atual)';
                            option.selected = true;
                            horaSelect.insertBefore(option, horaSelect.firstChild);
                        }
                    } else {
                        // Manter horário atual mesmo sem outros disponíveis
                        horaSelect.innerHTML = `<option value="${horarioAtual}" selected>${horarioAtual.substring(0, 5)} (atual)</option>`;
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar horários:', error);
                    horaSelect.disabled = false;
                    horaSelect.innerHTML = `<option value="${horarioAtual}" selected>${horarioAtual.substring(0, 5)} (atual)</option>`;
                });
        }
    }

    // Carregar horários ao mudar a data
    dataInput?.addEventListener('change', carregarHorarios);

    // Carregar horários ao abrir a página
    carregarHorarios();
});
</script>
