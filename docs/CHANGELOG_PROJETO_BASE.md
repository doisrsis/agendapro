# 📝 CHANGELOG - PROJETO BASE

**Autor:** Rafael Dias - doisr.com.br
**Data:** 16/11/2024 19:21

---

## 🎯 Objetivo

Transformar o projeto "Le Cortine - Sistema de Orçamentos" em um **projeto base reutilizável** para iniciar novos sistemas web com dashboard administrativo.

---

## ✅ O Que Foi Feito

### 🗄️ Banco de Dados

#### ❌ Removido (Tabelas Específicas do Le Cortine):
- `categorias` - Categorias de produtos
- `clientes` - Clientes do sistema de orçamentos
- `colecoes` - Coleções de tecidos
- `cores` - Cores de tecidos
- `extras` - Extras de produtos
- `orcamentos` - Orçamentos gerados
- `orcamento_itens` - Itens dos orçamentos
- `orcamento_extras` - Extras dos itens
- `orcamento_email_logs` - Logs de e-mails de orçamentos
- `precos` - Tabela de preços
- `produtos` - Produtos do catálogo
- `produto_imagens` - Galeria de imagens
- `tecidos` - Tecidos disponíveis
- `usuario_permissoes` - Permissões granulares (simplificado para níveis)

#### ✅ Mantido (Tabelas Essenciais):
- `usuarios` - Sistema de autenticação e usuários
- `notificacoes` - Sistema de notificações
- `configuracoes` - Configurações dinâmicas do sistema
- `logs` - Registro de ações do sistema

#### 📄 Novo Arquivo SQL:
- **Criado:** `docs/projeto_base_database.sql`
- Banco limpo e documentado
- Usuário padrão: admin@sistema.com.br / admin123
- Configurações padrão incluídas

---

### 🎮 Controllers

#### ❌ Removido:
- `Home.php` - Página pública do Le Cortine
- `Orcamento.php` - Sistema de orçamentos online
- `Welcome.php` - Página padrão do CodeIgniter
- `admin/Categorias.php`
- `admin/Clientes.php`
- `admin/Colecoes.php`
- `admin/Extras.php`
- `admin/Orcamentos.php`
- `admin/Precos.php`
- `admin/Produtos.php`
- `admin/Tecidos.php`

#### ✅ Mantido:
- `Auth.php` - Sistema de autenticação completo
- `admin/Dashboard.php` - Dashboard principal
- `admin/Usuarios.php` - Gerenciamento de usuários
- `admin/Configuracoes.php` - Configurações do sistema
- `admin/Perfil.php` - Perfil do usuário
- `admin/Logs.php` - Visualização de logs

#### 🔄 Atualizado:
- **`admin/Configuracoes.php`:**
  - Removido métodos de Correios e Mercado Pago
  - Adicionado método `smtp()` para configurações SMTP
  - Atualizado `testar_email()` para usar configurações dinâmicas do banco
  - SMTP agora é 100% configurável pelo painel

---

### 📊 Models

#### ❌ Removido:
- `Categoria_model.php`
- `Cliente_model.php`
- `Colecao_model.php`
- `Extra_model.php`
- `Orcamento_email_log_model.php`
- `Orcamento_model.php`
- `Preco_model.php`
- `Produto_model.php`
- `Tecido_model.php`

#### ✅ Mantido:
- `Usuario_model.php` - Gerenciamento de usuários
- `Configuracao_model.php` - Configurações do sistema
- `Log_model.php` - Sistema de logs

#### ➕ Criado:
- **`Notificacao_model.php`:**
  - Sistema completo de notificações
  - Métodos para criar, listar, marcar como lida
  - Suporte a notificações para usuário específico ou todos
  - Limpeza automática de notificações antigas

#### 🔄 Atualizado:
- **`Configuracao_model.php`:**
  - Adicionado método `get_valor($chave, $default)` - atalho para pegar valores
  - Adicionado método `get_smtp()` - buscar configurações SMTP
  - Mantidos métodos de grupos (geral, notificacoes, etc)

---

### 🎨 Views

#### ❌ Removido:
- Pasta `public/` - Views públicas do Le Cortine
- `welcome_message.php` - Página padrão do CI
- `admin/categorias/` - CRUD de categorias
- `admin/clientes/` - CRUD de clientes
- `admin/colecoes/` - CRUD de coleções
- `admin/extras/` - CRUD de extras
- `admin/orcamentos/` - Gerenciamento de orçamentos
- `admin/precos/` - CRUD de preços
- `admin/produtos/` - CRUD de produtos
- `admin/tecidos/` - CRUD de tecidos

#### ✅ Mantido:
- `auth/` - Views de autenticação (login, recuperar senha, resetar senha)
- `admin/layout/` - Header e Footer do painel
- `admin/dashboard/` - Dashboard principal
- `admin/usuarios/` - CRUD de usuários
- `admin/configuracoes/` - Configurações do sistema
- `admin/perfil/` - Perfil do usuário
- `admin/logs/` - Visualização de logs

#### 📝 Nota:
As views existentes do Tabler Dashboard foram mantidas e continuam funcionais.

---

### ⚙️ Configurações

#### 🔄 Atualizado:

**`application/config/database.php`:**
```php
// ANTES (Servidor remoto Le Cortine)
'hostname' => '177.136.251.242',
'username' => 'cecriativocom_orc_lecortine',
'password' => 'c$uZaCQh{%Dh7kc=2025',
'database' => 'cecriativocom_lecortine_orc',

// DEPOIS (Localhost padrão)
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'projeto_base',
```

**`application/config/email.php`:**
- Removidas credenciais hardcoded do Le Cortine
- Adicionada documentação sobre configuração dinâmica
- Configurações agora vêm do banco de dados via painel admin

---

### 📚 Documentação

#### ➕ Criado:

1. **`docs/projeto_base_database.sql`**
   - Banco de dados limpo e documentado
   - 4 tabelas essenciais
   - Dados iniciais (usuário admin + configurações)
   - Comentários explicativos

2. **`docs/README_PROJETO_BASE.md`**
   - Documentação completa do projeto
   - Funcionalidades incluídas
   - Stack tecnológica
   - Guia de instalação detalhado
   - Estrutura de arquivos
   - Como usar o projeto base
   - Boas práticas implementadas
   - Dicas de segurança

3. **`docs/INSTALACAO.md`**
   - Guia rápido de instalação (5 minutos)
   - Passo a passo ilustrado
   - Configurações pós-instalação
   - Solução de problemas comuns

4. **`docs/CHANGELOG_PROJETO_BASE.md`** (este arquivo)
   - Registro completo de todas as alterações
   - O que foi removido, mantido e criado

---

## 🎯 Funcionalidades do Projeto Base

### ✅ Incluídas e Funcionais:

1. **Sistema de Autenticação**
   - Login/Logout
   - Recuperação de senha por e-mail
   - Sistema "Lembrar-me"
   - Proteção de rotas

2. **Gerenciamento de Usuários**
   - CRUD completo
   - Níveis: Admin e Usuário
   - Status ativo/inativo
   - Avatar

3. **Sistema de Notificações**
   - Notificações internas
   - Notificações por e-mail
   - Tipos: Info, Sucesso, Aviso, Erro
   - Contador de não lidas

4. **Configurações Dinâmicas**
   - Configurações gerais
   - Configurações SMTP (100% dinâmicas)
   - Configurações de notificações
   - Teste de envio de e-mail

5. **Sistema de Logs**
   - Registro de login/logout
   - Registro de ações (criar, editar, excluir)
   - Dados antes/depois
   - IP e User Agent

6. **Interface Moderna**
   - Tabler Dashboard
   - Responsivo (PC/Tablet/Mobile)
   - Componentes prontos

---

## 📦 Estrutura Final

```
projeto_base/
├── application/
│   ├── controllers/
│   │   ├── Auth.php
│   │   └── admin/
│   │       ├── Dashboard.php
│   │       ├── Usuarios.php
│   │       ├── Configuracoes.php
│   │       ├── Perfil.php
│   │       └── Logs.php
│   ├── models/
│   │   ├── Usuario_model.php
│   │   ├── Notificacao_model.php
│   │   ├── Configuracao_model.php
│   │   └── Log_model.php
│   ├── views/
│   │   ├── auth/
│   │   └── admin/
│   └── config/
│       ├── database.php (atualizado)
│       └── email.php (atualizado)
├── docs/
│   ├── projeto_base_database.sql (NOVO)
│   ├── README_PROJETO_BASE.md (NOVO)
│   ├── INSTALACAO.md (NOVO)
│   └── CHANGELOG_PROJETO_BASE.md (NOVO)
└── ... (demais arquivos do CI3 e Tabler)
```

---

## 🚀 Como Usar

1. **Copie** este projeto para uma nova pasta
2. **Renomeie** com o nome do seu projeto
3. **Importe** o SQL: `docs/projeto_base_database.sql`
4. **Configure** `database.php` e `config.php`
5. **Acesse** e faça login: admin@sistema.com.br / admin123
6. **Personalize** as configurações no painel
7. **Desenvolva** suas funcionalidades específicas!

---

## 🎉 Resultado Final

Um projeto base **limpo**, **documentado** e **pronto para uso**, que economiza horas de desenvolvimento inicial e permite focar no que realmente importa: as funcionalidades específicas do seu sistema!

---

## 📊 Estatísticas

- **Tabelas removidas:** 14
- **Tabelas mantidas:** 4
- **Controllers removidos:** 12
- **Controllers mantidos:** 6
- **Models removidos:** 9
- **Models mantidos:** 3
- **Models criados:** 1
- **Views removidas:** ~30 arquivos
- **Views mantidas:** ~20 arquivos
- **Documentos criados:** 4

---

## 🔐 Credenciais Padrão

**Acesso ao Sistema:**
- E-mail: admin@sistema.com.br
- Senha: admin123

**Banco de Dados (localhost):**
- Host: localhost
- Usuário: root
- Senha: (vazio)
- Database: projeto_base

⚠️ **IMPORTANTE:** Altere todas as credenciais padrão após a instalação!

---

## 📞 Suporte

**Desenvolvido por:** Rafael Dias
**Website:** [doisr.com.br](https://doisr.com.br)
**E-mail:** contato@doisr.com.br

---

---

## 🆕 v1.1.0 - Sistema de Upload de Logo (16/11/2024 21:14)

### ✨ Novas Funcionalidades:

**1. Sistema de Upload de Logo**
- ✅ Campo de upload em Configurações → Geral
- ✅ Formatos aceitos: JPG, PNG, SVG
- ✅ Tamanho máximo: 2MB
- ✅ Preview da logo atual
- ✅ Opção para remover logo
- ✅ Remove logo antiga automaticamente ao enviar nova

**2. Helper de Logo**
- ✅ `application/helpers/logo_helper.php` criado
- ✅ Função `exibir_logo()` - Para menu admin (32px)
- ✅ Função `exibir_logo_login()` - Para login (80px)
- ✅ Função `get_nome_sistema()` - Retorna nome do sistema
- ✅ **Fallback automático:** Se não houver logo, exibe o nome do sistema

**3. Integração Completa**
- ✅ Logo no menu superior do admin
- ✅ Logo na página de login
- ✅ Logo na página de recuperação de senha
- ✅ Logo na página de resetar senha
- ✅ Título dinâmico em todas as páginas

### 📝 Arquivos Modificados:

**Controllers:**
- `application/controllers/admin/Configuracoes.php` - Processamento de upload

**Views:**
- `application/views/admin/configuracoes/index.php` - Campo de upload
- `application/views/admin/layout/header.php` - Logo no menu
- `application/views/auth/login.php` - Logo na tela de login
- `application/views/auth/recuperar_senha.php` - Logo na recuperação
- `application/views/auth/resetar_senha.php` - Logo no reset

**Config:**
- `application/config/autoload.php` - Carrega helper automaticamente

**Outros:**
- `index.php` - Remoção automática da pasta install/
- `.gitignore` - Atualizado

### 📁 Arquivos Criados:

- `application/helpers/logo_helper.php` - Helper de logo
- `assets/img/logo/` - Pasta para uploads
- `assets/img/logo/index.html` - Proteção de diretório

### 🎯 Como Usar:

1. Acesse: **Configurações → Geral**
2. Na seção "Personalização", faça upload da logo
3. A logo aparecerá automaticamente em:
   - Menu superior
   - Página de login
   - Recuperação de senha
4. Se não enviar logo, o nome do sistema será exibido

---

**Versão:** 1.1.0
**Data:** 16/11/2024 21:14
**Status:** ✅ Concluído e Pronto para Uso

---

*Este projeto base foi criado para acelerar o desenvolvimento de novos sistemas web. Use-o livremente em seus projetos!*
