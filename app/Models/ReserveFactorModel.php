<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * Acesso à matriz de fatores de resgate (reserve_factors).
 *
 * O motor de cotação carrega todos os fatores de uma idade de uma vez
 * (getFactorMapForAge) para iterar o loop preditivo em memória — nunca
 * consulta o banco dentro do for ano-a-ano.
 */
class ReserveFactorModel extends Model
{
    protected $table         = 'reserve_factors';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['idade_contratacao', 'sexo', 'ano_vigencia', 'fator', 'source'];

    /**
     * Mapa [ano_vigencia => (float) fator] para a idade de contratação + sexo,
     * ordenado por ano. Uma query, usada pelo loop inteiro.
     */
    public function getFactorMapForAge(int $idadeContratacao, string $sexo): array
    {
        $rows = $this->where('idade_contratacao', $idadeContratacao)
                     ->where('sexo', strtoupper($sexo))
                     ->orderBy('ano_vigencia', 'ASC')
                     ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r['ano_vigencia']] = (float) $r['fator'];
        }

        return $map;
    }

    /** Fator pontual idade x sexo x ano (usado na validação). */
    public function getFactor(int $idadeContratacao, string $sexo, int $anoVigencia): ?float
    {
        $row = $this->where('idade_contratacao', $idadeContratacao)
                    ->where('sexo', strtoupper($sexo))
                    ->where('ano_vigencia', $anoVigencia)
                    ->first();

        return $row ? (float) $row['fator'] : null;
    }
}
