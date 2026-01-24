# ⚠️ PENDENTE: PIX MANUAL - CÓDIGO COPIA E COLA INVÁLIDO

**Autor:** Rafael Dias - doisr.com.br
**Data:** 24/01/2026 02:08
**Status:** 🔴 PENDENTE CORREÇÃO

---

## ✅ O QUE ESTÁ FUNCIONANDO

1. ✅ **Bot respondendo normalmente**
2. ✅ **Conversas encerradas sendo reativadas automaticamente**
3. ✅ **PIX Manual sendo gerado via bot** (não mais Mercado Pago)
4. ✅ **QR Code sendo gerado**
5. ✅ **Código copia e cola sendo gerado**

---

## ❌ PROBLEMA IDENTIFICADO

### Código Copia e Cola NÃO é um PIX válido

**Sintoma:** Ao tentar colar o código no app do banco, dá erro - não reconhece como PIX válido.

**Causa Provável:** O código BR Code gerado pela biblioteca `Pix_lib.php` não está no formato correto do padrão PIX (EMV).

### Formato Esperado do PIX Copia e Cola:
```
00020126580014br.gov.bcb.pix0136420ab7c4-4d63-46d4-809e-cd3eebc129ec5204000053039865802BR5925Rafael de Andrade Dias6004Laje62070503***63041D3A
```

**Características do formato correto:**
- Inicia com `00020126...`
- Contém tags EMV (00, 01, 26, 52, 53, 58, 59, 60, 62, 63)
- Termina com CRC16 (4 dígitos hexadecimais)
- Tamanho variável conforme dados

---

## 🔍 ONDE INVESTIGAR

### Arquivo: `application/libraries/Pix_lib.php`

**Método responsável:** `gerar_pix_manual()`

```php
// Linha ~250-300 (aproximadamente)
public function gerar_pix_manual($dados) {
    // Gera BR Code (copia e cola)
    $brcode = $this->gerar_brcode($dados);

    // Gera QR Code a partir do BR Code
    $qrcode = $this->gerar_qrcode($brcode);

    return [
        'brcode' => $brcode,
        'qrcode' => $qrcode
    ];
}
```

**Verificar:**
1. Método `gerar_brcode()` - Está gerando formato EMV correto?
2. Cálculo do CRC16 - Está correto?
3. Montagem das tags EMV - Ordem e formato corretos?
4. Chave PIX - Está sendo incluída corretamente?

---

## 📋 TAGS EMV DO PIX (Padrão Banco Central)

```
00 - Payload Format Indicator (fixo: "01")
01 - Point of Initiation Method (opcional)
26 - Merchant Account Information
  00 - GUI (fixo: "br.gov.bcb.pix")
  01 - Chave PIX
52 - Merchant Category Code (fixo: "0000")
53 - Transaction Currency (fixo: "986" = BRL)
54 - Transaction Amount (valor)
58 - Country Code (fixo: "BR")
59 - Merchant Name (nome do recebedor)
60 - Merchant City (cidade)
62 - Additional Data Field Template
  05 - Reference Label (identificador da transação)
63 - CRC16 (checksum)
```

---

## 🔧 POSSÍVEIS SOLUÇÕES

### Opção 1: Corrigir biblioteca Pix_lib.php
- Revisar método `gerar_brcode()`
- Validar formato EMV
- Testar CRC16
- Comparar com biblioteca de referência

### Opção 2: Usar biblioteca externa validada
- `mpdf/qrcode` - Suporta PIX
- `chillerlan/php-qrcode` - Suporta PIX
- `piggly/php-pix` - Específica para PIX

### Opção 3: Validar com ferramenta online
- https://pix.nascent.com.br/tools/pix-qr-decoder/
- Decodificar QR Code gerado
- Comparar com formato esperado

---

## 🧪 TESTE PARA VALIDAÇÃO

### 1. Capturar código gerado
```sql
SELECT pagamento_pix_copia_cola
FROM agendamentos
WHERE estabelecimento_id = 4
ORDER BY id DESC
LIMIT 1;
```

### 2. Validar formato
- Deve iniciar com `00020126`
- Deve conter `br.gov.bcb.pix`
- Deve conter chave PIX (UUID sem traços)
- Deve terminar com 4 dígitos hexadecimais (CRC16)

### 3. Testar em app bancário
- Copiar código
- Colar no app do banco
- Deve reconhecer como PIX válido

---

## 📝 DADOS DO ESTABELECIMENTO (ID 4)

```
PIX Chave: 420ab7c44d6346d4809ecd3eebc129ec (UUID sem traços)
PIX Tipo: aleatoria
PIX Nome: Rafael de Andrade Dias
PIX Cidade: Laje
Valor Teste: R$ 1,00
```

---

## 🚀 PRÓXIMOS PASSOS (PARA AMANHÃ)

1. 🔍 Analisar código atual do `Pix_lib.php`
2. 🔍 Capturar código copia e cola gerado
3. 🔍 Validar formato EMV
4. 🔧 Corrigir geração do BR Code
5. 🧪 Testar em app bancário
6. ✅ Validar funcionamento completo

---

## 💡 REFERÊNCIAS

- **Documentação Oficial PIX:** https://www.bcb.gov.br/estabilidadefinanceira/pix
- **Especificação EMV QR Code:** https://www.emvco.com/emv-technologies/qrcodes/
- **Padrão BR Code:** Manual de Padrões para Iniciação do PIX (Banco Central)

---

## ✅ CORREÇÕES JÁ APLICADAS (FUNCIONANDO)

1. ✅ Validação de chave PIX aleatória (UUID)
2. ✅ Normalização de chave PIX (remoção de traços)
3. ✅ Reativação automática de conversas encerradas
4. ✅ Recarregamento de estabelecimento antes de gerar PIX
5. ✅ Controle independente de bot por estabelecimento
6. ✅ PIX Manual sendo gerado (ao invés de Mercado Pago)

---

## 🎯 RESUMO

**Funcionando:** Bot, fluxo de agendamento, geração de PIX Manual
**Pendente:** Formato do código copia e cola (BR Code) não é PIX válido
**Próximo:** Corrigir geração do BR Code no formato EMV correto
