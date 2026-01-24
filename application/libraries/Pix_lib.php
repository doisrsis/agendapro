<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Biblioteca PIX - Wrapper para piggly/php-pix
 *
 * Gera código PIX (copia e cola) usando biblioteca validada piggly/php-pix
 * Testada em 10+ bancos brasileiros
 *
 * @author Rafael Dias - doisr.com.br
 * @date 24/01/2026
 */

// Carregar classes da biblioteca piggly/php-pix na ordem correta
require_once FCPATH . 'vendor/piggly/php-pix/src/Exceptions/InvalidPixKeyException.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Exceptions/InvalidPixKeyTypeException.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Exceptions/InvalidEmvFieldException.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Exceptions/EmvIdIsRequiredException.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Exceptions/CannotParseKeyTypeException.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Utils/Helper.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Utils/Cast.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Parser.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Emv/AbstractField.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Emv/Field.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Emv/MultiField.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/Emv/MPM.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/AbstractPayload.php';
require_once FCPATH . 'vendor/piggly/php-pix/src/StaticPayload.php';

use Piggly\Pix\StaticPayload;
use Piggly\Pix\Parser;

class Pix_lib {

    private $CI;

    public function __construct() {
        // Verificar se get_instance() existe (ambiente CodeIgniter)
        if (function_exists('get_instance')) {
            $this->CI =& get_instance();
        }
    }

    /**
     * Gera BR Code (código copia e cola) PIX usando biblioteca piggly/php-pix
     *
     * @param array $dados Dados do PIX
     *   - chave_pix: Chave PIX do recebedor
     *   - tipo_chave: Tipo da chave (cpf, cnpj, email, telefone, aleatoria)
     *   - nome_recebedor: Nome do recebedor
     *   - cidade: Cidade do recebedor
     *   - valor: Valor da transação (opcional)
     *   - txid: ID da transação (opcional)
     *   - descricao: Descrição (opcional)
     * @return string|false BR Code gerado ou false em caso de erro
     */
    public function gerar_br_code($dados) {
        try {
            if (empty($dados['chave_pix']) || empty($dados['nome_recebedor']) || empty($dados['cidade'])) {
                log_message('error', 'PIX: Dados obrigatórios não informados');
                return false;
            }

            // Determinar tipo da chave PIX
            $tipo_chave = $this->determinar_tipo_chave($dados['chave_pix']);

            // Criar payload estático
            $payload = new StaticPayload();

            // Configurar chave PIX
            $payload->setPixKey($tipo_chave, $dados['chave_pix']);

            // Configurar dados do recebedor
            $payload->setMerchantName($dados['nome_recebedor']);
            $payload->setMerchantCity($dados['cidade']);

            // Configurar valor (se informado)
            if (isset($dados['valor']) && $dados['valor'] > 0) {
                $payload->setAmount($dados['valor']);
            }

            // Configurar TID (se informado)
            if (isset($dados['txid']) && !empty($dados['txid'])) {
                $payload->setTid($dados['txid']);
            }

            // Configurar descrição (se informada)
            if (isset($dados['descricao']) && !empty($dados['descricao'])) {
                $payload->setDescription($dados['descricao']);
            }

            // Gerar BR Code
            $brcode = $payload->getPixCode();

            log_message('debug', 'PIX: BR Code gerado - ' . substr($brcode, 0, 50) . '...');

            return $brcode;

        } catch (Exception $e) {
            log_message('error', 'PIX: Erro ao gerar BR Code - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Determina o tipo da chave PIX
     *
     * @param string $chave Chave PIX
     * @return string Tipo da chave (document, email, phone, random)
     */
    private function determinar_tipo_chave($chave) {
        $chave = trim($chave);

        // Email
        if (filter_var($chave, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Telefone
        $telefone = preg_replace('/[^0-9]/', '', $chave);
        if (strlen($telefone) >= 10 && strlen($telefone) <= 13) {
            return 'phone';
        }

        // CPF/CNPJ
        $documento = preg_replace('/[^0-9]/', '', $chave);
        if (strlen($documento) == 11 || strlen($documento) == 14) {
            return 'document';
        }

        // Chave aleatória (UUID)
        $chave_limpa = str_replace('-', '', $chave);
        if (strlen($chave_limpa) == 32 && ctype_xdigit($chave_limpa)) {
            return 'random';
        }

        // Padrão: random
        return 'random';
    }

    /**
     * Gera URL do QR Code
     *
     * @param string $br_code BR Code do PIX
     * @param int $size Tamanho da imagem (padrão: 300)
     * @return string|false URL do QR Code ou false em caso de erro
     */
    public function gerar_qrcode_url($br_code, $size = 300) {
        if (empty($br_code)) {
            return false;
        }

        // Usar API pública para gerar QR Code
        $url = 'https://api.qrserver.com/v1/create-qr-code/';
        $url .= '?size=' . $size . 'x' . $size;
        $url .= '&data=' . urlencode($br_code);
        $url .= '&format=png';
        $url .= '&margin=10';

        return $url;
    }

    /**
     * Valida chave PIX
     *
     * @param string $chave Chave PIX
     * @param string $tipo Tipo da chave (cpf, cnpj, email, telefone, aleatoria)
     * @return bool True se válida, false caso contrário
     */
    public function validar_chave_pix($chave, $tipo) {
        $chave = trim($chave);

        switch ($tipo) {
            case 'cpf':
                $cpf = preg_replace('/[^0-9]/', '', $chave);
                return strlen($cpf) == 11 && $this->validar_cpf($cpf);

            case 'cnpj':
                $cnpj = preg_replace('/[^0-9]/', '', $chave);
                return strlen($cnpj) == 14 && $this->validar_cnpj($cnpj);

            case 'email':
                return filter_var($chave, FILTER_VALIDATE_EMAIL) !== false;

            case 'telefone':
                $telefone = preg_replace('/[^0-9]/', '', $chave);
                return strlen($telefone) >= 10 && strlen($telefone) <= 13;

            case 'aleatoria':
                // Chave aleatória: UUID com ou sem hífens
                $chave_limpa = str_replace('-', '', $chave);
                return strlen($chave_limpa) == 32 && ctype_xdigit($chave_limpa);

            default:
                return false;
        }
    }

    /**
     * Valida CPF
     *
     * @param string $cpf CPF sem formatação
     * @return bool True se válido, false caso contrário
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
     *
     * @param string $cnpj CNPJ sem formatação
     * @return bool True se válido, false caso contrário
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
     *
     * @param string $chave Chave PIX
     * @param string $tipo Tipo da chave
     * @return string Chave formatada
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
