# Guia de Teste - Sistema de Agendamento

## 📋 Pré-requisitos

- ✅ XAMPP rodando (Apache + MySQL)
- ✅ Banco de dados já existente (do sistema anterior)

## 🚀 Passos para Testar

### 1. Importar Novas Tabelas

Acesse o phpMyAdmin e execute o arquivo:
```
docs/agendapro_database.sql
```

**OU** execute via linha de comando:
```bash
mysql -u cecriativocom_orc_lecortine -p cecriativocom_lecortine_orc < docs/agendapro_database.sql
```

### 2. Acessar o Sistema

Faça login no sistema:
```
http://localhost/agendapro/login
```

**Credenciais:**
- Email: `admin@lecortine.com.br`
- Senha: `admin123`

### 3. Testar Módulos Implementados

#### ✅ Estabelecimentos
```
http://localhost/agendapro/admin/estabelecimentos
```
- Criar novo estabelecimento
- Testar upload de logo
- Testar filtros

#### ✅ Agendamentos
```
http://localhost/agendapro/admin/agendamentos
```
- Criar novo agendamento
- Testar carregamento dinâmico de horários
- Testar cancelamento

### 4. Módulos Pendentes (sem views ainda)

Estes módulos têm Models e Controllers prontos, mas faltam as views:

- **Profissionais**: `http://localhost/agendapro/admin/profissionais`
- **Serviços**: `http://localhost/agendapro/admin/servicos`
- **Clientes**: `http://localhost/agendapro/admin/clientes`

**Resultado esperado:** Erro 404 ou tela em branco (normal, views não criadas ainda)

## 🐛 Possíveis Erros e Soluções

### Erro: "Table doesn't exist"
**Solução:** Importar o arquivo SQL novamente

### Erro: "Class not found"
**Solução:** Verificar se os arquivos dos Models estão em:
```
application/models/Estabelecimento_model.php
application/models/Agendamento_model.php
application/models/Profissional_model.php
application/models/Servico_model.php
application/models/Cliente_model.php
```

### Erro: "404 Not Found"
**Solução:** Verificar se as rotas estão corretas em:
```
application/config/routes.php
```

### Erro no Upload de Imagens
**Solução:** Criar diretórios com permissões:
```bash
mkdir uploads/logos
mkdir uploads/profissionais
mkdir uploads/clientes
chmod 755 uploads -R
```

## 📊 O Que Testar

### Estabelecimentos
- [x] Criar estabelecimento
- [x] Editar estabelecimento
- [x] Upload de logo
- [x] Filtros (busca, status, plano)
- [x] Deletar estabelecimento

### Agendamentos
- [x] Listar agendamentos
- [x] Criar agendamento
- [x] Seleção dinâmica de horários disponíveis
- [x] Cancelar agendamento
- [x] Finalizar agendamento
- [x] Filtros avançados

## 🎯 Próximos Passos Após Teste

Se tudo funcionar:
1. Criar views restantes (Profissionais, Serviços, Clientes)
2. Ou pular para integrações (Mercado Pago, WhatsApp)

Se houver erros:
1. Corrigir bugs encontrados
2. Ajustar Models/Controllers conforme necessário
