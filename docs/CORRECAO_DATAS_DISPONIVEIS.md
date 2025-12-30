# Correção: Datas Disponíveis com Horários Reais

**Autor:** Rafael Dias - doisr.com.br
**Data:** 30/12/2025
**Status:** ✅ Implementado

---

## 🔴 Problema Identificado

### **Comportamento Anterior:**

O bot listava datas baseado apenas no calendário do estabelecimento (dias abertos), mas **não verificava se realmente existiam horários disponíveis** nessas datas.

**Resultado:**
```
Usuário escolhe data 30/12/2025
Bot: "Desculpe, não há horários disponíveis nesta data. 😔
      Por favor, escolha outra data."
```

**Impacto:**
- ❌ Experiência ruim do usuário
- ❌ Frustração ao escolher datas sem horários
- ❌ Necessidade de tentar múltiplas datas
- ❌ Aumento de abandono do fluxo

---

## ✅ Solução Implementada

### **Comportamento Novo:**

O bot agora **verifica os horários disponíveis ANTES** de listar as datas, mostrando apenas datas que realmente têm horários livres.

**Resultado:**
```
Usuário vê apenas datas com horários disponíveis
Usuário escolhe data
Bot: Mostra horários disponíveis (sempre tem pelo menos 1)
```

---

## 🔧 Implementação

### **1. Modificação do Método `obter_datas_disponiveis`**

**Arquivo:** `application/controllers/Webhook_waha.php`

**Antes:**
```php
private function obter_datas_disponiveis($estabelecimento, $profissional_id, $dias = 7) {
    // ...
    for ($i = 0; $i < 14 && count($datas) < $dias; $i++) {
        $data = date('Y-m-d', strtotime($data_atual . " +{$i} days"));
        $dia_semana = date('w', strtotime($data));

        $horario = $this->Horario_estabelecimento_model->get_by_dia($estabelecimento->id, $dia_semana);

        if ($horario && $horario->ativo) {
            $datas[] = $data; // ❌ Adiciona sem verificar horários
        }
    }
    return $datas;
}
```

**Depois:**
```php
private function obter_datas_disponiveis($estabelecimento, $profissional_id, $dias = 7, $duracao_servico = 30) {
    // ...
    // Buscar até 30 dias para garantir que encontramos datas suficientes
    for ($i = 0; $i < 30 && count($datas) < $dias; $i++) {
        $data = date('Y-m-d', strtotime($data_atual . " +{$i} days"));
        $dia_semana = date('w', strtotime($data));

        $horario = $this->Horario_estabelecimento_model->get_by_dia($estabelecimento->id, $dia_semana);

        if ($horario && $horario->ativo) {
            // ✅ Verificar se realmente existem horários disponíveis
            $horarios_disponiveis = $this->obter_horarios_disponiveis(
                $estabelecimento,
                $profissional_id,
                $data,
                $duracao_servico
            );

            if (!empty($horarios_disponiveis)) {
                $datas[] = $data;
                log_message('debug', "Bot: data {$data} adicionada - " . count($horarios_disponiveis) . " horários disponíveis");
            } else {
                log_message('debug', "Bot: data {$data} ignorada - sem horários disponíveis");
            }
        }
    }
    return $datas;
}
```

---

### **2. Atualização das Chamadas do Método**

**Passar duração do serviço:**

```php
// Em processar_estado_data()
$duracao = $dados['servico_duracao'] ?? 30;
$datas_disponiveis = $this->obter_datas_disponiveis($estabelecimento, $dados['profissional_id'], 7, $duracao);

// Em enviar_opcoes_data()
$duracao = $dados['servico_duracao'] ?? 30;
$datas = $this->obter_datas_disponiveis($estabelecimento, $dados['profissional_id'], 7, $duracao);
```

---

## 📊 Melhorias

### **1. Janela de Busca Ampliada**

**Antes:** Buscava apenas 14 dias
**Depois:** Busca até 30 dias

**Motivo:** Garantir que sempre encontre 7 datas com horários disponíveis, mesmo em períodos com muitos agendamentos.

---

### **2. Validação Real de Disponibilidade**

Para cada data candidata, o sistema agora:

1. ✅ Verifica se o estabelecimento está aberto
2. ✅ Busca horários disponíveis considerando:
   - Horário de funcionamento
   - Horário de almoço
   - Agendamentos existentes
   - Duração do serviço
   - Intervalo entre agendamentos
3. ✅ Só inclui a data se houver pelo menos 1 horário livre

---

### **3. Logs Detalhados**

```php
log_message('debug', "Bot: data {$data} adicionada - 5 horários disponíveis");
log_message('debug', "Bot: data {$data} ignorada - sem horários disponíveis");
```

Facilita debugging e monitoramento.

---

## 🎯 Benefícios

### **Para o Usuário:**

1. ✅ **Experiência Melhor** - Só vê datas que realmente pode agendar
2. ✅ **Menos Frustração** - Não precisa tentar múltiplas datas
3. ✅ **Mais Rápido** - Escolhe e agenda direto
4. ✅ **Mais Confiança** - Sistema parece mais inteligente

### **Para o Estabelecimento:**

1. ✅ **Menos Abandono** - Usuários completam o agendamento
2. ✅ **Melhor Conversão** - Fluxo mais fluido
3. ✅ **Menos Suporte** - Menos reclamações sobre "datas sem horário"

---

## 🧪 Cenários de Teste

### **Teste 1: Dia com Horários Disponíveis**
```
Situação: Terça-feira com 5 horários livres
Resultado: Data aparece na lista
```

### **Teste 2: Dia Totalmente Ocupado**
```
Situação: Quarta-feira com todos horários agendados
Resultado: Data NÃO aparece na lista
```

### **Teste 3: Dia com Poucos Horários**
```
Situação: Quinta-feira com apenas 1 horário livre
Resultado: Data aparece na lista
```

### **Teste 4: Período Muito Ocupado**
```
Situação: Próximos 10 dias quase todos ocupados
Resultado: Sistema busca até 30 dias para encontrar 7 datas disponíveis
```

### **Teste 5: Serviço Longo**
```
Situação: Serviço de 60 minutos em dia com poucos horários
Resultado: Só mostra datas onde cabem 60 minutos livres
```

---

## 📝 Arquivos Modificados

1. **`application/controllers/Webhook_waha.php`**
   - Método `obter_datas_disponiveis()` - Linhas 1151-1186
   - Método `processar_estado_data()` - Linhas 597-600
   - Método `enviar_opcoes_data()` - Linhas 875-877

---

## 🔍 Considerações Técnicas

### **Performance:**

A validação de horários para cada data pode aumentar o tempo de processamento, mas:

- ✅ Acontece apenas 1 vez por agendamento
- ✅ Melhora drasticamente a UX
- ✅ Reduz mensagens de erro
- ✅ Cache pode ser implementado no futuro se necessário

### **Escalabilidade:**

- Busca até 30 dias (máximo ~30 iterações)
- Para cada dia aberto, verifica horários (1 query)
- Total: ~10-15 queries por agendamento
- Aceitável para volume médio de uso

---

## 🎉 Conclusão

Esta correção elimina um dos principais pontos de frustração do bot, garantindo que o usuário sempre veja apenas opções viáveis. A experiência de agendamento agora é muito mais fluida e profissional.

---

## 📌 Próximas Melhorias Possíveis

1. **Cache de Disponibilidade** - Armazenar datas/horários disponíveis por alguns minutos
2. **Sugestão Inteligente** - "Próxima data disponível: 02/01/2026"
3. **Filtro por Período** - "Manhã" ou "Tarde"
4. **Notificação de Vaga** - Avisar quando surgir horário em data desejada
