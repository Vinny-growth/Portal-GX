<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * Acesso à matriz de precificação de risco (actuarial_rates).
 *
 * Estende CodeIgniter\Model direto (não o BaseModel do projeto) porque é uma
 * tabela de lookup pura — não precisa de session/Globals e roda também em CLI
 * (seeder, comando de validação) onde os Globals podem não estar inicializados.
 */
class ActuarialRateModel extends Model
{
    protected $table         = 'actuarial_rates';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'idade', 'sexo',
        'wl10_taxa', 'wl20_taxa', 'dg_plus_taxa', 'dg_basico_taxa',
        'invalidez_taxa', 'renda_hospitalar_taxa', 'morte_acidental_taxa', 'frac_fator', 'source',
    ];

    /** Retorna a linha de taxas para a idade/sexo, ou null. */
    public function getRate(int $idade, string $sexo): ?array
    {
        $row = $this->where('idade', $idade)
                    ->where('sexo', strtoupper($sexo))
                    ->first();

        return $row ?: null;
    }
}
