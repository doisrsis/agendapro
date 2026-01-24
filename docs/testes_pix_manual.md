# 🧪 TESTES - FUNCIONALIDADE PIX MANUAL

**Autor:** Rafael Dias - doisr.com.br
**Data:** 23/01/2026
**Versão:** 1.0

---

## 📊 RESUMO DA IMPLEMENTAÇÃO

### Fases Concluídas:
1. ✅ **FASE 1:** Estrutura de Banco de Dados
2. ✅ **FASE 2:** Biblioteca PIX
3. ✅ **FASE 3:** Painel de Configuração
4. ✅ **FASE 4:** Bot WhatsApp - Fluxo PIX Manual
5. ✅ **FASE 5:** Bot WhatsApp - Tratamento de Mídia
6. ✅ **FASE 6:** Notificações - Lembrete PIX Manual
7. ✅ **FASE 7:** Painel Agendamentos - Confirmar Pagamento

### Arquivos Modificados/Criados:
1. `docs/sql_pix_manual.sql` ✅
2. `application/libraries/Pix_lib.php` ✅
3. `application/views/painel/configuracoes/index.php` ✅
4. `application/models/Estabelecimento_model.php` ✅
5. `application/controllers/painel/Configuracoes.php` ✅
6. `application/controllers/Webhook_waha.php` ✅
7. `application/libraries/Notificacao_whatsapp_lib.php` ✅
8. `application/controllers/painel/Agendamentos.php` ✅
9. `application/views/painel/agendamentos/visualizar.php` ✅
10. `application/views/agenda/agendamentos/editar.php` ✅

---

## 🧪 TESTES POR FASE

---

### ✅ FASE 1: ESTRUTURA DE BANCO DE DADOS

#### Teste 1.1: Verificar Campos Criados
**Objetivo:** Confirmar que os campos PIX Manual foram adicionados à tabela `estabelecimentos`

**Passos:**
1. Executar: `php docs/verificar_campos_pix.php`
2. Verificar saída no terminal

**Resultado Esperado:**
```
✅ Campos PIX Manual encontrados:

Campo                          Tipo                                      Null       Default
------------------------------------------------------------------------------------------
pagamento_tipo                 enum('mercadopago','pix_manual')         YES        mercadopago
pix_chave                      varchar(255)                             YES        NULL
pix_tipo_chave                 enum('cpf','cnpj','email'...)            YES        NULL
pix_nome_recebedor             varchar(255)                             YES        NULL
pix_cidade                     varchar(100)                             YES        NULL
```

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### ✅ FASE 2: BIBLIOTECA PIX

#### Teste 2.1: Gerar BR Code PIX
**Objetivo:** Validar geração de código PIX (copia e cola)

**Passos:**
1. Criar arquivo de teste: `docs/teste_pix_lib.php`
```php
<?php
require_once '../system/core/CodeIgniter.php';
require_once '../application/libraries/Pix_lib.php';

$pix_lib = new Pix_lib();

$br_code = $pix_lib->gerar_br_code([
    'chave_pix' => '12345678901',
    'nome_recebedor' => 'ESTABELECIMENTO TESTE',
    'cidade' => 'SALVADOR',
    'valor' => 50.00,
    'txid' => 'AG0000000001',
    'descricao' => 'Corte de Cabelo'
]);

echo "BR Code gerado:\n";
echo $br_code . "\n\n";
echo "Tamanho: " . strlen($br_code) . " caracteres\n";
```

2. Executar: `php docs/teste_pix_lib.php`

**Resultado Esperado:**
- BR Code iniciando com `00020101`
- Terminando com 4 dígitos hexadecimais (CRC16)
- Tamanho entre 100-300 caracteres

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 2.2: Validar Chave PIX
**Objetivo:** Testar validação de diferentes tipos de chave PIX

**Passos:**
1. Testar CPF válido: `12345678901`
2. Testar CNPJ válido: `12345678000199`
3. Testar Email válido: `teste@exemplo.com`
4. Testar Telefone válido: `5575999999999`
5. Testar chave inválida

**Resultado Esperado:**
- CPF/CNPJ: validação de dígitos verificadores
- Email: formato válido
- Telefone: 10-13 dígitos
- Chave inválida: retorna `false`

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### ✅ FASE 3: PAINEL DE CONFIGURAÇÃO

#### Teste 3.1: Acessar Configurações PIX Manual
**Objetivo:** Verificar interface de configuração

**Passos:**
1. Login no painel: `/painel/login`
2. Acessar: `/painel/configuracoes?aba=mercadopago`
3. Verificar dropdown "Tipo de Pagamento"
4. Selecionar "PIX Manual (Confirmação Manual)"

**Resultado Esperado:**
- Dropdown com 2 opções: Mercado Pago e PIX Manual
- Ao selecionar PIX Manual, exibir campos:
  - Chave PIX (obrigatório)
  - Tipo da Chave (obrigatório)
  - Nome do Recebedor (obrigatório)
  - Cidade (obrigatório)
- Ocultar seção Mercado Pago

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 3.2: Salvar Configuração PIX Manual
**Objetivo:** Testar salvamento de configurações

**Passos:**
1. Preencher campos PIX Manual:
   - Tipo: PIX Manual
   - Chave PIX: `12345678901`
   - Tipo da Chave: CPF
   - Nome: `ESTABELECIMENTO TESTE`
   - Cidade: `SALVADOR`
2. Clicar em "Salvar Configurações"

**Resultado Esperado:**
- Mensagem de sucesso: "Configurações de PIX Manual atualizadas!"
- Dados salvos no banco
- Ao recarregar, campos preenchidos corretamente

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 3.3: Validação de Chave PIX Inválida
**Objetivo:** Testar validação de chave PIX

**Passos:**
1. Selecionar Tipo: CPF
2. Digitar chave inválida: `12345`
3. Tentar salvar

**Resultado Esperado:**
- Mensagem de erro: "Chave PIX inválida para o tipo selecionado."
- Dados não salvos

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### ✅ FASE 4: BOT WHATSAPP - FLUXO PIX MANUAL

#### Teste 4.1: Agendamento com PIX Manual
**Objetivo:** Testar fluxo completo de agendamento via bot

**Pré-requisitos:**
- Estabelecimento configurado com PIX Manual
- Bot WAHA ativo
- Exigir pagamento ativado

**Passos:**
1. Enviar mensagem ao bot: `menu`
2. Escolher: `1 - Agendar`
3. Selecionar profissional, serviço, data e horário
4. Escolher: `1 - Pagar via PIX`

**Resultado Esperado:**
1. Mensagem de confirmação:
   ```
   🎉 Agendamento Criado!

   📋 Serviço: [Nome do Serviço]
   👤 Profissional: [Nome]
   📅 Data: [Data]
   ⏰ Horário: [Hora]
   💰 Valor: R$ [Valor]

   💳 PAGAMENTO VIA PIX

   Escaneie o QR Code abaixo ou use o código Pix Copia e Cola:
   ```

2. Receber QR Code como imagem
3. Receber código copia e cola
4. Mensagem pedindo comprovante:
   ```
   📎 Após realizar o pagamento, envie o comprovante aqui no WhatsApp.

   Confirmaremos seu agendamento assim que recebermos o pagamento. ✅
   ```

**Verificações no Banco:**
- Agendamento criado com:
  - `status = 'pendente'`
  - `pagamento_status = 'pendente'`
  - `forma_pagamento = 'pix_manual'`
  - `pagamento_pix_copia_cola` preenchido
  - `pagamento_pix_qrcode` preenchido (URL)

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 4.2: Notificação ao Profissional
**Objetivo:** Verificar se profissional recebe notificação de novo agendamento pendente

**Passos:**
1. Após criar agendamento PIX Manual (Teste 4.1)
2. Verificar WhatsApp do profissional

**Resultado Esperado:**
- Profissional recebe notificação com informações do agendamento
- Indicação de pagamento pendente

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### ✅ FASE 5: BOT WHATSAPP - TRATAMENTO DE MÍDIA

#### Teste 5.1: Enviar Comprovante (Imagem)
**Objetivo:** Testar recebimento de comprovante via WhatsApp

**Passos:**
1. Após criar agendamento PIX Manual (Teste 4.1)
2. Enviar uma imagem qualquer ao bot

**Resultado Esperado:**
- Bot responde:
  ```
  ✅ Comprovante recebido!

  Obrigado! Estamos verificando seu pagamento.

  Você receberá a confirmação do seu agendamento em breve. 🙏

  Digite menu para voltar ao menu.
  ```
- Bot NÃO processa imagem como mensagem de texto
- Bot NÃO responde com menu automático

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 5.2: Enviar Comprovante (Documento PDF)
**Objetivo:** Testar recebimento de documento

**Passos:**
1. Após criar agendamento PIX Manual
2. Enviar um PDF ao bot

**Resultado Esperado:**
- Mesma resposta do Teste 5.1
- Bot trata PDF como comprovante

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### ✅ FASE 6: NOTIFICAÇÕES - LEMBRETE PIX MANUAL

#### Teste 6.1: Lembrete com Comprovante Pendente
**Objetivo:** Verificar mensagem de lembrete para PIX Manual pendente

**Pré-requisitos:**
- Agendamento PIX Manual com `pagamento_status = 'pendente'`
- Agendamento para amanhã

**Passos:**
1. Executar CRON de lembretes ou aguardar horário automático
2. Verificar WhatsApp do cliente

**Resultado Esperado:**
- Mensagem de lembrete padrão +
- Nota de rodapé:
  ```
  📎 Caso ainda não tenha enviado o comprovante de pagamento,
  por favor, envie para confirmarmos seu agendamento.
  ```

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 6.2: Lembrete com Pagamento Confirmado
**Objetivo:** Verificar que nota de comprovante NÃO aparece quando pago

**Pré-requisitos:**
- Agendamento PIX Manual com `pagamento_status = 'pago'`

**Passos:**
1. Executar CRON de lembretes
2. Verificar mensagem

**Resultado Esperado:**
- Mensagem de lembrete padrão
- SEM nota sobre comprovante

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### ✅ FASE 7: PAINEL AGENDAMENTOS - CONFIRMAR PAGAMENTO

#### Teste 7.1: Visualizar Agendamento PIX Manual Pendente
**Objetivo:** Verificar exibição de agendamento com PIX Manual pendente

**Passos:**
1. Acessar: `/painel/agendamentos`
2. Clicar em agendamento com PIX Manual pendente

**Resultado Esperado:**
- Badge: "PIX Manual Pendente" (amarelo)
- Seção "Ações Rápidas" mostra:
  - Botão verde: "✅ Confirmar Pagamento PIX"
  - Alerta amarelo: "Aguardando Pagamento PIX"
- Botão "Confirmar" normal NÃO aparece

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 7.2: Confirmar Pagamento PIX Manual
**Objetivo:** Testar confirmação manual de pagamento

**Passos:**
1. Visualizar agendamento PIX Manual pendente
2. Clicar em "Confirmar Pagamento PIX"
3. Confirmar no popup

**Resultado Esperado:**
1. Mensagem de sucesso: "Pagamento confirmado! O cliente foi notificado via WhatsApp."
2. Agendamento atualizado:
   - `status = 'confirmado'`
   - `pagamento_status = 'pago'`
   - `confirmado_em` preenchido
3. Cliente recebe notificação de confirmação via WhatsApp
4. Profissional recebe notificação (se configurado)
5. Badge muda para: "Pago via PIX Manual" (verde)
6. Botão "Confirmar Pagamento PIX" desaparece

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

#### Teste 7.3: Editar Status de Pagamento
**Objetivo:** Testar edição manual de status

**Passos:**
1. Acessar: `/painel/agendamentos`
2. Clicar em "Editar" em qualquer agendamento
3. Verificar campos "Forma de Pagamento" e "Status do Pagamento"

**Resultado Esperado:**
- Dropdown "Forma de Pagamento" contém:
  - Não Definido
  - PIX (Mercado Pago)
  - **PIX Manual** ⭐
  - Presencial
  - Cartão

- Dropdown "Status do Pagamento" contém:
  - Não Requerido
  - Pendente
  - Pago
  - Presencial
  - Expirado
  - Cancelado

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

## 🔄 TESTES DE INTEGRAÇÃO

### Teste INT-1: Fluxo Completo PIX Manual
**Objetivo:** Testar fluxo end-to-end

**Passos:**
1. Configurar estabelecimento com PIX Manual
2. Cliente agenda via bot e escolhe PIX
3. Cliente envia comprovante
4. Estabelecimento confirma pagamento
5. Verificar notificações

**Resultado Esperado:**
- Todos os passos funcionam perfeitamente
- Notificações enviadas corretamente
- Status atualizados no banco

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### Teste INT-2: Compatibilidade com Mercado Pago
**Objetivo:** Garantir que PIX Manual não quebra Mercado Pago

**Passos:**
1. Configurar estabelecimento com Mercado Pago
2. Cliente agenda via bot e escolhe PIX
3. Verificar geração de PIX via Mercado Pago

**Resultado Esperado:**
- Fluxo Mercado Pago continua funcionando
- PIX gerado via API Mercado Pago
- Webhook de confirmação funciona

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### Teste INT-3: Pagamento Presencial
**Objetivo:** Garantir que pagamento presencial não foi afetado

**Passos:**
1. Cliente agenda via bot
2. Escolhe "Pagar no estabelecimento"

**Resultado Esperado:**
- Agendamento criado com:
  - `status = 'confirmado'`
  - `pagamento_status = 'presencial'`
  - `forma_pagamento = 'presencial'`
- Cliente recebe confirmação imediata

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

## 🐛 TESTES DE EDGE CASES

### Teste EDGE-1: PIX Manual sem Configuração
**Objetivo:** Testar erro quando PIX Manual não está configurado

**Passos:**
1. Configurar `pagamento_tipo = 'pix_manual'`
2. Deixar campos PIX vazios
3. Cliente tenta agendar

**Resultado Esperado:**
- Erro ao gerar BR Code
- Mensagem amigável ao cliente
- Log de erro registrado

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### Teste EDGE-2: Confirmar Pagamento Já Confirmado
**Objetivo:** Testar proteção contra dupla confirmação

**Passos:**
1. Confirmar pagamento PIX Manual
2. Tentar confirmar novamente

**Resultado Esperado:**
- Mensagem de erro: "Este agendamento não está aguardando confirmação..."
- Nenhuma alteração no banco

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

### Teste EDGE-3: Mídia sem Agendamento Pendente
**Objetivo:** Testar envio de mídia sem contexto

**Passos:**
1. Cliente sem agendamento pendente
2. Enviar imagem ao bot

**Resultado Esperado:**
- Bot ignora mídia
- Não responde nada ou responde com menu padrão

**Status:** [ ] Passou [ ] Falhou
**Observações:** _______________________________________________

---

## 📊 CHECKLIST FINAL

### Funcionalidades Principais
- [ ] Configuração PIX Manual no painel
- [ ] Geração de BR Code PIX
- [ ] Geração de QR Code
- [ ] Agendamento via bot com PIX Manual
- [ ] Envio de comprovante via WhatsApp
- [ ] Confirmação manual de pagamento
- [ ] Notificações ao cliente
- [ ] Notificações ao profissional
- [ ] Lembrete com nota de comprovante
- [ ] Edição de status de pagamento

### Compatibilidade
- [ ] Mercado Pago continua funcionando
- [ ] Pagamento presencial continua funcionando
- [ ] Sem pagamento continua funcionando
- [ ] Notificações existentes não foram afetadas

### Segurança
- [ ] Validação de chave PIX
- [ ] Proteção contra dupla confirmação
- [ ] Logs de erro adequados
- [ ] Permissões de acesso respeitadas

---

## 📝 OBSERVAÇÕES GERAIS

**Data do Teste:** ___/___/______
**Testador:** _____________________
**Ambiente:** [ ] Desenvolvimento [ ] Produção

**Problemas Encontrados:**
_____________________________________________________________
_____________________________________________________________
_____________________________________________________________

**Sugestões de Melhoria:**
_____________________________________________________________
_____________________________________________________________
_____________________________________________________________

---

## ✅ APROVAÇÃO

**Todos os testes passaram?** [ ] Sim [ ] Não

**Funcionalidade aprovada para produção?** [ ] Sim [ ] Não

**Assinatura:** _____________________
**Data:** ___/___/______
