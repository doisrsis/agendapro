# Guia de Deploy - cPanel

## 📋 Pré-requisitos

- Acesso ao cPanel
- Subdomínio configurado: `https://iafila.doisr.com.br`
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Certificado SSL ativo

---

## 🚀 Passo a Passo do Deploy

### 1. Preparar Arquivos Localmente

#### 1.1. Exportar Banco de Dados

```bash
# Via phpMyAdmin local:
# 1. Selecione o banco 'cecriativocom_lecortine_orc'
# 2. Clique em "Exportar"
# 3. Método: Rápido
# 4. Formato: SQL
# 5. Clique em "Executar"
```

#### 1.2. Criar arquivo .zip do projeto

**Opção 1 - Manual:**
1. Selecione todos os arquivos da pasta `c:\xampp\htdocs\agendapro`
2. Clique com botão direito > Enviar para > Pasta compactada
3. Renomeie para `agendapro.zip`

**Opção 2 - Via Git:**
```bash
cd c:\xampp\htdocs\agendapro
git archive -o agendapro.zip HEAD
```

---

### 2. Upload para cPanel

#### 2.1. Acessar Gerenciador de Arquivos

1. Faça login no cPanel
2. Vá em **Gerenciador de Arquivos**
3. Navegue até a pasta do subdomínio: `/public_html/iafila` ou `/home/usuario/iafila.doisr.com.br`

#### 2.2. Fazer Upload

1. Clique em **Upload**
2. Selecione `agendapro.zip`
3. Aguarde o upload completar
4. Volte ao Gerenciador de Arquivos
5. Clique com botão direito no arquivo > **Extrair**
6. Extraia para a pasta atual

---

### 3. Configurar Banco de Dados

#### 3.1. Criar Banco de Dados

1. No cPanel, vá em **MySQL® Databases**
2. Em "Create New Database":
   - Nome: `agendapro` (será criado como `usuario_agendapro`)
   - Clique em **Create Database**

#### 3.2. Criar Usuário

1. Em "Add New User":
   - Username: `agendapro_user`
   - Password: (gere uma senha forte)
   - Clique em **Create User**

#### 3.3. Vincular Usuário ao Banco

1. Em "Add User To Database":
   - User: `agendapro_user`
   - Database: `agendapro`
   - Clique em **Add**
2. Marque **ALL PRIVILEGES**
3. Clique em **Make Changes**

#### 3.4. Importar Banco de Dados

1. No cPanel, vá em **phpMyAdmin**
2. Selecione o banco `usuario_agendapro`
3. Clique em **Importar**
4. Escolha o arquivo SQL exportado
5. Clique em **Executar**

---

### 4. Configurar Aplicação

#### 4.1. Configurar Banco de Dados

Edite o arquivo: `application/config/database.php`

```php
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'usuario_agendapro_user', // Seu usuário completo
    'password' => 'SUA_SENHA_AQUI',
    'database' => 'usuario_agendapro', // Seu banco completo
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => FALSE, // IMPORTANTE: FALSE em produção
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
```

#### 4.2. Configurar URL Base

Edite o arquivo: `application/config/config.php`

```php
$config['base_url'] = 'https://iafila.doisr.com.br/';
```

#### 4.3. Configurar Ambiente

No mesmo arquivo `config.php`, procure por:

```php
// Linha ~310
define('ENVIRONMENT', 'production'); // Mudar de 'development' para 'production'
```

Ou edite o arquivo `index.php` na raiz:

```php
// Linha ~53
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
```

---

### 5. Configurar .htaccess

Verifique se o arquivo `.htaccess` na raiz está correto:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Redirecionar para HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Remover index.php da URL
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>

# Bloquear acesso a arquivos sensíveis
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Bloquear acesso a diretórios
Options -Indexes
```

---

### 6. Configurar Permissões

Via Gerenciador de Arquivos ou SSH:

```bash
# Permissões para pastas de upload
chmod 755 uploads
chmod 755 uploads/logos
chmod 755 uploads/profissionais
chmod 755 uploads/clientes

# Permissões para cache (se houver)
chmod 755 application/cache
chmod 755 application/logs
```

---

### 7. Testar Aplicação

1. Acesse: `https://iafila.doisr.com.br`
2. Faça login com as credenciais do banco local
3. Teste as funcionalidades principais

---

### 8. Configurar Webhook do Mercado Pago

Agora que está no ar, configure o webhook:

1. Acesse: [Webhooks Mercado Pago](https://www.mercadopago.com.br/developers/panel/webhooks)
2. URL: `https://iafila.doisr.com.br/webhook/mercadopago`
3. Eventos: Marque `payment`
4. Salve

---

## 🔧 Configurações Adicionais

### PHP.ini (se necessário)

Se tiver acesso ao php.ini ou via cPanel > Select PHP Version:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

### Cron Jobs (Opcional)

Para tarefas agendadas, configure em cPanel > Cron Jobs:

```bash
# Exemplo: Limpar logs antigos diariamente às 3h
0 3 * * * /usr/bin/php /home/usuario/iafila.doisr.com.br/index.php cron limpar_logs
```

---

## 🐛 Troubleshooting

### Erro 500 - Internal Server Error

1. Verifique permissões dos arquivos (644) e pastas (755)
2. Verifique se o `.htaccess` está correto
3. Ative logs de erro no `config.php`:
   ```php
   $config['log_threshold'] = 4; // Todos os logs
   ```
4. Verifique logs em: `application/logs/`

### Página em branco

1. Ative display_errors temporariamente no `index.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Verifique conexão com banco de dados
3. Verifique se o PHP está na versão correta

### CSS/JS não carregam

1. Verifique se a `base_url` está correta
2. Verifique permissões da pasta `assets`
3. Limpe cache do navegador

### Webhook não funciona

1. Teste a URL manualmente: `https://iafila.doisr.com.br/webhook/mercadopago`
2. Verifique logs em `application/logs/`
3. Confirme que a URL está acessível externamente

---

## 📊 Checklist de Deploy

- [ ] Banco de dados criado e importado
- [ ] Usuário do banco criado e vinculado
- [ ] Arquivos enviados e extraídos
- [ ] `database.php` configurado
- [ ] `config.php` com base_url correta
- [ ] Ambiente definido como 'production'
- [ ] Permissões de pastas configuradas
- [ ] SSL funcionando (HTTPS)
- [ ] Login testado
- [ ] Webhook do Mercado Pago configurado
- [ ] Credenciais do MP em produção configuradas

---

## 🔒 Segurança em Produção

1. **Desabilitar erros em tela:**
   ```php
   // index.php
   ini_set('display_errors', 0);
   error_reporting(0);
   ```

2. **Usar credenciais de produção do Mercado Pago**

3. **Fazer backup regular do banco de dados**

4. **Manter logs de acesso e erros**

5. **Usar senhas fortes para banco de dados**

---

## 📞 Suporte

Em caso de dúvidas ou problemas:
- Verifique os logs em `application/logs/`
- Consulte a documentação do CodeIgniter
- Entre em contato com o suporte do cPanel

**Autor:** Rafael Dias - doisr.com.br
**Data:** 06/12/2024
