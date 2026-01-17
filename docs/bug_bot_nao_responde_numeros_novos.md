# 🐛 BUG CORRIGIDO: Bot Não Responde para Números Novos (@lid)

**Autor:** Rafael Dias - doisr.com.br
**Data:** 16/01/2026
**Status:** ✅ CORRIGIDO

---

## 🐛 DESCRIÇÃO DO PROBLEMA

Bot não respondia para números novos (não cadastrados no banco), mas funcionava normalmente para clientes já cadastrados. Os novos números não eram criados como clientes no banco de dados.

---

## 📊 SITUAÇÃO OBSERVADA

### **Números Novos (108259113467972 - Railda Oliveira):**

```
22:13:40 → Webhook recebe mensagem "Oi"
22:13:40 → Bot processa: "pushName armazenado"
22:13:40 → Bot tenta enviar resposta
22:14:10 → ❌ ERROR: Operation timed out after 30 seconds
22:14:40 → ❌ ERROR: Operation timed out after 30 seconds
22:14:47 → ❌ ERROR: Operation timed out after 30 seconds
```

### **Números Cadastrados (557588890006 - Rafael):**

```
✅ Recebe mensagens
✅ Bot responde normalmente
✅ Sem timeout
```

---

## 🔍 CAUSA RAIZ

### **Novos Números do WhatsApp Usam Formato @lid**

O WhatsApp mudou o formato de identificação de números:

**Formato Antigo (funciona):**
```
from: 557588890006@c.us
```

**Formato Novo (não funcionava):**
```
from: 108259113467972@lid
sender: 557599935560@s.whatsapp.net
```

### **Problema no Código:**

#### 1. **Webhook_waha.php (linha 320)**

```php
// ❌ ANTES: Remove apenas @c.us, não trata @lid
$numero = preg_replace('/[^0-9]/', '', str_replace('@c.us', '', $from));

// Resultado:
// from = "108259113467972@lid"
// numero = "108259113467972" (perdeu o @lid)
```

#### 2. **Waha_lib.php (linha 540-557)**

```php
// ❌ ANTES: Só reconhecia @c.us
public function formatar_chat_id($numero) {
    if (strpos($numero, '@c.us') !== false) {
        return $numero;
    }
    // ...
    return $numero . '@c.us'; // Sempre adiciona @c.us
}

// Resultado:
// Bot recebe: "108259113467972"
// Bot formata: "108259113467972@c.us" ❌ (deveria ser @lid)
// API WAHA: Timeout (número não existe com @c.us)
```

---

## ✅ CORREÇÕES APLICADAS

### **1. Webhook_waha.php (linha 319-324)**

```php
// ✅ DEPOIS: Preserva formato original (@lid ou @c.us)
// Extrair número (preservar formato @lid ou @c.us para compatibilidade)
// Números novos do WhatsApp usam @lid, números antigos usam @c.us
$numero_completo = $from; // Preservar formato original
$numero = preg_replace('/[^0-9]/', '', str_replace(['@c.us', '@lid', '@s.whatsapp.net'], '', $from));

log_message('info', "WAHA Mensagem de {$numero}" . ($pushName ? " ({$pushName})" : "") . ": " . substr($body, 0, 100));
```

**Mudanças:**
- ✅ Criada variável `$numero_completo` que preserva `@lid` ou `@c.us`
- ✅ Remove `@lid` e `@s.whatsapp.net` além de `@c.us` para extrair apenas dígitos
- ✅ Mantém `$numero` para logs (apenas dígitos)

### **2. Webhook_waha.php (linha 351-356)**

```php
// ✅ DEPOIS: Passa número completo para o bot
if ($estabelecimento && $estabelecimento->waha_bot_ativo) {
    // Usar número completo (com @lid ou @c.us) para compatibilidade com novos números WhatsApp
    $this->processar_bot_agendamento($estabelecimento, $numero_completo, $body, $message_id, $pushName);
}
```

**Mudanças:**
- ✅ Bot recebe `$numero_completo` ao invés de `$numero`
- ✅ Formato `@lid` é preservado até a API WAHA

### **3. Waha_lib.php (linha 540-562)**

```php
// ✅ DEPOIS: Reconhece e preserva @lid
public function formatar_chat_id($numero) {
    // Se já tem @c.us ou @lid, retornar como está
    if (strpos($numero, '@c.us') !== false || strpos($numero, '@lid') !== false) {
        return $numero;
    }

    // Se já tem @s.whatsapp.net, converter para @c.us
    if (strpos($numero, '@s.whatsapp.net') !== false) {
        return str_replace('@s.whatsapp.net', '@c.us', $numero);
    }

    // Remover tudo que não for número
    $numero = preg_replace('/[^0-9]/', '', $numero);

    // Adicionar código do país se não tiver (números BR tem 10-11 dígitos)
    if (strlen($numero) <= 11) {
        $numero = '55' . $numero;
    }

    // Log para debug
    log_message('debug', 'WAHA formatar_chat_id: ' . $numero . '@c.us');

    return $numero . '@c.us';
}
```

**Mudanças:**
- ✅ Detecta e preserva formato `@lid`
- ✅ Detecta e preserva formato `@c.us`
- ✅ Converte `@s.whatsapp.net` para `@c.us`
- ✅ Apenas adiciona `@c.us` se não tiver nenhum formato

---

## 🎯 RESULTADO ESPERADO AGORA

### **Fluxo Corrigido:**

```
22:13:40 → Webhook recebe mensagem "Oi"
           from: "108259113467972@lid"

22:13:40 → Webhook processa:
           numero_completo = "108259113467972@lid" ✅
           numero = "108259113467972" (para logs)

22:13:40 → Bot recebe:
           numero = "108259113467972@lid" ✅

22:13:40 → Bot formata para WAHA:
           formatar_chat_id("108259113467972@lid")
           return "108259113467972@lid" ✅ (preservado)

22:13:40 → WAHA API envia para:
           chatId: "108259113467972@lid" ✅

22:13:41 → ✅ Mensagem enviada com sucesso
22:13:41 → ✅ Cliente criado no banco
22:13:41 → ✅ Bot responde normalmente
```

---

## 📋 COMPATIBILIDADE

### **Formatos Suportados:**

| Formato | Descrição | Suporte |
|---------|-----------|---------|
| `557588890006@c.us` | Números antigos | ✅ Mantido |
| `108259113467972@lid` | Números novos | ✅ Adicionado |
| `557588890006@s.whatsapp.net` | Formato alternativo | ✅ Convertido para @c.us |
| `557588890006` | Apenas dígitos | ✅ Adiciona @c.us |

### **Retrocompatibilidade:**

- ✅ Números antigos continuam funcionando
- ✅ Números novos agora funcionam
- ✅ Conversas existentes não são afetadas
- ✅ Banco de dados compatível

---

## 🧪 TESTE RECOMENDADO

### **Cenário 1: Número Novo (@lid)**

1. Enviar mensagem de número novo (nunca conversou)
2. ✅ Bot deve responder
3. ✅ Cliente deve ser criado no banco
4. ✅ Conversa deve funcionar normalmente

### **Cenário 2: Número Antigo (@c.us)**

1. Enviar mensagem de número cadastrado
2. ✅ Bot deve responder normalmente
3. ✅ Não deve quebrar funcionalidade existente

### **Cenário 3: Reagendamento**

1. Número novo agenda
2. Número novo reagenda
3. ✅ Deve funcionar sem timeout
4. ✅ Campos de confirmação devem resetar

---

## 📊 ARQUIVOS MODIFICADOS

### **1. application/controllers/Webhook_waha.php**

**Linhas 319-324:**
- Preserva formato original do número
- Cria `$numero_completo` com `@lid` ou `@c.us`

**Linhas 351-356:**
- Passa `$numero_completo` para o bot
- Garante formato correto na API WAHA

### **2. application/libraries/Waha_lib.php**

**Linhas 540-562:**
- Detecta e preserva `@lid`
- Detecta e preserva `@c.us`
- Converte `@s.whatsapp.net`

---

## 🔄 FLUXO COMPLETO

### **Antes da Correção:**

```
Webhook recebe: "108259113467972@lid"
    ↓
Extrai número: "108259113467972" (perde @lid)
    ↓
Bot recebe: "108259113467972"
    ↓
Bot formata: "108259113467972@c.us" ❌
    ↓
WAHA API: Timeout (número não existe)
    ↓
❌ Bot não responde
❌ Cliente não é criado
```

### **Depois da Correção:**

```
Webhook recebe: "108259113467972@lid"
    ↓
Preserva formato: "108259113467972@lid" ✅
    ↓
Bot recebe: "108259113467972@lid" ✅
    ↓
Bot formata: "108259113467972@lid" ✅ (preservado)
    ↓
WAHA API: Sucesso
    ↓
✅ Bot responde
✅ Cliente criado
✅ Conversa funciona
```

---

## ⚠️ OBSERVAÇÕES IMPORTANTES

### **1. Formato LID**

O formato `@lid` é usado pelo WhatsApp para:
- Números novos (criados recentemente)
- Números de negócios
- Números em algumas regiões específicas

### **2. Banco de Dados**

O campo `whatsapp` na tabela `clientes` armazena:
- Apenas dígitos: `557588890006`
- Não armazena `@c.us` ou `@lid`

Isso está correto e não precisa mudar.

### **3. API WAHA**

A API WAHA aceita ambos os formatos:
- `chatId: "557588890006@c.us"` ✅
- `chatId: "108259113467972@lid"` ✅

Mas **não aceita** formato errado:
- `chatId: "108259113467972@c.us"` ❌ (se o número é @lid)

---

## 🚀 PRÓXIMOS PASSOS

1. ✅ Correções aplicadas
2. ✅ Documentação criada
3. ⏳ Testar com números novos
4. ⏳ Monitorar logs
5. ⏳ Validar criação de clientes

---

## 📝 LOGS PARA MONITORAR

### **Sucesso:**
```
INFO: WAHA Mensagem de 108259113467972 (Railda Oliveira): Oi
DEBUG: WAHA formatar_chat_id: 108259113467972@lid
INFO: Bot: pushName armazenado na conversa
INFO: WAHA: Mensagem enviada com sucesso
```

### **Erro (antes da correção):**
```
ERROR: WAHA API Error: Operation timed out after 30 seconds
```

---

**Status:** ✅ CORRIGIDO
**Testado:** ⏳ Aguardando teste em produção
**Prioridade:** 🔴 CRÍTICA
