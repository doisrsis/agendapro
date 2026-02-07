<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model de Bloqueios
 *
 * Gerencia bloqueios de horários dos profissionais (férias, folgas, etc)
 *
 * @author Rafael Dias - doisr.com.br
 * @date 06/12/2024
 */
class Bloqueio_model extends CI_Model {

    protected $table = 'bloqueios';

    /**
     * Buscar todos os bloqueios
     */
    public function get_all($filtros = [], $limit = null, $offset = null) {
        $this->db->select('b.*, p.nome as profissional_nome, e.nome as estabelecimento_nome, s.nome as servico_nome');
        $this->db->from($this->table . ' b');
        $this->db->join('profissionais p', 'p.id = b.profissional_id', 'left');
        $this->db->join('estabelecimentos e', 'e.id = p.estabelecimento_id', 'left');
        $this->db->join('servicos s', 's.id = b.servico_id', 'left');

        if (!empty($filtros['profissional_id'])) {
            $this->db->where('b.profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['servico_id'])) {
            $this->db->where('b.servico_id', $filtros['servico_id']);
        }

        if (!empty($filtros['estabelecimento_id'])) {
            $this->db->where('p.estabelecimento_id', $filtros['estabelecimento_id']);
        }

        // Filtro de período unificado para suportar recorrência
        if (!empty($filtros['data_inicio']) || !empty($filtros['data_fim'])) {
            $filter_start = $filtros['data_inicio'] ?? '1000-01-01';
            $filter_end = $filtros['data_fim'] ?? '9999-12-31';

            $this->db->group_start();

            // 1. Bloqueios Normais ou Recorrentes desativados
            $this->db->group_start();
            $this->db->where('b.recorrencia', 'nao');
            // Lógica de sobreposição de períodos
            // (StartA <= EndB) AND (EndA >= StartB)
            $this->db->where('b.data_inicio <=', $filter_end);
            $this->db->group_start();
                $this->db->where('b.data_fim IS NULL', null, false); // Dia único
                $this->db->or_where('b.data_fim >=', $filter_start); // Período
            $this->db->group_end();
            // Para dia único (data_fim NULL), data_inicio deve ser >= filter_start também?
            // Se data_fim é NULL, intervalo é [data_inicio, data_inicio].
            // Então data_inicio >= filter_start (já coberto por EndA >= StartB se EndA=StartA)
            // Wait, se data_fim NULL:
            // [data_inicio, data_inicio] overlaps [filter_start, filter_end]
            // data_inicio <= filter_end AND data_inicio >= filter_start
            // O código acima faz: data_inicio <= filter_end AND (TRUE OR ...)
            // Faltou data_inicio >= filter_start especifíco para o caso NULL?
            // Não, se data_fim >= filter_start cobre o caso periodo.
            // Para dia único, data_fim é NULL. Então a condição OR segunda falha. A primeira passa.
            // Mas precisamos garantir que data_inicio >= filter_start para dia único?
            // Se eu tenho bloqueio 2024-01-01 (Null). Filter 2025.
            // data_inicio (2024) <= filter_end (2025). OK.
            // data_fim IS NULL. OK.
            // Result: Retorna bloqueio de 2024. ERRADO.
            // Preciso corrigir a lógica para dia único normal.

            // CORREÇÃO Lógica de Dia Único:
            $this->db->or_group_start();
                $this->db->where('b.recorrencia', 'nao');
                $this->db->where('b.data_fim IS NULL', null, false);
                $this->db->where('b.data_inicio >=', $filter_start);
                $this->db->where('b.data_inicio <=', $filter_end);
            $this->db->group_end();
            $this->db->group_end();

            // 2. Bloqueios Recorrentes
            $this->db->or_group_start();
                $this->db->where('b.recorrencia !=', 'nao');
                $this->db->where('b.data_inicio <=', $filter_end); // Deve ter iniciado antes do fim do filtro
                $this->db->group_start();
                    $this->db->where('b.data_limite IS NULL', null, false);
                    $this->db->or_where('b.data_limite >=', $filter_start); // Limite deve ser depois do inicio do filtro
                $this->db->group_end();
            $this->db->group_end();

            $this->db->group_end();
        }

        if (!empty($filtros['tipo'])) {
            $this->db->where('b.tipo', $filtros['tipo']);
        }

        $this->db->order_by('b.data_inicio', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Buscar bloqueio por ID
     */
    public function get_by_id($id) {
        $this->db->select('b.*, p.nome as profissional_nome');
        $this->db->from($this->table . ' b');
        $this->db->join('profissionais p', 'p.id = b.profissional_id');
        $this->db->where('b.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Criar novo bloqueio
     */
    public function create($dados) {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $dados)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Atualizar bloqueio
     */
    public function update($id, $dados) {
        $dados['atualizado_em'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Deletar bloqueio
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Verificar se há bloqueio em um período
     *
     * @param int $profissional_id
     * @param string $data
     * @param string $hora_inicio
     * @param string $hora_fim
     * @param int $servico_id (opcional) - se fornecido, verifica bloqueios específicos do serviço
     * @return bool
     */
    public function tem_bloqueio($profissional_id, $data, $hora_inicio = null, $hora_fim = null, $servico_id = null) {
        // Verificar bloqueios do profissional
        $this->db->where('profissional_id', $profissional_id);

        // Se servico_id foi fornecido, verificar apenas bloqueios que afetam este serviço
        if ($servico_id) {
            $this->db->group_start();
            // Bloqueio geral do profissional (servico_id NULL = todos os serviços)
            $this->db->where('servico_id IS NULL', null, false);
            // OU bloqueio específico deste serviço
            $this->db->or_where('servico_id', $servico_id);
            $this->db->group_end();
        } else {
            // Se não foi fornecido servico_id, considerar apenas bloqueios gerais (sem serviço específico)
            $this->db->where('servico_id IS NULL', null, false);
        }

        // Verificar bloqueios que afetam esta data
        $this->db->group_start();

        // 1. Bloqueios Normais (sem recorrência) ou Recorrentes com data específica
        $this->db->group_start();

            // Bloqueio de dia específico (data_fim é NULL ou igual a data_inicio)
            $this->db->group_start();
            $this->db->where('data_inicio', $data);
            $this->db->where('recorrencia', 'nao'); // Apenas não recorrentes aqui
            $this->db->group_start();
            $this->db->where('data_fim IS NULL', null, false);
            $this->db->or_where('data_fim', $data);
            $this->db->group_end();
            $this->db->group_end();

            // OU bloqueio de período
            $this->db->or_group_start();
            $this->db->where('recorrencia', 'nao'); // Apenas não recorrentes
            $this->db->where('data_inicio <=', $data);
            $this->db->where('data_fim >=', $data);
            $this->db->where('data_fim IS NOT NULL', null, false);
            $this->db->group_end();

            // OU Bloqueios Recorrentes
            $this->db->or_group_start();
                // Deve ter começado antes ou na data atual
                $this->db->where('data_inicio <=', $data);

                // Recorrência deve ser diferente de 'nao'
                $this->db->where('recorrencia !=', 'nao');

                // Não pode ter expirado (se tiver data limite)
                $this->db->group_start();
                $this->db->where('data_limite IS NULL', null, false);
                $this->db->or_where('data_limite >=', $data);
                $this->db->group_end();

                // Verificar tipo de recorrência
                $this->db->group_start();
                    // Diário
                    $this->db->where('recorrencia', 'diario');

                    // OU Semanal (dia da semana coincide)
                    $this->db->or_group_start();
                    $this->db->where('recorrencia', 'semanal');
                    // date('w') retorna 0 (dom) a 6 (sab), igual ao nosso BD
                    $dia_semana_atual = date('w', strtotime($data));
                    $this->db->where('dia_semana', $dia_semana_atual);
                    $this->db->group_end();
                $this->db->group_end();
            $this->db->group_end();

        $this->db->group_end(); // End main checks

        $this->db->group_end();

        // Se for verificação de horário específico
        if ($hora_inicio && $hora_fim) {
            $this->db->group_start();
            // Bloqueio de dia inteiro (hora_inicio e hora_fim são NULL)
            $this->db->where('hora_inicio IS NULL', null, false);
            // OU bloqueio de horário que sobrepõe
            $this->db->or_group_start();
            $this->db->where('hora_inicio <', $hora_fim);
            $this->db->where('hora_fim >', $hora_inicio);
            $this->db->group_end();
            $this->db->group_end();
        }

        $bloqueio = $this->db->get($this->table)->row();

        return $bloqueio ? true : false;
    }

    /**
     * Alias para tem_bloqueio (compatibilidade)
     */
    public function verificar_bloqueio($profissional_id, $data, $hora_inicio = null, $hora_fim = null, $servico_id = null) {
        return $this->tem_bloqueio($profissional_id, $data, $hora_inicio, $hora_fim, $servico_id);
    }

    /**
     * Buscar bloqueios ativos de um profissional
     */
    public function get_bloqueios_ativos($profissional_id) {
        $hoje = date('Y-m-d');

        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data_fim >=', $hoje);
        $this->db->order_by('data_inicio', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar bloqueios futuros
     */
    public function get_bloqueios_futuros($profissional_id, $dias = 30) {
        $hoje = date('Y-m-d');
        $data_limite = date('Y-m-d', strtotime("+{$dias} days"));

        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('data_inicio >=', $hoje);
        $this->db->where('data_inicio <=', $data_limite);
        $this->db->order_by('data_inicio', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Contar bloqueios
     */
    public function count($filtros = []) {
        if (!empty($filtros['profissional_id'])) {
            $this->db->where('profissional_id', $filtros['profissional_id']);
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('data_fim >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('data_inicio <=', $filtros['data_fim']);
        }

        return $this->db->count_all_results($this->table);
    }

    // =========================================================================
    // ALIASES PARA COMPATIBILIDADE DE NOMENCLATURA
    // =========================================================================

    /**
     * Alias para get_by_id()
     */
    public function get($id) {
        return $this->get_by_id($id);
    }

    /**
     * Alias para create()
     */
    public function criar($dados) {
        return $this->create($dados);
    }

    /**
     * Alias para update()
     */
    public function atualizar($id, $dados) {
        return $this->update($id, $dados);
    }

    /**
     * Alias para delete()
     */
    public function excluir($id) {
        return $this->delete($id);
    }

    /**
     * Verificar se existe bloqueio de serviço
     *
     * @param int $servico_id
     * @param string $data
     * @param string $hora_inicio
     * @param string $hora_fim
     * @return bool
     */
    public function tem_bloqueio_servico($servico_id, $data, $hora_inicio = null, $hora_fim = null) {
        $this->db->where('servico_id', $servico_id);
        $this->db->where('profissional_id IS NULL', null, false); // Bloqueio geral de serviço

        // Verificar bloqueios que afetam esta data
        $this->db->group_start();

        // Bloqueio de dia específico (data_fim é NULL ou igual a data_inicio)
        $this->db->group_start();
        $this->db->where('data_inicio', $data);
        $this->db->group_start();
        $this->db->where('data_fim IS NULL', null, false);
        $this->db->or_where('data_fim', $data);
        $this->db->group_end();
        $this->db->group_end();

        // OU bloqueio de período (data está entre data_inicio e data_fim)
        $this->db->or_group_start();
        $this->db->where('data_inicio <=', $data);
        $this->db->where('data_fim >=', $data);
        $this->db->where('data_fim IS NOT NULL', null, false);
        $this->db->group_end();

        $this->db->group_end();

        // Se for verificação de horário específico
        if ($hora_inicio && $hora_fim) {
            $this->db->group_start();
            // Bloqueio de dia inteiro (hora_inicio e hora_fim são NULL)
            $this->db->where('hora_inicio IS NULL', null, false);
            // OU bloqueio de horário que sobrepõe
            $this->db->or_group_start();
            $this->db->where('hora_inicio <', $hora_fim);
            $this->db->where('hora_fim >', $hora_inicio);
            $this->db->group_end();
            $this->db->group_end();
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Verificar se existe bloqueio específico (profissional + serviço)
     *
     * @param int $profissional_id
     * @param int $servico_id
     * @param string $data
     * @param string $hora_inicio
     * @param string $hora_fim
     * @return bool
     */
    public function tem_bloqueio_especifico($profissional_id, $servico_id, $data, $hora_inicio = null, $hora_fim = null) {
        $this->db->where('profissional_id', $profissional_id);
        $this->db->where('servico_id', $servico_id);

        // Verificar bloqueios que afetam esta data
        $this->db->group_start();

        // Bloqueio de dia específico
        $this->db->group_start();
        $this->db->where('data_inicio', $data);
        $this->db->group_start();
        $this->db->where('data_fim IS NULL', null, false);
        $this->db->or_where('data_fim', $data);
        $this->db->group_end();
        $this->db->group_end();

        // OU bloqueio de período
        $this->db->or_group_start();
        $this->db->where('data_inicio <=', $data);
        $this->db->where('data_fim >=', $data);
        $this->db->where('data_fim IS NOT NULL', null, false);
        $this->db->group_end();

        $this->db->group_end();

        // Se for verificação de horário específico
        if ($hora_inicio && $hora_fim) {
            $this->db->group_start();
            $this->db->where('hora_inicio IS NULL', null, false);
            $this->db->or_group_start();
            $this->db->where('hora_inicio <', $hora_fim);
            $this->db->where('hora_fim >', $hora_inicio);
            $this->db->group_end();
            $this->db->group_end();
        }

        return $this->db->count_all_results($this->table) > 0;
    }
}
