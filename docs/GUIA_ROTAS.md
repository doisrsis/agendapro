# Guia de Rotas - Sistema Multi-Tenant

**Autor:** Rafael Dias - doisr.com.br
**Data:** 10/12/2024

---

## 🔐 Rotas de Autenticação

### Login
- **URL:** `/login`
- **Controller:** `Login::index()`
- **Acesso:** Público
- **Descrição:** Página de login multi-tenant

### Logout
- **URL:** `/logout` ou `/sair`
- **Controller:** `Login::logout()`
- **Acesso:** Autenticado
- **Descrição:** Encerra sessão e redireciona para login

### Recuperar Senha
- **URL:** `/recuperar-senha`
- **Controller:** `Login::recuperar_senha()`
- **Acesso:** Público
- **Descrição:** Solicita recuperação de senha por e-mail

### Resetar Senha
- **URL:** `/resetar-senha/{token}`
- **Controller:** `Login::resetar_senha($token)`
- **Acesso:** Público (com token válido)
- **Descrição:** Define nova senha usando token recebido por e-mail

---

## 👨‍💼 Rotas Admin (Super Admin)

### Dashboard
- **URL:** `/admin` ou `/admin/dashboard`
- **Controller:** `Admin/Dashboard::index()`
- **Acesso:** `super_admin`
- **Descrição:** Painel principal do super administrador

### Outras Rotas Admin
- **Padrão:** `/admin/{controller}/{metodo}`
- **Exemplos:**
  - `/admin/estabelecimentos` - Gerenciar estabelecimentos
  - `/admin/planos` - Gerenciar planos
  - `/admin/usuarios` - Gerenciar usuários
  - `/admin/logs` - Visualizar logs do sistema

---

## 🏢 Rotas Painel (Estabelecimento)

### Dashboard
- **URL:** `/painel` ou `/painel/dashboard`
- **Controller:** `Painel/Dashboard::index()`
- **Acesso:** `estabelecimento`
- **Descrição:** Painel principal do estabelecimento

### Outras Rotas Painel
- **Padrão:** `/painel/{controller}/{metodo}`
- **Exemplos:**
  - `/painel/profissionais` - Gerenciar profissionais
  - `/painel/servicos` - Gerenciar serviços
  - `/painel/clientes` - Gerenciar clientes
  - `/painel/agendamentos` - Gerenciar agendamentos
  - `/painel/configuracoes` - Configurações do estabelecimento
  - `/painel/assinatura` - Gerenciar assinatura

### Páginas Especiais
- **URL:** `/painel/suspenso`
- **Descrição:** Conta suspensa por falta de pagamento

- **URL:** `/painel/cancelado`
- **Descrição:** Conta cancelada

- **URL:** `/painel/assinatura-expirada`
- **Descrição:** Assinatura expirou, necessário renovar

---

## 📅 Rotas Agenda (Profissional)

### Dashboard
- **URL:** `/agenda` ou `/agenda/dashboard`
- **Controller:** `Agenda/Dashboard::index()`
- **Acesso:** `profissional`
- **Descrição:** Agenda do profissional

### Outras Rotas Agenda
- **Padrão:** `/agenda/{controller}/{metodo}`
- **Exemplos:**
  - `/agenda/meus-agendamentos` - Ver agendamentos
  - `/agenda/disponibilidade` - Configurar disponibilidade
  - `/agenda/bloqueios` - Gerenciar bloqueios
  - `/agenda/perfil` - Editar perfil

---

## 🌐 Rotas Públicas

### Webhook Mercado Pago
- **URL:** `/webhook/mercadopago`
- **Controller:** `Webhook::mercadopago()`
- **Acesso:** Público (validação interna)
- **Descrição:** Recebe notificações de pagamento

### API Pública
- **Padrão:** `/api/{endpoint}`
- **Acesso:** Público (com autenticação de API se necessário)

---

## 🔄 Fluxo de Redirecionamento

### Após Login
O sistema redireciona automaticamente baseado no tipo de usuário:

```php
super_admin      → /admin/dashboard
estabelecimento  → /painel/dashboard
profissional     → /agenda/dashboard
```

### Acesso Não Autorizado
Se usuário tentar acessar área sem permissão:

```php
estabelecimento tentando acessar /admin → Redireciona para /painel/dashboard
profissional tentando acessar /painel  → Redireciona para /agenda/dashboard
```

### Sem Login
Qualquer acesso sem autenticação redireciona para `/login`

---

## 📝 Exemplos de Uso

### Login
```
GET  /login                    → Exibe formulário
POST /login                    → Processa autenticação
     email=user@example.com
     senha=123456
     lembrar=1
```

### Recuperar Senha
```
GET  /recuperar-senha          → Exibe formulário
POST /recuperar-senha          → Envia e-mail com token
     email=user@example.com

GET  /resetar-senha/abc123     → Exibe formulário de nova senha
POST /resetar-senha/abc123     → Define nova senha
     senha=novaSenha123
     senha_confirmar=novaSenha123
```

### Acesso ao Painel
```
GET /painel/profissionais      → Lista profissionais do estabelecimento
GET /painel/agendamentos       → Lista agendamentos do estabelecimento
GET /painel/configuracoes      → Configurações do estabelecimento
```

---

## 🔒 Proteção de Rotas

### Middleware Automático
Todos os controllers que estendem `Admin_Controller`, `Painel_Controller` ou `Agenda_Controller` têm proteção automática:

- ✅ Verifica se está logado
- ✅ Verifica tipo de usuário
- ✅ Verifica status do estabelecimento
- ✅ Verifica assinatura ativa
- ✅ Carrega dados do estabelecimento

### Verificação Manual
Para rotas específicas, use:

```php
// Verificar tipo
$this->auth_check->check_tipo(['super_admin', 'estabelecimento']);

// Verificar se é super admin
if (!$this->auth_check->is_super_admin()) {
    redirect('painel/dashboard');
}
```

---

## 🎯 Boas Práticas

1. **URLs Amigáveis:** Use `-` ao invés de `_` nas URLs
2. **Verbos HTTP:** GET para visualizar, POST para criar/atualizar
3. **Redirecionamento:** Sempre redirecione após POST
4. **Mensagens Flash:** Use `set_flashdata()` para feedback
5. **Validação:** Sempre valide dados antes de processar

---

**Conclusão:** Sistema de rotas completo e organizado para multi-tenant! 🎉
