1) O reagendamento está quase igual ao agendamento novo (ok), mas tem uma “CHAVE” errada na parte de datas

No teu código de reagendamento, a IA colocou essa regra:

Em enviar_opcoes_data_reagendamento e processar_estado_reagendando_data, ela chama obter_datas_disponiveis(...) SEM passar o agendamento_id (ela até comenta isso como “CHAVE”).

pasted



pasted

Só que em reagendamento, o correto geralmente é o inverso:

✅ Você deve passar o agendamento_id para excluir o próprio agendamento da checagem, senão o dia/horários podem ser calculados “como se aquele agendamento ainda estivesse ocupando o calendário”, e isso pode bagunçar disponibilidade em alguns cenários (ex.: o dia só tem aquele slot).

Isso não é o que causa “mostrar horário do outro cliente”, mas é um bug real que vale corrigir.

Correção sugerida (datas no reagendamento):

$datas = $this->obter_datas_disponiveis(
  $estabelecimento,
  $dados['profissional_id'],
  7,
  $duracao,
  $dados['agendamento_id'] // <-- passar aqui também
);

2) O teu bug principal: comparação de horários pode estar “em datas diferentes” e aí NÃO detecta conflito

O teu filtro de conflitos em obter_horarios_disponiveis() está assim:

Converte horários do slot com strtotime($hora_str)

Converte horários do agendamento existente com strtotime($ag->hora_inicio) e strtotime($ag->hora_fim)

E compara overlap:
if ($hora_atual < $ag_fim && $hora_fim_servico > $ag_inicio)

pasted

🚨 O problema clássico aqui: strtotime("09:00") usa a data de hoje, enquanto o teu $ag->hora_inicio / $ag->hora_fim pode estar vindo como:

só hora (09:00:00) ✅ (aí funciona)

ou datetime (2026-01-03 09:00:00) ❌ (aí quebra)

Se $ag->hora_inicio vier com DATA junto e $hora_str vier só HORA, os timestamps ficam em dias diferentes e a checagem de conflito pode falhar — e aí ele “acha” que o horário está livre e mostra mesmo estando ocupado.

Isso explica perfeitamente o teu exemplo:

existe um agendamento em 03/01/2026 às 09:00

o bot, ao montar horários, compara 09:00 (hoje) com 2026-01-03 09:00:00 (dia do agendamento) → pode não bater overlap → horário aparece como “livre”.

✅ Como blindar isso de vez: sempre comparar usando data + hora (do dia selecionado) pros slots, e pro agendamento existente também.

Patch seguro (recomendado) dentro de obter_horarios_disponiveis()

Quando estiver calculando o slot:

$slot_inicio_ts = strtotime($data . ' ' . $hora_str);
$slot_fim_ts    = strtotime("+{$duracao} minutes", $slot_inicio_ts);


E para cada agendamento existente:

se hora_inicio vier só hora, junta com $ag->data

se já vier datetime, strtotime() já resolve

Exemplo robusto:

$ag_inicio_str = (strlen($ag->hora_inicio) <= 8)
    ? ($ag->data . ' ' . $ag->hora_inicio)
    : $ag->hora_inicio;

$ag_fim_str = (strlen($ag->hora_fim) <= 8)
    ? ($ag->data . ' ' . $ag->hora_fim)
    : $ag->hora_fim;

$ag_inicio = strtotime($ag_inicio_str);
$ag_fim    = strtotime($ag_fim_str);


Aí a tua condição de conflito vira:

if ($slot_inicio_ts < $ag_fim && $slot_fim_ts > $ag_inicio) {
    $conflito = true;
}


👉 Isso elimina 100% dos casos em que o horário ocupado “escapa” por causa de timestamp em dias diferentes.

3) Um terceiro ponto que vale conferir rápido (mas é secundário)

O teu filtro ignora apenas status == 'cancelado'.

pasted


Se tu tiver status como reagendado, remarcado, expirado, bloqueado, etc., pode ser que um agendamento “válido” esteja ficando fora do conflito, dependendo de como o model retorna.

✅ Sugestão: definir explicitamente quais status bloqueiam agenda, tipo:

confirmado

pendente

em_atendimento

E ignorar o resto.

O que eu faria agora (passo a passo, direto ao ponto)

Aplicar o patch do timestamp (data + hora) dentro de obter_horarios_disponiveis() — esse é o que resolve o “mostra horário ocupado”.

Passar agendamento_id também na lista de datas do reagendamento (corrige inconsistências).

Abrir teu log (tu já loga “CONFLITO / ADICIONADO / IGNORADO”) e confirmar se, no caso do horário 09:00 ocupado, ele está logando “CONFLITO”.
