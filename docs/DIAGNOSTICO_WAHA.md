# Diagnóstico e Solução - Problema WAHA

**Autor:** Rafael Dias - doisr.com.br
**Data:** 29/12/2025

## 🔴 Problema Identificado

**Erro:** 502 Bad Gateway ao tentar reiniciar sessão WAHA
**Sessão:** est_4_modelo_barber
**Servidor:** zaptotal.doisrsistemas.com.br (via Cloudflare)

---

## 🔍 Causa do Problema

O erro **502 Bad Gateway** indica que:

1. **Servidor WAHA está offline/inacessível**
   - O serviço Docker do WAHA pode ter parado
   - O servidor onde o WAHA está hospedado pode estar fora do ar
   - Problema de rede/firewall bloqueando acesso

2. **Cloudflare não consegue alcançar o servidor origin**
   - O proxy reverso (Cloudflare) não consegue se conectar ao servidor WAHA

---

## ✅ Soluções

### Solução 1: Verificar Status do Servidor WAHA

**Acesse o servidor onde o WAHA está instalado e execute:**

```bash
# Verificar se o container WAHA está rodando
docker ps | grep waha

# Se não estiver rodando, iniciar
docker start waha

# Ou reiniciar
docker restart waha

# Verificar logs
docker logs waha --tail 100
```

---

### Solução 2: Verificar Configurações no Banco

**Execute no phpMyAdmin:**

```sql
-- Verificar URL da API WAHA
SELECT chave, valor
FROM configuracoes
WHERE grupo = 'waha';
```

**URLs possíveis:**
- `https://zaptotal.doisrsistemas.com.br` (atual - com problema)
- `http://localhost:3000` (se WAHA estiver local)
- `https://outro-servidor.com.br` (servidor alternativo)

---

### Solução 3: Atualizar URL da API WAHA (Temporário)

Se você tiver outro servidor WAHA funcionando:

```sql
UPDATE configuracoes
SET valor = 'https://novo-servidor-waha.com.br'
WHERE chave = 'waha_api_url';
```

---

### Solução 4: Resetar Sessão do Estabelecimento

Enquanto o servidor WAHA não volta:

```sql
-- Marcar como desconectado para evitar erros
UPDATE estabelecimentos
SET
    waha_status = 'desconectado',
    waha_numero_conectado = NULL
WHERE id = 4;
```

Quando o WAHA voltar:
1. Acesse: `painel/configuracoes?aba=whatsapp`
2. Clique em "Conectar WhatsApp"
3. Escaneie o QR Code novamente

---

## 🔧 Verificação de Conectividade

### Teste 1: Ping no Servidor

```bash
ping zaptotal.doisrsistemas.com.br
```

### Teste 2: Curl na API

```bash
curl -I https://zaptotal.doisrsistemas.com.br/api/sessions
```

**Resposta esperada:** 200 OK ou 401 Unauthorized
**Resposta com problema:** 502 Bad Gateway

### Teste 3: Via Browser

Acesse: `https://zaptotal.doisrsistemas.com.br/api/sessions`

---

## 📋 Checklist de Resolução

- [ ] Verificar se servidor WAHA está online
- [ ] Verificar se container Docker está rodando
- [ ] Verificar logs do WAHA para erros
- [ ] Testar conectividade com curl/browser
- [ ] Se necessário, reiniciar container WAHA
- [ ] Atualizar status no banco de dados
- [ ] Reconectar WhatsApp via painel

---

## 🚨 Ação Imediata Recomendada

**Enquanto o servidor WAHA não é corrigido:**

1. **Desabilitar bot temporariamente:**
```sql
UPDATE estabelecimentos
SET waha_bot_ativo = 0
WHERE id = 4;
```

2. **Marcar como desconectado:**
```sql
UPDATE estabelecimentos
SET waha_status = 'desconectado'
WHERE id = 4;
```

3. **Após WAHA voltar:**
   - Reabilitar bot: `waha_bot_ativo = 1`
   - Reconectar via painel

---

## 📞 Contato com Suporte

Se o problema persistir, entre em contato com:
- **Suporte do servidor WAHA**
- **Administrador do servidor zaptotal.doisrsistemas.com.br**
- **Suporte Cloudflare** (se o problema for no proxy)

---

## 🔄 Logs para Análise

Após resolver, verificar logs em:
- `application/logs/log-YYYY-MM-DD.php`
- Buscar por: "WAHA", "502", "Bad Gateway"
