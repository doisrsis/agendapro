# Fase 1: Melhorias de Navegação do Bot

**Autor:** Rafael Dias - doisr.com.br
**Data:** 30/12/2025
**Status:** ✅ Implementado

---

## 🎯 Objetivo

Melhorar a navegação do bot separando claramente os comandos de "voltar ao menu" e "sair da conversa", além de adicionar confirmação ao sair para evitar encerramentos acidentais.

---

## 🔧 Implementações

### **1. Separação de Comandos**

**Antes:**
- Comando `0` e `menu` faziam a mesma coisa (confuso)
- Não havia diferença clara entre "voltar" e "sair"

**Depois:**

#### **Comandos de Início** (resetam e mostram menu)
```
oi, olá, ola, hi, hello, bom dia, boa tarde, boa noite
```

#### **Comandos de Menu** (voltam ao menu sem encerrar)
```
menu, voltar, inicio, início
```

#### **Comandos de Saída** (encerram a sessão)
```
0, sair, tchau, obrigado, obrigada
```

---

### **2. Confirmação ao Sair**

**Comportamento:**

- **Se estiver no menu:** Encerra direto (sem confirmação)
- **Se estiver em outro estado:** Pede confirmação

**Fluxo:**

```
Usuário (em meio a agendamento): "0"
Bot: "Você tem certeza que deseja sair? 🤔

*1* ou *Sim* - Confirmar saída
*2* ou *Não* - Continuar conversa

Ou digite *menu* para voltar ao menu principal."
```

**Novo Estado:** `confirmando_saida`

---

### **3. Mensagens Melhoradas**

#### **Menu Principal:**
```
Olá, [Nome]! 👋

Bem-vindo(a) ao [Estabelecimento]! 💈✨

Como posso ajudar?

1️⃣ Agendar - Fazer novo agendamento
2️⃣ Meus Agendamentos - Ver agendamentos
3️⃣ Cancelar - Cancelar agendamento
0️⃣ Sair - Encerrar atendimento

💡 Dica: Digite menu ou voltar a qualquer momento para retornar aqui.
```

#### **Voltando ao Menu:**
```
Voltando ao menu principal... 🔙
```

#### **Confirmação de Saída:**
```
Você tem certeza que deseja sair? 🤔

*1* ou *Sim* - Confirmar saída
*2* ou *Não* - Continuar conversa

Ou digite *menu* para voltar ao menu principal.
```

#### **Continuando Conversa:**
```
Ok! Continuando... 😊
```

---

## 📊 Fluxos de Navegação

### **Fluxo 1: Voltar ao Menu**

```
Usuário está em: aguardando_servico
Usuário digita: "menu"
Bot: "Voltando ao menu principal... 🔙"
Bot: [Mostra menu principal]
Estado: menu
```

### **Fluxo 2: Sair do Menu**

```
Usuário está em: menu
Usuário digita: "0"
Bot: "Obrigado por entrar em contato! 😊..."
Estado: encerrada
encerrada: 1
```

### **Fluxo 3: Sair de Outro Estado (com confirmação)**

```
Usuário está em: aguardando_data
Usuário digita: "0"
Bot: "Você tem certeza que deseja sair? 🤔..."
Estado: confirmando_saida

Usuário digita: "1" (sim)
Bot: "Obrigado por entrar em contato! 😊..."
Estado: encerrada
encerrada: 1

OU

Usuário digita: "2" (não)
Bot: "Ok! Continuando... 😊"
Bot: [Mostra menu principal]
Estado: menu
```

---

## 🔍 Código Implementado

### **Arquivo:** `application/controllers/Webhook_waha.php`

#### **1. Separação de Comandos (linhas 397-441)**

```php
// Comandos globais (funcionam em qualquer estado)
$comandos_inicio = ['oi', 'olá', 'ola', 'hi', 'hello', 'bom dia', 'boa tarde', 'boa noite'];
$comandos_menu = ['menu', 'voltar', 'inicio', 'início'];
$comandos_sair = ['0', 'sair', 'tchau', 'obrigado', 'obrigada'];

// Comandos de início - resetam conversa e mostram menu
if (in_array($msg, $comandos_inicio)) {
    $this->Bot_conversa_model->resetar($conversa->id);
    $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
    return;
}

// Comandos para voltar ao menu - resetam sem encerrar
if (in_array($msg, $comandos_menu)) {
    $this->Bot_conversa_model->resetar($conversa->id);
    $this->waha_lib->enviar_texto($numero,
        "Voltando ao menu principal... 🔙\n\n"
    );
    $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
    return;
}

// Comando para sair - pede confirmação se não estiver no menu
if (in_array($msg, $comandos_sair)) {
    // Se já está no menu ou em estado encerrada, encerra direto
    if ($conversa->estado === 'menu' || $conversa->estado === 'encerrada') {
        $this->Bot_conversa_model->encerrar($conversa->id);
        $this->waha_lib->enviar_texto($numero,
            "Obrigado por entrar em contato! 😊\n\n" .
            "Até a próxima! 👋\n\n" .
            "_Digite *oi* quando precisar de mim novamente._"
        );
        return;
    }

    // Se está em outro estado, pede confirmação
    $this->Bot_conversa_model->atualizar_estado($conversa->id, 'confirmando_saida', []);
    $this->waha_lib->enviar_texto($numero,
        "Você tem certeza que deseja sair? 🤔\n\n" .
        "*1* ou *Sim* - Confirmar saída\n" .
        "*2* ou *Não* - Continuar conversa\n\n" .
        "_Ou digite *menu* para voltar ao menu principal._"
    );
    return;
}
```

#### **2. Novo Case no Switch (linha 473)**

```php
case 'confirmando_saida':
    $this->processar_estado_confirmando_saida($estabelecimento, $numero, $msg, $conversa, $cliente);
    break;
```

#### **3. Método de Processamento (linhas 721-752)**

```php
private function processar_estado_confirmando_saida($estabelecimento, $numero, $msg, $conversa, $cliente) {
    // Confirmar saída
    if (in_array($msg, ['1', 'sim', 's'])) {
        $this->Bot_conversa_model->encerrar($conversa->id);
        $this->waha_lib->enviar_texto($numero,
            "Obrigado por entrar em contato! 😊\n\n" .
            "Até a próxima! 👋\n\n" .
            "_Digite *oi* quando precisar de mim novamente._"
        );
        return;
    }

    // Continuar conversa - volta ao menu
    if (in_array($msg, ['2', 'não', 'nao', 'n'])) {
        $this->Bot_conversa_model->resetar($conversa->id);
        $this->waha_lib->enviar_texto($numero,
            "Ok! Continuando... 😊\n\n"
        );
        $this->enviar_menu_principal($estabelecimento, $numero, $cliente);
        return;
    }

    // Opção inválida
    $this->waha_lib->enviar_texto($numero,
        "Opção inválida. Por favor, escolha:\n\n" .
        "*1* ou *Sim* - Confirmar saída\n" .
        "*2* ou *Não* - Continuar conversa"
    );
}
```

---

## ✅ Benefícios

1. **Navegação Clara** - Usuário sabe exatamente o que cada comando faz
2. **Menos Erros** - Confirmação evita saídas acidentais
3. **Melhor UX** - Comandos intuitivos (`menu`, `voltar`)
4. **Flexibilidade** - Usuário pode voltar ao menu sem encerrar sessão
5. **Mensagens Informativas** - Dicas sobre comandos disponíveis

---

## 🧪 Testes

### **Teste 1: Voltar ao Menu**
```
1. Inicie agendamento: "oi" → "1"
2. Digite: "menu"
3. Resultado: Volta ao menu sem encerrar sessão
```

### **Teste 2: Sair do Menu**
```
1. Esteja no menu principal
2. Digite: "0"
3. Resultado: Encerra direto (sem confirmação)
4. Verifique banco: encerrada=1
```

### **Teste 3: Sair de Outro Estado**
```
1. Inicie agendamento: "oi" → "1"
2. Digite: "0"
3. Resultado: Pede confirmação
4. Digite: "1" (sim)
5. Resultado: Encerra sessão
6. Verifique banco: encerrada=1
```

### **Teste 4: Cancelar Saída**
```
1. Inicie agendamento: "oi" → "1"
2. Digite: "0"
3. Resultado: Pede confirmação
4. Digite: "2" (não)
5. Resultado: Volta ao menu
6. Verifique banco: encerrada=0, estado=menu
```

---

## 📝 Próximas Fases

- **Fase 2:** Implementar Reagendamento
- **Fase 3:** Melhorar Cancelamento com Sugestão de Reagendamento

---

## 🎉 Conclusão

A Fase 1 foi implementada com sucesso! O bot agora tem uma navegação muito mais intuitiva e clara, com comandos bem definidos e confirmações que evitam ações acidentais.
