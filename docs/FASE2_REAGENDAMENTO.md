# Fase 2: Reagendamento de Agendamentos

**Autor:** Rafael Dias - doisr.com.br
**Data:** 30/12/2025
**Status:** ✅ Implementado

---

## 🎯 Objetivo

Implementar funcionalidade completa de reagendamento no bot, permitindo que usuários possam alterar data e horário de seus agendamentos existentes diretamente pelo WhatsApp, com sugestão de reagendamento antes de cancelar.

---

## 🔧 Funcionalidades Implementadas

### **1. Gerenciamento de Agendamentos**

**Antes:**
- Opção "Meus Agendamentos" apenas listava agendamentos
- Não havia opção de reagendar
- Para alterar, usuário tinha que cancelar e criar novo

**Depois:**
- Lista agendamentos com opção de selecionar
- Oferece ações: Reagendar ou Cancelar
- Navegação completa entre estados

---

### **2. Fluxo de Reagendamento**

```
Menu Principal
    ↓ (2 - Meus Agendamentos)
Lista de Agendamentos
    ↓ (seleciona número)
Ações do Agendamento
    ↓ (1 - Reagendar)
Escolher Nova Data ←──────┐
    ↓ (número)            │ voltar
Escolher Novo Horário ←───┤
    ↓ (número)            │ voltar
Confirmar Reagendamento ──┘
    ↓ (1 - Sim)
Reagendamento Confirmado!
```

---

### **3. Cancelamento com Sugestão de Reagendamento**

**Fluxo Melhorado:**

```
Usuário: Escolhe "2 - Cancelar"
Bot: "⚠️ Confirmar Cancelamento

     Você tem certeza que deseja cancelar?

     1 - ❌ Sim, cancelar
     2 - 🔄 Não, prefiro reagendar"
```

Se usuário escolhe "2", vai direto para fluxo de reagendamento!

---

## 📊 Novos Estados Implementados

| Estado | Descrição | Ações Disponíveis |
|--------|-----------|-------------------|
| `gerenciando_agendamento` | Lista agendamentos do cliente | Selecionar número, menu |
| `aguardando_acao_agendamento` | Aguarda ação sobre agendamento | 1-Reagendar, 2-Cancelar, voltar |
| `reagendando_data` | Aguarda nova data | Número da data, voltar |
| `reagendando_hora` | Aguarda novo horário | Número do horário, voltar |
| `confirmando_reagendamento` | Aguarda confirmação | 1-Sim, 2-Não, voltar |

---

## 💬 Mensagens do Bot

### **Lista de Agendamentos:**
```
📅 Seus Próximos Agendamentos:

1. ✅ 02/01/2026 às 14:00
   💇 Barba
   👤 Mago

2. ⏳ 05/01/2026 às 10:00
   💇 Cabelo máquina
   👤 João

Digite o número do agendamento para gerenciar.
Ou digite menu para voltar ao menu.
```

### **Ações do Agendamento:**
```
📋 Agendamento Selecionado:

📅 Data: 02/01/2026
⏰ Horário: 14:00
💇 Serviço: Barba
👤 Profissional: Mago

O que deseja fazer?

1 - 🔄 Reagendar
2 - ❌ Cancelar

Ou digite voltar para ver outros agendamentos.
```

### **Escolher Nova Data:**
```
🔄 Reagendar Agendamento

📅 Agendamento atual: 02/01/2026 às 14:00
💇 Serviço: Barba
👤 Profissional: Mago

Escolha a nova data:

1. 03/01/2026 (Sex)
2. 05/01/2026 (Seg)
3. 06/01/2026 (Ter)
...

Digite o número da nova data.
Ou digite voltar para escolher outra ação.
```

### **Escolher Novo Horário:**
```
⏰ Escolha o Novo Horário:

📅 Agendamento atual: 02/01/2026 às 14:00
🔄 Nova data: 03/01/2026

Horários disponíveis:

1. 14:00
2. 14:30
3. 15:00
...

Digite o número do novo horário.
Ou digite voltar para escolher outra data.
```

### **Confirmação de Reagendamento:**
```
✅ Confirme o Reagendamento:

📋 Serviço: Barba
👤 Profissional: Mago

❌ De: 02/01/2026 às 14:00
✅ Para: 03/01/2026 às 15:00

Deseja confirmar o reagendamento?

1 ou Sim - Confirmar ✅
2 ou Não - Cancelar ❌

Ou digite voltar para escolher outro horário.
```

### **Reagendamento Confirmado:**
```
🎉 Reagendamento Confirmado!

📋 Serviço: Barba
👤 Profissional: Mago

❌ Era: 02/01/2026 às 14:00
✅ Agora: 03/01/2026 às 15:00

📍 Modelo Barber
📌 Rua Exemplo, 123

Até lá! 👋

Digite menu para voltar ao menu.
```

### **Confirmação de Cancelamento:**
```
⚠️ Confirmar Cancelamento

Você tem certeza que deseja cancelar o agendamento?

📅 02/01/2026 às 14:00
💇 Barba

1 - ❌ Sim, cancelar
2 - 🔄 Não, prefiro reagendar

Ou digite voltar para escolher outro agendamento.
```

---

## 🔍 Implementação Técnica

### **Arquivo:** `application/controllers/Webhook_waha.php`

### **1. Novos Métodos Implementados:**

#### **Gerenciamento:**
- `iniciar_gerenciar_agendamentos()` - Lista agendamentos com opções
- `processar_estado_gerenciando()` - Processa seleção do agendamento
- `processar_estado_acao_agendamento()` - Processa ação (reagendar/cancelar)

#### **Reagendamento - Data:**
- `enviar_opcoes_data_reagendamento()` - Mostra datas disponíveis
- `processar_estado_reagendando_data()` - Processa seleção de data

#### **Reagendamento - Horário:**
- `enviar_opcoes_hora_reagendamento()` - Mostra horários disponíveis
- `processar_estado_reagendando_hora()` - Processa seleção de horário

#### **Confirmação:**
- `enviar_confirmacao_reagendamento()` - Mostra resumo para confirmar
- `processar_estado_confirmando_reagendamento()` - Processa confirmação

---

### **2. Alterações no Switch Case:**

```php
case 'gerenciando_agendamento':
    $this->processar_estado_gerenciando($estabelecimento, $numero, $msg, $conversa, $cliente);
    break;

case 'aguardando_acao_agendamento':
    $this->processar_estado_acao_agendamento($estabelecimento, $numero, $msg, $conversa, $cliente);
    break;

case 'reagendando_data':
    $this->processar_estado_reagendando_data($estabelecimento, $numero, $msg, $conversa, $cliente);
    break;

case 'reagendando_hora':
    $this->processar_estado_reagendando_hora($estabelecimento, $numero, $msg, $conversa, $cliente);
    break;

case 'confirmando_reagendamento':
    $this->processar_estado_confirmando_reagendamento($estabelecimento, $numero, $msg, $conversa, $cliente);
    break;
```

---

### **3. Atualização do Banco de Dados:**

```php
// Incrementar contador de reagendamentos
$update_data = [
    'data' => $dados['nova_data'],
    'hora_inicio' => $hora_inicio,
    'hora_fim' => $hora_fim
];

// Se campo qtd_reagendamentos existir
if (property_exists($agendamento_atual, 'qtd_reagendamentos')) {
    $update_data['qtd_reagendamentos'] = ($agendamento_atual->qtd_reagendamentos ?? 0) + 1;
}

$this->Agendamento_model->update($agendamento_id, $update_data);
```

---

## ✅ Benefícios

### **Para o Usuário:**

1. ✅ **Flexibilidade** - Pode reagendar sem cancelar e criar novo
2. ✅ **Rapidez** - Processo simplificado em poucos passos
3. ✅ **Navegação** - Comando "voltar" em todas as etapas
4. ✅ **Clareza** - Mensagens informativas e emojis intuitivos
5. ✅ **Segurança** - Confirmação antes de efetivar mudanças

### **Para o Estabelecimento:**

1. ✅ **Menos Cancelamentos** - Usuários preferem reagendar
2. ✅ **Melhor Ocupação** - Horários são preenchidos ao invés de cancelados
3. ✅ **Satisfação** - Clientes têm mais controle
4. ✅ **Rastreamento** - Contador de reagendamentos para análise
5. ✅ **Automação** - Menos trabalho manual da equipe

---

## 🧪 Cenários de Teste

### **Teste 1: Reagendamento Completo**
```
1. Digite: "oi" → "2" (Meus Agendamentos)
2. Bot lista agendamentos
3. Digite: "1" (seleciona primeiro)
4. Digite: "1" (Reagendar)
5. Bot mostra datas disponíveis
6. Digite: "2" (escolhe data)
7. Bot mostra horários
8. Digite: "3" (escolhe horário)
9. Bot pede confirmação
10. Digite: "1" (confirma)
11. Resultado: Reagendamento confirmado ✅
12. Verificar banco: data e hora atualizadas
```

### **Teste 2: Navegação Voltar**
```
1. Inicie reagendamento
2. Escolha data
3. Digite: "voltar"
4. Resultado: Volta para lista de datas ✅
5. Digite: "voltar" novamente
6. Resultado: Volta para ações do agendamento ✅
```

### **Teste 3: Cancelamento com Sugestão**
```
1. Selecione agendamento
2. Digite: "2" (Cancelar)
3. Bot pergunta se tem certeza
4. Opções: 1-Cancelar, 2-Reagendar
5. Digite: "2" (Reagendar)
6. Resultado: Vai para fluxo de reagendamento ✅
```

### **Teste 4: Cancelamento Definitivo**
```
1. Selecione agendamento
2. Digite: "2" (Cancelar)
3. Digite: "1" (Sim, cancelar)
4. Resultado: Agendamento cancelado ✅
5. Verificar banco: status='cancelado'
```

### **Teste 5: Contador de Reagendamentos**
```
1. Reagende um agendamento
2. Verificar banco: qtd_reagendamentos = 1
3. Reagende novamente
4. Verificar banco: qtd_reagendamentos = 2 ✅
```

---

## 📝 Arquivos Modificados

1. **`application/controllers/Webhook_waha.php`**
   - Adicionados 8 novos métodos (linhas 1415-1891)
   - Alterado switch case para incluir 5 novos estados
   - Alterada chamada de `consultar_agendamentos` para `iniciar_gerenciar_agendamentos`

---

## 🔄 Integração com Funcionalidades Existentes

### **Reutilização de Métodos:**

- ✅ `obter_datas_disponiveis()` - Usado para reagendamento
- ✅ `obter_horarios_disponiveis()` - Usado para reagendamento
- ✅ Filtro de feriados aplicado automaticamente
- ✅ Filtro de horários ocupados aplicado automaticamente
- ✅ Validações de disponibilidade mantidas

### **Consistência:**

- ✅ Mesmas mensagens de navegação ("voltar", "menu")
- ✅ Mesmo padrão de emojis e formatação
- ✅ Mesma lógica de estados e transições
- ✅ Mesmos comandos de navegação

---

## 📊 Estatísticas da Implementação

| Métrica | Valor |
|---------|-------|
| Novos métodos | 8 |
| Novos estados | 5 |
| Linhas de código | ~480 |
| Mensagens únicas | 8 |
| Comandos "voltar" | 4 |

---

## 🎉 Conclusão

A Fase 2 foi implementada com sucesso! O bot agora oferece uma experiência completa de gerenciamento de agendamentos, com:

- ✅ Reagendamento intuitivo e rápido
- ✅ Sugestão de reagendamento antes de cancelar
- ✅ Navegação completa com comando "voltar"
- ✅ Mensagens claras e informativas
- ✅ Integração perfeita com funcionalidades existentes

O usuário agora tem controle total sobre seus agendamentos, podendo visualizar, reagendar ou cancelar de forma simples e eficiente, tudo pelo WhatsApp!

---

## 📌 Próximas Melhorias Possíveis

1. **Notificações** - Enviar confirmação de reagendamento por email
2. **Histórico** - Mostrar histórico de reagendamentos
3. **Limites** - Configurar limite de reagendamentos por agendamento
4. **Motivos** - Perguntar motivo do reagendamento para análise
5. **Sugestões Inteligentes** - Sugerir horários similares ao original
