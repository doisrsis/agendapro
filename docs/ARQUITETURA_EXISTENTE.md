# Documentação da Arquitetura Existente - AgendaPro

**Autor:** Rafael Dias - doisr.com.br
**Data:** 09/12/2024

---

## 📊 Resumo da Análise

O sistema **JÁ POSSUI** uma arquitetura funcional de autenticação e gerenciamento. Ao invés de recriar, devemos **ADAPTAR** o existente para multi-tenant.

---

## 🗂️ Estrutura Atual

### **Controllers Base**

#### 1. `MY_Controller.php` (core)
**Localização:** `application/core/MY_Controller.php`

**Classes:**
- `Admin_Controller` - Base para área administrativa
- `Public_Controller` - Base para área pública

**Funcionalidades:**
- ✅ Verificação de autenticação via `Auth_check`
- ✅ Carregamento de dados do usuário
- ✅ Registro de logs
- ✅ Upload de arquivos
- ✅ Respostas JSON padronizadas

**Problema:** Não suporta multi-tenant (estabelecimento_id)

#### 2. `Admin_Controller.php` (core) - DUPLICADO!
**Localização:** `application/core/Admin_Controller.php`

**Status:** ⚠️ Existe duplicado em `MY_Controller.php`

**Ação:** Remover duplicata e usar apenas `MY_Controller.php`

---

### **Libraries de Autenticação**

#### 1. `Auth_check.php` ✅ EXISTENTE
**Localização:** `application/libraries/Auth_check.php`

**Métodos:**
- `check_login()` - Verificar se está logado
- `check_nivel()` - Verificar nível de acesso
- `is_admin()` - Verificar se é admin
- `get_usuario()` - Obter dados do usuário
- `get_usuario_id()` - Obter ID
- `get_usuario_nome()` - Obter nome
- `get_usuario_nivel()` - Obter nível

**Problema:** Não suporta tipos multi-tenant (estabelecimento, profissional)

#### 2. `Auth_middleware.php` 🆕 CRIADO POR MIM
**Localização:** `application/libraries/Auth_middleware.php`

**Status:** Novo, criado para multi-tenant

**Ação:** Mesclar com `Auth_check.php` ou usar em paralelo

---

### **Models**

#### 1. `Usuario_model.php` ✅ EXISTENTE (ATUALIZADO)
**Localização:** `application/models/Usuario_model.php`

**Status:** JÁ FOI ATUALIZADO com métodos multi-tenant!

**Métodos Existentes:**
- ✅ `criar()` - Criar usuário com hash de senha
- ✅ `autenticar()` - Login
- ✅ `get()` - Buscar por ID
- ✅ `get_by_email()` - Buscar por e-mail
- ✅ `atualizar_senha()` - Atualizar senha
- ✅ `gerar_token_reset()` - Reset de senha
- ✅ `validar_token_reset()` - Validar token
- ✅ `criar_usuario_estabelecimento()` - Criar usuário estabelecimento
- ✅ `criar_usuario_profissional()` - Criar usuário profissional

**Conclusão:** Model já está pronto para multi-tenant!

#### 2. `Estabelecimento_model.php` ✅ EXISTENTE
**Localização:** `application/models/Estabelecimento_model.php`

**Métodos:**
- `get_all()` - Listar todos
- `get_by_id()` - Buscar por ID
- `create()` - Criar estabelecimento
- `update()` - Atualizar
- `delete()` - Deletar
- `criar_templates_notificacoes()` - Criar templates padrão
- `verificar_plano_vencido()` - Verificar vencimento
- `suspender()` - Suspender por falta de pagamento

**Problema:** Métodos usam `create/update/delete` ao invés de `criar/atualizar/excluir`

**Ação:** Padronizar nomenclatura OU criar aliases

#### 3. `Plano_model.php` ✅ EXISTENTE (ATUALIZADO)
**Status:** JÁ FOI ATUALIZADO com métodos multi-tenant!

#### 4. `Assinatura_model.php` ✅ EXISTENTE (ATUALIZADO)
**Status:** JÁ FOI ATUALIZADO com métodos multi-tenant!

#### 5. Outros Models Existentes:
- ✅ `Agendamento_model.php`
- ✅ `Bloqueio_model.php`
- ✅ `Cliente_model.php`
- ✅ `Configuracao_model.php`
- ✅ `Disponibilidade_model.php`
- ✅ `Log_model.php`
- ✅ `Notificacao_model.php`
- ✅ `Profissional_model.php`
- ✅ `Servico_model.php`

---

### **Views de Autenticação**

#### 1. `auth/login.php` ✅ EXISTENTE
**Localização:** `application/views/auth/login.php`

**Funcionalidades:**
- ✅ Formulário de login (e-mail/senha)
- ✅ Lembrar-me
- ✅ Link "Esqueci minha senha"
- ✅ Design com Tabler
- ✅ Toggle mostrar/ocultar senha

**Ação:** Usar como está!

#### 2. Outras Views de Auth (Provavelmente existem):
- `auth/recuperar_senha.php` (?)
- `auth/resetar_senha.php` (?)

---

### **Controllers Admin Existentes**

**Localização:** `application/controllers/admin/`

**Arquivos:**
1. ✅ `Dashboard.php` - Dashboard principal
2. ✅ `Agendamentos.php`
3. ✅ `Bloqueios.php`
4. ✅ `Clientes.php`
5. ✅ `Configuracoes.php`
6. ✅ `Disponibilidade.php`
7. ✅ `Estabelecimentos.php`
8. ✅ `Logs.php`
9. ✅ `Mercadopago_test.php`
10. ✅ `Pagamento_test.php`
11. ✅ `Pagamentos.php`
12. ✅ `Perfil.php`
13. ✅ `Profissionais.php`
14. ✅ `Servicos.php`
15. ✅ `Usuarios.php`

**Todos estendem:** `Admin_Controller`

**Problema:** Não filtram por `estabelecimento_id`

---

### **Libraries Existentes**

1. ✅ `Auth_check.php` - Autenticação básica
2. ✅ `Email_lib.php` - Envio de e-mails
3. ✅ `Mercadopago_lib.php` - Integração Mercado Pago
4. 🆕 `Auth_middleware.php` - Criado por mim (multi-tenant)

---

## 🎯 Plano de Adaptação

### **Fase 1: Consolidar Autenticação**

**Ação:** Mesclar `Auth_check.php` e `Auth_middleware.php`

**Resultado:** Uma única library com:
- Autenticação básica (existente)
- Suporte multi-tenant (novo)
- Verificação de limites de plano (novo)

### **Fase 2: Atualizar Admin_Controller**

**Ação:** Modificar `MY_Controller.php` → `Admin_Controller`

**Adicionar:**
```php
protected $estabelecimento_id;
protected $estabelecimento;

// Se usuário for tipo 'estabelecimento', carregar dados
if ($this->usuario->tipo === 'estabelecimento') {
    $this->estabelecimento_id = $this->usuario->estabelecimento_id;
    $this->estabelecimento = $this->Estabelecimento_model->get_by_id($this->estabelecimento_id);
}
```

### **Fase 3: Criar Painel_Controller e Agenda_Controller**

**Baseados em:** `Admin_Controller`

**Diferenças:**
- `Painel_Controller` → Apenas tipo 'estabelecimento'
- `Agenda_Controller` → Apenas tipo 'profissional'

### **Fase 4: Adaptar Controllers Admin**

**Ação:** Adicionar filtro por `estabelecimento_id` em todos os controllers

**Exemplo:**
```php
// ANTES
$clientes = $this->Cliente_model->get_all();

// DEPOIS
$clientes = $this->Cliente_model->get_all(['estabelecimento_id' => $this->estabelecimento_id]);
```

### **Fase 5: Criar Controller de Login Multi-Tenant**

**Ação:** Criar `Login.php` controller

**Funcionalidades:**
- Login único para todos os tipos
- Redirecionar conforme tipo:
  - `super_admin` → `/admin/dashboard`
  - `estabelecimento` → `/painel/dashboard`
  - `profissional` → `/agenda/dashboard`

### **Fase 6: Padronizar Nomenclatura**

**Ação:** Criar aliases em `Estabelecimento_model.php`

```php
public function criar($dados) {
    return $this->create($dados);
}

public function atualizar($id, $dados) {
    return $this->update($id, $dados);
}

public function get($id) {
    return $this->get_by_id($id);
}
```

---

## ✅ O Que JÁ Está Pronto

1. ✅ Banco de dados migrado para multi-tenant
2. ✅ `Usuario_model` com métodos multi-tenant
3. ✅ `Plano_model` completo
4. ✅ `Assinatura_model` completo
5. ✅ View de login pronta
6. ✅ Sistema de e-mail (`Email_lib`)
7. ✅ Integração Mercado Pago (`Mercadopago_lib`)
8. ✅ Todos os CRUDs básicos (Agendamentos, Clientes, etc)

---

## ⚠️ O Que Precisa Ser Adaptado

1. ⚠️ `Auth_check.php` → Adicionar suporte multi-tenant
2. ⚠️ `Admin_Controller` → Carregar dados do estabelecimento
3. ⚠️ Criar `Painel_Controller` e `Agenda_Controller`
4. ⚠️ Criar controller de Login multi-tenant
5. ⚠️ Adaptar todos os controllers admin para filtrar por estabelecimento
6. ⚠️ Padronizar nomenclatura de métodos

---

## 🚫 O Que NÃO Precisa Ser Criado

1. ❌ Sistema de autenticação básico (JÁ EXISTE)
2. ❌ Views de login (JÁ EXISTE)
3. ❌ Models básicos (JÁ EXISTEM)
4. ❌ Sistema de e-mail (JÁ EXISTE)
5. ❌ Integração Mercado Pago (JÁ EXISTE)
6. ❌ CRUDs básicos (JÁ EXISTEM)

---

## 📝 Próximos Passos Recomendados

1. **Mesclar Auth_check + Auth_middleware**
2. **Atualizar Admin_Controller para suportar multi-tenant**
3. **Criar Painel_Controller e Agenda_Controller**
4. **Criar controller Login.php**
5. **Adaptar controllers admin existentes**
6. **Testar fluxo completo**

---

**Conclusão:** O sistema está **80% pronto**. Precisamos apenas **ADAPTAR** o existente, não recriar! 🎯
