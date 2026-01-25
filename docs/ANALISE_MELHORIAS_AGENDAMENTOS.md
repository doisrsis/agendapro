# 📋 ANÁLISE: MELHORIAS NA PÁGINA DE AGENDAMENTOS

**Autor:** Rafael Dias - doisr.com.br
**Data:** 24/01/2026 16:52
**Status:** ⏳ AGUARDANDO APROVAÇÃO

---

## 🎯 REQUISITOS SOLICITADOS

### 1. Filtro Padrão
**Requisito:** Mostrar apenas **confirmados** e **pendentes** por padrão. Finalizados, cancelados e não compareceu apenas quando filtrar.

**Status Atual:** ❌ NÃO IMPLEMENTADO
- Filtro atual mostra "Todos" por padrão
- Localização: `application/views/admin/agendamentos/_rapida.php` linha 29

**Implementação Necessária:**
- Alterar valor padrão do filtro de status
- Adicionar lógica no controller para aplicar filtro padrão quando não especificado

---

### 2. Botão "Finalizar Atendimento"

**Requisito:** Ao clicar em "Finalizar Atendimento":
1. Mudar status para `finalizado`
2. Marcar como pago (`pagamento_status = 'pago'`)
3. **Se** `forma_pagamento = 'pix'` **E** estabelecimento usa PIX Manual:
   - Mudar `forma_pagamento` para `pix_manual`
4. **Se** `forma_pagamento = 'pix'` **E** estabelecimento usa Mercado Pago:
   - Apenas mudar status para `finalizado` (não mexer em pagamento)

**Status Atual:** ⚠️ PARCIALMENTE IMPLEMENTADO

**Código Existente:**
- ✅ Método `finalizar_rapido()` existe em `Agendamentos.php` (linhas 441-483)
- ❌ Não tem lógica de pagamento
- ❌ Não verifica tipo de PIX do estabelecimento

**Código Atual:**
```php
// application/controllers/painel/Agendamentos.php:464-467
$resultado = $this->Agendamento_model->update($id, [
    'status' => 'finalizado',
    'hora_fim_real' => date('H:i:s')
]);
```

**Implementação Necessária:**
1. Carregar dados do estabelecimento
2. Verificar `pagamento_tipo` do estabelecimento
3. Aplicar lógica condicional de pagamento

---

### 3. Botão "Confirmar Pagamento PIX"

**Requisito:**
- Botão para confirmar recebimento do PIX Manual
- Aparecer apenas para estabelecimentos com PIX Manual
- Mudar `pagamento_status` para `pago`
- Enviar notificação ao cliente

**Status Atual:** ✅ JÁ IMPLEMENTADO (PARCIALMENTE)

**Código Existente:**

#### Controller:
```php
// application/controllers/painel/Agendamentos.php:386-434
public function confirmar_pagamento_pix_manual($id) {
    // Verificações
    // Atualiza status para 'confirmado' e pagamento_status para 'pago'
    // Envia notificações WhatsApp
}
```

#### View (Visualizar):
```php
// application/views/painel/agendamentos/visualizar.php:156-171
<?php if ($agendamento->forma_pagamento == 'pix_manual' &&
          $agendamento->pagamento_status == 'pendente'): ?>
    <a href="<?= base_url('painel/agendamentos/confirmar_pagamento_pix_manual/' . $agendamento->id) ?>"
       class="btn btn-success">
        <i class="ti ti-check-circle me-1"></i>
        Confirmar Pagamento PIX
    </a>
<?php endif; ?>
```

**Problema Identificado:**
- ✅ Botão existe na página de **visualizar** (detalhes do agendamento)
- ❌ Botão **NÃO EXISTE** na view **rápida** (cards)
- ❌ Botão **NÃO EXISTE** na view **lista**

**Localização dos Cards:**
- `application/views/admin/agendamentos/_rapida.php` (linhas 185-260)
- `application/views/admin/agendamentos/_lista.php` (linhas 165-198)

---

## 📊 RESUMO DA SITUAÇÃO

| Requisito | Status | Localização | Ação Necessária |
|-----------|--------|-------------|-----------------|
| **Filtro Padrão** | ❌ Não implementado | `_rapida.php:29`<br>`Agendamentos.php:42-44` | Implementar filtro padrão |
| **Botão Finalizar - Lógica** | ⚠️ Parcial | `Agendamentos.php:441-483` | Adicionar lógica de pagamento |
| **Botão Confirmar PIX - Controller** | ✅ Implementado | `Agendamentos.php:386-434` | Nenhuma |
| **Botão Confirmar PIX - View Visualizar** | ✅ Implementado | `visualizar.php:156-171` | Nenhuma |
| **Botão Confirmar PIX - View Rápida** | ❌ Não existe | `_rapida.php:185-260` | Adicionar botão |
| **Botão Confirmar PIX - View Lista** | ❌ Não existe | `_lista.php:165-198` | Adicionar botão |

---

## 🔧 IMPLEMENTAÇÃO PROPOSTA

### 1. Filtro Padrão (Confirmados + Pendentes)

**Arquivo:** `application/controllers/painel/Agendamentos.php`

**Linha 42-44 (ATUAL):**
```php
if ($this->input->get('status') && $this->input->get('status') !== 'todos') {
    $filtros['status'] = $this->input->get('status');
}
```

**PROPOSTA:**
```php
// Filtro de status com padrão para confirmados e pendentes
$status_get = $this->input->get('status');
if ($status_get && $status_get !== 'todos') {
    $filtros['status'] = $status_get;
} elseif (!$status_get || $status_get === 'todos') {
    // Padrão: apenas confirmados e pendentes
    $filtros['status_in'] = ['confirmado', 'pendente'];
}
```

**Arquivo:** `application/views/admin/agendamentos/_rapida.php`

**Linha 29 (ATUAL):**
```php
<option value="todos" <?= !isset($filtros['status']) || $filtros['status'] == '' ? 'selected' : '' ?>>Todos</option>
```

**PROPOSTA:**
```php
<option value="todos">Todos</option>
```

---

### 2. Botão Finalizar Atendimento (Lógica Completa)

**Arquivo:** `application/controllers/painel/Agendamentos.php`

**Método:** `finalizar_rapido()` (linha 441)

**CÓDIGO ATUAL:**
```php
// Finalizar agendamento
$resultado = $this->Agendamento_model->update($id, [
    'status' => 'finalizado',
    'hora_fim_real' => date('H:i:s')
]);
```

**PROPOSTA:**
```php
// Carregar estabelecimento para verificar tipo de pagamento
$this->load->model('Estabelecimento_model');
$estabelecimento = $this->Estabelecimento_model->get($agendamento->estabelecimento_id);

// Preparar dados de atualização
$dados_atualizacao = [
    'status' => 'finalizado',
    'hora_fim_real' => date('H:i:s')
];

// Lógica de pagamento ao finalizar
if ($agendamento->forma_pagamento == 'pix') {
    // Se é PIX e estabelecimento usa PIX Manual
    if ($estabelecimento->pagamento_tipo == 'pix_manual') {
        $dados_atualizacao['forma_pagamento'] = 'pix_manual';
        $dados_atualizacao['pagamento_status'] = 'pago';
    }
    // Se é PIX e estabelecimento usa Mercado Pago
    // Não altera pagamento, apenas finaliza
} elseif ($agendamento->forma_pagamento == 'presencial') {
    // Pagamento presencial: marcar como pago
    $dados_atualizacao['pagamento_status'] = 'pago';
} elseif ($agendamento->forma_pagamento == 'pix_manual') {
    // PIX Manual: marcar como pago
    $dados_atualizacao['pagamento_status'] = 'pago';
}

// Atualizar agendamento
$resultado = $this->Agendamento_model->update($id, $dados_atualizacao);
```

---

### 3. Botão Confirmar Pagamento PIX (Adicionar nos Cards)

**Arquivo:** `application/views/admin/agendamentos/_rapida.php`

**Localização:** Após linha 196 (dentro do card, após botão "Finalizar Atendimento")

**PROPOSTA:**
```php
<!-- Botão Confirmar Pagamento PIX Manual -->
<?php if ($ag->forma_pagamento == 'pix_manual' && $ag->pagamento_status == 'pendente'): ?>
<button type="button"
        class="btn btn-success btn-confirmar-pix"
        data-agendamento-id="<?= $ag->id ?>"
        data-cliente-nome="<?= $ag->cliente_nome ?>">
    <i class="ti ti-check-circle me-2"></i>
    Confirmar Pagamento PIX
</button>
<?php endif; ?>
```

**JavaScript necessário (adicionar no final do arquivo):**
```javascript
// Confirmar Pagamento PIX Manual
document.querySelectorAll('.btn-confirmar-pix').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const agendamentoId = this.getAttribute('data-agendamento-id');
        const clienteNome = this.getAttribute('data-cliente-nome');

        Swal.fire({
            title: 'Confirmar Pagamento PIX',
            html: `Confirmar que o pagamento PIX foi recebido de <strong>${clienteNome}</strong>?<br><br><small class="text-muted">O cliente será notificado via WhatsApp.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2fb344'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('painel/agendamentos/confirmar_pagamento_pix_manual/') ?>' + agendamentoId;
            }
        });
    });
});
```

---

## 🎨 SUGESTÃO DE TEXTO DO BOTÃO

**Opções para o botão de confirmar pagamento:**

1. ✅ **"Confirmar Pagamento PIX"** (atual - RECOMENDADO)
2. "PIX Recebido"
3. "Confirmar PIX"
4. "Pagamento Recebido"
5. "Marcar como Pago"

**Recomendação:** Manter **"Confirmar Pagamento PIX"** pois é claro e específico.

---

## ⚠️ PONTOS DE ATENÇÃO

### 1. Verificação do Tipo de PIX
- Estabelecimento pode ter `pagamento_tipo` = `'pix_manual'` ou `'mercadopago'`
- Precisa carregar dados do estabelecimento antes de aplicar lógica

### 2. Botão Confirmar PIX
- Só deve aparecer para `forma_pagamento = 'pix_manual'`
- Só deve aparecer para `pagamento_status = 'pendente'`
- Não deve aparecer para Mercado Pago

### 3. Model Agendamento
- Método `update()` já existe e funciona
- Não precisa criar novos métodos

### 4. Notificações WhatsApp
- Já implementadas no método `confirmar_pagamento_pix_manual()`
- Não precisa adicionar lógica extra

---

## 📝 ARQUIVOS A MODIFICAR

1. ✅ `application/controllers/painel/Agendamentos.php`
   - Método `index()` - Adicionar filtro padrão
   - Método `finalizar_rapido()` - Adicionar lógica de pagamento

2. ✅ `application/views/admin/agendamentos/_rapida.php`
   - Adicionar botão "Confirmar Pagamento PIX"
   - Adicionar JavaScript para o botão
   - Ajustar filtro padrão (opcional)

3. ✅ `application/views/admin/agendamentos/_lista.php`
   - Adicionar botão "Confirmar Pagamento PIX" (se necessário)

4. ⚠️ `application/models/Agendamento_model.php`
   - Verificar se método `get_all()` suporta `status_in` (array de status)
   - Se não suportar, adicionar suporte

---

## 🚀 PRÓXIMOS PASSOS

1. ⏳ **AGUARDANDO:** Aprovação do usuário
2. ⏳ **AGUARDANDO:** Confirmação se análise está correta
3. ⏳ **AGUARDANDO:** OK para iniciar implementação

**Após aprovação:**
1. Implementar filtro padrão
2. Ajustar lógica do botão Finalizar
3. Adicionar botão Confirmar PIX nos cards
4. Testar todas as funcionalidades
5. Commit no git

---

## ❓ DÚVIDAS PARA O USUÁRIO

1. ✅ Análise está correta?
2. ✅ Requisitos foram bem compreendidos?
3. ✅ Alguma alteração necessária na proposta?
4. ✅ Posso prosseguir com a implementação?

---

**AGUARDANDO OK DO USUÁRIO PARA PROSSEGUIR** ⏳
