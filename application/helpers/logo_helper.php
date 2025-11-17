<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper para exibir logo do sistema
 *
 * @author Rafael Dias - doisr.com.br
 * @date 16/11/2024
 */

if (!function_exists('exibir_logo')) {
    /**
     * Exibe a logo do sistema ou o nome como fallback
     *
     * @param string $classe Classes CSS adicionais
     * @param string $estilo Estilos CSS inline
     * @return string HTML da logo ou nome
     */
    function exibir_logo($classe = '', $estilo = 'max-height: 40px;') {
        $CI =& get_instance();
        $CI->load->model('Configuracao_model');

        // Buscar logo e nome do sistema
        $logo = $CI->Configuracao_model->get_valor('sistema_logo');
        $nome_sistema = $CI->Configuracao_model->get_valor('sistema_nome', 'Dashboard Administrativo');

        // Se tem logo e o arquivo existe
        if ($logo && file_exists('./assets/img/logo/' . $logo)) {
            return '<img src="' . base_url('assets/img/logo/' . $logo) . '"
                         alt="' . htmlspecialchars($nome_sistema) . '"
                         class="' . $classe . '"
                         style="' . $estilo . '">';
        }

        // Fallback: exibir nome do sistema
        return '<span class="' . $classe . '" style="' . $estilo . ' font-weight: 600; font-size: 1.2rem;">'
               . htmlspecialchars($nome_sistema) .
               '</span>';
    }
}

if (!function_exists('exibir_logo_login')) {
    /**
     * Exibe a logo na página de login (maior)
     *
     * @return string HTML da logo ou nome
     */
    function exibir_logo_login() {
        $CI =& get_instance();
        $CI->load->model('Configuracao_model');

        $logo = $CI->Configuracao_model->get_valor('sistema_logo');
        $nome_sistema = $CI->Configuracao_model->get_valor('sistema_nome', 'Dashboard Administrativo');

        if ($logo && file_exists('./assets/img/logo/' . $logo)) {
            return '<img src="' . base_url('assets/img/logo/' . $logo) . '"
                         alt="' . htmlspecialchars($nome_sistema) . '"
                         style="max-height: 80px; max-width: 100%;">';
        }

        return '<h1 class="text-primary" style="font-weight: 700; font-size: 2rem;">'
               . htmlspecialchars($nome_sistema) .
               '</h1>';
    }
}

if (!function_exists('get_nome_sistema')) {
    /**
     * Retorna apenas o nome do sistema
     *
     * @return string Nome do sistema
     */
    function get_nome_sistema() {
        $CI =& get_instance();
        $CI->load->model('Configuracao_model');
        return $CI->Configuracao_model->get_valor('sistema_nome', 'Dashboard Administrativo');
    }
}
