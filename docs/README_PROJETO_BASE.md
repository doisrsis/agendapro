# 📦 PROJETO BASE - Dashboard Administrativo

**Autor:** Rafael Dias - [doisr.com.br](https://doisr.com.br)
**Data de Criação:** 16/11/2024 19:21
**Versão:** 1.0.0

---

## 📋 Sobre o Projeto

Este é um **projeto base** completo e pronto para uso, desenvolvido para acelerar o início de novos projetos web que necessitam de um painel administrativo.

O projeto já inclui toda a estrutura essencial de autenticação, gerenciamento de usuários, configurações dinâmicas e sistema de notificações, permitindo que você foque no desenvolvimento das funcionalidades específicas do seu sistema.

---

## ✨ Funcionalidades Incluídas

### 🔐 Sistema de Autenticação
- ✅ Login com validação de credenciais
- ✅ Logout seguro
- ✅ Recuperação de senha por e-mail
- ✅ Sistema de "Lembrar-me"
- ✅ Proteção de rotas administrativas
- ✅ Registro de logs de acesso

### 👥 Gerenciamento de Usuários
- ✅ CRUD completo de usuários
- ✅ Níveis de acesso (Admin e Usuário)
- ✅ Status ativo/inativo
- ✅ Perfil com foto de avatar
- ✅ Histórico de último acesso

### 🔔 Sistema de Notificações
- ✅ Notificações internas do sistema
- ✅ Notificações por e-mail
- ✅ Tipos: Info, Sucesso, Aviso, Erro
- ✅ Marcar como lida
- ✅ Contador de não lidas
- ✅ Limpeza automática de notificações antigas

### ⚙️ Configurações Dinâmicas
- ✅ Configurações gerais do sistema
- ✅ Configurações SMTP (e-mail)
- ✅ Configurações de notificações
- ✅ Todas armazenadas no banco de dados
- ✅ Teste de envio de e-mail integrado

### 📊 Sistema de Logs
- ✅ Registro de todas as ações importantes
- ✅ Rastreamento de login/logout
- ✅ Histórico de alterações (antes/depois)
- ✅ Registro de IP e User Agent

### 🎨 Interface Moderna
- ✅ Template Tabler Dashboard
- ✅ Design responsivo (PC/Tablet/Mobile)
- ✅ Interface limpa e profissional
- ✅ Componentes prontos para uso

---

## 🛠️ Stack Tecnológica

### Back-end
- **PHP:** 7.4+
- **Framework:** CodeIgniter 3
- **Banco de Dados:** MySQL 5.7+

### Front-end
- **HTML5, CSS3, JavaScript (ES6)**
- **jQuery**
- **Bootstrap 4**
- **Template:** Tabler Dashboard

---

## 📦 Estrutura do Banco de Dados

O projeto utiliza apenas **4 tabelas essenciais**:

### 1. `usuarios`
Armazena os usuários do sistema com autenticação completa.

**Campos principais:**
- `id`, `nome`, `email`, `senha`
- `nivel` (admin/usuario)
- `status` (ativo/inativo)
- `token_recuperacao`, `token_expiracao`
- `ultimo_acesso`

### 2. `notificacoes`
Sistema de notificações para os usuários.

**Campos principais:**
- `id`, `usuario_id`, `tipo`
- `titulo`, `mensagem`, `link`
- `lida`, `data_leitura`

### 3. `configuracoes`
Configurações dinâmicas do sistema.

**Campos principais:**
- `id`, `chave`, `valor`
- `tipo` (texto/numero/booleano/json/arquivo)
- `grupo` (geral/smtp/notificacoes)

### 4. `logs`
Registro de ações do sistema.

**Campos principais:**
- `id`, `usuario_id`, `acao`
- `tabela`, `registro_id`
- `dados_antigos`, `dados_novos`
- `ip`, `user_agent`

---

## 🚀 Instalação

### Passo 1: Requisitos
- XAMPP, WAMP ou servidor com PHP 7.4+
- MySQL 5.7+
- Composer (opcional)

### Passo 2: Banco de Dados
1. Crie um novo banco de dados no phpMyAdmin
2. Importe o arquivo: `docs/projeto_base_database.sql`
3. O banco será criado com um usuário padrão

### Passo 3: Configuração
1. Edite o arquivo `application/config/database.php`:
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'seu_usuario',
    'password' => 'sua_senha',
    'database' => 'seu_banco',
    // ... demais configurações
);
```

2. Edite o arquivo `application/config/config.php`:
```php
$config['base_url'] = 'http://localhost/projeto_base/';
$config['encryption_key'] = 'SuaChaveSecretaAqui123456789';
```

### Passo 4: Acesso
1. Acesse: `http://localhost/projeto_base/`
2. Faça login com as credenciais padrão:
   - **E-mail:** admin@sistema.com.br
   - **Senha:** admin123
3. **IMPORTANTE:** Altere a senha padrão após o primeiro acesso!

---

## ⚙️ Configuração SMTP

Para habilitar o envio de e-mails (recuperação de senha, notificações):

1. Acesse: **Configurações > SMTP**
2. Preencha os dados do seu servidor SMTP:
   - Host SMTP
   - Porta (587 para TLS, 465 para SSL)
   - Usuário (e-mail)
   - Senha
   - Segurança (TLS ou SSL)
3. Configure o remetente
4. Ative o SMTP
5. Clique em **"Testar E-mail"** para verificar

### Exemplos de Configuração SMTP

**Gmail:**
- Host: smtp.gmail.com
- Porta: 587
- Segurança: TLS
- *Obs: Habilite "Acesso a apps menos seguros"*

**Outlook/Hotmail:**
- Host: smtp-mail.outlook.com
- Porta: 587
- Segurança: TLS

**Servidor Próprio (cPanel):**
- Host: mail.seudominio.com.br
- Porta: 465
- Segurança: SSL

---

## 📁 Estrutura de Arquivos

```
projeto_base/
├── application/
│   ├── controllers/
│   │   ├── Auth.php              # Autenticação
│   │   └── admin/
│   │       ├── Dashboard.php     # Dashboard principal
│   │       ├── Usuarios.php      # Gerenciamento de usuários
│   │       ├── Configuracoes.php # Configurações do sistema
│   │       ├── Perfil.php        # Perfil do usuário
│   │       └── Logs.php          # Visualização de logs
│   ├── models/
│   │   ├── Usuario_model.php     # Model de usuários
│   │   ├── Notificacao_model.php # Model de notificações
│   │   ├── Configuracao_model.php# Model de configurações
│   │   └── Log_model.php         # Model de logs
│   ├── views/
│   │   ├── auth/                 # Views de autenticação
│   │   │   ├── login.php
│   │   │   ├── recuperar_senha.php
│   │   │   └── resetar_senha.php
│   │   └── admin/                # Views do painel admin
│   │       ├── layout/           # Header e Footer
│   │       ├── dashboard/        # Dashboard
│   │       ├── usuarios/         # CRUD de usuários
│   │       ├── configuracoes/    # Configurações
│   │       ├── perfil/           # Perfil
│   │       └── logs/             # Logs
│   └── core/
│       └── Admin_Controller.php  # Controller base admin
├── assets/                       # CSS, JS, Imagens
├── docs/                         # Documentação
│   ├── projeto_base_database.sql # SQL do banco
│   └── README_PROJETO_BASE.md    # Este arquivo
├── system/                       # Core do CodeIgniter
├── uploads/                      # Uploads (avatares, etc)
└── index.php                     # Ponto de entrada
```

---

## 🎯 Como Usar Este Projeto Base

### Para Iniciar um Novo Projeto:

1. **Clone ou copie** este projeto para uma nova pasta
2. **Renomeie** a pasta com o nome do seu projeto
3. **Importe** o banco de dados
4. **Configure** database.php e config.php
5. **Personalize** as configurações gerais no painel
6. **Comece a desenvolver** suas funcionalidades específicas!

### Adicionando Novos Módulos:

1. Crie as **tabelas** necessárias no banco
2. Crie o **Model** em `application/models/`
3. Crie o **Controller** em `application/controllers/admin/`
4. Crie as **Views** em `application/views/admin/`
5. Adicione o **menu** no header do layout

---

## 🔒 Segurança

O projeto já inclui:

- ✅ Proteção contra SQL Injection (Query Builder do CI)
- ✅ Proteção contra XSS (validação de inputs)
- ✅ Senhas criptografadas com password_hash()
- ✅ Tokens de recuperação de senha com expiração
- ✅ Proteção de rotas administrativas
- ✅ Registro de logs de ações
- ✅ Validação de sessões

**Recomendações adicionais:**
- Altere a `encryption_key` no config.php
- Use HTTPS em produção
- Mantenha o PHP e MySQL atualizados
- Faça backups regulares do banco de dados

---

## 📝 Boas Práticas Implementadas

- ✅ Arquitetura MVC
- ✅ Código comentado e documentado
- ✅ Nomenclatura padronizada
- ✅ Separação de responsabilidades
- ✅ Reutilização de código
- ✅ Validação de dados
- ✅ Tratamento de erros
- ✅ Logs de ações importantes

---

## 🆘 Suporte e Dúvidas

Para dúvidas ou suporte:
- **E-mail:** contato@doisr.com.br
- **Website:** [doisr.com.br](https://doisr.com.br)

---

## 📄 Licença

Este projeto base foi desenvolvido por **Rafael Dias** e pode ser utilizado livremente em seus projetos pessoais ou comerciais.

---

## 🎉 Pronto para Usar!

Agora você tem um projeto base completo e profissional para iniciar seus novos sistemas web. Economize tempo e foque no que realmente importa: as funcionalidades específicas do seu projeto!

**Bom desenvolvimento! 🚀**

---

*Última atualização: 16/11/2024*
