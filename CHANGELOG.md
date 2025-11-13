# 📋 Changelog - Sistema de Orçamento Le Cortine

**Autor:** Rafael Dias - [doisr.com.br](https://doisr.com.br)

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

---

## [1.0.0] - 2024-11-13

### 🎉 Release Inicial - Painel Administrativo Completo

#### ✅ Adicionado

**Estrutura Base:**
- Sistema de autenticação com sessões seguras
- Middleware de verificação de login
- Layout administrativo com Tabler Dashboard
- Dashboard com estatísticas e gráficos
- Sistema de logs de ações
- Configurações globais do sistema

**CRUD de Categorias:**
- Listar categorias com filtros
- Criar, editar e deletar categorias
- Upload de imagem de categoria
- Reordenação via drag & drop
- Toggle de status via AJAX
- Geração automática de slug

**CRUD de Produtos:**
- Listar produtos com filtros (categoria, status, busca)
- Criar, editar e deletar produtos
- Upload de imagem principal
- Galeria de imagens com múltiplos uploads
- Reordenação de galeria via drag & drop
- Exclusão de imagens via AJAX
- Toggle de status e destaque
- Campos: nome, categoria, descrição, preço, características
- SEO: meta title, description, keywords

**CRUD de Coleções:**
- Listar coleções de tecidos
- Criar, editar e deletar coleções
- Upload de imagem de coleção
- Contador de tecidos por coleção
- Toggle de status via AJAX
- Geração automática de slug

**CRUD de Tecidos:**
- Listar tecidos com filtros (coleção, status, busca)
- Criar, editar e deletar tecidos
- Upload de imagem de tecido
- Gerenciamento de cores via modal
- Adicionar cores com nome, código hex e imagem
- Reordenação de cores via drag & drop
- Exclusão de cores via AJAX
- Campos: nome, código, coleção, composição, largura, tipo
- Toggle de status via AJAX

**Funcionalidades Gerais:**
- Upload de imagens com validação
- Preview de imagens antes do upload
- Drag & drop para reordenação (SortableJS)
- Confirmações com SweetAlert2
- Validações frontend e backend
- Mensagens flash de sucesso/erro
- Layout 100% responsivo (PC/Tablet/Mobile)
- Tema claro forçado para melhor legibilidade

**Documentação:**
- README.md completo
- ROADMAP.md detalhado
- PROGRESSO.md com status atual
- SQL de instalação (EXECUTAR_ESTE.sql)
- SQL de dados de teste (DADOS_TESTE.sql)
- Instruções de teste (INSTRUCOES_TESTE.md)

#### 🔧 Tecnologias Utilizadas

- **Backend:** PHP 7.4+ com CodeIgniter 3
- **Banco de Dados:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript ES6
- **Framework CSS:** Bootstrap 5 (via Tabler)
- **Template Admin:** Tabler Dashboard
- **Bibliotecas JS:**
  - jQuery 3.x
  - SortableJS (drag & drop)
  - SweetAlert2 (modais)
  - Chart.js (gráficos)

#### 📊 Estatísticas

- **Arquivos Criados:** 50+
- **Linhas de Código:** 8.000+
- **Models:** 7 (Usuario, Cliente, Categoria, Produto, Colecao, Tecido, Orcamento)
- **Controllers:** 6 (Auth, Home, Dashboard, Categorias, Produtos, Colecoes, Tecidos)
- **Views:** 15+ (Login, Dashboard, CRUDs completos)
- **Tabelas BD:** 16

#### 🎯 Progresso do Projeto

- ✅ Estrutura Base: 100%
- ✅ Layout Admin: 100%
- ✅ Dashboard: 100%
- ✅ CRUD Categorias: 100%
- ✅ CRUD Produtos: 100%
- ✅ CRUD Coleções: 100%
- ✅ CRUD Tecidos: 100%
- ⏳ Formulário Público: 0%
- ⏳ Integrações: 0%

**Progresso Geral: ~75%**

#### 🔐 Credenciais Padrão

- **Email:** admin@lecortine.com.br
- **Senha:** admin123

#### 📝 Notas

- Primeira versão estável do painel administrativo
- Todos os CRUDs principais implementados
- Sistema pronto para testes e uso em desenvolvimento
- Próxima etapa: Formulário público de orçamento

---

## [1.1.0] - 2024-11-13

### ✨ Adicionado

**CRUD de Extras:**
- Model `Extra_model` completo com métodos CRUD
- Controller `admin/Extras` com todas as operações
- Listagem de extras com filtros (busca, tipo, status)
- Formulário criar/editar extras
- 3 tipos de preço: fixo, percentual, por m²
- Seleção de produtos aplicáveis (JSON)
- Toggle status via AJAX
- Reordenação drag & drop
- Deletar com confirmação SweetAlert2
- Validações frontend e backend

**CRUD de Preços:**
- Model `Preco_model` com cálculo automático
- Controller `admin/Precos` completo
- Listagem de preços com filtros
- Formulário criar/editar preços
- Faixas de dimensões (largura x altura)
- 3 tipos de preço: por m², por ml, fixo
- Método de cálculo automático de preços
- Deletar com confirmação
- Validações completas

**Melhorias Gerais:**
- Menu atualizado com links de Extras e Preços
- Documentação atualizada (PROGRESSO.md)
- Código otimizado e comentado

### 📊 Estatísticas

- **Models:** 9 (+ Extra_model, Preco_model)
- **Controllers:** 8 (+ Extras, Precos)
- **Views:** 19+ (+ 4 views de Extras e Preços)
- **Progresso Geral:** ~80%

---

## [Unreleased]

### 🚀 Próximas Funcionalidades

**Versão 1.1.0 (Planejada):**
- CRUD de Extras
- CRUD de Preços
- Gerenciamento de Configurações
- Perfil de usuário

**Versão 1.2.0 (Planejada):**
- Formulário público de orçamento (multi-step)
- Cálculo automático de preços
- Seleção de produtos e tecidos
- Captura de dados do cliente

**Versão 1.3.0 (Planejada):**
- Gerenciamento de orçamentos recebidos
- Edição de status de orçamentos
- Visualização detalhada
- Filtros e busca avançada

**Versão 2.0.0 (Planejada):**
- Integração WhatsApp API
- Integração Email SMTP
- Geração de PDF de orçamentos
- Envio automático de orçamentos

**Versão 2.1.0 (Planejada):**
- Otimizações de performance
- Otimizações de SEO
- Melhorias de segurança
- Melhorias de acessibilidade

---

## 📌 Tipos de Mudanças

- **Adicionado** - para novas funcionalidades
- **Modificado** - para mudanças em funcionalidades existentes
- **Descontinuado** - para funcionalidades que serão removidas
- **Removido** - para funcionalidades removidas
- **Corrigido** - para correção de bugs
- **Segurança** - para vulnerabilidades corrigidas

---

## 🔗 Links

- **Repositório:** https://github.com/doisrsis/orcamento_lecortine
- **Autor:** https://doisr.com.br
- **Issues:** https://github.com/doisrsis/orcamento_lecortine/issues
- **Releases:** https://github.com/doisrsis/orcamento_lecortine/releases

---

**Desenvolvido com ❤️ por Rafael Dias**
