# PRD - Página de Cadastro Externo de Estabelecimentos
**Autor:** Rafael Dias - doisr.com.br
**Data:** 25/01/2026
**Versão:** 1.0

---

## 📋 ÍNDICE
1. [Visão Geral](#visão-geral)
2. [Objetivos](#objetivos)
3. [Análise do Banco de Dados Atual](#análise-do-banco-de-dados-atual)
4. [Fluxo Proposto (Step-by-Step)](#fluxo-proposto-step-by-step)
5. [Especificação Técnica](#especificação-técnica)
6. [Wireframes e UX](#wireframes-e-ux)
7. [Validações e Regras de Negócio](#validações-e-regras-de-negócio)
8. [Integrações Necessárias](#integrações-necessárias)
9. [Segurança](#segurança)
10. [Pontos de Atenção](#pontos-de-atenção)

---

## 🎯 VISÃO GERAL

Criar uma página pública de cadastro (onboarding) para novos estabelecimentos se registrarem no AgendaPro sem necessidade de login prévio. O processo deve ser **intuitivo, guiado e completo**, coletando todos os dados necessários para o estabelecimento começar a usar o sistema imediatamente.

### Problema Atual
- Novos estabelecimentos precisam de intervenção manual para cadastro
- Processo demorado e não escalável
- Falta de autonomia para configuração inicial

### Solução Proposta
- Cadastro self-service em múltiplas etapas (wizard)
- Configuração completa de pagamentos e WhatsApp
- Onboarding guiado com instruções claras
- Ativação automática com período trial

---

## 🎯 OBJETIVOS

### Objetivos Primários
1. ✅ Permitir cadastro completo sem intervenção manual
2. ✅ Coletar todos os dados necessários do estabelecimento
3. ✅ Configurar método de pagamento (Mercado Pago ou PIX Manual)
4. ✅ Configurar WhatsApp Bot (WAHA)
5. ✅ Criar usuário administrador do estabelecimento
6. ✅ Ativar período trial automaticamente

### Objetivos Secundários
1. ✅ Experiência de usuário fluida e profissional
2. ✅ Reduzir fricção no processo de cadastro
3. ✅ Educar o usuário sobre recursos do sistema
4. ✅ Validar dados em tempo real
5. ✅ Enviar confirmação por email/WhatsApp

---

## 🗄️ ANÁLISE DO BANCO DE DADOS ATUAL

### Tabela: `estabelecimentos`

#### Campos Obrigatórios (NOT NULL)
- `nome` - Nome do estabelecimento

#### Campos Importantes para Cadastro
```sql
-- Dados Básicos
nome VARCHAR(200) NOT NULL
cnpj_cpf VARCHAR(18) UNIQUE
endereco TEXT
cep VARCHAR(9)
cidade VARCHAR(100)
estado VARCHAR(2)
telefone VARCHAR(20)
whatsapp VARCHAR(20)
email VARCHAR(100)
logo VARCHAR(255)

-- Plano e Status
plano_id INT(11) UNSIGNED
plano ENUM('trimestral','semestral','anual') DEFAULT 'trimestral'
plano_vencimento DATE
status ENUM('ativo','inativo','suspenso','cancelado') DEFAULT 'ativo'

-- Mercado Pago
mp_access_token_test VARCHAR(255)
mp_public_key_test VARCHAR(255)
mp_access_token_prod VARCHAR(255)
mp_public_key_prod VARCHAR(255)
mp_sandbox TINYINT(1) DEFAULT 1

-- PIX Manual
pagamento_tipo ENUM('mercadopago', 'pix_manual') DEFAULT 'mercadopago'
pix_chave VARCHAR(255)
pix_tipo_chave ENUM('cpf', 'cnpj', 'email', 'telefone', 'aleatoria')
pix_nome_recebedor VARCHAR(255)
pix_cidade VARCHAR(100)

-- WhatsApp Bot (WAHA)
evolution_api_url VARCHAR(255)
evolution_api_key VARCHAR(255)
evolution_instance_name VARCHAR(100)
whatsapp_numero VARCHAR(20)
whatsapp_conectado TINYINT(1) DEFAULT 0

-- Configurações
agendamento_requer_pagamento ENUM('nao', 'valor_total', 'taxa_fixa')
agendamento_taxa_fixa DECIMAL(10,2)
tempo_minimo_agendamento INT(11) DEFAULT 60
```

### Tabela: `usuarios`
```sql
email VARCHAR(255) NOT NULL UNIQUE
senha VARCHAR(255) NOT NULL
tipo ENUM('super_admin','estabelecimento','profissional') DEFAULT 'estabelecimento'
estabelecimento_id INT(11) UNSIGNED
nome VARCHAR(100) NOT NULL
telefone VARCHAR(20)
ativo TINYINT(1) DEFAULT 1
primeiro_acesso TINYINT(1) DEFAULT 1
```

### Tabela: `planos`
```sql
id INT(11) UNSIGNED
nome VARCHAR(100)
slug VARCHAR(50)
valor_mensal DECIMAL(10,2)
max_profissionais INT(11)
max_agendamentos_mes INT(11)
trial_dias INT(11) DEFAULT 7
```

---

## 🚀 FLUXO PROPOSTO (STEP-BY-STEP)

### **RECOMENDAÇÃO: 6 ETAPAS + CONFIRMAÇÃO**

### **ETAPA 1: BEM-VINDO 🎉**
**Objetivo:** Apresentar o sistema e criar expectativa positiva

**Conteúdo:**
- Logo do AgendaPro
- Título: "Bem-vindo ao AgendaPro!"
- Subtítulo: "Configure seu estabelecimento em poucos minutos"
- Barra de progresso: 1/6
- Lista de benefícios:
  - ✅ 7 dias grátis para testar
  - ✅ Agendamentos via WhatsApp automatizados
  - ✅ Pagamentos online integrados
  - ✅ Sem taxa de setup
- Botão: "Começar Cadastro"

**Por que essa etapa?**
- Reduz ansiedade do usuário
- Estabelece expectativas claras
- Aumenta taxa de conclusão

---

### **ETAPA 2: DADOS DO ESTABELECIMENTO 🏢**
**Objetivo:** Coletar informações básicas

**Campos:**
```
Nome do Estabelecimento * (obrigatório)
CNPJ ou CPF * (obrigatório, com validação)
Telefone * (obrigatório, formato: (XX) XXXXX-XXXX)
WhatsApp * (obrigatório, pode ser o mesmo do telefone)
Email * (obrigatório, validação de formato)

CEP * (obrigatório, busca automática de endereço via API ViaCEP)
Endereço * (preenchido automaticamente)
Número
Complemento
Cidade * (preenchido automaticamente)
Estado * (preenchido automaticamente)
```

**Validações:**
- CNPJ/CPF válido e único no sistema
- Email válido e único
- CEP válido (buscar endereço automaticamente)
- WhatsApp no formato correto

**Botões:**
- "Voltar"
- "Próximo"

---

### **ETAPA 3: ESCOLHA SEU PLANO 💎**
**Objetivo:** Selecionar plano e configurar trial

**Conteúdo:**
- Cards dos planos disponíveis (buscar da tabela `planos`)
- Destacar: "7 dias grátis em todos os planos"
- Mostrar recursos de cada plano
- Preço mensal destacado
- Plano recomendado (badge "Mais Popular")

**Campos:**
```
[ ] Autônomo - R$ 29,90/mês
    - 1 profissional
    - 100 agendamentos/mês
    - WhatsApp + Mercado Pago

[ ] Básico - R$ 79,90/mês (RECOMENDADO)
    - 3 profissionais
    - 300 agendamentos/mês
    - Todos os recursos

[ ] Profissional - R$ 149,90/mês
    - 10 profissionais
    - 1000 agendamentos/mês
    - Relatórios avançados

[ ] Premium - R$ 299,90/mês
    - Ilimitado
    - Suporte prioritário
```

**Informação adicional:**
- "Você pode mudar de plano a qualquer momento"
- "Período trial: 7 dias grátis"
- "Não cobramos cartão de crédito agora"

**Botões:**
- "Voltar"
- "Próximo"

---

### **ETAPA 4: CONFIGURAR PAGAMENTOS 💳**
**Objetivo:** Definir como o estabelecimento receberá pagamentos

**Seleção de Método:**
```
Como você quer receber pagamentos dos seus clientes?

( ) Mercado Pago (Recomendado)
    ✅ Pagamentos automáticos
    ✅ QR Code gerado automaticamente
    ✅ Confirmação instantânea via webhook
    ⚠️ Requer conta no Mercado Pago

( ) PIX Manual
    ✅ Use sua própria chave PIX
    ✅ Sem taxas de integração
    ⚠️ Confirmação manual de pagamentos
```

#### **SE ESCOLHER: Mercado Pago**

**Campos:**
```
Ambiente:
( ) Sandbox (Teste) - Recomendado para começar
( ) Produção

--- Credenciais de Teste ---
Access Token (Test) *
Public Key (Test) *

--- Credenciais de Produção ---
Access Token (Prod)
Public Key (Prod)
```

**Instruções (expandível):**
```
📖 Como obter as credenciais do Mercado Pago?

1. Acesse: https://www.mercadopago.com.br/developers/panel
2. Faça login na sua conta Mercado Pago
3. Vá em "Suas integrações" > "Credenciais"
4. Copie o "Access Token" e "Public Key"
5. Cole aqui nos campos correspondentes

🎥 [Ver vídeo tutorial]
📄 [Documentação completa]
```

**Validação:**
- Testar credenciais ao clicar em "Próximo"
- Mostrar feedback: "✅ Credenciais válidas" ou "❌ Credenciais inválidas"

#### **SE ESCOLHER: PIX Manual**

**Campos:**
```
Tipo de Chave PIX *
( ) CPF
( ) CNPJ
( ) Email
( ) Telefone
( ) Chave Aleatória

Chave PIX * (validar formato conforme tipo)
Nome do Recebedor * (aparecerá no PIX)
Cidade * (obrigatório no padrão PIX)
```

**Validações:**
- Formato da chave conforme tipo selecionado
- CPF/CNPJ válido se aplicável
- Email válido se aplicável
- Telefone válido se aplicável

**Botões:**
- "Voltar"
- "Próximo"

---

### **ETAPA 5: WHATSAPP BOT 📱**
**Objetivo:** Configurar integração com WAHA para automação

**Conteúdo:**
```
Configure o WhatsApp Bot para automatizar seus agendamentos

Número do WhatsApp * (mesmo da etapa 2 ou diferente)
```

**Instruções:**
```
🤖 Como funciona?

1. Usamos a plataforma WAHA para conectar seu WhatsApp
2. Você receberá um QR Code para escanear
3. Após conectar, o bot responderá automaticamente seus clientes
4. Clientes podem agendar, reagendar e confirmar via WhatsApp

⚠️ IMPORTANTE:
- Use um número exclusivo para o bot (não use seu WhatsApp pessoal)
- O número precisa ter WhatsApp ativo
- Recomendamos um chip dedicado para o negócio
```

**Opções:**
```
[ ] Configurar agora (Recomendado)
    → Você receberá o QR Code na próxima tela

[ ] Configurar depois
    → Você pode configurar no painel administrativo
```

**Se escolher "Configurar agora":**
- Próxima etapa mostrará QR Code
- Aguardar conexão (polling)
- Mostrar status: "Aguardando leitura do QR Code..."

**Se escolher "Configurar depois":**
- Pular para Etapa 6

**Botões:**
- "Voltar"
- "Próximo"

---

### **ETAPA 5.1: CONECTAR WHATSAPP (CONDICIONAL) 📲**
**Objetivo:** Escanear QR Code e conectar WhatsApp

**Conteúdo:**
```
Escaneie o QR Code com seu WhatsApp

[QR CODE GRANDE]

Status: 🔄 Aguardando conexão...

📱 Como escanear:
1. Abra o WhatsApp no celular
2. Toque em "Mais opções" (⋮) > "Aparelhos conectados"
3. Toque em "Conectar um aparelho"
4. Aponte a câmera para o QR Code acima

⏱️ O QR Code expira em: 02:00
```

**Comportamento:**
- Polling a cada 3 segundos para verificar conexão
- Quando conectar: ✅ "WhatsApp conectado com sucesso!"
- Botão "Próximo" só fica habilitado após conexão
- Opção: "Pular esta etapa" (desabilita bot temporariamente)

**Botões:**
- "Voltar"
- "Pular esta etapa"
- "Próximo" (habilitado após conexão)

---

### **ETAPA 6: CRIAR SUA CONTA 👤**
**Objetivo:** Criar usuário administrador do estabelecimento

**Conteúdo:**
```
Última etapa! Crie sua conta de acesso

Nome Completo *
Email * (mesmo da etapa 2 ou diferente)
Senha * (mínimo 8 caracteres)
Confirmar Senha *

[ ] Li e aceito os Termos de Uso e Política de Privacidade *
[ ] Aceito receber novidades e atualizações por email
```

**Validações:**
- Senha forte (mínimo 8 caracteres, 1 maiúscula, 1 número)
- Senhas coincidem
- Email único no sistema
- Termos aceitos (obrigatório)

**Botões:**
- "Voltar"
- "Finalizar Cadastro"

---

### **ETAPA 7: CONFIRMAÇÃO E PRÓXIMOS PASSOS ✅**
**Objetivo:** Confirmar cadastro e orientar próximos passos

**Conteúdo:**
```
🎉 Parabéns! Seu cadastro foi concluído com sucesso!

✅ Estabelecimento cadastrado
✅ Plano ativado (7 dias grátis)
✅ Pagamentos configurados
✅ WhatsApp conectado (se aplicável)
✅ Conta criada

📧 Enviamos um email de confirmação para: [email]

🚀 Próximos passos:

1. Cadastrar seus profissionais
2. Adicionar seus serviços
3. Configurar horários de atendimento
4. Fazer seu primeiro agendamento

[Botão: Acessar Painel Administrativo]
```

**Ações automáticas:**
1. Criar registro em `estabelecimentos`
2. Criar registro em `usuarios` (tipo: 'estabelecimento')
3. Vincular usuário ao estabelecimento
4. Definir `plano_vencimento` = hoje + trial_dias
5. Enviar email de boas-vindas
6. Enviar mensagem WhatsApp de boas-vindas (se conectado)
7. Redirecionar para login ou dashboard

---

## 💻 ESPECIFICAÇÃO TÉCNICA

### Estrutura de Arquivos

```
application/
├── controllers/
│   └── Cadastro_externo.php (novo)
├── models/
│   ├── Estabelecimento_model.php (existente, adicionar métodos)
│   ├── Usuario_model.php (existente, adicionar métodos)
│   └── Plano_model.php (existente)
├── views/
│   └── cadastro_externo/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── step1_boas_vindas.php
│       ├── step2_dados_estabelecimento.php
│       ├── step3_escolher_plano.php
│       ├── step4_configurar_pagamentos.php
│       ├── step5_whatsapp_bot.php
│       ├── step5_1_conectar_whatsapp.php
│       ├── step6_criar_conta.php
│       └── step7_confirmacao.php
└── libraries/
    └── Cadastro_wizard_lib.php (novo, gerenciar sessão do wizard)
```

### Controller: `Cadastro_externo.php`

```php
class Cadastro_externo extends CI_Controller {

    public function index() {
        // Redireciona para step 1
    }

    public function step1() {
        // Boas-vindas
    }

    public function step2() {
        // Dados do estabelecimento
        // POST: salvar em sessão e validar
    }

    public function step3() {
        // Escolher plano
        // Buscar planos ativos do banco
    }

    public function step4() {
        // Configurar pagamentos
        // Validar credenciais MP se aplicável
    }

    public function step5() {
        // WhatsApp bot
    }

    public function step5_conectar() {
        // Gerar QR Code WAHA
        // Polling para verificar conexão
    }

    public function step6() {
        // Criar conta
    }

    public function finalizar() {
        // Processar tudo e criar registros
        // Enviar emails
        // Redirecionar
    }

    public function verificar_cnpj() {
        // AJAX: verificar se CNPJ já existe
    }

    public function verificar_email() {
        // AJAX: verificar se email já existe
    }

    public function buscar_cep() {
        // AJAX: buscar endereço via ViaCEP
    }

    public function validar_credenciais_mp() {
        // AJAX: testar credenciais Mercado Pago
    }

    public function verificar_conexao_whatsapp() {
        // AJAX: polling para verificar se WhatsApp conectou
    }
}
```

### Library: `Cadastro_wizard_lib.php`

```php
class Cadastro_wizard_lib {

    private $session_key = 'cadastro_wizard_data';

    public function salvar_step($step, $dados) {
        // Salvar dados do step na sessão
    }

    public function obter_step($step) {
        // Recuperar dados de um step
    }

    public function obter_todos_dados() {
        // Recuperar todos os dados do wizard
    }

    public function limpar_sessao() {
        // Limpar dados após finalizar
    }

    public function validar_step($step, $dados) {
        // Validações específicas de cada step
    }

    public function step_atual() {
        // Retornar step atual baseado na sessão
    }

    public function pode_acessar_step($step) {
        // Verificar se steps anteriores foram completados
    }
}
```

---

## 🎨 WIREFRAMES E UX

### Design Geral
- Layout limpo e moderno
- Barra de progresso sempre visível no topo
- Máximo de 1 coluna (mobile-first)
- Campos grandes e espaçados
- Botões destacados (CTA)
- Validação em tempo real com feedback visual
- Ícones para facilitar compreensão
- Tooltips para ajuda contextual

### Paleta de Cores Sugerida
- Primária: #4F46E5 (Indigo) - Botões principais
- Sucesso: #10B981 (Green) - Validações OK
- Erro: #EF4444 (Red) - Erros de validação
- Aviso: #F59E0B (Amber) - Alertas
- Neutro: #6B7280 (Gray) - Textos secundários
- Fundo: #F9FAFB (Light Gray)

### Componentes Reutilizáveis
1. **Barra de Progresso**
   - 6 steps
   - Step atual destacado
   - Steps completados com ✓

2. **Card de Step**
   - Título grande
   - Subtítulo explicativo
   - Ícone temático
   - Formulário centralizado

3. **Botões de Navegação**
   - "Voltar" (secundário, esquerda)
   - "Próximo" (primário, direita)
   - "Finalizar" (destaque, step 6)

4. **Feedback Visual**
   - Loading spinners
   - Mensagens de sucesso/erro
   - Validação inline nos campos

---

## ✅ VALIDAÇÕES E REGRAS DE NEGÓCIO

### Step 2: Dados do Estabelecimento
```javascript
// CNPJ/CPF
- Formato válido
- Dígitos verificadores corretos
- Único no banco (AJAX)

// Email
- Formato válido (regex)
- Único no banco (AJAX)

// CEP
- 8 dígitos
- Buscar endereço via ViaCEP (AJAX)
- Preencher automaticamente: endereço, cidade, estado

// Telefone/WhatsApp
- Formato: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
- Apenas números
```

### Step 4: Pagamentos

#### Mercado Pago
```javascript
// Validar credenciais (AJAX)
- Fazer requisição teste para API do MP
- Verificar se tokens são válidos
- Mostrar feedback: ✅ ou ❌
```

#### PIX Manual
```javascript
// Validar chave conforme tipo
- CPF: 11 dígitos, validar dígitos
- CNPJ: 14 dígitos, validar dígitos
- Email: formato válido
- Telefone: formato válido
- Aleatória: 32 caracteres alfanuméricos
```

### Step 6: Criar Conta
```javascript
// Senha
- Mínimo 8 caracteres
- Pelo menos 1 maiúscula
- Pelo menos 1 número
- Pelo menos 1 caractere especial (opcional mas recomendado)

// Email
- Único no banco (AJAX)
- Formato válido

// Termos
- Checkbox obrigatório
```

---

## 🔌 INTEGRAÇÕES NECESSÁRIAS

### 1. ViaCEP (Busca de Endereço)
```javascript
// API Pública
URL: https://viacep.com.br/ws/{cep}/json/

// Exemplo
fetch('https://viacep.com.br/ws/45490000/json/')
  .then(response => response.json())
  .then(data => {
    // Preencher campos automaticamente
    endereco.value = data.logradouro;
    cidade.value = data.localidade;
    estado.value = data.uf;
  });
```

### 2. Mercado Pago (Validação de Credenciais)
```php
// Testar credenciais
$mp = new MercadoPago\SDK();
$mp->setAccessToken($access_token);

try {
    $payment_methods = $mp->get("/v1/payment_methods");
    return ['valid' => true];
} catch (Exception $e) {
    return ['valid' => false, 'error' => $e->getMessage()];
}
```

### 3. WAHA (WhatsApp Bot)
```php
// Gerar QR Code
POST https://waha.doisr.com.br/api/sessions/start
Headers: X-Api-Key: {api_key}
Body: {
    "name": "{instance_name}",
    "config": {}
}

// Resposta
{
    "qr": "data:image/png;base64,...",
    "status": "SCAN_QR_CODE"
}

// Verificar status (polling)
GET https://waha.doisr.com.br/api/sessions/{instance_name}
Headers: X-Api-Key: {api_key}

// Resposta quando conectado
{
    "status": "WORKING",
    "me": {
        "id": "5575988890006@c.us",
        "pushName": "Estabelecimento"
    }
}
```

### 4. Email (Confirmação e Boas-vindas)
```php
// Template de email
Assunto: Bem-vindo ao AgendaPro! 🎉

Olá {nome_estabelecimento},

Seu cadastro foi concluído com sucesso!

Dados do seu plano:
- Plano: {plano_nome}
- Período trial: 7 dias grátis
- Vencimento trial: {data_vencimento}

Acesse seu painel: {url_painel}
Email: {email}

Próximos passos:
1. Cadastrar profissionais
2. Adicionar serviços
3. Configurar horários

Dúvidas? Responda este email ou acesse nossa central de ajuda.

Equipe AgendaPro
```

---

## 🔒 SEGURANÇA

### Proteção contra Spam/Bots
```php
// Implementar:
1. Google reCAPTCHA v3 (invisível)
   - Validar score > 0.5
   - Bloquear se score < 0.3

2. Rate Limiting
   - Máximo 3 tentativas por IP em 1 hora
   - Bloquear temporariamente após limite

3. Honeypot Field
   - Campo oculto que humanos não preenchem
   - Se preenchido = bot

4. Token CSRF
   - Gerar token único por sessão
   - Validar em cada POST
```

### Validação de Dados
```php
// Sanitização
- Remover tags HTML (strip_tags)
- Escapar caracteres especiais
- Validar tipos de dados
- Limitar tamanho de strings

// Senhas
- Hash com password_hash() (bcrypt)
- Nunca armazenar em plain text
- Validar força da senha
```

### Proteção de Credenciais
```php
// Mercado Pago / WAHA
- Criptografar tokens antes de salvar no banco
- Usar openssl_encrypt() / openssl_decrypt()
- Chave de criptografia em .env
- Nunca expor em logs
```

---

## ⚠️ PONTOS DE ATENÇÃO

### 1. Experiência do Usuário
- ✅ **Salvar progresso:** Se usuário sair e voltar, manter dados preenchidos (sessão)
- ✅ **Validação em tempo real:** Feedback imediato, não só ao enviar
- ✅ **Mobile-first:** Maioria dos usuários acessará pelo celular
- ✅ **Loading states:** Sempre mostrar quando algo está processando
- ✅ **Mensagens claras:** Erros específicos, não genéricos

### 2. Conversão
- ✅ **Reduzir fricção:** Pedir apenas o essencial em cada step
- ✅ **Opções de "pular":** Permitir configurar depois (WhatsApp, por exemplo)
- ✅ **Trial sem cartão:** Não pedir cartão de crédito no cadastro
- ✅ **Destacar benefícios:** Lembrar o usuário do valor em cada step

### 3. Técnico
- ✅ **Transações:** Usar transações no banco ao finalizar (rollback se erro)
- ✅ **Logs:** Registrar cada step completado para debug
- ✅ **Retry:** Se integração falhar (MP, WAHA), permitir tentar novamente
- ✅ **Timeout:** Limitar tempo de sessão do wizard (ex: 1 hora)

### 4. Pós-Cadastro
- ✅ **Onboarding:** Criar tour guiado no primeiro acesso ao painel
- ✅ **Email drip:** Série de emails educativos durante trial
- ✅ **Suporte proativo:** Monitorar estabelecimentos que não completam setup

---

## 📊 MÉTRICAS DE SUCESSO

### KPIs para Monitorar
1. **Taxa de Conclusão:** % de usuários que completam todas as etapas
2. **Tempo Médio:** Quanto tempo leva para completar cadastro
3. **Drop-off por Step:** Onde usuários abandonam o processo
4. **Taxa de Ativação:** % que fazem primeiro agendamento em 7 dias
5. **Conversão Trial → Pago:** % que assinam após trial

### Metas Sugeridas
- Taxa de conclusão: > 70%
- Tempo médio: < 10 minutos
- Taxa de ativação: > 50%
- Conversão trial → pago: > 30%

---

## 🚀 ROADMAP DE IMPLEMENTAÇÃO

### Fase 1: MVP (Semana 1-2)
- [ ] Criar estrutura de controllers/views
- [ ] Implementar Steps 1-6 (sem WhatsApp)
- [ ] Validações básicas
- [ ] Integração ViaCEP
- [ ] Salvar em banco
- [ ] Email de confirmação

### Fase 2: Integrações (Semana 3)
- [ ] Integração Mercado Pago (validação)
- [ ] Integração WAHA (QR Code)
- [ ] Validações avançadas (AJAX)
- [ ] reCAPTCHA
- [ ] Rate limiting

### Fase 3: UX/Polish (Semana 4)
- [ ] Design responsivo
- [ ] Animações e transições
- [ ] Loading states
- [ ] Mensagens de erro amigáveis
- [ ] Testes de usabilidade

### Fase 4: Otimização (Semana 5)
- [ ] Testes A/B
- [ ] Analytics
- [ ] Ajustes baseados em métricas
- [ ] Documentação

---

## 💡 SUGESTÕES ADICIONAIS

### 1. **Vídeo Tutorial Curto**
- Criar vídeo de 2-3 minutos mostrando o processo
- Incorporar no Step 1
- Aumenta confiança e reduz dúvidas

### 2. **Chat de Suporte**
- Adicionar widget de chat (Tawk.to, Crisp, etc.)
- Suporte em tempo real durante cadastro
- Reduz abandono

### 3. **Social Proof**
- Mostrar número de estabelecimentos cadastrados
- Depoimentos de clientes
- Logos de clientes conhecidos

### 4. **Gamificação**
- Barra de progresso com % de conclusão
- Badges ao completar steps
- Mensagens motivacionais

### 5. **Recuperação de Cadastro Abandonado**
- Salvar email no Step 2
- Enviar email lembrando de completar cadastro
- Link direto para continuar de onde parou

### 6. **Opção de Agendar Demo**
- Para quem tem dúvidas
- Botão "Falar com Especialista"
- Agendar call de 15 minutos

---

## 📝 CHECKLIST FINAL

Antes de lançar, verificar:

### Funcional
- [ ] Todos os steps funcionam
- [ ] Validações funcionam
- [ ] Integrações funcionam
- [ ] Emails são enviados
- [ ] Dados salvos corretamente
- [ ] Redirecionamento funciona

### UX
- [ ] Responsivo (mobile/tablet/desktop)
- [ ] Loading states em todas as ações
- [ ] Mensagens de erro claras
- [ ] Barra de progresso atualiza
- [ ] Botões desabilitados quando necessário

### Segurança
- [ ] reCAPTCHA implementado
- [ ] Rate limiting ativo
- [ ] CSRF tokens validados
- [ ] Senhas hasheadas
- [ ] Credenciais criptografadas
- [ ] SQL injection prevenido

### Performance
- [ ] Página carrega em < 3 segundos
- [ ] AJAX requests otimizadas
- [ ] Imagens otimizadas
- [ ] CSS/JS minificados

### Analytics
- [ ] Google Analytics configurado
- [ ] Eventos de cada step rastreados
- [ ] Funil de conversão configurado
- [ ] Metas definidas

---

## 🎯 CONCLUSÃO

Este PRD propõe um **fluxo de cadastro completo e profissional** que:

✅ **Reduz fricção:** 6 steps claros e objetivos
✅ **Educa o usuário:** Instruções e links de ajuda em cada etapa
✅ **Coleta tudo:** Todos os dados necessários para começar
✅ **Configura tudo:** Pagamentos e WhatsApp prontos para usar
✅ **Converte melhor:** UX otimizada para maximizar conclusão

**Próximos passos:**
1. Revisar e aprovar este PRD
2. Criar wireframes detalhados (Figma)
3. Iniciar desenvolvimento (Fase 1 do Roadmap)
4. Testes com usuários beta
5. Lançamento gradual

---

**Dúvidas ou sugestões? Vamos discutir antes de implementar! 🚀**
