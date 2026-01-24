<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Biblioteca PIX - Geração de BR Code e QR Code
 *
 * Gera código PIX (copia e cola) seguindo padrão do Banco Central
 * e converte em QR Code para pagamentos
 *
 * @author Rafael Dias - doisr.com.br
 * @date 23/01/2026
 */
class Pix_lib {

    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Gera BR Code (código copia e cola) PIX
     *
     * @param array $dados Dados do PIX
     *   - chave_pix: Chave PIX do recebedor
     *   - nome_recebedor: Nome do recebedor
     *   - cidade: Cidade do recebedor
     *   - valor: Valor da transação (float)
     *   - txid: Identificador único da transação (opcional)
     *   - descricao: Descrição da transação (opcional)
     *
     * @return string BR Code gerado
     */
    public function gerar_br_code($dados) {
        // Validar dados obrigatórios
        if (empty($dados['chave_pix']) || empty($dados['nome_recebedor']) || empty($dados['cidade'])) {
            log_message('error', 'PIX: Dados obrigatórios não informados');
            return false;
        }

        // Preparar dados
        $chave_pix = $dados['chave_pix'];
        $nome_recebedor = $this->limpar_string($dados['nome_recebedor'], 25);
        $cidade = $this->limpar_string($dados['cidade'], 15);
        $valor = isset($dados['valor']) ? number_format($dados['valor'], 2, '.', '') : null;
        $txid = isset($dados['txid']) ? substr($dados['txid'], 0, 25) : null;
        $descricao = isset($dados['descricao']) ? $this->limpar_string($dados['descricao'], 72) : null;

        // Merchant Account Information (ID 26 - PIX)
        $payload_pix = $this->gerar_campo('00', 'BR.GOV.BCB.PIX'); // GUI
        $payload_pix .= $this->gerar_campo('01', $chave_pix); // Chave PIX

        if ($descricao) {
            $payload_pix .= $this->gerar_campo('02', $descricao); // Descrição
        }

        $merchant_account = $this->gerar_campo('26', $payload_pix);

        // Merchant Category Code (MCC)
        $merchant_category = $this->gerar_campo('52', '0000');

        // Transaction Currency (986 = BRL)
        $currency = $this->gerar_campo('53', '986');

        // Transaction Amount (se informado)
        $amount = '';
        if ($valor) {
            $amount = $this->gerar_campo('54', $valor);
        }

        // Country Code
        $country = $this->gerar_campo('58', 'BR');

        // Merchant Name
        $merchant_name = $this->gerar_campo('59', $nome_recebedor);

        // Merchant City
        $merchant_city = $this->gerar_campo('60', $cidade);

        // Additional Data Field (txid)
        $additional_data = '';
        if ($txid) {
            $additional_data_payload = $this->gerar_campo('05', $txid);
            $additional_data = $this->gerar_campo('62', $additional_data_payload);
        }

        // Montar payload completo (sem CRC ainda)
        $payload = '00020101'; // Payload Format Indicator
        $payload .= '010212'; // Point of Initiation Method (12 = static, 11 = dynamic)
        $payload .= $merchant_account;
        $payload .= $merchant_category;
        $payload .= $currency;
        $payload .= $amount;
        $payload .= $country;
        $payload .= $merchant_name;
        $payload .= $merchant_city;
        $payload .= $additional_data;
        $payload .= '6304'; // CRC16 placeholder

        // Calcular CRC16
        $crc = $this->calcular_crc16($payload);
        $payload .= $crc;

        log_message('debug', 'PIX: BR Code gerado - ' . substr($payload, 0, 50) . '...');

        return $payload;
    }

    /**
     * Gera campo do BR Code no formato ID+Tamanho+Valor
     */
    private function gerar_campo($id, $valor) {
        $tamanho = strlen($valor);
        return $id . str_pad($tamanho, 2, '0', STR_PAD_LEFT) . $valor;
    }

    /**
     * Limpa string removendo acentos e caracteres especiais
     */
    private function limpar_string($string, $max_length = null) {
        // Remover acentos
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

        // Remover caracteres especiais, manter apenas letras, números e espaços
        $string = preg_replace('/[^A-Za-z0-9 ]/', '', $string);

        // Converter para maiúsculas
        $string = strtoupper($string);

        // Limitar tamanho
        if ($max_length) {
            $string = substr($string, 0, $max_length);
        }

        return $string;
    }

    /**
     * Calcula CRC16-CCITT do payload PIX
     */
    private function calcular_crc16($payload) {
        $polynomial = 0x1021;
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= (ord($payload[$i]) << 8);

            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Gera URL do QR Code a partir do BR Code
     * Usa API do QR Server (gratuita e sem limite)
     *
     * @param string $br_code BR Code gerado
     * @param int $size Tamanho do QR Code em pixels (padrão: 300)
     * @return string URL do QR Code
     */
    public function gerar_qrcode_url($br_code, $size = 300) {
        if (empty($br_code)) {
            return false;
        }

        // Usar API QR Server (gratuita, sem autenticação)
        $url = 'https://api.qrserver.com/v1/create-qr-code/';
        $url .= '?size=' . $size . 'x' . $size;
        $url .= '&data=' . urlencode($br_code);
        $url .= '&format=png';
        $url .= '&margin=10';

        log_message('debug', 'PIX: QR Code URL gerada');

        return $url;
    }

    /**
     * Valida chave PIX
     *
     * @param string $chave Chave PIX
     * @param string $tipo Tipo da chave (cpf, cnpj, email, telefone, aleatoria)
     * @return bool
     */
    public function validar_chave_pix($chave, $tipo) {
        $chave = trim($chave);

        switch ($tipo) {
            case 'cpf':
                // Remove formatação
                $cpf = preg_replace('/[^0-9]/', '', $chave);
                return strlen($cpf) == 11 && $this->validar_cpf($cpf);

            case 'cnpj':
                // Remove formatação
                $cnpj = preg_replace('/[^0-9]/', '', $chave);
                return strlen($cnpj) == 14 && $this->validar_cnpj($cnpj);

            case 'email':
                return filter_var($chave, FILTER_VALIDATE_EMAIL) !== false;

            case 'telefone':
                // Formato: +5575999999999 ou 5575999999999
                $telefone = preg_replace('/[^0-9]/', '', $chave);
                return strlen($telefone) >= 10 && strlen($telefone) <= 13;

            case 'aleatoria':
                // Chave aleatória: UUID com ou sem hífens
                // Formato com hífens: 420ab7c4-4d63-46d4-809e-cd3eebc129ec (36 caracteres)
                // Formato sem hífens: 420ab7c44d6346d4809ecd3eebc129ec (32 caracteres)
                $chave_limpa = str_replace('-', '', $chave);
                return strlen($chave_limpa) == 32 && ctype_xdigit($chave_limpa);

            default:
                return false;
        }
    }

    /**
     * Valida CPF
     */
    private function validar_cpf($cpf) {
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    /**
     * Valida CNPJ
     */
    private function validar_cnpj($cnpj) {
        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != $digitos[0]) return false;

        $tamanho = $tamanho + 1;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        return $resultado == $digitos[1];
    }

    /**
     * Formata chave PIX para exibição
     */
    public function formatar_chave_pix($chave, $tipo) {
        switch ($tipo) {
            case 'cpf':
                $cpf = preg_replace('/[^0-9]/', '', $chave);
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

            case 'cnpj':
                $cnpj = preg_replace('/[^0-9]/', '', $chave);
                return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);

            case 'telefone':
                $tel = preg_replace('/[^0-9]/', '', $chave);
                if (strlen($tel) == 13) {
                    return preg_replace('/(\d{2})(\d{2})(\d{5})(\d{4})/', '+$1 ($2) $3-$4', $tel);
                } elseif (strlen($tel) == 11) {
                    return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $tel);
                }
                return $chave;

            default:
                return $chave;
        }
    }
}
