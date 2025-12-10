# Pendências para Cadastro de Usuários Multi-Tenant

**Data:** 10/12/2024
**Status:** Quase pronto - falta adicionar 1 método

---

## ✅ O QUE JÁ FOI FEITO

1. ✅ Controller `Usuarios.php` adaptado para multi-tenant
   - Usa `tipo` ao invés de `nivel`
   - Usa `ativo` (1/0) ao invés de `status` (ativo/inativo)
   - Valida campos condicionais por tipo
   - Carrega estabelecimentos e profissionais

2. ✅ View `index.php` corrigida
   - Mostra tipos corretos
   - Mostra status ativo/inativo

---

## ⏭️ O QUE FALTA

### 1. Adicionar Método ao Usuario_model ⚠️

Adicionar o método `get_all_with_filters()` no arquivo:
`application/models/Usuario_model.php`

**Localização:** Após a linha 131 (depois do método `get_all()`)

**Código para adicionar:**

```php
    /**
     * Listar usuários com filtros avançados
     *
     * @param string $busca Busca por nome ou email
     * @param array $filtros Filtros adicionais (tipo, ativo, estabelecimento_id)
     * @return array
     */
    public function get_all_with_filters($busca = null, $filtros = []) {
        $this->db->select('u.*, e.nome_fantasia as estabelecimento_nome, p.nome as profissional_nome')
            ->from($this->table . ' u')
            ->join('estabelecimentos e', 'u.estabelecimento_id = e.id', 'left')
            ->join('profissionais p', 'u.profissional_id = p.id', 'left');

        // Busca por nome ou email
        if ($busca) {
            $this->db->group_start()
                ->like('u.nome', $busca)
                ->or_like('u.email', $busca)
                ->group_end();
        }

        // Filtros
        if (isset($filtros['tipo'])) {
            $this->db->where('u.tipo', $filtros['tipo']);
        }

        if (isset($filtros['ativo'])) {
            $this->db->where('u.ativo', $filtros['ativo']);
        }

        if (isset($filtros['estabelecimento_id'])) {
            $this->db->where('u.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        $this->db->order_by('u.nome', 'ASC');

        return $this->db->get()->result();
    }
```

### 2. Atualizar Views de Criar/Editar Usuário

As views `criar.php` e `editar.php` precisam ser atualizadas para:

**Campos necessários:**
- Nome
- Email
- Telefone
- **Tipo** (select com: super_admin, estabelecimento, profissional)
- **Ativo** (checkbox)
- Senha (obrigatória ao criar, opcional ao editar)

**Campos condicionais:**
- Se tipo = "estabelecimento": mostrar select de Estabelecimento
- Se tipo = "profissional": mostrar select de Estabelecimento + Profissional

**JavaScript necessário:**
- Mostrar/ocultar campos baseado no tipo selecionado
- Carregar profissionais via AJAX quando selecionar estabelecimento

---

## 🎯 COMO TESTAR DEPOIS

1. Adicionar o método ao model
2. Atualizar as views
3. Acessar `/admin/usuarios/criar`
4. Criar usuários dos 3 tipos:
   - Super Admin
   - Estabelecimento (vincular a um estabelecimento)
   - Profissional (vincular a estabelecimento + profissional)
5. Fazer login com cada tipo e verificar redirecionamento

---

## 📝 EXEMPLO DE CRIAÇÃO

### Super Admin
- Tipo: super_admin
- Não precisa de estabelecimento_id nem profissional_id

### Estabelecimento
- Tipo: estabelecimento
- estabelecimento_id: (selecionar da lista)
- profissional_id: NULL

### Profissional
- Tipo: profissional
- estabelecimento_id: (selecionar da lista)
- profissional_id: (selecionar da lista filtrada)

---

**Próximo passo:** Adicionar o método `get_all_with_filters()` manualmente ou pedir para eu criar as views completas.
