# 📦 INSTALAÇÃO MANUAL: BIBLIOTECA PIGGLY/PHP-PIX

**Autor:** Rafael Dias - doisr.com.br
**Data:** 24/01/2026 11:10

---

## ⚠️ PROBLEMA

Composer não está configurado no PATH do Windows. Vou fazer instalação manual da biblioteca.

---

## 📋 PASSOS PARA INSTALAÇÃO MANUAL

### 1. Baixar biblioteca manualmente

Acesse: https://github.com/piggly-dev/php-pix/releases

Ou clone o repositório:
```bash
git clone https://github.com/piggly-dev/php-pix.git vendor/piggly/php-pix
```

### 2. Instalar dependências

A biblioteca piggly/php-pix requer:
- PHP >= 8.0
- Extensão GD (para gerar QR Code)
- Extensão MBString

---

## 🚀 SOLUÇÃO ALTERNATIVA

Vou criar uma implementação wrapper usando a biblioteca piggly/php-pix baixada manualmente e integrada ao projeto.

**Arquivos a criar:**
1. Baixar biblioteca piggly/php-pix
2. Criar novo `Pix_lib.php` que usa a biblioteca
3. Testar código PIX gerado

---

## 📝 PRÓXIMOS PASSOS

Vou criar a nova implementação do `Pix_lib.php` usando a biblioteca piggly/php-pix de forma manual, sem depender do Composer.
