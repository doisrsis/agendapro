# 🚀 GUIA RÁPIDO DE INSTALAÇÃO

**Projeto Base - Dashboard Administrativo**
**Autor:** Rafael Dias - doisr.com.br

---

## ⚡ Instalação Rápida (5 minutos)

### 1️⃣ Banco de Dados

1. Abra o **phpMyAdmin**
2. Crie um novo banco de dados chamado `projeto_base`
3. Selecione o banco criado
4. Clique em **Importar**
5. Escolha o arquivo: `docs/projeto_base_database.sql`
6. Clique em **Executar**

✅ **Pronto!** O banco foi criado com todas as tabelas e dados iniciais.

---

### 2️⃣ Configuração do Banco

Edite o arquivo: `application/config/database.php`

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',           // Seu usuário MySQL
    'password' => '',               // Sua senha MySQL
    'database' => 'projeto_base',   // Nome do banco criado
    // ... resto das configurações
);
```

---

### 3️⃣ Configuração da URL Base

Edite o arquivo: `application/config/config.php`

Localize a linha `$config['base_url']` e configure:

```php
// Se estiver na raiz do localhost
$config['base_url'] = 'http://localhost/projeto_base/';

// Se estiver em uma subpasta
$config['base_url'] = 'http://localhost/minha_pasta/projeto_base/';

// Em produção
$config['base_url'] = 'https://meusite.com.br/';
```

---

### 4️⃣ Chave de Criptografia

No mesmo arquivo `config.php`, localize e altere:

```php
$config['encryption_key'] = 'SuaChaveSecretaAqui123456789';
```

💡 **Dica:** Use uma string aleatória de pelo menos 32 caracteres.

---

### 5️⃣ Primeiro Acesso

1. Abra o navegador
2. Acesse: `http://localhost/projeto_base/`
3. Faça login com:
   - **E-mail:** admin@sistema.com.br
   - **Senha:** admin123

🔒 **IMPORTANTE:** Altere a senha padrão imediatamente!

---

## ⚙️ Configurações Pós-Instalação

### Configurar SMTP (Envio de E-mails)

1. No painel, vá em: **Configurações > SMTP**
2. Preencha os dados do seu servidor de e-mail
3. Ative o SMTP
4. Clique em **"Testar E-mail"**

**Exemplo Gmail:**
- Host: `smtp.gmail.com`
- Porta: `587`
- Usuário: `seuemail@gmail.com`
- Senha: `sua_senha_ou_app_password`
- Segurança: `TLS`

---

### Personalizar Sistema

1. Vá em: **Configurações > Geral**
2. Altere:
   - Nome do sistema
   - E-mail de contato
   - Telefone
   - Logo (futuro)

---

## 🎯 Próximos Passos

Agora você pode começar a desenvolver suas funcionalidades!

### Adicionar Novo Módulo:

1. **Criar tabela** no banco de dados
2. **Criar Model** em `application/models/`
3. **Criar Controller** em `application/controllers/admin/`
4. **Criar Views** em `application/views/admin/`
5. **Adicionar menu** no layout

---

## 🆘 Problemas Comuns

### Erro 404 - Página não encontrada

**Solução:** Verifique se o `.htaccess` está na raiz do projeto e se o `mod_rewrite` está ativado no Apache.

### Erro de conexão com banco de dados

**Solução:** Verifique as credenciais em `database.php` e se o MySQL está rodando.

### Página em branco

**Solução:**
1. Ative o debug em `config.php`: `$config['log_threshold'] = 4;`
2. Verifique os logs em `application/logs/`

### E-mails não estão sendo enviados

**Solução:**
1. Verifique se o SMTP está ativado nas configurações
2. Teste as credenciais SMTP
3. Verifique se a porta está aberta no firewall
4. Para Gmail, use "Senha de app" ao invés da senha normal

---

## 📚 Documentação Completa

Para mais detalhes, consulte: `docs/README_PROJETO_BASE.md`

---

## 💡 Dicas de Segurança

- ✅ Altere a senha padrão do admin
- ✅ Altere a `encryption_key`
- ✅ Use HTTPS em produção
- ✅ Mantenha backups regulares
- ✅ Atualize PHP e MySQL regularmente

---

**Pronto! Seu projeto base está instalado e funcionando! 🎉**

*Bom desenvolvimento!*

---

**Suporte:** contato@doisr.com.br
**Website:** [doisr.com.br](https://doisr.com.br)
