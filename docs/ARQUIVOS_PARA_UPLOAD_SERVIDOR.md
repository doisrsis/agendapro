# 📦 ARQUIVOS PARA UPLOAD NO SERVIDOR

**Autor:** Rafael Dias - doisr.com.br
**Data:** 24/01/2026 11:50

---

## 🎯 OBJETIVO

Subir biblioteca **piggly/php-pix** e arquivos corrigidos para o servidor de produção.

---

## 📂 ESTRUTURA DE PASTAS A CRIAR NO SERVIDOR

```
/home/dois8950/iafila.doisr.com.br/
├── vendor/
│   └── piggly/
│       └── php-pix/
│           ├── src/
│           │   ├── Exceptions/
│           │   ├── Utils/
│           │   ├── Emv/
│           │   ├── AbstractPayload.php
│           │   ├── StaticPayload.php
│           │   └── Parser.php
│           └── ...
```

---

## 📋 LISTA DE ARQUIVOS PARA UPLOAD

### 1️⃣ BIBLIOTECA PIGGLY/PHP-PIX (PASTA COMPLETA)

**Origem Local:**
```
c:\xampp\htdocs\agendapro\vendor\piggly\php-pix\
```

**Destino Servidor:**
```
/home/dois8950/iafila.doisr.com.br/vendor/piggly/php-pix/
```

**⚠️ IMPORTANTE:** Subir **TODA A PASTA** `php-pix` com todos os subdiretórios e arquivos.

**Arquivos principais que DEVEM estar presentes:**
- ✅ `src/Exceptions/InvalidPixKeyException.php`
- ✅ `src/Exceptions/InvalidPixKeyTypeException.php`
- ✅ `src/Exceptions/InvalidEmvFieldException.php`
- ✅ `src/Exceptions/EmvIdIsRequiredException.php`
- ✅ `src/Exceptions/CannotParseKeyTypeException.php`
- ✅ `src/Utils/Helper.php`
- ✅ `src/Utils/Cast.php`
- ✅ `src/Parser.php`
- ✅ `src/Emv/AbstractField.php`
- ✅ `src/Emv/Field.php`
- ✅ `src/Emv/MultiField.php`
- ✅ `src/Emv/MPM.php`
- ✅ `src/AbstractPayload.php`
- ✅ `src/StaticPayload.php`

---

### 2️⃣ ARQUIVO PIX_LIB.PHP (ATUALIZADO)

**Origem Local:**
```
c:\xampp\htdocs\agendapro\application\libraries\Pix_lib.php
```

**Destino Servidor:**
```
/home/dois8950/iafila.doisr.com.br/application/libraries/Pix_lib.php
```

**⚠️ ATENÇÃO:** Este arquivo já existe no servidor. **SUBSTITUIR** pelo novo.

---

### 3️⃣ ARQUIVO WEBHOOK_WAHA.PHP (ATUALIZADO)

**Origem Local:**
```
c:\xampp\htdocs\agendapro\application\controllers\Webhook_waha.php
```

**Destino Servidor:**
```
/home/dois8950/iafila.doisr.com.br/application/controllers/Webhook_waha.php
```

**⚠️ ATENÇÃO:** Este arquivo já existe no servidor. **SUBSTITUIR** pelo novo.

**Mudanças:**
- ✅ Mensagens reorganizadas (1: detalhes, 2: código PIX)
- ✅ QR Code removido

---

### 4️⃣ ARQUIVO CONFIGURACOES.PHP (ATUALIZADO)

**Origem Local:**
```
c:\xampp\htdocs\agendapro\application\controllers\painel\Configuracoes.php
```

**Destino Servidor:**
```
/home/dois8950/iafila.doisr.com.br/application/controllers/painel/Configuracoes.php
```

**⚠️ ATENÇÃO:** Este arquivo já existe no servidor. **SUBSTITUIR** pelo novo.

**Mudanças:**
- ✅ UUID agora salvo COM TRAÇOS (biblioteca piggly requer)

---

## 🚀 PASSO A PASSO PARA UPLOAD

### Opção 1: Via FTP/SFTP (Recomendado)

1. Conectar via FileZilla ou WinSCP
2. Navegar até `/home/dois8950/iafila.doisr.com.br/`
3. Criar pasta `vendor/piggly/` se não existir
4. Subir pasta completa `php-pix` para `vendor/piggly/`
5. Substituir arquivos em `application/libraries/Pix_lib.php`
6. Substituir arquivos em `application/controllers/Webhook_waha.php`
7. Substituir arquivos em `application/controllers/painel/Configuracoes.php`

### Opção 2: Via cPanel File Manager

1. Acessar cPanel
2. Abrir File Manager
3. Navegar até `/home/dois8950/iafila.doisr.com.br/`
4. Criar pasta `vendor/piggly/` se não existir
5. Fazer upload da pasta `php-pix` compactada (.zip)
6. Extrair no servidor
7. Substituir os 3 arquivos PHP mencionados

### Opção 3: Via Git (Se configurado)

```bash
git pull origin main
```

---

## ✅ VERIFICAÇÃO PÓS-UPLOAD

Após subir os arquivos, verificar se existem no servidor:

```bash
# Verificar biblioteca piggly
ls -la /home/dois8950/iafila.doisr.com.br/vendor/piggly/php-pix/src/

# Verificar Pix_lib.php
ls -la /home/dois8950/iafila.doisr.com.br/application/libraries/Pix_lib.php

# Verificar Webhook_waha.php
ls -la /home/dois8950/iafila.doisr.com.br/application/controllers/Webhook_waha.php

# Verificar Configuracoes.php
ls -la /home/dois8950/iafila.doisr.com.br/application/controllers/painel/Configuracoes.php
```

---

## 🧪 TESTE APÓS UPLOAD

1. Acessar painel de configurações
2. Verificar se chave PIX está salva corretamente
3. Fazer novo agendamento via bot WhatsApp
4. Escolher "Pagar via PIX"
5. Verificar se recebe:
   - ✅ Mensagem 1: Detalhes completos
   - ✅ Mensagem 2: Código PIX para copiar
6. Testar código no app bancário

---

## 📊 TAMANHO DOS ARQUIVOS

- **vendor/piggly/php-pix/**: ~500 KB (pasta completa)
- **Pix_lib.php**: ~10 KB
- **Webhook_waha.php**: ~100 KB
- **Configuracoes.php**: ~30 KB

**Total aproximado:** ~640 KB

---

## ⚠️ PROBLEMAS COMUNS

### Erro: "Failed to open stream"
**Causa:** Biblioteca piggly não foi enviada ou está em pasta errada
**Solução:** Verificar se pasta está em `/home/dois8950/iafila.doisr.com.br/vendor/piggly/php-pix/`

### Erro: "Permission denied"
**Causa:** Permissões incorretas
**Solução:** Ajustar permissões (755 para pastas, 644 para arquivos)

### Erro: "Class not found"
**Causa:** Arquivos da biblioteca incompletos
**Solução:** Re-enviar pasta completa `php-pix`

---

## 📞 SUPORTE

Se houver problemas após upload:
1. Verificar logs em `/home/dois8950/iafila.doisr.com.br/application/logs/`
2. Verificar se todos os arquivos foram enviados
3. Verificar permissões dos arquivos
4. Limpar cache do PHP (OPcache)
