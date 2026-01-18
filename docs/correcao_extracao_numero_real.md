# Correção: Extração do numero_real do Webhook WAHA
**Autor:** Rafael Dias - doisr.com.br
**Data:** 18/01/2026

---

## 🐛 PROBLEMA IDENTIFICADO

### **Sintoma:**
Cliente Mary Oliveira (ID 16) foi criado com telefone incorreto:
- WhatsApp salvo: `557597058104@c.us` ✅ (correto)
- Telefone salvo: `53884844269782` ❌ (incorreto - era um @lid do SenderAlt)
- Telefone esperado: `557597058104` (do campo from)

### **Causa Raiz:**
A lógica de extração do `numero_real` assumia que o campo `SenderAlt` **sempre** contém o telefone real, mas isso é verdade **apenas para números @lid**.

---

## 🔍 ANÁLISE DO WEBHOOK

### **Estrutura do Payload WAHA:**

**Para números @c.us (antigos/tradicionais):**
```json
{
  "from": "557597058104@c.us",           // ← Telefone real aqui
  "_data": {
    "Info": {
      "SenderAlt": "53884844269782@lid", // ← @lid do contato (não é telefone)
      "PushName": "Mary Oliveira"
    }
  }
}
```

**Para números @lid (novos):**
```json
{
  "from": "108259113467972@lid",                    // ← ID interno
  "_data": {
    "Info": {
      "SenderAlt": "557599935560@s.whatsapp.net",  // ← Telefone real aqui
      "PushName": "Railda Oliveira"
    }
  }
}
```

---

## ❌ LÓGICA INCORRETA (ANTES)

```php
// Extrair número real do telefone
// Para números @lid, o número real está em SenderAlt
$numero_real = null;
if (isset($payload['_data']['Info']['SenderAlt']) && !empty($payload['_data']['Info']['SenderAlt'])) {
    $numero_real = preg_replace('/[^0-9]/', '', $payload['_data']['Info']['SenderAlt']);
}
```

**Problema:** Sempre extraía do `SenderAlt`, independente do tipo de número.

**Resultado:**
- ✅ Números @lid: funcionava (SenderAlt tem telefone real)
- ❌ Números @c.us: pegava @lid do contato ao invés do telefone

---

## ✅ LÓGICA CORRETA (DEPOIS)

```php
// Extrair número real do telefone
// Para números @lid, o número real está em SenderAlt
// Para números @c.us, o número real está no próprio from
$numero_real = null;
if (strpos($from, '@lid') !== false) {
    // Número @lid: telefone real está em SenderAlt
    if (isset($payload['_data']['Info']['SenderAlt']) && !empty($payload['_data']['Info']['SenderAlt'])) {
        $numero_real = preg_replace('/[^0-9]/', '', $payload['_data']['Info']['SenderAlt']);
    }
} else if (strpos($from, '@c.us') !== false) {
    // Número @c.us: telefone real está no próprio from
    $numero_real = preg_replace('/[^0-9]/', '', $from);
}
```

**Regra:**
1. Se `from` contém `@lid` → extrair telefone do `SenderAlt`
2. Se `from` contém `@c.us` → extrair telefone do próprio `from`

---

## 📊 COMPARAÇÃO DE RESULTADOS

### **Cliente Railda (ID 15) - @lid:**

| Campo | Antes | Depois |
|-------|-------|--------|
| from | `108259113467972@lid` | `108259113467972@lid` |
| SenderAlt | `557599935560@s.whatsapp.net` | `557599935560@s.whatsapp.net` |
| **numero_real extraído** | `557599935560` ✅ | `557599935560` ✅ |
| whatsapp salvo | `108259113467972@lid` | `108259113467972@lid` |
| telefone salvo | `NULL` ❌ (bug anterior) | `557599935560` ✅ |

### **Cliente Mary (ID 16) - @c.us:**

| Campo | Antes | Depois |
|-------|-------|--------|
| from | `557597058104@c.us` | `557597058104@c.us` |
| SenderAlt | `53884844269782@lid` | `53884844269782@lid` |
| **numero_real extraído** | `53884844269782` ❌ | `557597058104` ✅ |
| whatsapp salvo | `557597058104@c.us` | `557597058104@c.us` |
| telefone salvo | `53884844269782` ❌ | `557597058104` ✅ |

---

## 🎯 FLUXO CORRETO APÓS CORREÇÃO

### **Cenário 1: Cliente com @lid**
```
1. Webhook recebe:
   from: 108259113467972@lid
   SenderAlt: 557599935560@s.whatsapp.net

2. Sistema detecta: from contém @lid
3. Extrai numero_real de: SenderAlt
4. Resultado: numero_real = 557599935560 ✅

5. Cliente criado:
   whatsapp: 108259113467972@lid
   telefone: 557599935560 ✅
```

### **Cenário 2: Cliente com @c.us**
```
1. Webhook recebe:
   from: 557597058104@c.us
   SenderAlt: 53884844269782@lid (ID do contato)

2. Sistema detecta: from contém @c.us
3. Extrai numero_real de: from
4. Resultado: numero_real = 557597058104 ✅

5. Cliente criado:
   whatsapp: 557597058104@c.us
   telefone: 557597058104 ✅
```

---

## 🔧 ARQUIVO MODIFICADO

**`application/controllers/Webhook_waha.php`** - Linhas 319-331

---

## 🔄 CORREÇÃO DE DADOS EXISTENTES

### **Cliente Mary Oliveira (ID 16):**

Execute o script SQL:
```sql
UPDATE `clientes`
SET `telefone` = '557597058104'
WHERE `id` = 16
  AND `whatsapp` = '557597058104@c.us'
  AND `telefone` = '53884844269782';
```

Ou execute o arquivo:
```bash
SOURCE docs/corrigir_telefone_mary_id16.sql;
```

---

## 🧪 TESTES NECESSÁRIOS

### **Teste 1: Novo Cliente @lid**
```
✅ Cliente envia primeira mensagem (número @lid)
✅ Sistema extrai numero_real do SenderAlt
✅ Cliente criado com telefone correto
✅ View exibe telefone formatado + botões WhatsApp
```

### **Teste 2: Novo Cliente @c.us**
```
✅ Cliente envia primeira mensagem (número @c.us)
✅ Sistema extrai numero_real do from
✅ Cliente criado com telefone correto
✅ View exibe telefone formatado + botões WhatsApp
```

---

## 📝 ENTENDENDO OS FORMATOS

### **O que é SenderAlt?**

O campo `SenderAlt` tem significados diferentes dependendo do tipo de número:

**Para @lid:**
- `SenderAlt` = telefone real do usuário
- Formato: `557599935560@s.whatsapp.net`
- Usado porque o `from` é apenas um ID interno

**Para @c.us:**
- `SenderAlt` = @lid do contato na agenda
- Formato: `53884844269782@lid`
- É um identificador interno do WhatsApp, **não é telefone**
- Não deve ser usado como telefone do cliente

---

## ✅ CONCLUSÃO

A correção garante que o `numero_real` seja extraído do campo correto dependendo do tipo de número WhatsApp:
- **@lid** → extrair de `SenderAlt` (telefone real)
- **@c.us** → extrair de `from` (já é o telefone real)

Isso resolve o problema de clientes com números `@c.us` terem telefones incorretos salvos no banco de dados.

**Status:** ✅ Implementado e pronto para teste
**Prioridade:** 🔴 Crítica (afeta cadastro de clientes @c.us)
**Complexidade:** 🟢 Baixa (1 condicional adicionada)

---

## 📚 DOCUMENTOS RELACIONADOS

- `docs/correcao_numero_real_bot_conversa.md` - Preservação do numero_real
- `docs/correcao_formato_numero_whatsapp_bot.md` - Formato de números no bot
- `docs/melhoria_campo_telefone_clientes.md` - Campo telefone separado
