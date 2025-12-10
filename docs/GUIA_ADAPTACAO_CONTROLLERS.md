# Guia de Adaptação de Controllers para Multi-Tenant

**Autor:** Rafael Dias - doisr.com.br
**Data:** 10/12/2024

---

## 📋 Checklist de Adaptação

Para cada controller admin, siga estes passos:

### ✅ 1. Filtrar Listagem por Estabelecimento

**ANTES:**
```php
public function index() {
    $filtros = [];

    if ($this->input->get('estabelecimento_id')) {
        $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
    }

    $data['items'] = $this->Model->get_all($filtros);
    $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();
}
```

**DEPOIS:**
```php
public function index() {
    $filtros = [];

    // Multi-tenant: filtrar automaticamente
    if ($this->estabelecimento_id) {
        // Usuário de estabelecimento: apenas seu estabelecimento
        $filtros['estabelecimento_id'] = $this->estabelecimento_id;
    } elseif ($this->input->get('estabelecimento_id')) {
        // Super admin: pode filtrar qualquer estabelecimento
        $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
    }

    $data['items'] = $this->Model->get_all($filtros);

    // Estabelecimentos apenas para super admin
    if ($this->auth_check->is_super_admin()) {
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();
    }
}
```

---

### ✅ 2. Verificar Permissão ao Visualizar/Editar/Deletar

**ANTES:**
```php
public function visualizar($id) {
    $item = $this->Model->get_by_id($id);

    if (!$item) {
        redirect('admin/items');
    }

    // ... resto do código
}
```

**DEPOIS:**
```php
public function visualizar($id) {
    $item = $this->Model->get_by_id($id);

    if (!$item) {
        $this->session->set_flashdata('erro', 'Item não encontrado.');
        redirect('admin/items');
    }

    // Verificar se pertence ao estabelecimento (multi-tenant)
    if ($this->estabelecimento_id && $item->estabelecimento_id != $this->estabelecimento_id) {
        $this->session->set_flashdata('erro', 'Você não tem permissão para visualizar este item.');
        redirect('admin/items');
    }

    // ... resto do código
}
```

---

### ✅ 3. Usar Estabelecimento Correto ao Criar

**ANTES:**
```php
public function criar() {
    if ($this->input->method() === 'post') {
        $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required');

        if ($this->form_validation->run()) {
            $dados = [
                'estabelecimento_id' => $this->input->post('estabelecimento_id'),
                // ... outros campos
            ];

            $this->Model->create($dados);
        }
    }

    $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();
}
```

**DEPOIS:**
```php
public function criar() {
    if ($this->input->method() === 'post') {
        // Validação condicional
        if (!$this->estabelecimento_id) {
            $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required');
        }

        if ($this->form_validation->run()) {
            $dados = [
                // Multi-tenant: usar estabelecimento do usuário ou do formulário
                'estabelecimento_id' => $this->estabelecimento_id ?: $this->input->post('estabelecimento_id'),
                // ... outros campos
            ];

            $id = $this->Model->create($dados);

            if ($id) {
                $this->registrar_log('criar', 'tabela', $id, null, $dados);
            }
        }
    }

    // Estabelecimentos apenas para super admin
    if ($this->auth_check->is_super_admin()) {
        $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();
    }
}
```

---

### ✅ 4. Registrar Logs de Ações

**Adicionar em criar/atualizar/deletar:**

```php
// Criar
if ($id) {
    $this->registrar_log('criar', 'nome_tabela', $id, null, $dados);
}

// Atualizar
$dados_antigos = (array) $item;
if ($this->Model->update($id, $dados)) {
    $this->registrar_log('atualizar', 'nome_tabela', $id, $dados_antigos, $dados);
}

// Deletar
if ($this->Model->delete($id)) {
    $this->registrar_log('deletar', 'nome_tabela', $id, (array) $item, null);
}
```

---

## 🎯 Exemplo Completo: Profissionais Controller

```php
<?php
class Profissionais extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Profissional_model');
    }

    // 1. LISTAGEM
    public function index() {
        $filtros = [];

        // Multi-tenant
        if ($this->estabelecimento_id) {
            $filtros['estabelecimento_id'] = $this->estabelecimento_id;
        } elseif ($this->input->get('estabelecimento_id')) {
            $filtros['estabelecimento_id'] = $this->input->get('estabelecimento_id');
        }

        $data['profissionais'] = $this->Profissional_model->get_all($filtros);

        if ($this->auth_check->is_super_admin()) {
            $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();
        }

        $this->load->view('admin/profissionais/index', $data);
    }

    // 2. CRIAR
    public function criar() {
        // Verificar limite do plano
        if ($this->estabelecimento_id && !$this->pode_criar_profissional()) {
            $this->session->set_flashdata('erro', 'Limite de profissionais atingido. Faça upgrade do plano.');
            redirect('admin/profissionais');
        }

        if ($this->input->method() === 'post') {
            if (!$this->estabelecimento_id) {
                $this->form_validation->set_rules('estabelecimento_id', 'Estabelecimento', 'required');
            }

            $this->form_validation->set_rules('nome', 'Nome', 'required');

            if ($this->form_validation->run()) {
                $dados = [
                    'estabelecimento_id' => $this->estabelecimento_id ?: $this->input->post('estabelecimento_id'),
                    'nome' => $this->input->post('nome'),
                    // ... outros campos
                ];

                $id = $this->Profissional_model->create($dados);

                if ($id) {
                    $this->registrar_log('criar', 'profissionais', $id, null, $dados);
                    $this->session->set_flashdata('sucesso', 'Profissional criado!');
                    redirect('admin/profissionais');
                }
            }
        }

        if ($this->auth_check->is_super_admin()) {
            $data['estabelecimentos'] = $this->Estabelecimento_model->get_all();
        }

        $this->load->view('admin/profissionais/form', $data);
    }

    // 3. EDITAR
    public function editar($id) {
        $profissional = $this->Profissional_model->get_by_id($id);

        if (!$profissional) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('admin/profissionais');
        }

        // Verificar permissão
        if ($this->estabelecimento_id && $profissional->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Sem permissão.');
            redirect('admin/profissionais');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nome', 'Nome', 'required');

            if ($this->form_validation->run()) {
                $dados_antigos = (array) $profissional;

                $dados = [
                    'nome' => $this->input->post('nome'),
                    // ... outros campos
                ];

                if ($this->Profissional_model->update($id, $dados)) {
                    $this->registrar_log('atualizar', 'profissionais', $id, $dados_antigos, $dados);
                    $this->session->set_flashdata('sucesso', 'Profissional atualizado!');
                    redirect('admin/profissionais');
                }
            }
        }

        $data['profissional'] = $profissional;
        $this->load->view('admin/profissionais/form', $data);
    }

    // 4. DELETAR
    public function deletar($id) {
        $profissional = $this->Profissional_model->get_by_id($id);

        if (!$profissional) {
            $this->session->set_flashdata('erro', 'Profissional não encontrado.');
            redirect('admin/profissionais');
        }

        // Verificar permissão
        if ($this->estabelecimento_id && $profissional->estabelecimento_id != $this->estabelecimento_id) {
            $this->session->set_flashdata('erro', 'Sem permissão.');
            redirect('admin/profissionais');
        }

        if ($this->Profissional_model->delete($id)) {
            $this->registrar_log('deletar', 'profissionais', $id, (array) $profissional, null);
            $this->session->set_flashdata('sucesso', 'Profissional deletado!');
        }

        redirect('admin/profissionais');
    }
}
```

---

## 📝 Controllers que Precisam ser Adaptados

### ✅ Já Adaptado
- [x] `Clientes.php` ✅

### ⏭️ Pendentes (14 controllers)
- [ ] `Profissionais.php`
- [ ] `Servicos.php`
- [ ] `Agendamentos.php`
- [ ] `Bloqueios.php`
- [ ] `Disponibilidade.php`
- [ ] `Dashboard.php`
- [ ] `Configuracoes.php`
- [ ] `Perfil.php`
- [ ] `Logs.php`
- [ ] `Usuarios.php`
- [ ] `Estabelecimentos.php` (apenas super admin)
- [ ] `Pagamentos.php`
- [ ] `Mercadopago_test.php`
- [ ] `Pagamento_test.php`

---

## 🚫 Controllers que NÃO Precisam de Adaptação

### Super Admin Apenas
- `Estabelecimentos.php` - Já filtra corretamente
- `Planos.php` - Gerenciamento global
- `Assinaturas.php` - Gerenciamento global

### Públicos
- `Webhook.php` - Recebe notificações externas

---

## ⚡ Atalhos Úteis

### Verificar se é Super Admin
```php
if ($this->auth_check->is_super_admin()) {
    // Código apenas para super admin
}
```

### Verificar Limite de Plano
```php
if (!$this->pode_criar_profissional()) {
    $this->session->set_flashdata('erro', 'Limite atingido.');
    redirect('admin/profissionais');
}
```

### Verificar Recurso do Plano
```php
if (!$this->tem_recurso('whatsapp')) {
    $this->session->set_flashdata('erro', 'Recurso não disponível no seu plano.');
    redirect('admin/dashboard');
}
```

---

## 🎯 Prioridade de Adaptação

### Alta Prioridade (Funcionalidades Principais)
1. `Profissionais.php`
2. `Servicos.php`
3. `Agendamentos.php`
4. `Dashboard.php`

### Média Prioridade
5. `Disponibilidade.php`
6. `Bloqueios.php`
7. `Configuracoes.php`
8. `Usuarios.php`

### Baixa Prioridade
9. `Logs.php`
10. `Perfil.php`
11. `Pagamentos.php`

---

## ✅ Conclusão

**Padrão de Adaptação:**
1. ✅ Filtrar listagem por `estabelecimento_id`
2. ✅ Verificar permissão em visualizar/editar/deletar
3. ✅ Usar `estabelecimento_id` correto ao criar
4. ✅ Ocultar seleção de estabelecimento para não super_admin
5. ✅ Registrar logs de ações
6. ✅ Verificar limites de plano (quando aplicável)

**Tempo Estimado:** ~15-20 minutos por controller
**Total:** ~4-5 horas para todos os controllers
