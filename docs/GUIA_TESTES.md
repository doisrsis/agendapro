# Guia de Testes - Sistema Multi-Tenant

**Autor:** Rafael Dias - doisr.com.br
**Data:** 10/12/2024

---

## ✅ O Que Está Pronto Para Testar

### 1. Autenticação Multi-Tenant ✅
- Login com redirecionamento automático
- Logout
- Recuperação de senha
- Lembrar-me

### 2. Isolamento de Dados ✅
- Clientes filtrados por estabelecimento
- Verificação de permissões

### 3. Limites de Plano ✅
- Verificação automática de assinatura
- Métodos prontos (ainda não aplicados em todos os controllers)

---

## 🧪 Testes Básicos

### Teste 1: Login e Redirecionamento

**1. Acessar a página de login:**
```
http://localhost/agendapro/login
```

**2. Fazer login com um usuário existente**

**Resultado Esperado:**
- Se for `super_admin` → Redireciona para `/admin/dashboard`
- Se for `estabelecimento` → Redireciona para `/painel/dashboard`
- Se for `profissional` → Redireciona para `/agenda/dashboard`

**⚠️ Nota:** Como ainda não criamos os controllers `Painel` e `Agenda`, você pode receber erro 404 se logar como estabelecimento ou profissional. Isso é normal!

---

### Teste 2: Clientes (Multi-Tenant)

**1. Fazer login como super_admin**

**2. Acessar:**
```
http://localhost/agendapro/admin/clientes
```

**Resultado Esperado:**
- ✅ Lista TODOS os clientes
- ✅ Mostra filtro de estabelecimento
- ✅ Pode criar cliente para qualquer estabelecimento

**3. Fazer login como usuário de estabelecimento**

**4. Acessar:**
```
http://localhost/agendapro/admin/clientes
```

**Resultado Esperado:**
- ✅ Lista APENAS clientes do seu estabelecimento
- ✅ NÃO mostra filtro de estabelecimento
- ✅ Ao criar cliente, estabelecimento_id é automático

---

### Teste 3: Recuperação de Senha

**1. Acessar:**
```
http://localhost/agendapro/recuperar-senha
```

**2. Informar e-mail cadastrado**

**Resultado Esperado:**
- ✅ Mensagem de sucesso
- ✅ E-mail enviado com link de recuperação
- ⚠️ Verificar se `Email_lib` está configurado

---

### Teste 4: Logout

**1. Estando logado, acessar:**
```
http://localhost/agendapro/logout
```
ou
```
http://localhost/agendapro/sair
```

**Resultado Esperado:**
- ✅ Sessão encerrada
- ✅ Redireciona para `/login`
- ✅ Mensagem de sucesso

---

## ⚠️ Problemas Esperados (Normal!)

### 1. Erro 404 em `/painel/dashboard` ou `/agenda/dashboard`
**Causa:** Controllers `Painel` e `Agenda` ainda não foram criados

**Solução Temporária:**
- Logar apenas como super_admin
- OU criar controllers básicos de Painel e Agenda

### 2. Erro ao enviar e-mail de recuperação
**Causa:** `Email_lib` pode não estar configurado

**Solução:** Verificar configurações de e-mail em `config/email.php`

### 3. Outros controllers admin ainda não filtram por estabelecimento
**Causa:** Apenas `Clientes.php` foi adaptado

**Solução:** Adaptar os demais controllers conforme o guia

---

## 🎯 Teste Rápido (5 minutos)

### Cenário 1: Login como Super Admin
```bash
1. Acesse: http://localhost/agendapro/login
2. Login: (seu usuário super_admin)
3. Deve redirecionar para: /admin/dashboard
4. Acesse: /admin/clientes
5. Deve listar todos os clientes
```

### Cenário 2: Verificar Isolamento
```bash
1. Acesse: http://localhost/agendapro/admin/clientes
2. Tente visualizar um cliente
3. Tente editar um cliente
4. Tente criar um cliente
5. Verifique se logs estão sendo registrados
```

---

## 🐛 Possíveis Erros e Soluções

### Erro: "Call to undefined function get_nome_sistema()"
**Solução:** Criar helper ou definir função em `autoload.php`

### Erro: "Class 'Log_model' not found"
**Solução:** Criar `Log_model` ou comentar chamadas de log temporariamente

### Erro: "Table 'logs' doesn't exist"
**Solução:** Criar tabela `logs` ou comentar registros de log

### Erro: "Unable to load the requested class: Email_lib"
**Solução:** Verificar se `Email_lib` existe ou usar `email` nativo do CI

---

## 📝 Checklist de Teste

- [ ] Login funciona
- [ ] Redirecionamento correto por tipo de usuário
- [ ] Logout funciona
- [ ] Clientes lista apenas do estabelecimento correto
- [ ] Criar cliente usa estabelecimento correto
- [ ] Editar cliente verifica permissão
- [ ] Deletar cliente verifica permissão
- [ ] Recuperação de senha envia e-mail
- [ ] Reset de senha funciona

---

## 🚀 Próximos Passos Após Testes

### Se tudo funcionar:
1. ✅ Adaptar controllers prioritários (Profissionais, Serviços, Agendamentos)
2. ✅ Criar controllers Painel e Agenda
3. ✅ Testar fluxo completo

### Se houver erros:
1. ⚠️ Anotar os erros
2. ⚠️ Verificar logs do PHP
3. ⚠️ Corrigir um por um

---

## 💡 Dicas

1. **Abra o console do navegador (F12)** para ver erros JavaScript
2. **Verifique logs do PHP** em `application/logs/`
3. **Use modo desenvolvedor** para ver requisições
4. **Teste com diferentes tipos de usuário**

---

## ✅ Conclusão

**Você pode testar agora:**
- ✅ Login multi-tenant
- ✅ Clientes com isolamento de dados
- ✅ Recuperação de senha
- ✅ Logout

**Ainda não está pronto:**
- ⏭️ Outros 14 controllers admin
- ⏭️ Controllers Painel e Agenda
- ⏭️ Dashboard multi-tenant

**Mas o core do sistema multi-tenant está funcionando!** 🎉

---

**Bons testes!** 🚀

Se encontrar algum erro, me avise que eu corrijo imediatamente!
