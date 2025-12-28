# Integração WAHA - WhatsApp HTTP API

**AgendaPro - Sistema de Agendamentos**

**Autor:** Rafael Dias - doisr.com.br
**Data:** 28/12/2024

---

## 📋 Visão Geral

A integração com **WAHA (WhatsApp HTTP API)** permite que o sistema AgendaPro envie e receba mensagens via WhatsApp, possibilitando:

### Para o Super Admin (SaaS)
- Enviar notificações aos estabelecimentos (clientes do SaaS)
- Bot de suporte automatizado
- Notificações sobre planos e cobranças

### Para Estabelecimentos
- Enviar notificações aos clientes sobre agendamentos
- Bot de agendamento via WhatsApp
- Confirmações e lembretes automáticos

---

## 🚀 Instalação da WAHA

### Requisitos
- Docker instalado
- Porta 3000 disponível (ou outra de sua escolha)

### Instalação via Docker

```bash
# Versão gratuita (Core)
docker run -d \
  --name waha \
  -p 3000:3000 \
  devlikeapro/waha

# Versão Plus (recursos adicionais)
docker run -d \
  --name waha-plus \
  -p 3000:3000 \
  -e WAHA_API_KEY=sua-api-key-secreta \
  devlikeapro/waha-plus
```

### Verificar Instalação

Acesse: `http://localhost:3000/api/sessions/`

Se retornar um array (mesmo vazio), a instalação está correta.

---

## ⚙️ Configuração no AgendaPro

### 1. Executar SQL de Migração

Execute o arquivo `docs/sql_waha_integracao.sql` no phpMyAdmin ou via terminal:

```bash
mysql -u root -p dois8950_agendapro < docs/sql_waha_integracao.sql
```

### 2. Configurar Super Admin

1. Acesse: `/admin/configuracoes`
2. Clique na aba **WhatsApp (WAHA)**
3. Preencha:
   - **URL da API WAHA:** `http://localhost:3000` (ou sua URL)
   - **API Key:** Chave configurada no Docker (se houver)
   - **Nome da Sessão:** `saas_admin` (padrão)
   - **URL do Webhook:** `https://seudominio.com/webhook_waha`
4. Ative a integração
5. Clique em **Iniciar Sessão**
6. Escaneie o QR Code com seu WhatsApp

### 3. Configurar Estabelecimento

1. Acesse: `/painel/configuracoes`
2. Clique na aba **WhatsApp**
3. Selecione **WAHA API**
4. Preencha as credenciais
5. Clique em **Conectar**
6. Escaneie o QR Code

---

## 📁 Arquivos Criados/Modificados

### Novos Arquivos

| Arquivo | Descrição |
|---------|-----------|
| `application/libraries/Waha_lib.php` | Library de integração com a API WAHA |
| `application/controllers/Webhook_waha.php` | Controller para receber webhooks |
| `docs/sql_waha_integracao.sql` | SQL para criar campos e tabelas |

### Arquivos Modificados

| Arquivo | Modificação |
|---------|-------------|
| `application/controllers/admin/Configuracoes.php` | Adicionada aba WAHA e métodos de gerenciamento |
| `application/controllers/painel/Configuracoes.php` | Adicionados métodos WAHA para estabelecimentos |
| `application/models/Estabelecimento_model.php` | Adicionados campos WAHA no método update() |
| `application/views/admin/configuracoes/index.php` | Adicionada aba WhatsApp (WAHA) |
| `application/views/painel/configuracoes/index.php` | Adicionada seção WAHA na aba WhatsApp |

---

## 🔌 Endpoints da API WAHA Utilizados

### Sessões

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/sessions/` | Criar sessão |
| GET | `/api/sessions/{name}` | Obter sessão |
| POST | `/api/sessions/{name}/start` | Iniciar sessão |
| POST | `/api/sessions/{name}/stop` | Parar sessão |
| POST | `/api/sessions/{name}/logout` | Fazer logout |
| DELETE | `/api/sessions/{name}` | Deletar sessão |
| GET | `/api/{session}/auth/qr` | Obter QR Code |
| GET | `/api/sessions/{session}/me` | Obter info do número |

### Mensagens

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/sendText` | Enviar texto |
| POST | `/api/sendImage` | Enviar imagem |
| POST | `/api/sendFile` | Enviar arquivo |
| POST | `/api/sendVoice` | Enviar áudio |
| POST | `/api/sendLocation` | Enviar localização |
| POST | `/api/sendSeen` | Marcar como lido |
| POST | `/api/reaction` | Reagir a mensagem |

---

## 🔄 Webhooks

### URL do Webhook

- **SaaS Admin:** `https://seudominio.com/webhook_waha`
- **Estabelecimento:** `https://seudominio.com/webhook_waha/estabelecimento/{id}`

### Eventos Tratados

| Evento | Descrição |
|--------|-----------|
| `session.status` | Mudança de status da sessão |
| `message` | Nova mensagem recebida |
| `message.ack` | Confirmação de entrega/leitura |

---

## 🤖 Bot de Agendamento

### Comandos Disponíveis

| Comando | Ação |
|---------|------|
| `oi`, `olá`, `menu` | Exibe menu principal |
| `1`, `agendar` | Inicia agendamento |
| `2`, `meus agendamentos` | Lista agendamentos |
| `3`, `cancelar` | Inicia cancelamento |
| `0`, `sair` | Encerra atendimento |

### Fluxo de Agendamento

```
Cliente: oi
Bot: Menu principal com opções

Cliente: 1
Bot: Lista de serviços disponíveis

Cliente: [número do serviço]
Bot: Lista de profissionais (se mais de 1)

Cliente: [número do profissional]
Bot: Datas disponíveis

Cliente: [data]
Bot: Horários disponíveis

Cliente: [horário]
Bot: Confirmação do agendamento
```

---

## 📊 Tabelas do Banco de Dados

### Tabela `whatsapp_mensagens`

Log de todas as mensagens enviadas e recebidas.

```sql
CREATE TABLE whatsapp_mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT DEFAULT NULL,
    direcao ENUM('entrada', 'saida') NOT NULL,
    numero_destino VARCHAR(20) NOT NULL,
    tipo_mensagem ENUM('texto', 'imagem', 'audio', 'documento', 'localizacao'),
    conteudo TEXT NOT NULL,
    message_id VARCHAR(100),
    status ENUM('enviado', 'entregue', 'lido', 'erro', 'recebido'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabela `waha_sessoes`

Controle de sessões WAHA ativas.

```sql
CREATE TABLE waha_sessoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT DEFAULT NULL,
    session_name VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('stopped', 'starting', 'scan_qr', 'working', 'failed'),
    numero_conectado VARCHAR(20),
    push_name VARCHAR(100),
    qr_code_base64 TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔧 Uso da Library Waha_lib

### Exemplo: Enviar Mensagem para Cliente

```php
// Carregar library
$this->load->library('waha_lib');

// Configurar para estabelecimento
$estabelecimento = $this->Estabelecimento_model->get_by_id($id);
$this->waha_lib->set_estabelecimento($estabelecimento);

// Enviar mensagem
$resultado = $this->waha_lib->enviar_texto(
    '5511999999999',
    'Olá! Seu agendamento foi confirmado! ✅'
);

if ($resultado['success']) {
    echo 'Mensagem enviada!';
}
```

### Exemplo: Enviar Imagem

```php
$this->waha_lib->enviar_imagem(
    '5511999999999',
    'https://exemplo.com/imagem.jpg',
    'Legenda da imagem'
);
```

### Exemplo: Verificar Status da Conexão

```php
if ($this->waha_lib->esta_conectado()) {
    echo 'WhatsApp conectado!';
} else {
    echo 'Status: ' . $this->waha_lib->get_status();
}
```

---

## ⚠️ Observações Importantes

1. **Webhook em HTTPS:** Em produção, o webhook deve usar HTTPS
2. **Número de Telefone:** Sempre no formato internacional (5511999999999)
3. **Rate Limiting:** Evite enviar muitas mensagens em sequência
4. **Sessão Persistente:** A sessão WAHA persiste mesmo após reiniciar o container
5. **Backup:** Faça backup regular da pasta de dados do Docker

---

## 🐛 Troubleshooting

### QR Code não aparece
- Verifique se a URL da API está correta
- Verifique se a sessão foi iniciada
- Confira os logs do container Docker

### Mensagens não são enviadas
- Verifique se o status é "working"
- Confira se o número está no formato correto
- Verifique os logs em `application/logs/`

### Webhook não recebe eventos
- Verifique se a URL do webhook está acessível externamente
- Confira se o webhook foi configurado na criação da sessão
- Use ferramentas como webhook.site para testar

---

## 📚 Referências

- [Documentação WAHA](https://waha.devlike.pro/docs/)
- [GitHub WAHA](https://github.com/devlikeapro/waha)
- [Swagger WAHA](https://waha.devlike.pro/swagger/)
