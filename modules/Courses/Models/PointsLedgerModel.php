<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class PointsLedgerModel extends BaseModel
{
    protected $table         = 'points_ledger';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'points', 'reason', 'ref_type', 'ref_id', 'created_at'];

    /**
     * Credita XP de forma idempotente (a unique key user+reason+ref_type+ref_id impede
     * duplicidade — ex.: concluir a mesma aula 2x não dá XP em dobro). Retorna true se creditou.
     */
    public function award(int $userId, int $points, string $reason, ?string $refType = null, ?int $refId = null): bool
    {
        if ($userId <= 0 || $points === 0) {
            return false;
        }
        $b = $this->db->table('points_ledger');
        $dup = $b->where('user_id', $userId)->where('reason', $reason)
            ->where('ref_type', $refType)->where('ref_id', $refId)->countAllResults();
        if ($dup > 0) {
            return false;
        }
        return (bool) $this->db->table('points_ledger')->insert([
            'user_id'    => $userId,
            'points'     => $points,
            'reason'     => $reason,
            'ref_type'   => $refType,
            'ref_id'     => $refId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function totalFor(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $row = $this->db->table('points_ledger')
            ->select('COALESCE(SUM(points),0) AS xp')->where('user_id', $userId)->get()->getFirstRow();
        return $row ? (int) $row->xp : 0;
    }
}
