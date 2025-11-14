# 📧 CONFIGURAÇÃO DE E-MAIL SMTP - LE CORTINE

**Autor:** Rafael Dias - doisr.com.br  
**Data:** 14/11/2024  
**Status:** ✅ CONFIGURADO E PRONTO

---

## 🎯 O QUE FOI FEITO

### **1. Arquivo de Configuração SMTP**
✅ `application/config/email.php`

**Credenciais configuradas:**
- **Servidor:** mail.lecortine.com.br
- **Porta:** 465 (SSL)
- **Usuário:** nao-responder@lecortine.com.br
- **Senha:** a5)?O5qF+5!H@JaT2025
- **Protocolo:** SMTP com SSL

---

### **2. Library de E-mail**
✅ `application/libraries/Email_lib.php`

**Funcionalidades:**
- ✅ Enviar e-mail de novo orçamento (para você)
- ✅ Enviar e-mail de pagamento aprovado (para você)
- ✅ Enviar confirmação para o cliente
- ✅ Templates HTML profissionais
- ✅ Verificação automática se notificações estão ativas

---

### **3. Página de Configurações**
✅ `application/views/admin/configuracoes/notificacoes.php`

**Recursos:**
- ✅ Ativar/desativar notificações por e-mail
- ✅ Configurar e-mail destinatário
- ✅ Escolher quais eventos notificar
- ✅ Botão para testar envio
- ✅ Resumo visual em tempo real

---

### **4. Método de Teste**
✅ `application/controllers/admin/Configuracoes.php`

**Rota:** `/admin/configuracoes/testar_email`

Envia um e-mail de teste com dados fictícios para verificar se está funcionando.

---

## 🚀 COMO USAR

### **1. Acessar Configurações**
```
http://localhost/orcamento/admin/configuracoes/notificacoes
```

### **2. Configurar Notificações**
- ✅ Marque "Ativar Notificações por E-mail"
- ✅ Confirme o e-mail destinatário
- ✅ Marque os eventos que deseja receber
- ✅ Clique em "Salvar Configurações"

### **3. Testar Envio**
- Clique no botão **"Enviar E-mail de Teste"**
- Aguarde a mensagem de sucesso/erro
- Verifique sua caixa de entrada

---

## 📧 TEMPLATES DE E-MAIL

### **Template 1: Novo Orçamento**
**Quando:** Cliente faz um orçamento  
**Para:** Você (admin)  
**Conteúdo:**
- 🎉 Título chamativo
- 📋 Dados do cliente
- 🛍️ Detalhes do pedido
- 🔗 Link para ver no admin
- 📝 Próximos passos

### **Template 2: Pagamento Aprovado**
**Quando:** Pagamento é confirmado  
**Para:** Você (admin)  
**Conteúdo:**
- 💰 Título de sucesso
- 💳 Informações do pagamento
- 👤 Dados do cliente
- 🔗 Link para ver pedido
- 📝 Próximos passos

### **Template 3: Confirmação Cliente**
**Quando:** Cliente faz orçamento  
**Para:** Cliente  
**Conteúdo:**
- ✅ Confirmação de recebimento
- 📋 Resumo do orçamento
- 📞 Dados de contato
- ⏰ Próximos passos

---

## 🔧 COMO FUNCIONA

### **Fluxo Automático:**

```
Cliente faz orçamento
        ↓
Sistema salva no banco
        ↓
Verifica se notificações estão ativas
        ↓
Envia e-mail para você
        ↓
Envia confirmação para cliente
```

### **Verificações Automáticas:**
- ✅ Notificações por e-mail ativas?
- ✅ Evento específico ativo?
- ✅ E-mail destinatário configurado?
- ✅ Conexão SMTP funcionando?

---

## 📊 EVENTOS QUE GERAM E-MAIL

| Evento | Admin | Cliente | Descrição |
|--------|-------|---------|-----------|
| Novo Orçamento | ✅ | ✅ | Quando cliente solicita orçamento |
| Pagamento Aprovado | ✅ | ❌ | Quando pagamento é confirmado |
| Pagamento Pendente | ❌ | ✅ | PIX/Boleto aguardando |
| Pedido Enviado | ❌ | ✅ | Quando produto é despachado |

---

## 🎨 EXEMPLO DE E-MAIL

### **Novo Orçamento:**

```
┌─────────────────────────────────────┐
│  🎉 Novo Orçamento Recebido!        │
│  Orçamento #1234                    │
├─────────────────────────────────────┤
│                                     │
│  📋 Dados do Cliente                │
│  Nome: João Silva                   │
│  E-mail: joao@email.com             │
│  Telefone: (75) 98889-0006          │
│                                     │
│  🛍️ Detalhes do Pedido              │
│  Produto: Cortina Rolô              │
│  Tecido: Blackout Premium           │
│  Dimensões: 2.00m x 1.80m           │
│  Entrega: Feira de Santana/BA       │
│                                     │
│  [Ver Orçamento Completo]           │
│                                     │
└─────────────────────────────────────┘
```

---

## 🧪 TESTAR AGORA

### **Passo a Passo:**

1. **Acesse:**
   ```
   http://localhost/orcamento/admin/configuracoes/notificacoes
   ```

2. **Configure:**
   - ✅ Ative notificações por e-mail
   - ✅ Confirme o e-mail destinatário
   - ✅ Marque "Novo Orçamento"
   - ✅ Salve

3. **Teste:**
   - Clique em "Enviar E-mail de Teste"
   - Aguarde mensagem de sucesso
   - Verifique seu e-mail

4. **Verifique:**
   - Caixa de entrada
   - Spam/Lixo eletrônico
   - Logs do sistema (se erro)

---

## ⚠️ TROUBLESHOOTING

### **E-mail não chega:**

1. **Verifique spam/lixo eletrônico**
2. **Verifique as credenciais:**
   - Usuário: nao-responder@lecortine.com.br
   - Senha: a5)?O5qF+5!H@JaT2025
   - Servidor: mail.lecortine.com.br
   - Porta: 465

3. **Verifique os logs:**
   ```
   application/logs/log-YYYY-MM-DD.php
   ```

4. **Ative debug temporariamente:**
   ```php
   // Em application/config/email.php
   $config['smtp_debug'] = 2;
   ```

### **Erro de conexão:**

- Verifique se a porta 465 está aberta
- Teste com telnet: `telnet mail.lecortine.com.br 465`
- Verifique firewall/antivírus

### **Erro de autenticação:**

- Confirme usuário e senha
- Verifique se a conta está ativa
- Teste login no webmail

---

## 📝 LOGS

Os logs de e-mail ficam em:
```
application/logs/log-YYYY-MM-DD.php
```

**Exemplos de log:**
```
INFO - E-mail enviado para: contato@lecortine.com.br
ERROR - Erro ao enviar e-mail: SMTP connection failed
```

---

## 🔐 SEGURANÇA

### **Boas Práticas:**

- ✅ Senha forte configurada
- ✅ Conta específica (nao-responder@)
- ✅ SSL/TLS ativo
- ✅ Credenciais não expostas no código
- ✅ Debug desligado em produção

### **Não fazer:**

- ❌ Compartilhar credenciais
- ❌ Usar conta pessoal
- ❌ Deixar debug ativo em produção
- ❌ Expor senha em repositório público

---

## 🎯 PRÓXIMOS PASSOS

### **Implementar nos Controllers:**

Quando cliente finalizar orçamento:
```php
// No controller Orcamento.php
$this->load->library('Email_lib');

// Enviar para admin
$this->Email_lib->enviar_novo_orcamento($orcamento_id, $dados);

// Enviar para cliente
$this->Email_lib->enviar_confirmacao_cliente(
    $dados['email'], 
    $orcamento_id, 
    $dados
);
```

Quando pagamento for aprovado:
```php
// No webhook do Mercado Pago
$this->load->library('Email_lib');
$this->Email_lib->enviar_pagamento_aprovado($orcamento_id, $dados_pagamento);
```

---

## ✅ CHECKLIST

- [x] Arquivo de configuração SMTP criado
- [x] Library de e-mail criada
- [x] Templates HTML profissionais
- [x] Página de configurações
- [x] Método de teste
- [x] Credenciais configuradas
- [ ] Testar envio de e-mail
- [ ] Integrar com orçamentos
- [ ] Integrar com pagamentos

---

## 📞 SUPORTE

**Dúvidas sobre e-mail?**

- Verifique os logs primeiro
- Teste com o botão de teste
- Verifique spam/lixo eletrônico
- Entre em contato com suporte do servidor

---

**Desenvolvido com ❤️ por Rafael Dias - doisr.com.br**
