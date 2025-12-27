# Edição Manual Necessária

## 📝 Atualizar Função `calcular_data_maxima_dias_uteis()`

### **Arquivos:**
1. `application/controllers/painel/Agendamentos.php` (linha ~505)
2. `application/controllers/agenda/Agendamentos.php` (linha ~307)

---

### **Código Atual:**

```php
// Calcular data máxima pulando dias inativos
$data_atual = new DateTime();
$dias_contados = 0;

while ($dias_contados < $dias_necessarios) {
    $data_atual->add(new DateInterval('P1D')); // Adicionar 1 dia
    $dia_semana = (int)$data_atual->format('w'); // 0=domingo, 6=sábado

    // Contar apenas se o dia está ativo
    if (in_array($dia_semana, $dias_ativos)) {
        $dias_contados++;
    }
}
```

---

### **Substituir por:**

```php
// Carregar model de feriados
$this->load->model('Feriado_model');

// Calcular data máxima pulando dias inativos E feriados
$data_atual = new DateTime();
$dias_contados = 0;

while ($dias_contados < $dias_necessarios) {
    $data_atual->add(new DateInterval('P1D')); // Adicionar 1 dia
    $dia_semana = (int)$data_atual->format('w'); // 0=domingo, 6=sábado
    $data_str = $data_atual->format('Y-m-d');

    // Contar apenas se o dia está ativo E não é feriado
    if (in_array($dia_semana, $dias_ativos) &&
        !$this->Feriado_model->is_feriado($data_str, $estabelecimento_id)) {
        $dias_contados++;
    }
}
```

---

### **O que foi adicionado:**
1. Load do `Feriado_model`
2. Variável `$data_str` com data formatada
3. Verificação `!$this->Feriado_model->is_feriado()` no `if`

---

**Faça essa alteração nos 2 arquivos e me avise quando concluir!**
