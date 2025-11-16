# 🚀 Projeto Base - Dashboard Administrativo

![PHP](https://img.shields.io/badge/PHP-7.4+-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3-red)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/License-MIT-green)

Projeto base reutilizável para iniciar novos sistemas web com dashboard administrativo, eliminando trabalho repetitivo de autenticação, usuários, configurações e permissões.

**Desenvolvido por:** [Rafael Dias - doisr.com.br](https://doisr.com.br)

---

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso](#uso)
- [Estrutura do Banco](#estrutura-do-banco)
- [Sistema de Permissões](#sistema-de-permissões)
- [Capturas de Tela](#capturas-de-tela)
- [Contribuindo](#contribuindo)
- [Licença](#licença)

---

## 🎯 Sobre o Projeto

Este é um **projeto base completo** para desenvolvimento de sistemas web administrativos. Ele já inclui toda a estrutura essencial que você precisa para começar um novo projeto, sem precisar reescrever autenticação, gerenciamento de usuários, configurações e logs toda vez.

### Por que usar este projeto?

✅ **Economize tempo** - Não reescreva código repetitivo
✅ **Código limpo** - Seguindo boas práticas e padrões
✅ **Seguro** - Sistema de autenticação robusto com bcrypt
✅ **Flexível** - Sistema de permissões granular por módulo
✅ **Moderno** - Interface responsiva com Tabler Dashboard
✅ **Documentado** - Código comentado e documentação completa

---

## ✨ Funcionalidades

### 🔐 Autenticação
- Login com e-mail e senha
- Recuperação de senha por e-mail
- Logout seguro
- Proteção de rotas administrativas
- Hash de senhas com bcrypt

### 👥 Gerenciamento de Usuários
- CRUD completo de usuários
- Dois níveis: **Admin** e **Usuário**
- Sistema de permissões por módulo
- Controle de status (ativo/inativo)
- Avatar personalizado

### 🔑 Sistema de Permissões
- Controle granular por módulo
- 4 níveis de permissão:
  - Visualizar
  - Criar
  - Editar
  - Excluir
- Admin tem acesso total
- Usuários têm acesso controlado

### ⚙️ Configurações
- **Geral:** Nome do sistema, e-mail, telefone
- **SMTP:** Configuração dinâmica de e-mail
- Teste de envio de e-mail
- Armazenamento no banco de dados

### 📊 Logs do Sistema
- Registro automático de ações
- Filtros por usuário, ação e data
- Paginação de resultados
- Limpeza de logs antigos

### 🔔 Notificações
- Sistema de notificações internas
- Notificações por e-mail
- Marcação de lidas/não lidas

---

## 🛠️ Tecnologias

### Back-end
- **PHP 7.4+**
- **CodeIgniter 3** - Framework MVC
- **MySQL 5.7+** - Banco de dados

### Front-end
- **HTML5, CSS3, JavaScript**
- **Bootstrap 4** - Framework CSS
- **Tabler Dashboard** - Template administrativo
- **jQuery** - Biblioteca JavaScript
- **Tabler Icons** - Ícones

---

## 📥 Instalação

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite
- Composer (opcional)

### Passo a Passo

1. **Clone o repositório:**
```bash
git clone https://github.com/doisrsis/projeto_base.git
cd projeto_base
```

2. **Configure o banco de dados:**
```bash
# Crie um banco de dados MySQL
CREATE DATABASE projeto_base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Importe o SQL
mysql -u root -p projeto_base < docs/projeto_base_database.sql

# Se quiser permissões por módulo, execute também:
mysql -u root -p projeto_base < docs/adicionar_permissoes.sql
```

3. **Configure o CodeIgniter:**

Copie e edite os arquivos de configuração:

```bash
# Configuração do banco
cp application/config/database.php.example application/config/database.php

# Configuração do sistema
cp application/config/config.php.example application/config/config.php
```

Edite `application/config/database.php`:
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'projeto_base',
);
```

Edite `application/config/config.php`:
```php
$config['base_url'] = 'http://localhost/projeto_base/';
$config['encryption_key'] = 'SUA_CHAVE_SECRETA_AQUI';
```

4. **Configure permissões (Linux/Mac):**
```bash
chmod -R 755 application/cache
chmod -R 755 application/logs
```

5. **Acesse o sistema:**
```
http://localhost/projeto_base/
```

**Credenciais padrão:**
- **E-mail:** admin@sistema.com.br
- **Senha:** admin123

⚠️ **IMPORTANTE:** Altere essas credenciais após o primeiro acesso!

---

## ⚙️ Configuração

### SMTP (E-mail)

1. Acesse: **Configurações → SMTP**
2. Preencha os dados do servidor SMTP
3. Clique em "Testar E-mail" para validar

**Exemplos de configuração:**

**Gmail:**
```
Host: smtp.gmail.com
Porta: 587
Segurança: TLS
Usuário: seu-email@gmail.com
Senha: Senha de App (não a senha normal!)
```

**Outlook:**
```
Host: smtp-mail.outlook.com
Porta: 587
Segurança: TLS
```

### Permissões de Usuários

1. Acesse: **Usuários → Editar**
2. Selecione nível "Usuário"
3. Marque os módulos e permissões desejadas
4. Salve

---

## 💾 Estrutura do Banco

O projeto usa **4 tabelas essenciais:**

### 1. `usuarios`
Gerenciamento de usuários do sistema
- Autenticação e perfil
- Níveis: admin, usuario
- Status: ativo, inativo

### 2. `usuario_permissoes`
Controle de permissões por módulo
- Permissões granulares (visualizar, criar, editar, excluir)
- Vinculado ao usuário

### 3. `configuracoes`
Configurações dinâmicas do sistema
- Grupos: geral, smtp
- Armazenamento chave-valor

### 4. `notificacoes`
Sistema de notificações
- Notificações internas
- Controle de leitura

### 5. `logs`
Registro de ações do sistema
- Auditoria completa
- Rastreamento de mudanças

---

## 🔐 Sistema de Permissões

### Níveis de Acesso

**Admin:**
- Acesso total a todos os módulos
- Não precisa de permissões configuradas
- Pode gerenciar outros usuários

**Usuário:**
- Acesso controlado por permissões
- Admin define quais módulos pode acessar
- 4 níveis por módulo: visualizar, criar, editar, excluir

### Módulos Disponíveis

- **Dashboard** - Página inicial
- **Usuários** - Gerenciamento de usuários
- **Configurações** - Configurações do sistema
- **Logs** - Histórico de ações

---

## 🚀 Uso

### Criando Novos Módulos

1. **Crie a tabela no banco de dados**
2. **Crie o Model** em `application/models/`
3. **Crie o Controller** em `application/controllers/admin/`
4. **Crie as Views** em `application/views/admin/`
5. **Adicione ao menu** em `application/views/admin/layout/header.php`
6. **Adicione às permissões** em `Usuarios::get_modulos_sistema()`

### Exemplo de Controller Admin

```php
<?php
class Meu_Modulo extends Admin_Controller {

    protected $modulo_atual = 'meu_modulo';

    public function __construct() {
        parent::__construct();
        $this->load->model('Meu_Model');
    }

    public function index() {
        // Verificação automática de permissão
        $data['titulo'] = 'Meu Módulo';
        $data['menu_ativo'] = 'meu_modulo';

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/meu_modulo/index', $data);
        $this->load->view('admin/layout/footer');
    }
}
```

---

## 📸 Capturas de Tela

_Em breve..._

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:

1. Fazer um Fork do projeto
2. Criar uma Branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a Branch (`git push origin feature/MinhaFeature`)
5. Abrir um Pull Request

---

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Autor

**Rafael Dias**

- Website: [doisr.com.br](https://doisr.com.br)
- GitHub: [@doisrsis](https://github.com/doisrsis)

---

## 📞 Suporte

Se você tiver alguma dúvida ou problema, abra uma [issue](https://github.com/doisrsis/projeto_base/issues) no GitHub.

---

## 🎉 Agradecimentos

- [CodeIgniter](https://codeigniter.com/)
- [Tabler](https://tabler.io/)
- [Bootstrap](https://getbootstrap.com/)

---

**Desenvolvido com ❤️ por [Rafael Dias - doisr.com.br](https://doisr.com.br)**
