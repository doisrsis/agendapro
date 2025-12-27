<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Feriado
 *
 * Gestão de feriados nacionais, municipais e personalizados
 *
 * @author Rafael Dias - doisr.com.br
 * @date 27/12/2024
 */
class Feriado_model extends CI_Model {

    private $table = 'feriados';

    /**
     * Listar todos os feriados com filtros
     */
    public function get_all($filtros = []) {
        $this->db->select('*');
        $this->db->from($this->table);

        // Filtro por estabelecimento (NULL = nacionais)
        if (isset($filtros['estabelecimento_id'])) {
            if ($filtros['estabelecimento_id'] === 'nacional') {
                $this->db->where('estabelecimento_id IS NULL');
            } else {
                $this->db->group_start();
                $this->db->where('estabelecimento_id', $filtros['estabelecimento_id']);
                $this->db->or_where('estabelecimento_id IS NULL');
                $this->db->group_end();
            }
        }

        // Filtro por tipo
        if (isset($filtros['tipo'])) {
            $this->db->where('tipo', $filtros['tipo']);
        }

        // Filtro por ano
        if (isset($filtros['ano'])) {
            $this->db->where('YEAR(data)', $filtros['ano']);
        }

        // Filtro por ativo
        if (isset($filtros['ativo'])) {
            $this->db->where('ativo', $filtros['ativo']);
        }

        $this->db->order_by('data', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Buscar feriado por ID
     */
    public function get($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Buscar feriado por data
     */
    public function get_by_data($data, $estabelecimento_id = null) {
        $this->db->where('data', $data);
        $this->db->where('ativo', 1);

        if ($estabelecimento_id) {
            $this->db->group_start();
            $this->db->where('estabelecimento_id', $estabelecimento_id);
            $this->db->or_where('estabelecimento_id IS NULL');
            $this->db->group_end();
        } else {
            $this->db->where('estabelecimento_id IS NULL');
        }

        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Verificar se uma data é feriado
     */
    public function is_feriado($data, $estabelecimento_id = null) {
        $this->db->where('data', $data);
        $this->db->where('ativo', 1);

        if ($estabelecimento_id) {
            $this->db->group_start();
            $this->db->where('estabelecimento_id', $estabelecimento_id);
            $this->db->or_where('estabelecimento_id IS NULL');
            $this->db->group_end();
        } else {
            $this->db->where('estabelecimento_id IS NULL');
        }

        $count = $this->db->count_all_results($this->table);
        return $count > 0;
    }

    /**
     * Buscar feriados de um ano específico
     */
    public function get_feriados_ano($ano, $estabelecimento_id = null) {
        $this->db->where('YEAR(data)', $ano);
        $this->db->where('ativo', 1);

        if ($estabelecimento_id) {
            $this->db->group_start();
            $this->db->where('estabelecimento_id', $estabelecimento_id);
            $this->db->or_where('estabelecimento_id IS NULL');
            $this->db->group_end();
        } else {
            $this->db->where('estabelecimento_id IS NULL');
        }

        $this->db->order_by('data', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Criar novo feriado
     */
    public function create($dados) {
        // Validar dados obrigatórios
        if (empty($dados['nome']) || empty($dados['data'])) {
            return false;
        }

        // Preparar dados
        $insert_data = [
            'nome' => $dados['nome'],
            'data' => $dados['data'],
            'tipo' => $dados['tipo'] ?? 'personalizado',
            'recorrente' => $dados['recorrente'] ?? 1,
            'ativo' => $dados['ativo'] ?? 1,
            'estabelecimento_id' => $dados['estabelecimento_id'] ?? null
        ];

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Atualizar feriado
     */
    public function update($id, $dados) {
        $update_data = [];

        if (isset($dados['nome'])) $update_data['nome'] = $dados['nome'];
        if (isset($dados['data'])) $update_data['data'] = $dados['data'];
        if (isset($dados['tipo'])) $update_data['tipo'] = $dados['tipo'];
        if (isset($dados['recorrente'])) $update_data['recorrente'] = $dados['recorrente'];
        if (isset($dados['ativo'])) $update_data['ativo'] = $dados['ativo'];

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Deletar feriado
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Calcular data da Páscoa usando algoritmo de Meeus/Jones/Butcher
     */
    public function calcular_pascoa($ano) {
        $a = $ano % 19;
        $b = floor($ano / 100);
        $c = $ano % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $mes = floor(($h + $l - 7 * $m + 114) / 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
    }

    /**
     * Gerar feriados móveis para um ano
     */
    public function gerar_feriados_moveis($ano) {
        // Calcular data da Páscoa
        $pascoa = new DateTime($this->calcular_pascoa($ano));

        // Sexta-feira Santa (2 dias antes da Páscoa)
        $sexta_santa = clone $pascoa;
        $sexta_santa->sub(new DateInterval('P2D'));

        // Carnaval - Segunda (47 dias antes da Páscoa)
        $carnaval_segunda = clone $pascoa;
        $carnaval_segunda->sub(new DateInterval('P47D'));

        // Carnaval - Terça (46 dias antes da Páscoa)
        $carnaval_terca = clone $pascoa;
        $carnaval_terca->sub(new DateInterval('P46D'));

        // Corpus Christi (60 dias após a Páscoa)
        $corpus_christi = clone $pascoa;
        $corpus_christi->add(new DateInterval('P60D'));

        // Inserir feriados móveis
        $feriados = [
            [
                'nome' => 'Sexta-feira Santa',
                'data' => $sexta_santa->format('Y-m-d'),
                'tipo' => 'nacional',
                'recorrente' => 0
            ],
            [
                'nome' => 'Carnaval - Segunda',
                'data' => $carnaval_segunda->format('Y-m-d'),
                'tipo' => 'facultativo',
                'recorrente' => 0
            ],
            [
                'nome' => 'Carnaval - Terça',
                'data' => $carnaval_terca->format('Y-m-d'),
                'tipo' => 'facultativo',
                'recorrente' => 0
            ],
            [
                'nome' => 'Corpus Christi',
                'data' => $corpus_christi->format('Y-m-d'),
                'tipo' => 'facultativo',
                'recorrente' => 0
            ]
        ];

        $inseridos = 0;
        foreach ($feriados as $feriado) {
            // Verificar se já existe
            $existe = $this->get_by_data($feriado['data']);
            if (!$existe) {
                if ($this->create($feriado)) {
                    $inseridos++;
                }
            }
        }

        return $inseridos;
    }

    /**
     * Ativar/Desativar feriado
     */
    public function toggle_ativo($id) {
        $feriado = $this->get($id);
        if (!$feriado) {
            return false;
        }

        $novo_status = $feriado->ativo ? 0 : 1;
        return $this->update($id, ['ativo' => $novo_status]);
    }
}
