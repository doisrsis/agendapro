# 🚀 ROADMAP DE MELHORIAS - Sistema Le Cortine

**Autor:** Rafael Dias - doisr.com.br  
**Data:** 14/11/2024  
**Versão:** 1.0

---

## 📋 ANÁLISE DOS PEDIDOS DO CLIENTE

### ✅ **1. Escolha de Produto e Tecido (Filtro por Produto)**
**Status:** ⚠️ PARCIALMENTE IMPLEMENTADO  
**Complexidade:** 🟢 BAIXA  
**Prioridade:** 🔴 ALTA

**Situação Atual:**
- ✅ Sistema já carrega todos os tecidos na etapa 4
- ❌ Não filtra tecidos por produto específico
- ✅ Estrutura de banco permite relacionamento

**O que precisa:**
- Adicionar campo `produto_id` na tabela `tecidos` (ou criar tabela pivot `produto_tecido`)
- Modificar query no controller para filtrar tecidos por produto
- Atualizar interface admin para vincular tecidos aos produtos

**Tempo estimado:** 2-3 horas

---

### 🆕 **2. Integração de Correios (Cálculo de Frete)**
**Status:** ❌ NÃO IMPLEMENTADO  
**Complexidade:** 🟡 MÉDIA  
**Prioridade:** 🔴 ALTA

**Situação Atual:**
- ✅ Já captura CEP via ViaCEP
- ✅ Já tem campos de endereço completo
- ❌ Não calcula frete

**O que precisa:**
- **Credenciais Correios:** Contrato com Correios (PAC/SEDEX)
- **API:** Integração com Webservice dos Correios
- **Dados necessários:**
  - Código administrativo dos Correios
  - Senha do contrato
  - CEP de origem (loja)
  - Peso e dimensões dos produtos
- **Biblioteca:** Criar helper para calcular frete
- **Interface:** Mostrar opções de frete na etapa 8

**Tempo estimado:** 6-8 horas

**⚠️ ATENÇÃO:** Precisa fornecer:
1. Contrato dos Correios (código + senha)
2. CEP de origem da loja
3. Peso médio dos produtos
4. Dimensões da embalagem

---

### 🆕 **3. Integração Mercado Pago (Pagamento Online)**
**Status:** ❌ NÃO IMPLEMENTADO  
**Complexidade:** 🔴 ALTA  
**Prioridade:** 🟡 MÉDIA

**Situação Atual:**
- ❌ Sistema apenas gera orçamento
- ❌ Não processa pagamentos
- ✅ Já salva valores no banco

**O que precisa:**
- **Credenciais Mercado Pago:**
  - Public Key
  - Access Token
  - Client ID e Client Secret
- **SDK:** Mercado Pago SDK PHP
- **Implementações:**
  - Checkout Transparente ou Checkout Pro
  - Webhook para confirmação de pagamento
  - Atualização de status do orçamento
  - Página de sucesso/falha
- **Segurança:** Certificado SSL obrigatório

**Tempo estimado:** 12-16 horas

**⚠️ ATENÇÃO:** Precisa fornecer:
1. Conta Mercado Pago verificada
2. Credenciais de produção
3. Certificado SSL instalado no servidor
4. URL de callback/webhook

---

### 🆕 **4. Página de Consultoria com Vídeos**
**Status:** ⚠️ PARCIALMENTE IMPLEMENTADO  
**Complexidade:** 🟢 BAIXA  
**Prioridade:** 🟡 MÉDIA

**Situação Atual:**
- ✅ Página de consultoria existe (`orcamento/consultoria`)
- ❌ Não tem vídeos
- ❌ Não tem link de pagamento
- ❌ Não tem página de agradecimento

**O que precisa:**
- Upload dos vídeos (YouTube/Vimeo ou servidor)
- Link de pagamento da consultoria (Mercado Pago)
- Criar view de agradecimento
- Lógica de redirecionamento pós-pagamento

**Tempo estimado:** 3-4 horas

**⚠️ ATENÇÃO:** Precisa fornecer:
1. Vídeos (links ou arquivos)
2. Valor da consultoria
3. Texto/roteiro da página

---

### 🆕 **5. Opção de Retirada no Local (Sem Frete)**
**Status:** ❌ NÃO IMPLEMENTADO  
**Complexidade:** 🟢 BAIXA  
**Prioridade:** 🟢 BAIXA

**Situação Atual:**
- ✅ Sistema já tem etapa de endereço
- ❌ Não tem opção de retirada

**O que precisa:**
- Adicionar checkbox "Retirar no local" na etapa 8
- Condicional para não calcular frete
- Mostrar endereço da loja
- Salvar tipo de entrega no banco

**Tempo estimado:** 2 horas

---

## 📊 ORDEM DE EXECUÇÃO (DO MAIS SIMPLES AO MAIS COMPLEXO)

### 🥇 **FASE 1 - Melhorias Básicas (6-9 horas)**

#### 1.1 - Opção de Retirada no Local ⏱️ 2h
- ✅ Não precisa de credenciais
- ✅ Não precisa de API externa
- ✅ Alteração simples no formulário

**Arquivos a modificar:**
- `application/views/public/orcamento/etapa8.php`
- `application/controllers/Orcamento.php`
- `application/models/Orcamento_model.php`

---

#### 1.2 - Filtro de Tecidos por Produto ⏱️ 3h
- ✅ Não precisa de credenciais
- ✅ Usa estrutura existente
- ⚠️ Precisa ajustar banco de dados

**Arquivos a modificar:**
- `docs/EXECUTAR_ESTE.sql` (adicionar campo ou tabela)
- `application/models/Tecido_model.php`
- `application/controllers/Orcamento.php`
- `application/controllers/admin/Tecidos.php`
- `application/views/admin/tecidos/*`

---

#### 1.3 - Página de Consultoria com Vídeos ⏱️ 4h
- ⚠️ Precisa dos vídeos
- ⚠️ Precisa do valor da consultoria
- ✅ Estrutura básica já existe

**Arquivos a modificar:**
- `application/views/public/orcamento/consultoria.php`
- `application/controllers/Orcamento.php`
- Criar: `application/views/public/orcamento/agradecimento.php`

---

### 🥈 **FASE 2 - Integrações Médias (6-8 horas)**

#### 2.1 - Integração Correios (Frete) ⏱️ 8h
- ❌ **PRECISA DE CREDENCIAIS DOS CORREIOS**
- ❌ **PRECISA DE DADOS DOS PRODUTOS**

**Credenciais necessárias:**
```
- Código Administrativo: _______
- Senha do Contrato: _______
- CEP Origem: _______
- Peso médio produto: _______ kg
- Dimensões embalagem: L:___ x A:___ x C:___ cm
```

**Arquivos a criar:**
- `application/libraries/Correios.php`
- `application/config/correios.php`

**Arquivos a modificar:**
- `application/controllers/Orcamento.php`
- `application/views/public/orcamento/etapa8.php`
- `application/views/public/orcamento/resumo.php`

---

### 🥉 **FASE 3 - Integrações Complexas (12-16 horas)**

#### 3.1 - Integração Mercado Pago ⏱️ 16h
- ❌ **PRECISA DE CONTA MERCADO PAGO**
- ❌ **PRECISA DE SSL INSTALADO**
- ❌ **PRECISA DE CREDENCIAIS DE PRODUÇÃO**

**Credenciais necessárias:**
```
- Public Key: _______
- Access Token: _______
- Client ID: _______
- Client Secret: _______
```

**Arquivos a criar:**
- `application/libraries/MercadoPago.php`
- `application/config/mercadopago.php`
- `application/controllers/Pagamento.php`
- `application/views/public/pagamento/checkout.php`
- `application/views/public/pagamento/sucesso.php`
- `application/views/public/pagamento/falha.php`

**Arquivos a modificar:**
- `application/controllers/Orcamento.php`
- `application/models/Orcamento_model.php`
- `docs/EXECUTAR_ESTE.sql` (adicionar campos de pagamento)

---

## 📝 CHECKLIST DE INFORMAÇÕES NECESSÁRIAS

### ✅ Informações que JÁ TEMOS:
- [x] Estrutura do banco de dados
- [x] Sistema de orçamentos funcionando
- [x] Captura de CEP e endereço
- [x] Cálculo de valores
- [x] Página de consultoria básica

### ❌ Informações que PRECISAMOS:

#### Para Correios:
- [ ] Código administrativo dos Correios
- [ ] Senha do contrato
- [ ] CEP de origem (loja)
- [ ] Peso médio dos produtos (kg)
- [ ] Dimensões da embalagem (L x A x C em cm)
- [ ] Serviços desejados (PAC, SEDEX, ambos?)

#### Para Mercado Pago:
- [ ] Conta Mercado Pago criada e verificada
- [ ] Public Key (produção)
- [ ] Access Token (produção)
- [ ] Client ID
- [ ] Client Secret
- [ ] SSL instalado no domínio
- [ ] URL do webhook para callbacks

#### Para Consultoria:
- [ ] Link do vídeo 1 (apresentação)
- [ ] Link do vídeo 2 (agradecimento)
- [ ] Valor da consultoria (R$)
- [ ] Texto/roteiro da página
- [ ] Duração da consultoria (minutos)

#### Para Produtos:
- [ ] Quais tecidos são compatíveis com cada produto?
- [ ] Peso de cada produto (para frete)
- [ ] Dimensões de embalagem por produto

---

## 🎯 RECOMENDAÇÃO DE EXECUÇÃO

### **COMEÇAR POR:**

1. **Opção de Retirada no Local** (2h)
   - Simples, não precisa de nada externo
   - Melhora UX imediatamente

2. **Filtro de Tecidos por Produto** (3h)
   - Melhora lógica do sistema
   - Evita confusão do cliente

3. **Página de Consultoria com Vídeos** (4h)
   - Só precisa dos vídeos
   - Valoriza o serviço

### **DEPOIS (quando tiver credenciais):**

4. **Integração Correios** (8h)
   - Aguardando: credenciais + dados produtos

5. **Integração Mercado Pago** (16h)
   - Aguardando: conta MP + SSL + credenciais

---

## 💰 ESTIMATIVA TOTAL

- **Fase 1 (Básico):** 9 horas
- **Fase 2 (Correios):** 8 horas
- **Fase 3 (Mercado Pago):** 16 horas

**TOTAL:** ~33 horas de desenvolvimento

---

## ❓ PRÓXIMOS PASSOS

1. **Cliente fornecer informações faltantes**
2. **Definir prioridades com o cliente**
3. **Executar Fase 1 (não depende de nada)**
4. **Aguardar credenciais para Fases 2 e 3**

---

**Desenvolvido com ❤️ por Rafael Dias - doisr.com.br**
