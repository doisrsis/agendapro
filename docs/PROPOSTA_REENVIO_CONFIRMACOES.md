# Proposta: Sistema de Reenvio Automático de Confirmações

**Autor:** Rafael Dias - doisr.com.br
**Data:** 09/01/2026 22:45

---

## 📋 Situação Atual

### **O que já existe:**

1. ✅ **Envio inicial de confirmação**
   - Envia X horas antes OU no dia anterior em horário fixo
   - Atualiza `confirmacao_enviada = 1` e `confirmacao_enviada_em`
   - Bot fica aguardando resposta do cliente

2. ✅ **Cooldown de 23 horas**
   - Evita spam enviando a mesma confirmação várias vezes
   - Só reenvia após 23h se ainda estiver pendente

3. ✅ **Cancelamento automático** (já implementado mas não configurado)
   - Cancela agendamentos não confirmados X horas antes
   - Configurável por estabelecimento
   - Envia notificação de cancelamento

### **O que está faltando:**

❌ **Sistema de tentativas múltiplas** antes de cancelar
❌ **Intervalo configurável** entre tentativas
❌ **Contador de tentativas** para controle
❌ **Notificação progressiva** (mais urgente a cada tentativa)

---

## 🎯 Proposta de Solução

### **Opção 1: Sistema Simples (Recomendado)**

**Funcionamento:**
1. Primeira confirmação enviada (dia anterior ou X horas antes)
2. Se não responder em Y horas, reenvia (tentativa 2)
3. Se não responder em Y horas, reenvia (tentativa 3)
4. Se não responder em Y horas, **cancela automaticamente**

**Configurações no estabelecimento:**
- `confirmacao_max_tentativas` (ex: 3)
- `confirmacao_intervalo_tentativas_horas` (ex: 4 horas)
- `confirmacao_cancelar_automatico` (sim/não)

**Vantagens:**
- ✅ Simples de implementar
- ✅ Fácil de entender e configurar
- ✅ Não sobrecarrega o cliente com mensagens
- ✅ Usa estrutura de banco existente

**Desvantagens:**
- ⚠️ Requer adicionar 2 campos na tabela `agendamentos`
- ⚠️ Requer adicionar 3 campos na tabela `estabelecimentos`

---

### **Opção 2: Sistema Avançado com Escalação**

**Funcionamento:**
1. Primeira confirmação (tom neutro)
2. Segunda tentativa após X horas (tom mais urgente)
3. Terceira tentativa após X horas (tom de última chance)
4. Cancelamento automático com notificação

**Mensagens progressivas:**
- **Tentativa 1:** "Olá! Confirme seu agendamento..."
- **Tentativa 2:** "⚠️ Ainda não recebemos sua confirmação..."
- **Tentativa 3:** "🚨 ÚLTIMA CHANCE! Seu agendamento será cancelado em X horas..."
- **Cancelamento:** "❌ Seu agendamento foi cancelado por falta de confirmação"

**Configurações no estabelecimento:**
- `confirmacao_max_tentativas` (ex: 3)
- `confirmacao_intervalo_tentativas_horas` (ex: 4)
- `confirmacao_cancelar_automatico` (sim/não)
- `confirmacao_mensagem_urgente` (personalizada)

**Vantagens:**
- ✅ Mais efetivo para obter resposta
- ✅ Cliente sabe que vai ser cancelado
- ✅ Reduz no-shows

**Desvantagens:**
- ⚠️ Mais complexo de implementar
- ⚠️ Pode ser percebido como spam por alguns clientes
- ⚠️ Requer mais campos de configuração

---

### **Opção 3: Sistema Híbrido (Melhor Custo-Benefício)**

**Funcionamento:**
1. Primeira confirmação (dia anterior ou X horas antes)
2. Se não responder, aguarda Y horas
3. Segunda tentativa com mensagem mais direta
4. Se não responder, aguarda Y horas
5. **Última tentativa** com aviso de cancelamento
6. Se não responder, cancela automaticamente

**Exemplo prático:**
```
Dia anterior 22:15 → Primeira confirmação
Não respondeu
↓
Dia do agendamento 08:00 → Segunda tentativa (4h antes)
Não respondeu
↓
Dia do agendamento 10:00 → Última tentativa (2h antes)
"🚨 Seu agendamento será cancelado em 1 hora se não confirmar!"
Não respondeu
↓
Dia do agendamento 11:00 → Cancelamento automático (1h antes)
```

**Configurações no estabelecimento:**
- `confirmacao_max_tentativas` (padrão: 3)
- `confirmacao_intervalo_tentativas_horas` (padrão: 4)
- `confirmacao_cancelar_automatico` (padrão: sim)
- `confirmacao_horas_antes_cancelamento` (padrão: 1)

**Vantagens:**
- ✅ Equilíbrio entre efetividade e simplicidade
- ✅ Dá múltiplas chances ao cliente
- ✅ Avisa antes de cancelar
- ✅ Cancela com tempo suficiente para liberar horário

**Desvantagens:**
- ⚠️ Requer modificações no banco de dados
- ⚠️ Requer ajustes na lógica do cron

---

## 🗄️ Alterações Necessárias no Banco

### **Tabela `agendamentos`:**
```sql
ALTER TABLE `agendamentos`
ADD COLUMN `confirmacao_tentativas` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT 'Número de tentativas de confirmação enviadas',
ADD COLUMN `confirmacao_ultima_tentativa` DATETIME NULL COMMENT 'Data/hora da última tentativa de confirmação';
```

### **Tabela `estabelecimentos`:**
```sql
ALTER TABLE `estabelecimentos`
ADD COLUMN `confirmacao_max_tentativas` TINYINT(1) UNSIGNED DEFAULT 3 COMMENT 'Máximo de tentativas de confirmação',
ADD COLUMN `confirmacao_intervalo_tentativas_horas` TINYINT(2) UNSIGNED DEFAULT 4 COMMENT 'Intervalo em horas entre tentativas',
ADD COLUMN `confirmacao_cancelar_automatico` ENUM('sim','nao') DEFAULT 'sim' COMMENT 'Cancelar automaticamente se não confirmar';
```

---

## 📊 Comparação das Opções

| Critério | Opção 1 | Opção 2 | Opção 3 |
|----------|---------|---------|---------|
| **Complexidade** | Baixa | Alta | Média |
| **Efetividade** | Média | Alta | Alta |
| **Experiência do Cliente** | Boa | Pode incomodar | Muito Boa |
| **Tempo de Implementação** | 1-2h | 3-4h | 2-3h |
| **Manutenção** | Fácil | Complexa | Média |
| **Flexibilidade** | Média | Alta | Alta |

---

## 💡 Recomendação Final

**Recomendo a Opção 3 (Sistema Híbrido)** pelos seguintes motivos:

1. ✅ **Equilíbrio perfeito** entre simplicidade e efetividade
2. ✅ **Experiência do cliente** é respeitosa mas firme
3. ✅ **Reduz no-shows** sem ser invasivo
4. ✅ **Tempo de implementação** razoável (2-3 horas)
5. ✅ **Fácil de configurar** e ajustar por estabelecimento
6. ✅ **Aproveita estrutura existente** de cancelamento automático

---

## 🔄 Fluxo Proposto (Opção 3)

```
┌─────────────────────────────────────────────────────────────┐
│ AGENDAMENTO CRIADO (status: pendente)                       │
│ confirmacao_tentativas = 0                                   │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ CRON: Enviar Confirmações (roda a cada 1 hora)             │
│ Verifica: dia anterior OU X horas antes                     │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ PRIMEIRA TENTATIVA                                           │
│ - Envia mensagem de confirmação                             │
│ - confirmacao_tentativas = 1                                 │
│ - confirmacao_ultima_tentativa = NOW()                       │
│ - Bot aguarda resposta                                       │
└─────────────────────────────────────────────────────────────┘
                           ↓
              ┌────────────┴────────────┐
              │                         │
         RESPONDEU?                 NÃO RESPONDEU
              │                         │
              ↓                         ↓
    ┌─────────────────┐    ┌──────────────────────────┐
    │ status =        │    │ Aguarda Y horas          │
    │ confirmado      │    │ (intervalo configurado)  │
    │ FIM ✅          │    └──────────────────────────┘
    └─────────────────┘                 ↓
                           ┌──────────────────────────┐
                           │ SEGUNDA TENTATIVA        │
                           │ - Mensagem mais direta   │
                           │ - tentativas = 2         │
                           │ - ultima_tentativa = NOW │
                           └──────────────────────────┘
                                        ↓
                           ┌────────────┴────────────┐
                           │                         │
                      RESPONDEU?                 NÃO RESPONDEU
                           │                         │
                           ↓                         ↓
                 ┌─────────────────┐    ┌──────────────────────────┐
                 │ status =        │    │ Aguarda Y horas          │
                 │ confirmado      │    └──────────────────────────┘
                 │ FIM ✅          │                 ↓
                 └─────────────────┘    ┌──────────────────────────┐
                                        │ TERCEIRA TENTATIVA       │
                                        │ - Aviso de cancelamento  │
                                        │ - tentativas = 3         │
                                        │ - ultima_tentativa = NOW │
                                        └──────────────────────────┘
                                                     ↓
                                        ┌────────────┴────────────┐
                                        │                         │
                                   RESPONDEU?                 NÃO RESPONDEU
                                        │                         │
                                        ↓                         ↓
                              ┌─────────────────┐    ┌──────────────────────────┐
                              │ status =        │    │ CANCELAMENTO AUTOMÁTICO  │
                              │ confirmado      │    │ - status = cancelado     │
                              │ FIM ✅          │    │ - cancelado_por = sistema│
                              └─────────────────┘    │ - Notifica cliente       │
                                                     │ FIM ❌                   │
                                                     └──────────────────────────┘
```

---

## 🚀 Próximos Passos

Se aprovar a **Opção 3**, vou:

1. ✅ Criar migration SQL para adicionar campos
2. ✅ Atualizar model `Agendamento_model`
3. ✅ Modificar lógica do cron `enviar_confirmacoes`
4. ✅ Adicionar mensagens progressivas
5. ✅ Atualizar painel de configurações
6. ✅ Testar fluxo completo
7. ✅ Documentar configurações

**Tempo estimado:** 2-3 horas

---

## ⚠️ Considerações Importantes

1. **Evitar duplicidade:** A lógica atual de cooldown (23h) será **substituída** pela lógica de tentativas
2. **Compatibilidade:** Agendamentos antigos terão `confirmacao_tentativas = 0` (primeira tentativa)
3. **Configuração padrão:** Valores sensatos para não incomodar clientes
4. **Flexibilidade:** Cada estabelecimento pode ajustar conforme sua necessidade
5. **Logs detalhados:** Registrar cada tentativa para análise

---

**Aguardo sua aprovação para prosseguir com a implementação!**
