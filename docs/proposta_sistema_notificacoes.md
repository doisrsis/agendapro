# 📢 PROPOSTA: Sistema de Notificações para Profissionais e Estabelecimento

**Autor:** Rafael Dias - doisr.com.br
**Data:** 16/01/2026
**Versão:** 1.0

---

## 📊 ANÁLISE DO SISTEMA ATUAL

### ✅ O que já existe:

#### Notificações para CLIENTES (via WhatsApp):
- ✅ Agendamento criado (pendente)
- ✅ Agendamento confirmado
- ✅ Lembrete pré-atendimento
- ✅ Cancelamento
- ✅ Reagendamento
- ✅ Início de atendimento
- ✅ Finalização (pedir avaliação)
- ✅ Lembrete de pagamento pendente

#### Notificações para PROFISSIONAIS/ESTABELECIMENTO (via WhatsApp):
- ✅ Novo agendamento
- ✅ Cancelamento
- ✅ Reagendamento

#### Sistema de Notificações Internas (Painel):
- ✅ Modelo de notificações no banco de dados
- ✅ Sistema de leitura/não lidas
- ✅ Notificações por usuário
- ❌ **NÃO ESTÁ SENDO USADO ATIVAMENTE**

---

## 🎯 GAPS IDENTIFICADOS

### 1. **Falta de Notificações em Tempo Real no Painel**
- Profissionais não veem notificações no sistema
- Dependem apenas do WhatsApp
- Sem histórico de notificações no painel

### 2. **Falta de Notificações Importantes**
- Cliente não compareceu (no-show)
- Cliente atrasado
- Horário próximo (15min antes)
- Confirmação recebida
- Pagamento recebido
- Fila de espera disponível

### 3. **Falta de Configuração de Preferências**
- Profissional não pode escolher quais notificações receber
- Não há opção de canal (WhatsApp, Email, Painel)
- Sem controle de horários (não perturbar)

### 4. **Falta de Notificações para Estabelecimento**
- Relatórios diários/semanais
- Alertas de performance
- Avisos de sistema

---

## 💡 PROPOSTA DE NOTIFICAÇÕES

### 📱 CANAIS DE NOTIFICAÇÃO

#### 1. **WhatsApp** (Prioritário)
- ✅ Já implementado
- ✅ Alta taxa de leitura
- ✅ Notificações em tempo real

#### 2. **Painel Web** (Novo)
- 🔔 Badge de notificações não lidas
- 📋 Centro de notificações
- 🔴 Notificações em tempo real (WebSocket ou polling)
- 📊 Histórico completo

#### 3. **Email** (Opcional)
- 📧 Resumos diários/semanais
- 📊 Relatórios
- ⚠️ Alertas importantes

#### 4. **SMS** (Futuro)
- 📱 Backup para WhatsApp
- ⚠️ Notificações críticas

---

## 📋 TIPOS DE NOTIFICAÇÕES PROPOSTAS

### 🟢 PARA PROFISSIONAIS

#### **A. Notificações de Agendamento**

##### 1. **Novo Agendamento** ⭐ (JÁ EXISTE)
- **Quando:** Cliente agenda
- **Canais:** WhatsApp + Painel
- **Conteúdo:**
  - Nome do cliente
  - Serviço
  - Data e horário
  - Valor
  - WhatsApp do cliente
- **Ação:** Visualizar detalhes

##### 2. **Agendamento Confirmado pelo Cliente** 🆕
- **Quando:** Cliente confirma presença
- **Canais:** WhatsApp + Painel
- **Conteúdo:**
  - Cliente confirmou presença
  - Data e horário
  - Serviço
- **Ação:** Preparar atendimento

##### 3. **Cancelamento** ⭐ (JÁ EXISTE)
- **Quando:** Cliente cancela
- **Canais:** WhatsApp + Painel
- **Conteúdo:**
  - Cliente que cancelou
  - Data e horário liberado
  - Motivo (se informado)
- **Ação:** Horário liberado para outros

##### 4. **Reagendamento** ⭐ (JÁ EXISTE)
- **Quando:** Cliente reagenda
- **Canais:** WhatsApp + Painel
- **Conteúdo:**
  - Horário anterior
  - Novo horário
  - Cliente
- **Ação:** Atualizar agenda

#### **B. Notificações de Atendimento**

##### 5. **Próximo Atendimento (15min antes)** 🆕
- **Quando:** 15 minutos antes do horário
- **Canais:** Painel + WhatsApp (opcional)
- **Conteúdo:**
  - Cliente chegando em 15min
  - Serviço a realizar
  - Observações do cliente
- **Ação:** Preparar materiais/espaço

##### 6. **Cliente Atrasado** 🆕
- **Quando:** 10min após horário marcado sem check-in
- **Canais:** Painel
- **Conteúdo:**
  - Cliente atrasado
  - Tempo de atraso
  - WhatsApp para contato
- **Ação:** Ligar para cliente ou remarcar

##### 7. **Cliente Não Compareceu (No-Show)** 🆕
- **Quando:** 30min após horário sem check-in
- **Canais:** Painel + WhatsApp
- **Conteúdo:**
  - Cliente não compareceu
  - Horário perdido
  - Sugestão: marcar como faltou
- **Ação:** Liberar horário, registrar falta

##### 8. **Horário Livre por Cancelamento** 🆕
- **Quando:** Cancelamento libera horário próximo (hoje/amanhã)
- **Canais:** Painel + WhatsApp
- **Conteúdo:**
  - Horário disponível
  - Data e hora
  - Sugestão: oferecer para fila de espera
- **Ação:** Contatar clientes da fila

#### **C. Notificações Financeiras**

##### 9. **Pagamento Recebido** 🆕
- **Quando:** Cliente paga agendamento
- **Canais:** Painel + WhatsApp (opcional)
- **Conteúdo:**
  - Cliente que pagou
  - Valor recebido
  - Método (PIX, cartão, etc)
  - Data e horário do agendamento
- **Ação:** Confirmar recebimento

##### 10. **Comissão Disponível** 🆕
- **Quando:** Fim do dia/semana
- **Canais:** Painel + WhatsApp
- **Conteúdo:**
  - Total de comissões do período
  - Quantidade de atendimentos
  - Detalhamento por serviço
- **Ação:** Visualizar relatório

#### **D. Notificações de Performance**

##### 11. **Resumo Diário** 🆕
- **Quando:** Fim do dia (20h)
- **Canais:** WhatsApp + Email
- **Conteúdo:**
  - Total de atendimentos
  - Faturamento do dia
  - Taxa de ocupação
  - Avaliações recebidas
- **Ação:** Acompanhar performance

##### 12. **Meta Atingida** 🆕
- **Quando:** Profissional atinge meta configurada
- **Canais:** Painel + WhatsApp
- **Conteúdo:**
  - Meta atingida (ex: 10 atendimentos/dia)
  - Parabenização
  - Progresso
- **Ação:** Motivação

##### 13. **Avaliação Recebida** 🆕
- **Quando:** Cliente avalia atendimento
- **Canais:** Painel + WhatsApp
- **Conteúdo:**
  - Nota recebida
  - Comentário (se houver)
  - Cliente que avaliou
- **Ação:** Responder/agradecer

---

### 🏢 PARA ESTABELECIMENTO (ADMIN/GERENTE)

#### **E. Notificações Operacionais**

##### 14. **Novo Agendamento (Visão Geral)** 🆕
- **Quando:** Qualquer agendamento novo
- **Canais:** Painel
- **Conteúdo:**
  - Cliente
  - Profissional
  - Serviço
  - Data/hora
  - Valor
- **Ação:** Monitorar agenda

##### 15. **Cancelamento (Visão Geral)** 🆕
- **Quando:** Qualquer cancelamento
- **Canais:** Painel
- **Conteúdo:**
  - Cliente
  - Profissional
  - Motivo
  - Horário liberado
- **Ação:** Analisar motivos, oferecer horário

##### 16. **Taxa de Ocupação Baixa** 🆕
- **Quando:** Dia com menos de 50% de ocupação
- **Canais:** Painel + WhatsApp
- **Conteúdo:**
  - Data com baixa ocupação
  - Horários disponíveis
  - Sugestão: promoção/divulgação
- **Ação:** Ações de marketing

##### 17. **Profissional sem Agendamentos** 🆕
- **Quando:** Profissional sem agendamentos hoje/amanhã
- **Canais:** Painel
- **Conteúdo:**
  - Profissional ocioso
  - Horários disponíveis
- **Ação:** Redistribuir clientes, folga

#### **F. Notificações Financeiras**

##### 18. **Pagamento Aprovado** 🆕
- **Quando:** Pagamento PIX/cartão aprovado
- **Canais:** Painel
- **Conteúdo:**
  - Cliente
  - Valor
  - Método
  - Agendamento
- **Ação:** Confirmar recebimento

##### 19. **Pagamento Pendente Expirado** 🆕
- **Quando:** Cliente não paga no prazo
- **Canais:** Painel
- **Conteúdo:**
  - Cliente
  - Valor
  - Agendamento cancelado
- **Ação:** Liberar horário

##### 20. **Resumo Financeiro Diário** 🆕
- **Quando:** Fim do dia (21h)
- **Canais:** Email + Painel
- **Conteúdo:**
  - Faturamento do dia
  - Pagamentos recebidos
  - Pagamentos pendentes
  - Comissões a pagar
- **Ação:** Fechar caixa

##### 21. **Resumo Financeiro Semanal** 🆕
- **Quando:** Segunda-feira (9h)
- **Canais:** Email + WhatsApp
- **Conteúdo:**
  - Faturamento da semana
  - Comparativo com semana anterior
  - Top serviços
  - Top profissionais
- **Ação:** Análise de performance

#### **G. Notificações de Sistema**

##### 22. **WhatsApp Desconectado** 🆕
- **Quando:** Sessão WAHA desconecta
- **Canais:** Painel + Email + WhatsApp (se possível)
- **Conteúdo:**
  - WhatsApp desconectado
  - Notificações não estão sendo enviadas
  - Ação urgente necessária
- **Ação:** Reconectar WhatsApp

##### 23. **Erro em Cron Jobs** 🆕
- **Quando:** Cron falha 3x seguidas
- **Canais:** Email + Painel
- **Conteúdo:**
  - Cron que falhou
  - Erro ocorrido
  - Última execução bem-sucedida
- **Ação:** Verificar sistema

##### 24. **Backup Realizado** 🆕
- **Quando:** Backup automático concluído
- **Canais:** Email
- **Conteúdo:**
  - Backup concluído com sucesso
  - Tamanho do arquivo
  - Data/hora
- **Ação:** Confirmar integridade

##### 25. **Atualização Disponível** 🆕
- **Quando:** Nova versão do sistema
- **Canais:** Painel
- **Conteúdo:**
  - Nova versão disponível
  - Novidades
  - Link para atualizar
- **Ação:** Agendar atualização

---

## 🎨 INTERFACE PROPOSTA

### 1. **Badge de Notificações (Header)**
```
🔔 (3)  ← Badge com contador
```

### 2. **Dropdown de Notificações**
```
┌─────────────────────────────────────┐
│ 🔔 Notificações (3 não lidas)       │
├─────────────────────────────────────┤
│ 🆕 Novo Agendamento                 │
│ Maria Silva - Corte - 16:00         │
│ há 5 minutos                    [●] │
├─────────────────────────────────────┤
│ ✅ Confirmação Recebida             │
│ João Santos confirmou - 14:00       │
│ há 10 minutos                   [●] │
├─────────────────────────────────────┤
│ ⏰ Próximo Atendimento              │
│ Pedro Lima em 15 minutos            │
│ há 1 minuto                     [●] │
├─────────────────────────────────────┤
│          Ver todas (12)             │
└─────────────────────────────────────┘
```

### 3. **Centro de Notificações (Página Completa)**
```
┌─────────────────────────────────────────────────┐
│ 🔔 Central de Notificações                      │
├─────────────────────────────────────────────────┤
│ Filtros: [Todas] [Não lidas] [Hoje] [Semana]   │
│ Tipos: [Agendamentos] [Financeiro] [Sistema]    │
├─────────────────────────────────────────────────┤
│ HOJE - 16/01/2026                               │
│                                                 │
│ 🆕 15:30 - Novo Agendamento                     │
│    Maria Silva agendou Corte para 16:00        │
│    [Ver Detalhes] [Marcar como lida]           │
│                                                 │
│ ✅ 15:20 - Confirmação Recebida                 │
│    João Santos confirmou presença às 14:00     │
│    [Ver Agendamento]                            │
│                                                 │
│ ❌ 14:50 - Cancelamento                         │
│    Ana Costa cancelou - Horário 13:00 livre    │
│    Motivo: Imprevisto                           │
│    [Oferecer para Fila]                         │
├─────────────────────────────────────────────────┤
│ ONTEM - 15/01/2026                              │
│ ...                                             │
└─────────────────────────────────────────────────┘
```

### 4. **Notificação Toast (Tempo Real)**
```
┌─────────────────────────────────┐
│ 🆕 Novo Agendamento             │
│ Maria Silva - Corte - 16:00     │
│ [Ver] [Fechar]                  │
└─────────────────────────────────┘
```

---

## ⚙️ CONFIGURAÇÕES DE NOTIFICAÇÕES

### **Painel de Preferências do Profissional**

```
┌─────────────────────────────────────────────────┐
│ ⚙️ Preferências de Notificações                 │
├─────────────────────────────────────────────────┤
│                                                 │
│ 📱 CANAIS DE NOTIFICAÇÃO                        │
│                                                 │
│ ☑ WhatsApp (75 98889-0006)                      │
│ ☑ Painel Web                                    │
│ ☐ Email (profissional@email.com)               │
│ ☐ SMS                                           │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│ 🔔 TIPOS DE NOTIFICAÇÕES                        │
│                                                 │
│ Agendamentos:                                   │
│ ☑ Novo agendamento        [WhatsApp] [Painel]  │
│ ☑ Confirmação recebida    [WhatsApp] [Painel]  │
│ ☑ Cancelamento            [WhatsApp] [Painel]  │
│ ☑ Reagendamento           [WhatsApp] [Painel]  │
│                                                 │
│ Atendimento:                                    │
│ ☑ Próximo cliente (15min) [Painel]             │
│ ☑ Cliente atrasado        [Painel]             │
│ ☑ No-show                 [WhatsApp] [Painel]  │
│                                                 │
│ Financeiro:                                     │
│ ☑ Pagamento recebido      [Painel]             │
│ ☑ Comissão disponível     [WhatsApp]           │
│ ☑ Resumo diário           [WhatsApp] [Email]   │
│                                                 │
│ Performance:                                    │
│ ☑ Meta atingida           [WhatsApp] [Painel]  │
│ ☑ Avaliação recebida      [WhatsApp] [Painel]  │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│ ⏰ HORÁRIOS (Não Perturbar)                     │
│                                                 │
│ ☑ Ativar modo "Não Perturbar"                  │
│   Das [22:00] às [08:00]                        │
│   Dias: [Todos os dias] ▼                       │
│                                                 │
│ ⚠️ Notificações urgentes sempre serão enviadas  │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│ 🔊 SOM E VIBRAÇÃO                               │
│                                                 │
│ ☑ Som de notificação                            │
│ ☑ Vibração                                      │
│ ☐ Notificações silenciosas                     │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│           [Salvar Preferências]                 │
│                                                 │
└─────────────────────────────────────────────────┘
```

---

## 🗄️ ESTRUTURA DE BANCO DE DADOS

### **Tabela: `notificacoes`** (já existe, melhorar)

```sql
CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estabelecimento_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `profissional_id` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `categoria` enum('agendamento','financeiro','sistema','performance','atendimento') DEFAULT 'agendamento',
  `titulo` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `dados_json` text COMMENT 'Dados adicionais em JSON',
  `link` varchar(255) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `cor` varchar(20) DEFAULT NULL,
  `prioridade` enum('baixa','media','alta','urgente') DEFAULT 'media',
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` datetime DEFAULT NULL,
  `enviada_whatsapp` tinyint(1) DEFAULT 0,
  `enviada_email` tinyint(1) DEFAULT 0,
  `enviada_sms` tinyint(1) DEFAULT 0,
  `criado_em` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `profissional_id` (`profissional_id`),
  KEY `estabelecimento_id` (`estabelecimento_id`),
  KEY `lida` (`lida`),
  KEY `criado_em` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Tabela: `notificacoes_preferencias`** (nova)

```sql
CREATE TABLE `notificacoes_preferencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `profissional_id` int(11) DEFAULT NULL,
  `tipo_notificacao` varchar(50) NOT NULL,
  `canal_whatsapp` tinyint(1) DEFAULT 1,
  `canal_painel` tinyint(1) DEFAULT 1,
  `canal_email` tinyint(1) DEFAULT 0,
  `canal_sms` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime NOT NULL,
  `atualizado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_tipo` (`usuario_id`,`tipo_notificacao`),
  UNIQUE KEY `profissional_tipo` (`profissional_id`,`tipo_notificacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Tabela: `notificacoes_nao_perturbar`** (nova)

```sql
CREATE TABLE `notificacoes_nao_perturbar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `profissional_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `dias_semana` varchar(50) DEFAULT 'todos' COMMENT 'todos, seg-sex, fim-semana, etc',
  `criado_em` datetime NOT NULL,
  `atualizado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `profissional_id` (`profissional_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🚀 IMPLEMENTAÇÃO SUGERIDA

### **FASE 1: Fundação (1-2 semanas)**
1. ✅ Melhorar tabela `notificacoes`
2. ✅ Criar tabelas de preferências
3. ✅ Criar library `Notificacao_lib.php`
4. ✅ Implementar badge no header
5. ✅ Criar dropdown de notificações

### **FASE 2: Notificações Básicas (1 semana)**
6. ✅ Novo agendamento (painel)
7. ✅ Confirmação recebida
8. ✅ Cancelamento (painel)
9. ✅ Pagamento recebido
10. ✅ Próximo atendimento (15min)

### **FASE 3: Notificações Avançadas (1 semana)**
11. ✅ Cliente atrasado
12. ✅ No-show
13. ✅ Horário livre
14. ✅ Resumo diário
15. ✅ Meta atingida

### **FASE 4: Configurações (1 semana)**
16. ✅ Painel de preferências
17. ✅ Modo "Não Perturbar"
18. ✅ Escolha de canais
19. ✅ Teste de notificações

### **FASE 5: Notificações de Sistema (1 semana)**
20. ✅ WhatsApp desconectado
21. ✅ Erro em cron
22. ✅ Resumos financeiros
23. ✅ Relatórios por email

---

## 📊 PRIORIZAÇÃO

### 🔴 **ALTA PRIORIDADE** (Implementar primeiro)
1. Novo agendamento (painel)
2. Confirmação recebida
3. Próximo atendimento (15min)
4. Pagamento recebido
5. WhatsApp desconectado

### 🟡 **MÉDIA PRIORIDADE** (Implementar depois)
6. Cliente atrasado
7. No-show
8. Resumo diário
9. Cancelamento (painel)
10. Horário livre

### 🟢 **BAIXA PRIORIDADE** (Implementar por último)
11. Meta atingida
12. Avaliação recebida
13. Resumos semanais
14. Taxa de ocupação baixa
15. Backup realizado

---

## 💰 ESTIMATIVA DE ESFORÇO

| Fase | Descrição | Tempo Estimado | Complexidade |
|------|-----------|----------------|--------------|
| 1 | Fundação (BD + UI básica) | 8-12 horas | Média |
| 2 | Notificações básicas | 6-8 horas | Baixa |
| 3 | Notificações avançadas | 8-10 horas | Média |
| 4 | Configurações | 6-8 horas | Média |
| 5 | Sistema e relatórios | 8-10 horas | Alta |
| **TOTAL** | **Sistema completo** | **36-48 horas** | **Média-Alta** |

---

## 🎯 BENEFÍCIOS ESPERADOS

### Para Profissionais:
- ✅ Maior controle da agenda
- ✅ Menos surpresas (no-shows, atrasos)
- ✅ Melhor preparação para atendimentos
- ✅ Acompanhamento de performance
- ✅ Motivação com metas

### Para Estabelecimento:
- ✅ Visão completa da operação
- ✅ Identificação rápida de problemas
- ✅ Melhor gestão financeira
- ✅ Relatórios automáticos
- ✅ Alertas de sistema

### Para Clientes:
- ✅ Melhor experiência (profissional preparado)
- ✅ Menos espera
- ✅ Atendimento pontual

---

## ❓ PERGUNTAS PARA DECISÃO

1. **Quais notificações são mais importantes para você?**
   - Priorizar implementação

2. **Prefere começar com notificações no painel ou melhorar WhatsApp?**
   - Definir fase 1

3. **Quer notificações em tempo real ou pode ser com delay?**
   - Define se usa WebSocket ou polling

4. **Profissionais devem poder configurar preferências?**
   - Define se implementa fase 4

5. **Quer relatórios por email?**
   - Define se implementa envio de email

6. **Quantos profissionais por estabelecimento em média?**
   - Define lógica de notificação (individual vs estabelecimento)

---

## 📝 PRÓXIMOS PASSOS

1. **Revisar proposta** e escolher prioridades
2. **Definir quais notificações implementar** primeiro
3. **Aprovar estrutura de banco de dados**
4. **Definir design da interface**
5. **Iniciar implementação** fase por fase

---

**Aguardo seu feedback para começarmos a implementação! 🚀**

Qual fase você gostaria que eu começasse? Ou prefere que eu ajuste algo na proposta?
